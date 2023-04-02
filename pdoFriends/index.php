

<?php
require('./config/connec.php');

$pdo = new PDO(DSN, USER, PASS);

// Récupération des amis
$query = "SELECT * FROM friend";
$statement = $pdo->query($query);
$friends = $statement->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire
$errors = [];
$success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST["firstname"]);
    $lastname = trim($_POST["lastname"]);

    // Validation des données du formulaire
    if (empty($firstname) || strlen($firstname) > 45) {
        $errors[] = "Le prénom doit comporter entre 1 et 45 caractères.";
    }
    if (empty($lastname) || strlen($lastname) > 45) {
        $errors[] = "Le nom doit comporter entre 1 et 45 caractères.";
    }

    // Si les données sont valides, on insère un nouvel ami
    if (empty($errors)) {
        $query = "INSERT INTO friend (firstname, lastname) VALUES (:firstname, :lastname)";
        $statement = $pdo->prepare($query);
        $statement->bindValue(":firstname", $firstname, PDO::PARAM_STR);
        $statement->bindValue(":lastname", $lastname, PDO::PARAM_STR);
        $success = $statement->execute();
        if (!$success) {
            $errors[] = "Une erreur est survenue lors de l'ajout de l'ami.";
        } else {
            header("Location: index.php");
            exit;
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Friends</title>
</head>
<body>
    <h1>My Friends</h1>
    <?php if (!empty($friends)): ?>
        <ul>
            <?php foreach ($friends as $friend): ?>
                <li><?= $friend['firstname'] ?> <?= $friend['lastname'] ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun ami pour le moment.</p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p>Ami ajouté avec succès !</p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h2>Ajouter un ami</h2>
    <form method="post">
    
        <div>
            <label for="firstname">Prénom : </label>
            <input type="text" id="firstname" name="firstname" required maxlength="45">
        </div>
        <div>
            <label for="lastname">Nom : </label>
            <input type="text" id="lastname" name="lastname" required maxlength="45">
        </div>
        <button type="submit">Ajouter</button>
    </form>
</body>
</html>