<div class="card shadow">
  <div class="card-body">
    <h5 class="card-title mb-3">Property Managers</h5>

    <table class="table table-striped table-bordered align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Company</th>
          <th>Entity</th>
          <th>Manager</th>
          <th>Contact Person</th>
          <th>Telephone</th>
          <th>Cell</th>
          <th>Email</th>
          <th style="width:150px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($managers as $manager)
          <tr>
            <td>{{ $manager->id }}</td>
            <td>{{ $manager->company_name }}</td>
            <td>{{ $manager->entity_name }}</td>
            <td>{{ $manager->manager_name }}</td>
            <td>{{ $manager->contact_person }}</td>
            <td>{{ $manager->telephone }}</td>
            <td>{{ $manager->cell_number }}</td>
            <td>{{ $manager->email }}</td>
            <td>
              <button type="button" class="btn btn-sm btn-primary"
                      onclick="editManager({{ $manager->id }})">
                <i class="fa fa-edit"></i> Edit
              </button>
              <button type="button" class="btn btn-sm btn-danger"
                      onclick="deleteManager({{ $manager->id }})">
                <i class="fa fa-trash"></i> Delete
              </button>
            </td>
          </tr>
        @empty
          <tr><td colspan="9" class="text-center">No property managers found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
