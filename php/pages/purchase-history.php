<?php
$statement = db()->prepare('SELECT * FROM orders WHERE buyer_id = ? ORDER BY created_at DESC');
$statement->execute([$user['id']]);
$orders = $statement->fetchAll();
?>
<section class="<?= $orders ? 'browse-page' : 'empty-page' ?>">
    <div class="sub-header"><a href="?page=profile">←</a><b>Purchase History</b><span></span></div>
    <?php if (!$orders): ?>
        <div class="empty-icon">◷</div>
        <h1>No purchases yet</h1>
        <p>Items you buy will show up here.</p>
        <a class="primary empty-button" href="?page=browse">Browse Items</a>
    <?php else: ?>
        <?php foreach ($orders as $order): $listing = resolve_listing((int)$order['listing_id']); ?>
            <article class="listing-row"><img src="<?= e(image_for($listing)) ?>" alt=""><div><b><?= e($listing['title']) ?></b><span>$<?= e(number_format((float)$order['amount'], 2)) ?> · <?= e(date('d M Y', strtotime($order['created_at']))) ?></span></div></article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
