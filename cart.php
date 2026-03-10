<?php
session_start();
require_once("./classes/class.user.php");
require_once("./classes/class.header.php");

$user = new USER();
$userHeader = new HEADER("cart");

$session_id = session_id();

// Handle delete item request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product_id'])) {
    $deleteProductId = (int) $_POST['delete_product_id'];
    if ($deleteProductId > 0) {
        $user->deleteTableRow(
            "cart",
            array(
                "session_id" => $session_id,
                "product_id" => $deleteProductId
            )
        );
    }
    header("Location: cart.php");
    exit;
}

$cartItems = $user->fetchAll(
    array("product_id","quantity"),
    array("cart"),
    array("session_id"=>$session_id)
);
?>

<!DOCTYPE html>
<html>
<head>
<?php echo $userHeader->printUserHeader(); ?>
</head>

<body>

<?php echo $userHeader->printUserNav(); ?>

<?php
$total = 0;
$totalItems = 0;

foreach ($cartItems as $item) {
    $product = $user->fetchAll(
        array("id","product_name","price","discounted_price","image","age_group","brand","language"),
        array("products"),
        array("id"=>$item['product_id'])
    )[0];

    $price = $product['discounted_price'] > 0 ? $product['discounted_price'] : $product['price'];
    $subtotal = $price * $item['quantity'];

    $total += $subtotal;
    $totalItems += $item['quantity'];
}

$shipping = $total > 0 ? 450 : 0;
$orderTotal = $total + $shipping;
?>

<div class="container honey-cart-container" style="margin-top: 110px !important;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-3">
            <li class="breadcrumb-item"><a href="index.php" class="text-success"><i class="fa fa-home"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="product_page.php" class="text-success">The Honey Market</a></li>
            <li class="breadcrumb-item active">Treasures</li>
        </ol>
    </nav>

    <h2 class="honey-cart-title mb-3">Honey Cart</h2>

    <div class="honey-cart-steps mb-4 text-center">
        <span class="step active">HONEY CART</span>
        <span class="step-separator">&gt;</span>
        <span class="step">CHECKOUT</span>
        <span class="step-separator">&gt;</span>
        <span class="step">ORDER COMPLETE</span>
    </div>

    <?php if (empty($cartItems)): ?>
        <div class="alert alert-info text-center my-5">
            Your Honey Cart is empty. Start collecting treasures from the <a href="product_page.php" class="text-success">Honey Market</a>!
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <?php foreach ($cartItems as $item): ?>
                    <?php
                    $product = $user->fetchAll(
                        array("id","product_name","price","discounted_price","image","age_group","brand","language"),
                        array("products"),
                        array("id"=>$item['product_id'])
                    )[0];

                    $price = $product['discounted_price'] > 0 ? $product['discounted_price'] : $product['price'];
                    $subtotal = $price * $item['quantity'];
                    ?>
                    <div class="honey-cart-item d-flex align-items-center mb-3">
                        <div class="honey-cart-item-image">
                            <img src="./img/products/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="honey-cart-item-info flex-grow-1">
                            <h5 class="item-title mb-1"><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                            <div class="item-meta">
                                <?php echo !empty($product['language']) ? htmlspecialchars($product['language'], ENT_QUOTES, 'UTF-8') : ''; ?>
                                <?php if (!empty($product['brand'])): ?>
                                    <?php echo !empty($product['language']) ? ' | ' : ''; ?>
                                    <?php echo htmlspecialchars($product['brand'], ENT_QUOTES, 'UTF-8'); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="honey-cart-item-qty text-center">
                            <div class="qty-display">
                                <span class="minus disabled">−</span>
                                <span class="qty-number"><?php echo (int) $item['quantity']; ?></span>
                                <span class="plus disabled">+</span>
                            </div>
                        </div>
                        <div class="honey-cart-item-price text-right">
                            <div class="price-per-unit">Rs. <?php echo number_format($price, 2); ?></div>
                            <div class="price-subtotal">Rs. <?php echo number_format($subtotal, 2); ?></div>
                            <form method="POST" action="cart.php" class="d-inline">
                                <input type="hidden" name="delete_product_id" value="<?php echo (int) $item['product_id']; ?>">
                                <button type="submit" class="delete-link">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="col-lg-4">
                <div class="honey-cart-summary">
                    <h5 class="summary-title">YOUR ORDER</h5>
                    <div class="summary-row">
                        <span>Subtotal (<?php echo $totalItems; ?> item<?php echo $totalItems > 1 ? 's' : ''; ?>)</span>
                        <span>Rs. <?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping (Weight Based)</span>
                        <span>Rs. <?php echo number_format($shipping, 2); ?></span>
                    </div>
                    <p class="summary-note">
                        * Shipping is calculated based on your total order weight and delivery location.
                    </p>
                    <div class="summary-total-row">
                        <span>Order Total</span>
                        <span>Rs. <?php echo number_format($orderTotal, 2); ?></span>
                    </div>
                    <a href="checkout.php" class="btn btn-success btn-block mt-3">PROCEED TO CHECKOUT</a>
                    <a href="product_page.php" class="btn btn-link btn-block continue-shopping-link">CONTINUE SHOPPING</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php echo $userHeader->printUserFooter(); ?>

</body>
</html>