<?php
// process.php — Receives POST, validates, saves to DB, sends email
// Requirements: PHP 8+, PDO, PHPMailer

require __DIR__.'/config.php';

// Basic POST presence check
$required = ['name','organisation','email','learnerName','learnerID','issueDate'];
foreach ($required as $key) {
  if (!isset($_POST[$key]) || trim($_POST[$key]) === '') {
    http_response_code(422);
    exit('Missing field: '.$key);
  }
}

// Sanitize & validate
$name          = trim($_POST['name']);
$organisation  = trim($_POST['organisation']);
$email         = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
$learnerName   = trim($_POST['learnerName']);
$learnerID     = trim($_POST['learnerID']);
$issueDate     = trim($_POST['issueDate']); // YYYY-MM-DD

if (!$email) {
  http_response_code(422);
  exit('Invalid email address');
}

// Optional: further validation on date format
$dt = DateTime::createFromFormat('Y-m-d', $issueDate);
if (!$dt || $dt->format('Y-m-d') !== $issueDate) {
  http_response_code(422);
  exit('Invalid issue date');
}

// Save to DB via PDO prepared statements
try {
  $pdo = new PDO(
    'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
    DB_USER,
    DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
  );

  $stmt = $pdo->prepare('INSERT INTO verification_requests
    (name, organisation, email, learner_name, learner_id, issue_date)
    VALUES (:name, :organisation, :email, :learner_name, :learner_id, :issue_date)');

  $stmt->execute([
    ':name' => $name,
    ':organisation' => $organisation,
    ':email' => $email,
    ':learner_name' => $learnerName,
    ':learner_id' => $learnerID,
    ':issue_date' => $issueDate,
  ]);

  $insertId = $pdo->lastInsertId();
}
catch (Throwable $e) {
  http_response_code(500);
  exit('Database error');
}

// Send confirmation email via PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__.'/vendor/autoload.php'; // after "composer require phpmailer/phpmailer"

try {
  $mail = new PHPMailer(true);
  $mail->isSMTP();
  $mail->Host       = SMTP_HOST;      // e.g. smtp.yourdomain.com
  $mail->SMTPAuth   = true;
  $mail->Username   = SMTP_USER;      // your full email
  $mail->Password   = SMTP_PASS;      // app password / SMTP pass
  $mail->Port       = SMTP_PORT;      // 587 (TLS) or 465 (SSL)
  if (SMTP_SECURE === 'ssl') { $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; }
  else { $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; }

  $mail->setFrom(SMTP_FROM, 'Verification Desk');
  $mail->addAddress($email, $name); // Send result to requester

  $mail->Subject = 'Your verification request (ID #'.$insertId.') received';
  $mail->isHTML(true);
  $mail->Body = '<p>Dear '.htmlspecialchars($name).',</p>'
    .'<p>Your verification request has been received. Reference ID: <strong>#'.$insertId.'</strong>.</p>'
    .'<p>Summary:</p>'
    .'<ul>'
    .'<li>Organisation: '.htmlspecialchars($organisation).'</li>'
    .'<li>Learner: '.htmlspecialchars($learnerName).'</li>'
    .'<li>Learner/Certificate No.: '.htmlspecialchars($learnerID).'</li>'
    .'<li>Issue Date: '.htmlspecialchars($issueDate).'</li>'
    .'</ul>'
    .'<p>We will get back to you within 2 working days.</p>'
    .'<p>Regards,<br>Verification Team</p>';
  $mail->AltBody = "Your verification request has been received. Ref #$insertId";

  $mail->send();
}
catch (Throwable $e) {
  // If email fails, we still show success page (optional: log $e->getMessage())
}

// Optional: clear localStorage via redirect flag
header('Location: thankyou.html?ref='.$insertId);
exit;

?>
