<?php
require_once 'dbsetup.php';
$users = R::findAll('requser', 'ORDER BY username');
?>
<!DOCTYPE html>
<html lang='en'><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="css/custom.css">
<script
  src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous">
</script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<title>Requisitions Users</title>
</head>
<body>
<div class='container'>
<table class='table table-striped table-sm'>
<tr><th>Name</th><th>Title</th><th>Phone</th><th>Email</th><th>?&nbsp;</th></tr>
<?php
foreach($users as $u) {
  $uid = $u->id;
  $un = $u->name;
  $ut = $u->title;
  $up = $u->phone;
  $ue = $u->email;
  echo "<tr><td><a href='dispuser.php?id=$uid'>$un</a></td><td>$ut</td><td>$up</td><td>$ue</td><td><a href='dispuser.php?id=$uid'><i class='material-icons'>edit</i></a></td></tr>\n";
  //echo "<tr><td><a href='dispuser.php?id=$uid'>$un</a></td><td>$ut</td><td>$up</td><td>$ue</td><td><a href='dispuser.php?id=$uid'><i class='material-icons'>biotech</i></a></td></tr>\n";
}
?>
</table>
<div class='text-center text-danger'>
<i class='material-icons bigpic'>coronavirus</i><span class='horspace'></span>
<i class='material-icons bigpic'>masks</i><span class='horspace'></span>
<i class='material-icons bigpic'>6_ft_apart</i><span class='horspace'></span>
<i class='material-icons bigpic'>clean_hands</i>
</div>
</div>
</body>
