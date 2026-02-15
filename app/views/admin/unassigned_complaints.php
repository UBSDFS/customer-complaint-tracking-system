<?php
// app/views/admin/unassigned_complaints.php

$flashError = $_SESSION['flash_error'] ?? '';
$flashSuccess = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_error'], $_SESSION['flash_success']);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Unassigned Complaints</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/customer-complaint-tracking-system/public/assets/css/dashboard.css">
</head>

<body>
    <main class="dash">

        <section class="work-card" style="grid-column: 1 / -1;">
            <div class="topbar">
                <div class="topbar-title">
                    <h1>Unassigned Complaints</h1>
                    <p class="subtext">Assign open complaints that currently have no technician.</p>
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

            <?php if (empty($techs)): ?>
                <div class="complaint-card">
                    <div class="details">
                        No technicians found. Add a technician first before assigning complaints.
                    </div>
                    <div class="actions">
                        <a class="btn primary" href="index.php?action=adminEmployeeCreate">Add Technician</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (empty($complaints)): ?>
                <div class="complaint-card">
                    <div class="details">No unassigned open complaints found.</div>
                </div>
            <?php else: ?>

                <?php foreach ($complaints as $c): ?>
                    <div class="complaint-card">
                        <div class="card-top">
                            <span class="product">Complaint #<?php echo htmlspecialchars((string)$c['complaint_id']); ?></span>
                            <span class="badge status open"><?php echo htmlspecialchars((string)$c['status']); ?></span>
                        </div>

                        <div class="details">
                            <strong><?php echo htmlspecialchars((string)($c['complaint_type_name'] ?? '')); ?></strong>
                        </div>

                        <div class="details readonly">
                            <?php echo nl2br(htmlspecialchars((string)($c['details'] ?? ''))); ?>
                        </div>

                        <form method="POST" action="index.php?action=adminAssignComplaint" class="form" style="margin-top:1rem;">
                            <input type="hidden" name="complaint_id" value="<?php echo htmlspecialchars((string)$c['complaint_id']); ?>">

                            <div class="grid-2">
                                <div class="field">
                                    <label class="field-label" for="tech_<?php echo (int)$c['complaint_id']; ?>">Assign Technician</label>
                                    <select class="select" name="tech_id" id="tech_<?php echo (int)$c['complaint_id']; ?>" <?php echo empty($techs) ? 'disabled' : ''; ?>>
                                        <option value="">-- Select --</option>
                                        <?php foreach ($techs as $t): ?>
                                            <option value="<?php echo (int)$t['user_id']; ?>">
                                                <?php
                                                $name = trim(($t['first_name'] ?? '') . ' ' . ($t['last_name'] ?? ''));
                                                $label = $name !== '' ? $name : ($t['email'] ?? ('Tech #' . $t['user_id']));
                                                echo htmlspecialchars($label);
                                                ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="field" style="display:flex; align-items:flex-end;">
                                    <button class="btn primary" type="submit" <?php echo empty($techs) ? 'disabled' : ''; ?>>
                                        Assign
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>

        </section>

    </main>
</body>

</html>