<?php
require_once '../utils/session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if($user instanceof Teacher) {
        $teacherId = $user->userId;
    }
    if($user instanceof Admin) {
        $teacherId = $_POST['teacherId'];
    }
    if ($user instanceof Student) {
        header("Location: consultationListS.php");
        exit;
    }
    if ($user instanceof Admin && $teacherId === null) {
        header("Location: userList.php");
        exit;
    }
    $consultationDate = $_POST['consultationDate'];
    $duration = $_POST['duration'];
    $subject = $_POST['subject'];
    $descriptionFromTeacher = $_POST['descriptionFromTeacher'];

    if ($app->checkConsultationForOverlap($teacherId, $consultationDate, $duration)) {
        header('Location: ../views/consultationCreateDetail.php?selectedUserId=' . $teacherId . '&error=overlap');
        exit();
    }
    $consultationId = $app->createConsultation($teacherId, $descriptionFromTeacher, $consultationDate, $duration, $subject);
    
    if ($consultationId) {
        header('Location: ../views/consultationListT.php?filter=free&selectedUserId=' . $teacherId);
        exit();
    } else {
        echo "Error creating consultation.";
    }
} else {
    die("Invalid request.");
}
?>