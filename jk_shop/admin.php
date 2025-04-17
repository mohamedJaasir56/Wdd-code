<?php 
require 'config.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    if (isset($_POST['add'])) {
        
        $name = sanitizeInput($_POST['name']);
        $price = (float)$_POST['price'];
        
        
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file_type = $_FILES['image']['type'];
            $file_size = $_FILES['image']['size'];
            
            if (!in_array($file_type, ALLOWED_TYPES)) {
                $error = "Invalid file type. Only JPG, PNG, and WEBP allowed.";
            } elseif ($file_size > MAX_FILE_SIZE) {
                $error = "File too large. Maximum size is 2MB.";
            } else {
                $filename = uniqid() . '_' . basename($_FILES['image']['name']);
                $target_path = UPLOAD_DIR . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    $stmt = $conn->prepare("INSERT INTO products (name, price, image_path) VALUES (?, ?, ?)");
                    $stmt->bind_param("sds", $name, $price, $target_path);
                    if ($stmt->execute()) {
                        $message = "Product added successfully!";
                    } else {
                        $error = "Error saving product to database";
                    }
                } else {
                    $error = "Error uploading file";
                }
            }
        } else {
            $error = "Please select an image file";
        }
    } elseif (isset($_POST['delete'])) {
        
        $product_id = (int)$_POST['product_id'];
        
        
        $stmt = $conn->prepare("SELECT image_path FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        
        if ($product) {
            
            if (file_exists($product['image_path'])) {
                unlink($product['image_path']);
            }
            
            
            $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $message = "Product deleted successfully!";
        }
    }
}


$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="admin-nav">
        <div class="brand">JK Shop Admin</div>
        <a href="logout.php" class="logout">Logout</a>
    </nav>

    <div class="admin-container">
        <?php if ($message): ?>
            <div class="success"><?= $message ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <div class="product-form">
            <h2>Add New Product</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                
                <div class="form-group">
                    <label>Product Name:</label>
                    <input type="text" name="name" required>
                </div>
                
                <div class="form-group">
                    <label>Price:</label>
                    <input type="number" step="0.01" name="price" required>
                </div>
                
                <div class="form-group">
                    <label>Product Image:</label>
                    <input type="file" name="image" accept="image/*" required>
                </div>
                
                <button type="submit" name="add">Add Product</button>
            </form>
        </div>

        <div class="product-list">
            <h2>Manage Products</h2>
            <?php while ($product = $products->fetch_assoc()): ?>
            <div class="product-item">
                <img src="<?= $product['image_path'] ?>" alt="<?= $product['name'] ?>">
                <div class="product-info">
                    <h3><?= $product['name'] ?></h3>
                    <p>Rs.<?= number_format($product['price'], 2) ?>/=</p>
                </div>
                <form method="POST" onsubmit="return confirm('Are you sure?')">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <button type="submit" name="delete" class="delete-btn">Delete</button>
                </form>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>