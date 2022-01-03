<?php
require_once 'auth.php';
require_once 'functions.php';
$role = $_GET['role'];
$admins = getRoleMembers($role);
$adminstr = implode(", ", $admins);
print_r($admins);
echo "Group: $adminstr\n";
$a = [];
$a["ABC"]="DEF";
$b = array();
$b["ABC"]="DEF";
var_dump($a);
var_dump($b);
?>
