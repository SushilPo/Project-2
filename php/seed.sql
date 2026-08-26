USE thriftwear;

INSERT INTO users (name, email, password_hash, location) VALUES
('Mia Chen', 'mia@thriftwear.demo', '$2y$12$l/.JVFK3PNw6YoXjGP.A3e2GJUxgyUdsBQFNgAIw6eMzRQZOFv0T.', 'Melbourne'),
('Oliver Grant', 'oliver@thriftwear.demo', '$2y$12$l/.JVFK3PNw6YoXjGP.A3e2GJUxgyUdsBQFNgAIw6eMzRQZOFv0T.', 'Sydney'),
('Aisha Patel', 'aisha@thriftwear.demo', '$2y$12$l/.JVFK3PNw6YoXjGP.A3e2GJUxgyUdsBQFNgAIw6eMzRQZOFv0T.', 'Brisbane'),
('Noah Williams', 'noah@thriftwear.demo', '$2y$12$l/.JVFK3PNw6YoXjGP.A3e2GJUxgyUdsBQFNgAIw6eMzRQZOFv0T.', 'Melbourne'),
('Sofia Rossi', 'sofia@thriftwear.demo', '$2y$12$l/.JVFK3PNw6YoXjGP.A3e2GJUxgyUdsBQFNgAIw6eMzRQZOFv0T.', 'Adelaide'),
('Ethan Brooks', 'ethan@thriftwear.demo', '$2y$12$l/.JVFK3PNw6YoXjGP.A3e2GJUxgyUdsBQFNgAIw6eMzRQZOFv0T.', 'Perth'),
('Harper Lee', 'harper@thriftwear.demo', '$2y$12$l/.JVFK3PNw6YoXjGP.A3e2GJUxgyUdsBQFNgAIw6eMzRQZOFv0T.', 'Canberra')
AS incoming ON DUPLICATE KEY UPDATE name = incoming.name, location = incoming.location;

INSERT INTO listings (user_id, title, description, price, category, size, item_condition, swap_available, image_url)
SELECT u.id, seed.title, seed.description, seed.price, seed.category, seed.size, seed.item_condition, seed.swap_available, seed.image_url
FROM users u
JOIN (
    SELECT 'mia@thriftwear.demo' email, 'Linen Wrap Dress' title, 'A breathable linen dress in a soft floral print, perfect for warm weekends.' description, 42.00 price, 'Women' category, 'S' size, 'Excellent' item_condition, 1 swap_available, 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?auto=format&fit=crop&w=700&q=85' image_url
    UNION ALL SELECT 'aisha@thriftwear.demo', 'Vintage Denim Jacket', 'A structured vintage jacket with an easy oversized fit and plenty of life left.', 58.00, 'Women', 'M', 'Good', 1, 'https://images.unsplash.com/photo-1544022613-e87ca75a784a?auto=format&fit=crop&w=700&q=85'
    UNION ALL SELECT 'sofia@thriftwear.demo', 'Satin Midi Skirt', 'A timeless satin skirt with a fluid drape and elegant deep green finish.', 35.00, 'Women', 'M', 'Like new', 0, 'https://images.unsplash.com/photo-1583496661160-fb5886a13d27?auto=format&fit=crop&w=700&q=85'
    UNION ALL SELECT 'harper@thriftwear.demo', 'Cotton Weekend Shirt', 'Relaxed cotton button-up in a clean stripe, freshly laundered and ready to wear.', 24.00, 'Women', 'L', 'Excellent', 1, 'https://images.unsplash.com/photo-1605763240000-7e93b172d754?auto=format&fit=crop&w=700&q=85'
    UNION ALL SELECT 'oliver@thriftwear.demo', 'Levi 501 Straight Jeans', 'Classic straight-leg denim with a broken-in wash and no damage.', 45.00, 'Men', 'W32 L30', 'Good', 1, 'https://images.unsplash.com/photo-1542272604-787c3835535d?auto=format&fit=crop&w=700&q=85'
    UNION ALL SELECT 'noah@thriftwear.demo', 'Merino Wool Crewneck', 'Warm lightweight merino in charcoal grey, ideal for layering.', 39.00, 'Men', 'M', 'Excellent', 0, 'https://images.unsplash.com/photo-1610652492500-ded49ceeb378?auto=format&fit=crop&w=700&q=85'
    UNION ALL SELECT 'ethan@thriftwear.demo', 'Utility Overshirt', 'A durable olive overshirt with roomy pockets and a casual fit.', 49.00, 'Men', 'L', 'Like new', 1, 'https://images.unsplash.com/photo-1551488831-00ddcb6c6bd3?auto=format&fit=crop&w=700&q=85'
    UNION ALL SELECT 'harper@thriftwear.demo', 'Oxford Button Down', 'A crisp blue Oxford shirt that works for the office or weekend.', 22.00, 'Men', 'M', 'Good', 0, 'https://images.unsplash.com/photo-1596755389378-c31d21fd1273?auto=format&fit=crop&w=700&q=85'
    UNION ALL SELECT 'aisha@thriftwear.demo', 'Nike Air Max 90 White', 'Clean white sneakers with light wear and lots of everyday mileage left.', 95.00, 'Shoes', 'US 8', 'Like new', 0, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=700&q=85'
    UNION ALL SELECT 'mia@thriftwear.demo', 'Leather Ankle Boots', 'Black leather ankle boots with a comfortable low heel and grippy sole.', 72.00, 'Shoes', 'EU 39', 'Excellent', 1, 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&w=700&q=85'
    UNION ALL SELECT 'ethan@thriftwear.demo', 'Canvas High Tops', 'Classic cream canvas high tops with a little character and clean soles.', 31.00, 'Shoes', 'US 10', 'Good', 1, 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?auto=format&fit=crop&w=700&q=85'
    UNION ALL SELECT 'oliver@thriftwear.demo', 'Suede Loafers', 'Soft tan suede loafers, lightly worn and perfect for smart casual days.', 64.00, 'Shoes', 'US 9', 'Excellent', 0, 'https://images.unsplash.com/photo-1533867617858-e7b97e060509?auto=format&fit=crop&w=700&q=85'
    UNION ALL SELECT 'sofia@thriftwear.demo', 'Leather Crossbody Bag', 'A timeless everyday bag with an adjustable strap and roomy interior.', 62.00, 'Bags', 'One size', 'Good', 1, 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=700&q=85'
    UNION ALL SELECT 'noah@thriftwear.demo', 'Woven Market Tote', 'A sturdy woven tote for farmers markets, books, and sunny day trips.', 28.00, 'Bags', 'One size', 'Like new', 1, 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=700&q=85'
    UNION ALL SELECT 'harper@thriftwear.demo', 'Canvas Shoulder Bag', 'A practical neutral canvas bag with an adjustable shoulder strap.', 26.00, 'Bags', 'One size', 'Excellent', 0, 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=700&q=85'
    UNION ALL SELECT 'sofia@thriftwear.demo', 'Mini Evening Clutch', 'A compact black clutch with a simple shape for evenings out.', 34.00, 'Bags', 'One size', 'Good', 0, 'https://images.unsplash.com/photo-1566150905458-1bf1fc113f0d?auto=format&fit=crop&w=700&q=85'
) seed ON u.email = seed.email
WHERE NOT EXISTS (SELECT 1 FROM listings existing WHERE existing.title = seed.title AND existing.user_id = u.id);
