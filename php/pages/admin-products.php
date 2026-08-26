<?php
$adminSearch = trim((string)($_GET['pq'] ?? ''));
$statusFilter = (string)($_GET['status'] ?? '');
$statement = db()->prepare("SELECT l.* FROM listings l WHERE l.user_id = ? AND (? = '' OR l.title LIKE CONCAT('%', ?, '%')) AND (? = '' OR l.status = ?) ORDER BY l.created_at DESC");
$statement->execute([$user['id'], $adminSearch, $adminSearch, $statusFilter, $statusFilter]);
$adminListings = $statement->fetchAll();
$statement = db()->prepare('SELECT COUNT(*) FROM listings WHERE user_id = ?');
$statement->execute([$user['id']]);
$totalCount = (int)$statement->fetchColumn();
$statement = db()->prepare("SELECT COUNT(*) FROM listings WHERE user_id = ? AND status = 'pending'");
$statement->execute([$user['id']]);
$pausedCount = (int)$statement->fetchColumn();
?>
<section class="<?= $adminListings ? 'admin-page' : 'empty-page' ?>">
    <div class="sub-header"><a href="?page=admin">←</a><b>My Products</b><span></span></div>
    <?php if (!$totalCount): ?>
        <div class="empty-icon">▣</div>
        <h1>You haven't listed anything yet</h1>
        <p>List your first item to start managing it here.</p>
        <a class="primary empty-button" href="?page=sell">List an Item</a>
    <?php else: ?>
        <form class="browse-search"><input type="hidden" name="page" value="admin-products"><span>⌕</span><input name="pq" value="<?= e($adminSearch) ?>" placeholder="Search your products..."><button>⌕</button></form>
        <div class="admin-filters">
            <a class="<?= $statusFilter === '' ? 'active' : '' ?>" href="?page=admin-products&pq=<?= urlencode($adminSearch) ?>">All (<?= number_format($totalCount) ?>)</a>
            <a class="<?= $statusFilter === 'pending' ? 'active' : '' ?>" href="?page=admin-products&pq=<?= urlencode($adminSearch) ?>&status=pending">Paused (<?= number_format($pausedCount) ?>)</a>
        </div>
        <?php foreach ($adminListings as $listing): ?>
            <div class="admin-product">
                <img src="<?= e(image_for($listing)) ?>" alt="">
                <div><b><?= e($listing['title']) ?></b><small>$<?= e((string)$listing['price']) ?> · <?= e(date('d M Y', strtotime($listing['created_at']))) ?></small></div>
                <em class="<?= $listing['status'] === 'pending' ? 'pending' : '' ?>"><?= $listing['status'] === 'pending' ? 'Paused' : 'Active' ?></em>
                <form method="post"><input type="hidden" name="action" value="toggle_listing_status"><input type="hidden" name="id" value="<?= (int)$listing['id'] ?>"><button><?= $listing['status'] === 'pending' ? 'Activate' : 'Pause' ?></button></form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>


