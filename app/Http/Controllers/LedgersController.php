<?php

namespace App\Http\Controllers;

use App\Models\Ledgers;
use App\Models\relation_ledger_request;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Ledger;
//use Illuminate\Support\Facades\Auth;

class LedgersController extends Controller
{
    /**
     * List all ledger entries for the authenticated user
     */
    public function index()
    {
       //$userId = Auth::id();
       try {
    $userId = 1; // Temporary hardcoded user ID for testing
        $ledgers = Ledgers::with(['insertedBy:id,full_name', 'approvedBy:id,full_name', 'rlr.seller.user', 'rlr.buyer.user'])
            ->whereHas('rlr', function($q) use ($userId) {
                $q->whereHas('seller.user', fn($s) => $s->where('id', $userId))
                  ->orWhereHas('buyer.user', fn($b) => $b->where('id', $userId));
            })
            ->get();

        return response()->json($ledgers, 200);
         } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred while fetching ledger entries.'], 500);
         }
    }

    /**
     * Create a new ledger entry
     */
    public function store(Request $request)
    {
        try {
        $request->validate([
            'rlr_id' => 'required|exists:relation_ledger_request,id',
            'date' => 'required|date',
            'description' => 'required|string|max:100',
            'Qty' => 'nullable|string|max:11',
            'Set' => 'nullable|string|max:11',
            'Rate' => 'nullable|numeric',
            'Debit' => 'required|numeric|min:0',
            'Credit' => 'required|numeric|min:0',
        ]);

        //$userId = Auth::id();
        $userId = 1; // Temporary hardcoded user ID for testing
        $rlr = relation_ledger_request::with(['seller.user', 'buyer.user'])->findOrFail($request->rlr_id);

        // Check if user is part of this ledger request
        if (!in_array($userId, [$rlr->seller->user_id, $rlr->buyer->user_id])) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        // Get previous balance
        $lastBalance = Ledgers::where('rlr_id', $rlr->id)->latest('id')->value('Balance') ?? 0;
        $balance = $lastBalance + $request->Credit - $request->Debit;

        $ledgers = Ledgers::create([
            'rlr_id' => $rlr->id,
            'date' => $request->date,
            'description' => $request->description,
            'inserted_by' => $userId,
            'requested_by' => $userId,
            'Qty' => $request->Qty,
            'Set' => $request->Set,
            'Rate' => $request->Rate,
            'Total' => ($request->Qty && $request->Rate) ? $request->Qty * $request->Rate : null,
            'Debit' => $request->Debit,
            'Credit' => $request->Credit,
            'Balance' => $balance,
        ]);

        return response()->json([
            'message' => 'Ledger entry created successfully.',
            'ledger' => $ledgers
        ], 201);
        } catch(\Exception $e){
            return response()->json(['error' => 'An error occurred while creating the ledger entry.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Approve a ledger entry
     */
    public function approve($id)
    {
        //$userId = Auth::id();
        $userId = 2; // Temporary hardcoded user ID for testing
        $ledgers = Ledgers::with(['rlr.seller.user', 'rlr.buyer.user'])->findOrFail($id);

        // Only the other party can approve
        if ($ledgers->requested_by == $userId) {
            return response()->json(['error' => 'You cannot approve your own ledger entry.'], 422);
        }

        $allowedUserIds = [$ledgers->rlr->seller->user_id, $ledgers->rlr->buyer->user_id];
        if (!in_array($userId, $allowedUserIds)) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        // Approve
        $ledgers->approved_by = $userId;
        $ledgers->updated_time = now();
        $ledgers->save();

        return response()->json([
            'message' => 'Ledger entry approved successfully.',
            'ledger' => $ledgers
        ], 200);
    }

    /**
     * Show single ledger entry
     */
    public function show(Ledgers $ledgers)
    {
        $ledgers->load(['insertedBy:id,full_name', 'approvedBy:id,full_name', 'rlr.seller.user', 'rlr.buyer.user']);
        return response()->json($ledgers, 200);
    }
}
