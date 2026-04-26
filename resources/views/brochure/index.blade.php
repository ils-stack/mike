@extends('layouts.app')

@section('title', 'Brochures')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="fa-solid fa-file-pdf text-danger"></i>
            Brochures
        </h4>

        <a href="/brochure/create" class="btn btn-primary">
            <i class="fa-solid fa-plus-circle"></i> Create New Brochure
        </a>
    </div>

    <!-- Brochure List -->
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-striped table-bordered mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="60">#</th>
                        <th>Title</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th width="260">Actions</th>
                    </tr>
                </thead>

                <tbody id="brochureTableBody">

                    @forelse($brochures as $br)

                        <tr id="row_{{ $br->id }}">
                            <td>{{ $br->id }}</td>

                            <td>{{ $br->title ?? 'Untitled' }}</td>

                            <td>{{ $br->user->name ?? 'Unknown' }}</td>

                            <td>{{ $br->created_at }}</td>

                            <td>

                                <!-- Preview -->
                                <a href="{{ route('brochure.preview', $br->id) }}"
                                   class="btn btn-sm btn-secondary"
                                   target="_blank">
                                    <i class="fa-solid fa-eye"></i>
                                    Preview
                                </a>

                                <!-- Download PDF -->
                                <a href="{{ route('brochure.download', $br->id) }}"
                                   class="btn btn-sm btn-success">
                                    <i class="fa-solid fa-file-arrow-down"></i>
                                    PDF
                                </a>

                                <!-- Delete -->
                                <button class="btn btn-sm btn-danger deleteBrochure"
                                        data-id="{{ $br->id }}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>

                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="fa-solid fa-circle-info text-primary"></i>
                                No brochures found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>

        </div>
    </div>

</div>

@endsection


@push('page-js')
<script>
$(document).ready(function() {

    // 🔥 Delete brochure AJAX
    $('.deleteBrochure').click(function() {
        const id = $(this).data('id');

        if (!confirm("Delete this brochure permanently?")) {
            return;
        }

        $.ajax({
            url: "/brochure/delete/" + id,
            type: "DELETE",
            data: { _token: "{{ csrf_token() }}" },

            success: function(res) {
                if (res.success) {
                    $('#row_' + id).fadeOut(300, function() {
                        $(this).remove();
                    });

                    showToast('success', 'Brochure deleted');
                }
            },

            error: function() {
                showToast('error', 'Failed to delete. Try again.');
            }
        });
    });

});

/* required do not remove  */
function initMap(){}

</script>
@endpush
