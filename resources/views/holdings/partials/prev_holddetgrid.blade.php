<div class="row">
  <div class="col-12">
    <div class="mb-3">
      <input type="text" class="form-control" id="datatable-search-input-prev" placeholder="Search">
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">

    <table id="prevhold_tbl" class="table table-sm table-hover align-middle">

      <?php
        switch($api_tp){
          default:
          case 1:
      ?>
        <thead class="table-light">
          <tr>
            <th>Instrument Name</th>
            <th>Unit Price</th>
            <th>Sum</th>
            <th>-</th>
            <th>-</th>
          </tr>
        </thead>
      <?php
        break;
        case 2:
      ?>
        <thead class="table-light">
          <tr>
            <th>Instrument Name</th>
            <th>Unit Price</th>
          </tr>
        </thead>
      <?php
        break;
        }
      ?>

      <tbody>
        @if(isset($p_grid['Instrument name']))
          @for($i=0;$i<count($p_grid['Instrument name']);$i++)

            <?php
            switch($api_tp){
              default:
              case 1:
            ?>
              @include('holdings.partials.prev_hold_det_ele')
            <?php
              break;
              case 2:
            ?>
              @include('holdings.partials.prev_hold_det_ele_prime')
            <?php
              break;
            }
            ?>

          @endfor
        @endif
      </tbody>

    </table>

  </div>
</div>

<style>
#prevhold_tbl td:first-child{
  white-space: normal !important;
}
</style>

<script>

$(document).ready(function(){

    var tablePrev = $('#prevhold_tbl').DataTable({
        paging:false,
        info:false,
        lengthChange:false
    });

    $('#datatable-search-input-prev').on('keyup', function(){
        tablePrev.search(this.value).draw();
    });

});

</script>
