<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once 'vendor/autoload.php';
require_once('config.php');

// oauth
// Inicializacia Google API klienta
$client = new Google\Client();

// Definica konfiguracneho JSON suboru pre autentifikaciu klienta.
// Subor sa stiahne z Google Cloud Console v zalozke Credentials.
$client->setAuthConfig('client_secret.json');

// Nastavenie URI, na ktoru Google server presmeruje poziadavku po uspesnej autentifikacii.
$redirect_uri = "https://site48.webte.fei.stuba.sk/webte2_zadanie1/redirect.php";
$client->setRedirectUri($redirect_uri);

// Definovanie Scopes - rozsah dat, ktore pozadujeme od pouzivatela z jeho Google uctu.
$client->addScope("email");
$client->addScope("profile");

// Vytvorenie URL pre autentifikaciu na Google server - odkaz na Google prihlasenie.
$auth_url = $client->createAuthUrl();




try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT osoby.id, osoby.name,osoby.surname, umiestnenia.placing, olympijskehry.type, olympijskehry.year, olympijskehry.city, umiestnenia.discipline
    FROM osoby
    JOIN umiestnenia ON osoby.id = umiestnenia.person_id
    JOIN olympijskehry ON umiestnenia.games_id = olympijskehry.id
    WHERE umiestnenia.placing = 1
    ORDER BY osoby.name";
    $stmt = $db->query($query);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $err) {
    echo $err->getMessage();
}


?>

<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">



    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@1.*/css/pico.min.css">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="style.css">

    <title>Olympiada</title>
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

    <div class="d-flex justify-content-center align-items-center">
        <div class="container-md">
            <div class="col-12 bg-clip-content text-black text-center">
                <h1>Naši olympionici</h1>
            </div>
            <table id="example" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <td>Meno</td>
                        <td>Priezvisko</td>
                        <td>Umiestnenie</td>
                        <td>Typ</td>
                        <td>Rok</td>
                        <td>Mesto</td>
                        <td>Disciplína</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($results as $result) {
                        echo "<tr>";
                        echo "<td><a href='detail.php?id={$result['id']}'>{$result['name']}</a></td>";
                        echo "<td>" . $result['surname'] . "</td>";
                        echo "<td>" . $result['placing'] . "</td>";
                        echo "<td>" . $result['type'] . "</td>";
                        echo "<td>" . $result['year'] . "</td>";
                        echo "<td>" . $result['city'] . "</td>";
                        echo "<td>" . $result['discipline'] . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <main>

        <?php



        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            // Neprihlaseny pouzivatel, zobraz odkaz na Login alebo Register stranku.
            echo '<p>Nie ste prihlásený, prosím <a href="login.php">prihláste sa</a> alebo sa <a href="register.php">zaregistrujte</a>.</p>';
        } else {
            // Prihlaseny pouzivatel, zobraz odkaz na zabezpecenu stranku.
            echo '<h3>Vitaj ' . $_SESSION['fullname'] . ' </h3>';
            echo '<a href="admin.php">Zabezpečená stránka</a>';
            echo '<a href="logout.php">Odhlasit sa</a>';
        }

        ?>

        <!-- oauth -->
        <?php
        // Ak som prihlaseny, existuje session premenna.
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            // Vypis relevantne info a uvitaciu spravu.
            echo '<h3>Vitaj ' . $_SESSION['name'] . '</h3>';
            echo '<p>Si prihlasený ako: ' . $_SESSION['email'] . '</p>';
            echo '<p><a role="button" href="restricted.php">Zabezpečená stránka</a>';
            echo '<a role="button" class="secondary" href="logout.php">Odhlás ma</a></p>';
        } else {
            // Ak nie som prihlaseny, zobraz mi tlacidlo na prihlasenie.
            echo '<h3>Nie si prihlásený</h3>';
            echo '<a role="button" href="' . filter_var($auth_url, FILTER_SANITIZE_URL) . '">Google prihlásenie</a>';
        }
        ?>


    </main>

    <div class="row">
        <div class="col-12 bg-clip-content text-white text-center bg-primary">
            <footer class="px-2">
                Michal Belan, &copy; 2023
            </footer>
        </div>

    </div>


    <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.3/js/dataTables.bootstrap5.min.js"></script>
    <script src="script.js"></script>
</body>

</html>