<section class="auth">
    <div class="auth-hero">
        <p>SUSTAINABLE FASHION MARKETPLACE</p>
        <h1>Set a new password</h1>
        <span>Demo reset — no email is sent, just choose a new password for <?= e((string)($_GET['email'] ?? '')) ?>.</span>
    </div>
    <form method="post" class="form">
        <input type="hidden" name="action" value="reset_password">
        <input type="hidden" name="email" value="<?= e((string)($_GET['email'] ?? '')) ?>">
        <label>New password<input name="password" type="password" required minlength="8" placeholder="********"></label>
        <button class="primary">Reset password</button>
        <p class="center">Changed your mind? <a href="?page=login">Sign in</a></p>
    </form>
</section>
