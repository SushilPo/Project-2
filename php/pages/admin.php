<?php
$statement = db()->prepare('SELECT COUNT(*) FROM listings WHERE user_id = ?');
$statement->execute([$user['id']]);
$totalProducts = (int)$statement->fetchColumn();

$statement = db()->prepare('SELECT COALESCE(SUM(o.amount), 0) FROM orders o JOIN listings l ON l.id = o.listing_id WHERE l.user_id = ?');
$statement->execute([$user['id']]);
$totalSales = (float)$statement->fetchColumn();

$statement = db()->prepare('SELECT COUNT(*) FROM swap_requests sr JOIN listings l ON l.id = sr.listing_id WHERE l.user_id = ?');
$statement->execute([$user['id']]);
$totalSwaps = (int)$statement->fetchColumn();

$statement = db()->prepare("SELECT COUNT(*) FROM swap_requests sr JOIN listings l ON l.id = sr.listing_id WHERE l.user_id = ? AND sr.status = 'accepted'");
$statement->execute([$user['id']]);
$acceptedSwaps = (int)$statement->fetchColumn();
$swapRate = $totalSwaps ? round($acceptedSwaps / $totalSwaps * 100) : 0;

$statement = db()->prepare('SELECT COUNT(*) FROM orders o JOIN listings l ON l.id = o.listing_id WHERE l.user_id = ?');
$statement->execute([$user['id']]);
$totalOrders = (int)$statement->fetchColumn();

$statement = db()->prepare(
    "SELECT 'listing' AS kind, title AS label, created_at FROM listings WHERE user_id = ?
     UNION ALL
     SELECT 'order' AS kind, l.title AS label, o.created_at FROM orders o JOIN listings l ON l.id = o.listing_id WHERE l.user_id = ?
     UNION ALL
     SELECT 'swap' AS kind, l.title AS label, sr.created_at FROM swap_requests sr JOIN listings l ON l.id = sr.listing_id WHERE l.user_id = ?
     ORDER BY created_at DESC LIMIT 5"
);
$statement->execute([$user['id'], $user['id'], $user['id']]);
$activity = $statement->fetchAll();
$activityLabels = ['listing' => 'New product listed', 'order' => 'Item sold', 'swap' => 'Swap request received'];
?>
<section class="admin-page"><div class="sub-header"><a href="?page=profile">←</a><b>My Dashboard</b><a href="?page=admin-products">Products</a></div><div class="admin-tabs"><a class="active" href="?page=admin">Overview</a><a href="?page=admin-products">My Products</a></div><div class="admin-stats"><div><b><?= number_format($totalProducts) ?></b><small>My Listings</small></div><div><b><?= number_format($totalOrders) ?></b><small>Items Sold</small></div><div><b>$<?= number_format($totalSales, 2) ?></b><small>Total Earned</small></div><div><b><?= $swapRate ?>%</b><small>Swap Accept Rate</small></div></div><div class="chart-card"><h3>Recent Activity</h3><?php if ($activity): ?><?php foreach ($activity as $event): ?><p class="activity-row">● &nbsp; <?= e($activityLabels[$event['kind']]) ?> — <?= e($event['label']) ?><small><?= e(date('d M, g:i A', strtotime($event['created_at']))) ?></small></p><?php endforeach; ?><?php else: ?><p class="activity-row">No activity yet — list an item to get started.</p><?php endif; ?></div></section>


