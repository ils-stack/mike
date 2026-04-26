@extends('layouts.app')

@section('title', 'Create Brochure')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="fa-solid fa-file-circle-plus text-primary"></i>
            Create Brochure
        </h4>

        <a href="/brochure" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>

    <!-- Card -->
    <div class="card shadow-sm">

        <div class="card-header bg-dark text-white">
            Selected Units ({{ count($units) }})
        </div>

        <div class="card-body">

            @if(count($units) == 0)
                <div class="alert alert-info text-center py-4">
                    <i class="fa-solid fa-circle-info"></i>
                    No units selected.
                    <br>
                    Please go to Units and click
                    <b>“Add to Brochure”</b>.
                </div>

            @else

            <!-- Unit list -->
            <ul class="list-group mb-4" id="brochureUnitList">

                @foreach($units as $u)
                <li class="list-group-item d-flex justify-content-between align-items-center"
                    id="item_{{ $u->id }}">

                    <div>
                        <strong>Unit {{ $u->unit_no }}</strong>
                        <br>
                        <small class="text-muted">
                            {{ $u->unit_type }}
                            — {{ number_format($u->unit_size, 2) }} m²
                        </small>
                        @if($u->availability)
                            <br>
                            <small class="text-muted">
                                Availability: {{ $u->availability }}
                            </small>
                        @endif
                    </div>

                    <button class="btn btn-sm btn-danger removeFromCart"
                            data-id="{{ $u->id }}">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </li>
                @endforeach

            </ul>

            <!-- Brochure form -->
            <h5 class="mt-4">Brochure Details</h5>
            <hr>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Brochure Title</label>
                    <input type="text" id="brochure_title" class="form-control"
                           placeholder="Example: Industrial Portfolio – Unit Selection">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Prepared By</label>
                    <input type="text" class="form-control"
                           value="{{ Auth::user()->name }} {{ Auth::user()->surname }}"
                           disabled>
                </div>
            </div>

            <div class="text-end">
                <button id="generateBrochureBtn" class="btn btn-primary btn-lg">
                    <i class="fa-solid fa-file-pdf"></i>
                    Generate Brochure
                </button>
            </div>

            @endif
        </div>
    </div>

</div>

@endsection

@push('page-js')
<script>

/* required do not remove  */
function initMap(){}

$(document).ready(function() {

    // ============================================
    // 🔥 REMOVE UNIT FROM CART (AJAX)
    // ============================================
    $('.removeFromCart').click(function() {
        const id = $(this).data('id');

        $.post('/brochure/cart/remove', {
            unit_id: id,                    // UPDATED
            _token: "{{ csrf_token() }}"
        }, function(res) {

            if (res.success) {

                // Remove row
                $('#item_' + id).fadeOut(200, function() {
                    $(this).remove();
                });

                // Update navbar counter
                $('#brochureCount').text(res.count);

                showToast('success', 'Unit removed');
            }

        }).fail(function() {
            showToast('error', 'Error removing unit');
        });
    });


    // ============================================
    // 🔥 GENERATE BROCHURE (Save + PDF later)
    // ============================================
    $('#generateBrochureBtn').click(function() {

        let title = $('#brochure_title').val().trim();

        if (title.length == 0) {
            showToast('error', 'Enter a brochure title');
            return;
        }

        $.post('/brochure/store', {
            title: title,
            _token: "{{ csrf_token() }}"
        }, function(res) {

            if (res.success) {
                showToast('success', 'Brochure saved');

                setTimeout(() => {
                    window.location.href = "/brochure";
                }, 800);
            }

        }).fail(function() {
            showToast('error', 'Failed to save brochure');
        });

    });


    function showToast(type, message) {

        let toastId = (type === 'success') ? '#saveToast' : '#alertToast';

        if (type === 'success') {
            $('#toast_message').text(message);
        } else {
            $('#alert_message').text(message);
        }

        let toastEl = document.querySelector(toastId);
        if (toastEl) {
            let toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
    }

});
</script>
@endpush
