<?php
// app/views/dashboard/tech.php
// Data comes from DashboardController::tech()
// Expected variables:
//   $tech (name,email,role)
//   $complaints (array of complaint rows assigned to this tech)
//   $selectedId (int)
//   $selectedComplaint (complaint row or null)
//   $filterStatus (string)

function statusLabel(string $status): string
{
    return match ($status) {
        'open' => 'Open',
        'assigned' => 'Assigned',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        default => ucfirst($status),
    };
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tech Dashboard</title>

    <link rel="stylesheet" href="/customer-complaint-tracking-system/public/assets/css/dashboard.css">
</head>

<body>
    <main class="dash">

        <!-- LEFT COLUMN -->
        <aside>
            <!-- Profile -->
            <section class="profile-card">
                <div class="avatar">
                    <?php
                    $initials = '';
                    if (!empty($tech['name'])) {
                        $parts = preg_split('/\s+/', trim($tech['name']));
                        $initials = strtoupper(substr($parts[0] ?? 'T', 0, 1) . substr($parts[1] ?? '', 0, 1));
                    }
                    echo htmlspecialchars($initials ?: 'T');
                    ?>
                </div>

                <div class="name"><?php echo htmlspecialchars($tech['name'] ?? ''); ?></div>

                <div class="meta">
                    <div class="meta-row">
                        <span class="meta-label">Email</span>
                        <span class="meta-value"><?php echo htmlspecialchars($tech['email'] ?? ''); ?></span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Role</span>
                        <span class="meta-value"><?php echo htmlspecialchars($tech['role'] ?? 'Technician'); ?></span>
                    </div>
                </div>

                <a class="btn secondary" href="index.php?action=changePassword">Change Password</a>
            </section>

            <!-- Assigned complaints -->
            <section class="work-card sidebar-section">
                <div class="work-header">
                    <h1>Assigned Complaints</h1>
                </div>
                <p class="subtext">Select a ticket to work.</p>

                <form method="GET" action="index.php" class="form">
                    <input type="hidden" name="action" value="techDashboard">
                    <input type="hidden" name="complaint_id" value="<?php echo htmlspecialchars((string)($selectedId ?? 0)); ?>">

                    <div class="field">
                        <label class="field-label" for="status">Filter</label>
                        <select class="select" id="status" name="status">
                            <option value="" <?php echo ($filterStatus ?? '') === '' ? 'selected' : ''; ?>>All</option>
                            <option value="open" <?php echo ($filterStatus ?? '') === 'open' ? 'selected' : ''; ?>>Open</option>
                            <option value="assigned" <?php echo ($filterStatus ?? '') === 'assigned' ? 'selected' : ''; ?>>Assigned</option>
                            <option value="in_progress" <?php echo ($filterStatus ?? '') === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                            <option value="resolved" <?php echo ($filterStatus ?? '') === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                        </select>
                    </div>

                    <div class="actions">
                        <button class="btn secondary" type="submit">Apply</button>
                    </div>
                </form>

                <div class="complaint-list queue-scroll">
                    <?php if (empty($complaints)): ?>
                        <div class="complaint-card">
                            <div class="details">No complaints assigned.</div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($complaints as $c): ?>
                            <?php
                            if (($filterStatus ?? '') !== '' && ($c['status'] ?? '') !== ($filterStatus ?? '')) {
                                continue;
                            }

                            $cid = (int)($c['complaint_id'] ?? 0);
                            $active = ($cid === (int)($selectedId ?? 0));

                            $badgeStatusClass = match ($c['status'] ?? 'open') {
                                'open' => 'open',
                                'assigned' => 'open',
                                'in_progress' => 'in-progress',
                                'resolved' => 'closed',
                                default => 'open',
                            };

                            $details = trim((string)($c['details'] ?? ''));
                            $title = $details !== '' ? $details : 'Complaint';
                            $titleShort = (mb_strlen($title) > 44) ? (mb_substr($title, 0, 44) . '…') : $title;
                            ?>
                            <div class="complaint-card <?php echo $active ? 'active' : ''; ?>">
                                <div class="card-top">
                                    <span class="product">#<?php echo htmlspecialchars((string)$cid); ?></span>
                                    <span class="badge status <?php echo htmlspecialchars($badgeStatusClass); ?>">
                                        <?php echo htmlspecialchars(statusLabel((string)($c['status'] ?? 'open'))); ?>
                                    </span>
                                </div>

                                <div class="details"><?php echo htmlspecialchars($titleShort); ?></div>

                                <div class="card-bottom">
                                    <span class="subtext"></span>
                                    <div class="actions">
                                        <a class="btn secondary"
                                            href="index.php?action=techDashboard&complaint_id=<?php echo urlencode((string)$cid); ?>&status=<?php echo urlencode((string)($filterStatus ?? '')); ?>">
                                            Open
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </aside>

        <!-- RIGHT COLUMN -->
        <section class="work-card">
            <div class="topbar">
                <div class="topbar-title">
                    <h1>Technician Dashboard</h1>
                    <p class="subtext">Review, note, update status, and resolve assigned complaints.</p>
                </div>
                <div class="topbar-actions">
                    <span class="badge role">Tech</span>
                    <a class="btn secondary" href="index.php?action=logout">Logout</a>
                </div>
            </div>

            <?php if (!$selectedComplaint): ?>
                <div class="complaint-card">
                    <div class="details">No complaint selected (or you don’t have access to that complaint).</div>
                </div>
            <?php else: ?>
                <?php
                $selectedBadgeStatusClass = match ($selectedComplaint['status'] ?? 'open') {
                    'open' => 'open',
                    'assigned' => 'open',
                    'in_progress' => 'in-progress',
                    'resolved' => 'closed',
                    default => 'open',
                };

                $resolutionDate = $selectedComplaint['complaint_resolution_date'] ?? null;
                ?>
                <!-- Summary strip -->
                <div class="summary">
                    <div class="summary-item">
                        <span class="summary-label">Ticket</span>
                        <span class="summary-value">#<?php echo htmlspecialchars((string)$selectedComplaint['complaint_id']); ?></span>
                    </div>

                    <div class="summary-item">
                        <span class="summary-label">Status</span>
                        <span class="badge status <?php echo htmlspecialchars($selectedBadgeStatusClass); ?>">
                            <?php echo htmlspecialchars(statusLabel((string)($selectedComplaint['status'] ?? 'open'))); ?>
                        </span>
                    </div>

                    <div class="summary-item">
                        <span class="summary-label">Resolution Date</span>
                        <span class="summary-value">
                            <?php echo $resolutionDate ? htmlspecialchars((string)$resolutionDate) : '—'; ?>
                        </span>
                    </div>
                </div>

                <!-- Customer complaint (read-only) -->
                <div class="complaint-card">
                    <div class="card-top">
                        <span class="badge type">Customer Input</span>
                        <span class="subtext">Complaint ID: <?php echo htmlspecialchars((string)$selectedComplaint['complaint_id']); ?></span>
                    </div>

                    <div class="product">
                        Complaint Type ID: <?php echo htmlspecialchars((string)($selectedComplaint['complaint_type_id'] ?? '')); ?>
                    </div>

                    <div class="details readonly">
                        <?php echo htmlspecialchars((string)($selectedComplaint['details'] ?? '')); ?>
                    </div>

                    <?php if (!empty($selectedComplaint['image_path'])): ?>
                        <div class="details">
                            <a class="btn secondary" href="<?php echo htmlspecialchars((string)$selectedComplaint['image_path']); ?>" target="_blank" rel="noopener">
                                View Uploaded Image
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Technician update -->
                <div class="complaint-card">
                    <div class="card-top">
                        <span class="badge type">Technician Update</span>
                        <span class="subtext">Resolution notes required to resolve</span>
                    </div>

                    <?php if (!empty($_SESSION['flash_error'])): ?>
                        <div class="details" style="color:#b91c1c;">
                            <?php
                            echo htmlspecialchars($_SESSION['flash_error']);
                            unset($_SESSION['flash_error']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?action=updateComplaint" class="form">
                        <input type="hidden" name="complaint_id" value="<?php echo htmlspecialchars((string)$selectedComplaint['complaint_id']); ?>">

                        <div class="field">
                            <label class="field-label" for="technician_notes">Technician Notes / Analysis</label>
                            <textarea class="textarea" id="technician_notes" name="technician_notes" rows="5"
                                placeholder="Enter investigation steps, findings, and internal notes..."><?php
                                                                                                            echo htmlspecialchars((string)($selectedComplaint['technician_notes'] ?? ''));
                                                                                                            ?></textarea>
                        </div>

                        <div class="grid-2">
                            <div class="field">
                                <label class="field-label" for="status_update">Status</label>
                                <select class="select" id="status_update" name="status">
                                    <?php $currentStatus = (string)($selectedComplaint['status'] ?? 'open'); ?>
                                    <option value="open" <?php echo $currentStatus === 'open' ? 'selected' : ''; ?>>Open</option>
                                    <option value="assigned" <?php echo $currentStatus === 'assigned' ? 'selected' : ''; ?>>Assigned</option>
                                    <option value="in_progress" <?php echo $currentStatus === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="resolved" <?php echo $currentStatus === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                </select>
                            </div>

                            <div class="field">
                                <label class="field-label" for="resolved_at">Resolution Date</label>
                                <input class="input" id="resolved_at" type="text"
                                    value="<?php echo $resolutionDate ? htmlspecialchars((string)$resolutionDate) : ''; ?>"
                                    placeholder="Auto-set when resolved" disabled>
                            </div>
                        </div>

                        <div class="field">
                            <label class="field-label" for="resolution_notes">
                                Resolution Notes <span class="required">*</span>
                            </label>
                            <textarea class="textarea" id="resolution_notes" name="resolution_notes" rows="5"
                                placeholder="Required if setting status to Resolved. What action resolved the complaint?"><?php
                                                                                                                            echo htmlspecialchars((string)($selectedComplaint['resolution_notes'] ?? ''));
                                                                                                                            ?></textarea>
                            <p class="subtext">This field is required to resolve the complaint.</p>
                        </div>

                        <div class="actions">
                            <button class="btn primary" type="submit">Save Changes</button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

        </section>
    </main>
</body>

</html>