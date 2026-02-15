<?php
$userId = (int)$customer['user_id'];
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Edit Customer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/customer-complaint-tracking-system/public/assets/css/dashboard.css">
</head>

<body>
    <main class="dash">
        <section class="work-card" style="grid-column:1 / -1;">
            <div class="topbar">
                <div class="topbar-title">
                    <h1>Edit Customer</h1>
                    <p class="subtext">Customers live in customer_profiles.</p>
                </div>
                <div class="topbar-actions">
                    <a class="btn secondary" href="index.php?action=adminCustomers">Back</a>
                    <a class="btn secondary" href="index.php?action=logout">Logout</a>
                </div>
            </div>

            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="complaint-card">
                    <div class="details" style="color:#b91c1c;">
                        <?php echo htmlspecialchars($_SESSION['flash_error']);
                        unset($_SESSION['flash_error']); ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="complaint-card">
                <form method="POST" action="index.php?action=adminCustomerUpdate" class="form">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars((string)$userId); ?>">

                    <div class="field">
                        <label class="field-label">Email</label>
                        <input class="input" name="email" type="email" required
                            value="<?php echo htmlspecialchars($customer['email'] ?? ''); ?>">
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label class="field-label">First Name</label>
                            <input class="input" name="first_name" type="text" required
                                value="<?php echo htmlspecialchars($customer['first_name'] ?? ''); ?>">
                        </div>
                        <div class="field">
                            <label class="field-label">Last Name</label>
                            <input class="input" name="last_name" type="text" required
                                value="<?php echo htmlspecialchars($customer['last_name'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="field">
                        <label class="field-label">Street Address</label>
                        <input class="input" name="street_address" type="text" required
                            value="<?php echo htmlspecialchars($customer['street_address'] ?? ''); ?>">
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label class="field-label">City</label>
                            <input class="input" name="city" type="text" required
                                value="<?php echo htmlspecialchars($customer['city'] ?? ''); ?>">
                        </div>
                        <div class="field">
                            <label class="field-label">State</label>
                            <input class="input" name="state" type="text" maxlength="2" required
                                value="<?php echo htmlspecialchars($customer['state'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label class="field-label">ZIP</label>
                            <input class="input" name="zip" type="text" maxlength="5" required
                                value="<?php echo htmlspecialchars($customer['zip'] ?? ''); ?>">
                        </div>
                        <div class="field">
                            <label class="field-label">Phone</label>
                            <input class="input" name="phone" type="text" required
                                value="<?php echo htmlspecialchars($customer['phone'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="actions">
                        <button class="btn primary" type="submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>

</html>