<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="assets/css/login.css">
    <title>SDC342L Project Login Page</title>
</head>

<body>
    <main class="login-page">
        <section class="login-card">
            <header class="login-header">
                <h1>Login</h1>
                <p>Sign in to continue.</p>
            </header>
            <form method='POST'>
                <div class="field">
                    <label for="email">E-Mail:</label>
                    <input type="text" name="email" value="<?php echo htmlspecialchars($email) ?>"><br>
                    <?= htmlspecialchars($errors["email_error"]) ?>
                    <br>
                </div>
                <div class="field">
                    <label for="password">Password:</label>
                    <input type="password" name="password" required><br>
                    <?= htmlspecialchars($errors["password_error"]) ?><br>
                    <br>
                </div>
                <button type="submit" class="btn-primary">Login</button>

                <p class="login-footer">
                    Don’t have an account?
                    <a href="/public/index.php?action=register">Register</a>
                </p>
            </form>
        </section>
    </main>
</body>

</html>