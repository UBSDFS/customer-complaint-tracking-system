<?php
// views/dashboard/tech.php
// UI-FIRST scaffold (placeholders). Wire DB + controllers later.

// --- Placeholder "logged-in tech" profile ---
$tech = [
    'name'  => 'Technician Name',
    'email' => 'tech@example.com',
    'role'  => 'Tech',
];

// --- Placeholder assigned complaints list (left sidebar queue) ---
$assignedComplaints = [
    ['id' => 1024, 'title' => 'Duplicate charge on account', 'submitted' => '2026-02-04', 'status' => 'open'],
    ['id' => 1025, 'title' => 'App crashes on login',        'submitted' => '2026-02-05', 'status' => 'in_progress'],
    ['id' => 1026, 'title' => 'Warranty status mismatch',    'submitted' => '2026-02-01', 'status' => 'resolved'],
];

// Which complaint is "selected" (right panel)
$selectedId = isset($_GET['id']) ? (int)$_GET['id'] : $assignedComplaints[0]['id'];

// --- Placeholder selected complaint detail (customer input + tech fields) ---
$selectedComplaint = [
    'id'            => $selectedId,
    'status'        => 'open',
    'submitted_at'  => '2026-02-04 09:14',
    'customer_name' => 'J. Smith',
    'customer_email' => 'jsmith@email.com',
    'category'      => 'Billing',
    'description'   => 'I was charged twice for my subscription. Please refund the duplicate charge and confirm my account is in good standing.',
    'resolved_at'   => null, // display only; later set when resolved
];

function statusLabel(string $status): string
{
    return match ($status) {
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        default => ucfirst($status),
    };
}

function statusBadgeClass(string $status): string
{
    return match ($status) {
        'open' => 'badge-open',
        'in_progress' => 'badge-progress',
        'resolved' => 'badge-resolved',
        default => 'badge-default',
    };
}

// Optional UI filter (GET only for now)
$filterStatus = $_GET['status'] ?? '';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tech Dashboard</title>

    <!-- TODO: Use your project stylesheet -->
    <!-- <link rel="stylesheet" href="/public/assets/css/styles.css"> -->

    <!-- Minimal layout helpers (remove once you have real CSS) -->
    <link rel="stylesheet" href="/public/assets/css/layout.css">
</head>

<body>
    <header class="app-header">
        <div class="container header-row">
            <div>
                <h1 class="page-title">Technician Dashboard</h1>
                <div class="muted" style="font-size:13px;">Work assigned complaints: review, note, update status, resolve.</div>
            </div>

            <div class="topbar-links">
                <!-- TODO: point to real routes -->
                <a class="btn btn-secondary" href="#">Logout</a>
            </div>
        </div>
    </header>

    <main class="container">
        <section class="layout">
            <!--LEFT SIDEBAR  -->
            <aside class="stack">
                <!-- Tech Profile -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Technician Profile</h2>
                        <p class="card-subtitle">Account + quick actions</p>
                    </div>

                    <div class="kv" style="margin-bottom:10px;">
                        <div class="k">Name</div>
                        <div class="v"><?php echo htmlspecialchars($tech['name']); ?></div>
                    </div>

                    <div class="kv" style="margin-bottom:10px;">
                        <div class="k">Email</div>
                        <div class="v"><?php echo htmlspecialchars($tech['email']); ?></div>
                    </div>

                    <div class="kv" style="margin-bottom:12px;">
                        <div class="k">Role</div>
                        <div class="v"><?php echo htmlspecialchars($tech['role']); ?></div>
                    </div>

                    <!-- Change Password -->
                    <a class="btn btn-primary btn-block" href="#">
                        Change Password
                    </a>
                </div>

                <!-- Assigned Complaints Queue -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Assigned Complaints</h2>
                        <p class="card-subtitle">Select a ticket to work</p>
                    </div>

                    <!-- Filter (UI only for now) -->
                    <form method="GET" action="" style="display:flex; gap:10px; margin-bottom:12px;">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars((string)$selectedId); ?>">
                        <div style="flex:1;">
                            <label for="status" style="display:block; margin-bottom:6px;">Filter</label>
                            <select id="status" name="status">
                                <option value="" <?php echo $filterStatus === '' ? 'selected' : ''; ?>>All</option>
                                <option value="open" <?php echo $filterStatus === 'open' ? 'selected' : ''; ?>>Open</option>
                                <option value="in_progress" <?php echo $filterStatus === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="resolved" <?php echo $filterStatus === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                            </select>
                        </div>
                        <div style="align-self:flex-end;">
                            <button class="btn btn-secondary" type="submit">Apply</button>
                        </div>
                    </form>

                    <div class="queue" aria-label="Assigned complaints list">
                        <?php foreach ($assignedComplaints as $c): ?>
                            <?php
                            if ($filterStatus !== '' && $c['status'] !== $filterStatus) continue;
                            $active = ((int)$c['id'] === (int)$selectedId);
                            ?>
                            <div class="queue-item <?php echo $active ? 'active' : ''; ?>">
                                <div class="queue-top">
                                    <div style="font-weight:700;">#<?php echo htmlspecialchars((string)$c['id']); ?></div>
                                    <span class="badge <?php echo statusBadgeClass($c['status']); ?>">
                                        <?php echo statusLabel($c['status']); ?>
                                    </span>
                                </div>
                                <div class="muted" style="font-size:13px; margin-top:6px;">
                                    <?php echo htmlspecialchars($c['title']); ?>
                                </div>
                                <div class="muted" style="font-size:12px; margin-top:6px;">
                                    Submitted: <?php echo htmlspecialchars($c['submitted']); ?>
                                </div>
                                <div style="margin-top:10px;">
                                    <a class="btn btn-secondary btn-block" href="?id=<?php echo urlencode((string)$c['id']); ?>&status=<?php echo urlencode((string)$filterStatus); ?>">
                                        Open Ticket
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </aside>

            <!--RIGHT SIDE/WORK SPACE -->
            <section class="stack">
                <!-- Selected Ticket Header Strip -->
                <div class="card">
                    <div class="detail-grid">
                        <div class="kv">
                            <div class="k">Ticket</div>
                            <div class="v">#<?php echo htmlspecialchars((string)$selectedComplaint['id']); ?></div>
                        </div>
                        <div class="kv">
                            <div class="k">Status</div>
                            <div class="v">
                                <span class="badge <?php echo statusBadgeClass($selectedComplaint['status']); ?>">
                                    <?php echo statusLabel($selectedComplaint['status']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="kv">
                            <div class="k">Submitted</div>
                            <div class="v"><?php echo htmlspecialchars($selectedComplaint['submitted_at']); ?></div>
                        </div>
                        <div class="kv">
                            <div class="k">Resolution Date</div>
                            <div class="v">
                                <?php echo $selectedComplaint['resolved_at'] ? htmlspecialchars($selectedComplaint['resolved_at']) : '—'; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Input (read-only) -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Customer Complaint</h2>
                        <p class="card-subtitle">Read-only: what the customer entered</p>
                    </div>

                    <div class="detail-grid" style="margin-bottom:12px;">
                        <div class="kv">
                            <div class="k">Customer</div>
                            <div class="v"><?php echo htmlspecialchars($selectedComplaint['customer_name']); ?></div>
                        </div>
                        <div class="kv">
                            <div class="k">Email</div>
                            <div class="v"><?php echo htmlspecialchars($selectedComplaint['customer_email']); ?></div>
                        </div>
                        <div class="kv">
                            <div class="k">Category</div>
                            <div class="v"><?php echo htmlspecialchars($selectedComplaint['category']); ?></div>
                        </div>
                        <div class="kv">
                            <div class="k">Ticket ID</div>
                            <div class="v">#<?php echo htmlspecialchars((string)$selectedComplaint['id']); ?></div>
                        </div>
                    </div>

                    <label style="display:block; margin-bottom:8px;">Complaint Description</label>
                    <div class="readonly"><?php echo htmlspecialchars($selectedComplaint['description']); ?></div>
                </div>

                <!-- Tech Update Panel (notes + status dropdown + resolution notes) -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Technician Update</h2>
                        <p class="card-subtitle">Add notes, update status, and resolve (resolution notes required)</p>
                    </div>

                    <!-- UI-only form: action will be wired later -->
                    <form method="POST" action="#">
                        <input type="hidden" name="complaint_id" value="<?php echo htmlspecialchars((string)$selectedComplaint['id']); ?>">

                        <div class="form-group">
                            <label for="tech_notes">Technician Notes / Analysis</label>
                            <textarea id="tech_notes" name="tech_notes" rows="5" placeholder="Enter investigation steps, findings, and internal notes..."></textarea>
                        </div>

                        <div class="form-group">
                            <label for="status_update">Status</label>
                            <select id="status_update" name="status">
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="resolved">Resolved</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="resolution_notes">Resolution Notes <span class="required">*</span></label>
                            <textarea id="resolution_notes" name="resolution_notes" rows="5" placeholder="Required if setting status to Resolved. What action resolved the complaint?"></textarea>
                            <div class="muted" style="font-size:12px;">
                                This field is required to resolve the complaint. Resolution date will be set automatically.
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="resolved_at">Resolution Date</label>
                            <input id="resolved_at" type="text" value="" placeholder="Auto-set when resolved" disabled>
                        </div>

                        <div class="actions">
                            <button class="btn btn-secondary" type="submit" name="save_changes">Save Changes</button>
                            <button class="btn btn-primary" type="submit" name="resolve">Resolve Complaint</button>
                        </div>
                    </form>
                </div>
            </section>
        </section>
    </main>

    <footer class="container" style="padding-top: 0;">
        <div class="muted" style="font-size:12px; padding: 16px 0;">&copy; 2026 Customer Complaint Tracking System</div>
    </footer>
</body>

</html>