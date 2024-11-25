<?php
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $conn = new Connection();
    $userCRUD = new UserCRUD();
    $user = $userCRUD->login($email, $password);

    if ($user) {
        $_SESSION['user_id'] = $user->user_id;
        $_SESSION['username'] = $user->username;
        $_SESSION['user_type'] = $user->user_type;
        if ($user->user_type == 'admin') {
            header("Location: admin.php");
        } else {

            header("Location: index.php");
        }
        exit;
    } else {
        $error_message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Login</title>
</head>

<body class="bg-pink-50 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-3xl font-semibold text-center text-pink-600 mb-6">Login</h2>

        <?php if (!empty($error_message)) : ?>
            <p class="text-red-500 text-center mb-4"><?= htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-pink-600">Email:</label>
                <input type="text" id="email" name="email" required class="mt-1 p-2 w-full border border-pink-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-pink-600">Password:</label>
                <input type="password" id="password" name="password" required class="mt-1 p-2 w-full border border-pink-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
            </div>

            <button type="submit" class="w-full py-2 px-4 bg-pink-600 text-white rounded-md hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-500">Login</button>
        </form>

        <p class="mt-4 text-center text-sm text-pink-500">Don't have an account? <a href="register.php" class="text-pink-600 hover:text-pink-700">Sign up here</a>.</p>
    </div>

</body>

</html>
