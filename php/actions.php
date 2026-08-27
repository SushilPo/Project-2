<?php
$action = $_POST['action'] ?? '';

try {
    if ($action === 'register') {
        $name = trim((string)($_POST['name'] ?? ''));
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $password = (string)($_POST['password'] ?? '');
        if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            throw new RuntimeException('Enter a name, valid email and password of at least 8 characters.');
        }
        $statement = db()->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
        $statement->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
        $_SESSION['user'] = ['id' => (int)db()->lastInsertId(), 'name' => $name, 'email' => $email];
        flash('Welcome to ThriftWear.');
        redirect('?page=home');
    }

    if ($action === 'login') {
        $statement = db()->prepare('SELECT id, name, email, password_hash FROM users WHERE email = ?');
        $statement->execute([strtolower(trim((string)($_POST['email'] ?? '')))]);
        $account = $statement->fetch();
        if (!$account || !password_verify((string)($_POST['password'] ?? ''), $account['password_hash'])) {
            throw new RuntimeException('Invalid email or password.');
        }
        unset($account['password_hash']);
        $_SESSION['user'] = $account;
        db()->prepare('INSERT INTO login_history (user_id, ip_address, login_method) VALUES (?, ?, ?)')->execute([$account['id'], $_SERVER['REMOTE_ADDR'] ?? null, 'password']);
        redirect('?page=home');
    }

    if ($action === 'logout') {
        $_SESSION = [];
        session_destroy();
        redirect('?page=home');
    }

    if ($action === 'create_listing') {
        $owner = require_auth();
        $title = trim((string)($_POST['title'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $price = filter_var($_POST['price'] ?? null, FILTER_VALIDATE_FLOAT);
        $category = (string)($_POST['category'] ?? '');
        $size = trim((string)($_POST['size'] ?? ''));
        $condition = (string)($_POST['condition'] ?? '');
        $image = trim((string)($_POST['image_url'] ?? ''));
        $categories = ['Women', 'Men', 'Shoes', 'Bags'];
        $conditions = ['Like new', 'Excellent', 'Good', 'Fair'];

        if (strlen($title) < 3 || $price === false || $price < 0 || !in_array($category, $categories, true) || !$size || !in_array($condition, $conditions, true)) {
            throw new RuntimeException('Complete all listing fields with valid values.');
        }

        if (isset($_FILES['listing_photo']) && $_FILES['listing_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            $photo = $_FILES['listing_photo'];
            if ($photo['error'] !== UPLOAD_ERR_OK || $photo['size'] > 5 * 1024 * 1024) {
                throw new RuntimeException('Choose an image smaller than 5 MB.');
            }
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($photo['tmp_name']);
            $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
            if (!isset($extensions[$mime]) || @getimagesize($photo['tmp_name']) === false) {
                throw new RuntimeException('Choose a valid JPG, PNG, GIF or WEBP image.');
            }
            $uploadDirectory = __DIR__ . '/assets/uploads';
            if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true)) {
                throw new RuntimeException('Photo storage is unavailable.');
            }
            $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
            if (!move_uploaded_file($photo['tmp_name'], $uploadDirectory . '/' . $filename)) {
                throw new RuntimeException('The photo could not be saved.');
            }
            $image = 'assets/uploads/' . $filename;
        }

        $statement = db()->prepare('INSERT INTO listings (user_id, title, description, price, category, size, item_condition, swap_available, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $statement->execute([$owner['id'], $title, $description, $price, $category, $size, $condition, isset($_POST['swap_available']) ? 1 : 0, $image]);
        flash('Your listing is live.');
        redirect('?page=home');
    }

    if ($action === 'delete_listing') {
        $owner = require_auth();
        $statement = db()->prepare('DELETE FROM listings WHERE id = ? AND user_id = ?');
        $statement->execute([(int)($_POST['id'] ?? 0), $owner['id']]);
        $deleted = $statement->rowCount() > 0;
        flash($deleted ? 'Listing deleted.' : 'Listing was not found.', $deleted ? 'success' : 'error');
        redirect('?page=profile');
    }

    if ($action === 'place_order') {
        $buyer = require_auth();
        $listingId = (int)($_POST['listing_id'] ?? 0);
        $fullName = trim((string)($_POST['full_name'] ?? ''));
        $address = trim((string)($_POST['address'] ?? ''));
        $city = trim((string)($_POST['city'] ?? ''));
        $postcode = trim((string)($_POST['postcode'] ?? ''));
        $amount = filter_var($_POST['amount'] ?? null, FILTER_VALIDATE_FLOAT);
        $cardNumber = preg_replace('/\D/', '', (string)($_POST['card_number'] ?? ''));
        $cvc = preg_replace('/\D/', '', (string)($_POST['cvc'] ?? ''));

        if (strlen($fullName) < 2 || !$address || !$city || !$postcode || $amount === false || $amount <= 0 || strlen($cardNumber) < 12 || strlen($cvc) < 3) {
            throw new RuntimeException('Complete all shipping and payment fields with valid values.');
        }

        $statement = db()->prepare('INSERT INTO orders (listing_id, buyer_id, full_name, address, city, postcode, amount) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $statement->execute([$listingId, $buyer['id'], $fullName, $address, $city, $postcode, $amount]);
        flash('Payment successful — your order is on its way!');
        redirect('?page=home');
    }

    if ($action === 'add_to_cart') {
        require_auth();
        $listingId = (int)($_POST['listing_id'] ?? 0);
        if ($listingId < 1) {
            throw new RuntimeException('This item is not available to add to your cart.');
        }
        $_SESSION['cart'] ??= [];
        if (!in_array($listingId, $_SESSION['cart'], true)) {
            $_SESSION['cart'][] = $listingId;
        }
        flash('Item added to your cart.');
        redirect('?page=details&id=' . $listingId);
    }

    if ($action === 'remove_from_cart') {
        require_auth();
        $listingId = (int)($_POST['listing_id'] ?? 0);
        $_SESSION['cart'] = array_values(array_filter($_SESSION['cart'] ?? [], static fn (int $id): bool => $id !== $listingId));
        flash('Item removed from your cart.');
        redirect('?page=cart');
    }

    if ($action === 'toggle_wishlist') {
        $user = require_auth();
        $listingId = (int)($_POST['listing_id'] ?? 0);
        $back = (string)($_POST['back'] ?? '?page=home');
        $existing = db()->prepare('SELECT id FROM wishlists WHERE user_id = ? AND listing_id = ?');
        $existing->execute([$user['id'], $listingId]);
        if ($existing->fetch()) {
            db()->prepare('DELETE FROM wishlists WHERE user_id = ? AND listing_id = ?')->execute([$user['id'], $listingId]);
            flash('Removed from saved items.');
        } else {
            db()->prepare('INSERT INTO wishlists (user_id, listing_id) VALUES (?, ?)')->execute([$user['id'], $listingId]);
            flash('Saved to your wishlist.');
        }
        redirect($back);
    }

    if ($action === 'update_settings') {
        $user = require_auth();
        $allowed = ['dark_mode', 'push_notifications', 'swap_alerts', 'location_enabled', 'profile_public', 'email_notifications'];
        $fields = array_intersect(array_map('trim', explode(',', (string)($_POST['fields'] ?? ''))), $allowed);
        $updates = [];
        $values = [];
        foreach ($fields as $field) {
            $updates[] = "$field = ?";
            $values[] = isset($_POST[$field]) ? 1 : 0;
        }
        if ($updates) {
            $values[] = $user['id'];
            db()->prepare('UPDATE users SET ' . implode(', ', $updates) . ' WHERE id = ?')->execute($values);
            flash('Settings updated.');
        }
        redirect('?page=settings');
    }

    if ($action === 'change_password') {
        $user = require_auth();
        $current = (string)($_POST['current_password'] ?? '');
        $new = (string)($_POST['new_password'] ?? '');
        $statement = db()->prepare('SELECT password_hash FROM users WHERE id = ?');
        $statement->execute([$user['id']]);
        $row = $statement->fetch();
        if (!$row || !password_verify($current, $row['password_hash'])) {
            throw new RuntimeException('Current password is incorrect.');
        }
        if (strlen($new) < 8) {
            throw new RuntimeException('New password must be at least 8 characters.');
        }
        db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([password_hash($new, PASSWORD_DEFAULT), $user['id']]);
        flash('Password updated.');
        redirect('?page=settings');
    }

    if ($action === 'request_password_reset') {
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $statement = db()->prepare('SELECT id FROM users WHERE email = ?');
        $statement->execute([$email]);
        if (!$statement->fetch()) {
            throw new RuntimeException('No account found with that email.');
        }
        redirect('?page=reset-password&email=' . urlencode($email));
    }

    if ($action === 'reset_password') {
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $password = (string)($_POST['password'] ?? '');
        if (strlen($password) < 8) {
            throw new RuntimeException('Password must be at least 8 characters.');
        }
        $statement = db()->prepare('UPDATE users SET password_hash = ? WHERE email = ?');
        $statement->execute([password_hash($password, PASSWORD_DEFAULT), $email]);
        if ($statement->rowCount() === 0) {
            throw new RuntimeException('No account found with that email.');
        }
        flash('Password reset. Sign in with your new password.');
        redirect('?page=login');
    }

    if ($action === 'send_swap_request') {
        $user = require_auth();
        $listingId = (int)($_POST['listing_id'] ?? 0);
        $message = trim((string)($_POST['message'] ?? ''));
        if (strlen($message) < 3) {
            throw new RuntimeException('Add a short message to your swap request.');
        }
        db()->prepare('INSERT INTO swap_requests (listing_id, requester_id, message) VALUES (?, ?, ?)')->execute([$listingId, $user['id'], $message]);
        flash('Swap request sent!');
        redirect('?page=details&id=' . $listingId);
    }

    if ($action === 'send_message') {
        $user = require_auth();
        $listingId = (int)($_POST['listing_id'] ?? 0);
        $body = trim((string)($_POST['body'] ?? ''));
        if ($body === '') {
            throw new RuntimeException('Type a message first.');
        }
        db()->prepare('INSERT INTO messages (listing_id, sender_id, body) VALUES (?, ?, ?)')->execute([$listingId, $user['id'], substr($body, 0, 1000)]);
        redirect('?page=chat&id=' . $listingId);
    }

    if ($action === 'toggle_listing_status') {
        $owner = require_auth();
        $listingId = (int)($_POST['id'] ?? 0);
        $statement = db()->prepare('SELECT status FROM listings WHERE id = ? AND user_id = ?');
        $statement->execute([$listingId, $owner['id']]);
        $row = $statement->fetch();
        if ($row) {
            $newStatus = $row['status'] === 'approved' ? 'pending' : 'approved';
            db()->prepare('UPDATE listings SET status = ? WHERE id = ? AND user_id = ?')->execute([$newStatus, $listingId, $owner['id']]);
            flash($newStatus === 'pending' ? 'Listing paused.' : 'Listing is active again.');
        }
        redirect('?page=admin-products');
    }
} catch (PDOException $exception) {
    $message = $exception->getCode() === '23000' ? 'That email is already registered.' : 'Database is unavailable. Check MySQL and schema.sql.';
    flash($message, 'error');
    redirect('?page=' . ($action === 'login' || $action === 'register' ? $action : 'home'));
} catch (RuntimeException $exception) {
    flash($exception->getMessage(), 'error');
    $back = 'sell';
    if ($action === 'login' || $action === 'register') {
        $back = $action;
    } elseif ($action === 'place_order') {
        $back = 'payment&id=' . (int)($_POST['listing_id'] ?? 0);
    } elseif ($action === 'add_to_cart') {
        $back = 'details&id=' . (int)($_POST['listing_id'] ?? 0);
    } elseif ($action === 'remove_from_cart') {
        $back = 'cart';
    } elseif ($action === 'request_password_reset') {
        $back = 'forgot-password';
    } elseif ($action === 'reset_password') {
        $back = 'reset-password&email=' . urlencode((string)($_POST['email'] ?? ''));
    } elseif ($action === 'change_password') {
        $back = 'change-password';
    } elseif ($action === 'send_swap_request') {
        $back = 'swap&id=' . (int)($_POST['listing_id'] ?? 0);
    } elseif ($action === 'send_message') {
        $back = 'chat&id=' . (int)($_POST['listing_id'] ?? 0);
    }
    redirect('?page=' . $back);
}
