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
<?php echo "var reqid = 266;\n"; ?>
$("#itemsbtn").on('change', function() {
  var fname = $(this).val();
  if(fname !== '') {
    var lidata = $('#itemsbtn').prop('files')[0];
console.log(lidata);
    uploadli(lidata);
  }
});

function uploadli(lidata) {
  var fdata = new FormData();
  fdata.append('itemsfile', lidata);
  //this appears in $_POST, not $_FILES!
  fdata.append('reqid', reqid);
  $.ajax({
    url: "implines.php",
    dataType: 'text',
    cache: false,
    contentType: false,
    processData: false,
    data: fdata,
    type: 'POST',
    success: function(result) {
      var jresult = JSON.parse(result);
      alert('success');
      console.log(result);
      if('error' in jresult) {
        alert(jresult['error']);
      } else {
        //getitems(reqid);
        var result = jresult['itemsfile'];
        $('#results').html(result);
      }
    }
  });
}
</script>
</body>
</html>
