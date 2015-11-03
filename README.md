# The Plant Survival program
This repository contains record of how can one approach the plant survival problem. I am not sure if this problem has been discussed before. There might be problems similar to this. However, I encountered this problem in the IndiaProperty.com Hiring Challenge on TechGig held in the month of October 2015.

## The Problem
Consider a field which is divided into many square blocks. Some blocks have a plant while some do not. The purpose of the program is to determine the state of the field in different generations. There may be new plants born, some might continue to survive while some may die depending on certain conditions.

### Conditions.
1. A plant survives into next generation if the number of plants surrounding it are within a range. Lets call this (S1-S2). So for (2-4), the plant will survive if the neighbouring plant count is between 2 and 4. Anything less than 2 and more than 4, the plant will not survive.
2. Similarly, a new plant is born on an empty block if the number of neighbouring plants is within a range. Lets call this (B1-B2).

### So what is the input and output of the program ?
1. Field dimensions.(D1,D2) Yes, this is dynamic.
2. Survival Range. (S1-S2).
3. Birth Range. (B1-B2).
3. Number of generations to simulate. (G)
4. Initial state of the field [0,1,0,1,1,0,1,0....] where 0 represents an empty block and 1 represents a plant.
5. Output should be a one dimensional array of 0s and 1s depending on the conditions.

The above inputs can also be represented in the form of two arrays

1. [D1,D2,S1,S2,B1,B2,G].
2. [A00,A01,A02,A03,A04,A10,A11,A12....A44]

So, an input of [4,5,2,4,3,4,3] and [0,1,0,1,0,1,1,0,1,1,1,0,1,1,0,1,1,0,1,0] translates to 
"Simulate through 3 generations of a field which has a dimension of 4 x 5 where a plant will survive if the neighbouring plant count is between 2 to 4 and a plant will be born if the neighbouring count is between 3 to 4 with an initial state of: 
|0 1 0 1 0|

|1 1 0 1 1|

|1 0 1 1 0|

|1 1 0 1 0|
"
If you have understood the problem, you are already halfway there. Now, you can channelize your thoughts towards the solution.

Lets start with some pseudocode and then slowly drill down. Looking at the problem, we can clearly say that there is a good amount of looping involved. So lets start with the most obvious one, the generation loop:

```php
$generations = 3;
for($i=0; $i<$generations ; $i++) {
	// simulate the field one generation at a time.
}
```
We would definitely need one function which handles this simulation in the loop. Lets name it "simulate_generation". Its very important to name your functions correctly. Not only does this help others when reading your code, but also helps you in progressing correctly with your code. It just makes you think in the right direction. Moving on.

What should be the input and output of this function? Since the purpose of this function is to simulate one field generation, the input must be the previous state of field and the output should be the new state after one generation. So, lets do that.

```php
function simulate_generation($input_state){
	// write the logic which simulates.
	return $output_state;
}
```

We need to keep on updating the input state after every generation. So, the output of the simulator becomes the input for the next generation. Lets improve the generation loop now.

```php
$generations = 3;
$input_state = array(0,1,0,1,0,1,1,0,1,1,1,0,1,1,0,1,1,0,1,0);

for($i=0; $i < $generations ; $i++) {
	$input_state = simulate_generation($input_state);
}
```
Before starting to write the simulate_generation function which is the most critical part of the code, lets think and try to break it to smaller pieces of code. To list down:

 ```php
function simulate_generation($input_state){
  // 1. Loop through each and every element.
  // 2. As we loop through, we will have to consider the neighbour count of each element. It would be good to write a function which returns the neighbour count given the position as the input.
  // 3. Once we get the neighbour count, we will check with two things - survival range and birth range.
  // 4. If the current element has a plant(i.e is 1), check if within survival range. If yes, set value to 1 else 0.
  // 5. If the current element is empty, check if within birth range. If yes, set value to 1 else 0.
  return $output_state;
}
```

Ok, Task 1. We want to visualize the output in plant field, but the given array is provided in 1-dimensional. It makes sense to convert into a 2 dimensional array. So, lets write a function which converts to it. This is so that we can loop through each element inside simulate_generation.

 ```php
/**
* Loads the 1-dimension field into a 2-dimensional array.
*/
function load_field($input_array,$rows,$col){
    $loaded_field = array(); // plotting the previous state
    
    $counter = 0;
    for($i=0;$i<$rows;$i++){
       for($j=0;$j<$col;$j++){
        $loaded_field[$i][$j]=$input_array[$counter];
        $counter++;
       }
    }

    return $loaded_field;
}
```

Lets go to Task 2. A function to return the neighbour count. The number of neighbours in a field should be between 1 and 8. Example:


|1 0|


|1 0|

|0 1|

|1 1|

and 

|0 1 0 1 0|

|1 1 0 1 1|

|1 0 1 1 0|

|1 1 0 1 0|

You could notice that the ones at the corner will have the least neighbours, followed by the edge blocks and finally the center blocks will have the maximum neighbours. It is important to consider only the neighbour blocks which have plants. 

These are too many cases to consider. So, here instead of considering different scenarios,lets assume 8 neighbours(maximum possible) for all and checked if they exist and hold a value of 1.

 ```php
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
```

The above code does not look efficient, but we will consider it if there are any issues in future. It is sometimes better to start optimizing only when the need is. It saves time to consider more critical things.

Lets move to the final tasks 3,4 and 5 and finish the simulator function.

 ```php
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
```
Finally put everything in a triggering function.

 ```php
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
```

Lets write some "developer" code which would help us in debugging and improving our code clearly. So, lets write a function which will accept a 2-dimension array and prints it in a nice matrix of Plant field. Lets call this "print_field"

```php
function print_field($array){
  echo "<pre>";
  foreach($array as $values){
    foreach ($values as $val) {
      echo $val." ";
    }
    echo "\n";
  }
  echo "</pre>";
}
```

Don't forget to test ! Cheers.


