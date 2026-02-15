<?php
// app/views/admin/open_complaints.php
$flashError = $_SESSION['flash_error'] ?? '';
$flashSuccess = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_error'], $_SESSION['flash_success']);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Open Complaints</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/customer-complaint-tracking-system/public/assets/css/dashboard.css">
</head>

<body>
    <main class="dash">
        <section class="work-card" style="grid-column: 1 / -1;">
            <div class="topbar">
                <div class="topbar-title">
                    <h1>Open Complaints</h1>
                    <p class="subtext">All complaints that are not resolved, including assigned technician (if any).</p>
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
                <?php if (empty($complaints)): ?>
                    <div class="details">No open complaints found.</div>
                <?php else: ?>
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Status</th>
                                    <th>Type</th>
                                    <th>Customer</th>
                                    <th>Technician</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($complaints as $c): ?>
                                    <tr>
                                        <td>#<?php echo htmlspecialchars((string)$c['complaint_id']); ?></td>
                                        <td><?php echo htmlspecialchars((string)$c['status']); ?></td>
                                        <td><?php echo htmlspecialchars((string)$c['complaint_type_name']); ?></td>
                                        <td><?php echo htmlspecialchars((string)$c['customer_name']); ?></td>
                                        <td>
                                            <?php echo $c['tech_id'] ? htmlspecialchars((string)$c['tech_name']) : '<span class="subtext">Unassigned</span>'; ?>
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