<div class="card shadow">
  <div class="card-body">
    <h5 class="card-title mb-3">Agents</h5>

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
        @forelse($agents as $agent)
          <tr>
            <td>{{ $agent->id }}</td>
            <td>{{ $agent->company_name }}</td>
            <td>{{ $agent->entity_name }}</td>
            <td>{{ $agent->manager_name }}</td>
            <td>{{ $agent->contact_person }}</td>
            <td>{{ $agent->telephone }}</td>
            <td>{{ $agent->cell_number }}</td>
            <td>{{ $agent->email }}</td>
            <td>
              <button type="button" class="btn btn-sm btn-primary"
                      onclick="editAgent({{ $agent->id }})">
                <i class="fa fa-edit"></i> Edit
              </button>
              <button type="button" class="btn btn-sm btn-danger"
                      onclick="deleteAgent({{ $agent->id }})">
                <i class="fa fa-trash"></i> Delete
              </button>
            </td>
          </tr>
        @empty
          <tr><td colspan="9" class="text-center">No agents found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
