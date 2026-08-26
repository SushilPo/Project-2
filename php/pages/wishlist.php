<?php $savedItems = array_map('resolve_listing', $savedIds); ?>
<section class="<?= $savedItems ? 'browse-page' : 'empty-page' ?> with-bottom-nav">
    <div class="sub-header"><a href="?page=home">←</a><b>Saved Items</b><span></span></div>
    <?php if (!$savedItems): ?>
        <div class="empty-icon">♡</div>
        <h1>Nothing saved yet</h1>
        <p>Tap the heart on any item to save it here.</p>
        <a class="primary empty-button" href="?page=browse">Browse Items</a>
    <?php else: ?>
        <p class="result-count"><?= count($savedItems) ?> saved items</p>
        <section class="browse-grid">
            <?php foreach ($savedIds as $index => $listingId): $listing = $savedItems[$index]; ?>
                <a class="browse-card" href="?page=details&id=<?= $listingId ?>"><img src="<?= e(image_for($listing)) ?>" alt="<?= e($listing['title']) ?>"><div><b><?= e($listing['title']) ?></b><small>♧ <?= e($listing['size']) ?></small><strong>$<?= e((string)$listing['price']) ?></strong><em><?= e($listing['item_condition']) ?></em></div></a>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../partials/bottom-nav.php'; ?>

