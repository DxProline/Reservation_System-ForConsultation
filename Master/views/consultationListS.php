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

if ($user instanceof Teacher) {
    header("Location: consultationListT.php");
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
        $consultations = $app->listConsultationsForStudent($selectedUserId, $limit, $offset);
        $totalConsultations = $app->countConsultationsForStudent($selectedUserId);
        $pageTitle = "SEZNAM REZERVOVANÝCH KONZULTACÍ" . ($userFullName ? " ({$userFullName})" : "") . ":";
        $tableClass = "reservedConsultationsForStudent";
        break;
    case "free":
        $consultations = $app->listFreeConsultationsForStudent($limit, $offset);
        $totalConsultations = $app->countFreeConsultationsForStudent();
        $pageTitle = "SEZNAM VOLNÝCH KONZULTACÍ" . ($userFullName ? " ({$userFullName})" : "") . ":";
        $tableClass = "freeConsultationsForStudent";
        break;
    case "past":
        $consultations = $app->listPastConsultationsForStudent($selectedUserId, $limit, $offset);
        $totalConsultations = $app->countPastConsultationsForStudent($selectedUserId);
        $pageTitle = "SEZNAM PROBĚHLÝCH KONZULTACÍ" . ($userFullName ? " ({$userFullName})" : "") . ":";
        $tableClass = "pastConsultationsForStudent";
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
    <script src="js/reserveConsultation.js"></script>
    <script src="js/logout.js"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo"><img src="images/Logo_Students.png" alt=""></div>
            <div class="nav">
                <a href="consultationListS.php?filter=reserved&selectedUserId=<?= $selectedUserId ?>" class="<?= $filter === 'reserved' ? 'active' : '' ?>">Rezervované kontultace</a>
                <a href="consultationListS.php?filter=free&selectedUserId=<?= $selectedUserId ?>" class="<?= $filter === 'free' ? 'active' : '' ?>">Volné konzultace</a>
                <a href="consultationListS.php?filter=past&selectedUserId=<?= $selectedUserId ?>" class="<?= $filter === 'past' ? 'active' : '' ?>">Proběhlé konzultace</a>
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
                    <th>Učitel</th>
                    <th>Předmět</th>
                    <th>Popis od učitele</th>
                    <?php if ($filter === "reserved" || $filter === "past"): ?>
                        <th>Popis od žáka</th>
                    <?php endif; ?>
                </tr>
                <!-- Data konzultací zde -->

                <?php foreach ($consultations as $consultation): ?>
                    <tr>
                        <td data-label="ID">
                            <?php if ($filter === "reserved"): ?>
                                <div class="reservationButton cancel" data-id="<?= $consultation->consultationId ?>" data-action="cancel" data-selected-user-id="<?= $selectedUserId ?>">Zrušit</div>
                            <?php elseif ($filter === "past"): ?>
                                <div class="reservationButton" data-id="<?= $consultation->consultationId ?>" data-action="completed" style="cursor:default;">Proběhnuto</div>
                            <?php else: ?>
                                <div class="reservationButton" id="create" data-id="<?= $consultation->consultationId ?>" data-action="reserve" data-selected-user-id="<?= $selectedUserId ?>">Rezervovat</div>
                            <?php endif; ?>
                        </td>
                        <td data-label="Datum"><?= htmlspecialchars($consultation->consultationDate) ?></td>
                        <td data-label="Délka"><?= htmlspecialchars($consultation->duration) ?> mins</td>
                        <td data-label="Učitel"><?= htmlspecialchars($consultation->ownerName) ?></td>
                        <td data-label="Předmět"><?= htmlspecialchars($consultation->subject) ?></td>
                        <td data-label="Popis od učitele"><?= htmlspecialchars($consultation->descriptionFromTeacher) ?></td>
                        <?php if ($filter === "reserved" || $filter === "past"): ?>
                            <td data-label="Popis od žáka"><?= htmlspecialchars($consultation->descriptionFromStudent) ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="pagination">
        <?php
            if ($page > 1): ?>
                <a href="?filter=<?= $filter ?>&page=<?= $page - 1 ?>" class="prev">← Předchozí</a>
            <?php endif; ?>
            <span class="current-page"><?= $page ?></span>
            <?php if ($page < $totalPages): ?>
                <a href="?filter=<?= $filter ?>&page=<?= $page + 1 ?>" class="next">Další →</a>
            <?php endif; ?>
        </div>
        <div class="fill"></div>
    </div>
</body>
</html>