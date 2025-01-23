<?php
session_start();
include(__DIR__.'/cfg.php');

// Funkcje pomocnicze
function addToCart($id, $title, $price_net, $tax_rate, $image_url, $quantity = 1) {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    
    $currentInCart = $_SESSION['cart'][$id]['quantity'] ?? 0;
    $newTotal = $currentInCart + $quantity;

    // Sprawdź dostępność w bazie
    $stmt = $GLOBALS['conn']->prepare("SELECT stock_quantity FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stock = $result->fetch_assoc()['stock_quantity'];

    if($newTotal > $stock) {
        $_SESSION['error'] = "Nie można dodać więcej niż $stock sztuk tego produktu!";
        return false;
    }

    $_SESSION['cart'][$id] = [
        'title' => $title,
        'price_net' => $price_net,
        'tax_rate' => $tax_rate,
        'image_url' => $image_url,
        'quantity' => $newTotal
    ];
    return true;
}

function updateCartItem($product_id, $new_quantity) {
    // Pobierz aktualny stan magazynu
    $stmt = $GLOBALS['conn']->prepare("SELECT stock_quantity FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stock = $stmt->get_result()->fetch_assoc()['stock_quantity'];
    
    if ($new_quantity > $stock) {
        $_SESSION['error'] = "Maksymalna dostępna ilość: $stock sztuk!";
        return false;
    }
    
    if ($new_quantity > 0) {
        $_SESSION['cart'][$product_id]['quantity'] = $new_quantity;
    } else {
        unset($_SESSION['cart'][$product_id]);
    }
    return true;
}

// Obsługa formularzy
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_cart'])) {
        $product_id = $_POST['product_id'];
        $quantity = intval($_POST['quantity']);
        
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            $image_url = $product['image_url'] ?? 'https://americor.com/wp-content/uploads/2022/12/default-scaled.jpeg';
            
            // Sprawdź dostępność
            $currentInCart = $_SESSION['cart'][$product_id]['quantity'] ?? 0;
            $available = $product['stock_quantity'] - $currentInCart;
            
            if($quantity > $available) {
                $_SESSION['error'] = "Możesz dodać maksymalnie $available sztuk!";
                header("Location: sklep.php");
                exit;
            }
            
            if(addToCart($product['id'], $product['title'], $product['price_net'], 
                       $product['tax_rate'], $image_url, $quantity)) {
                $_SESSION['success'] = "Produkt dodany do koszyka!";
            }
        }
        header("Location: sklep.php");
        exit;
    }

    if (isset($_POST['update_quantity'])) {
        $product_id = $_POST['product_id'];
        $new_quantity = intval($_POST['quantity']);
        
        if(updateCartItem($product_id, $new_quantity)) {
            $_SESSION['success'] = "Koszyk zaktualizowany!";
        }
        header("Location: koszyk.php");
        exit;
    }

    if (isset($_POST['remove_item'])) {
        $product_id = $_POST['product_id'];
        if(isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            $_SESSION['success'] = "Produkt usunięty z koszyka!";
        }
        header("Location: koszyk.php");
        exit;
    }
}

// Pobierz produkty
$products = [];
$result = $conn->query("SELECT * FROM products WHERE availability_status = 'available'");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Sklep Mostostal</title>
    <link rel="stylesheet" href="../css/cw1.css">
</head>
<body>
    <section id="menu">
        <nav>
            <ul>
                <li><a href="../index.php">Strona Główna</a></li>
                <li><a href="koszyk.php">Koszyk (<?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>)</a></li>
                <li><a href="../admin/admin.php">Panel Admina</a></li>
                <li><a href="../php/contact.php">Kontakt</a></li>
            </ul>
        </nav>
    </section>

    <main class="shop-container">
        <h1 class="shop-title">Nasze Produkty</h1>
        
        <?php if(isset($_SESSION['error'])): ?>
        <div class="error">
            <?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']); ?>
        </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['success'])): ?>
        <div class="success">
            <?= $_SESSION['success'] ?>
            <?php unset($_SESSION['success']); ?>
        </div>
        <?php endif; ?>

        <div class="products-grid">
            <?php if(!empty($products)): ?>
                <?php foreach($products as $row): 
                    $currentInCart = $_SESSION['cart'][$row['id']]['quantity'] ?? 0;
                    $available = $row['stock_quantity'] - $currentInCart;
                    $image_url = $row['image_url'] ?? 'https://americor.com/wp-content/uploads/2022/12/default-scaled.jpeg';
                ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?= htmlspecialchars($image_url) ?>" 
                             alt="<?= htmlspecialchars($row['title']) ?>" 
                             loading="lazy">
                    </div>
                    
                    <div class="product-details">
                        <h3 class="product-title"><?= htmlspecialchars($row['title']) ?></h3>
                        
                        <div class="product-meta">
                            <span class="product-stock">Dostępność: <?= $available ?> szt.</span>
                            <span class="product-price">Cena netto: <?= number_format($row['price_net'], 2) ?> zł</span>
                        </div>

                        <?php if($available > 0): ?>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                            <div class="quantity-control">
                                <input type="number" 
                                       name="quantity" 
                                       value="1" 
                                       min="1" 
                                       max="<?= $available ?>"
                                       class="quantity-input"
                                       <?= $available === 0 ? 'disabled' : '' ?>>
                                <button type="submit" 
                                        name="add_to_cart" 
                                        class="add-to-cart-btn"
                                        <?= $available === 0 ? 'disabled' : '' ?>>
                                    <?= $available === 0 ? 'Brak w magazynie' : '🛒 Dodaj do koszyka' ?>
                                </button>
                            </div>
                        </form>
                        <?php else: ?>
                        <div class="out-of-stock">Produkt niedostępny</div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
            <p class="no-products">Brak dostępnych produktów</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>