@extends('layouts.app')

@section('title', 'Assets')

@php
    $categoryCount = count($budget_cats);
    $sortedRows = [];

    foreach ($user_fields as $userField) {
        if (str_contains($userField['field'] ?? '', 'client_description')) {
            $parts = explode('_', $userField['field']);
            $categoryId = $parts[3] ?? null;

            if ($categoryId !== null) {
                $sortedRows[$categoryId][] = true;
            }
        }
    }
@endphp

@section('content')
<div class="container-fluid pt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Assets & Liabilities</h4>
        <a class="btn btn-outline-secondary" href="/crm-assets/print">
            <i class="fas fa-print me-1"></i> Print
        </a>
    </div>

    @if ($categoryCount === 0)
        <div class="alert alert-warning mb-0">No asset categories found in `ss_als`.</div>
    @endif

    @for ($i = 0; $i < $categoryCount; $i++)
        @php
            $category = $budget_cats[$i];
            $budgetCnt = isset($sortedRows[$category['alid']]) ? count($sortedRows[$category['alid']]) : 0;
        @endphp

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>{{ $category['alname'] }}</strong>
                <button
                    class="btn btn-sm btn-primary"
                    type="button"
                    onclick="edit_record('', '', this)"
                    name="adder_{{ $category['alid'] }}"
                    id="adder_{{ $category['alid'] }}"
                    currRow="{{ $budgetCnt + 1 }}"
                    data-bs-toggle="modal"
                    data-bs-target="#budgetAddModal"
                >
                    <i class="fas fa-plus me-1"></i>Add Row
                </button>
            </div>

            <div class="card-body">
                @include('inc.assets_grid')
            </div>

            <div class="card-footer text-end">
                <span class="me-2">Total records:</span>
                <input
                    type="text"
                    class="form-control d-inline-block text-center"
                    style="width:80px;background-color:#FFFF8D;"
                    name="totals_{{ $category['alid'] }}"
                    id="totals_{{ $category['alid'] }}"
                    value="{{ $budgetCnt }}"
                    readonly
                />
            </div>
        </div>
    @endfor
</div>

<div class="modal fade" id="budgetAddModal" tabindex="-1" aria-labelledby="titleBudgetAddModal" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="/crm-assets" name="assetForm" id="assetForm" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="titleBudgetAddModal">Add / Edit Asset Row</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4"><label class="form-label mb-0">Description</label></div>
                    <div class="col-md-8"><input type="text" class="form-control" name="client_description" id="client_description" autocomplete="off"></div>

                    <div class="col-md-4"><label class="form-label mb-0">Value (Client)</label></div>
                    <div class="col-md-8"><input type="text" class="form-control" name="client_value" id="client_value" autocomplete="off"></div>

                    <div class="col-md-4"><label class="form-label mb-0">Owing (Client)</label></div>
                    <div class="col-md-8"><input type="text" class="form-control" name="client_owing" id="client_owing" autocomplete="off"></div>

                    <div class="col-md-4"><label class="form-label mb-0">Value (Spouse)</label></div>
                    <div class="col-md-8"><input type="text" class="form-control" name="spouse_value" id="spouse_value" autocomplete="off"></div>

                    <div class="col-md-4"><label class="form-label mb-0">Owing (Spouse)</label></div>
                    <div class="col-md-8"><input type="text" class="form-control" name="spouse_owing" id="spouse_owing" autocomplete="off"></div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="hidden" name="budget_id" id="budget_id" value="">
                <input type="hidden" name="nth_row" id="nth_row" value="">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function edit_record(fdl_txt, sp_txt, obj) {
    const fldArr = ["client_description", "client_value", "client_owing"];
    const spArr = ["spouse_value", "spouse_owing"];

    $("#budgetAddModal input[type='text']").val('');

    const budgetId = $(obj).attr('id').replace('adder_', '');
    const currRow = parseInt($(obj).attr('currRow'), 10);

    $("#budget_id").val(budgetId);
    $("#nth_row").val(currRow);

    const recId = fdl_txt.replace('client_owing_', '');
    const spId = sp_txt.replace('spouse_owing_', '');

    if (!recId) {
        return;
    }

    fldArr.forEach(function (fieldName) {
        const source = $('#' + fieldName + '_' + recId);
        if (source.length) {
            $('#' + fieldName).val(source.val());
        }
    });

    spArr.forEach(function (fieldName) {
        const source = $('#' + fieldName + '_' + spId);
        if (source.length) {
            $('#' + fieldName).val(source.val());
        }
    });
}
</script>
@endsection
