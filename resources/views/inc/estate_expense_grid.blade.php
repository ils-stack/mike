<div id="dtatblEstExp" data-mdb-pagination="false" data-mdb-sorting="false">
</div>

<!-- current assets list -->

<script>
$( document ).ready(function() {
  estExpLst();
});

function resetEExpLst(){
  $("#dtatblEstExp").html("");
}

function estExpLst(){

  resetEExpLst();

  const columns = [
    { label: 'Description', field: 'expensename',sort:false },
    { label: 'Monthly', field: 'c_taxable',sort:false },
  ];

  const asyncTable = new mdb.Datatable(
    document.getElementById('dtatblEstExp'),
    { columns, },
    { loading: true }
  );

  //BA: comments re ... operator
  // https://jsonplaceholder.typicode.com/users
  //let array = [...value]
  // The spread syntax is a new addition to the set of operators in JavaScript ES6.
  // It takes in an iterable (e.g an array) and expands it into individual elements

  fetch('{{ route('getExpenseHeadsEst.get') }}')
    .then((response) => response.json())
    .then((data) => {
    asyncTable.update(
      {
      rows: data.map((ajxData) => ({
        ...ajxData,
      })),
      },
    { loading: false }
    );
  });

  // const instance = new mdb.Datatable(document.getElementById('dtatblEstExp'), ajxData)
  //
  // document.getElementById('datatable-search-input').addEventListener('input', (e) => {
  //   instance.search(e.target.value);
  // });

}
</script>
<!-- current expense grid -->
