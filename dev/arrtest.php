<?php
$s = "CrTn";
$arr = ['amp','btl','bx','can','case','CLMN','CRTN','EA','gm'];
$sarray = array_map('strtolower', $arr);
echo "<pre>";
print_r($sarray);
//echo array_search("ea", $sarray);
echo "<p>" . $arr[array_search(strtolower($s), $sarray)];
echo "</p></pre>";
?>
