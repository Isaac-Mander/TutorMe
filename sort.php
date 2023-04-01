<?php
$array = [
    [1, 10, "John"],
    [2, 15, "Jane"],
    [3, 20, "Mike"],
];

usort($array, function($a, $b) {
    return $a[1] <=> $b[1];
});

echo $array[0][2]; // Output: Array ( [0] => Array ( [0] => 2 [1] => 5 [2] => Jane ) [1] => Array ( [0] => 1 [1] => 10 [2] => John ) [2] => Array ( [0] => 3 [1] => 20 [2] => Mike ) )

?>