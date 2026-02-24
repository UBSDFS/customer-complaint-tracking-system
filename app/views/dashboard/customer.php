<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Customer Dashboard</title>

    <link rel="stylesheet" href="/customer-complaint-tracking-system/public/assets/css/dashboard.css">
</head>

<body>
    <main class="dash">
        <aside class="profile-card">
            <div class="avatar" aria-hidden="true">
                <?php if (!empty($user['avatar_path'])): ?>
                    <img src="<?= htmlspecialchars($user['avatar_path']) ?>" alt="Profile picture"
                        style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                <?php else: ?>
                    PFP
                <?php endif; ?>
            </div>

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

            <a class="btn secondary" href="index.php?action=profile">Edit Profile</a>
            <a class="btn tertiary" href="index.php?action=logout">Logout</a>
        </aside>


        <section class="work-card">
            <header class="work-header">
                <div>
                    <h1>Your Complaints</h1>
                    <p class="subtext">View complaints or submit a new one.</p>
                </div>


                <a class="btn primary" href="index.php?action=newComplaint">
                    + New Complaint
                </a>
            </header>

            <div class="summary">
                <div class="summary-item">
                    <span class="summary-label">Open</span>
                    <span class="summary-value"><?= (int) ($summary['open'] ?? 0) ?></span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Resolved</span>
                    <span class="summary-value"><?= (int) ($summary['resolved'] ?? 0) ?></span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Total</span>
                    <span class="summary-value"><?= (int) ($summary['total'] ?? 0) ?></span>
                </div>
            </div>

            <?php if (empty($complaints)): ?>
                <div class="empty-state">
                    <h3>No complaints yet</h3>
                    <p>When you submit a complaint, it will appear here with status updates.</p>
                </div>
            <?php else: ?>

                <div class="complaint-list">
                    <?php foreach ($complaints as $c): ?>
                        <?php

                        $typeName = $c['complaint_type_name'] ?? ($c['type'] ?? 'Other');


                        $status = strtolower((string) ($c['status'] ?? 'open'));
                        $details = $c['details'] ?? '';
                        $id = $c['complaint_id'] ?? 0;

                        // image_path from DB (nullable)
                        $img = $c['image_path'] ?? '';



                        $statusClass = preg_replace('/[^a-z_]/', '', $status);
                        ?>

                        <article class="complaint-card">
                            <div class="card-top">
                                <span class="badge type"><?= htmlspecialchars($typeName) ?></span>
                                <span class="badge status <?= htmlspecialchars($statusClass) ?>">
                                    <?= htmlspecialchars(str_replace('_', ' ', $status)) ?>
                                </span>
                            </div>

                            <?php if (!empty($details)): ?>
                                <p class="details"><?= htmlspecialchars($details) ?></p>
                            <?php endif; ?>

                            <div class="card-bottom">
                                <div class="thumb">
                                    <?php if (!empty($img)): ?>
                                        <img src="<?= htmlspecialchars($img) ?>" alt="Complaint image">
                                    <?php else: ?>
                                        <span class="thumb-placeholder">No Image</span>
                                    <?php endif; ?>
                                </div>

                                <div class="actions">
                                    <a class="link"
                                        href="index.php?action=viewComplaint&complaint_id=<?= urlencode((string) $id) ?>">
                                        View Details
                                    </a>
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