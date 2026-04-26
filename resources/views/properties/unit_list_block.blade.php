<div class="col-md-12">
  <div class="info-box card shadow mt-2 mb-2">
    <div class="row">

      <div class="col-md-10">
        @php
          $status = $unit->unit_status
            ? \App\Models\PropertyStatus::find($unit->unit_status)
            : null;
        @endphp

        <p><strong>Unit No:</strong> {{ $unit->unit_no ?? '-' }}</p>
        <p><strong>Unit Type:</strong> {{ $unit->unit_type ?? '-' }}</p>

        {{-- ✅ UNIT STATUS --}}
        <p>
          <strong>Status:</strong>
          @if($status)
            <span style="
              display:inline-block;
              width:20px;
              height:20px;
              border-radius:50%;
              background:{{ $status->marker_color }};
              margin-right:6px;
              vertical-align:middle;
            "></span>
            {{ $status->status }}
          @else
            -
          @endif
        </p>

        <p><strong>Size:</strong> {{ $unit->unit_size }} m²</p>
        <p><strong>Gross Rental:</strong> {{ $unit->gross_rental ?? '-' }}</p>
        <p><strong>Sale Price:</strong> {{ $unit->sale_price ?? '-' }}</p>
        <p><strong>Yield:</strong> {{ $unit->yield_percentage ? $unit->yield_percentage.'%' : '-' }}</p>
        <p><strong>Availability:</strong> {{ $unit->availability ?? '-' }}</p>

        <!-- BA: unit photos -->

        @if(!empty($unit->images) && $unit->images->count())
          <div class="mt-3 unit-image-strip"
               data-unit-id="{{ $unit->id }}">

            <ul class="list-group sortable-unit-images">

              @foreach($unit->images as $img)
                <li class="list-group-item d-flex align-items-center unit-image-row"
                    data-asset-id="{{ $img->id }}">

                  <!-- ☰ drag handle -->
                  <span class="drag-handle me-3 text-muted"
                        style="cursor:move;">
                    <i class="fa fa-grip-lines"></i>
                  </span>

                  <!-- 🖼️ thumbnail -->
                  <img src="{{ $img->url }}"
                       class="rounded me-3"
                       style="width:60px;height:60px;object-fit:cover;cursor:pointer;"
                       onclick="openUnitImageModal('{{ $img->url }}')">

                  <!-- spacer / future meta -->
                  <div class="flex-grow-1 text-muted small">
                    Image
                  </div>

                  <!-- ❌ unassign -->
                  <span class="text-danger ms-2"
                        style="cursor:pointer;"
                        title="Unassign image"
                        onclick="unassignUnitImage({{ $unit->id }}, {{ $img->id }})">
                    <i class="fa fa-times"></i>
                  </span>

                </li>
              @endforeach

            </ul>
          </div>
        @endif


        <!-- big photo modal -->

        <div class="modal fade" id="unitImagePreviewModal" tabindex="-1">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-body text-center">
                <img id="unitImagePreview"
                     src=""
                     class="img-fluid rounded">
              </div>
            </div>
          </div>
        </div>

      

      </div>

      <div class="col-md-2 text-end">

        <!-- Edit Unit -->
        <span title="Edit"
              style="cursor:pointer;"
              onclick="editUnit({{ $unit->id }});">
          <i class="fa-solid fa-pen-to-square"></i>
        </span>

        &nbsp;&nbsp;

        <!-- Delete Unit -->
        <span title="Delete"
              style="cursor:pointer;"
              onclick="deleteUnit({{ $unit->id }});">
          <i class="fa-solid fa-trash text-danger"></i>
        </span>

        &nbsp;&nbsp;

        <!-- Add Unit to Brochure -->
        <span class="addUnitToBrochure"
              title="Add Unit to Brochure"
              style="cursor:pointer;"
              data-id="{{ $unit->id }}">
          <i class="fa-solid fa-file-circle-plus"></i>
        </span>

        &nbsp;&nbsp;

        <span title="Unit Documents"
              style="cursor:pointer;"
              onclick="openPropertyDocsModal('unit_doc', {{ $unit->id }}, @js('Unit Documents - ' . ($unit->unit_no ?? 'Unit '.$unit->id)))">
          <i class="fa-solid fa-file-lines text-primary"></i>
        </span>
      </div>

    </div>
  </div>
</div>
