<?php
// Démarrer la session si nécessaire
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../src/php/db/db_pg_connect.php";

// Sécurité : accès admin uniquement
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php?page=login");
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = trim($_POST['nom']);
    $marque = trim($_POST['marque']);
    $prix = trim($_POST['prix']);
    $quantite = trim($_POST['quantite']);

    // Gestion de l'image (optionnelle)
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = __DIR__ . "/../uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $filename = basename($_FILES['image']['name']);
        $imagePath = $targetDir . $filename;
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
        $imagePath = "uploads/" . $filename; // chemin relatif pour la BDD
    }

    if ($nom && $marque && $prix && $quantite !== '') {
        try {
            $stmt = $db->prepare("INSERT INTO boissons (nom, marque, prix, quantite, image) VALUES (:nom, :marque, :prix, :quantite, :image)");
            $stmt->execute([
                ':nom' => $nom,
                ':marque' => $marque,
                ':prix' => $prix,
                ':quantite' => $quantite,
                ':image' => $imagePath
            ]);

            header("Location: index.php?page=dashboard&msg=Boisson+ajoutée+avec+succès");
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
    <title>Ajouter une boisson</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Ajouter une boisson</h1>

    <?php if ($message): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom de la boisson</label>
            <input type="text" name="nom" id="nom" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="marque" class="form-label">Marque</label>
            <input type="text" name="marque" id="marque" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="prix" class="form-label">Prix (€)</label>
            <input type="number" step="0.01" name="prix" id="prix" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="quantite" class="form-label">Quantité</label>
            <input type="number" name="quantite" id="quantite" class="form-control" value="0" required>
        </div>

        <div class="mb-3">
    <label for="image" class="form-label">Image (optionnelle)</label>
    <input type="file" name="image" id="image" class="form-control" accept="image/*">
    <img id="preview" style="max-width:150px; margin-top:10px; display:none;" />
</div>


        <button type="submit" class="btn btn-success">Ajouter</button>
        <a href="index.php?page=dashboard" class="btn btn-secondary">Retour</a>
    </form>
</div>
<script src="assets/js/image-preview.js"></script>


</body>
</html>
