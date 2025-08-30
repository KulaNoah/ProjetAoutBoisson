<?php
try {
    // Paramètres de connexion à PostgreSQL
    $host = 'localhost';
    $port = '5432';
    $dbname = 'projet_boissons';
    $user = 'postgres';
    $password = 'admin'; 

    // Connexion avec PDO
    $db = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);

    // Activer les erreurs en mode exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo 'Connexion échouée : ' . $e->getMessage();
    exit;
}
?>
