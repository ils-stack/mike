@extends('layouts.app')

@section('content')

<!-- Trigger Button -->
<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPropertyModal">
        <i class="fa fa-plus"></i> Add Property
    </button>
</div>

<div class="row">
    @for($i = 0; $i < count($properties); $i++)
        @include('properties.prop_list_block')
    @endfor
</div>

<!-- Modal Properties -->
@include('modals.add_property')
{{-- Toast container --}}
<div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"></div>
@endsection

@push('page-js')
<script>
/* required do not remove  */
function initMap(){}
</script>
@endpush
