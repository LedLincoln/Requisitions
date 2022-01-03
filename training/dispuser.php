<?php
require_once 'auth.php';
require_once 'functions.php';
require_once 'arrays.php';
$gid = $_GET['id'];
$u = R::load('requser', $gid);
$uid = $u->id;
if($uid == 0) {
  exit("<p>Cannot find user</p>");
}
$un = $u->username;
$rolebeans = $u->sharedRoleList;
$eduserroles = array();
foreach($rolebeans as $rb) {
  array_push($eduserroles, $rb->rolename);
}
$allroles = R::findAll('role');
?>
<!DOCTYPE html>
<html lang='en'><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" href="css/custom.css">
<link href="open-iconic/font/css/open-iconic-bootstrap.css" rel="stylesheet">
<script
  src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous">
</script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<?php echo "<title>$un</title>\n"; ?>
</head>
<body>
<div class='container'>
<p><a href='index.php'><span class='oi oi-list'></span> List Reqs</a> <span class='horspace'></span><a href='listusers.php'><span class='oi oi-people'></span> List Users</a></p>
<?php
$uid = $u->id;
$ut = $u->title;
$up = $u->phone;
$ue = $u->email;
$um = $u->manager;
$uapp = $u->applevel;
$mbean = R::findOne('requser', 'username = ?', [$um]);
$mbid = $mbean->id;
$subs = R::findAll('requser', 'manager = ? ORDER BY username', [$un]);
echo "<p>Name: <strong>$un</strong><br>\n";
echo "Title: $ut<br>\n";
echo "Phone: $up<br>\n";
echo "Email: $ue<br>\n";
echo "Manager: <a href='dispuser.php?id=$mbid'>$um</a></p>";
if(!empty($subs)) {
  echo "Reports:\n";
  echo "<ul>\n";
  forEach($subs as $s) {
    $sid = $s->id;
    echo "<li><a href='dispuser.php?id=$sid'>$s->username</a></li>\n";
  }
}
echo "</ul>\n";
if(in_array_any(["Admin","Finance"], $userroles)) {
  include_once 'inc_eduser';
}
?>
</div>
</body>
