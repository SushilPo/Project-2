<?php
function home_first_name(?array $user): string
{
    return $user ? explode(' ', trim($user['name']))[0] : 'Mia';
}
?>

<section class="hero">
    <div class="home-brand"><img src="assets/thriftwear-logo.svg" alt="ThriftWear"><a href="?page=wishlist" aria-label="Saved items" title="Saved items"><i class="fa-regular fa-heart" aria-hidden="true"></i></a></div>
    <p>Good morning, 🌿</p>
    <h1>Hey, <?= e(home_first_name($user)) ?>!</h1>
    <form class="search">
        <input name="q" value="<?= e($search) ?>" placeholder="Search for items, brands...">
        <select name="category">
            <option value="">All categories</option>
            <?php foreach (['Women', 'Men', 'Shoes', 'Bags'] as $option): ?>
                <option <?= $category === $option ? 'selected' : '' ?>><?= $option ?></option>
            <?php endforeach; ?>
        </select>
        <button aria-label="Search" title="Search"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></button>
    </form>
</section>

<div class="category-row" aria-label="Browse categories">
    <?php foreach (['Women' => 'fa-solid fa-person-dress', 'Men' => 'fa-solid fa-person', 'Shoes' => 'fa-solid fa-shoe-prints', 'Bags' => 'fa-solid fa-bag-shopping'] as $name => $icon): ?>
        <a href="?page=home&category=<?= urlencode($name) ?>"><span><i class="<?= $icon ?>" aria-hidden="true"></i></span><?= $name ?></a>
    <?php endforeach; ?>
</div>

<div class="section-title">
    <h2><?= $search || $category ? 'Search results' : 'Featured Picks' ?></h2>
    <a href="?page=home">See all</a>
</div>

<section class="grid">
    <?php foreach ($listings as $number => $listing): ?>
        <a class="card" href="?page=details&id=<?= $number ?>">
            <img src="<?= e(image_for($listing)) ?>" alt="<?= e($listing['title']) ?>">
            <div class="card-body">
                <small><?= e($listing['category']) ?> · <?= e($listing['item_condition']) ?></small>
                <h3><?= e($listing['title']) ?></h3>
                <b>$<?= e((string)$listing['price']) ?></b>
                <?php if ($listing['swap_available']): ?><span class="tag">↻ Swap</span><?php endif; ?>
                <p><?= e($listing['description']) ?></p>
                <small>by <?= e($listing['seller']) ?></small>
            </div>
        </a>
    <?php endforeach; ?>
</section>

<div class="scroll-hint"><span>◀</span><i></i><span>▶</span></div>

<div class="section-title near-title"><h2>Near You</h2><a href="?page=map">View map</a></div>
<section class="near-grid">
    <?php foreach (array_slice($listings, 0, 2, true) as $number => $listing): ?>
        <a class="near-card" href="?page=details&id=<?= $number ?>"><img src="<?= e(image_for($listing)) ?>" alt=""><b><?= e($listing['title']) ?></b><strong>$<?= e((string)$listing['price']) ?></strong></a>
    <?php endforeach; ?>
</section>

<?php if (!$listings && !$dbError): ?>
    <p class="empty">No listings found yet. Be the first to sell something.</p>
<?php endif; ?>

<?php require __DIR__ . '/../partials/bottom-nav.php'; ?>
