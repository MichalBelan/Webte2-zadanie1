<?php

session_start();

// Uvolnenie session premennych. Tieto dva prikazy su ekvivalentne.
$_SESSION = array();
session_unset();

// Vymazanie session.
session_destroy();

// Presmerovanie na hlavnu stranku.
header("location: index.php");
exit;

?>

<!-- oauth -->
<!doctype html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@1.*/css/pico.min.css">
    <title>OAuth2 cez Google</title>
</head>
<body>
    <header>
        <h1>Boli ste úspešne odhlásený</h1>
    </header>
    <main>
        <a role="button" href="index.php" class="secondary">Vrátiť sa na hlavnú stránku</a>
    </main>
</body>
</html>

