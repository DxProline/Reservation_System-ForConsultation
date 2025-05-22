<?php
session_start();
require_once '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$tenantId = $_ENV['TENANT_ID'];           // ID tenanta
$clientId = $_ENV['CLIENT_ID'];          // ID aplikace (client_id)
$redirectUri = $_ENV['REDIRECT_URI'];

$scope = "https://graph.microsoft.com/User.Read";
$authUrl = "https://login.microsoftonline.com/$tenantId/oauth2/v2.0/authorize?" . http_build_query([    
    "client_id" => $clientId,
    "response_type" => "code",
    "redirect_uri" => $redirectUri,
    "scope" => $scope,
    "response_mode" => "query"
]);

header("Location: $authUrl");
exit;
?>