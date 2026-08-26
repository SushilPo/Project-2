<?php
declare(strict_types=1);

if (PHP_SAPI === 'cli-server') {
    $requested = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($requested !== __DIR__ . '/index.php' && is_file($requested)) {
        return false;
    }
}

require __DIR__ . '/config.php';
require __DIR__ . '/google_auth.php';

$page = $_GET['page'] ?? 'home';
$user = current_user();
$_SESSION['cart'] ??= [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/actions.php';
}

if ($page === 'google-login') {
    if (!google_oauth_configured()) {
        flash('Google sign-in is not configured yet. Ask the site admin to add Google API credentials.', 'error');
        redirect('?page=login');
    }
    redirect(google_auth_url());
}

if ($page === 'google-callback') {
    try {
        $profile = google_handle_callback();
        $statement = db()->prepare('SELECT id, name, email FROM users WHERE google_id = ? OR email = ?');
        $statement->execute([$profile['sub'], strtolower((string)$profile['email'])]);
        $account = $statement->fetch();
        if ($account) {
            db()->prepare('UPDATE users SET google_id = ? WHERE id = ?')->execute([$profile['sub'], $account['id']]);
        } else {
            $insert = db()->prepare('INSERT INTO users (name, email, password_hash, google_id) VALUES (?, ?, ?, ?)');
            $insert->execute([(string)($profile['name'] ?? 'ThriftWear User'), strtolower((string)$profile['email']), password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT), $profile['sub']]);
            $account = ['id' => (int)db()->lastInsertId(), 'name' => (string)($profile['name'] ?? 'ThriftWear User'), 'email' => strtolower((string)$profile['email'])];
        }
        $_SESSION['user'] = $account;
        db()->prepare('INSERT INTO login_history (user_id, ip_address, login_method) VALUES (?, ?, ?)')->execute([$account['id'], $_SERVER['REMOTE_ADDR'] ?? null, 'google']);
        flash('Signed in with Google.');
        redirect('?page=home');
    } catch (RuntimeException $exception) {
        flash($exception->getMessage(), 'error');
        redirect('?page=login');
    } catch (PDOException $exception) {
        flash('Database is unavailable. Check MySQL and schema.sql.', 'error');
        redirect('?page=login');
    }
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$search = trim((string)($_GET['q'] ?? ''));
$category = (string)($_GET['category'] ?? '');
$sort = in_array($_GET['sort'] ?? '', ['newest', 'price_asc', 'price_desc', 'condition'], true) ? $_GET['sort'] : 'newest';
$listings = [];
$mine = [];
$lastLogin = null;
$dbError = null;

function demo_listings(): array
{
    return [
        ['id' => 0, 'title' => "Vintage Levi's 501 Jeans", 'description' => 'Classic Levi\'s 501 jeans in great condition. Worn a handful of times, no fading or damage. A wardrobe staple that never goes out of style.', 'price' => '45', 'category' => 'Men', 'size' => 'W28 L30', 'item_condition' => 'Good', 'swap_available' => 1, 'image_url' => 'https://images.unsplash.com/photo-1542272604-787c3835535d?auto=format&fit=crop&w=700&q=85', 'seller' => 'maya_thrift'],
        ['id' => 0, 'title' => 'Nike Air Max 90 White', 'description' => 'Clean white sneakers ready for a new home.', 'price' => '95', 'category' => 'Shoes', 'size' => 'US 8', 'item_condition' => 'Like new', 'swap_available' => 0, 'image_url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=700&q=85', 'seller' => 'mia_thrift'],
        ['id' => 0, 'title' => 'Zara Floral Midi Dress', 'description' => 'Bright floral dress for sunny days.', 'price' => '28', 'category' => 'Women', 'size' => 'S', 'item_condition' => 'Excellent', 'swap_available' => 1, 'image_url' => 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?auto=format&fit=crop&w=700&q=85', 'seller' => 'maya_thrift'],
        ['id' => 0, 'title' => 'Leather Crossbody Bag', 'description' => 'A timeless everyday bag.', 'price' => '62', 'category' => 'Bags', 'size' => 'One size', 'item_condition' => 'Good', 'swap_available' => 1, 'image_url' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=700&q=85', 'seller' => 'mia_thrift'],
    ];
}

try {
    if (in_array($page, ['home', 'browse', 'sort', 'map'], true)) {
        $orderBy = ['newest' => 'l.created_at DESC', 'price_asc' => 'l.price ASC', 'price_desc' => 'l.price DESC', 'condition' => "FIELD(l.item_condition,'Like new','Excellent','Good','Fair')"][$sort];
        $statement = db()->prepare("SELECT l.*, u.name AS seller FROM listings l JOIN users u ON u.id = l.user_id WHERE l.status = 'approved' AND (? = '' OR l.title LIKE CONCAT('%', ?, '%') OR l.description LIKE CONCAT('%', ?, '%')) AND (? = '' OR l.category = ?) ORDER BY $orderBy");
        $statement->execute([$search, $search, $search, $category, $category]);
        $listings = $statement->fetchAll();
        if (!$listings && !$search && !$category) {
            $listings = demo_listings();
            $conditionOrder = array_flip(['Like new', 'Excellent', 'Good', 'Fair']);
            usort($listings, function ($a, $b) use ($sort, $conditionOrder) {
                return match ($sort) {
                    'price_asc' => $a['price'] <=> $b['price'],
                    'price_desc' => $b['price'] <=> $a['price'],
                    'condition' => $conditionOrder[$a['item_condition']] <=> $conditionOrder[$b['item_condition']],
                    default => 0,
                };
            });
        }
    }
    if ($page === 'profile' && $user) {
        $statement = db()->prepare('SELECT * FROM listings WHERE user_id = ? ORDER BY created_at DESC');
        $statement->execute([$user['id']]);
        $mine = $statement->fetchAll();
        $statement = db()->prepare('SELECT logged_in_at, login_method FROM login_history WHERE user_id = ? ORDER BY logged_in_at DESC LIMIT 1 OFFSET 1');
        $statement->execute([$user['id']]);
        $lastLogin = $statement->fetch() ?: null;
    }
} catch (PDOException $exception) {
    $dbError = 'Connect MySQL and run php/schema.sql to load marketplace data.';
}

$savedIds = [];
if ($user) {
    try {
        $statement = db()->prepare('SELECT listing_id FROM wishlists WHERE user_id = ?');
        $statement->execute([$user['id']]);
        $savedIds = array_map('intval', array_column($statement->fetchAll(), 'listing_id'));
    } catch (PDOException $exception) {
        $savedIds = [];
    }
}

function resolve_listing(int $id): array
{
    try {
        $statement = db()->prepare('SELECT l.*, u.name AS seller FROM listings l JOIN users u ON u.id = l.user_id WHERE l.id = ?');
        $statement->execute([$id]);
        $found = $statement->fetch();
        if ($found) {
            return $found;
        }
    } catch (PDOException $exception) {
        // Fall through to demo data when the database is unavailable.
    }
    $demo = demo_listings();
    return $demo[$id] ?? $demo[0];
}

$product = resolve_listing((int)($_GET['id'] ?? 0));

if ($page === 'splash') {
    require __DIR__ . '/pages/splash.php';
    exit;
}

function image_for(array $listing): string
{
    return $listing['image_url'] ?: 'https://images.unsplash.com/photo-1542272604-787c3835535d?auto=format&fit=crop&w=700&q=85';
}

require __DIR__ . '/partials/header.php';

switch ($page) {
    case 'login':
    case 'register':
        require __DIR__ . '/pages/auth.php';
        break;
    case 'forgot-password':
        require __DIR__ . '/pages/forgot-password.php';
        break;
    case 'reset-password':
        require __DIR__ . '/pages/reset-password.php';
        break;
    case 'change-password':
        require_auth();
        require __DIR__ . '/pages/change-password.php';
        break;
    case 'sell':
    case 'add':
        require_auth();
        require __DIR__ . '/pages/sell.php';
        break;
    case 'profile':
        require_auth();
        require __DIR__ . '/pages/profile.php';
        break;
    case 'browse':
    case 'sort':
        require __DIR__ . '/pages/browse.php';
        break;
    case 'details':
        require __DIR__ . '/pages/details.php';
        break;
    case 'payment':
        require_auth();
        require __DIR__ . '/pages/payment.php';
        break;
    case 'cart':
        require_auth();
        $cartItems = [];
        foreach ($_SESSION['cart'] as $cartId) {
            $cartItems[] = resolve_listing((int)$cartId);
        }
        require __DIR__ . '/pages/cart.php';
        break;
    case 'swap':
        require_auth();
        require __DIR__ . '/pages/swap.php';
        break;
    case 'chat':
        require_auth();
        require __DIR__ . '/pages/chat.php';
        break;
    case 'wishlist':
        require_auth();
        require __DIR__ . '/pages/wishlist.php';
        break;
    case 'purchase-history':
        require_auth();
        require __DIR__ . '/pages/purchase-history.php';
        break;
    case 'swap-requests':
        require_auth();
        require __DIR__ . '/pages/swap-requests.php';
        break;
    case 'messages':
        require_auth();
        require __DIR__ . '/pages/messages.php';
        break;
    case 'map':
        require __DIR__ . '/pages/map.php';
        break;
    case 'settings':
        require_auth();
        require __DIR__ . '/pages/settings.php';
        break;
    case 'admin':
        require_auth();
        require __DIR__ . '/pages/admin.php';
        break;
    case 'admin-products':
        require_auth();
        require __DIR__ . '/pages/admin-products.php';
        break;
    default:
        require __DIR__ . '/pages/home.php';
}

require __DIR__ . '/partials/footer.php';
