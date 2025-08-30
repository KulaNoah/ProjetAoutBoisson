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

// Vérifier si l'id est fourni
$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $stmt = $db->prepare("DELETE FROM boissons WHERE id = :id");
        $stmt->execute([':id' => $id]);

        // Succès : redirection avec message
        header("Location: index.php?page=dashboard&msg=Boisson+supprimée+avec+succès");
        exit();
    } catch (PDOException $e) {
        // Gestion des erreurs de clé étrangère
        if (strpos($e->getMessage(), 'Foreign key') !== false) {
            header("Location: index.php?page=dashboard&msg=Impossible+de+supprimer+la+boisson+car+elle+est+dans+une+commande");
            exit();
        } else {
            die("Erreur lors de la suppression : " . $e->getMessage());
        }
    }
} else {
    // Si aucun id fourni, on retourne au dashboard
    header("Location: index.php?page=dashboard");
    exit();
}
