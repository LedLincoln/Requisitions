<?php
#ten days
$sesslife = 864000;
session_name("Reqs");
session_set_cookie_params($sesslife);
session_start();

$username = strtolower($_POST["username"]); //remove case sensitivity on the username
//allow email address
$atpos = strpos($username, "@");
if($atpos) {
  $username = substr($username, 0, $atpos);
}
$shortuser = substr($username, 0, 20); //samaccountname is limited to 20 characters
$sam = "licor\\" . $shortuser;
$password = $_POST["password"];
$redirect = $_POST['redirect'] ?? 'index.php';
require __DIR__ . '/vendor/autoload.php';
use LdapRecord\Connection;
$connection = new Connection([
    'hosts' => ['172.24.40.6','172.24.40.106'],
]);

if($connection->auth()->attempt($sam, $password)) {
  require_once 'dbsetup.php';
  $userbean = R::findOne('requser', 'logon = ?', [$shortuser]);
  if(!$userbean) {
    die("Something went wrong. Authentication was successful, but you are not found in the Req system. Contact a system administrator for support.");
  }
  $_SESSION["username"] = $userbean->username;
  //some of these will probably not be used
  $_SESSION["userinfo"]["logon"] = $userbean->logon;
  $_SESSION["userinfo"]["manager"] = $userbean->manager;
  $_SESSION["userinfo"]["phone"] = $userbean->phone;
  $_SESSION["userinfo"]["deptnum"] = $userbean->deptnum;
  $_SESSION["userinfo"]["email"] = $userbean->email;

  header("location:$redirect");
} else {
  die("Invalid username or password. <a href='login.php'>Try again</a>\n");
}
?>
