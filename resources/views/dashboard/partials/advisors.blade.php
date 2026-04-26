<div class="row g-2 mb-2">
  <div class="col-lg-12 col-md-6">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <div class="row g-2 mb-3 ">
          <div class="col-lg-6 col-md-4 col-sm-6 pb-2 border-bottom" align = "left">
            <!-- searchable advisor dropdown -->
            <label class="form-label fw-bold mt-2 mb-2">
                Advisor
            </label>

            <select class="selectpicker form-control"
                    data-live-search="true"
                    name="advisor_id"
                    id="advisor_id"
                    title="Select Advisor" autocomplete = "off">

                @foreach($advisors as $advisor)
                    <option value="{{ $advisor->id }}"
                    {{ session('advisor_id') == $advisor->id ? 'selected' : '' }}>
                        {{ $advisor->advisor_name }} ({{ $advisor->advisor_code }})
                    </option>
                @endforeach

            </select>

          </div>

          <div class="col-lg-6 col-md-4 col-sm-6 pb-2 border-bottom" align = "left">

              <label class="form-label fw-bold mt-2 mb-2">
                  Investor
              </label>

              <select class="selectpicker form-control"
                      data-live-search="true"
                      name="investor_id"
                      id="investor_id"
                      title="Select Investor"
                      autocomplete="off">

                  @if(session('advisor_id'))
                      @foreach($investors as $investor)
                          <option value="{{ $investor->id }}"
                          {{ session('investor_id') == $investor->id ? 'selected' : '' }}>
                              {{ $investor->investor_name }} ({{ $investor->client_number }})
                          </option>
                      @endforeach
                  @endif

              </select>

          </div>

        </div>
      </div>
    </div>
  </div>
</div>
