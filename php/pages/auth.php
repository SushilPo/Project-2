<section class="auth">
    <div class="auth-hero">
        <p>SUSTAINABLE FASHION MARKETPLACE</p>
        <h1><?= $page === 'register' ? 'Join ThriftWear' : 'Welcome back' ?></h1>
        <span><?= $page === 'register' ? 'Fashion that is good for you and the planet.' : 'Sign in to your ThriftWear account.' ?></span>
    </div>
    <form method="post" class="form">
        <input type="hidden" name="action" value="<?= e($page) ?>">
        <?php if ($page === 'register'): ?>
            <label>Full name<input name="name" required placeholder="Mia Chen"></label>
        <?php endif; ?>
        <label>Email address<input name="email" type="email" required placeholder="you@email.com"></label>
        <label>Password<input name="password" type="password" required minlength="8" placeholder="********"></label>
        <?php if ($page === 'login'): ?><a class="forgot" href="?page=forgot-password">Forgot password?</a><?php endif; ?>
        <button class="primary"><?= $page === 'register' ? 'Create account' : 'Sign in' ?></button>
        <div class="or-line">or continue with</div>
        <a class="google-button" href="?page=google-login"><span>G</span> Continue with Google</a>
        <p class="center">
            <?php if ($page === 'register'): ?>Already registered? <a href="?page=login">Sign in</a>
            <?php else: ?>New here? <a href="?page=register">Create an account</a><?php endif; ?>
        </p>
    </form>
</section>
