<?php

namespace App\Http\Controllers;

use App\Models\Ledgers;
use App\Models\relation_ledger_request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LedgerController extends Controller
{
    /**
     * List all ledger entries for the authenticated user
     */
    public function index()
    {
        $userId = Auth::id();

        $ledgers = Ledgers::with(['insertedBy:id,full_name', 'approvedBy:id,full_name', 'rlr.seller.user', 'rlr.buyer.user'])
            ->whereHas('rlr', function($q) use ($userId) {
                $q->whereHas('seller.user', fn($s) => $s->where('id', $userId))
                  ->orWhereHas('buyer.user', fn($b) => $b->where('id', $userId));
            })
            ->get();

        return response()->json($ledgers, 200);
    }

    /**
     * Create a new ledger entry
     */
    public function store(Request $request)
    {
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

        $userId = Auth::id();
        $rlr = relation_ledger_request::with(['seller.user', 'buyer.user'])->findOrFail($request->rlr_id);

        // Check if user is part of this ledger request
        if (!in_array($userId, [$rlr->seller->user_id, $rlr->buyer->user_id])) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        // Get previous balance
        $lastBalance = Ledgers::where('rlr_id', $rlr->id)->latest('id')->value('Balance') ?? 0;
        $balance = $lastBalance + $request->Credit - $request->Debit;

        $ledger = Ledgers::create([
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
            'ledger' => $ledger
        ], 201);
    }

    /**
     * Approve a ledger entry
     */
    public function approve($id)
    {
        $userId = Auth::id();
        $ledger = Ledgers::with(['rlr.seller.user', 'rlr.buyer.user'])->findOrFail($id);

        // Only the other party can approve
        if ($ledger->requested_by == $userId) {
            return response()->json(['error' => 'You cannot approve your own ledger entry.'], 422);
        }

        $allowedUserIds = [$ledger->rlr->seller->user_id, $ledger->rlr->buyer->user_id];
        if (!in_array($userId, $allowedUserIds)) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        // Approve
        $ledger->approved_by = $userId;
        $ledger->updated_time = now();
        $ledger->save();

        return response()->json([
            'message' => 'Ledger entry approved successfully.',
            'ledger' => $ledger
        ], 200);
    }

    /**
     * Show single ledger entry
     */
    public function show(Ledgers $ledger)
    {
        $ledger->load(['insertedBy:id,full_name', 'approvedBy:id,full_name', 'rlr.seller.user', 'rlr.buyer.user']);
        return response()->json($ledger, 200);
    }
}
