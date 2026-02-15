<?php
// app/views/dashboard/admin.php

// Expect: session already validated in controller
$adminName = trim(($_SESSION['firstName'] ?? '') . ' ' . ($_SESSION['lastName'] ?? ''));
if ($adminName === '') $adminName = 'Administrator';

$flashError = $_SESSION['flash_error'] ?? '';
$flashSuccess = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_error'], $_SESSION['flash_success']);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="/customer-complaint-tracking-system/public/assets/css/dashboard.css">
</head>

<body>
    <main class="dash">

        <section class="work-card" style="grid-column: 1 / -1;">
            <div class="topbar">
                <div class="topbar-title">
                    <h1>Admin Dashboard</h1>
                    <p class="subtext">Manage users and route complaints to technicians.</p>
                </div>
                <div class="topbar-actions">
                    <span class="badge role">Admin</span>
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
                <div class="details">
                    Signed in as <strong><?php echo htmlspecialchars($adminName); ?></strong>
                    <span class="subtext"> • <?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
                </div>
            </div>
        </section>

        <!-- BOX 1: PEOPLE MANAGEMENT -->
        <section class="work-card">
            <div class="card-top">
                <span class="badge type">People Management</span>
            </div>

            <h2 style="margin-top:.5rem;">Customers & Employees</h2>
            <p class="subtext">
                View and maintain user records. Employees include technicians and administrators.
            </p>

            <div class="complaint-card" style="margin-top: 1rem;">
                <div class="details">
                    <strong>Customers</strong>
                    <p class="subtext">View the list of customers and update customer profile information.</p>
                </div>
                <div class="actions">
                    <a class="btn primary" href="index.php?action=adminCustomers">View Customers</a>
                </div>
            </div>

            <div class="complaint-card">
                <div class="details">
                    <strong>Employees</strong>
                    <p class="subtext">View employees, add a new employee, or update employee details (User ID cannot change).</p>
                </div>
                <div class="actions">
                    <a class="btn primary" href="index.php?action=adminEmployees">View Employees</a>
                    <a class="btn secondary" href="index.php?action=adminEmployeeCreate">Add Employee</a>
                </div>
            </div>
        </section>

        <!-- BOX 2: COMPLAINT OPERATIONS -->
        <section class="work-card">
            <div class="card-top">
                <span class="badge type">Complaint Operations</span>
            </div>

            <h2 style="margin-top:.5rem;">Complaints & Assignment</h2>
            <p class="subtext">
                Review open complaints and assign them to technicians. Monitor workload.
            </p>

            <div class="complaint-card" style="margin-top: 1rem;">
                <div class="details">
                    <strong>Open Complaints (All)</strong>
                    <p class="subtext">View open complaints including which technician is assigned (if any).</p>
                </div>
                <div class="actions">
                    <a class="btn primary" href="index.php?action=adminOpenComplaints">Open Complaints</a>
                </div>
            </div>

            <div class="complaint-card">
                <div class="details">
                    <strong>Unassigned Complaints</strong>
                    <p class="subtext">View open complaints with no technician assigned and assign them.</p>
                </div>
                <div class="actions">
                    <a class="btn primary" href="index.php?action=adminUnassignedComplaints">Unassigned</a>
                </div>
            </div>

            <div class="complaint-card">
                <div class="details">
                    <strong>Technician Workload</strong>
                    <p class="subtext">View technicians with count of open complaints assigned to each.</p>
                </div>
                <div class="actions">
                    <a class="btn primary" href="index.php?action=adminWorkload">Tech Workload</a>
                </div>
            </div>
        </section>

    </main>
</body>

</html>