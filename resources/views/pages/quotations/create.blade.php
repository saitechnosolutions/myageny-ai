@extends('layouts.app')

@section('title', 'Create Quotation')

@push('styles')
{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
/* ── Layout ─────────────────────────────────────────────── */
.page-wrapper   { padding: 28px 32px; max-width: 100%; }
.page-header    { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
.page-title     { font-size:22px; font-weight:700; color:#121212; }
.back-link      { color:#9e9e9e; font-size:13px; text-decoration:none; }
.back-link:hover{ color:#fe5f04; }

/* ── Card ────────────────────────────────────────────────── */
.form-card {
    background:#fff; border:1px solid #e1dee3;
    border-radius:14px; padding:28px; margin-bottom:20px;
}
.form-card-title{
    font-size:15px; font-weight:600; color:#121212;
    margin-bottom:20px; padding-bottom:10px;
    border-bottom:1px solid #f0f0f0;
    display:flex; align-items:center; gap:8px;
}
.form-card-title i{ color:#fe5f04; }

/* ── Grid ────────────────────────────────────────────────── */
.grid-2{ display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.grid-3{ display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; }
.grid-4{ display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:16px; }

/* ── Inputs ──────────────────────────────────────────────── */
.form-group       { display:flex; flex-direction:column; gap:6px; }
.form-label       { font-size:12px; font-weight:600; color:#6e6e6e; }
.form-control {
    border:1px solid #e1dee3; border-radius:10px;
    padding:9px 14px; font-size:13px; color:#121212;
    font-family:inherit; background:#fcfcfc;
    transition:border-color .2s;
    width:100%;
}
.form-control:focus{ outline:none; border-color:#fe5f04; background:#fff; }
textarea.form-control{ resize:vertical; min-height:80px; }
.input-readonly{ background:#f4f4f4; color:#888; cursor:not-allowed; }

/* ── Products Table ──────────────────────────────────────── */
.products-table-wrapper{ overflow-x:auto; }
table.products-table{ width:100%; border-collapse:collapse; }
table.products-table th{
    padding:10px 12px; font-size:11px; font-weight:600;
    color:#7c7c7c; background:#f8f8f8;
    border-bottom:1px solid #e1dee3; text-align:left;
}
table.products-table td{
    padding:8px 8px; vertical-align:middle;
    border-bottom:1px solid #f4f4f4;
}
table.products-table tbody tr:last-child td{ border-bottom:none; }
.td-product  { min-width:220px; }
.td-desc     { min-width:180px; }
.td-qty      { width:90px; }
.td-price    { width:120px; }
.td-disc     { width:110px; }
.td-total    { width:120px; text-align:right; }
.td-del      { width:44px; text-align:center; }

.row-total-val{ font-weight:600; color:#121212; font-size:13px; }

/* ── Totals ──────────────────────────────────────────────── */
.totals-row{ display:flex; justify-content:flex-end; margin-top:18px; }
.totals-box{
    border:1px solid #e1dee3; border-radius:12px;
    padding:16px 24px; min-width:280px;
    display:flex; flex-direction:column; gap:10px;
}
.totals-line{ display:flex; justify-content:space-between; font-size:13px; color:#444; }
.totals-line.grand{
    font-size:16px; font-weight:700; color:#121212;
    padding-top:10px; border-top:1px solid #e1dee3;
}

/* ── Buttons ─────────────────────────────────────────────── */
.btn-add-row{
    display:inline-flex; align-items:center; gap:6px;
    background:#fff8f4; color:#fe5f04;
    border:1px dashed #ffc19e; border-radius:10px;
    padding:7px 16px; font-size:13px; font-weight:600;
    cursor:pointer; margin-top:14px;
}
.btn-add-row:hover{ background:#ffe8d6; }
.btn-del-row{
    background:none; border:none; color:#ccc;
    cursor:pointer; font-size:18px; line-height:1;
    padding:4px;
}
.btn-del-row:hover{ color:#e53935; }
.btn-submit{
    display:inline-flex; align-items:center; gap:8px;
    background:#fe5f04; color:#fff; border:none;
    padding:10px 28px; border-radius:20px;
    font-size:14px; font-weight:600; cursor:pointer;
    font-family:inherit;
}
.btn-submit:hover{ background:#e05400; }
.form-footer{
    display:flex; justify-content:flex-end;
    gap:12px; margin-top:24px;
}
.btn-cancel{
    display:inline-flex; align-items:center; gap:8px;
    background:#f4f4f4; color:#555; border:none;
    padding:10px 24px; border-radius:20px;
    font-size:14px; font-weight:500; cursor:pointer;
    text-decoration:none; font-family:inherit;
}

/* ── Select2 overrides ───────────────────────────────────── */
.select2-container--default .select2-selection--single{
    border:1px solid #e1dee3; border-radius:10px;
    height:38px; background:#fcfcfc;
    display:flex; align-items:center;
}
.select2-container--default .select2-selection--single .select2-selection__rendered{
    line-height:36px; padding-left:14px; color:#121212; font-size:13px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow{ height:36px; }
.select2-dropdown{ border:1px solid #e1dee3; border-radius:10px; box-shadow:0 4px 16px rgba(0,0,0,.08); }
.select2-container--default .select2-results__option--highlighted[aria-selected]{
    background-color:#fe5f04;
}

/* ── Validation errors ───────────────────────────────────── */
.is-invalid{ border-color:#e53935 !important; }
.error-text{ color:#e53935; font-size:11px; margin-top:2px; }
</style>
@endpush

@section('content')
<div class="page-wrapper">

    {{-- Header --}}
    <div class="page-header">
        <div>
            <a href="{{ route('quotations.index') }}" class="back-link">
                <i class="bi bi-arrow-left"></i> Back to Quotations
            </a>
            <div class="page-title" style="margin-top:4px">Create Quotation - {{ $lead->contact_name }} {{ $lead->company_name }}</div>
        </div>
    </div>

    {{-- Validation errors --}}
    @if($errors->any())
    <div style="background:#fff5f5;border:1px solid #ffc4c4;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#c00;font-size:13px;">
        <strong>Please fix the following errors:</strong>
        <ul style="margin:8px 0 0 16px; padding:0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('quotations.store') }}" id="quotationForm">
        @csrf

        {{-- ── Section 1: Quotation Info ── --}}
        <div class="form-card">
            <div class="form-card-title">
                <i class="bi bi-file-earmark-text-fill"></i> Quotation Details
            </div>

            <div class="grid-4" style="margin-bottom:16px">
                {{-- Quotation No --}}
                <div class="form-group">
                    <label class="form-label">Quotation Number</label>
                    <input type="text" class="form-control input-readonly"
                           value="{{ $defaults['quotation_no'] }}" readonly>
                </div>

                {{-- Date --}}
                <div class="form-group">
                    <label class="form-label">Quotation Date <span style="color:#e53935">*</span></label>
                    <input type="date" name="quotation_date" class="form-control @error('quotation_date') is-invalid @enderror"
                           value="{{ old('quotation_date', $defaults['quotation_date']) }}" required>
                    @error('quotation_date')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                {{-- Valid Until --}}
                <div class="form-group">
                    <label class="form-label">Valid Until <span style="color:#e53935">*</span></label>
                    <input type="date" name="valid_until" class="form-control @error('valid_until') is-invalid @enderror"
                           value="{{ old('valid_until', $defaults['valid_until']) }}" required>
                    @error('valid_until')<div class="error-text">{{ $message }}</div>@enderror
                </div>


            </div>

             <div class="grid-2" style="margin-bottom:16px">
             <div class="form-card">
            <div class="form-card-title">
                <i class="bi bi-sticky-fill"></i> Bill To Address
            </div>
            <div class="form-group">
                <textarea name="bill_to_address" class="form-control" placeholder="Address"></textarea>
            </div>
        </div>

         <div class="form-card">
            <div class="form-card-title">
                <i class="bi bi-sticky-fill"></i> Ship To Address
            </div>
            <div class="form-group">
                <textarea name="ship_to_address" class="form-control" placeholder="Address"></textarea>
            </div>
        </div>
        </div>

        </div>

        {{-- ── Section 2: Products ── --}}
        <div class="form-card">
            <div class="form-card-title">
                <i class="bi bi-box-seam-fill"></i> Products / Services
            </div>

            {{-- Embed products as JS data --}}
            <script id="productsData" type="application/json">
                {!! json_encode($products->map(fn($p) => [
                    'id'          => $p->id,
                    'text'        => $p->product_name,
                    'description' => $p->description ?? '',
                    'price'       => $p->base_price,
                ])) !!}
            </script>

            <div class="products-table-wrapper">
                <table class="products-table" id="productsTable">
                    <thead>
                        <tr>
                            <th class="td-product">Product</th>
                            <th class="td-desc">Description</th>
                            <th class="td-qty">Qty</th>
                            <th class="td-price">Unit Price (₹)</th>
                            <th class="td-disc">Discount (₹)</th>
                            <th class="td-total">Total (₹)</th>
                            <th class="td-del"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        {{-- rows injected by JS --}}
                    </tbody>
                </table>
            </div>

            <button type="button" class="btn-add-row" id="addRowBtn">
                <i class="bi bi-plus-circle"></i> Add Product
            </button>

            {{-- Totals --}}
            <div class="totals-row">
                <div class="totals-box">
                    <div class="totals-line">
                        <span>Subtotal</span>
                        <span id="displaySubtotal">₹0.00</span>
                    </div>
                    <div class="totals-line">
                        <span>CGST (<span id="displayTaxPctCgst">0</span>%)</span>
                        <span id="displayTaxAmtCgst">₹0.00</span>
                    </div>
                    <div class="totals-line">
                        <span>SGST (<span id="displayTaxPctSgst">0</span>%)</span>
                        <span id="displayTaxAmtSgst">₹0.00</span>
                    </div>

                    {{--  <div class="totals-line">
                        <span>Tax (<span id="displayTaxPct">0</span>%)</span>
                        <span id="displayTaxAmt">₹0.00</span>
                    </div>  --}}
                    <div class="totals-line grand">
                        <span>Grand Total</span>
                        <span id="displayTotal">₹0.00</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Section 3: Notes ── --}}

        <div class="grid-2" style="margin-bottom:16px">
             <div class="form-card">
            <div class="form-card-title">
                <i class="bi bi-sticky-fill"></i> Payment Terms
            </div>
            <div class="form-group">
                <textarea name="payment_terms" class="form-control" placeholder="Payment Terms…">{{ old('notes') }}</textarea>
            </div>
        </div>

         <div class="form-card">
            <div class="form-card-title">
                <i class="bi bi-sticky-fill"></i> Notes
            </div>
            <div class="form-group">
                <textarea name="notes" class="form-control" placeholder="Optional internal notes…">{{ old('notes') }}</textarea>
                <input type="hidden" name="lead_id" value="{{ $leadId ?? null }}">
            </div>
        </div>
        </div>


        {{-- Footer --}}
        <div class="form-footer">
            <a href="{{ route('quotations.index') }}" class="btn-cancel">Cancel</a>
            <button type="submit" class="btn-submit" id="submitBtn">
                <i class="bi bi-save2-fill"></i> Save Quotation
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
{{-- jQuery (if not already in layout) --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
{{-- Select2 --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>



$(function () {



    /* ── Product data from Blade ─────────────────────────────────────────── */
    const PRODUCTS = JSON.parse(document.getElementById('productsData').textContent);
    const productMap = {};
    PRODUCTS.forEach(p => { productMap[p.id] = p; });

    let rowIndex = 1;

    /* ── Helpers ─────────────────────────────────────────────────────────── */
    const fmt  = n => '₹' + parseFloat(n || 0).toFixed(2);
    const fnum = n => parseFloat(n || 0);


    let isAddingRow = false;

    /* ── Add a new product row ───────────────────────────────────────────── */
    function addRow(data = {}) {

         if (isAddingRow) return; // 🔥 prevent double call
    isAddingRow = true;

        const idx = Date.now();

        const row = $(`
            <tr id="item-row-${idx}">
                <td class="td-product">
                    <select name="items[${idx}][product_id]"
                            class="product-select"
                            data-row="${idx}" required>
                        <option value="">— Select —</option>
                        ${PRODUCTS.map(p => `<option value="${p.id}"
                            ${data.product_id == p.id ? 'selected' : ''}
                            data-desc="${p.description || ''}"
                            data-price="${p.price}">${p.text}</option>`).join('')}
                    </select>
                </td>
                <td class="td-desc">
                    <input type="text" name="items[${idx}][description]"
                           class="form-control item-desc" style="min-width:160px"
                           value="${data.description || ''}" placeholder="Description">
                </td>
                <td class="td-qty">
                    <input type="number" name="items[${idx}][qty]"
                           class="form-control item-qty" min="0.01" step="0.01"
                           value="${data.qty || 1}" required>
                </td>
                <td class="td-price">
                    <input type="number" name="items[${idx}][unit_price]"
                           class="form-control item-price" min="0" step="0.01"
                           value="${data.unit_price || ''}" placeholder="0.00" required>
                </td>
                <td class="td-disc">
                    <input type="number" name="items[${idx}][discount]"
                           class="form-control item-disc" min="0" step="0.01"
                           value="${data.discount || 0}">
                </td>
                <td class="td-total" style="text-align:right">
                    <span class="row-total-val" id="row-total-${idx}">₹0.00</span>
                </td>
                <td class="td-del">
                    <button type="button" class="btn-del-row" data-row="${idx}"
                            title="Remove row">
                        <i class="bi bi-x-circle-fill"></i>
                    </button>
                </td>
            </tr>
        `);

        $('#itemsBody').append(row);

        /* Init Select2 on the new row's select */
        setTimeout(() => {
    row.find('.product-select').select2({
        placeholder: '— Select product —',
        allowClear: true,
        width: '100%',
    });

     isAddingRow = false;
}, 0);

        /* If editing with pre-filled product, recalculate */
        if (data.product_id) recalcRow(idx);

        return idx;
    }



    /* ── Recalculate one row total ───────────────────────────────────────── */
    function recalcRow(el) {

    const row = $(el).closest('tr'); // 🔥 current row

    const qty   = fnum(row.find('.item-qty').val());
    const price = fnum(row.find('.item-price').val());
    const disc  = fnum(row.find('.item-disc').val());

    const total = Math.max((qty * price) - disc, 0);

    row.find('.row-total-val').text(fmt(total));

    recalcTotals();
}

    /* ── Recalculate grand totals ────────────────────────────────────────── */
   function recalcTotals(el = null) {

    // 🔥 If element passed → find its table
    let container = el
        ? $(el).closest('.products-table-wrapper')
        : $('.products-table-wrapper');

    let subtotal = 0;

    container.find('.row-total-val').each(function () {
        subtotal += parseFloat($(this).text().replace('₹', '') || 0);
    });

    const taxPctCgst  = 18/2;
    const taxPctSgst  = 18/2;
    const taxAmt  = subtotal * (taxPctCgst / 100);
    const grand   = subtotal + taxAmt;

    $('#displaySubtotal').text(fmt(subtotal));
    $('#displayTaxPctSgst').text(taxPctCgst.toFixed(2));
    $('#displayTaxPctCgst').text(taxPctSgst.toFixed(2));
    $('#displayTaxAmtCgst').text(fmt(taxAmt));
    $('#displayTaxAmtSgst').text(fmt(taxAmt));
    $('#displayTotal').text(fmt(grand));
}

    /* ── Event: product select changes ──────────────────────────────────── */
    $(document).on('change', '.product-select', function () {

    const row = $(this).closest('tr');   // 🔥 get current row
    const idx = $(this).data('row');     // keep for recalc

    const pid  = $(this).val();
    const prod = productMap[pid];

    if (prod) {
        row.find('.item-desc').val(prod.description);
        row.find('.item-price').val(parseFloat(prod.price).toFixed(2));
    } else {
        row.find('.item-desc').val('');
        row.find('.item-price').val('');
    }

    row.find('.item-price').trigger('change');

    recalcRow(idx);
});

    /* ── Event: qty / price / discount change ────────────────────────────── */
    $(document).on('input change', '.item-qty, .item-price, .item-disc', function () {

    const row = $(this).closest('tr');

    const qty   = parseFloat(row.find('.item-qty').val() || 0);
    const price = parseFloat(row.find('.item-price').val() || 0);
    const disc  = parseFloat(row.find('.item-disc').val() || 0); // %

    const subtotal = qty * price;

    const discountAmount = subtotal * (disc / 100); // 🔥 % calculation

    const total = Math.max(subtotal - discountAmount, 0);

    row.find('.row-total-val').text('₹' + total.toFixed(2));

    recalcTotals();
});

    /* ── Event: tax changes ──────────────────────────────────────────────── */
    $('#taxInput').on('input change', recalcTotals);

    /* ── Event: add row ──────────────────────────────────────────────────── */
    $(document).off('click', '#addRowBtn').on('click', '#addRowBtn', function () {
    console.log('clicked');
    addRow();
});

    /* ── Event: remove row ───────────────────────────────────────────────── */
    $(document).on('click', '.btn-del-row', function () {
        const idx = $(this).data('row');
        $(`#item-row-${idx}`).remove();
        recalcTotals();
    });

    /* ── Init Select2 on lead dropdown ──────────────────────────────────── */
    $('#leadSelect').select2({ placeholder: '— Select Lead —', width: '100%' });

    /* ── Start with one empty row ────────────────────────────────────────── */
if ($('#itemsBody tr').length === 0) {
    addRow();
}

    /* ── Form submit guard ───────────────────────────────────────────────── */
    $('#quotationForm').on('submit', function () {
        if ($('#itemsBody tr').length === 0) {
            alert('Please add at least one product.');
            return false;
        }
        $('#submitBtn').prop('disabled', true).html(
            '<i class="bi bi-hourglass-split"></i> Saving…'
        );
    });

});
</script>
@endpush
