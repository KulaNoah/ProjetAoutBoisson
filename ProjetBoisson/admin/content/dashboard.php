<?php
require_once __DIR__ . "/../src/php/db/db_pg_connect.php";

// Sécurité : accès admin uniquement
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php?page=login");
    exit();
}

// Récupérer toutes les boissons
$stmt = $db->query("SELECT * FROM boissons ORDER BY id DESC");
$boissons = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Boissons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php?page=dashboard">Admin Dashboard</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php?page=dashboard">Accueil</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php?page=add">Ajouter une boisson</a></li>
        <li class="nav-item"><a class="nav-link" href="../index.php?page=logout">Déconnexion</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
    <h1 class="mb-4">Gestion des boissons</h1>

    <!-- Message d'alerte si nécessaire -->
    <?php if (!empty($_GET['msg'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <a href="index.php?page=add" class="btn btn-success mb-3">➕ Ajouter une boisson</a>

    <table class="table table-striped table-bordered table-hover align-middle">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prix</th>
            <th>Quantité</th> <!-- Nouvelle colonne -->
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($boissons as $b): ?>
            <tr>
                <td><?= htmlspecialchars($b['id']) ?></td>
                <td><?= htmlspecialchars($b['nom']) ?></td>
                <td><?= htmlspecialchars($b['prix']) ?> €</td>
                <td><?= htmlspecialchars($b['quantite']) ?></td> <!-- Affichage de la quantité -->
                <td>
                    <a href="index.php?page=edit&id=<?= $b['id'] ?>" class="btn btn-sm btn-primary">✏ Modifier</a>
                    <a href="index.php?page=delete&id=<?= $b['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette boisson ?')">🗑 Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</div>

</body>
</html>
