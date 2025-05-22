<?php
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function prepareEmail(){
    try {
        $mail = new PHPMailer(true);
        /*$mail->SMTPDebug = 2; // Nastavení úrovně debug */
    
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // SMTP server Google
        $mail->SMTPAuth   = true;
        $mail->Username   = 'yourMail'; // Tvůj Gmail
        $mail->Password   = EMAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
    
        // Odesílatel a příjemce
        $mail->setFrom('yourMail', 'NoReply RS Reservation System');
    
        // **Nastavení kódování na UTF-8**
        $mail->CharSet = 'UTF-8';
    
        // Obsah e-mailu
        $mail->isHTML(true);
        return $mail;
    } catch (Exception $e) {
        return null;
    }
}
function sendReservationEmailForStudent($student, $teacher, $consultation){
    try {
        $mail = prepareEmail();
        if($mail === null){
            return false;
        }
        $mail->addAddress($student->getEmail(), $student->firstName . ' ' . $student->lastName);
        $mail->Subject = 'Rezervace konzultace';
        $mail->Body    = 'Dobrý den, ' . $student->firstName . ' ' . $student->lastName . ',<br><br>'
                        . 'rezervovali jste si konzultaci s učitelem ' . $teacher->firstName . ' ' . $teacher->lastName . '.<br>'
                        . 'Datum a čas konzultace: ' . $consultation->consultationDate . '<br>'
                        . 'S pozdravem,<br>'
                        . 'RS Reservation System';
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
function sendReservationEmailForTeacher($student, $teacher, $consultation){
    try {
        $mail = prepareEmail();
        if($mail === null){
            return false;
        }
        $mail->addAddress($teacher->getEmail(), $teacher->firstName . ' ' . $teacher->lastName);
        $mail->Subject = 'Rezervace konzultace';
        $mail->Body    = 'Dobrý den, ' . $teacher->firstName . ' ' . $teacher->lastName . ',<br><br>'
                        . 'student ' . $student->firstName . ' ' . $student->lastName . ' si rezervoval konzultaci s Vámi.<br>'
                        . 'Datum a čas konzultace: ' . $consultation->consultationDate . '<br>'
                        . 'Předmět konzultace: ' . $consultation->subject . '<br>'
                        . 'Popis od studenta: ' . $consultation->descriptionFromStudent . '<br>'
                        . 'S pozdravem,<br>'
                        . 'RS Reservation System';
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function sendCancelReservationEmailForTeacher($student, $teacher, $consultation){
    try {
        $mail = prepareEmail();
        if($mail === null){
            return false;
        }
        $mail->addAddress($teacher->getEmail(), $teacher->firstName . ' ' . $teacher->lastName);
        $mail->Subject = 'Zrušení rezervace konzultace';
        $mail->Body    = 'Dobrý den, ' . $teacher->firstName . ' ' . $teacher->lastName . ',<br><br>'
                        . 'student ' . $student->firstName . ' ' . $student->lastName . ' zrušil rezervaci konzultace s Vámi.<br>'
                        . 'Datum a čas konzultace: ' . $consultation->consultationDate . '<br>'
                        . 'S pozdravem,<br>'
                        . 'RS Reservation System';
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
function sendCancelReservationEmailForStudent($student, $teacher, $consultation){
    try {
        $mail = prepareEmail();
        if($mail === null){
            return false;
        }
        $mail->addAddress($student->getEmail(), $student->firstName . ' ' . $student->lastName);
        $mail->Subject = 'Zrušení rezervace konzultace';
        $mail->Body    = 'Dobrý den, ' . $student->firstName . ' ' . $student->lastName . ',<br><br>'
                        . 'byla zrušena rezervace konzultace s učitelem ' . $teacher->firstName . ' ' . $teacher->lastName . '.<br>'
                        . 'Datum a čas konzultace: ' . $consultation->consultationDate . '<br>'
                        . 'S pozdravem,<br>'
                        . 'RS Reservation System';
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
function sendCancelConsultationEmailForStudent($student, $teacher, $consultation){
    try {
        $mail = prepareEmail();
        if($mail === null){
            return false;
        }
        $mail->addAddress($student->getEmail(), $student->firstName . ' ' . $student->lastName);
        $mail->Subject = 'Zrušení konzultace';
        $mail->Body    = 'Dobrý den, ' . $student->firstName . ' ' . $student->lastName . ',<br><br>'
                        . 'byla zrušena konzultace, kterou jste měli rezervovanou s učitelem ' . $teacher->firstName . ' ' . $teacher->lastName . '.<br>'
                        . 'Datum a čas konzultace: ' . $consultation->consultationDate . '<br>'
                        . 'S pozdravem,<br>'
                        . 'RS Reservation System';
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
function sendCancelConsultationEmailForTeacher($teacher, $consultation){
    try {
        $mail = prepareEmail();
        if($mail === null){
            return false;
        }
        $mail->addAddress($teacher->getEmail(), $teacher->firstName . ' ' . $teacher->lastName);
        $mail->Subject = 'Zrušení konzultace';
        $mail->Body    = 'Dobrý den, ' . $teacher->firstName . ' ' . $teacher->lastName . ',<br><br>'
                        . 'byla zrušena konzultace, kterou jste měli vytvořenou.<br>'
                        . 'Datum a čas konzultace: ' . $consultation->consultationDate . '<br>'
                        . 'S pozdravem,<br>'
                        . 'RS Reservation System';
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
