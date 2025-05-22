<?php
require_once '../utils/session.php';
require_once '../utils/mailer.php';
$db = (new Database())->getConnection();
$app = new Application($db);

$data = json_decode(file_get_contents("php://input"), true);
$consultationId = $data['id'];
$consultation = $app->getConsultationById($consultationId);
$teacher = $app->getUserById($consultation->ownerId);
$response = ['success' => false];

if($consultation) {
    // pouze učitel může zrušit svojí konzultaci nebo admin může zrušit všechny konzultace
    if($user instanceof Teacher && $consultation->ownerId === $user->userId) {
        $app->cancelConsultation($consultationId);
        $response['success'] = true;
    }
    if ($user instanceof Admin){
        $app->cancelConsultation($consultationId);
        $response['success'] = true;
    }
    if ($response['success'] && $consultation->studentId !== null) {
        $student = $app->getUserById($consultation->studentId);
        sendCancelConsultationEmailForStudent($student, $teacher, $consultation);
    }
    if ($response['success']) {
        sendCancelConsultationEmailForTeacher($teacher, $consultation);
    }
}
echo json_encode($response);
?>