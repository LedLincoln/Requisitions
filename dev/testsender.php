<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '/usr/share/php/PHPMailer-master/src/Exception.php';
require '/usr/share/php/PHPMailer-master/src/PHPMailer.php';
require '/usr/share/php/PHPMailer-master/src/SMTP.php';
require_once 'dbsetup.php';
require_once 'functions.php';
$baseurl = "https://reqs.licor.com";
//for monitoring
$dn = "Mickey Mouse";
$admin = "Larry Dietrich";
$admin_email = "larry.dietrich@licor.com";
$mail = new PHPMailer();
$mail->isSMTP();
$mail->SMTPAutoTLS=false;
$mail->SMTPSecure=false;
$mail->isHTML(true);
$body = <<<MBODY
<!DOCTYPE html
<html>
<body>
<p>(Sent to $submitto)</p>
<p style="font-family: sans-serif;">$dn has submitted a <a href="$baseurl/dispreq.php?id=$reqid">
requisition</a> for your approval.</p>
</body>
</html>
MBODY;
$subject = "New requisition for Ivan Johnson";

$mail->Priority = 1;
$mail->addCustomHeader("X-MSMail-Priority: High");
$mail->addCustomHeader("Importance: High");
$recipients = getEmail("Ivan Johnson");
//the commented lines are not liked by Exchange
//$mail->SetFrom('requisitions@licor.com', 'Requisition System');
//$mail->SetFrom('licor.noreply@licor.com', 'Requisition System');
//$mail->SetFrom('bogus.user@licor.com', 'Requisition System');
//$mail->SetFrom('requisitions@licor.com');
//$mail->SetFrom('requisitions@licor.com', '"Awesome Requisition System"');
$mail->setFrom('requisitions@licor.com', 'Awesome Requisition System');
foreach($recipients as $name => $email) {
  $mail->addAddress($email, $name);
}
//fyi, addBCC needs to be after addAddress, or To: appears as Undisclosed recipients
$mail->addBCC($admin_email, $admin);
$mail->Subject = $subject;
$mail->Body = $body;
if(!$mail->Send()) {
  error_log("PHP mail error: " . $mail->ErrorInfo);
  echo "Email not sent!\n";
} else {
  echo "Email sent to Ivan!\n";
}
?>
