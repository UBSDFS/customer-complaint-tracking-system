<?php
$types  = $types  ?? [];
$products = $products ?? [];
$errors = $errors ?? [];
$old    = $old    ?? ['complaintTypeId' => '', 'details' => '', 'productId' => ''];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="/customer-complaint-tracking-system/public/assets/css/registration.css">
    <title>New Complaint</title>
</head>

<body>
    <main class="new-complaint-page">
        <section class="complaint-card">
            <header class="complaint-header">
                <h2>New Complaint</h2>
            </header>

            <?php if (!empty($errors)): ?>
                <div class="error-box">
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST"
                action="index.php?action=storeComplaint"
                enctype="multipart/form-data">


                <div class="field">
                    <label for="complaintTypeId">Complaint Type:</label>
                    <select id="complaintTypeId" name="complaintTypeId" required>
                        <option value="">-- Select a type --</option>
                        <?php foreach ($types as $t): ?>
                            <option value="<?= (int)$t['complaint_type_id'] ?>"
                                <?= ((string)$old['complaintTypeId'] === (string)$t['complaint_type_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="productId">Product Type:</label>
                    <select id="productId" name="productId" required>
                        <option value="">-- Select a product --</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= (int)$p['product_id'] ?>"
                                <?= ((string)$old['productId'] === (string)$p['product_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="details">Description:</label>
                    <textarea id="details" name="details" rows="5" required><?= htmlspecialchars($old['details']) ?></textarea>
                </div>

                <div class="field">
                    <label for="image">Attach Image (optional):</label>
                    <input id="image" type="file" name="image" accept="image/*">
                </div>

                <div class="actions">
                    <button type="submit">Submit Complaint</button>
                    <a href="index.php?action=dashboard">Cancel</a>



                </div>
            </form>
        </section>
    </main>
</body>

</html>

<style>
    /* New Complaint Page */

    .new-complaint-page {
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #f4f6fb;
        padding: 20px;
    }

    .complaint-card {
        width: 100%;
        max-width: 560px;
        background: #ffffff;
        padding: 28px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .complaint-header h2 {
        margin-bottom: 20px;
        font-size: 22px;
    }

    .field {
        margin-bottom: 16px;
        display: flex;
        flex-direction: column;
    }

    .field label {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #374151;
    }

    .field select,
    .field textarea,
    .field input[type="file"] {
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        font-size: 14px;
        transition: border 0.2s ease;
    }

    .field textarea {
        resize: vertical;
    }

    .field select:focus,
    .field textarea:focus {
        outline: none;
        border-color: #2563eb;
    }

    .error-box {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 14px;
    }

    .error-box ul {
        margin: 0;
        padding-left: 18px;
    }

    .actions {
        margin-top: 20px;
        display: flex;
        gap: 12px;
    }

    .actions input[type="submit"] {
        padding: 10px 18px;
        border-radius: 8px;
        border: none;
        background: #2563eb;
        color: white;
        font-weight: 600;
        cursor: pointer;
    }

    .actions input[type="submit"]:hover {
        background: #1e40af;
    }

    .actions a {
        align-self: center;
        font-size: 14px;
        color: #6b7280;
        text-decoration: none;
    }

    .actions a:hover {
        text-decoration: underline;
    }
</style>