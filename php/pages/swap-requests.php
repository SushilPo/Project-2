<?php
$statement = db()->prepare('SELECT * FROM swap_requests WHERE requester_id = ? ORDER BY created_at DESC');
$statement->execute([$user['id']]);
$requests = $statement->fetchAll();
?>
<section class="<?= $requests ? 'browse-page' : 'empty-page' ?>">
    <div class="sub-header"><a href="?page=profile">←</a><b>Swap Requests</b><span></span></div>
    <?php if (!$requests): ?>
        <div class="empty-icon">↻</div>
        <h1>No swap requests yet</h1>
        <p>Request a swap on any item that accepts swaps.</p>
        <a class="primary empty-button" href="?page=browse">Browse Items</a>
    <?php else: ?>
        <?php foreach ($requests as $request): $listing = resolve_listing((int)$request['listing_id']); ?>
            <article class="listing-row"><img src="<?= e(image_for($listing)) ?>" alt=""><div><b><?= e($listing['title']) ?></b><span><?= e(ucfirst($request['status'])) ?> · <?= e(date('d M Y', strtotime($request['created_at']))) ?></span></div></article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
