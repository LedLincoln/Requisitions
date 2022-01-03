<!DOCTYPE html>
<html lang='en'><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" href="css/custom.css">
<link rel="stylesheet" href="css/jqui.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<style>
#vendornum {
  width: 11em;
}
#vendorname {
  width: 20em;
}
</style>
<script
  src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous">
</script>
<script src = "https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<title>Requisition 100008</title>
</head>
<body>
<div class='container reqsec'>
<div class='row'>
<div class='col-sm text-danger'>
<i class='material-icons md-36'>coronavirus</i><span class='horspace'></span>
<i class='material-icons md-36'>masks</i>
</div>
<div class='col-sm'>
<h4 id='reqhead' class='text-center'><em>LI-COR Biosciences&reg;</em></h4>
</div>
<div class='col-sm text-right text-danger'>
<i class='material-icons md-36'>6_ft_apart</i><span class='horspace'></span>
<i class='material-icons md-36'>clean_hands</i>
</div>
</div>
<h3 class='text-center'>Purchase Order/Requisition</h3>
</div>
<div class='container reqsec'>
<div class='row'>
<div class='col-sm-4'>
Vendor: <input id='vendornum' class='compact' placeholder='Type name or number'>
</div>
<div class='col-sm-4'>
Name: <input id='vendorname' class='compact' readonly>
</div>
<div class='col-sm-4'>
Phone: <input id='vendorphone' class='compact'>
</div>
</div>


<script>
$(function() {
  $("#vendornum").autocomplete({
    source: "api/getvend-w-phone.php",
    minLength: 3,
    delay: 360,
    position: {my: "center top", at: "center bottom"},
    select: function(event, ui) {
      $("#vendorname").val(ui.item.vendorname);
      $("#vendorphone").val(ui.item.phone);
    }
  });
});
// to show number & name, but select number only
//    source: "api/getvend-lab-val.php",
// another possibility:   position: {my: "center top", at: "right bottom", of: "#reqhead"}
//    select: function(event, ui) {alert($(this).val())}
//    select: function(event, ui) {alert(ui.item.value)}
</script>
</body>
</html>
