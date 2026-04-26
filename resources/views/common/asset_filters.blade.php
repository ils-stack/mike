<!-- Asset Filters -->
<div class="card mb-3">
  <div class="card-body">
    <div class="row g-2 align-items-end">

      <!-- Module Filter -->
      <div class="col-md-3">
        <label class="form-label mb-1">Module</label>
        <select id="filter_modules"
                class="selectpicker form-control"
                multiple
                data-live-search="true"
                data-actions-box="true"
                title="Select module(s)">
          {{-- options populated in Step 2 --}}
        </select>
      </div>

      <!-- Type Filter -->
      <div class="col-md-3">
        <label class="form-label mb-1">Type</label>
        <select id="filter_types"
                class="selectpicker form-control"
                multiple
                data-live-search="true"
                data-actions-box="true"
                title="Select file type(s)">
          {{-- options populated in Step 2 --}}
        </select>
      </div>

      <!-- Uploaded By Filter -->
      <div class="col-md-3">
        <label class="form-label mb-1">Uploaded By</label>
        <select id="filter_users"
                class="selectpicker form-control"
                multiple
                data-live-search="true"
                data-actions-box="true"
                title="Select user(s)">
          {{-- options populated in Step 2 --}}
        </select>
      </div>

      <!-- Unassigned Filter -->
      <div class="col-md-3">
        <div class="form-check mt-4">
          <input class="form-check-input"
                 type="checkbox"
                 id="filter_unassigned">
          <label class="form-check-label" for="filter_unassigned">
            Show unassigned only
          </label>
        </div>
      </div>

    </div>
  </div>
</div>
