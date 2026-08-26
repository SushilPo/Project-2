<?php
$statement = db()->prepare('SELECT COUNT(*) FROM orders WHERE buyer_id = ?');
$statement->execute([$user['id']]);
$purchaseCount = (int)$statement->fetchColumn();

$statement = db()->prepare('SELECT COUNT(*) FROM swap_requests WHERE requester_id = ?');
$statement->execute([$user['id']]);
$swapCount = (int)$statement->fetchColumn();

$statement = db()->prepare('SELECT COUNT(DISTINCT listing_id) FROM messages WHERE sender_id = ?');
$statement->execute([$user['id']]);
$messageCount = (int)$statement->fetchColumn();
?>
<section class="profile with-bottom-nav">
    <div class="profile-hero">
        <span class="avatar">🌿</span>
        <h1><?= e($user['name']) ?></h1>
        <p><?= e($user['email']) ?> · Australia</p>
        <div class="stats">
            <b><?= count($mine) ?><small>Listings</small></b>
            <b>34<small>Sold</small></b>
            <b>4.9<small>Rating</small></b>
        </div>
    </div>
    <?php if ($lastLogin): ?><div class="last-login"><b>Last sign-in</b><span><?= e(date('M j, Y g:i A', strtotime($lastLogin['logged_in_at']))) ?> via <?= e(ucfirst($lastLogin['login_method'])) ?></span></div><?php endif; ?>
    <div class="seller-badge"><b>✓ &nbsp; Eco Seller Badge</b><span><?= count($mine) + 34 ?> items given a second life 🌿</span></div>
    <section class="profile-menu">
        <?php foreach ([['▣', 'My Listings', count($mine), 'profile'], ['◷', 'Purchase History', $purchaseCount, 'purchase-history'], ['♡', 'Saved Items', count($savedIds), 'wishlist'], ['↻', 'Swap Requests', $swapCount, 'swap-requests'], ['◯', 'Messages', $messageCount, 'messages'], ['◧', 'My Dashboard', '', 'admin'], ['⚙', 'Settings', '', 'settings']] as $item): ?>
            <a href="?page=<?= $item[3] ?>"><span class="menu-icon"><?= $item[0] ?></span><b><?= $item[1] ?></b><?php if ($item[2] !== ''): ?><strong><?= $item[2] ?></strong><?php endif; ?><i>›</i></a>
        <?php endforeach; ?>
    </section>
    <?php if ($mine): ?><h2>My listings</h2><?php endif; ?>
    <?php foreach ($mine as $listing): ?><article class="listing-row"><img src="<?= e(image_for($listing)) ?>" alt=""><div><b><?= e($listing['title']) ?></b><span>$<?= e((string)$listing['price']) ?> · <?= e($listing['item_condition']) ?></span></div><form method="post"><input type="hidden" name="action" value="delete_listing"><input type="hidden" name="id" value="<?= (int)$listing['id'] ?>"><button class="delete">Delete</button></form></article><?php endforeach; ?>
</section>
<?php require __DIR__ . '/../partials/bottom-nav.php'; ?>

