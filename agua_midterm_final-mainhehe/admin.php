<?php
require_once 'connection.php';

$productCRUD = new ProductCRUD();
$categoryCRUD = new CategoryCRUD();
$products = $productCRUD->readProducts();
$categories = $categoryCRUD->readCategories();
$orderCRUD = new OrderCRUD();
$orders = $orderCRUD->readOrders();
if ($_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $action = $_POST['action'];

    if ($action === 'complete') {
        $orderCRUD->updateOrderStatus($order_id, 'completed');
    } elseif ($action === 'delete') {
        $orderCRUD->deleteOrder($order_id);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // File upload handling
    $image_url = null;
    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image_url']['tmp_name'];
        $file_name = basename($_FILES['image_url']['name']);
        $target_path = "assets/products/" . $file_name;

        if (move_uploaded_file($file_tmp, $target_path)) {
            $image_url = $target_path;
        }
    }

    if (isset($_POST['action']) && $_POST['action'] == 'create') {
        $productCRUD->createProduct(
            $_POST['product_name'],
            $_POST['description'],
            $_POST['price'],
            $_POST['category'],
            $_POST['stock_quantity'],
            $image_url
        );
    } elseif (isset($_POST['action']) && $_POST['action'] == 'update') {
        $productCRUD->updateProduct(
            $_POST['product_id'],
            $_POST['product_name'],
            $_POST['description'],
            $_POST['price'],
            $_POST['category'],
            $_POST['stock_quantity'],
            $image_url
        );
    } elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $productCRUD->deleteProduct($_POST['product_id']);
    }

    if (isset($_POST['action']) && $_POST['action'] == 'create_category') {
        $categoryCRUD->createCategory($_POST['category_name']);
    } elseif (isset($_POST['action']) && $_POST['action'] == 'delete_category') {
        $categoryCRUD->deleteCategory($_POST['category_id']);
    } elseif (isset($_POST['action']) && $_POST['action'] == 'update_category') {
        $categoryCRUD->updateCategory($_POST['category_id'], $_POST['category_name']);
    }

    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Admin Panel</title>
</head>

<body>
    <div class="admin-container">
        <div class="admin-cart">
            <a href="logout.php">Logout <i class="fa-solid fa-right-from-bracket"></i></a>
        </div>
        <h1 class="admin-h1">Admin - Manage Products and Categories</h1>

        <!-- Category Form -->
        <form action="admin.php" method="POST" class="admin-form admin-category-form">
            <input type="text" name="category_name" placeholder="Category Name" required>
            <button type="submit" name="action" value="create_category">Add Category</button>
        </form>

        <!-- Categories Table -->
        <table class="admin-table admin-categories-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= $category->category_id; ?></td>
                        <td><?= htmlspecialchars($category->category_name); ?></td>
                        <td class="admin-actions">
                            <form action="admin.php" method="POST" style="display:inline;">
                                <input type="hidden" name="category_id" value="<?= $category->category_id; ?>">
                                <button type="submit" name="action" value="delete_category">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Product Form -->
        <form action="admin.php" method="POST" enctype="multipart/form-data" class="admin-form admin-product-form">
            <input type="hidden" name="product_id">
            <input type="text" name="product_name" placeholder="Product Name" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <input type="number" step="0.01" name="price" placeholder="Price" required>
            <select name="category" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category->category_id; ?>"><?= htmlspecialchars($category->category_name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="stock_quantity" placeholder="Stock Quantity" required>
            <input type="file" name="image_url" accept="image/*">
            <button type="submit" name="action" value="create">Add Product</button>
        </form>

        <table class="admin-table admin-products-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= $product->product_id; ?></td>
                        <td><img src="<?= $product->image_url; ?>" alt="Product Image" class="admin-product-img"></td>
                        <td><?= htmlspecialchars($product->product_name); ?></td>
                        <td><?= htmlspecialchars($product->description); ?></td>
                        <td><?= $product->price; ?></td>
                        <td><?= htmlspecialchars($product->category); ?></td>
                        <td><?= $product->stock_quantity; ?></td>
                        <td class="admin-actions">
                            <form action="admin.php" method="POST" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?= $product->product_id; ?>">
                                <button type="submit" name="action" value="delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Manage Orders</h2>
        <table class="admin-table admin-products-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User name</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Payment Method</th>
                    <th>Shipping Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $orders = $orderCRUD->readOrders();
                foreach ($orders as $order):
                    if ($order->user_name !== null && $order->status == 'pending'):
                ?>
                        <tr>
                            <td><?= htmlspecialchars($order->order_id) ?></td>
                            <td><?= htmlspecialchars($order->user_name) ?></td>
                            <td><?= number_format($order->total_amount, 2) ?></td>
                            <td><?= htmlspecialchars(ucfirst($order->status)) ?></td>
                            <td><?= htmlspecialchars($order->payment_method) ?></td>
                            <td><?= htmlspecialchars($order->shipping_address) ?></td>
                            <td>
                                <form action="" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order->order_id) ?>">
                                    <button type="submit" name="action" value="complete">Complete</button>
                                </form>
                                <form action="" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order->order_id) ?>">
                                    <button type="submit" name="action" value="delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                <?php
                    endif;
                endforeach;
                ?>

            </tbody>
        </table>


    </div>
</body>

</html>