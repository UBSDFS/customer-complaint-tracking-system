<?php
// app/views/admin/workload.php

$flashError = $_SESSION['flash_error'] ?? '';
$flashSuccess = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_error'], $_SESSION['flash_success']);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Technician Workload</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/customer-complaint-tracking-system/public/assets/css/dashboard.css">
</head>

<body>
    <main class="dash">

        <section class="work-card" style="grid-column: 1 / -1;">
            <div class="topbar">
                <div class="topbar-title">
                    <h1>Technician Workload</h1>
                    <p class="subtext">Open complaint counts by technician.</p>
                </div>
                <div class="topbar-actions">
                    <a class="btn secondary" href="index.php?action=adminDashboard">Back</a>
                    <a class="btn secondary" href="index.php?action=logout">Logout</a>
                </div>
            </div>

            <?php if ($flashError): ?>
                <div class="complaint-card">
                    <div class="details" style="color:#b91c1c;"><?php echo htmlspecialchars($flashError); ?></div>
                </div>
            <?php endif; ?>

            <?php if ($flashSuccess): ?>
                <div class="complaint-card">
                    <div class="details" style="color:#065f46;"><?php echo htmlspecialchars($flashSuccess); ?></div>
                </div>
            <?php endif; ?>

            <div class="complaint-card">
                <?php if (empty($workload)): ?>
                    <div class="details">No technicians found.</div>
                    <div class="actions">
                        <a class="btn primary" href="index.php?action=adminEmployeeCreate">Add Technician</a>
                    </div>
                <?php else: ?>
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tech</th>
                                    <th>Email</th>
                                    <th style="text-align:right;">Open Complaints</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($workload as $t): ?>
                                    <tr>
                                        <td>
                                            <?php
                                            $name = trim(($t['first_name'] ?? '') . ' ' . ($t['last_name'] ?? ''));
                                            echo htmlspecialchars($name !== '' ? $name : ('Tech #' . $t['user_id']));
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars((string)($t['email'] ?? '')); ?></td>
                                        <td style="text-align:right;"><?php echo htmlspecialchars((string)($t['open_count'] ?? '0')); ?></td>
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