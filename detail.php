<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once('config.php');



try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } else {

        header('Location: index.php');
        exit();
    }

    $query = "SELECT umiestnenia.placing, olympijskehry.year, olympijskehry.city,olympijskehry.type ,olympijskehry.country, umiestnenia.discipline
    FROM umiestnenia
    JOIN olympijskehry ON umiestnenia.games_id = olympijskehry.id
    WHERE umiestnenia.person_id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll();

    $query = "SELECT name, surname, birth_day, birth_place, birth_country, death_day, death_place, death_country
          FROM osoby
          WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $osoba = $stmt->fetch();


    if ($osoba) {
        // echo "<h1>{$osoba['name']} {$osoba['surname']}</h1>";
    } else {
        echo "<p>Osoba s daným ID neexistuje.</p>";
    }
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Detail športovca</title>
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
    <div class="container-md">
        <?php

        echo "<h1>{$osoba['name']} {$osoba['surname']}</h1>";

        ?>
        <table id="table" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <td>Miesto</td>
                    <td>Rok</td>
                    <td>Typ</td>
                    <td>Disciplína</td>
                    <td>Umiestnenie</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($results as $result) {
                    echo "<tr>";
                    echo "<td>" . $result['city'] . "</td>";
                    echo "<td>" . $result['year'] . "</td>";
                    echo "<td>" . $result['type'] . "</td>";
                    echo "<td>" . $result['discipline'] . "</td>";
                    echo "<td>" . $result['placing'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.3/js/dataTables.bootstrap5.min.js"></script>
    <script src="script.js"></script>
</body>

</html>