<section class="form-page">
    <a class="back" href="?page=settings">← Back</a>
    <h1>Account Security</h1>
    <p>Update the password used to sign in to ThriftWear.</p>
    <form method="post" class="form">
        <input type="hidden" name="action" value="change_password">
        <label>Current password<input name="current_password" type="password" required placeholder="********"></label>
        <label>New password<input name="new_password" type="password" required minlength="8" placeholder="********"></label>
        <button class="primary">Update password</button>
    </form>
</section>
