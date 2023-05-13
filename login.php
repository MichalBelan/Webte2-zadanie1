<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();


// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: restricted.php");
    exit;
}

require_once('config.php');
require_once 'PHPGangsta/GoogleAuthenticator.php';

$pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // TODO: Skontrolovat ci login a password su zadane (podobne ako v register.php).

    $sql = "SELECT fullname, email, login, password, created_at, 2fa_code FROM users WHERE login = :login";

    $stmt = $pdo->prepare($sql);

    // TODO: Upravit SQL tak, aby mohol pouzivatel pri logine zadat login aj email.
    $stmt->bindParam(":login", $_POST["login"], PDO::PARAM_STR);

    if ($stmt->execute()) {
        if ($stmt->rowCount() == 1) {
            // Uzivatel existuje, skontroluj heslo.
            $row = $stmt->fetch();
            $hashed_password = $row["password"];

            if (password_verify($_POST['password'], $hashed_password)) {
                // Heslo je spravne.
                $g2fa = new PHPGangsta_GoogleAuthenticator();
                if ($g2fa->verifyCode($row["2fa_code"], $_POST['2fa'], 2)) {
                    // Heslo aj kod su spravne, pouzivatel autentifikovany.

                    // Uloz data pouzivatela do session.
                    $_SESSION["loggedin"] = true;
                    $_SESSION["login"] = $row['login'];
                    $_SESSION["fullname"] = $row['fullname'];
                    $_SESSION["email"] = $row['email'];
                    $_SESSION["created_at"] = $row['created_at'];

                    $sql = "INSERT INTO login_s (email, login, source) VALUES(?,?,?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$_SESSION["email"], $_SESSION["login"], "2FA"]);
                    
                    header("location: restricted.php");
                } else {
                    echo "Neplatný kód 2FA.";
                }
            } else {
                echo "Nesprávne meno alebo heslo.";
            }
        } else {
            echo "Nesprávne meno alebo heslo.";
        }
    } else {
        echo "Ups. Niečo sa pokazilo!";
    }

    unset($stmt);
    unset($pdo);
}

?>

<!doctype html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <title>Login/register s 2FA - Login</title>

</head>

<body>
<div class="row">
        <div class="col-12 bg-clip-content text-black text-center">
            <header>
                <h1>Olympíjske hry</h1>
            </header>
        </div>
    </div>

    <div class="row">
        <nav class="col-12 bg-clip-content text-center bg-primary">
            <ul>
                <li>
                    <a href="index.php">Naši olympionici</a>
                </li>
                <li>
                    <a href="topten.php">Top 10 najlepších olympionikov</a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-12 bg-clip-content text-black text-center">
            <header>
                <hgroup>
                    <h1>Prihlásenie</h1>
                    <h2>Prihlásenie používateľa po registrácii</h2>
                </hgroup>
            </header>

        </div>
    </div>



    <div class="row">
        <div class="col-12 bg-clip-content text-black text-center">
            <div class="container-md">
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">

                    <label for="login">
                        Prihlasovacie meno:
                        <input type="text" name="login" value="" id="login" maxlength="32" required>
                    </label>
                    <br>
                    <label for="password">
                        Heslo:
                        <input type="password" name="password" value="" id="password" maxlength="32" required>
                    </label>
                    <br>
                    <label for="2fa">
                        2FA kód:
                        <input type="number" name="2fa" value="" id="2fa" maxlength="32" required>
                    </label>

                    <button type="submit">Prihlásiť sa</button>
                </form>
                <p>Ešte nemáte vytvorené konto? <a href="register.php">Registrujte sa tu.</a></p>
            </div>
        </div>
    </div>


</body>

</html>