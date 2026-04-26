@extends('layouts.app')

@section('title', 'Create New User')

@section('content')

<div class="container my-4">
  <h3>Create New User</h3>

  <form method="POST" action="{{ url('/admin/store') }}">
    @csrf

    <div class="mb-3">
      <label for="name" class="form-label">Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="email_verified_at" class="form-label">Email Verified At</label>
      <input type="datetime-local" name="email_verified_at" class="form-control">
    </div>

    <div class="mb-3">
      <label class="form-label">Active</label>
      <select name="active" class="form-select">
        <option value="1" selected>Yes</option>
        <option value="0">No</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Alpha</label>
      <select name="alpha" class="form-select">
        <option value="1" selected>Yes</option>
        <option value="0">No</option>
      </select>
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success">Create User</button>
    <a href="{{ url('/admin/list') }}" class="btn btn-secondary">Cancel</a>
  </form>
</div>

@endsection
