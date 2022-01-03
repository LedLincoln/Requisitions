<?php
$shortuser = "training";
$password = "puzzling#13pipe";
require __DIR__ . '/vendor/autoload.php';
use LdapRecord\Connection;
//    'hosts' => ['172.24.40.5','172.24.40.4']
$connection = new Connection([
    'hosts' => ['172.24.40.6','172.24.40.106']
]);
//    'base_dn' => 'dc=licor,dc=local',
echo "<pre>\n";
//$connection->connect();
print_r($connection);
$user = "cn=$shortuser,dc=licor,dc=local";
echo "</pre>\n";
//if($connection->auth()->attempt('licor\training', 'l1c0rtr')) {
if($connection->auth()->attempt('licor\training', $password)) {
//if($connection->auth()->attempt('cn=training,dc=licor,dc=local', 'l1c0rtr', true)) {
//if($connection->auth()->attempt('training@licor.com', 'l1c0rtr')) {
  echo "success";
} else {
  echo "fail";
}
exit();
//$connection->connect();
echo "$user<br>\n";
/*
try {
  $connection->auth()->attempt($user, $password);
} catch (\LdapRecord\Auth\AuthException $e) {
  $error = $e->getDetailedError();
  print_r($error);
}
exit();
*/
$result = $connection->auth()->attempt($user, $password);
print_r($result);
echo "DONE\n";
exit();

if($connection->auth()->attempt($user, $password)) {
  echo "Authentication Successful!";
} else {
  echo "You Fail!";
}
exit();

?>
