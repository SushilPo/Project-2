<?php
$listingId = (int)($_GET['id'] ?? 0);
$statement = db()->prepare('SELECT body, created_at FROM messages WHERE listing_id = ? AND sender_id = ? ORDER BY created_at ASC');
$statement->execute([$listingId, $user['id']]);
$myMessages = $statement->fetchAll();
?>
<section class="chat-page"><div class="sub-header"><a href="?page=details&id=<?= $listingId ?>">←</a><span class="chat-avatar">🌿</span><b>maya_thrift<small>● Online</small></b><button>☷</button></div><div class="chat-item"><img src="<?= e(image_for($product)) ?>" alt=""><b><?= e($product['title']) ?><strong>$<?= e((string)$product['price']) ?></strong></b></div><div class="bubble received">Hi! Is the dress still available?<small>10:32 AM</small></div><div class="bubble sent">Yes it is! Just washed and ready to go.<small>10:35 AM</small></div><div class="bubble received">Amazing! Would you consider swapping for a pair of white Nikes size 8?<small>10:36 AM</small></div><div class="bubble sent">I’d love that actually! Can you send me some photos?<small>10:38 AM</small></div><div class="bubble received short">Of course, sending now 📸<small>10:39 AM</small></div><?php foreach ($myMessages as $message): ?><div class="bubble sent"><?= e($message['body']) ?><small><?= e(date('g:i A', strtotime($message['created_at']))) ?></small></div><?php endforeach; ?><form class="composer" method="post"><input type="hidden" name="action" value="send_message"><input type="hidden" name="listing_id" value="<?= $listingId ?>"><input name="body" required placeholder="Type a message..."><button>➤</button></form></section>

