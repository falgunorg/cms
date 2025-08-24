<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller {

    public function index() {
        $invoices = Invoice::latest()->paginate(10);
        return view('invoices.index', compact('invoices'));
    }

    public function create() {
        return view('invoices.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|mimes:pdf,jpg,jpeg,png',
            'amount' => 'required|numeric',
            'remarks' => 'nullable|string'
        ]);

        if ($request->hasFile('file')) {
            $validated['file'] = $request->file('file')->store('invoices', 'public');
        }

        Invoice::create($validated);

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice) {
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice) {
        return view('invoices.edit', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
            'amount' => 'required|numeric',
            'remarks' => 'nullable|string'
        ]);

        if ($request->hasFile('file')) {
            if ($invoice->file && file_exists(storage_path('app/public/' . $invoice->file))) {
                unlink(storage_path('app/public/' . $invoice->file));
            }
            $validated['file'] = $request->file('file')->store('invoices', 'public');
        }

        $invoice->update($validated);

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice) {
        if ($invoice->file && file_exists(storage_path('app/public/' . $invoice->file))) {
            unlink(storage_path('app/public/' . $invoice->file));
        }
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }
}
