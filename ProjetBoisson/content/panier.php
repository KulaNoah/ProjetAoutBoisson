<h2 class="mb-4">Votre Panier</h2>

<?php if (empty($_SESSION['cart'])): ?>
    <p>Votre panier est vide.</p>
<?php else: ?>
    <form method="post" action="index.php?page=panier">
        <table class="table table-striped">
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
                        <td><img src="admin/assets/images/<?= htmlspecialchars($item['image']) ?>" width="50"></td>
                        <td><?= htmlspecialchars($item['nom']) ?></td>
                        <td><?= number_format($item['prix'], 2) ?> €</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-sm btn-outline-secondary me-1 ajax-update">-</button>
                                <span class="quantity"><?= $item['quantite'] ?></span>
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-1 ajax-update">+</button>
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

        <div class="d-flex justify-content-between mt-4">
            <button type="submit" name="clear_cart" class="btn btn-outline-danger">Vider le panier</button>
            <button type="submit" name="checkout" class="btn btn-success">Passer la commande</button>
        </div>
    </form>

    <h4>Total : <span id="cart-total"><?= number_format($total, 2) ?></span> €</h4>
<?php endif; ?>

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
