<?php
require_once 'dbsetup.php';
//require_once 'lib/rb.php';
//R::setup('mysql:host=localhost;dbname=reqs; charset=utf8',
//        'redbean','redbean1');
$users = R::findAll('requser', 'ORDER BY username');
?>
<!DOCTYPE html>
<html lang='en'><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link href="open-iconic/font/css/open-iconic-bootstrap.css" rel="stylesheet">
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
$icons = ["account-login","account-logout","action-redo","action-undo","align-center","align-left","align-right","aperture","arrow-bottom","arrow-circle-bottom","arrow-circle-left","arrow-circle-right","arrow-circle-top","arrow-left","arrow-right","arrow-thick-bottom","arrow-thick-left","arrow-thick-right","arrow-thick-top","arrow-top","audio","audio-spectrum","badge","ban","bar-chart","basket","battery-empty","battery-full","beaker","bell","bluetooth","bold","bolt","book","bookmark","box","briefcase","british-pound","browser","brush","bug","bullhorn","calculator","calendar","camera-slr","caret-bottom","caret-left","caret-right","caret-top","cart","chat","check","chevron-bottom","chevron-left","chevron-right","chevron-top","circle-check","circle-x","clipboard","clock","cloud","cloud-download","cloud-upload","cloudy","code","cog","collapse-down","collapse-left","collapse-right","collapse-up","command","comment-square","compass","contrast","copywriting","credit-card","crop","dashboard","data-transfer-download","data-transfer-upload","delete","dial","document","dollar","double-quote-sans-left","double-quote-sans-right","double-quote-serif-left","double-quote-serif-right","droplet","eject","elevator","ellipses","envelope-closed","envelope-open","euro","excerpt","expand-down","expand-left","expand-right","expand-up","external-link","eye","eyedropper","file","fire","flag","flash","folder","fork","fullscreen-enter","fullscreen-exit","globe","graph","grid-four-up","grid-three-up","grid-two-up","hard-drive","header","headphones","heart","home","image","inbox","infinity","info","italic","justify-center","justify-left","justify-right","key","laptop","layers","lightbulb","link-broken","link-intact","list","list-rich","location","lock-locked","lock-unlocked","loop","loop-circular","loop-square","magnifying-glass","map","map-marker","media-pause","media-play","media-record","media-skip-backward","media-skip-forward","media-step-backward","media-step-forward","media-stop","medical-cross","menu","microphone","minus","monitor","moon","move","musical-note","paperclip","pencil","people","person","phone","pie-chart","pin","play-circle","plus","power-standby","print","project","pulse","puzzle-piece","question-mark","rain","random","reload","resize-both","resize-height","resize-width","rss","rss-alt","script","share","share-boxed","shield","signal","signpost","sort-ascending","sort-descending","spreadsheet","star","sun","tablet","tag","tags","target","task","terminal","text","thumb-down","thumb-up","timer","transfer","trash","underline","vertical-align-bottom","vertical-align-center","vertical-align-top","video","volume-high","volume-low","volume-off","warning","wifi","wrench","x","yen","zoom-in","zoom-out"];
$icount = count($icons);
$ic = 0;
foreach($users as $u) {
  $uid = $u->id;
  $un = $u->username;
  $ut = $u->title;
  $up = $u->phone;
  $ue = $u->email;
  $icon = $icons[$ic];
  $ic++;
  if($ic >= $icount) {$ic = 0;}
  echo "<tr><td><a href='dispuser.php?id=$uid'>$un</a></td><td>$ut</td><td>$up</td><td>$ue</td><td><a href='dispuser.php?id=$uid'><span class='oi oi-$icon' title='$icon' aria-hidden='true'></span></a></td></tr>\n";
}
?>
</table>
</div>
</body>
