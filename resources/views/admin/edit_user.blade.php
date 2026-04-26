@extends('layouts.app')

@section('title', 'Edit User')

@section('content')

<div class="container my-4">
  <h3>Edit User - {{ $user->name }}</h3>

  <form method="POST" action="{{ url('/admin/update/' . $user->id) }}">
    @csrf
    @method('POST')

    <div class="mb-3">
      <label for="name" class="form-label">Name</label>
      <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
    </div>

    <div class="mb-3">
      <label for="email_verified_at" class="form-label">Email Verified At</label>
      <input type="datetime-local" name="email_verified_at" class="form-control"
        value="{{ $user->email_verified_at ? \Carbon\Carbon::parse($user->email_verified_at)->format('Y-m-d\TH:i') : '' }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Active</label>
      <select name="active" class="form-select">
        <option value="1" {{ $user->active == 1 ? 'selected' : '' }}>Yes</option>
        <option value="0" {{ $user->active == 0 ? 'selected' : '' }}>No</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Alpha</label>
      <select name="alpha" class="form-select">
        <option value="1" {{ $user->alpha == 1 ? 'selected' : '' }}>Yes</option>
        <option value="0" {{ $user->alpha == 0 ? 'selected' : '' }}>No</option>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Save Changes</button>
    <a href="{{ url('/admin/list') }}" class="btn btn-secondary">Cancel</a>
  </form>
</div>

@endsection
