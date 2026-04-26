@extends('layouts.app')

@section('title', 'Brochure Preview')

@section('content')

<style>
    .brochure-page {
        background: #ffffff;
        padding: 30px;
        margin: auto;
        max-width: 900px;
        box-shadow: 0 0 10px rgba(0,0,0,0.15);
        font-family: Arial, sans-serif;
    }

    .hero-img {
        width: 100%;
        height: 360px;
        object-fit: cover;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .img-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .grid-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 5px;
    }

    .section-title {
        font-size: 20px;
        font-weight: bold;
        margin-top: 20px;
        border-bottom: 2px solid #000;
        padding-bottom: 4px;
    }

    .info-table td {
        padding: 6px 0;
        font-size: 14px;
    }
</style>

<div class="brochure-page">

    {{-- ================================ --}}
    {{--   LOAD RANDOM IMAGES (SERVER)   --}}
    {{-- ================================ --}}
    @php
        $folder = storage_path('app/public/assets/user_4/images');

        $files = collect(glob($folder.'/*.*'))
            ->map(fn($f) => str_replace(storage_path('app/public'), '/storage', $f))
            ->take(5); // use max 5 pics

        $hero = $files->first();
        $others = $files->slice(1);
    @endphp


    {{-- ================================ --}}
    {{--      HERO IMAGE TOP SECTION      --}}
    {{-- ================================ --}}
    @if($hero)
        <img src="{{ $hero }}" class="hero-img">
    @endif


    {{-- ================================ --}}
    {{-- PROPERTY INFORMATION --}}
    {{-- ================================ --}}
    <h2>{{ $property->building_name }}</h2>
    <p style="margin-top:-6px; color:#666;">{{ $property->address }}</p>

    <table class="info-table" width="100%">
        <tr><td><strong>ERF No:</strong></td><td>{{ $property->erf_no }}</td></tr>
        <tr><td><strong>ERF Size:</strong></td><td>{{ $property->erf_size }}</td></tr>
        <tr><td><strong>GLA:</strong></td><td>{{ $property->gla }}</td></tr>
        <tr><td><strong>Zoning:</strong></td><td>{{ $property->zoning }}</td></tr>
        <tr><td><strong>Locale:</strong></td><td>{{ $property->property_locale }}</td></tr>
    </table>


    {{-- ================================ --}}
    {{--         UNITS INFORMATION        --}}
    {{-- ================================ --}}
    <h3 class="section-title">Available Units</h3>

    @foreach($units as $unit)
        <div style="margin-bottom:12px;">
            <strong>Unit {{ $unit->unit_no }}</strong> — {{ $unit->unit_type }}<br>
            {{ $unit->unit_size }} m² | Rental: R{{ $unit->gross_rental }} |
            Sale: R{{ $unit->sale_price }} | Yield: {{ $unit->yield_percentage }}%
        </div>
    @endforeach


    {{-- ================================ --}}
    {{--      IMAGE GRID BELOW HERO       --}}
    {{-- ================================ --}}
    @if($others->count() > 0)
        <h3 class="section-title">Gallery</h3>

        <div class="img-grid">
            @foreach($others as $img)
                <img src="{{ $img }}" class="grid-img">
            @endforeach
        </div>
    @endif


    {{-- ================================ --}}
    {{--      CONTACT INFORMATION         --}}
    {{-- ================================ --}}
    <h3 class="section-title">Contact</h3>

    <p>
        <strong>{{ $agent->manager_name ?? 'Agent Name' }}</strong><br>
        {{ $agent->company_name ?? 'Company' }}<br>
        {{ $agent->cell_number ?? '000 000 0000' }}<br>
        {{ $agent->email ?? 'agent@example.com' }}
    </p>

</div>

@endsection
