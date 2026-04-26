@extends('layouts.app')
@section('title','Reports')
@section('content')
<div class="container py-3">
    <h4 class="mb-4 text-center">Reports</h4>

    <div class="d-flex justify-content-center">
        <div class="btn-group" role="group" aria-label="Report actions">
            <a
                href="{{ url('/reports/policy-schedule') }}"
                target="_blank"
                rel="noopener"
                class="btn btn-primary"
            >
                Policy Schedule
            </a>
        </div>
    </div>
</div>
@endsection
