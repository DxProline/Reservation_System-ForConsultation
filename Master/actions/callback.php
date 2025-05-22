<?php
session_start();
require_once '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$tenantId = $_ENV['TENANT_ID'];           // ID tenanta
$clientId = $_ENV['CLIENT_ID'];          // ID aplikace (client_id)
$clientSecret = $_ENV['CLIENT_SECRET'];  // Tajný klíč aplikace
$redirectUri = $_ENV['REDIRECT_URI'];

if (!isset($_GET['code'])) {
    die("Chybí authorization code.");
}

$code = $_GET['code'];
$tokenUrl = "https://login.microsoftonline.com/$tenantId/oauth2/v2.0/token";

$data = [
    "client_id" => $clientId,
    "client_secret" => CLIENT_SECRET,
    "code" => $code,
    "redirect_uri" => $redirectUri,
    "grant_type" => "authorization_code",
    "scope" => "https://graph.microsoft.com/User.Read"
];

$options = [
    "http" => [
        "header" => "Content-Type: application/x-www-form-urlencoded",
        "method" => "POST",
        "content" => http_build_query($data),
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($tokenUrl, false, $context);
$tokenData = json_decode($response, true);

if (!isset($tokenData["access_token"])) {
    die("Chyba při získávání tokenu.");
}

$_SESSION["access_token"] = $tokenData["access_token"];

header("Location: ../views/consultationListS.php");
exit;
