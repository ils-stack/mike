<div class="row mt-4">
  <div class="col-md-6">
    <h5 class="">Agents</h5>
  </div>

  <div class="col-md-6 text-end">

    <!-- Please do not remove the commented block below -->

    <!-- <button type="button" class="btn btn-primary"
            onclick="assignAgents({{ $property->id }})"
            style="min-width:200px;">
      <i class="fas fa-user-plus"></i> Assign Existing Agent
    </button> -->

    <button type="button" class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#agentModal"
            onclick="resetAgentForm();">
      Add Agent
    </button>
  </div>
</div>

<div class="col-md-12">
  <div class="info-box card shadow mb-2 mt-2">

    @if($property->agents->count())
      @foreach($property->agents as $agent)
        <div class="border-bottom pb-2 mb-2">

          <!-- Edit / Delete Agent -->
          <div class="d-flex justify-content-end gap-2">
            <span title="Edit"
                  style="cursor:pointer;"
                  onclick="editAgent({{ $agent->id }});">
              <i class="fa-solid fa-pen-to-square"></i>
            </span>

            <span title="Delete"
                  style="cursor:pointer;"
                  onclick="deleteAgent({{ $agent->id }});">
              <i class="fa-solid fa-trash text-danger"></i>
            </span>
          </div>

          <p class="mb-1">
            <strong>Company:</strong> {{ $agent->company_name }}
          </p>

          @if($agent->entity_name)
            <p class="mb-1">
              <strong>Entity:</strong> {{ $agent->entity_name }}
            </p>
          @endif

          @if($agent->manager_name)
            <p class="mb-1">
              <strong>Manager:</strong> {{ $agent->manager_name }}
            </p>
          @endif

          @if($agent->contact_person)
            <p class="mb-1">
              <strong>Contact:</strong> {{ $agent->contact_person }}
            </p>
          @endif

          <p class="mb-1">
            @if($agent->telephone)
              <strong>Tel:</strong> {{ $agent->telephone }}
            @endif
            @if($agent->cell_number)
              &nbsp;|&nbsp;<strong>Cell:</strong> {{ $agent->cell_number }}
            @endif
          </p>

          @if($agent->email)
            <p class="mb-0">
              <strong>Email:</strong> {{ $agent->email }}
            </p>
          @endif

        </div>
      @endforeach
    @else
      <p class="text-muted mb-0">No agents assigned yet.</p>
    @endif

  </div>
</div>
