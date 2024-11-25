<?php
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$productCRUD = new ProductCRUD();
$cartCRUD = new CartCRUD();
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id'])) {
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        if (isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
            $cartCRUD->addToCart($user_id, $_POST['product_id'], $quantity);
            header("Location: index.php");
            exit;
        } elseif (isset($_POST['action']) && $_POST['action'] === 'buy_now') {
            $cartCRUD->buyNow($user_id, $_POST['product_id'], $quantity);
            header("Location: confirmation.php");
            exit;
        }
    }
}

$products = $productCRUD->readProducts();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Agua Midterm - Products</title>
</head>

<body class="bg-pink-50 text-gray-800">

    <!-- Header Section -->
    <header class="bg-pink-600 py-4">
        <nav class="container mx-auto flex justify-between items-center px-4">
            <div class="logo text-white text-2xl font-semibold">Happy Shopping</div>
            <ul class="flex space-x-6 text-white">
                <li><a href="index.php" class="hover:text-pink-200">Products</a></li>
                <li><a href="cart.php" class="hover:text-pink-200">Cart</a></li>
            </ul>
            <div class="cart">
                <a href="logout.php" class="text-white hover:text-pink-200"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </div>
        </nav>
    </header>

    <!-- Main Content Section -->
    <main class="my-8">
        <section class="container mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 px-4">
            <?php foreach ($products as $product): ?>
                <div class="product-card bg-white p-4 rounded-lg shadow-lg hover:shadow-2xl transition-transform transform hover:scale-105">
                    <img src="<?= htmlspecialchars($product->image_url); ?>"
                        alt="<?= htmlspecialchars($product->product_name); ?>" class="product-image w-full h-64 object-cover rounded-t-lg mb-4">
                    <div class="product-info">
                        <h3 class="product-title text-lg font-semibold text-gray-900"><?= htmlspecialchars($product->product_name); ?></h3>
                        <p class="product-price text-xl text-pink-600 font-bold mt-2">$<?= number_format($product->price, 2); ?></p>
                        <div class="product-actions mt-4 space-y-4">
                            <!-- Add to Cart Form -->
                            <form action="index.php" method="POST" class="flex items-center space-x-2">
                                <input type="hidden" name="product_id" value="<?= $product->product_id; ?>">
                                <input type="number" name="quantity" value="1" min="1" class="quantity-input border border-pink-300 rounded-lg p-2 w-16 text-center" required>
                                <button type="submit" name="action" value="add_to_cart" class="btn add-to-cart-btn bg-pink-500 text-white hover:bg-pink-600 px-4 py-2 rounded-lg transition">Add to Cart</button>
                            </form>

                            <!-- Buy Now Form -->
                            <form action="index.php" method="POST" class="flex items-center space-x-2">
                                <input type="hidden" name="product_id" value="<?= $product->product_id; ?>">
                                <input type="number" name="quantity" value="1" min="1" class="quantity-input border border-pink-300 rounded-lg p-2 w-16 text-center" required>
                                <button type="submit" name="action" value="buy_now" class="btn buy-now-btn bg-pink-700 text-white hover:bg-pink-800 px-4 py-2 rounded-lg transition">Buy Now</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
    </main>

</body>

</html>
