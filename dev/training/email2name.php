<?php
$email = "johnny.doe@licor.com";
$atpos = strpos($email, "@");
$nameonly = substr($email, 0, $atpos);
echo "|$nameonly|";
?>
