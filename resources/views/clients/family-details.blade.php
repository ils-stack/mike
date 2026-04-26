@extends('layouts.app')

@section('title','Family Details')

@php
    $maritalStatusOptions = ['Single', 'Married', 'Divorced', 'Widowed'];
    $entityOptions = ['Individual', 'Company', 'Trust', 'Partnership', 'Other'];

    $initialDependants = old('dependants_json')
        ? (json_decode(old('dependants_json'), true) ?: [])
        : $dependants->map(function ($dependant) {
            return [
                'id' => $dependant->id,
                'first_name' => $dependant->first_name,
                'surname' => $dependant->surname,
                'relationship' => $dependant->relationship,
                'id_number' => $dependant->id_number,
                'date_of_birth' => optional($dependant->date_of_birth)->format('Y-m-d'),
                'gender' => $dependant->gender,
                'email' => $dependant->email,
                'phone' => $dependant->phone,
                'notes' => $dependant->notes,
                'deleted_at' => $dependant->deleted_at,
            ];
        })->values()->all();
@endphp

@section('content')

<div class="container-fluid">
    <form method="POST" action="/family-details" id="familyDetailsForm">
        @csrf

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Family Details</h5>

            <div class="d-flex gap-2">
                <button class="btn btn-sm text-white" style="background:#E34234;" type="button">
                    <i class="fas fa-phone me-1"></i> Call
                </button>
                <button class="btn btn-sm text-white" style="background:#E34234;" type="button">
                    <i class="fas fa-print me-1"></i> Print
                </button>
                <button
                    class="btn btn-sm text-white"
                    style="background:#198754;"
                    type="submit"
                    {{ $selectedInvestor ? '' : 'disabled' }}
                >
                    <i class="fas fa-save me-1"></i> Save
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                Please correct the highlighted validation errors and try again.
            </div>
        @endif

        @include('dashboard.partials.advisors')

        @if(!$selectedInvestor)
            <div class="alert alert-warning">
                Select an investor first before capturing family details.
            </div>
        @endif

        <input type="hidden" name="investor_id" value="{{ old('investor_id', optional($selectedInvestor)->id) }}">
        <input type="hidden" name="dependants_json" id="dependantsJson" value="{{ old('dependants_json') }}">

        <div class="card mb-3">
            <div class="card-header fw-bold">Your Details</div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label">Marital Status</label>
                        <select class="form-select @error('client.marital_status') is-invalid @enderror" name="client[marital_status]">
                            <option value="">Select Marital Status</option>
                            @foreach($maritalStatusOptions as $option)
                                <option value="{{ $option }}" {{ old('client.marital_status', optional($clientDetail)->marital_status) === $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Entity</label>
                        <select class="form-select @error('client.entity') is-invalid @enderror" name="client[entity]">
                            <option value="">Select Entity</option>
                            @foreach($entityOptions as $option)
                                <option value="{{ $option }}" {{ old('client.entity', optional($clientDetail)->entity) === $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Surname</label>
                        <input class="form-control @error('client.surname') is-invalid @enderror" name="client[surname]" value="{{ old('client.surname', optional($clientDetail)->surname) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">First Name</label>
                        <input class="form-control @error('client.first_name') is-invalid @enderror" name="client[first_name]" value="{{ old('client.first_name', optional($clientDetail)->first_name) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ID Number</label>
                        <input class="form-control @error('client.id_number') is-invalid @enderror" name="client[id_number]" value="{{ old('client.id_number', optional($clientDetail)->id_number) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tax Number</label>
                        <input class="form-control @error('client.tax_number') is-invalid @enderror" name="client[tax_number]" value="{{ old('client.tax_number', optional($clientDetail)->tax_number) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Email</label>
                        <input class="form-control @error('client.email') is-invalid @enderror" name="client[email]" value="{{ old('client.email', optional($clientDetail)->email) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control @error('client.date_of_birth') is-invalid @enderror" name="client[date_of_birth]" value="{{ old('client.date_of_birth', optional(optional($clientDetail)->date_of_birth)->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header fw-bold">Addresses & Contact</div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Physical Address</label>
                        <textarea class="form-control @error('client.physical_address') is-invalid @enderror" name="client[physical_address]" rows="3">{{ old('client.physical_address', optional($clientDetail)->physical_address) }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Postal Address</label>
                        <textarea class="form-control @error('client.postal_address') is-invalid @enderror" name="client[postal_address]" rows="3">{{ old('client.postal_address', optional($clientDetail)->postal_address) }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Contact Details</label>
                        <input class="form-control mb-2 @error('client.cellular') is-invalid @enderror" name="client[cellular]" placeholder="Cellular" value="{{ old('client.cellular', optional($clientDetail)->cellular) }}">
                        <input class="form-control mb-2 @error('client.home_tel') is-invalid @enderror" name="client[home_tel]" placeholder="Home Tel" value="{{ old('client.home_tel', optional($clientDetail)->home_tel) }}">
                        <input class="form-control @error('client.work_tel') is-invalid @enderror" name="client[work_tel]" placeholder="Work Tel" value="{{ old('client.work_tel', optional($clientDetail)->work_tel) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header fw-bold">Spouse Info</div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label">Marital Status</label>
                        <select class="form-select @error('spouse.marital_status') is-invalid @enderror" name="spouse[marital_status]">
                            <option value="">Select Marital Status</option>
                            @foreach($maritalStatusOptions as $option)
                                <option value="{{ $option }}" {{ old('spouse.marital_status', optional($spouseDetail)->marital_status) === $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Entity</label>
                        <select class="form-select @error('spouse.entity') is-invalid @enderror" name="spouse[entity]">
                            <option value="">Select Entity</option>
                            @foreach($entityOptions as $option)
                                <option value="{{ $option }}" {{ old('spouse.entity', optional($spouseDetail)->entity) === $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Surname</label>
                        <input class="form-control @error('spouse.surname') is-invalid @enderror" name="spouse[surname]" value="{{ old('spouse.surname', optional($spouseDetail)->surname) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">First Name</label>
                        <input class="form-control @error('spouse.first_name') is-invalid @enderror" name="spouse[first_name]" value="{{ old('spouse.first_name', optional($spouseDetail)->first_name) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ID Number</label>
                        <input class="form-control @error('spouse.id_number') is-invalid @enderror" name="spouse[id_number]" value="{{ old('spouse.id_number', optional($spouseDetail)->id_number) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tax Number</label>
                        <input class="form-control @error('spouse.tax_number') is-invalid @enderror" name="spouse[tax_number]" value="{{ old('spouse.tax_number', optional($spouseDetail)->tax_number) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Email</label>
                        <input class="form-control @error('spouse.email') is-invalid @enderror" name="spouse[email]" value="{{ old('spouse.email', optional($spouseDetail)->email) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control @error('spouse.date_of_birth') is-invalid @enderror" name="spouse[date_of_birth]" value="{{ old('spouse.date_of_birth', optional(optional($spouseDetail)->date_of_birth)->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header fw-bold">Spouse Addresses & Contact</div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Physical Address</label>
                        <textarea class="form-control @error('spouse.physical_address') is-invalid @enderror" name="spouse[physical_address]" rows="3">{{ old('spouse.physical_address', optional($spouseDetail)->physical_address) }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Postal Address</label>
                        <textarea class="form-control @error('spouse.postal_address') is-invalid @enderror" name="spouse[postal_address]" rows="3">{{ old('spouse.postal_address', optional($spouseDetail)->postal_address) }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Contact Details</label>
                        <input class="form-control mb-2 @error('spouse.cellular') is-invalid @enderror" name="spouse[cellular]" placeholder="Cellular" value="{{ old('spouse.cellular', optional($spouseDetail)->cellular) }}">
                        <input class="form-control mb-2 @error('spouse.home_tel') is-invalid @enderror" name="spouse[home_tel]" placeholder="Home Tel" value="{{ old('spouse.home_tel', optional($spouseDetail)->home_tel) }}">
                        <input class="form-control @error('spouse.work_tel') is-invalid @enderror" name="spouse[work_tel]" placeholder="Work Tel" value="{{ old('spouse.work_tel', optional($spouseDetail)->work_tel) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold">Dependants</span>
                <button
                    type="button"
                    class="btn btn-sm text-white"
                    style="background:#E34234;"
                    id="openDependantModalBtn"
                    {{ $selectedInvestor ? '' : 'disabled' }}
                >
                    <i class="fas fa-plus me-1"></i> Add Dependant
                </button>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>First Name</th>
                                <th>Surname</th>
                                <th>Relationship</th>
                                <th>ID Number</th>
                                <th>Date of Birth</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="dependantsTableBody"></tbody>
                    </table>
                </div>
                <div id="dependantsEmptyState" class="p-4 text-center text-muted d-none">
                    No active dependants added yet.
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="dependantModal" tabindex="-1" aria-labelledby="dependantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="dependantForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="dependantModalLabel">Add Dependant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="dependantIndex">
                    <input type="hidden" id="dependantRecordId">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="dependantFirstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="dependantFirstName" required>
                        </div>
                        <div class="col-md-4">
                            <label for="dependantSurname" class="form-label">Surname</label>
                            <input type="text" class="form-control" id="dependantSurname" required>
                        </div>
                        <div class="col-md-4">
                            <label for="dependantRelationship" class="form-label">Relationship</label>
                            <input type="text" class="form-control" id="dependantRelationship" required>
                        </div>
                        <div class="col-md-4">
                            <label for="dependantIdNumber" class="form-label">ID Number</label>
                            <input type="text" class="form-control" id="dependantIdNumber">
                        </div>
                        <div class="col-md-4">
                            <label for="dependantDob" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="dependantDob">
                        </div>
                        <div class="col-md-4">
                            <label for="dependantGender" class="form-label">Gender</label>
                            <select class="form-select" id="dependantGender">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="dependantEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="dependantEmail">
                        </div>
                        <div class="col-md-6">
                            <label for="dependantPhone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="dependantPhone">
                        </div>
                        <div class="col-12">
                            <label for="dependantNotes" class="form-label">Notes</label>
                            <textarea class="form-control" id="dependantNotes" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white" style="background:#E34234;">Save Dependant</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('page-js')
<script>
$(function () {
    var dependants = @json($initialDependants);
    var dependantModalElement = document.getElementById('dependantModal');
    var dependantModal = dependantModalElement ? new bootstrap.Modal(dependantModalElement) : null;

    function syncDependantsField() {
        $('#dependantsJson').val(JSON.stringify(dependants));
    }

    function formatDate(dateString) {
        if (!dateString) {
            return '';
        }

        var parts = dateString.split('-');

        if (parts.length !== 3) {
            return dateString;
        }

        return parts[2] + '/' + parts[1] + '/' + parts[0];
    }

    function renderDependants() {
        var activeDependants = dependants.filter(function (dependant) {
            return !dependant.deleted_at;
        });

        var rows = activeDependants.map(function (dependant, rowIndex) {
            var sourceIndex = dependants.findIndex(function (item) {
                return item === dependant;
            });

            return '' +
                '<tr>' +
                    '<td>' + (rowIndex + 1) + '</td>' +
                    '<td>' + (dependant.first_name || '') + '</td>' +
                    '<td>' + (dependant.surname || '') + '</td>' +
                    '<td>' + (dependant.relationship || '') + '</td>' +
                    '<td>' + (dependant.id_number || '') + '</td>' +
                    '<td>' + formatDate(dependant.date_of_birth) + '</td>' +
                    '<td class="text-end">' +
                        '<button type="button" class="btn btn-sm text-white me-1 edit-dependant-btn" style="background:#E34234;" data-index="' + sourceIndex + '">' +
                            '<i class="fas fa-edit"></i>' +
                        '</button>' +
                        '<button type="button" class="btn btn-sm btn-dark delete-dependant-btn" data-index="' + sourceIndex + '">' +
                            '<i class="fas fa-trash"></i>' +
                        '</button>' +
                    '</td>' +
                '</tr>';
        }).join('');

        $('#dependantsTableBody').html(rows);
        $('#dependantsEmptyState').toggleClass('d-none', activeDependants.length !== 0);
        syncDependantsField();
    }

    function resetDependantForm() {
        $('#dependantForm')[0].reset();
        $('#dependantIndex').val('');
        $('#dependantRecordId').val('');
        $('#dependantModalLabel').text('Add Dependant');
    }

    function populateDependantForm(index) {
        var dependant = dependants[index];

        if (!dependant) {
            return;
        }

        $('#dependantIndex').val(index);
        $('#dependantRecordId').val(dependant.id || '');
        $('#dependantModalLabel').text('Edit Dependant');
        $('#dependantFirstName').val(dependant.first_name || '');
        $('#dependantSurname').val(dependant.surname || '');
        $('#dependantRelationship').val(dependant.relationship || '');
        $('#dependantIdNumber').val(dependant.id_number || '');
        $('#dependantDob').val(dependant.date_of_birth || '');
        $('#dependantGender').val(dependant.gender || '');
        $('#dependantEmail').val(dependant.email || '');
        $('#dependantPhone').val(dependant.phone || '');
        $('#dependantNotes').val(dependant.notes || '');
    }

    $('#advisor_id').on('changed.bs.select', function () {
        var advisorId = $(this).val();
        var url = new URL(window.location.href);
        url.searchParams.set('advisor_id', advisorId);
        url.searchParams.delete('investor_id');
        window.location.href = url.toString();
    });

    $('#investor_id').on('changed.bs.select', function () {
        var investorId = $(this).val();
        var url = new URL(window.location.href);
        url.searchParams.set('investor_id', investorId);
        window.location.href = url.toString();
    });

    $('#openDependantModalBtn').on('click', function () {
        resetDependantForm();
        dependantModal.show();
    });

    $('#dependantForm').on('submit', function (event) {
        event.preventDefault();

        var index = $('#dependantIndex').val();
        var dependantPayload = {
            id: $('#dependantRecordId').val() || null,
            first_name: $('#dependantFirstName').val(),
            surname: $('#dependantSurname').val(),
            relationship: $('#dependantRelationship').val(),
            id_number: $('#dependantIdNumber').val(),
            date_of_birth: $('#dependantDob').val(),
            gender: $('#dependantGender').val(),
            email: $('#dependantEmail').val(),
            phone: $('#dependantPhone').val(),
            notes: $('#dependantNotes').val(),
            deleted_at: null
        };

        if (index === '') {
            dependants.push(dependantPayload);
        } else {
            dependants[Number(index)] = $.extend({}, dependants[Number(index)], dependantPayload);
        }

        renderDependants();
        dependantModal.hide();
        resetDependantForm();
    });

    $(document).on('click', '.edit-dependant-btn', function () {
        populateDependantForm($(this).data('index'));
        dependantModal.show();
    });

    $(document).on('click', '.delete-dependant-btn', function () {
        var index = Number($(this).data('index'));

        if (!dependants[index]) {
            return;
        }

        dependants[index].deleted_at = new Date().toISOString();
        renderDependants();
    });

    if (dependantModalElement) {
        dependantModalElement.addEventListener('hidden.bs.modal', function () {
            resetDependantForm();
        });
    }

    renderDependants();
});
</script>
@endpush
