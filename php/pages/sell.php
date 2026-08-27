<section class="form-page">
    <a class="back" href="?page=home">← Back</a>
    <h1>List an Item</h1>
    <p>Give something you love a second life.</p>
    <form method="post" class="form" enctype="multipart/form-data">
        <input type="hidden" name="action" value="create_listing">
        <label class="photo-drop" for="listing-photo"><i class="fa-solid fa-camera" aria-hidden="true"></i><b>Add a Photo</b><small>JPG, PNG, GIF or WEBP up to 5 MB</small></label>
        <input id="listing-photo" class="file-input" name="listing_photo" type="file" accept="image/jpeg,image/png,image/gif,image/webp">
        <div class="photo-preview" aria-live="polite"></div>
        <label>Item Title<input name="title" required minlength="3" placeholder="Vintage denim jacket"></label>
        <label>Description<textarea name="description" maxlength="2000" placeholder="Tell buyers about the fit and condition..."></textarea></label>
        <div class="two">
            <label>Price (AUD)<input name="price" type="number" min="0" step="0.01" required></label>
            <label>Size<input name="size" required placeholder="M"></label>
        </div>
        <label>Category<select name="category"><option>Women</option><option>Men</option><option>Shoes</option><option>Bags</option></select></label>
        <label>Condition<select name="condition"><option>Like new</option><option>Excellent</option><option>Good</option><option>Fair</option></select></label>
        <label>Image URL<input name="image_url" type="url" placeholder="https://..."></label>
        <label class="check"><input name="swap_available" type="checkbox"> Open to Swap</label>
        <button class="primary">List Item</button>
    </form>
</section>
