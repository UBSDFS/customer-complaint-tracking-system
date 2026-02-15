<?php
// app/views/admin/employees.php

$flashError = $_SESSION['flash_error'] ?? '';
$flashSuccess = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_error'], $_SESSION['flash_success']);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Employees</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/customer-complaint-tracking-system/public/assets/css/dashboard.css">
</head>

<body>
    <main class="dash">

        <section class="work-card" style="grid-column: 1 / -1;">
            <div class="topbar">
                <div class="topbar-title">
                    <h1>Employees</h1>
                    <p class="subtext">Technicians and Administrators.</p>
                </div>
                <div class="topbar-actions">
                    <a class="btn secondary" href="index.php?action=adminDashboard">Back</a>
                    <a class="btn secondary" href="index.php?action=adminEmployeeCreate">Add Employee</a>
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
                <?php if (empty($employees)): ?>
                    <div class="details">No employees found.</div>
                    <div class="actions">
                        <a class="btn primary" href="index.php?action=adminEmployeeCreate">Add Employee</a>
                    </div>
                <?php else: ?>
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone Ext</th>
                                    <th>Level</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($employees as $e): ?>
                                    <tr>
                                        <td><?php echo (int)$e['user_id']; ?></td>
                                        <td><?php echo htmlspecialchars(trim(($e['first_name'] ?? '') . ' ' . ($e['last_name'] ?? ''))); ?></td>
                                        <td><?php echo htmlspecialchars((string)($e['email'] ?? '')); ?></td>
                                        <td><?php echo htmlspecialchars((string)($e['phone_ext'] ?? '')); ?></td>
                                        <td><?php echo htmlspecialchars((string)($e['level'] ?? $e['role'] ?? '')); ?></td>
                                        <td style="text-align:right;">
                                            <a class="btn secondary" href="index.php?action=adminEmployeeEdit&user_id=<?php echo (int)$e['user_id']; ?>">
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