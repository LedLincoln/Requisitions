<?php
require_once 'auth.php';
$plid = $_POST['lineid'];
$linebean = R::load('lineitem', $plid);
$lid = $linebean->id;
if($lid != 0) {
  R::trash($linebean);
  R::close();
} else {
  die("<p>Error: Line item not found.</p><p><a href='/'>Go Back</a></p>\n");
}
?>
