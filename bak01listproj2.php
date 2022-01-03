<?php
require_once 'auth.php';
require_once 'functions.php';
require_once 'arrays.php';
/*
$coldef = array(
["visible" => false, "targets" => 0],
["width" => "10%", "targets" => 2],
["width" => "15%", "targets" => [3, 4, 6, 8]],
["width" => "20%", "targets" => 5],
["width" => "30%", "targets" => -2],
["orderable" => false, "targets" => -2],
);
$ordef = [1, "desc"];
*/
$fy = $_GET['fy'] ?? '';
//echo "FY: $fy<br>";
switch($fy) {
  case 1:
    $fyn = 1;
    break;
  case 2:
    $fyn = 2;
    break;
  case 3:
    $fyn = 3;
    break;
  case 4:
    $fyn = 4;
    break;
  case 5:
    $fyn = 5;
    break;
  case 6:
    $fyn = 6;
    break;
  case 7:
    $fyn = 7;
    break;
  case 8:
    $fyn = 8;
    break;
  case 9:
    $fyn = 9;
    break;
  case 10:
    $fyn = 10;
    break;
  case 11:
    $fyn = 11;
    break;
  case 12:
    $fyn = 12;
    break;
  default:
    $fyn = 0;
}
if($fyn == 0) {
  $fyqry = "";
} else {
  $fyqry = "AND MONTH(created) = $fyn";
}
$qry = "SELECT r.project, p.projname, COUNT(*) AS count, SUM(r.total) AS total FROM req r JOIN project p ON r.project = p.projnum WHERE r.project != '' AND r.project != '10000' AND r.status != 'Canceled' $fyqry GROUP BY r.project";
//echo $qry;
$projs = R::getAll($qry);
$view = '11';
$views = ["","1. New - Not Submitted","2. New - Submitted","3. Waiting for Assignment","4. Assigned","5. Quotations - Open","6. Quotations - Complete","7. Canceled","8. Closed","9. All Reqs","10. My Reqs"];
$title = $views[$view];
?>
<!DOCTYPE html>
<html lang='en'><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="shortcut icon" href="reqs-favicon.png" type="image/x-icon">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" href="css/sidebar.css">
<link rel="stylesheet" href="css/jqui.css">
<link rel="stylesheet" href="css/proj.css">
<!-- <link rel='stylesheet' type='text/css' href="css/datatables.min.css" /> -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.22/datatables.min.css"/>
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
  if($i == $view) {
    $c = "class='current'";
  } else {
    $c = "";
  }
  echo "<li $c><a href='listreqs.php?view=$i'>$views[$i]</a></li>";
}
?>
<li><a href='listdept.php'>11. by Department</a></li>
<li><a href='listproj.php'>12. by Project</a></li>
<li><a href='search.php'>13. Search</a></li>
</ul>
</nav>
<div id='content'>
<div class='listhead'>
<span class='bigpart'>
<?php echo "$views[$view]\n"; ?>
</span>
<div class='lably'>Fiscal year: </div><select id='fy'>
<option value=''>All</option>
<option value='01'>January</option>
<option value='02'>February</option>
<option value='03'>March</option>
<option value='04'>April</option>
<option value='05'>May</option>
</select>
<span class='smallpart text-center'>
<span class='dropdown'>
<input type='text' id='dtfilter'>
<a type='button' class='btn btn-primary' href='edreq.php'><span class='oi oi-plus'></span> New Req</a> 
<a type='button' class='btn btn-primary' href='edreq.php?type=rfq'><span class='oi oi-question-mark'></span> New Quote</a> 
<?php
if(in_array("Admin", $userroles)) {
  echo "<button class='btn btn-primary dropdown-toggle' data-toggle='dropdown' type='button'><span class='oi oi-cog'></span> Admin</button>\n";
  echo "<div class='dropdown-menu'>\n";
  echo "<a class='dropdown-item' href='listbc.php'>Buyer Codes</a>\n";
  echo "<a class='dropdown-item' href='listusers.php'>Users</a>\n";
  echo "<a class='dropdown-item point' id='rptbtn'>Reports</a>\n";
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
<div class='space-46'></div>
<div id='projaccordion'>
<?php
foreach($projs as $p) {
  $proj = $p['project'];
  $pname = $p['projname'];
  $count = $p['count'];
  $total = $p['total'];
  echo "<h3 class='projrow' data-id='$proj'>$proj $pname<span class='money'>$total</span></h3>\n";
  echo "<div><p>Loading...please wait</p></div>\n";
}
?>
</div>
<div id='details'></div>
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
<?php include_once 'inc_report'; ?>
<!-- <script src="js/datatables.min.js"></script> -->
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.22/datatables.min.js"></script>
<script>
//don't sort the last column, fix widths of certain columns for best row height
<?php
//echo "var coldef = " . json_encode($coldef) . ";\n";
//echo "var ordef = " . json_encode($ordef) . ";\n";
echo "var currentuser = '$dn';\n";
echo "var fiscalyear = '$fy';\n";
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

$("#rptbtn").on('click', function() {
  $("#reportdates").modal('show');
});

$("#runbtn").on('click', function(e) {
  e.preventDefault();
  if($("#reportform")[0].checkValidity()) {
    $("#reportform").submit();
  } else {
    alert("Dates are not valid.");
  }
});

var already_loaded = {};
$("#projaccordion").accordion({
  collapsible: 'true',
  active: 'none',
  activate: function (e, ui) {
    //console.log(ui.newHeader);
    // only fire when the accordion is opening..
    if(ui.newHeader.length > 0) {
      // only retrieve the remote content once..
      projnum = ui.newHeader.attr('data-id');
      if(! already_loaded[ui.newHeader[0].id] == 1) {
        console.log("Loading " + projnum + ".");
        var url = "getprojreqs.php";
        var pdata = {'projnum': projnum};
        $.post(url, pdata, function (data) {
          var jdata = JSON.parse(data);
console.log(jdata);
          var projdata = "<ul class='projlist'>";
          $.each(jdata['data'], function(k, v) {
            //projdata += "<li><a href='dispreq.php?id=" + v['id'] + "' target='_blank'>R" + v['id'] + "</a> " + v['onbehalf'] + " " + v['description'] + " " + v['total'] + "</li>";
            projdata += "<li><a href='dispreq.php?id=" + v['id'] + "' target='_blank'>R" + v['id'] + "</a> " + v['total'] + " " + v['onbehalf'] + " " + v['description'] + "</li>";
          });
          projdata += "</ul>";
          $(ui.newHeader[0]).next().html(projdata);
          var contentDiv = $(ui.newHeader[0]).next()[0];
          $('#'+contentDiv.id).height(contentDiv.scrollHeight);
          already_loaded[ui.newHeader[0].id]=1;
        });
      } else {
        console.log(projnum + " is already loaded.");
      }
    }
  }
});
$("#fy").on('change', function() {
  console.log($(this).val());
});
</script>
</body>
</html>
