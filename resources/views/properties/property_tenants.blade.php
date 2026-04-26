<div class="row mt-4">
  <div class="col-md-6">
    <h5 class="">Tenants</h5>
  </div>

  <div class="col-md-6 text-end">

    <!-- Please do not remove the commented block below -->

    <!-- <button type="button" class="btn btn-primary"
            onclick="assignTenants({{ $property->id }})"
            style="min-width:200px;">
      <i class="fas fa-user-plus"></i> Assign Existing Tenant
    </button> -->

    <button type="button" class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#tenantModal"
            onclick="resetTenantForm();">
      Add Tenant
    </button>
  </div>
</div>

<div class="col-md-12">
  <div class="info-box card shadow mb-2 mt-2">

    @if($property->tenants->count())
      @foreach($property->tenants as $tenant)
        <div class="border-bottom pb-2 mb-2">

          <!-- Edit / Delete Tenant -->
          <div class="d-flex justify-content-end gap-2">
            <span title="Edit"
                  style="cursor:pointer;"
                  onclick="editTenant({{ $tenant->id }});">
              <i class="fa-solid fa-pen-to-square"></i>
            </span>

            <span title="Delete"
                  style="cursor:pointer;"
                  onclick="deleteTenant({{ $tenant->id }});">
              <i class="fa-solid fa-trash text-danger"></i>
            </span>
          </div>

          <p class="mb-1">
            <strong>Company:</strong> {{ $tenant->company_name }}
          </p>

          @if($tenant->entity_name)
            <p class="mb-1">
              <strong>Entity:</strong> {{ $tenant->entity_name }}
            </p>
          @endif

          @if($tenant->contact_person)
            <p class="mb-1">
              <strong>Contact:</strong> {{ $tenant->contact_person }}
            </p>
          @endif

          <p class="mb-1">
            @if($tenant->telephone)
              <strong>Tel:</strong> {{ $tenant->telephone }}
            @endif

            @if($tenant->cell_number)
              &nbsp;|&nbsp;<strong>Cell:</strong> {{ $tenant->cell_number }}
            @endif
          </p>

          @if($tenant->email)
            <p class="mb-0">
              <strong>Email:</strong> {{ $tenant->email }}
            </p>
          @endif

        </div>
      @endforeach
    @else
      <p class="text-muted mb-0">No tenants assigned yet.</p>
    @endif

  </div>
</div>
