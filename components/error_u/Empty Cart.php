<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart is Empty</title>
  <style>
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f5f7fa;
    }
    .container {
      text-align: center;
      padding: 20px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .container img {
      max-width: 300px;
      width: 100%;
      margin-bottom: 20px;
    }
    .container h2 {
      margin-bottom: 10px;
      font-size: 28px;
      color: #333;
    }
    .container p {
      margin-bottom: 20px;
      font-size: 16px;
      color: #666;
    }
    .container a {
      display: inline-block;
      background: #3498db;
      color: #fff;
      padding: 10px 20px;
      text-decoration: none;
      border-radius: 5px;
      transition: background 0.3s ease;
    }
    .container a:hover {
      background: #2980b9;
    }
  </style>
</head>
<body>
  <div class="container">
    <img src="\hello\assets\images\cart.gif" alt="Error GIF">
    <h2>Your Cart is Empty</h2>
    <p>It looks like you haven't added anything to your cart yet.</p>
    <a href="/hello/user_page.php">Continue Shopping</a>
  </div>
</body>
</html>
