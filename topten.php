<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();


require_once('config.php');

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $query = "SELECT o.name, o.surname, COUNT(u.placing) AS num_gold_medals
    FROM osoby o
    INNER JOIN umiestnenia u ON o.id = u.person_id
    WHERE u.placing = 1
    GROUP BY o.id
    ORDER BY num_gold_medals DESC
    LIMIT 10";
    $stmt = $db->query($query);
    $golden_medals = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Topten</title>
</head>

<body>

<div class="row">
        <div class="col-12 bg-clip-content text-black text-center">
            <header>
                <h1>Olympíjske hry</h1>
            </header>
        </div>
    </div>

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

    <div class="container-md">
        <h1>10 najúspešnejších olympionikov</h1>
        <table id="top10" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <td>Meno</td>
                    <td>Priezvisko</td>
                    <td>Počet zlatých medailií</td>

                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($golden_medals as $medal) {
                    echo "<tr>";
                    echo "<td>" . $medal['name'] . "</td>";
                    echo "<td>" . $medal['surname'] . "</td>";
                    echo "<td>" . $medal['num_gold_medals'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

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