<div class="row mt-4">
  <div class="col-md-6">
    <h5>Landlords</h5>
  </div>
  <div class="col-md-6 text-end">

    <!-- Please do not remove the commented block below -->

    <!-- <button type="button" class="btn btn-primary"
            onclick="assignLandlords({{ $property->id }})"
            style="min-width:200px;">
      <i class="fas fa-user-plus"></i> Assign Existing Landlord
    </button> -->

    <!-- Trigger Add -->
    <button type="button" class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#addLandlordModal"
            onclick="resetLandlordForm()">
      Add Landlord
    </button>
  </div>

  <!-- do not remove the commneted block below -->
  <!-- <div class="col-md-12">
    <div class="info-box card shadow mt-2" id="landlord-list">
      @if($property->landlords->count())
        <ul>
          @foreach($property->landlords as $landlord)
            <li>{{ $landlord->company_name }} ({{ $landlord->contact_person }})</li>
          @endforeach
        </ul>
      @else
        <p class="text-muted">No landlords assigned yet.</p>
      @endif
    </div>
  </div> -->

</div>


<div class="info-box card shadow mt-2" id="landlord-list">

  @if($property->landlords->count())
    @foreach($property->landlords as $landlord)
      <div class="border-bottom pb-2 mb-2">

        <!-- Edit / Delete Landlord -->
        <div class="d-flex justify-content-end gap-2">
          <span title="Edit"
                style="cursor:pointer;"
                onclick="editLandlord({{ $landlord->id }});">
            <i class="fa-solid fa-pen-to-square"></i>
          </span>

          <span title="Delete"
                style="cursor:pointer;"
                onclick="deleteLandlord({{ $landlord->id }});">
            <i class="fa-solid fa-trash text-danger"></i>
          </span>
        </div>

        <p class="mb-1">
          <strong>Company:</strong> {{ $landlord->company_name }}
        </p>

        @if($landlord->entity_name)
          <p class="mb-1">
            <strong>Entity:</strong> {{ $landlord->entity_name }}
          </p>
        @endif

        @if($landlord->registration_number)
          <p class="mb-1">
            <strong>Registration number:</strong> {{ $landlord->registration_number }}
          </p>
        @endif

        @if($landlord->contact_person)
          <p class="mb-1">
            <strong>Contact:</strong> {{ $landlord->contact_person }}
          </p>
        @endif

        <p class="mb-1">
          @if($landlord->telephone)
            <strong>Tel:</strong> {{ $landlord->telephone }}
          @endif

          @if($landlord->cell_number)
            &nbsp;|&nbsp;<strong>Cell:</strong> {{ $landlord->cell_number }}
          @endif
        </p>

        @if($landlord->email)
          <p class="mb-0">
            <strong>Email:</strong> {{ $landlord->email }}
          </p>
        @endif

      </div>
    @endforeach
  @else
    <p class="text-muted mb-0">No landlords assigned yet.</p>
  @endif

</div>
