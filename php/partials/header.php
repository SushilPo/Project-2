<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ThriftWear</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="phone">
    <header class="top">
        <a class="logo" href="?page=home"><span>↻</span>ThriftWear</a>
        <nav>
            <?php if ($user): ?>
                <a href="?page=sell">Sell</a>
                <a class="cart-link" href="?page=cart" aria-label="Shopping cart">Cart<?php if (!empty($_SESSION['cart'])): ?><span><?= count($_SESSION['cart']) ?></span><?php endif; ?></a>
                <a class="settings-link" href="?page=profile">⚙</a>
                <form method="post"><input type="hidden" name="action" value="logout"><button>Log out</button></form>
            <?php else: ?>
                <a class="cart-link" href="?page=cart" aria-label="Shopping cart">Cart<?php if (!empty($_SESSION['cart'])): ?><span><?= count($_SESSION['cart']) ?></span><?php endif; ?></a>
                <a href="?page=login">Sign in</a>
            <?php endif; ?>
        </nav>
    </header>
    <?php if ($flash): ?>
        <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>
    <?php if ($dbError): ?>
        <div class="flash error"><?= e($dbError) ?></div>
    <?php endif; ?>
