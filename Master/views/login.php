        <?php


// Login Page
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    //Příprava na budoucí rozšíření o přihlašování pomocí hesla

    
        $error = "Invalid login credentials.";
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/styleLogin.css">

</head>
<body>

    
    <main class="login-screen">
        <div class="logo-login"><img src="images/Logo_Students.png" alt=""></div>
        <div class="login-form">
        <h1>Přihlásit se pomocí:</h1>
        <div class="logo-microsoft"><a href="../actions/login2.php"><img src="images/Logo_microsoft.jpg" alt=""></a></div>
        </div>

    </main>
</body>
</html>