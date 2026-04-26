<div class="row">
  <div class="col-12">
    <div class="mb-3">
      <input type="text" class="form-control" id="datatable-search-input" placeholder="Search">
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">

    <table id="datatable_holdings" class="table table-sm table-hover align-middle">
      <?php
        switch($api_tp){
          default:
          case 1:
      ?>
        <thead class="table-light">
          <tr>
            <th>Instrument Name</th>
            <th>Unit Price</th>
            <th>Units</th>
            <th>Sum</th>
            <th>Movement</th>
            <th>Fact Sheet</th>
            <th>View</th>
            <th>Transactions</th>
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
            <th>Units</th>
            <th>Sum</th>
            <th>Transactions</th>
          </tr>
        </thead>
      <?php
        break;
        }
      ?>

      <tbody>
        @if(isset($c_grid['Instrument name']))
          @for($i=0;$i<count($c_grid['Instrument name']);$i++)

            <?php
            switch($api_tp){
              default:
              case 1:
            ?>
              @include('holdings.partials.holddet_ele')
            <?php
              break;
              case 2:
            ?>
              @include('holdings.partials.holddet_ele_prime')
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
#datatable_holdings td:first-child {
  white-space: normal !important;
}
</style>

<script>
$(document).ready(function(){

    var table = $('#datatable_holdings').DataTable({
        paging:false,
        info:false,
        lengthChange:false
    });

    $('#datatable-search-input').on('keyup', function(){
        table.search(this.value).draw();
    });

});
</script>
