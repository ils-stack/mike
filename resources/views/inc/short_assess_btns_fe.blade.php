<!-- communication buttons here  -->

<!-- @ include('inc.comm') -->

<?php
$variable[] = "Estate";
$variable[] = "Disability Calculator";
$variable[] = "Retirement Calculator";
$variable[] = "Tax Calculator";

$variable[0] = "Estate Calculator";

$variable_link[] = "/short-assessments";
$variable_link[] = "/short-assessments/disabilty";
$variable_link[] = "/short-assessments/retirement";
$variable_link[] = "/short-assessments/tax-calc";

$variable_class[] = "btn-primary";
$variable_class[] = "btn-primary";
$variable_class[] = "btn-primary";
$variable_class[] = "btn-primary";

// $needle = preg_replace("|^/|","",$variable_link[$key]);
// if(Request::is("short-assessments")){
//   echo 1;
// }
// exit;
?>

<!-- communication buttons here  -->

<!-- <div class = "p-3">&nbsp;</div> -->

<div role="group" aria-label="Control Panel" class="d-flex flex-wrap gap-2 justify-content-center mb-4">
@foreach($variable as $each_key => $each_val)
  @php
  $needle = preg_replace("|^/|","",$variable_link[$each_key]);
  @endphp
  @if(Request::is($needle))
    @php
      $variable_class[$each_key] = "btn-secondary";
    @endphp
  @endif
  <a class="btn {{$variable_class[$each_key]}}" href = "{{$variable_link[$each_key]}}">{{$each_val}}</a>
@endforeach
</div>

<!-- <div role="group" aria-label="Control Panel" class = "text-center">
  <a class="btn mb-3 btn-primary" href = "/short-assessments">Estate</a>
  <a class="btn mb-3 btn-primary" href = "/short-assessments/disabilty">Disability</a>
  <a class="btn mb-3 btn-primary" href = "/short-assessments/retirement">Retirement</a>
</div> -->
