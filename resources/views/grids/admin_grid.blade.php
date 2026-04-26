<h3>User Admin</h3>

<div class = "p-1">
  <a href="{{ url('/admin/create') }}" class="btn btn-success mb-3">
    <i class="fas fa-plus"></i> Add New User
  </a>
</div>

<table id="usersTable" class="table table-striped table-bordered" style="width:100%">
  <thead class="table-dark">
    <tr>
      <th>Name</th>
      <th>Email</th>
      <th>Role</th>
      <th>Created At</th>
      <th>Updated At</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    @for($i=0;$i<count($usr_dta);$i++)
      <tr>
        <!-- <td>{{$usr_dta[$i]['name']??''}}</td> -->

        <td>
          {{ $usr_dta[$i]['name'] }}
          @if($usr_dta[$i]['alpha'] == 1)
            <span class="text-muted">(Alpha)</span>
          @endif
        </td>

        <td>{{$usr_dta[$i]['email']??''}}</td>
        <td>
          <select class="form-select form-select-sm role-select"
                  data-id="{{ $usr_dta[$i]['id'] }}"
                  {{ $usr_dta[$i]['alpha'] == 1 ? 'disabled' : '' }}  autocomplete = "off">
            <option value="Super Admin" {{ $usr_dta[$i]['role']=='Super Admin'?'selected':'' }}>Super Admin</option>
            <option value="Admin" {{ $usr_dta[$i]['role']=='Admin'?'selected':'' }}>Admin</option>
            <option value="User" {{ $usr_dta[$i]['role']=='User'?'selected':'' }}>User</option>
          </select>
        </td>
        <td>{{$usr_dta[$i]['created_at_format']??''}}</td>
        <td>{{$usr_dta[$i]['updated_at_format']??''}}</td>
        <td class="text-center">
          <a href="/admin/edit/{{($usr_dta[$i]['id']??0)}}" class="btn btn-sm btn-primary me-1">
            <i class="fas fa-edit"></i>
          </a>

          <a href="#"
             class="btn btn-sm btn-warning me-1 deactivate-btn"
             data-id="{{ $usr_dta[$i]['id'] }}"
             data-active="{{ $usr_dta[$i]['active'] }}">
             <i class="fas fa-user-slash"></i>
          </a>

          <a href="#"
             class="btn btn-sm btn-danger delete-btn"
             data-id="{{ $usr_dta[$i]['id'] }}">
             <i class="fas fa-trash-alt"></i>
          </a>

        </td>
      </tr>
    @endfor
  </tbody>
</table>
