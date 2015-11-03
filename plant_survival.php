<?php

function simulate_generation($prev_state,$rows,$col,$survival_lower,$survival_upper,$birth_lower,$birth_upper,$generations){

    $new_state = $prev_state;

    for($i=0;$i<$rows;$i++){
        for($j=0;$j<$col;$j++){
            // if s1 - s2 neighbours around live cells.
            $neighbour_count = neighbours_count($prev_state,$i,$j);

            if($prev_state[$i][$j]==1){ // if the plant is already surviving
              if(($neighbour_count >=$survival_lower) && ($neighbour_count <= $survival_upper)){
                $new_state[$i][$j] = 1;
              }else{
                $new_state[$i][$j] = 0;
              }
            }elseif($prev_state[$i][$j]==0){ // if the cell is empty, check for neighbour condtion for birth
                if(($neighbour_count >=$birth_lower) && ($neighbour_count <= $birth_upper)){
                        $new_state[$i][$j] = 1;
                }else{
                        $new_state[$i][$j] = 0;
                }
            }
        }
    }

    return $new_state;
}


function load_field($input2,$rows,$col){
    $loaded_field = array(); // plotting the previous state
    
    $counter = 0;
    for($i=0;$i<$rows;$i++){
       for($j=0;$j<$col;$j++){
        $loaded_field[$i][$j]=$input2[$counter];
        $counter++;
       }
    }

    return $loaded_field;
}

function neighbours_count($modified_state,$i,$j){
  $neighbour_count = 0;
  $poss_neighbours = array(
                (isset($modified_state[$i-1][$j-1]) ? $modified_state[$i-1][$j-1]: 0),
                (isset($modified_state[$i-1][$j])? $modified_state[$i-1][$j]:0),
                (isset($modified_state[$i-1][$j+1])? $modified_state[$i-1][$j+1]:0),
                (isset($modified_state[$i][$j-1])? $modified_state[$i][$j-1]:0),
                (isset($modified_state[$i][$j+1])? $modified_state[$i][$j+1]:0),
                (isset($modified_state[$i+1][$j-1])? $modified_state[$i+1][$j-1]:0),
                (isset($modified_state[$i+1][$j])? $modified_state[$i+1][$j]:0),
                (isset($modified_state[$i+1][$j+1])? $modified_state[$i+1][$j+1]:0),
  );

  foreach($poss_neighbours as $neighbour){
    if($neighbour==1){
      $neighbour_count ++;
    }
  }

  return $neighbour_count;
}



function survivalcells($input1,$input2)   {

    $prev_state = array();
    $output = array();


    if(count($input1)!=7){
      return "Invalid input given";
    }

    $rows = $input1[0];
    $col = $input1[1];
    $survival_lower = $input1[2];
    $survival_upper = $input1[3];
    $birth_lower = $input1[4];
    $birth_upper = $input1[5];
    $generations = $input1[6];

   if(count($input2) == ($rows*$col)){
     $state = load_field($input2,$rows,$col);

     for($i=1; $i<=$generations;$i++){
        $state = simulate_generation($state,$rows,$col,$survival_lower,$survival_upper,$birth_lower,$birth_upper,$i);
     }
   }else{
     return "Invalid input given"; // invalid array given. 
   } 

   foreach($state as $rows){
      foreach($rows as $val){
          $output[]=$val;
      }
   }
   
   return $output;
   
}



?>