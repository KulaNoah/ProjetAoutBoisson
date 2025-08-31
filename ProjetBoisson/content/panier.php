<?php

?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Votre Panier</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #ffe259, #ffa751);
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    padding-bottom: 50px;
}

h2 {
    color: #fff;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
}

.table {
    background: rgba(255,255,255,0.95);
    border-radius: 15px;
    overflow: hidden;
}

.table thead {
    background: #ff6f61;
    color: #fff;
}

.table-striped > tbody > tr:nth-of-type(odd) {
    background-color: rgba(255,111,97,0.1);
}

.btn-success {
    background-color: #28c76f;
    border: none;
    transition: background-color 0.3s;
}

.btn-success:hover {
    background-color: #20b358;
}

.btn-outline-danger {
    border-color: #ff6f61;
    color: #ff6f61;
}

.btn-outline-danger:hover {
    background-color: #ff6f61;
    color: #fff;
}

.btn-outline-secondary {
    border-radius: 50%;
    width: 30px;
    height: 30px;
    padding: 0;
    font-weight: bold;
}

.quantity {
    display: inline-block;
    width: 30px;
    text-align: center;
}
</style>
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4 text-center">Votre Panier</h2>

    <?php if (empty($_SESSION['cart'])): ?>
        <p class="text-center text-white fs-5">Votre panier est vide.</p>
    <?php else: ?>
        <form method="post" action="index.php?page=panier">
            <div class="table-responsive">
                <table class="table table-striped align-middle text-center">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Prix</th>
                            <th>Quantité</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        foreach ($_SESSION['cart'] as $index => $item):
                            $subtotal = $item['prix'] * $item['quantite'];
                            $total += $subtotal;
                        ?>
                            <tr data-cart-index="<?= $index ?>">
                                <td><img src="admin/assets/images/<?= htmlspecialchars($item['image']) ?>" width="50" class="rounded"></td>
                                <td><?= htmlspecialchars($item['nom']) ?></td>
                                <td><?= number_format($item['prix'], 2) ?> €</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <button type="button" class="btn btn-outline-secondary me-2 ajax-update">-</button>
                                        <span class="quantity"><?= $item['quantite'] ?></span>
                                        <button type="button" class="btn btn-outline-secondary ms-2 ajax-update">+</button>
                                    </div>
                                </td>
                                <td class="subtotal"><?= number_format($subtotal, 2) ?> €</td>
                                <td>
                                    <button type="submit" name="remove" value="<?= $index ?>" class="btn btn-sm btn-danger">Supprimer</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-4 flex-wrap gap-2">
                <button type="submit" name="clear_cart" class="btn btn-outline-danger">Vider le panier</button>
                <button type="submit" name="checkout" class="btn btn-success">Passer la commande</button>
            </div>

            <h4 class="mt-4 text-end">Total : <span id="cart-total"><?= number_format($total, 2) ?></span> €</h4>
        </form>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.ajax-update').forEach(button => {
    button.addEventListener('click', () => {
        const row = button.closest('tr');
        const index = row.dataset.cartIndex;
        const action = button.textContent.trim() === '+' ? 'increase' : 'decrease';

        fetch('ajax_cart.php', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: `action=${action}&index=${index}`
        })
        .then(res => res.json())
        .then(data => {
            const cartArray = Object.values(data.cart);
            const item = cartArray[index];
            if (item) {
                row.querySelector('.quantity').textContent = item.quantite;
                row.querySelector('.subtotal').textContent = (item.prix * item.quantite).toFixed(2) + ' €';
            }
            document.getElementById('cart-total').textContent = data.total;
        })
        .catch(err => console.error(err));
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
