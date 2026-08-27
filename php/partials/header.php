<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ThriftWear</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
<main class="phone">
    <header class="top">
        <a class="logo" href="?page=home" aria-label="ThriftWear home"><img src="assets/thriftwear-logo.svg" alt="ThriftWear"></a>
        <nav>
            <?php if ($user): ?>
                <a href="?page=sell"><i class="fa-solid fa-tag" aria-hidden="true"></i> Sell</a>
                <a class="cart-link" href="?page=cart" aria-label="Shopping cart"><i class="fa-solid fa-cart-shopping" aria-hidden="true"></i> Cart<?php if (!empty($_SESSION['cart'])): ?><span><?= count($_SESSION['cart']) ?></span><?php endif; ?></a>
                <a class="settings-link" href="?page=profile" aria-label="Profile and settings" title="Profile and settings"><i class="fa-solid fa-gear" aria-hidden="true"></i></a>
                <form method="post"><input type="hidden" name="action" value="logout"><button>Log out</button></form>
            <?php else: ?>
                <a class="cart-link" href="?page=cart" aria-label="Shopping cart"><i class="fa-solid fa-cart-shopping" aria-hidden="true"></i> Cart<?php if (!empty($_SESSION['cart'])): ?><span><?= count($_SESSION['cart']) ?></span><?php endif; ?></a>
                <a href="?page=login"><i class="fa-solid fa-arrow-right-to-bracket" aria-hidden="true"></i> Sign in</a>
            <?php endif; ?>
        </nav>
    </header>
    <?php if ($flash): ?>
        <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>
    <?php if ($dbError): ?>
        <div class="flash error"><?= e($dbError) ?></div>
    <?php endif; ?>
