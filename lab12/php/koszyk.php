<?php
session_start();
include(__DIR__.'/cfg.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_item'])) {
        unset($_SESSION['cart'][$_POST['product_id']]);
    }
    
    if (isset($_POST['update_quantity'])) {
        $product_id = $_POST['product_id'];
        $new_quantity = intval($_POST['quantity']);
        
        if ($new_quantity > 0) {
            $_SESSION['cart'][$product_id]['quantity'] = $new_quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    }
}

$total_netto = 0;
$total_brutto = 0;
$cart_items = $_SESSION['cart'] ?? [];
$default_image = 'https://americor.com/wp-content/uploads/2022/12/default-scaled.jpeg';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Koszyk</title>
    <link rel="stylesheet" href="../css/cw1.css">
</head>
<body>
    <section id="menu">
        <nav>
            <ul>
                <li><a href="sklep.php">Wróć do sklepu</a></li>
                <li><a href="../index.php">Strona Główna</a></li>
            </ul>
        </nav>
    </section>

    <main class="cart-container">
        <h1 class="cart-title">Twój Koszyk</h1>
        
        <?php if(empty($cart_items)): ?>
            <div class="empty-cart">
                <p>Koszyk jest pusty</p>
                <a href="sklep.php" class="back-to-shop">Przejdź do sklepu</a>
            </div>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach($cart_items as $id => $item): 
                    $price_netto = $item['price_net'];
                    $price_brutto = $price_netto * (1 + $item['tax_rate']/100);
                    $total_netto += $price_netto * $item['quantity'];
                    $total_brutto += $price_brutto * $item['quantity'];
                    $image_url = !empty($item['image_url']) ? $item['image_url'] : $default_image;
                ?>
                <div class="cart-item">
                    <div class="cart-item-image">
                        <img src="<?= htmlspecialchars($image_url) ?>" 
                             alt="<?= htmlspecialchars($item['title']) ?>">
                    </div>
                    
                    <div class="cart-item-details">
                        <h4><?= htmlspecialchars($item['title']) ?></h4>
                        <div class="cart-item-meta">
                            <p>Cena netto: <?= number_format($price_netto, 2) ?> zł</p>
                            <p>Cena brutto: <?= number_format($price_brutto, 2) ?> zł</p>
                            <p>VAT: <?= $item['tax_rate'] ?>%</p>
                        </div>
                        
                        <form method="POST" class="quantity-form">
                            <input type="hidden" name="product_id" value="<?= $id ?>">
                            <div class="quantity-control">
                                <label>Ilość:</label>
                                <input type="number" 
                                       name="quantity" 
                                       value="<?= $item['quantity'] ?>" 
                                       min="1"
                                       class="quantity-input">
                                <button type="submit" name="update_quantity" class="update-btn">🔄</button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="cart-item-actions">
                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?= $id ?>">
                            <button type="submit" name="remove_item" class="remove-btn">🗑️</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                <div class="summary-box">
                    <h3>Podsumowanie zamówienia</h3>
                    <div class="total-price">
                        <span>Suma netto:</span>
                        <span><?= number_format($total_netto, 2) ?> zł</span>
                    </div>
                    <div class="total-price">
                        <span>Suma brutto:</span>
                        <span><?= number_format($total_brutto, 2) ?> zł</span>
                    </div>
                    <button class="checkout-btn">➡️ Przejdź do płatności</button>
                </div>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>