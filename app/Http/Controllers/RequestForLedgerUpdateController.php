<?php

namespace App\Http\Controllers;

use App\Models\request_for_ledger_update;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\relation_ledger_request;
use App\Models\Ledgers;
use Illuminate\Support\Facades\Auth;

class RequestForLedgerUpdateController extends Controller
{
   public function index()
    {
        try {
            //$userId = Auth::id();
            $userId = 1; // temporary hardcoded user id for testing

            $requests = request_for_ledger_update::with([
                'rlr.seller.user',
                'rlr.buyer.user',
                'ledger.insertedBy',
                'ledger.approvedBy',
                'requestedBy',
                'approvedBy'
            ])
            ->where(function($q) use ($userId) {
                $q->where('requested_by', $userId)
                  ->orWhere('approved_by', $userId);
            })
            ->get();

            return response()->json($requests, 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch ledger update requests.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new ledger update request
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'rlr_id' => 'required|exists:relation_ledger_request,id',
                'ledger_id' => 'required|:ledgers,id',
                'seller_id' => 'required|exists:business_users,id',
                'buyer_id' => 'required|exists:business_users,id',
                'reason' => 'required|string|max:255',
            ]);

            //$userId = Auth::id();
            $userId = 1; // temporary hardcoded user id

            $ledgerRequest = request_for_ledger_update::create([
                'rlr_id' => $request->rlr_id,
                'ledger_id' => $request->ledger_id,
                'seller_id' => $request->seller_id,
                'buyer_id' => $request->buyer_id,
                'reason' => $request->reason,
                'requested_by' => $userId,
                'status' => 'pending'
            ]);

            return response()->json([
                'message' => 'Ledger update request created successfully.',
                'request' => $ledgerRequest
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed.',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create ledger update request.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a single ledger update request
     */
    public function show($id)
    {
        try {
            $requestUpdate = request_for_ledger_update::with([
                'rlr.seller.user',
                'rlr.buyer.user',
                'ledger.insertedBy',
                'ledger.approvedBy',
                'requestedBy',
                'approvedBy'
            ])->findOrFail($id);

            return response()->json($requestUpdate, 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch the ledger update request.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve or reject a ledger update request
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:approved,rejected',
            ]);

            //$userId = Auth::id();
            $userId = 2; // temporary hardcoded approver

            $requestUpdate = request_for_ledger_update::findOrFail($id);

            // Prevent requester from approving own request
            if ($requestUpdate->requested_by == $userId) {
                return response()->json(['error' => 'You cannot approve/reject your own request.'], 422);
            }

            $requestUpdate->status = $request->status;
            $requestUpdate->approved_by = $userId;
            $requestUpdate->save();

            return response()->json([
                'message' => 'Ledger update request status updated successfully.',
                'request' => $requestUpdate
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed.',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update ledger update request.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deletion is forbidden
     */
    public function destroy($id)
    {
        return response()->json([
            'message' => 'Ledger update request deletion is not allowed.'
        ], 403);
    }
}
