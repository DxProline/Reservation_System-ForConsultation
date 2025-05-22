<?php
require_once '../utils/session.php';
require_once '../utils/mailer.php';
$db = (new Database())->getConnection();
$app = new Application($db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($user instanceof Student) {
        $studentId = $user->userId;
    }
    if ($user instanceof Admin) {
        $studentId = $_POST['selectedUserId'];
    } 
    if ($user instanceof Teacher) {
        header("Location: consultationListT.php");
        exit;
    }
    if ($user instanceof Admin && $studentId === null) {
        header("Location: userList.php");
        exit;
    }
    $consultationId = $_POST['consultationId'];
    $descriptionFromStudent = $_POST['descriptionFromStudent'];
    $subject = $_POST['subject'];

    $consultation = $app->getConsultationById($consultationId);
    $teacher = $app->getUserById($consultation->ownerId);
    $student = $app->getUserById($studentId);

    $result = $app->createReservation($studentId, $consultationId, $descriptionFromStudent, $subject);

    if ($result) {
        header('Location: ../views/consultationListS.php?filter=free&selectedUserId=' . $studentId);
        sendReservationEmailForStudent($student, $teacher, $consultation);
        sendReservationEmailForTeacher($student, $teacher, $consultation);
        exit;
    } else {
        echo "Chyba při rezervaci konzultace.";
    }
} else {
    echo "Neplatný požadavek.";
}
?>