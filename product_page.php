<?php
session_start();
require_once("./classes/class.user.php");
require_once("./classes/class.header.php");
require_once("./classes/class.widgets.php");

$userHeader = new HEADER("shop");
$user = new USER();
$widgets = new WIDGETS();

// --- 1. Fetch Filter Options from DB ---
$categories = $user->fetchAll(array("id", "name"), array("product_categories"), array());
$ageGroups = $user->fetchAll(array("DISTINCT age_group"), array("products"), array("status" => 1));
$brands = $user->fetchAll(array("DISTINCT brand"), array("products"), array("status" => 1));
$price = $user->fetchAll(array("discounted_price"), array("products"), array("status" => 1));

// Get unique values for dropdowns
$conn = $user->getConnection();

// --- 2. Handle Filtering Logic ---
$catF = isset($_GET['category']) ? $_GET['category'] : '';
$ageF = isset($_GET['age']) ? $_GET['age'] : '';
$brandF = isset($_GET['brand']) ? $_GET['brand'] : '';
$priceF = isset($_GET['price']) ? $_GET['price'] : '';
$offerF = isset($_GET['offers']) ? $_GET['offers'] : '';

$query = "SELECT * FROM products WHERE status = 1";
$params = [];

if(!empty($catF)) { $query .= " AND category_id = :cat"; $params[':cat'] = $catF; }
if(!empty($ageF)) { $query .= " AND age_group = :age"; $params[':age'] = $ageF; }
if(!empty($brandF)) { $query .= " AND brand = :brand"; $params[':brand'] = $brandF; }

if($offerF == 'available') { 
    $query .= " AND discount_percentage > 0"; 
}

if(!empty($priceF)) {
    if($priceF == 'low') $query .= " ORDER BY discounted_price ASC";
    elseif($priceF == 'high') $query .= " ORDER BY discounted_price DESC";
} else {
    $query .= " ORDER BY id DESC";
}

$stmt = $conn->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $userHeader->printUserHeader() ?>
    <link rel="stylesheet" href="css/product_style.css">
</head>
<body>
    <?php echo $userHeader->printUserNav(); ?>

    <div class="container mt-5" style="margin-top: 110px !important;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-success"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active">The Honey Market</li>
            </ol>
        </nav>
        
        <h2 class="treasure-title">TREASURES</h2>

        <form method="GET" action="" class="filter-wrapper row mb-5">
            <div class="col-md-2 col-6">
                <select name="category" onchange="this.form.submit()">
                    <option value="">Category</option>
                    <?php foreach($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $catF == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 col-6">
                <select name="age" onchange="this.form.submit()">
                    <option value="">Age</option>
                    <?php foreach($ageGroups as $a): ?>
                        <option value="<?= $a['age_group'] ?>" <?= $ageF == $a['age_group'] ? 'selected' : '' ?>><?= $a['age_group'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 col-6">
                <select name="brand" onchange="this.form.submit()">
                    <option value="">Brands</option>
                    <?php foreach($brands as $b): ?>
                        <option value="<?= $b['brand'] ?>" <?= $brandF == $b['brand'] ? 'selected' : '' ?>><?= $b['brand'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 col-6">
                <select name="price" onchange="this.form.submit()">
                    <option value="">Price</option>
                    <option value="low" <?= $priceF == 'low' ? 'selected' : '' ?>>Low to High</option>
                    <option value="high" <?= $priceF == 'high' ? 'selected' : '' ?>>High to Low</option>
                </select>
            </div>
            <div class="col-md-2 col-6">
                <select name="offers" onchange="this.form.submit()">
                    <option value="">Offers</option>
                    <option value="available" <?= $offerF == 'available' ? 'selected' : '' ?>>Available</option>
                </select>
            </div>
        </form>

        <div class="row">
            <?php if(empty($products)): ?>
                <div class="col-12 text-center py-5"><h4>No treasures found!</h4></div>
            <?php else: foreach($products as $p): ?>
                <div class="col-lg-3 col-md-4 col-6 mb-4">
                    <div class="treasure-card">
                        <div class="img-container">
                            <a href="product_details.php?product_id=<?= $p['id'] ?>">
                               <img src="./img/products/<?= $p['image'] ?>" class="img-fluid cart-product-image">
                            </a>
                        </div>
                        <h6 class="product-name"><?= strtoupper($p['product_name']) ?></h6>
                        <div class="price-box">
                            <?php if($p['discounted_price'] > 0): ?>
                                <span class="old-price">LKR <?= $p['price'] ?>.00</span>
                                <span class="new-price text-success">LKR <?= $p['discounted_price'] ?>.00</span>
                            <?php else: ?>
                                <span class="new-price">LKR <?= $p['price'] ?>.00</span>
                            <?php endif; ?>
                        </div>
                        <form method="POST" action="add_to_cart.php">
                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                            <button type="submit" class="collect-btn add-to-cart-btn">Collect</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>

        <div class="pagination-ui mt-5 mb-5 text-center">
             <span class="page-btn"><i class="fa fa-angle-double-left"></i></span>
             <span class="page-btn active">1</span>
             <span class="page-btn">2</span>
             <span class="page-btn">3</span>
             <span class="page-btn">...</span>
             <span class="page-btn">8</span>
             <span class="page-btn"><i class="fa fa-angle-double-right"></i></span>
        </div>
    </div>

    <?php echo $userHeader->printUserFooter(); ?>
    <script>
        document.querySelectorAll(".add-to-cart-btn").forEach(button => {
            button.addEventListener("click", function(e) {
                e.preventDefault();

                const form = this.closest("form");
                const productCard = this.closest(".treasure-card");
                const productImage = productCard.querySelector(".cart-product-image");
                const cartIcon = document.querySelector("#cart-icon");

                const imgClone = productImage.cloneNode(true);

                const rect = productImage.getBoundingClientRect();
                const cartRect = cartIcon.getBoundingClientRect();

                imgClone.style.position = "fixed";
                imgClone.style.left = rect.left + "px";
                imgClone.style.top = rect.top + "px";
                imgClone.style.width = rect.width + "px";
                imgClone.style.zIndex = 9999;
                imgClone.style.transition = "all 0.8s ease-in-out";

                document.body.appendChild(imgClone);

                setTimeout(() => {
                    imgClone.style.left = cartRect.left + "px";
                    imgClone.style.top = cartRect.top + "px";
                    imgClone.style.width = "20px";
                    imgClone.style.opacity = "0.3";
                }, 10);

                setTimeout(() => {
                    imgClone.remove();

                    /* SEND CART REQUEST WITHOUT PAGE RELOAD */
                    if (form) {
                        fetch("add_to_cart.php", {
                            method: "POST",
                            body: new FormData(form)
                        });
                    }

                    /* CART BOUNCE EFFECT */
                    if (cartIcon) {
                        cartIcon.classList.add("bounce");
                        setTimeout(() => cartIcon.classList.remove("bounce"), 400);
                    }
                }, 800);
            });
        });
    </script>
</body>
</html>