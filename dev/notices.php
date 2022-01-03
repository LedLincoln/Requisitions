<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '/usr/share/php/PHPMailer-master/src/Exception.php';
require '/usr/share/php/PHPMailer-master/src/PHPMailer.php';
require '/usr/share/php/PHPMailer-master/src/SMTP.php';
$admins = getRoleMembers('Admin');
$adminstr = implode(', ', $admins);
function sendNotice($ntype, $proto) {
//pretend indent
global $dn;
global $obo;
//global $obomgr;
global $reqid;
global $submitto;
global $admins;
global $adminstr;
$baseurl = "https://reqs.licor.com/dev";
//for monitoring
$admin = "Larry Dietrich";
$admin_email = "larry.dietrich@licor.com";
$protoline = '';
if($proto == 'true') {
  $protoline = "<p style='font-family: sans-serif;'>Requisition is for prototype parts.</p>";
}
$mail = new PHPMailer();
$mail->isSMTP();
$mail->SMTPAutoTLS=false;
$mail->SMTPSecure=false;
$mail->isHTML(true);
$subject2 = "";
switch($ntype) {
  case "submit":
    //if $submitto != $obomgr send 3rd notice?
    $recipients = getEmail("Larry Dietrich");
    //$recipients = getEmail($submitto);
    $body = <<<MBODY
<!DOCTYPE html
<html>
<body>
<!-- <p>(Sent to $submitto)</p> -->
<p style="font-family: sans-serif;">$dn has submitted a <a href="$baseurl/dispreq.php?id=$reqid">
requisition</a> for your approval.</p>
$protoline
</body>
</html>
MBODY;
    $subject = "New requisition for $obo";

    if($dn != $obo) {
      $recipients2 = array("Larry Dietrich"=>"larry.dietrich@licor.com");
      //$recipients2 = getEmail($obo);
      $body2 = <<<MBODY2
<!DOCTYPE html
<html>
<body>
<!-- <p>(Sent to $obo)</p> -->
<p style="font-family: sans-serif;">$dn has submitted a <a href="$baseurl/dispreq.php?id=$reqid">
requisition</a> on your behalf.</p>
$protoline
</body>
</html>
MBODY2;
      $subject2 = "Requisition submitted on your behalf";
    }
    break;

  case "submittrue":
    $mail->Priority = 1;
    $mail->AddCustomHeader("X-MSMail-Priority: High");
    $mail->AddCustomHeader("Importance: High");
    //if $submitto != $obomgr send 2nd notice?
    $recipients = getEmail("Larry Dietrich");
    //$recipients = getEmail($submitto);
    $body = <<<MBODY
<!DOCTYPE html
<html>
<body>
<!-- <p>(Submitted to $submitto)</p> -->
<p style="font-family: sans-serif;">$dn has submitted a <strong><a href="$baseurl/dispreq.php?id=$reqid">
HOT requisition</a></strong> for your approval.</p>
$protoline
</body>
</html>
MBODY;
    $subject = "HOT requisition for $obo";

    if($dn != $obo) {
      $recipients2 = array("Larry Dietrich"=>"larry.dietrich@licor.com");
      //$recipients2 = getEmail($obo);
      $body2 = <<<MBODY2
<!DOCTYPE html
<html>
<body>
<!-- <p>(Sent to $obo)</p> -->
<p style="font-family: sans-serif;">$dn has submitted a <a href="$baseurl/dispreq.php?id=$reqid">HOT 
requisition</a> on your behalf.</p>
$protoline
</body>
</html>
MBODY2;
      $subject2 = "HOT requisition submitted on your behalf";
    }
    break;

  case "rtnquote":
  case "rtnquotetrue":
    $recipients = array("Larry Dietrich"=>"larry.dietrich@licor.com");
    ////$recipients = [getEmail($createdby), getEmail($obo)];
    //array_push($recipients, getEmail($obo));
    //$recipients = getEmail($createdby);
    //$recipients += getEmail($obo);
    $body = <<<MBODY
<!DOCTYPE html
<html>
<body>
<!-- <p>(Sent to $obo)</p> -->
<p style="font-family: sans-serif;">$dn has returned your <a href="$baseurl/dispreq.php?id=$reqid">request for quotation</a>. You may submit it for approval if you wish to continue, or disregard if you do not wish to purchase..</p>
</body>
</html>
MBODY;
    $subject = "Quotation has been completed for you";
    break;

  case "xquotetrue - not used":
    //send to assigner
    $recipients = array("Larry Dietrich"=>"larry.dietrich@licor.com");
    $mail->Priority = 1;
    $mail->addCustomHeader("X-MSMail-Priority: High");
    $mail->addCustomHeader("Importance: High");
    $subject = "Hot Request for Quotation";
    $body = <<<MBODY
<!DOCTYPE html
<html>
<body>
<!-- <p>(Sent to $adminstr)</p> -->
<p style="font-family: sans-serif;">$dn has submitted a <strong><a href="$baseurl/dispreq.php?id=$reqid">HOT request for quotation</a></strong>.
Please assign to buyer promptly.</p>
$protoline
</body>
</html>
MBODY;
    break;

  case "xapprovetrue - not used":
    //send to assigner
    $recipients = array("Larry Dietrich"=>"larry.dietrich@licor.com");
    $mail->Priority = 1;
    $mail->AddCustomHeader("X-MSMail-Priority: High");
    $mail->AddCustomHeader("Importance: High");
    $subject = "Hot Requisition";
    $body = <<<MBODY
<!DOCTYPE html
<html>
<body>
<!-- <p>(Sent to $adminstr)</p> -->
<p style="font-family: sans-serif;">$dn has approved a <strong><a href="$baseurl/dispreq.php?id=$reqid">HOT requisition</a></strong> for $obo.
Please assign to buyer promptly.</p>
$protoline
</body>
</html>
MBODY;
    break;

  case "quote":
  case "quotetrue":
  case "save":
  case "savetrue":
  case "approve":
  case "approvetrue":
  case "close":
  case "closetrue":
    //notice not needed, do nothing
    break;

  default:
    $recipients = array($admin=>$admin_email);
    $body = <<<MBODY
<!DOCTYPE html
<html>
<body>
<p style="font-family: sans-serif;"><a href='$baseurl/dispreq.php?id=$reqid'>Requisition R$reqid</a> has called 
for a notification without specifying a valid reason for the notification. It specified '$ntype'. 
You may need to troubleshoot. ($admin_email).</p>
</body>
</html>
MBODY;
    $subject = "Requisition System Error";
  }

//$mail->SetFrom('infradmin@licor.com', 'Requisition System');
$mail->SetFrom('requisitions@licor.com');
foreach($recipients as $name => $email) {
  $mail->addAddress($email, $name);
}
//fyi, addBCC needs to be after addAddress, or To: appears as Undisclosed recipients
$mail->addBCC($admin_email, $admin);
$mail->Subject = $subject;
$mail->Body = $body;
if(!$mail->Send()) {
  error_log("PHP mail error: " . $mail->ErrorInfo);
}
if($subject2 != "") {
  $mail2 = new PHPMailer();
  $mail2->isSMTP();
  $mail2->SMTPAutoTLS=false;
  $mail2->SMTPSecure=false;
  $mail2->isHTML(true);
  //$mail2->SetFrom('infradmin@licor.com', 'Requisition System');
  $mail2->setFrom('requisitions@licor.com');
  $mail2->Body = $body2;
  $mail2->Subject = $subject2;
  foreach($recipients2 as $name => $email) {
    $mail2->addAddress($email, $name);
  }
  $mail2->addBCC($admin_email, $admin);
  if(!$mail2->Send()) {
    error_log("PHP mail error: " . $mail2->ErrorInfo);
  }
}
}
?>
