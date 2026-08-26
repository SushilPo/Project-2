<?php
$statement = db()->prepare('SELECT listing_id, MAX(created_at) AS last_at FROM messages WHERE sender_id = ? GROUP BY listing_id ORDER BY last_at DESC');
$statement->execute([$user['id']]);
$conversations = $statement->fetchAll();
?>
<section class="<?= $conversations ? 'browse-page' : 'empty-page' ?>">
    <div class="sub-header"><a href="?page=profile">←</a><b>Messages</b><span></span></div>
    <?php if (!$conversations): ?>
        <div class="empty-icon">◯</div>
        <h1>No conversations yet</h1>
        <p>Message a seller from any item's details page.</p>
        <a class="primary empty-button" href="?page=browse">Browse Items</a>
    <?php else: ?>
        <?php foreach ($conversations as $conversation): $listing = resolve_listing((int)$conversation['listing_id']); ?>
            <a class="chat-item" href="?page=chat&id=<?= (int)$conversation['listing_id'] ?>"><img src="<?= e(image_for($listing)) ?>" alt=""><b><?= e($listing['seller']) ?><strong><?= e($listing['title']) ?></strong></b></a>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
