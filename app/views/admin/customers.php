<?php
// app/views/admin/customers.php

$flashError = $_SESSION['flash_error'] ?? '';
$flashSuccess = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_error'], $_SESSION['flash_success']);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Customers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/customer-complaint-tracking-system/public/assets/css/dashboard.css">
</head>

<body>
    <main class="dash">

        <section class="work-card" style="grid-column: 1 / -1;">
            <div class="topbar">
                <div class="topbar-title">
                    <h1>Customers</h1>
                    <p class="subtext">View and update customers in the system.</p>
                </div>
                <div class="topbar-actions">
                    <a class="btn secondary" href="index.php?action=adminDashboard">Back</a>
                    <a class="btn secondary" href="index.php?action=logout">Logout</a>
                </div>
            </div>

            <?php if ($flashError): ?>
                <div class="complaint-card">
                    <div class="details" style="color:#b91c1c;">
                        <?php echo htmlspecialchars($flashError); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($flashSuccess): ?>
                <div class="complaint-card">
                    <div class="details" style="color:#065f46;">
                        <?php echo htmlspecialchars($flashSuccess); ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="complaint-card">
                <?php if (empty($customers)): ?>
                    <div class="details">No customers found.</div>
                <?php else: ?>
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Location</th>
                                    <th>Phone</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customers as $c): ?>
                                    <tr>
                                        <td><?php echo (int)$c['user_id']; ?></td>
                                        <td><?php echo htmlspecialchars(trim(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? ''))); ?></td>
                                        <td><?php echo htmlspecialchars((string)($c['email'] ?? '')); ?></td>
                                        <td>
                                            <?php
                                            $city = $c['city'] ?? '';
                                            $state = $c['state'] ?? '';
                                            echo htmlspecialchars(trim($city . ($city && $state ? ', ' : '') . $state));
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars((string)($c['phone'] ?? '')); ?></td>
                                        <td style="text-align:right;">
                                            <a class="btn secondary" href="index.php?action=adminCustomerEdit&user_id=<?php echo (int)$c['user_id']; ?>">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

        </section>

    </main>
</body>

</html>