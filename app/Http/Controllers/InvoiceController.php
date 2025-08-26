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

    public function show(Invoice $invoice) {
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice) {
        return view('invoices.edit', compact('invoice'));
    }

    public function store_origin(Request $request) {
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

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
            'amount' => 'required|numeric',
            'remarks' => 'nullable|string'
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/invoices'), $fileName);
            $validated['file'] = 'assets/invoices/' . $fileName;
        }

        Invoice::create($validated);

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function update(Request $request, Invoice $invoice) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
            'amount' => 'required|numeric',
            'remarks' => 'nullable|string'
        ]);

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($invoice->file && file_exists(public_path($invoice->file))) {
                unlink(public_path($invoice->file));
            }

            // Upload new file
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/invoices'), $fileName);
            $validated['file'] = 'assets/invoices/' . $fileName;
        }

        $invoice->update($validated);

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice) {
        if ($invoice->file && file_exists(public_path($invoice->file))) {
            unlink(public_path($invoice->file));
        }

        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }
}
