@csrf
<div class="mb-3">
    <label>Title</label>
    <input type="text" name="title" value="{{ old('title',$invoice->title ?? '') }}" class="form-control">
</div>
<div class="mb-3">
    <label>Amount</label>
    <input type="number" step="0.01" name="amount" value="{{ old('amount',$invoice->amount ?? '') }}" class="form-control">
</div>
<div class="mb-3">
    <label>File</label>
    <input type="file" name="file" class="form-control">
    @if(!empty($invoice->file))
    <p><a href="{{ asset('storage/'.$invoice->file) }}" target="_blank">Current File</a></p>
    @endif
</div>
<div class="mb-3">
    <label>Remarks</label>
    <textarea name="remarks" class="form-control">{{ old('remarks',$invoice->remarks ?? '') }}</textarea>
</div>
<button class="btn btn-success">Save</button>
