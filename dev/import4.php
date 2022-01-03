<!DOCTYPE html>
<html><head><title>Import</title>
<script
  src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous">
</script>
</head>
<body>
<input name='itemsbtn' id='itemsbtn' type='file' accept='.csv'>
<pre>
<div id='results'></div>
</pre>
<script>
<?php //echo "var reqid = $rid;\n"; ?>
<?php echo "var reqid = 51;\n"; ?>
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
    url: "implines4.php",
    dataType: 'text',
    cache: false,
    contentType: false,
    processData: false,
    data: fdata,
    type: 'POST',
    success: function(result) {
      //console.log(result);
      var jresult = JSON.parse(result);
      if('error' in jresult) {
        alert(jresult['error']);
      } else {
        //getitems(reqid);
        console.log(jresult['hello']);
        var result = jresult['itemsfile'];
        $('#results').html(result);
      }
    }
  });
}
</script>
</body>
</html>
