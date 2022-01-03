<!DOCTYPE html>
<html><head><title>Import</title></head>
<body>
<?php
$f = "test.csv";
echo "<p>Trying to open $f</p>\n";
echo "<table border='1'>\n";
$row = 1;
if(($handle = fopen($f, "r")) !== false) {
  while(($data = fgetcsv($handle, 1000, ",")) !== false) {
    //$num = count($data);
    $num = 21;
    //echo "<p>$num fields in line $row:<br><\p>\n";
    $row++;
    echo "<tr>";
    for($c = 0; $c < $num; $c++) {
      //echo $data[$c] . "<br>\n";
      $d = $data[$c];
      echo "<td>$d</td>";
    }
    echo "</tr>\n";
  }
  echo "</table>\n";
  fclose($handle);
} else {
  echo "<p>Cannot find $f.</p>\n";
}
?>
