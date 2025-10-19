<?php
session_start();

$title = "Заказ оформлен";
require("blocks/header.php");

if (!isset($_GET['order_id'])) {
    header("Location: catalog.php");
    exit();
}

$order_id = intval($_GET['order_id']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ оформлен - MangaMerchHub</title>
    <link rel="stylesheet" href="css/merch-catalog.css">
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✅</div>
        <h1>Заказ успешно оформлен!</h1>
        
        <?php if (isset($_SESSION['order_success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['order_success']; ?>
                <?php unset($_SESSION['order_success']); ?>
            </div>
        <?php endif; ?>
        
        <p><strong>Номер вашего заказа: #<?php echo $order_id; ?></strong></p>
        <p>Мы свяжемся с вами в ближайшее время для подтверждения заказа.</p>
        <p>На вашу почту отправлено письмо с деталями заказа.</p>
        
        <div class="success-buttons">
            <a href="catalog.php" class="btn