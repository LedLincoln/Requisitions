<?php
require_once 'functions.php';
echo "nothing: " . isValidDate('') . "<br>";
echo "Feb 22: " . isValidDate('2019-02-22') . "<br>";
echo "Crazy: " . isValidDate('2019-02-92') . "<br>";
?>
