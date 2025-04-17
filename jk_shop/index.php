<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jk ShOP</title>
    <link rel="icon" href="screwdriver.png">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>
<body>
<nav class="navbar">
        <div class="logo">
            <a href="#">⚒ JK ShOP</a>
        </div>
        <ul class="menu" id="menu">
            <li><a href="#">Home</a></li>
            <li><a href="#">Shop</a></li>
            <li><a href="#">Categories</a></li>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
        <div class="search-bar">
            <input type="text" placeholder="Search products...">
            <button><i class="fas fa-search"></i></button>
        </div>
        <div class="icons">
            <a href="#"><i class="fas fa-shopping-cart"></i></a>
            <a href="#"><i class="fas fa-user"></i></a>
            <a href="#"><i class="fas fa-heart"></i></a>
        </div>
        <div class="menu-toggle" id="mobile-menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to JK ShOP</h1>
            <p>Your one-stop shop for all your hardware needs.</p>
            <a href="#" class="btn">Shop Now</a>
        </div>
    </section>
    

    <div class="product-grid">
        <?php
        $result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
        while($row = $result->fetch_assoc()): ?>
        <div class="product-card">
            <img src="<?= $row['image_path'] ?>" alt="<?= $row['name'] ?>">
            <h3><?= $row['name'] ?></h3>
            <p>Rs.<?= number_format($row['price'], 2) ?>/=</p>
            <button onclick="addToCart('<?= $row['name'] ?>')">Add to Cart</button>
        </div>
        <?php endwhile; ?>
    </div>

    
</body>
</html>