<?php if (!isset($complaint) || !is_array($complaint)) {
    http_response_code(500);
    echo "Complaint data not available.";
    exit;
}
$complaint_id = (int)($complaint['complaint_id'] ?? 0);
$status = (string)($complaint['status'] ?? '');
$details = (string)($complaint['details'] ?? '');
$image_path = (string)($complaint['image_path'] ?? '');
$type_name = (string)($complaint['complaint_type_name'] ?? $complaint['type_name'] ?? $complaint['complaint_type'] ?? '');
$product_name = (string)($complaint['product_name'] ?? '');
$role = $_SESSION['role'] ?? '';
$backAction = match ($role) {
    'admin' => 'adminDashboard',
    'technician' => 'techDashboard',
    default => 'dashboard',
};
$editAction = ($role === 'customer') ? 'editComplaintCustomer' : 'editComplaint'; ?> <h2>Complaint #<?= htmlspecialchars((string)$complaint_id) ?></h2> <?php if ($status !== ''): ?> <p><strong>Status:</strong> <?= htmlspecialchars($status) ?></p> <?php endif; ?> <?php if ($type_name !== ''): ?> <p><strong>Complaint Type:</strong> <?= htmlspecialchars($type_name) ?></p> <?php endif; ?> <?php if ($product_name !== ''): ?> <p><strong>Product:</strong> <?= htmlspecialchars($product_name) ?></p> <?php endif; ?> <p><strong>Description:</strong></p>
<p><?= nl2br(htmlspecialchars($details)) ?></p>
<hr>
<h3>Image</h3> <?php if ($image_path !== ''): ?> <img src="<?= htmlspecialchars($image_path) ?>" alt="Complaint image" style="max-width:600px;width:100%;height:auto;"> <?php else: ?> <p><em>No image uploaded.</em></p> <?php endif; ?>
<hr>
<p> <a class="link" href="index.php?action=<?= urlencode($backAction) ?>">Back</a> <?php if ($status !== 'resolved'): ?> <a class="link" href="index.php?action=<?= urlencode($editAction) ?>&id=<?= urlencode((string)$complaint_id) ?>"> Edit </a> <?php endif; ?> </p>

<style>
    .complaint-show .field {
        margin-bottom: 16px;
    }

    .complaint-show {
        max-width: 760px;
        margin: 40px auto;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        padding: 24px;
    }

    .complaint-show h2 {
        margin: 0 0 12px;
        font-size: 22px;
    }

    .complaint-show p {
        margin: 8px 0;
        color: #374151;
        font-size: 14px;
        line-height: 1.5;
    }

    .complaint-show strong {
        color: #111827;
    }

    .complaint-show hr {
        border: none;
        border-top: 1px solid #e5e7eb;
        margin: 18px 0;
    }

    .complaint-show img {
        width: 100%;
        max-width: 600px;
        height: auto;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
    }
</style>