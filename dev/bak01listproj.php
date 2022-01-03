<?php
require_once 'auth.php';
require_once 'functions.php';
require_once 'arrays.php';
$coldef = array(
["visible" => false, "targets" => 0],
["width" => "10%", "targets" => 2],
["width" => "15%", "targets" => [3, 4, 6, 8]],
["width" => "20%", "targets" => 5],
["width" => "30%", "targets" => -2],
["orderable" => false, "targets" => -2],
);
$ordef = [1, "desc"];
//adjust query for view in switch below
//also, restricts allowable GET variablea
//$qry = "SELECT project, COUNT(*) AS count, SUM(total) AS total FROM req WHERE project != '' AND project != '10000' AND req.status != 'Canceled' GROUP BY project";
$qry = "SELECT r.project, p.projname, COUNT(*) AS count, SUM(r.total) AS total FROM req r JOIN project p ON r.project = p.projnum WHERE r.project != '' AND r.project != '10000' AND r.status != 'Canceled' GROUP BY r.project";
//$qry = "SELECT project, COUNT(*), SUM(total) FROM req GROUP BY project";
$projs = R::getAll($qry);
//$reqs = R::find('req', $qry);
$view = '11';
$views = ["","1. New - Not Submitted","2. New - Submitted","3. Waiting for Assignment","4. Assigned","5. Quotations - Open","6. Quotations - Complete","7. Canceled","8. Closed","9. All Reqs","10. My Reqs", "11. Projects"];
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
<li><a href='search.php'>12. Search</a></li>
</ul>
</nav>
<div id='content'>
<div class='listhead'>
<span class='bigpart'>
<?php echo "$views[$view]\n"; ?>
</span>
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
<table>
<thead><tr><th>Project</th><th>&nbsp;</th><th># of Reqs</th><th>Total</th></tr></thead>
<tbody>
<?php
//print_r($projs);
foreach($projs as $p) {
  $proj = $p['project'];
  $pname = $p['projname'];
  $count = $p['count'];
  $total = $p['total'];
  //echo "<tr><td>$proj</td><td>$count</td><td>$total</td><td>$rby</td><td>$obo</td><td>$rstat</td><td>$asgnto</td><td>$rvnum</td><td>$rvnam</td><td>$avnum</td><td>$avnam</td><td>$rdesc</td><td class='text-right'>$total</td></tr>\n";
  echo "<tr><td><span class='projrow' data-id='$proj'>&gt;$proj</span></td><td>$pname</td><td>$count</td><td>$total</td></tr>\n";
}
?>
</tbody>
</table>
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
echo "var coldef = " . json_encode($coldef) . ";\n";
echo "var ordef = " . json_encode($ordef) . ";\n";
echo "var currentuser = '$dn';\n";
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

var table = $('#reqtable').DataTable( {
  "dom": 'ti',
  "columnDefs": coldef,
  "order": ordef,
  "createdRow": function(row, data, index) {
    if(data[0] == '!') {
      $(row).addClass('hotrow');
    }
  },
});
$("#dtfilter").on("keyup search input paste cut", function() {
   table.search(this.value).draw();
});
$('#reqtable tbody').on('click', 'tr', function() {
  var reqno = table.row(this).data()[1];
  location = 'dispreq.php?id=' + reqno;
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
$(".projrow").on('click', function() {
  $("#details").html('');
  projnum = $(this).attr('data-id');
  console.log(projnum);
  var url = "getprojreqs.php";
  var pdata = {'projnum': projnum};
  $.ajax({
    url: url,
    data: pdata,
    type: "POST",
    success: function(result) {
      console.log(result);
      jresult = JSON.parse(result);
      //$("#details").html(result);
      $.each(jresult, function(k, v) {
        $("#details").append(v['id'] + " " + v['onbehalf'] + " " + v['description'] + "<br>");
      });
    }
  });
});
</script>
</body>
</html>
