<div id="dtatblRetireBudget" data-mdb-pagination="false">
</div>

<!-- current assets list -->

<script>
$( document ).ready(function() {
  retireBudgetLst();
});

function resetRBudgetLst(){
  $("#dtatblRetireBudget").html("");
}


function retireBudgetLst(){

  resetRBudgetLst();

  const columns = [
    { label: 'Client', field: 'incomename' },
    { label: 'Taxable', field: 'c_taxable' },
    { label: 'Non-Taxable', field: 'c_nontaxable' },
    { label: 'Spouse', field: 'incomename' },
    { label: 'Taxable', field: 's_taxable' },
    { label: 'Non-Taxable', field: 's_nontaxable' },
  ];

  const asyncTable = new mdb.Datatable(
    document.getElementById('dtatblRetireBudget'),
    { columns, },
    { loading: true }
  );

  //BA: comments re ... operator
  // https://jsonplaceholder.typicode.com/users
  //let array = [...value]
  // The spread syntax is a new addition to the set of operators in JavaScript ES6.
  // It takes in an iterable (e.g an array) and expands it into individual elements

  fetch('{{ route('getIncomeHeadsRt.get') }}')
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

  // const instance = new mdb.Datatable(document.getElementById('dtatblRetireBudget'), ajxData)
  //
  // document.getElementById('datatable-search-input').addEventListener('input', (e) => {
  //   instance.search(e.target.value);
  // });

}
</script>
