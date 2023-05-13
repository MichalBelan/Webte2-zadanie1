<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('config.php');


if (!isset($_GET['id'])) {
    exit("id not exist");
}

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['akcia']) && $_POST['akcia'] == 'vymaz') {
        $sql = "DELETE FROM osoby WHERE id = ?";
        $stmt = $db->prepare($sql);
        if ($stmt->execute([$_GET['id']]))
            exit(header("Location: admin.php"));
        else
            echo "CHYBA";
    }

    if (isset($_POST['akcia']) && $_POST['akcia'] == 'pridaj_umiestnenie') {
        $sql = "INSERT INTO umiestnenia (person_id, games_id, placing, discipline) VALUES (?,?,?,?)";
        $stmt = $db->prepare($sql);
        $pridajum = $stmt->execute([$_GET['id'], $_POST['games_id'], $_POST['placing'], $_POST['discipline']]);
        echo "<script>alert('Úspešne pridané umiestnenie');</script>";
    }


    if (!empty($_POST) && !empty($_POST['name'])) {
        // var_dump($_POST);
        $sql = "UPDATE osoby SET name=?, surname=?, birth_day=?, birth_place=?, birth_country=? where id=?";
        $stmt = $db->prepare($sql);
        $success = $stmt->execute([$_POST['name'], $_POST['surname'], $_POST['birth_day'], $_POST['birth_place'], $_POST['birth_country'], intval($_POST['person_id'])]);
    }

    // $query = "DELETE FROM osoby WHERE id=?";
    // $stmt = $db->prepare($query);
    // $stmt->execute([$_GET['id']]);

    // // presmerovanie používateľa na zoznam osôb po vymazaní
    // header("Location: admin.php");
    

    $query = "SELECT * FROM osoby where id=?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['id']]);
    $person = $stmt->fetch(PDO::FETCH_ASSOC);

    $query = "SELECT * FROM olympijskehry";
    $stmt = $db->query($query);
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_POST['del_placement_id'])) {
        $sql = "DELETE FROM umiestnenia WHERE id=?";
        $stmt = $db->prepare($sql);
        $stmt->execute([intval($_POST['del_placement_id'])]);
    }

    $query = "select umiestnenia.*, olympijskehry.city from umiestnenia join olympijskehry on umiestnenia.games_id = olympijskehry.id where umiestnenia.person_id=?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['id']]);
    $placements = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
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
        
        <h2>Info o športovcovi</h2>
        <form action="#" method="post">
            <input type="hidden" name="person_id" value="<?php echo $person['id']; ?>">
            <div class="mb-3">
                <label for="InputName" class="form-label">Meno:</label>
                <input type="text" name="name" class="form-control" id="InputName" maxlength="32" value="<?php echo $person['name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="InputSurname" class="form-label">Priezvisko:</label>
                <input type="text" name="surname" class="form-control" id="InputSurname" maxlength="32" value="<?php echo $person['surname']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="InputDate" class="form-label">Dátum narodenia:</label>
                <input type="date" name="birth_day" class="form-control" id="InputDate" maxlength="32" value="<?php echo $person['birth_day']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="InputbrPlace" class="form-label">Miesto narodenia:</label>
                <input type="text" name="birth_place" class="form-control" id="InputBrPlace" maxlength="32" value="<?php echo $person['birth_place']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="InputBrCountry" class="form-label">Krajina narodenia:</label>
                <input type="text" name="birth_country" class="form-control" id="InputBrCountry" maxlength="32" value="<?php echo $person['birth_country']; ?>" required>
            </div>

            <div class="row mb-3">
                <div class="col-12 col-sm-4 mb-3 mb-sm-0">
                    <button type="submit" class="btn btn-primary">Upraviť</button>
                </div>

                <div class="col-12 col-sm-4 mb-3 mb-sm-0">
                    <button type="submit" name="akcia" value="vymaz" class="btn btn-danger">Vymazať športovca</button>
                </div>

            </div>
        </form>

        <h3 class="pb-3">Pridaj umiestnenia</h3>
        <form action="#" method="post">
            <div class="row mb-3">
                <div class="col-12 col-sm-4 mb-3 mb-sm-0">
                    <select name="games_id">
                        <?php
                        foreach ($games as $game) {
                            echo '<option value="' . $game['id'] . '">' . $game['city'] . ' ' . $game['type'] . ' ' . $game['year'] . '</option>';
                        }

                        ?>
                    </select>
                </div>

                <div class="col-12 col-sm-4 mb-3 mb-sm-0">
                    <label for="InputDiscipline">Disciplína:</label><br>
                    <input type="text" id="InputDiscipline" name="discipline" min="1" max="100">
                </div>

                <!-- <div class="mb-3">
                    <div id="InputDisciplineWarn" class="red"></div>
                </div> -->

                <div class="col-12 col-sm-4 mb-3 mb-sm-0">
                    <label for="InputPlace">Umiestenie:</label><br>
                    <input type="number" id="InputPlace" name="placing" min="1" max="100"><br><br><br>
                </div>

                <!-- <div id="InputPlaceWarn" class="red"></div> -->
            </div>

            <div class="row mb-3 pb-3 border-bottom">
                <div class="col-12 d-grid">
                    <button type="submit" name="akcia" value="pridaj_umiestnenie" class="btn btn-primary">Pridaj umiestnenie</button>
                </div>
            </div>

        </form>

        <h2>Umiestnenia</h2>
        <table class="table">
            <thead>
                <tr>
                    <td>Umiestnenie</td>
                    <td>disciplína</td>
                    <td>OH</td>
                    <td>Akcia</td>
                </tr>
            </thead>
            <tbody>
                <?php //var_dump($results) 
                foreach ($placements as $umiestnenia) {
                    //var_dump($placement);
                    echo '<tr><td>' . $umiestnenia['placing'] . '</td><td>' . $umiestnenia['discipline'] . '</td><td>' . $umiestnenia['city'] . '</td><td>';
                    echo '<form action="#" method="post"><input type="hidden" name="del_placement_id" value="' . $umiestnenia['id'] . '"><button type="submit" class="btn btn-danger">Vymaž</button></form>';
                    echo "<a href='./editPlacement.php?id={$umiestnenia['id']} role='button' class='btn btn-warning'>Upraviť</a>";
                    

                    echo '</td></tr>';
                }
                ?>
            </tbody>
        </table>



        </table>
    </div>
</body>

</html>