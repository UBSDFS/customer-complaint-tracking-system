<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Customer Dashboard</title>

    <!-- Use a dedicated dashboard stylesheet -->
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>

<body>
    <main class="dash">

        <!-- LEFT: Customer Profile / Quick Info -->
        <aside class="profile-card">
            <div class="avatar" aria-hidden="true">PFP</div>

            <h2 class="name">
                <?= htmlspecialchars(($user['firstName'] ?? '') . ' ' . ($user['lastName'] ?? '')) ?>
            </h2>

            <div class="meta">
                <div class="meta-row">
                    <span class="meta-label">Email</span>
                    <span class="meta-value"><?= htmlspecialchars($user['email'] ?? '') ?></span>
                </div>

                <div class="meta-row">
                    <span class="meta-label">Phone</span>
                    <span class="meta-value"><?= htmlspecialchars($user['phoneNumber'] ?? '') ?></span>
                </div>

                <div class="meta-row">
                    <span class="meta-label">Status</span>
                    <span class="badge role">Customer</span>
                </div>
            </div>

            <!-- Future -->
            <a class="btn secondary" href="index.php?action=profile">Edit Profile</a>
        </aside>

        <!-- RIGHT: Complaints Work Area -->
        <section class="work-card">
            <header class="work-header">
                <div>
                    <h1>Your Complaints</h1>
                    <p class="subtext">View open complaints or submit a new one.</p>
                </div>

                <a class="btn primary" href="index.php?action=createComplaint">
                    + New Complaint
                </a>
            </header>

            <!-- Optional summary (placeholder until DB) -->
            <div class="summary">
                <div class="summary-item">
                    <span class="summary-label">Open</span>
                    <span class="summary-value"><?= (int)($summary['open'] ?? 0) ?></span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Closed</span>
                    <span class="summary-value"><?= (int)($summary['closed'] ?? 0) ?></span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Total</span>
                    <span class="summary-value"><?= (int)($summary['total'] ?? 0) ?></span>
                </div>
            </div>

            <?php if (empty($complaints)): ?>
                <div class="empty-state">
                    <h3>No complaints yet</h3>
                    <p>When you submit a complaint, it will appear here with status updates.</p>
                    <a class="btn primary" href="index.php?action=createComplaint">Submit your first complaint</a>
                </div>
            <?php else: ?>

                <div class="complaint-list">
                    <?php foreach ($complaints as $c): ?>
                        <?php
                        $type    = $c['type'] ?? 'Other';
                        $product = $c['product'] ?? 'Unknown product';
                        $status  = $c['status'] ?? 'Open';
                        $details = $c['details'] ?? '';
                        $id      = $c['complaint_id'] ?? null;

                        $statusClass = strtolower($status) === 'closed' ? 'closed' : 'open';
                        $img = $c['image'] ?? ''; // later: filename/path from DB
                        ?>

                        <article class="complaint-card">
                            <div class="card-top">
                                <span class="badge type"><?= htmlspecialchars($type) ?></span>
                                <span class="badge status <?= $statusClass ?>">
                                    <?= htmlspecialchars($status) ?>
                                </span>
                            </div>

                            <h3 class="product"><?= htmlspecialchars($product) ?></h3>

                            <?php if (!empty($details)): ?>
                                <p class="details"><?= htmlspecialchars($details) ?></p>
                            <?php endif; ?>

                            <div class="card-bottom">
                                <div class="thumb">
                                    <?php if (!empty($img)): ?>
                                        <!-- later: use a real public uploads path -->
                                        <img src="<?= htmlspecialchars($img) ?>" alt="Complaint image">
                                    <?php else: ?>
                                        <span class="thumb-placeholder">No Image</span>
                                    <?php endif; ?>
                                </div>

                                <div class="actions">
                                    <a class="link" href="index.php?action=viewComplaint&id=<?= urlencode((string)$id) ?>">
                                        View Details
                                    </a>

                                    <?php if (strtolower($status) !== 'closed'): ?>
                                        <a class="link" href="index.php?action=editComplaint&id=<?= urlencode((string)$id) ?>">
                                            Edit
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>

                    <?php endforeach; ?>
                </div>

            <?php endif; ?>
        </section>

    </main>
</body>

</html>






<!--
|--------------------------------------------------------------------------
| TODO – Customer Dashboard
|--------------------------------------------------------------------------
| Phase: UI Shell & Planning
|
| Profile Panel:
| - [ ] Display customer name from session
| - [ ] Display email / phone
| - [ ] Add profile image placeholder
| - [ ] Wire "Edit Profile" button (future route)
|
| Complaints Panel:
| - [ ] Add "New Complaint" primary button
| - [ ] Render complaint summary cards
| - [ ] Show complaint type, product, and status
| - [ ] Add empty-state message (no complaints yet)
|
| Complaint Card:
| - [ ] Add status badge (Open / Closed)
| - [ ] Add "View Details" link
| - [ ] Allow edit only if status = Open
|
| Complaint Creation (Next Sprint):
| - [ ] Build create complaint form view
| - [ ] Add complaint type dropdown
| - [ ] Add product field
| - [ ] Add description textarea
| - [ ] Add image upload field
|
| Notes:
| - No database calls in this view
| - All complaint data will be injected by controller
|--------------------------------------------------------------------------
*/
?>

-->