<?php
require_once "../models/consultation.php";
require_once "../models/users.php";
require_once "../services/database.php";
require_once "../services/application.php";

function getCurrentUser() {
    session_start();

    if (!isset($_SESSION["access_token"])) {
        die("Nejste přihlášen.");
    }

    $accessToken = $_SESSION["access_token"];
    $url = "https://graph.microsoft.com/v1.0/me";

    $headers = [
        "Authorization: Bearer $accessToken",
        "Accept: application/json"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        $user = json_decode($response, true);
        return $user;
    } else {
        die("Chyba při načítání profilu.");
    }
}

$oAuthUser = getCurrentUser();
$db = (new Database())->getConnection();
$app = new Application($db);
$user = $app->findUserByLoginName($oAuthUser["userPrincipalName"]);
$userId = null;

if (empty($user)) {
    $userId = $app->createUser($oAuthUser["givenName"], $oAuthUser["surname"], $oAuthUser["userPrincipalName"], $oAuthUser["id"]);
    $user = new Student($userId, $oAuthUser["givenName"], $oAuthUser["surname"], $oAuthUser["userPrincipalName"]);
} else {
    $userId = $user->userId;
    if ($user instanceof Student) {
        $user = new Student($userId, $user->firstName, $user->lastName, $user->loginName);
    }
    if ($user instanceof Teacher) {
        $user = new Teacher($userId, $user->firstName, $user->lastName, $user->loginName);
    }
    if ($user instanceof Admin) {
        $user = new Admin($userId, $user->firstName, $user->lastName, $user->loginName);
    }
}
?>