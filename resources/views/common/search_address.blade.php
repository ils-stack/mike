<div class="row">
  <!-- Main Content -->
  <div class="col-md-12">
    <h5>Search Address</h5>

    <div class="mb-3">
      <input type="text" id="searchBox" class="form-control" placeholder="Enter address">
    </div>

    <input type="hidden" id="fullAddress" class="form-control" readonly>

    <div class="row">
      <div class="col-sm-6 mb-3">
        <div class="row">
          <div class="col-sm-6 mb-3">
            <label for="latitude">Latitude</label>
            <input type="text" id="latitude" class="form-control" readonly>
          </div>
          <div class="col-sm-6 mb-3">
            <label for="longitude">Longitude</label>
            <input type="text" id="longitude" class="form-control" readonly>
          </div>
        </div>
      </div>

      <div class="col-sm-6 mb-3 pt-4 d-flex align-items-start gap-2">
        <!-- Add Property Button -->
        <!-- <button
          type="button"
          class="btn btn-primary"
          data-bs-toggle="modal"
          data-bs-target="#addPropertyModal"
          onclick="populate_prop();"
        >
          Add Property
        </button> -->

        <!-- Help / Instructions Button -->
        <button
          type="button"
          class="btn btn-outline-primary"
          data-bs-toggle="modal"
          data-bs-target="#mapInstructionsModal"
          title="How to add a property"
        >
          <i class="fa-solid fa-circle-question"></i>
        </button>
      </div>
    </div>
  </div>
</div>
