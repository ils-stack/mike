<div id="dtatblCurrBudget-{{$i}}" >
  <div  class="datatable">
    <table>
      <thead>
        <tr>
          <th class="th-sm">Description</th>
          <th class="th-sm">Value (Client)</th>
          <th class="th-sm">Owing (Client)</th>
          <th class="th-sm">Value (Spouse)</th>
          <th class="th-sm">Owing (Spouse)</th>
          @if(!isset($print))
            <th class="th-sm">Edit</th>
            <th class="th-sm">Delete</th>
          @endif  
        </tr>
      </thead>
      <tbody>
        <?php
        $field_arr = array("client_description","client_value","client_owing");
        $field_arr_sp = array("spouse_value","spouse_owing");
        $last_col = count($field_arr);
        $last_col_sp = count($field_arr_sp);

        unset($fld_idx);
        $fld_idx = 1;

        //BA: find the count of each category based on sorted array
        // of records in the main array
        $budget_cnt = 0;
        if(isset($sorted_rows[$budget_cats[$i]['alid']]))
          $budget_cnt = count($sorted_rows[$budget_cats[$i]['alid']]);
        ?>
        <!-- budget row iteration -->
        @for($k=0;$k<$budget_cnt;$k++)
          <tr>
          @for($j=0;$j<$last_col;$j++)
            <?php
            // client data
            unset($fld_id,$sp_fld);
            $fld_id = $client_id."_".$budget_cats[$i]['alid']."_".($fld_idx);

            $field = $field_arr[$j]."_".$fld_id;
            unset($field_data);
            $field_data = "";
            //match the field
            foreach($user_fields as $e_key => $e_val){
              foreach($e_val as $sub_key => $sub_val){
                if($sub_val == $field){
                  $field_data = $e_val['value'];
                  break;
                }
              }
            }
            ?>
            <td><input type = "text" class = "form-control" name = "{{$field}}" id = "{{$field}}" value = "{{$field_data}}"></td>
          @endfor

          @for($j=0;$j<$last_col_sp;$j++)
            <?php
            // spouse data
            unset($fld_id,$sp_fld);
            $sp_fld = $spouse_ref."_".$budget_cats[$i]['alid']."_".($fld_idx);

            $sp_field = $field_arr_sp[$j]."_".$sp_fld;
            unset($field_data);
            $field_data = "";
            //match the field
            foreach($user_fields as $e_key => $e_val){
              foreach($e_val as $sub_key => $sub_val){
                if($sub_val == $sp_field){
                  $field_data = $e_val['value'];
                }
              }
            }
            ?>
            <td><input type = "text" class = "form-control" name = "{{$sp_field}}" id = "{{$sp_field}}" value = "{{$field_data}}"></td>
          @endfor

          @if(!isset($print))
            <td>
              <button class="btn btn-secondary"
                type="button"
                onclick = "edit_record('{{$field}}','{{$sp_field}}',this)"
                name = "adder_{{$budget_cats[$i]['alid']}}"
                id = "adder_{{$budget_cats[$i]['alid']}}"
                currRow = "{{$fld_idx}}"
                data-mdb-toggle="modal"
                data-mdb-target="#budgetAddModal"
                >
                <i class="fas fa-pen text-white"></i>
              </button>
            </td>
            <td>
              <a class="btn btn-secondary" type="button" href = "/crm-assets/delete-asset/{{$budget_cats[$i]['alid']}}-{{$fld_idx}}">
                <i class="fas fa-trash text-white"></i>
              </a>
            </td>
          @endif
          </tr>
          <?php
          $fld_idx++;
          ?>
        @endfor

      </tbody>
    </table>
  </div>
</div>
