<?php

require_once __DIR__ . "/../src/php/db/db_pg_connect.php";

// Sécurité : accès admin uniquement
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php?page=login");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php?page=dashboard");
    exit();
}

// Récupérer la boisson
$stmt = $db->prepare("SELECT * FROM boissons WHERE id = :id");
$stmt->execute([':id' => $id]);
$boisson = $stmt->fetch();

if (!$boisson) {
    header("Location: index.php?page=dashboard");
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = trim($_POST['nom']);
    $prix = trim($_POST['prix']);

    if ($nom && $prix) {
        try {
            $stmt = $db->prepare("UPDATE boissons SET nom = :nom, prix = :prix WHERE id = :id");
            $stmt->execute([':nom' => $nom, ':prix' => $prix, ':id' => $id]);

            header("Location: index.php?page=dashboard&msg=Boisson+modifiée+avec+succès");
            exit();
        } catch (PDOException $e) {
            $message = "Erreur : " . $e->getMessage();
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une boisson</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Modifier la boisson</h1>

    <?php if ($message): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom de la boisson</label>
            <input type="text" name="nom" id="nom" class="form-control" value="<?= htmlspecialchars($boisson['nom']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="prix" class="form-label">Prix (€)</label>
            <input type="number" step="0.01" name="prix" id="prix" class="form-control" value="<?= htmlspecialchars($boisson['prix']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="index.php?page=dashboard" class="btn btn-secondary">Annuler</a>
    </form>
</div>

</body>
</html>
