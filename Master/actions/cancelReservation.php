<?php
require_once '../utils/session.php';
require_once '../utils/mailer.php';
$db = (new Database())->getConnection();
$app = new Application($db);

$data = json_decode(file_get_contents("php://input"), true);
$consultationId = $data['id'];
$consultation = $app->getConsultationById($consultationId);
$response = ['success' => false];

if($consultation) {
    // pouze student může zrušit svojí rezervaci nebo admin může zrušit všechny rezervace
    if($user instanceof Student && $consultation->studentId === $user->userId) {
        $app->cancelReservation($consultationId);
        $response['success'] = true;
    }
    if ($user instanceof Admin){
        $app->cancelReservation($consultationId);
        $response['success'] = true;
    }
}
ob_start();
echo json_encode($response);
header('Content-Type: application/json');
header('Connection: close');
header('Content-Length: ' . ob_get_length());
ob_end_flush();
flush(); // Zajistí odeslání odpovědi klientovi

// Pokračování v dalších operacích, například odeslání e-mailu
if ($response['success']) {
    $teacher = $app->getUserById($consultation->ownerId);
    $student = $app->getUserById($consultation->studentId);
    sendCancelReservationEmailForStudent($student, $teacher, $consultation);
    sendCancelReservationEmailForTeacher($student, $teacher, $consultation);
}
?>