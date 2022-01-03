<?php
require_once 'auth.php';
require_once 'functions.php';
//not currently needed
//require_once 'arrays.php';
$sort = $_GET['orderby'];
switch($sort) {
  case 'deptnam':
    $ob = 'ORDER BY deptnam, username';
    break;
  case 'deptnum':
    $ob = 'ORDER BY deptnum, username';
    break;
  case 'applevel':
    $ob = 'ORDER BY applevel, username';
    break;
  default:
    $ob = 'ORDER BY username';
}
$users = R::findAll('requser', $ob);
?>
<!DOCTYPE html>
<html lang='en'><head>
<meta charset="utf-8">
<!-- <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> -->
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> -->
<link href="open-iconic/font/css/open-iconic-bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/simplelist.css">
<style>
table {
  margin: 0 4em;
  font-family: Arial, Helvetica, sans-serif;
}
tr:nth-child(even) {background-color: #ccffcc;}
</style>
<script
  src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous">
</script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js'></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<title>Requisitions Users</title>
</head>
<body>
<div class='container'>
<div class='listhead'>
<span class='bigpart'>Requisitions User Profiles</span>
<span class='smallpart text-center'><a href='listusers.php'>Simple Listing</a> 
<button type='button' class='btn btn-primary' id='listbtn'><span class='oi oi-list'></span> List Reqs</button>
<button type='button' class='btn btn-primary' id='newbtn'><span class='oi oi-plus'></span> New Req</button>
<?php
//there is currently no need for an Admin button, since we are on the only admin page
/*
if(in_array("XAdmin", $userroles)) {
  echo "<span class='dropdown'>\n";
  echo "<button class='btn btn-primary dropdown-toggle' data-toggle='dropdown' type='button'><span class='oi oi-cog'></span> Admin</button>\n";
  echo "<div class='dropdown-menu'>\n";
  echo "<a class='dropdown-item' href='#'>System Settings</a>\n";
  echo "</div>\n";
  echo "</span>\n";
}
*/
?>
</span>
</div>
<div class='space-56'></div>
<p>Approval levels: 
<?php
print_r($approvallevels);
?>
</p>
<table class='table table-striped table-sm'>
<tr><th><a href='listusers2.php?orderby=name'>Name</a></th><th>Title</th><th>Phone</th><th>Email</th><th><a href='listusers2.php?orderby=deptnum'>Dept</a></th><th><a href='listusers2.php?orderby=deptnam'>Department Name</a></th><th><a href='listusers2.php?orderby=applevel'>Approval Level</a></th><th>Manager</th><th><span class='oi oi-pencil'></span></th></tr>
<?php
foreach($users as $u) {
  $uid = $u->id;
  $un = $u->username;
  $ut = $u->title;
  $up = $u->phone;
  $ue = $u->email;
  $udnum = $u->deptnum;
  $udnam = $u->deptnam;
  $um = $u->manager;
  $ua = $u->applevel;
  echo "<tr><td><a href='dispuser.php?id=$uid'>$un</a></td><td>$ut</td><td>$up</td><td>$ue</td><td>$udnum</td><td>$udnam</td><td>$ua</td><td>$um</td><td><a href='dispuser.php?id=$uid'><span class='oi oi-pencil' title='Edit' aria-hidden='true'></span></a></td></tr>\n";
}
?>
</table>
</div>
<div class='space-46'></div>
<script>
<?php echo "var currentuser = '$dn';\n"; ?>
$("#newbtn").click(function() {
  location.href = "edreq.php";
});
$("#listbtn").click(function() {
  location.href = "listreqs.php";
});
</script>
</body>
