<div class="row mt-4">
  <div class="col-md-6">
    <h5 class="">Property Managers</h5>
  </div>

  <!-- Please do not remove the commented block below -->

  <div class="col-md-6" align = "right">

    <!--  <button type="button" class="btn btn-primary"
            onclick="assignLandlords({{ $property->id }})"
            style="min-width:200px;">
      <i class="fas fa-user-plus"></i> Assign Existing Property Manager
    </button> -->

    <button type="button" class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#managerModal"
            onclick="resetManagerForm();">
      Add Property Manager
    </button>
  </div>
</div>

<div class="col-md-12">
  <div class="info-box card shadow mb-2 mt-2">

    @if($property->propertyManagers->count())
      @foreach($property->propertyManagers as $manager)
        <div class="border-bottom pb-2 mb-2">

          <!-- Edit / Delete Property Manager -->
          <div class="d-flex justify-content-end gap-2">
            <span title="Edit"
                  style="cursor:pointer;"
                  onclick="editManager({{ $manager->id }});">
              <i class="fa-solid fa-pen-to-square"></i>
            </span>

            <span title="Delete"
                  style="cursor:pointer;"
                  onclick="deleteManager({{ $manager->id }});">
              <i class="fa-solid fa-trash text-danger"></i>
            </span>
          </div>

          <p class="mb-1"><strong>Company:</strong> {{ $manager->company_name }}</p>

          @if($manager->entity_name)
            <p class="mb-1"><strong>Entity:</strong> {{ $manager->entity_name }}</p>
          @endif

          @if($manager->manager_name)
            <p class="mb-1"><strong>Manager:</strong> {{ $manager->manager_name }}</p>
          @endif

          @if($manager->contact_person)
            <p class="mb-1"><strong>Contact:</strong> {{ $manager->contact_person }}</p>
          @endif

          <p class="mb-1">
            @if($manager->telephone)
              <strong>Tel:</strong> {{ $manager->telephone }}
            @endif
            @if($manager->cell_number)
              &nbsp;|&nbsp;<strong>Cell:</strong> {{ $manager->cell_number }}
            @endif
          </p>

          @if($manager->email)
            <p class="mb-0"><strong>Email:</strong> {{ $manager->email }}</p>
          @endif

        </div>
      @endforeach
    @else
      <p class="text-muted mb-0">No property managers assigned yet.</p>
    @endif

  </div>
</div>
