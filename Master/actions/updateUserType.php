<?php
require_once '../utils/session.php';
if (!($user instanceof Admin)) {
    header("Location: ../index.php");
    exit;
}
$db = (new Database())->getConnection();
$app = new Application($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedUserId = isset($_POST['userId']) ? (int)$_POST['userId'] : null;
    $action = isset($_POST['action']) ? $_POST['action'] : null;

    if ($updatedUserId && $action) {
        $updatedUser = $app->getUserById($updatedUserId);
        if ($updatedUser) {
            $newUserType = $updatedUser->userType;
            if ($action === 'promote' && $updatedUser->userType < 2) {
                $newUserType++;
            } elseif ($action === 'degrade' && $updatedUser->userType > 0) {
                $newUserType--;
            }
            $app->updateUserType($updatedUserId, $newUserType);
            echo "success";
        } else {
            echo "user not found";
        }
    } else {
        echo "invalid request";
    }
} else {
    echo "invalid request method";
}
?>