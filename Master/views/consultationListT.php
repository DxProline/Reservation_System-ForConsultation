<?php
require_once '../utils/session.php';
$db = (new Database())->getConnection();
$app = new Application($db);
$filter = isset($_GET['filter']) ? $_GET['filter'] : "free";
$selectedUserId = isset($_GET['selectedUserId']) ? $_GET['selectedUserId'] : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 8;
$offset = ($page - 1) * $limit;

// Učitel ani student se nemohou vydávat za někoho jiného, takže pokud si vyberou ID jiného uživatele pomocí selectedUserId, tak se vrátí zpět na své ID
if ($user instanceof Teacher || $user instanceof Student) {
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
// Pokud je přihlášen admin a je nastaven $selectedUserId, získáme informace o uživateli
if ($user instanceof Admin && $selectedUserId !== null) {
    $selectedUser = $app->getUserById($selectedUserId);
    if ($selectedUser) {
        $userFullName = htmlspecialchars($selectedUser->firstName . ' ' . $selectedUser->lastName);
    } else {
        die ("Uživatel nebyl nalezen.");
    }
} else {
    $userFullName = "";
}

switch ($filter) {
    case "reserved":
        $consultations = $app->listReservedConsultationsForTeacher($selectedUserId, $limit, $offset);
        $totalConsultations = $app->countReservedConsultationsForTeacher($selectedUserId);
        $pageTitle = "SEZNAM REZERVOVANÝCH KONZULTACÍ" . ($userFullName ? " ({$userFullName})" : "") . ":";
        $tableClass = "reservedConsultationsForTeacher";
        break;
    case "free":
        $consultations = $app->listFreeConsultationsForTeacher($selectedUserId, $limit, $offset);
        $totalConsultations = $app->countFreeConsultationsForTeacher($selectedUserId);
        $pageTitle = "SEZNAM VOLNÝCH KONZULTACÍ" . ($userFullName ? " ({$userFullName})" : "") . ":";
        $tableClass = "freeConsultationsForTeacher";
        break;
    case "past":
        $consultations = $app->listPastConsultationsForTeacher($selectedUserId, $limit, $offset);
        $totalConsultations = $app->countPastConsultationsForTeacher($selectedUserId);
        $pageTitle = "SEZNAM PROBĚHLÝCH KONZULTACÍ" . ($userFullName ? " ({$userFullName})" : "") . ":";
        $tableClass = "pastConsultationsForTeacher";
        break;
    default:
        die("Neplatný filtr.");
}

$totalPages = ceil($totalConsultations / $limit);
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seznam konzultací</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/styleEdit.css">
    <script src="js/consultationListT.js"></script>
    <script src="js/logout.js"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo"><img src="images/Logo_Students.png" alt=""></div>
            <div class="nav">
                <a href="consultationListT.php?filter=reserved&selectedUserId=<?= $selectedUserId ?>" class="<?= $filter === 'reserved' ? 'active' : '' ?>">Rezervované kontultace</a>
                <a href="consultationListT.php?filter=free&selectedUserId=<?= $selectedUserId ?>" class="<?= $filter === 'free' ? 'active' : '' ?>">Moje konzultace</a>
                <a href="consultationListT.php?filter=past&selectedUserId=<?= $selectedUserId ?>" class="<?= $filter === 'past' ? 'active' : '' ?>">Proběhlé konzultace</a>
                <?php if ($user instanceof Admin): ?>
                    <a href="userList.php">Správa uživatelů</a>
                <?php endif; ?>
            </div>
            <div class="user">
                <img src="images/user_ikon.jpg" alt="">
                <span id="login-user"> <?php  echo "" . $user ?></span>
                <div class="logout">
                    <span id="logout">Odhlásit</span>
                </div>
            </div>
        </div>
        <h2 class="title"><?= $pageTitle ?></h2>
        <div class="table-container">
            <table class="<?= $tableClass ?>">

                <tr>
                    <th>Akce</th>
                    <th>Datum</th>
                    <th>Délka</th>
                    <th>Předmět</th>
                    <?php if ($filter === "reserved" || $filter === "past"): ?>
                        <th>Student</th>
                    <?php else: ?>
                        <th>Popis od učitele</th>
                    <?php endif; ?>
                    <?php if ($filter === "reserved" || $filter === "past"): ?>
                        <th>Popis od žáka</th>
                    <?php endif; ?>
                </tr>
                <?php foreach ($consultations as $consultation): ?>
                    <tr>
                        <td data-label="ID">
                            <?php if ($filter === "past"): ?>
                                <div class="reservationButton" data-id="<?= $consultation->consultationId ?>" data-action="completed" style="cursor: default; color: black;">Proběhnuto</div>
                            <?php else: ?>
                                <div class="reservationButton cancel" data-id="<?= $consultation->consultationId ?>" data-action="cancel">Zrušit</div>
                            <?php endif; ?>
                        </td>
                        <td data-label="Datum"><?= htmlspecialchars($consultation->consultationDate) ?></td>
                        <td data-label="Délka"><?= htmlspecialchars($consultation->duration) ?> mins</td>
                        <td data-label="Předmět"><?= htmlspecialchars($consultation->subject) ?></td>
                        <?php if ($filter === "reserved" || $filter === "past"): ?>
                            <td data-label="Student"><?= htmlspecialchars($consultation->studentName) ?></td>
                        <?php else: ?>
                            <td data-label="Popis od učitele"><?= htmlspecialchars($consultation->descriptionFromTeacher) ?></td>
                        <?php endif; ?>
                        <?php if ($filter === "reserved" || $filter === "past"): ?>
                            <td data-label="Popis od žáka"><?= htmlspecialchars($consultation->descriptionFromStudent) ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php if ($filter === "free"): ?>
            <div class="custom-button create-consultation" id="create">Vytvořit konzultaci</div>
        <?php endif; ?>
        <div class="fill"></div>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?filter=<?= $filter ?>&page=<?= $page - 1 ?>" class="prev">← Předchozí</a>
            <?php endif; ?>
            <span class="current-page"><?= $page ?></span>
            <?php if ($page < $totalPages): ?>
                <a href="?filter=<?= $filter ?>&page=<?= $page + 1 ?>" class="next">Další →</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>