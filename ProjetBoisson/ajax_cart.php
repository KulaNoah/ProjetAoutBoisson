<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

$index = isset($_POST['index']) ? intval($_POST['index']) : null;
$action = $_POST['action'] ?? null;

if ($index !== null && isset($_SESSION['cart'][$index])) {
    switch ($action) {
        case 'increase':
            $_SESSION['cart'][$index]['quantite']++;
            break;
        case 'decrease':
            $_SESSION['cart'][$index]['quantite']--;
            if ($_SESSION['cart'][$index]['quantite'] < 1) {
                $_SESSION['cart'][$index]['quantite'] = 1;
            }
            break;
    }
}

// Recalcul du total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['prix'] * $item['quantite'];
}

echo json_encode([
    'cart' => $_SESSION['cart'],
    'total' => number_format($total, 2)
]);
