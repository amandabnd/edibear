<?php
session_start();
require_once("./classes/class.user.php");

$user = new USER();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id'])) {
    header("Location: ./");
    exit();
}

$product_id = (int) $_POST['product_id'];

if ($product_id <= 0) {
    header("Location: ./");
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['session_tourism_user']) || empty($_SESSION['session_tourism_user'])) {
    // Return error response for AJAX request
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['error' => 'Please login to continue']);
        exit();
    } else {
        header("Location: ./login");
        exit();
    }
}

$user_id = (int) $_SESSION['session_tourism_user'];
$session_id = session_id();

/*
    Check existing cart item by user_id + product.
    Only logged in users can add to cart.
*/
$existingCart = $user->fetchAll(
    array("id", "quantity"),
    array("cart"),
    array(
        "user_id" => $user_id,
        "product_id" => $product_id
    )
);

if (!empty($existingCart)) {
    $cart_id = (int) $existingCart[0]['id'];
    $newQty = (int) $existingCart[0]['quantity'] + 1;

    $user->updateTable(
        "cart",
        array("quantity" => $newQty),
        array("id" => $cart_id)
    );
} else {
    $user->insertTable(
        "cart",
        array(
            "user_id" => $user_id,
            "session_id" => $session_id,
            "product_id" => $product_id,
            "quantity" => 1
        )
    );
}

// Return success response for AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    echo json_encode(['success' => true]);
    exit();
}

header("Location: ./");
exit();
?>