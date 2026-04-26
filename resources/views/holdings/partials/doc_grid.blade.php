<div id="datatable_holdings" data-mdb-pagination="false" class="datatable">
  <table class="table">
    <thead>
      <tr>
        <th class="th-sm">Type</th>
        <th class="th-sm">Label</th>
        <th class="th-sm">Date</th>
        <th class="th-sm">View</th>
      </tr>
    </thead>
    <tbody>
      @if(!empty($doc_arr))
        @for($i=0;$i<count($doc_arr);$i++)
          @include('holdings.partials.docgrid_ele')
        @endfor
      @endif
    </tbody>
  </table>
</div>
