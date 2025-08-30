<?php
require_once __DIR__ . '/../admin/src/php/db/db_pg_connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // On stocke toutes les infos utiles en session
                $_SESSION['user'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role']; // <-- nouveau

                // Redirection selon le rôle
                if ($user['role'] === 'admin') {
                    header('Location: admin/index.php?page=dashboard');
                } else {
                    header('Location: index.php?page=accueil');
                }
                exit;
            } else {
                $message = "Identifiants incorrects.";
            }
        } catch (PDOException $e) {
            $message = "Erreur : " . $e->getMessage();
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
?>

<h2>Connexion</h2> 

<?php if ($message): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label for="username" class="form-label">Nom d'utilisateur</label>
        <input type="text" name="username" id="username" class="form-control" required>
    </div>
    
    <div class="mb-3">
        <label for="password" class="form-label">Mot de passe</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>
    
    <button type="submit" class="btn btn-primary">Se connecter</button>
</form>

<p>Pas encore de compte ? <a href="index.php?page=register">Inscription</a></p>
