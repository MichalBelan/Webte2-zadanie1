<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once('config.php');

if ((isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) || (isset($_SESSION['access_token']) && $_SESSION['access_token'])) {}
else {
    header("Location: login.php");
    exit;
}

if (isset($_POST['akcia']) && $_POST['akcia'] == 'vyhladaj_sportovca') {
    exit(header("Location: editPerson.php?id={$_POST['person_id']}"));
}

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!empty($_POST) && !empty($_POST['name'])) {
        // var_dump($_POST);
        $sql = "INSERT INTO osoby(name, surname, birth_day, birth_place, birth_country) VALUES(?,?,?,?,?)";
        $stmt = $db->prepare($sql);
        $sucess = $stmt->execute([$_POST["name"], $_POST["surname"], $_POST["birth_day"], $_POST["birth_place"], $_POST["birth_country"]]);
        echo "<script>alert('Úspešne pridanie sportovca');</script>";
    }

    $query = "SELECT * FROM osoby";
    $stmt = $db->query($query);
    $persons = $stmt->fetchAll(PDO::FETCH_ASSOC);

   
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
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
        <h1>Admin panel</h1>
        <h2>Pridaj športovca</h2>
        <form action="#" method="post">
            <div class="mb-3">
                <label for="InputName" class="form-label">Meno:</label>
                <input type="text" name="name" class="form-control" id="InputName" maxlength="32" required>
            </div>
            <div class="mb-3">
                <label for="InputSurname" class="form-label">Priezvisko:</label>
                <input type="text" name="surname" class="form-control" id="InputSurname" maxlength="32" required>
            </div>
            <div class="mb-3">
                <label for="InputDate" class="form-label">Dátum narodenia:</label>
                <input type="date" name="birth_day" class="form-control" id="InputDate" maxlength="32" required>
            </div>
            <div class="mb-3">
                <label for="InputbrPlace" class="form-label">Miesto narodenia:</label>
                <input type="text" name="birth_place" class="form-control" id="InputBrPlace" maxlength="32" required>
            </div>
            <div class="mb-3">
                <label for="InputBrCountry" class="form-label">Krajina narodenia:</label>
                <input type="text" name="birth_country" class="form-control" id="InputBrCountry" maxlength="32" required>
            </div>
            <button type="submit" class="btn btn-primary">Odošli</button><br>
        </form>

        <h3 class="pb-3">Vyhľadaj športovca</h3>
        <form action="#" method="post">

            <div class="row mb-3">
                <div class="col-6">
                    <select name="person_id">
                        <?php
                        foreach ($persons as $osoby) {
                            echo '<option value="' . $osoby['id'] . '">' . $osoby['name'] . ' ' . $osoby['surname'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-6 d-grid">
                    <button type="submit" name="akcia" value="vyhladaj_sportovca" class="btn btn-primary">Vyhľadaj športovca</button>
                    
                </div>
            </div>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <td>Meno</td>
                    <td>Priezvisko</td>
                    <td>Narodenie</td>
                </tr>
            </thead>
            <tbody>
                <?php //var_dump($results) 
                foreach ($persons as $osoby) {
                    $date = new DateTimeImmutable($osoby["birth_day"]);
                    echo "<tr><td><a href='editPerson.php?id=" .  $osoby["id"] . "'>" . $osoby["name"] . "</a></td><td>" . $osoby["surname"] . "</td><td>" . $date->format("d.m.Y") . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>