<?php
$active = "";

$btn_arr["/budget"] = "Current Budget";
$btn_arr["/budget/estate"] = "Estate Budget";
$btn_arr["/budget/disability"] = "Disability Budget";
$btn_arr["/budget/retirement"] = "Retirement Budget";

unset($active);
echo "<div class = \"text-center\">";
foreach($btn_arr as $c_key => $c_val){
  if(Request::is(preg_replace("|^/|","",$c_key))){
    $active = "btn-dark";
  }else{
    $active = "btn-primary";
  }
  ?>
  <a type="button" class="btn mb-3 <?php echo $active; ?>" href = "<?php echo $c_key; ?>">
    <i class="fas fa-dollar-sign text-white"></i>
    <?php echo $c_val; ?>
  </a>
  <?php
}
echo "</div>";
?>
