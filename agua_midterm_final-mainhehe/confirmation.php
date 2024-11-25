<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Confirmation</title>
    <link rel="stylesheet" href="style.css">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            color: #333;
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            /* Full height */
            padding: 20px;
        }

        .confirmation-container {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 30px 40px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .confirmation-message h1 {
            font-size: 24px;
            font-weight: bold;
            color: #111;
            margin-bottom: 10px;
        }

        .confirmation-message p {
            font-size: 16px;
            color: #555;
            margin-bottom: 20px;
        }

        .back-to-products {
            display: inline-block;
            background-color: #ff9900;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .back-to-products:hover {
            background-color: #e68a00;
        }

        @media (max-width: 768px) {
            .confirmation-container {
                padding: 20px;
                width: 100%;
                margin: 0 auto;
            }
        }
    </style>
</head>

<body>
    <div class="confirmation-container">
        <div class="confirmation-message">
            <h1>Thank you for your purchase!</h1>
            <p>Your item has been purchased successfully.</p>
            <a href="index.php" class="back-to-products">Continue Shopping</a>
        </div>
    </div>
</body>

</html>