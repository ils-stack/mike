@if(!empty($doc_arr))
  <div class="col-xl-12">
    <div class = "row">
      <div class="col-xl-12 col-md-12">
        <div class="card">
          <div class="card-body">
            <div class = "row">
              <div class="col-xl-12 col-md-12">
                <strong>Account Documents</strong>
              </div>
              <div class="col-xl-12 col-md-12 small">
                @include('holdings.partials.doc_grid')
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endif
