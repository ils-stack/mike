@extends('layouts.app')

@section('title','Dashboard')

@section('content')

<div class="container-fluid">

    @include('dashboard.partials.quicklinks')

    @include('dashboard.partials.advisors')

    @include('dashboard.partials.contracts')

</div>

@endsection


@push('page-js')
<script>

$(function(){

    $('#advisor_id').on('changed.bs.select', function () {

        var advisor_id = $(this).val();

        var url = new URL(window.location.href);
        url.searchParams.set('advisor_id', advisor_id);

        window.location.href = url.toString();

    });

    $('#investor_id').on('changed.bs.select', function () {

        var investor_id = $(this).val();

        var url = new URL(window.location.href);
        url.searchParams.set('investor_id', investor_id);

        window.location.href = url.toString();

    });

});


$(document).ready(function(){

    $('#accountsTable').DataTable({
        paging:false,
        info:false,
        searching:true
    });

    const ctx = document.getElementById('portfolioChart');

    if(ctx){

        new Chart(ctx, {

            type: 'pie',

            data: {

                labels: {!! json_encode($chartData->keys()) !!},

                datasets: [{
                    data: {!! json_encode($chartData->values()) !!},

                    backgroundColor: [
                        '#e6b35a',
                        '#6c757d',
                        '#0d6efd',
                        '#198754',
                        '#dc3545',
                        '#20c997'
                    ]
                }]

            },

            options:{
                responsive:true,
                plugins:{
                    legend:{
                        position:'bottom'
                    }
                }
            }

        });

    }

});

</script>
@endpush
