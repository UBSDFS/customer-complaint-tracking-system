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
                    <input type="submit" value="Submit Complaint">
                    <a href="index.php?action=dashboard">Cancel</a>

                </div>
            </form>
        </section>
    </main>
</body>

</html>