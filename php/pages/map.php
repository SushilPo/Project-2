<?php
$nearby = array_slice($listings, 0, 4, true);
$mapListings = array_map(static function (array $listing, int $number): array {
    return ['id' => $number, 'title' => $listing['title'], 'price' => $listing['price']];
}, $nearby, array_keys($nearby));
?>
<section class="map-page">
    <div class="sub-header"><a href="?page=home">←</a><b>Nearby Map</b><span></span></div>
    <div class="map-legend"><b><i class="you"></i> Your location</b><b><i class="pin"></i> Listings nearby</b></div>
    <div id="osm-map" class="map-canvas" aria-label="OpenStreetMap of nearby listings"></div>
    <div class="map-list">
        <?php foreach ($nearby as $number => $listing): ?>
            <a href="?page=details&id=<?= $number ?>"><img src="<?= e(image_for($listing)) ?>" alt=""><div><b><?= e($listing['title']) ?></b><small>Melbourne · <?= e($listing['item_condition']) ?></small></div><strong>$<?= e((string)$listing['price']) ?></strong><em>›</em></a>
        <?php endforeach; ?>
    </div>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        const mapListings = <?= json_encode($mapListings, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        const melbourne = { lat: -37.8136, lng: 144.9631 };

        const map = L.map('osm-map').setView([melbourne.lat, melbourne.lng], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        mapListings.forEach((listing, index) => {
            const lat = melbourne.lat + (index * 0.012);
            const lng = melbourne.lng + (index * 0.018);
            const marker = L.marker([lat, lng], { title: listing.title }).addTo(map);
            marker.on('click', () => {
                window.location.href = `?page=details&id=${listing.id}`;
            });
        });

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                const userCoords = [position.coords.latitude, position.coords.longitude];
                L.circleMarker(userCoords, {
                    radius: 8,
                    color: '#2a68c9',
                    fillColor: '#2a68c9',
                    fillOpacity: 0.9
                }).addTo(map).bindPopup('Your location');
            });
        }
    </script>
    <?php if (!$nearby): ?><p class="empty">No nearby listings yet.</p><?php endif; ?>
</section>
