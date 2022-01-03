<?php
require_once 'auth.php';
$greqid = $_GET['id'] ?? '';
if($greqid == '') {
  die("Invalid req number.");
}
$reqbean = R::load('req', $greqid);
$reqid = $reqbean->id;
if($reqid == 0) {
  die("Req not found.");
}
$rdesc = $reqbean->description;
?>
<!DOCTYPE html>
<html><head><title>Import</title>
<script
  src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous">
</script>
</head>
<body>
<?php
echo "<h4>Import line items to requisition R-$reqid $rdesc</h4>\n";
echo "<p><a href='edreq.php?id=$reqid'>Cancel</a></p>\n";
?>
<pre>
<p>CSV file must conform to this example layout. Row 1 is disregarded.</p>
<table border='1'>
<tr><th>Quantity</th><th>Unit</th><th>Price</th><th>Vendor p/n</th><th>LI-COR p/n</th><th>Description</th><th>Comment</th></tr>
<tr><td>25</td><td>EA</td><td>1.11</td><td>S-521</td><td></td><td>14 1⁄2 x 8 3⁄4 x 12" Multi-Depth Corrugated Boxes</td><td>no substitutions</td></tr>
<tr><td>6</td><td>ROLL</td><td>15.00</td><td>S-852</td><td>215-29381</td><td>International Safe Handling Labels - "Fragile" with Broken Glass, 3 x 4"</td><td></td></tr>
</table>
<p><input name='itemsbtn' id='itemsbtn' type='file' accept='.csv'></p>
<div id='results'></div>
</pre>
<div id='linkback'></div>
<?php
require_once 'dbsetup.php';
//$units = R::findAll('unit', 'ORDER BY unitname');
$units = R::getCol('SELECT code FROM unit ORDER BY unitname');
//print_r($units);
?>
<script>
<?php echo "var reqid = $reqid;\n"; ?>
$("#itemsbtn").on('change', function() {
  var fname = $(this).val();
  if(fname !== '') {
    var lifile = $('#itemsbtn').prop('files')[0];
    console.log(lifile);
    uploadli(lifile);
  }
});

function uploadli(lifile) {
  var fdata = new FormData();
  fdata.append('itemsfile', lifile);
  //this appears in $_POST, not $_FILES!
  fdata.append('reqid', reqid);
  $.ajax({
    url: "implines7.php",
    dataType: 'text',
    cache: false,
    contentType: false,
    processData: false,
    data: fdata,
    type: 'POST',
    success: function(result) {
      var jresult = JSON.parse(result);
      if('error' in jresult) {
        alert(jresult['error']);
      } else {
        console.log(jresult['title']);
        var reqid = jresult['reqid'];
        console.log("Req ID: " + reqid);
        var result = jresult['itemsfile'];
        $('#results').html(result);
        $('#linkback').html("<h4>Items Imported</h4><a href='edreq.php?id=" + reqid + "'>Back to Requisition</a>");
      }
    }
  });
}
</script>
</body>
</html>
