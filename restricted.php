<?php

session_start();

// oauth

// Ak je pouzivatel prihlaseny, ziskam data zo session, pracujem s DB etc...
if ((isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) || (isset($_SESSION['access_token']) && $_SESSION['access_token'])) {

    $email = $_SESSION['email'];
    $id = $_SESSION['id'];
    $fullname = $_SESSION['fullname'];
    $name = $_SESSION['name'];
    $surname = $_SESSION['surname'];

} else {
    // Ak pouzivatel prihlaseny nie je, presmerujem ho na hl. stranku.
    header('Location: login.php');
}

// TODO: Poskytnut pouzivatelovi docasne deaktivovat 2FA.
// TODO: Poskytnut pouzivatelovi moznost resetovania hesla.

?>

<!doctype html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@1.*/css/pico.min.css">
    <title>Login/register s 2FA - Zabezpečená stránka</title>

    
</head>
<body>
<header>
    <hgroup>
        <h1>Zabezpečená stránka</h1>
        <h2>Obsah tu je dostupný iba po prihlásení.</h2>
    </hgroup>
</header>
<main>

    <h3>Vitaj <?php echo $_SESSION['fullname']; ?></h3>
    <p><strong>Si prihlásený pod emailom:</strong> <?php echo $_SESSION['email']; ?></p>
    <p><strong>Tvoj identifikátor (login) je:</strong> <?php echo $_SESSION['login']; ?></p>
    <p><strong>Dátum registracie/vytvonia konta:</strong> <?php echo $_SESSION['created_at'] ?></p>

    <a href="logout.php">Odhlásenie</a></p><br>
    <a href="admin.php">Admin</a></p><br>
    <a href="index.php">Späť na hlavnú stránku</a></p>

     <!-- oauth -->
    <h3>Vitaj <?php echo $fullname ?></h3>
    <p>Si prihlásený pod emailom: <?php echo $email?></p>
    <p>Tvoj identifikátor je: <?php echo $id?></p>
    <p>Meno: <?php echo $name?>, Priezvisko: <?php echo $surname?></p>

    <a role="button" class="secondary" href="logout.php">Odhlásenie</a></p>
    <a role="button" href="index.php">Späť na hlavnú stránku</a></p>




</main>
</body>
</html>

