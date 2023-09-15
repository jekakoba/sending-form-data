<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);
$mail->CharSet = 'UTF-8';
$mail->setLanguage('en', 'phpmailer/language/');
$mail->IsHTML(true);

$senderName = $_POST['userName'];
$senderStreet = $_POST['street'];
$senderCity = $_POST['city'];
$senderPoscode = $_POST['poscode'];
$senderEmail = $_POST['email'];
$senderPhone = $_POST['phone'];
$message = $_POST['message'];

if (!preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,8})+$/', $senderEmail)) {
    $response['message'] = 'Invalid email';
} else {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = 'TLS';
    $mail->Port       = 587;
    $mail->Username   = 'endlesss2k2k@gmail.com';
    $mail->Password   = 'akxmvdlvdkfhzmmm';
    $mail->setFrom('endlesss2k2k@gmail.com', 'Feedback');  // Від кого лист

    // Mail of the recipient
    $mail->addAddress('endlesss2k2k@gmail.com'); // Вкажіть бажаний електронний лист одержувача

    $mail->Subject = 'Feedback';
    $html = file_get_contents('template.html');
    $html = str_replace('{{sender_name}}', $senderName, $html);
    $html = str_replace('{{sender_street}}', $senderStreet, $html);
    $html = str_replace('{{sender_city}}', $senderCity, $html);
    $html = str_replace('{{sender_poscode}}', $senderPoscode, $html);
    $html = str_replace('{{sender_email}}', $senderEmail, $html);
    $html = str_replace('{{sender_phone}}', $senderPhone, $html);
    $html = str_replace('{{message}}', $message, $html);

    $mail->msgHTML($html);

   // Отримання завантажених файлів
   $uploadedFiles = $_FILES['file'];

// Максимальний допустимий розмір файлу (в байтах)
$maxFileSize = 10 * 1024 * 1024; // 10 МБ

// Допустимі розширення файлів
$allowedExtensions = array('doc', 'docx', 'pdf');

   // Checking, whether the downloaded files are
   if (!empty($uploadedFiles)) {
	   // Перебираємо завантажені файли
	   for ($i = 0; $i < count($uploadedFiles['name']); $i++) {
		   $tmpFilePath = $uploadedFiles['tmp_name'][$i];
		   $fileName = $uploadedFiles['name'][$i];


		   // Отримання розміру файлу
		   $fileSize = filesize($tmpFilePath);

		   // Отримання розширення файлу
		   $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

		   // Перевірка, чи файл завантажений успішно
		   if (is_uploaded_file($tmpFilePath)&& $fileSize <= $maxFileSize && in_array(strtolower($fileExtension), $allowedExtensions)) {
			   // Додавання файлу до листа
			   $mail->addAttachment($tmpFilePath, $fileName);
		   }
		   else{
			$response['message'] = 'Error';
		   }
	   }
   }

    if (!$mail->send()) {
        $response['message'] = 'Error';
    } else {
        $response['message'] = 'Success';
    }
}

header('Content-type: application/json');
echo json_encode($response);

// Telegram
$content = '';
foreach ($_POST as $key => $value) {
    if ($value){
        $content .="<b>$key</b>: <i>$value</i>\n";
    }
}
if(trim($content)){
    $content = "<b>Message from Get in Touch:</b>\n".$content;
    $apiToken = "6522109225:AAEGXYe6qcYPSeU9Mcu5MDEtPjMzanfI3bw";
    $data = [
        'chat_id' => '@JekaKobaMessage',
        'text' => "$content",
        'parse_mode' => 'HTML'
    ];

    $responseTelegram = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?".http_build_query($data));
}
?>