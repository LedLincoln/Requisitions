<?php
require_once 'auth.php';
require_once 'functions.php';
require_once 'arrays.php';
$sp['createdby'] = R::getCol("SELECT DISTINCT createdby FROM req WHERE createdby <> '' ORDER BY createdby");
$sp['onbehalf'] = R::getCol("SELECT DISTINCT onbehalf FROM req WHERE onbehalf <> '' ORDER BY onbehalf");
$sp['approvedby'] = R::getCol("SELECT DISTINCT approvedby FROM req WHERE approvedby <> '' ORDER BY approvedby");
$sp['assignedto'] = R::getCol("SELECT DISTINCT assignedto FROM req WHERE assignedto <> '' ORDER BY assignedto");
$projopts = R::getCol("SELECT DISTINCT project FROM req ORDER BY project");
$views = ["","1. New - Not Submitted","2. New - Submitted","3. Waiting for Assignment","4. Assigned","5. Quotations - Open","6. Quotations - Complete","7. Ordered","8. Closed","9. All Reqs","10. My Reqs"];
$title = "Search Requisitions";
?>
<!DOCTYPE html>
<html lang='en'><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="shortcut icon" href="reqs-favicon.png" type="image/x-icon">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" href="css/sidebar.css">
<link rel="stylesheet" href="css/jqui.css">
<!-- <link rel='stylesheet' type='text/css' href="css/datatables.min.css" /> -->
<link href="open-iconic/font/css/open-iconic-bootstrap.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<script
  src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous">
</script>
<script src = "https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js'></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<?php echo "<title>$title</title>\n"; ?>
</head>
<body>
<div class='wrapper'>
<div class='space-40'></div>
<nav id='sidebar'>
<div class='sidebar-header'>
<h4>Requisitions</h4>
</div>
<ul class='list-unstyled components'>
<?php
for($i = 1; $i < count($views); $i++) {
  echo "<li><a href='listreqs.php?view=$i'>$views[$i]</a></li>";
}
?>
<li class='current'><a>11. Search</a></li>
</ul>
</nav>
<div id='content'>
<div class='listhead'>
<span class='bigpart'>
<?php echo "Search Requisitions\n"; ?>
</span>
<span class='smallpart text-center'>
<span class='dropdown'>
<button type='button' class='btn btn-primary'><a href='edreq.php'><span class='oi oi-plus'></span> New Req</a></button> 
<button type='button' class='btn btn-primary'><a href='edreq.php?type=rfq'><span class='oi oi-question-mark'></span> New Quote</a></button> 
<?php
if(in_array("Admin", $userroles)) {
  echo "<button class='btn btn-primary dropdown-toggle' data-toggle='dropdown' type='button'><span class='oi oi-cog'></span> Admin</button>\n";
  echo "<div class='dropdown-menu'>\n";
  echo "<a class='dropdown-item' href='listbc.php'>Buyer Codes</a>\n";
  echo "<a class='dropdown-item' href='listusers.php'>Users</a>\n";
  echo "<!-- <a class='dropdown-item' href='#'>System Settings</a> -->\n";
  echo "</div>\n";
}
?>
</span>
<span class='dropdown'>
<?php echo "<button class='btn btn-primary dropdown-toggle' data-toggle='dropdown' type='button'><span class='oi oi-person'></span> $dn</button>\n"; ?>
<div class='dropdown-menu'>
<!-- <a class='dropdown-item' href='#'>Preferences</a> -->
<div class='dropdown-item point' id='logoff'>Log Off</div>
</div>
</span>
</span>
</div>
<div class='space-46'>&nbsp;</div>
<!-- <form method='POST' action='disppost.php'> -->
<form method='POST' id='searchform'>
<div class='reqsec reqsec-general'>
<div class='row'>
<div class='col-sm-6'>
<div class='labl'>Search by date:</div>
<select class='compact' name='selectdate'>
<option value='created'>Created</option>
<option value='approvaldate'>Approved</option>
<option value='orderdate'>Ordered</option>
</select> <span class='labl'>is between</span>
<input class='compact' type='date' name='startdate'> <span class='labl'>and</span> <input class='compact' type='date' name='enddate'>
</div>
<div class='col-sm-4'><div class='labl'>Search by person</div>
<select class='compact' name='personopt' id='personopt'>
<option value=''>-any-</option>
<option value='createdby'>Created by</option>
<option value='onbehalf'>On behalf of</option>
<option value='approvedby'>Approved by</option>
<option value='assignedto'>Assigned to</option>
</select>
<select class='compact' name='findperson'>
<option value=''>-any-</option>
</select>
</div>
<div class='col-sm-2'><div class='labl'>Search by project</div>
<select class='compact' name='findproject'>
<option value=''>-any-</option>
</select>
</div>
</div>

<div class='row'>
<div class='col'><div class='labl'>Containing text</div><input type='text' class='compact' name='searchtext'></div>
</div>
<div class='spacer'>&nbsp;</div>
<div class='row'>
<div class='col'><button class='btn btn-primary btn-sm' type='button' id='searchbtn'>Search</button></div>
</div>
</form>
</div>
<div class='reqsec reqsec-general'>
<div class='labl'>Results</div>
<div id='results'></div>
</div>
</div>
</div>
</div>
<div id='confirmdlg' class='modal fade' role='dialog'>
<div class='modal-dialog' role='document'>
<div class='modal-content'>
<div class='modal-header'>
<h4 class='modal-title' id='confirmhead'>Delete Line</h4>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
<div class='modal-body'>
<p id='confirmprompt'>Are you sure you want to delete this line item?</p>
</div>
<div class='modal-footer'>
<button class='btn btn-primary' id='confirmbtn'>Yes</button>
<button type='button' class='btn btn-default' data-dismiss='modal'>Cancel</button>
</div> <!-- modal-footer -->
</div> <!-- modal-content -->
</div> <!-- modal-dialog -->
</div> <!-- confdel -->
<script>
<?php
echo "var currentuser = '$dn';\n";
echo "var sp = " . json_encode($sp) . ";\n";
echo "var projopts = " . json_encode($projopts) . ";\n";
?>
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
$('#searchform').keydown(function(k) {
  if(k.keyCode == 13) {
    k.preventDefault();
    $('#searchbtn').trigger('click');
    return false;
  }
});
$('#personopt').change(function() {
  var po = $('option:selected',this).val();
  if(po != '') {
    var curopts = sp[po];
  } else {
    var curopts = '';
  }
  var opts = "<option value=''>-any-</option>";
  for(var i = 0; i < curopts.length; i++) {
    var nm = curopts[i];
    opts += "<option value='" + nm + "'>" + nm + "</option>";
  }
  $("select[name='findperson']").empty().append(opts);
});

var opts = "<option value=''>-any-</option>";
for(var i = 0; i < projopts.length; i++) {
  var nm = projopts[i];
  if(nm != '') {
    opts += "<option value='" + nm + "'>" + nm + "</option>";
  }
}

$("select[name='findproject']").empty().append(opts);
$("#searchbtn").click(function() {
  var qry = $("#searchform").serialize();
  //console.log(qry);
  $.ajax({
    url: "procsearch.php",
    data: qry,
    type: "POST",
    success: function(result) {
      $("#results").html(result);
    }
  });
});
</script>
</body>
</html>
