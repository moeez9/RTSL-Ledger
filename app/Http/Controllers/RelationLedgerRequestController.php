<?php

namespace App\Http\Controllers;

use App\Models\relation_ledger_request;
use Illuminate\Http\Request;
use App\Models\business_users;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class RelationLedgerRequestController extends Controller
{
    /**
     * Display all ledger requests related to the logged-in user.
     */
    public function index()
    {
        $userId = Auth::id();

        $requests = relation_ledger_request::with([
            'seller.user:id,full_name,email',
            'seller.business:id,business_name',
            'buyer.user:id,full_name,email',
            'buyer.business:id,business_name'
        ])
        //main table (relation_ledger_request) ki query
        ->where(function($q) use ($userId) {
            $q->whereHas('seller.user', fn($q2) => $q2->where('id', $userId))//related table (user) ki query
              ->orWhereHas('buyer.user', fn($q2) => $q2->where('id', $userId));
        })
        ->get();

        $result = $requests->map(function ($req) {
            return [
                'id' => $req->id,
                'seller_name' => $req->seller->user->full_name,
                'seller_email' => $req->seller->user->email,
                'seller_business' => $req->seller->business->business_name,
                'buyer_name' => $req->buyer->user->full_name,
                'buyer_email' => $req->buyer->user->email,
                'buyer_business' => $req->buyer->business->business_name,
                'status' => $req->status,
                'requested_by' => $req->requested_by,
                'created_at' => $req->created_at,
                'updated_at' => $req->updated_at,
            ];
        });

        return response()->json($result, 200);
    }

    /**
     * Create a new ledger request.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'seller_business_user_id' => 'required|exists:business_users,id',
                'buyer_business_user_id' => 'required|exists:business_users,id|different:seller_business_user_id',
            ]);

            $seller = business_users::with('role', 'business', 'user')->findOrFail($request->seller_business_user_id);
            $buyer = business_users::with('role', 'business', 'user')->findOrFail($request->buyer_business_user_id);

            if ($seller->role->role !== 'seller') {
                return response()->json(['message' => 'The selected seller does not have a seller role.'], 422);
            }

            if ($buyer->role->role !== 'buyer') {
                return response()->json(['message' => 'The selected buyer does not have a buyer role.'], 422);
            }

            if ($seller->bus_name_id === $buyer->bus_name_id) {
                return response()->json(['message' => 'Seller and Buyer must belong to different businesses.'], 422);
            }

            // Prevent duplicate requests in both directions
            $exists = relation_ledger_request::where(function($q) use ($seller, $buyer) {
                $q->where([
                    'seller_business_user_id' => $seller->id,
                    'buyer_business_user_id' => $buyer->id,
                ])->orWhere([
                    'seller_business_user_id' => $buyer->id,
                    'buyer_business_user_id' => $seller->id,
                ]);
            })->whereIn('status', ['pending', 'accepted'])->exists();

            if ($exists) {
                return response()->json(['message' => 'A ledger request already exists between these users in pending or accepted status.'], 409);
            }

            $userId = Auth::id();
            $requested_by = $userId === $seller->user_id ? 'seller' : ($userId === $buyer->user_id ? 'buyer' : null);
            if (!$requested_by) {
                return response()->json(['message' => 'You must be either the seller or the buyer to create a ledger request.'], 403);
            }

            $ledger = relation_ledger_request::create([
                'seller_business_user_id' => $seller->id,
                'buyer_business_user_id' => $buyer->id,
                'status' => 'pending',
                'requested_by' => $requested_by,
            ]);

            $ledger->load([
                'seller.user:id,full_name,email',
                'seller.business:id,business_name',
                'buyer.user:id,full_name,email',
                'buyer.business:id,business_name',
            ]);

            return response()->json([
                'message' => 'Ledger request created successfully.',
                'rlr_id' => $ledger->id,
                'seller_name' => $ledger->seller->user->full_name,
                'seller_business' => $ledger->seller->business->business_name,
                'buyer_name' => $ledger->buyer->user->full_name,
                'buyer_business' => $ledger->buyer->business->business_name,
                'status' => $ledger->status,
                'requested_by' => $ledger->requested_by,
                'created_at' => $ledger->created_at,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed.', 'details' => $e->errors()], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['error' => 'Database error.', 'message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unexpected error.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Accept a pending ledger request.
     */
    public function acceptLedgerRequest($id)
    {
        $userId = Auth::id();
        $ledger = relation_ledger_request::with(['seller', 'buyer'])->findOrFail($id);

        if (!in_array($userId, [$ledger->seller->user_id, $ledger->buyer->user_id])) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        if ($ledger->status !== 'pending') {
            return response()->json(['message' => 'Request already ' . $ledger->status . '.'], 400);
        }

        $acceptor = $userId === $ledger->seller->user_id ? 'seller' : 'buyer';

        if ($ledger->requested_by === $acceptor) {
            return response()->json(['error' => 'You cannot accept your own request.'], 422);
        }

        $ledger->status = 'accepted';
        $ledger->save();

        return response()->json([
            'message' => 'Ledger request accepted successfully.',
            'rlr_id' => $ledger->id,
            'status' => $ledger->status,
            'accepted_by' => $acceptor,
            'requested_by' => $ledger->requested_by,
            'updated_at' => $ledger->updated_at,
        ], 200);
    }

    /**
     * Cancel a pending ledger request (only requester can cancel).
     */
    public function cancelLedgerRequest($id)
    {
        $userId = Auth::id();
        $ledger = relation_ledger_request::with(['seller', 'buyer'])->findOrFail($id);

        if (!in_array($userId, [$ledger->seller->user_id, $ledger->buyer->user_id])) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        if ($ledger->status !== 'pending') {
            return response()->json(['message' => 'Request already ' . $ledger->status . '.'], 400);
        }

        $canceler = $userId === $ledger->seller->user_id ? 'seller' : 'buyer';

        if ($ledger->requested_by !== $canceler) {
            return response()->json(['error' => 'Only the requester can cancel the request.'], 422);
        }

        $ledger->status = 'cancelled';
        $ledger->save();

        return response()->json([
            'message' => 'Ledger request cancelled successfully.',
            'rlr_id' => $ledger->id,
            'status' => $ledger->status,
            'cancelled_by' => $canceler,
            'requested_by' => $ledger->requested_by,
            'updated_at' => $ledger->updated_at,
        ], 200);
    }

    /**
     * Display a single ledger request.
     */
    public function show(relation_ledger_request $ledger)
    {
        $ledger->load([
            'seller.user:id,full_name,email',
            'seller.business:id,business_name',
            'buyer.user:id,full_name,email',
            'buyer.business:id,business_name',
        ]);

        return response()->json([
            'rlr_id' => $ledger->id,
            'seller_name' => $ledger->seller->user->full_name,
            'buyer_name' => $ledger->buyer->user->full_name,
            'seller_business' => $ledger->seller->business->business_name,
            'buyer_business' => $ledger->buyer->business->business_name,
            'status' => $ledger->status,
            'requested_by' => $ledger->requested_by,
            'created_at' => $ledger->created_at,
            'updated_at' => $ledger->updated_at,
        ], 200);
    }

    /**
     * Prevent deletion.
     */
    public function destroy(relation_ledger_request $ledger)
    {
        return response()->json(['message' => 'Ledger request deletion is not allowed.'], 403);
    }
}
