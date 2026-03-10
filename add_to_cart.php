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

$session_id = session_id();
$user_id = isset($_SESSION['session_tourism_user']) ? (int) $_SESSION['session_tourism_user'] : null;

/*
    Check existing cart item by session + product.
    This works for guests and logged users.
*/
$existingCart = $user->fetchAll(
    array("id", "quantity"),
    array("cart"),
    array(
        "session_id" => $session_id,
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

header("Location: ./");
exit();
?>