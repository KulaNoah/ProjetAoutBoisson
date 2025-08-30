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
                    <tr>
                        <td><img src="admin/assets/images/<?= htmlspecialchars($item['image']) ?>" width="50"></td>
                        <td><?= htmlspecialchars($item['nom']) ?></td>
                        <td><?= number_format($item['prix'], 2) ?> €</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <button type="submit" name="decrease" value="<?= $index ?>" class="btn btn-sm btn-outline-secondary me-1">-</button>
                                <span><?= $item['quantite'] ?></span>
                                <button type="submit" name="increase" value="<?= $index ?>" class="btn btn-sm btn-outline-secondary ms-1">+</button>
                            </div>
                        </td>
                        <td><?= number_format($subtotal, 2) ?> €</td>
                        <td>
                            <button type="submit" name="remove" value="<?= $index ?>" class="btn btn-sm btn-danger">Supprimer</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="d-flex justify-content-between mt-4">
    <form method="post" action="index.php?page=panier">
        <button type="submit" name="clear_cart" class="btn btn-outline-danger">Vider le panier</button>
    </form>

    <form method="post" action="index.php?page=panier">
        <button type="submit" name="checkout" class="btn btn-success">Passer la commande</button>
    </form>
</div>

    </form>
    <h4>Total : <?= number_format($total, 2) ?> €</h4>
<?php endif; ?>
