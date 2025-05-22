<?php
require_once '../utils/session.php';
$db = (new Database())->getConnection();
$app = new Application($db);

$selectedUserId = isset($_GET['selectedUserId']) ? $_GET['selectedUserId'] : null;

if ($user instanceof Teacher) {
    $selectedUserId = $userId;
}
if ($user instanceof Student) {
    header("Location: consultationListS.php");
    exit;
}
if ($user instanceof Admin && $selectedUserId === null) {
    header("Location: userList.php");
    exit;
}

$teacher = $selectedUserId ? $app->getUserById($selectedUserId) : $user;
if($teacher === null) {
    $teacher = $user;
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
    <script src="js/createConsultation.js"></script>
</head>
<body>
    <div class="consultation-container">
        <div class="header">
            <div class="logo"><img src="images/Logo_Students.png" alt=""></div>
            <div class="nav">
                <a href="consultationListT.php?filter=free&selectedUserId=<?= $selectedUserId ?>" >Zpět na seznam</a>
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
            (<?= htmlspecialchars($teacher->firstName . ' ' . $teacher->lastName) ?>)
        <?php endif; ?>
        </h2>
        <div class="consultation-detail">

            <form action="../actions/createConsultation.php" id="consultation-form" method="post">

                <input type="hidden" name="teacherId" value="<?= htmlspecialchars($teacher->userId) ?>">
                <label for="teacher">Učitel:</label>
                <input type="text" id="teacher" name="teacher" value="<?= htmlspecialchars($teacher->firstName . ' ' . $teacher->lastName) ?>" readonly>
                
                <label for="consultationDate">Datum:</label>
                <input type="datetime-local" id="consultationDate" name="consultationDate" value="">
                <span id="date-error-message" style="color: red; display: none;">Nebylo zadáno správné datum nebo datum je v minulosti.</span>
                <span id="overlap-error-message" style="color: red; display: none;">Konzultace se překrývá s jinou konzultací</span>
            
                
                <label for="duration">Délka:</label>
                <input type="number" id="duration" name="duration" value="45" min="10">
                <span id="duration-error-message" style="color: red; display: none;">Konzultace musí být v rozsahu 10-100 minut.</span>

                <label for="subject">Předmět:</label>
                <input type="text" id="subject" name="subject" value="">
                <span id="subject-error-message" style="color: red; display: none;">Předmět nesmí být delší než 80 znaků</span>
    
                <label for="descriptionFromTeacher">Popis od učitele:</label>
                <textarea id="descriptionFromTeacher" name="descriptionFromTeacher"></textarea>
            
                <button class="create-consultation-submit" type="submit">Vytvořit</button>
            </form>
        </div>
    </div>
</body>
</html>