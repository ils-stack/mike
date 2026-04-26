<div class="card shadow">
  <div class="card-body">
    <h5 class="card-title mb-3">Tenants</h5>

    <table class="table table-striped table-bordered align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Company</th>
          <th>Entity</th>
          <th>Contact Person</th>
          <th>Telephone</th>
          <th>Cell</th>
          <th>Email</th>
          <th style="width:150px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($landlords as $landlord)
          <tr>
            <td>{{ $landlord->id }}</td>
            <td>{{ $landlord->company_name }}</td>
            <td>{{ $landlord->entity_name }}</td>
            <td>{{ $landlord->contact_person }}</td>
            <td>{{ $landlord->telephone }}</td>
            <td>{{ $landlord->cell_number }}</td>
            <td>{{ $landlord->email }}</td>
            <td>
              <button type="button" class="btn btn-sm btn-primary"
                      onclick="editLandlord({{ $landlord->id }})">
                <i class="fa fa-edit"></i> Edit
              </button>
              <button type="button" class="btn btn-sm btn-danger"
                      onclick="deleteLandlord({{ $landlord->id }})">
                <i class="fa fa-trash"></i> Delete
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center">No landlords found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@push('page-js')

@endpush
