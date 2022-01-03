<?php
require_once 'auth.php';
require_once 'functions.php';
require_once 'arrays.php';
if(!in_array("Admin", $userroles)) {
  die("You are not authorized to delete buyer codes.");
}
$pbcid = $_POST['bcid'] ?? '';
$bcbean = R::load('buyercode', $pbcid);
$bcid = $bcbean->id;
if($bcid !== 0) {
  try {
    R::trash($bcbean);
    echo "Buyer code deleted.";
  } catch (Exception $e) {
    die($e->getMessage());
  }
} else {
  die('Buyer code not found.');
}
?>
