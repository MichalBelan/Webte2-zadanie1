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

    if (!empty($_POST)) {
        // var_dump($_POST);
        $sql = "UPDATE umiestnenia SET games_id=?, placing=?, discipline=? where id=?";
        $stmt = $db->prepare($sql);
        $success = $stmt->execute([$_POST['games_id'], $_POST['placing'], $_POST['discipline'], intval($_GET['id'])]);
    }

    $query = "select * from olympijskehry";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $query = "select * from umiestnenia where id=?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['id']]);
    $placements = $stmt->fetch();
} catch (PDOException $err) {
    echo $err->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <title>Edit</title>
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
        <form action="#" method="post">
            <div class="mb-3">
                <label for="InputPlacing" class="form-label">Umiestnenie:</label>
                <input type="number" name="placing" class="form-control" id="InputPlacing" maxlength="32" value="<?php echo $placements['placing']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="InputDiscipline" class="form-label">Disciplína:</label>
                <input type="text" name="discipline" class="form-control" id="InputDiscipline" maxlength="32" value="<?php echo $placements['discipline']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="InputOH" class="form-label">OH:</label>


                <select name="games_id" class="form-select" require>;

                    <?php
                    foreach ($games as $game) {
                        if ($game['id'] == $placements['games_id']) {
                            echo '<option selected value="' . $game['id'] . '">' . $game['city'] . '</option>';
                        } else {
                            echo '<option value="' . $game['id'] . '">' . $game['city'] . '</option>';
                        }
                    }

                    ?>

                </select>
            </div>

            <button type="submit" class="btn btn-primary">Upraviť</button>
        </form>


    </div>
</body>

</html>