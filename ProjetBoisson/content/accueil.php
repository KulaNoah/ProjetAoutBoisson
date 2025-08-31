<?php
require_once __DIR__ . '/../admin/src/php/db/db_pg_connect.php';
require_once __DIR__ . '/../admin/src/php/classes/Boisson.class.php';
require_once __DIR__ . '/../admin/src/php/classes/BoissonDAO.class.php';

$dao = new BoissonDAO($db);

// --- Récupération des boissons (avec filtrage) ---
if (!empty($_GET['marque'])) {
    $marque = $_GET['marque'];
    $boissons = $dao->getByMarque($marque);
} else {
    $boissons = $dao->getAll();
}

// --- Récupération de l'historique des commandes ---
$commandes = [];
if (isset($_SESSION['user_id'])) {
    $commandeStmt = $db->prepare("SELECT * FROM commandes WHERE user_id = :user_id ORDER BY date_commande DESC");
    $commandeStmt->execute([':user_id' => $_SESSION['user_id']]);
    $commandes = $commandeStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nos Boissons</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #ffe259, #ffa751);
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
}

h2, h3 {
    color: #fff;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
}

.card {
    border-radius: 15px;
    transition: transform 0.3s, box-shadow 0.3s;
    background: #fff;
}

.card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}

.card-title {
    color: #ff6f61;
    font-weight: 600;
}

.card-text.text-muted {
    color: #6c757d !important;
}

.btn-success {
    background-color: #28c76f;
    border: none;
    transition: background-color 0.3s;
}

.btn-success:hover {
    background-color: #20b358;
}

.btn-outline-primary {
    border-color: #ff6f61;
    color: #ff6f61;
}

.btn-outline-primary:hover {
    background-color: #ff6f61;
    color: #fff;
}

.form-select {
    border-radius: 50px;
}

.list-group-item {
    border-radius: 10px;
    margin-bottom: 10px;
    background: rgba(255,255,255,0.85);
}
</style>
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center mb-4">Nos Boissons</h2>

    <!-- Formulaire de filtrage -->
    <form method="get" action="index.php" class="mb-4">
        <input type="hidden" name="page" value="accueil">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <select name="marque" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Toutes les marques --</option>
                    <?php
                    $stmt = $db->query("SELECT DISTINCT marque FROM boissons ORDER BY marque ASC");
                    $marques = $stmt->fetchAll(PDO::FETCH_COLUMN);

                    foreach ($marques as $m): 
                        $selected = (isset($_GET['marque']) && $_GET['marque'] === $m) ? "selected" : "";
                    ?>
                        <option value="<?= htmlspecialchars($m) ?>" <?= $selected ?>>
                            <?= htmlspecialchars($m) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>

    <!-- Liste des boissons -->
    <div class="row g-4">
        <?php foreach ($boissons as $boisson): ?>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <img src="admin/assets/images/<?= htmlspecialchars($boisson->getImage()) ?>" 
                         class="card-img-top" 
                         alt="<?= htmlspecialchars($boisson->getNom()) ?>" 
                         style="height: 200px; object-fit: cover;">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?= htmlspecialchars($boisson->getNom()) ?></h5>
                        <p class="card-text text-muted"><?= htmlspecialchars($boisson->getMarque()) ?></p>
                        <p class="fw-bold text-primary"><?= number_format($boisson->getPrix(), 2) ?> €</p>
                        <p>Stock : <?= htmlspecialchars($boisson->getQuantite()) ?></p>
                        
                        <?php if (isset($_SESSION['user'])): ?>
                            <form method="post" action="index.php?page=accueil">
                                <input type="hidden" name="id" value="<?= $boisson->getId() ?>">
                                <input type="hidden" name="nom" value="<?= $boisson->getNom() ?>">
                                <input type="hidden" name="prix" value="<?= $boisson->getPrix() ?>">
                                <input type="hidden" name="image" value="<?= $boisson->getImage() ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-success w-100">
                                    Ajouter au panier
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="index.php?page=login" class="btn btn-outline-primary w-100">
                                Connectez-vous pour acheter
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Section Commandes -->
    <h3 class="text-center mt-5">Vos Commandes</h3>
    <?php if (!empty($commandes)): ?>
        <div class="list-group">
            <?php foreach ($commandes as $commande): ?>
                <div class="list-group-item">
                    <h5 class="mb-1">
                        Commande #<?= htmlspecialchars($commande['id']) ?> 
                        - <?= htmlspecialchars($commande['date_commande']) ?>
                    </h5>
                    <p><strong>Total :</strong> <?= number_format($commande['total'], 2) ?> €</p>
                    <ul>
                        <?php
                        $detailsStmt = $db->prepare("
                            SELECT cd.quantite, b.nom, cd.prix
                            FROM commande_details cd
                            JOIN boissons b ON cd.boisson_id = b.id
                            WHERE cd.commande_id = :commande_id
                        ");
                        $detailsStmt->execute([':commande_id' => $commande['id']]);
                        $details = $detailsStmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($details as $detail):
                        ?>
                            <li>
                                <?= htmlspecialchars($detail['quantite']) ?> x 
                                <?= htmlspecialchars($detail['nom']) ?> - 
                                <?= number_format($detail['prix'], 2) ?> € chacune
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-white">Aucune commande passée pour le moment.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
