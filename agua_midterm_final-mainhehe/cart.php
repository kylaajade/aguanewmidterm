<?php
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$cartCRUD = new CartCRUD();
$productCRUD = new ProductCRUD();
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$cartItems = $cartCRUD->getCartItems($user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update quantity
    if (isset($_POST['update_quantity'])) {
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $cartCRUD->updateQuantity($user_id, $product_id, $quantity);
        header("Location: cart.php");
        exit;
    }

    // Remove item from cart
    if (isset($_POST['remove_item'])) {
        $product_id = $_POST['product_id'];
        $cartCRUD->removeFromCart($product_id);
        header("Location: cart.php");
        exit;
    }

    if (isset($_POST['checkout'])) {
        error_log("Checkout started for user: $user_id");
        try {
            $payment_method = 'COD';
            $shipping_address = $_SESSION['shipping_address'];

            $connection = $cartCRUD->openConnection();
            $connection->beginTransaction();

            $cartItems = $cartCRUD->getCartItems($user_id);
            $totalAmount = 0;
            foreach ($cartItems as $item) {
                $totalAmount += $item->price * $item->quantity;
            }

            $orderCRUD = new OrderCRUD();
            $order_id = $orderCRUD->createOrder($user_id, $totalAmount, $payment_method, $shipping_address);
            error_log("Order created with ID: $order_id");

            foreach ($cartItems as $item) {
                $orderCRUD->addOrderItem($order_id, $item->product_id, $item->quantity, $item->price);
                $productCRUD->updateProductStock($item->product_id, $item->quantity);
            }

            $cartCRUD->clearCart($user_id);
            $connection->commit();

            header("Location: confirmation.php?order_id=$order_id");
            exit;
        } catch (Exception $e) {
            if (isset($connection)) {
                $connection->rollBack();
            }
            error_log("Error during checkout: " . $e->getMessage());
            echo "Error during checkout: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/brands.min.css" integrity="sha512-EJp8vMVhYl7tBFE2rgNGb//drnr1+6XKMvTyamMS34YwOEFohhWkGq13tPWnK0FbjSS6D8YoA3n3bZmb3KiUYA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Your Cart</title>
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="logo">Kj's Shop</div>
            <ul class="nav-links">
                <a href="index.php">Products</a>
                <a href="cart.php">Cart</a>
            </ul>
            <div class="cart">
                <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>
            </div>
        </nav>
    </header>

    <main>
        <section class="cart">
            <h1>Your Shopping Cart</h1>

            <?php if (count($cartItems) > 0): ?>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td>
                                    <img src="<?= htmlspecialchars($item->image_url); ?>" alt="<?= htmlspecialchars($item->product_name); ?>" class="cart-product-image">
                                    <?= htmlspecialchars($item->product_name); ?>
                                </td>
                                <td>$<?= number_format($item->price, 2); ?></td>
                                <td>
                                    <form action="cart.php" method="POST" class="quantity-form">
                                        <input type="hidden" name="product_id" value="<?= $item->product_id; ?>">
                                        <input type="number" name="quantity" value="<?= $item->quantity; ?>" min="1" required>
                                        <button type="submit" name="update_quantity">Update</button>
                                    </form>
                                </td>
                                <td>$<?= number_format($item->price * $item->quantity, 2); ?></td>
                                <td>
                                    <form action="cart.php" method="POST">
                                        <input type="hidden" name="product_id" value="<?= $item->product_id; ?>">
                                        <button type="submit" name="remove_item">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="cart-summary">
                    <?php
                    $total = 0;
                    foreach ($cartItems as $item) {
                        $total += $item->price * $item->quantity;
                    }
                    ?>
                    <p><strong>Total: $<?= number_format($total, 2); ?></strong></p>
                    <form action="cart.php" method="POST">
                        <button type="submit" name="checkout">Proceed to Checkout</button>
                    </form>
                </div>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </section>
    </main>

</body>

</html>