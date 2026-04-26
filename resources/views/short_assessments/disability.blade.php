@extends('layouts.app')

@section('title', 'Short Assessments')

@section('content')
<div class="container-fluid pt-4">
  <section class="mb-4">
    <div class="card">
      <div class="card-header py-3">
        <h5 class="mb-0 text-center"><strong>Assessment Calculators</strong></h5>
      </div>
      <div class="card-body" style="min-height:800px;">
        @include('inc.short_assess_btns_fe')
        <div>
          @include('inc.disability_short_fe')
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
