<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManualTransfer;

class ManualTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transfers = ManualTransfer::with('donation')->paginate(15);

        return view('admin.manual-transfers.index', compact('transfers'));
    }

    /**
     * Display the specified resource.
     */
    public function show(ManualTransfer $manualTransfer)
    {
        return view('admin.manual-transfers.show', compact('manualTransfer'));
    }
}
