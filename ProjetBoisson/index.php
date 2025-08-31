<?php
session_start();

if (isset($_POST['add_to_cart'])) {
    $item = [
        'id' => $_POST['id'],
        'nom' => $_POST['nom'],
        'prix' => $_POST['prix'],
        'image' => $_POST['image'],
        'quantite' => 1
    ];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Vérifier si l'article est déjà dans le panier
    $found = false;
    foreach ($_SESSION['cart'] as &$cartItem) {
        if ($cartItem['id'] == $item['id']) {
            $cartItem['quantite']++;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $_SESSION['cart'][] = $item;
    }
}

// Augmenter la quantité
if (isset($_POST['increase'])) {
    $index = $_POST['increase'];
    $_SESSION['cart'][$index]['quantite']++;
}

// Diminuer la quantité
if (isset($_POST['decrease'])) {
    $index = $_POST['decrease'];
    if ($_SESSION['cart'][$index]['quantite'] > 1) {
        $_SESSION['cart'][$index]['quantite']--;
    } else {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

// Supprimer un article
if (isset($_POST['remove'])) {
    $index = $_POST['remove'];
    unset($_SESSION['cart'][$index]);
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

// Vider le panier
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
}

// Passer la commande
if (isset($_POST['checkout'])) {
    if (!isset($_SESSION['user'])) {
        echo "<div class='alert alert-warning text-center'>Vous devez être connecté pour passer une commande.</div>";
    } elseif (empty($_SESSION['cart'])) {
        echo "<div class='alert alert-danger text-center'>Votre panier est vide.</div>";
    } else {
        try {
            $db = new PDO("pgsql:host=localhost;dbname=projet_boissons;port=5432", "postgres", "admin");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Calculer le total
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['prix'] * $item['quantite'];
            }

            // Insérer la commande
            $stmt = $db->prepare("INSERT INTO commandes (user_id, total, date_commande) VALUES (:user_id, :total, NOW()) RETURNING id");
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':total' => $total
            ]);
            $order_id = $stmt->fetchColumn();

            // Insérer les détails de commande et mettre à jour les quantités
            $stmtDetail = $db->prepare("INSERT INTO commande_details (commande_id, boisson_id, quantite, prix) VALUES (:commande_id, :boisson_id, :quantite, :prix)");
            $stmtStock  = $db->prepare("UPDATE boissons SET quantite = quantite - :qte WHERE id = :id");

            foreach ($_SESSION['cart'] as $item) {
                $stmtDetail->execute([
                    ':commande_id' => $order_id,
                    ':boisson_id'  => $item['id'],
                    ':quantite'    => $item['quantite'],
                    ':prix'        => $item['prix']
                ]);

                // Mettre à jour la quantité en base
                $stmtStock->execute([
                    ':qte' => $item['quantite'],
                    ':id'  => $item['id']
                ]);
            }

            // Vider le panier après la commande
            $_SESSION['cart'] = [];

            echo "<div class='alert alert-success text-center'>Commande passée avec succès !</div>";
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger text-center'>Erreur : " . $e->getMessage() . "</div>";
        }
    }
}

// Page par défaut
if (!isset($_SESSION['page'])) {
    $_SESSION['page'] = 'accueil.php';
}

// Si on change de page via GET
if (isset($_GET['page'])) {
    $_SESSION['page'] = $_GET['page'] . '.php';
}

// Définition du chemin complet du fichier à inclure
$path = __DIR__ . '/content/' . $_SESSION['page'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vente de Boissons</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php?page=accueil">🍹 Vente de Boissons</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=accueil">Accueil</a>
                    </li>

                    <?php if (!isset($_SESSION['user'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=login">Connexion</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=register">Inscription</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <span class="nav-link">Bienvenue, <?= htmlspecialchars($_SESSION['user']) ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="index.php?page=panier">
                                🛒 Panier
                                <?php
                                $count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                                if ($count > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <?= $count ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=logout">Déconnexion</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main class="container">
<?php
if (file_exists($path)) {
    include $path;
} else {
    include __DIR__ . '/content/page404.php';
}
?>
</main>
</body>
</html>
