<?php
require_once '../utils/session.php';
$db = (new Database())->getConnection();
$app = new Application($db);
$filter = isset($_GET['filter']) ? $_GET['filter'] : "students";
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

if ($user instanceof Student) {
    header("Location: consultationListS.php");
    exit;
}
if ($user instanceof Teacher) {
    header("Location: consultationListT.php");
    exit;
}

switch ($filter) {
    case "teachers":
        $users = $app->listUsers(1, $limit, $offset);
        $totalUsers = $app->countUsers(1);
        $pageTitle = "SEZNAM UČITELŮ:";
        $tableClass = "teachers";
        break;
    case "students":
        $users = $app->listUsers(0, $limit, $offset);
        $totalUsers = $app->countUsers(0);
        $pageTitle = "SEZNAM STUDENTŮ:";
        $tableClass = "students";
        break;
    case "admins":
        $users = $app->listUsers(2, $limit, $offset);
        $totalUsers = $app->countUsers(2);
        $pageTitle = "SEZNAM ADMINISTRÁTORŮ:";
        $tableClass = "admins";
        break;
    default:
        die("Neplatný filtr.");
}

$totalPages = ceil($totalUsers / $limit);
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seznam uživatelů</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/styleEdit.css">
    <script src="js/logout.js"></script>
    <script src="js/userList.js"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo"><img src="images/Logo_Students.png" alt=""></div>
            <div class="nav">
                <a href="userList.php?filter=teachers" class="<?= $filter === 'teachers' ? 'active' : '' ?>">Správa učitelů</a>
                <a href="userList.php?filter=students" class="<?= $filter === 'students' ? 'active' : '' ?>">Správa studentů</a>
                <a href="userList.php?filter=admins" class="<?= $filter === 'admins' ? 'active' : '' ?>">Správa administrátorů</a>
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
                    <th>Příjmení</th>
                    <th>Jméno</th>
                    <th>E-mail</th>
                    <th>Zastupování</th>
                </tr>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td data-label="ID">
                            <?php if ($u->userType < 2): ?>
                                <div class="userPromoteButton userPromote" data-id="<?= $u->userId ?>" data-action="promoteUser">Povýšit</div>
                            <?php endif; ?>
                            <?php if ($u->userType > 0): ?>
                                <div class="userDegradeButton userDegrade" data-id="<?= $u->userId ?>" data-action="degradeUser">Sesadit</div>
                            <?php endif; ?>
                        </td>
                        <td data-label="Příjmení"><?= htmlspecialchars($u->lastName) ?></td>
                        <td data-label="Jméno"><?= htmlspecialchars($u->firstName) ?></td>
                        <td data-label="E-Mail"><?= htmlspecialchars($u->loginName) ?></td>
                        <td data-label="Zastupování">
                            <div class="userSelectButton userSelect"
                                data-id="<?= $u->userId ?>"
                                data-filter="<?= $filter ?>"
                                data-action="select">Přepnout</div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
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