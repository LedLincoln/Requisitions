<?php
session_name("Reqs");
session_start();
//require __DIR__ . 'vendor/autoload.php';
$redirect = $_SESSION['prevpage'] ?? 'index.php';
//$username = $_SESSION['username'];
$logonname = $_SESSION['userinfo']['logon'] ?? '';
$params = session_get_cookie_params();
session_unset();
session_destroy();
$dn = "";
unset($userroles);
#ten days
$sesslife = 864000;
session_name("Reqs");
session_set_cookie_params($sesslife);
session_start();
//setcookie("CSLabels", '', time() - 42000,
setcookie("Reqs", '', 1,
	$params["path"], $params["domain"],
	$params["secure"], $params["httponly"]
);
$_SESSION['prevpage'] = $redirect;
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" href="css/custom.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Arimo">
<link href="open-iconic/font/css/open-iconic-bootstrap.css" rel="stylesheet">
<style>
.fail {
	color: red;
	background-color: #dddddd;
	border: 2px solid red;
	border-radius: 13px;
}
</style>
<title>Requisition System Login</title>
</head>
<body>
<div class='container-fluid'>
<?php
if($msg == 'loginfail') {
	echo "<div class='alert alert-danger' role='alert'>\n";
	echo "<span class='oi oi-ban'></span>\n";
	echo "Login failed - Please try again\n</div>\n";
}
?>
<div class='card smallbox'>
<form id='loginform' method='post' action='proclogin.php'>
<?php echo "<input type='hidden' name='redirect' value='$redirect'>\n"; ?>
<div class='card-body'><h6 class='card-title text-center'>Login with your LI-COR Credentials</h6>
<div class='loginfield'><p class='labl'>Username<br>
<?php echo "<input type='text' class='form-control' name='username' id='username' value='$logonname' required='required' placeholder='firstname.lastname'></p></div>\n"; ?>
<div class='loginfield'><p class='labl'>Password<br>
<input type='password' class='form-control' name='password' id='password' required='required'></p></div>
<input type='submit' class='btn btn-sm btn-primary float-right' value='Submit'>
</div>
</form>
</div>
</div> <!-- container -->
<script>
<?php
//if($username == '') {
if($logonname == '') {
  echo "document.getElementById('username').focus();\n";
} else {
  echo "document.getElementById('password').focus();\n";
}
?>
</script>
</body>
</html>
