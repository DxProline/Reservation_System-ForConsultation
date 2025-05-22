<?php
require_once '../utils/session.php';
$db = (new Database())->getConnection();
$app = new Application($db);

$consultationId = isset($_GET['id']) ? $_GET['id'] : null;
$selectedUserId = isset($_GET['selectedUserId']) ? $_GET['selectedUserId'] : null;
$consultation = $app->getConsultationById($consultationId);

if (!$consultation) {
    echo "Konzultace nebyla nalezena.";
    exit;
}

$teacher = $app->getUserById($consultation->ownerId);

if (!$teacher) {
    echo "Učitel nebyl nalezen.";
    exit;
}

if ($user instanceof Student) {
    $selectedUserId = $userId;
}

if ($user instanceof Teacher) {
    header("Location: consultationListT.php");
    exit;
}
if ($user instanceof Admin && $selectedUserId === null) {
    header("Location: userList.php");
    exit;    
}


$student = $app->getUserById($selectedUserId);

if (!$student) {
    echo "Student nebyl nalezen.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail konzultace</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/styleDetail.css">
    <script src="js/logout.js"></script>
    <script src="js/consultationReserveDetail.js"></script>
</head>
<body>
    <div class="consultation-container">
        <div class="header">
            <div class="logo"><img src="images/Logo_Students.png" alt=""></div>
            <div class="nav">
                <a href="consultationListS.php?filter=free&selectedUserId=<?= $selectedUserId ?>" >Zpět na seznam</a>
            </div>
            <div class="user">
                <img src="images/user_ikon.jpg" alt="">
                <span id="login-user"> <?php  echo "" . $user ?></span>
                <div class="logout">
                    <span id="logout">Odhlásit</span>
                </div>
            </div>
        </div>
        <h2>DETAIL KONZULTACE
        <?php if ($user instanceof Admin && $selectedUserId !== null): ?>
            (<?= htmlspecialchars($student->firstName . ' ' . $student->lastName) ?>)
        <?php endif; ?>
        </h2>
        <div class="consultation-detail">

            <form action="../actions/reserveConsultation.php" id="consultation-form" method="post">
                <input type="hidden" name="consultationId" value="<?= htmlspecialchars($consultation->consultationId) ?>">
                <input type="hidden" name="selectedUserId" value="<?= htmlspecialchars($selectedUserId) ?>">
                <label for="teacher">Učitel:</label>
                <input type="text" id="teacher" name="teacher" value="<?= htmlspecialchars($teacher->firstName . ' ' . $teacher->lastName) ?>" readonly>
                
                <label for="consultationDate">Datum:</label>
                <input type="datetime-local" id="consultationDate" name="consultationDate" value="<?= htmlspecialchars($consultation->consultationDate) ?>" readonly>
            
                <label for="duration">Délka:</label>
                <input type="text" id="duration" name="duration" value="<?= htmlspecialchars($consultation->duration) ?>" readonly>
            
                <label for="subject">Předmět:</label>
                <input type="text" id="subject" name="subject" value="<?= htmlspecialchars($consultation->subject) ?>" <?= $consultation->subjectLocked ? 'readonly' : '' ?>>
                <span id="subject-empty-message" style="color: red; display: none;">Předmět nesmí být prázdný</span>
                <span id="subject-too-long-message" style="color: red; display: none;">Předmět nesmí být delší než 80 znaků</span>
            
                <label for="descriptionFromTeacher">Popis od učitele:</label>
                <textarea id="descriptionFromTeacher" name="descriptionFromTeacher" readonly><?= htmlspecialchars($consultation->descriptionFromTeacher) ?></textarea>
            
                <label for="descriptionFromStudent">Popis od studenta:</label>
                <textarea id="descriptionFromStudent" name="descriptionFromStudent" placeholder="Zde napište svůj problém..." ><?= htmlspecialchars($consultation->descriptionFromStudent) ?></textarea>
                <button class="reserve-consultation-submit" type="submit">Rezervovat</button>
            </form>
        </div>
    </div>
</body>
</html>