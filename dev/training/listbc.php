<?php
require_once 'auth.php';
//not currently needed
//require_once 'arrays.php';
$buyercodes = R::findAll('buyercode', 'ORDER BY rolenum');
?>
<!DOCTYPE html>
<html lang='en'><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link href="open-iconic/font/css/open-iconic-bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/simplelist.css">
<script
  src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous">
</script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js'></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<title>Buyer Codes</title>
</head>
<body>
<div class='container'>
<div class='listhead'>
<span class='bigpart'>Requisitions Buyer Codes</span>
<span class='smallpart text-center'>
<button type='button' class='btn btn-primary' id='newbcbtn'><span class='oi oi-key'></span> New Buyer Code</button>
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
<span class='dropdown'>
<?php echo "<button class='btn btn-primary dropdown-toggle' data-toggle='dropdown' type='button'><span class='oi oi-person'></span> $dn</button>\n"; ?>
<div class='dropdown-menu'>
<div class='dropdown-item point' id='logoff'>Log Off</div>
</div>
</span>
</span>
</div>
<div class='space-56'></div>
<table class='table table-striped table-sm'>
<tr><th>Name</th><th>Description</th><th><span class='oi oi-pencil' title='Edit' aria-hidden='true'></span></th><th><span class='oi oi-trash' title='Delete' aria-hidden='true'></span></th></tr>
<?php
foreach($buyercodes as $bc) {
  $bcid = $bc->id;
  $bcnum = $bc->rolenum;
  $bcname = $bc->rolename;
  echo "<tr><td><span class='edicon point' data-id='$bcid'>$bcnum</a></td><td>$bcname</td><td><span class='edicon point' data-id='$bcid'><span class='oi oi-pencil' title='Edit' aria-hidden='true'></span></span></td><td><span class='delicon point' data-id='$bcid'><span class='oi oi-trash'></span></span></td></tr>\n";
}
?>
</table>
</div>
<div id='edbc' class='modal fade' role='dialog'>
<div class='modal-dialog' role='document'>
<div class='modal-content'>
<div class='modal-header'>
<h4 class='modal-title' id='confirmhead'>Edit Buyer Code</h4>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
<div class='modal-body'>
<form id='bcform'>
<input type='hidden' name='bcid' id='bcid'>
<div class='labl'>Code Number</div>
<input name='codenumber' id='codenumber'>
<div class='labl'>Code Name</div>
<input class='widething' name='codename' id='codename'>
</form>
</div>
<div class='modal-footer'>
<button class='btn btn-primary' id='savebc'>Save</button>
<button type='button' class='btn btn-default' data-dismiss='modal'>Cancel</button>
</div> <!-- modal-footer -->
</div> <!-- modal-content -->
</div> <!-- modal-dialog -->
</div> <!-- edbc -->
<div id='confirmdlg' class='modal fade' role='dialog'>
<div class='modal-dialog' role='document'>
<div class='modal-content'>
<div class='modal-header'>
<h4 class='modal-title' id='confirmhead'>Delete Buyer Code</h4>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
<div class='modal-body'>
<p id='confirmprompt'>Are you sure you want to delete this buyer code?</p>
</div>
<div class='modal-footer'>
<button class='btn btn-primary' id='confdel'>Yes</button>
<button type='button' class='btn btn-default' data-dismiss='modal'>Cancel</button>
</div> <!-- modal-footer -->
</div> <!-- modal-content -->
</div> <!-- modal-dialog -->
</div> <!-- confdel -->
<script>
<?php echo "var currentuser = '$dn';\n"; ?>
$("#newbcbtn").on('click', function() {
  $("#bcid").val('');
  $("#codenumber").val('');
  $("#codename").val('');
  $("#edbc").modal('show');
});
$(".edicon").on('click', function() {
  var bcid = $(this).attr('data-id');
  var url = "getbc.php?bcid=" + bcid;
  $.ajax({
    url: url,
    success: function(bcdata) {
      jdata = JSON.parse(bcdata);
      $("#codenumber").val(jdata['rolenum']);
      $("#codename").val(jdata['rolename']);
      $("#savebc").attr('data-id', bcid);
      $("#edbc").modal('show');
    }
  });
});
$(".delicon").on('click', function() {
  var bcid = $(this).attr('data-id');
  var bcnum = $(this).parent().prev().prev().prev().find('span').html();
  var bcname = $(this).parent().prev().prev().html();
  $("#confirmprompt").html("<strong>" + bcnum + " " + bcname + "</strong><br>Are you sure you want to delete this code?");
  $("#confdel").prop("data-id", bcid);
  $("#confirmdlg").modal('show');
});
$("#confdel").on('click', function() {
  var bcid = $(this).prop('data-id');
  var url = "delbc.php";
  var pdata = {'bcid': bcid};
  $.ajax({
    url: url,
    data: pdata,
    type: "POST",
    success: function(result) {
      console.log(result);
      location.reload(true);
    }
  });
  $("#confirmdlg").modal('hide');
});
$("#savebc").on('click', function() {
  var bcid = $(this).attr('data-id');
  $("#bcid").val(bcid);
  var bcdata = $("#bcform").serialize();
  $("#edbc").modal('hide');
  $.ajax({
    url: "procbc.php",
    data: bcdata,
    type: "POST",
    success: function(result) {
      console.log(result);
      location.reload(true);
    }
  });
});

$("#newbtn").click(function() {
  location.href = "edreq.php";
});
$("#listbtn").click(function() {
  location.href = "listreqs.php";
});
$("#logoff").click(function() {
  $("#confirmhead").html("Log Off");
  $("#confirmprompt").html("You are logged in as " + currentuser + ". Do you wish to log off?");
  $("#confirmbtn").prop("data-action", "logoff");
  $("#confirmdlg").modal('show');
});
$("#confirmbtn").click(function() {
  $("#confirmdlg").modal('hide');
  var confirmaction = $("#confirmbtn").prop("data-action");
  switch(confirmaction) {
    case 'logoff':
      location = "login.php";
      break;
    default:
      console.log("Confirm button clicked without a valid action.");
  }
});

</script>
</body>
