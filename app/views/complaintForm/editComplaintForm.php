<?php
// expects: $complaint_id, $types, $products, $errors, $old
if (!isset($complaint_id)) {
    $complaint_id = (int) ($_POST['complaint_id'] ?? 0); // fallback only
}
?>
<h2>Edit Complaint</h2>

<?php if (!empty($errors)): ?>
    <ul>
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST" action="index.php?action=updateCustomerComplaint" enctype="multipart/form-data">
    <input type="hidden" name="complaint_id" value="<?= htmlspecialchars((string) $complaint_id) ?>">

    <!-- Show existing history (read-only) -->
    <label>Existing Description / History</label><br>
    <textarea rows="10" cols="60" readonly><?= htmlspecialchars($old['details'] ?? '') ?></textarea>
    <br><br>

    <!-- New note to append -->
    <label>Add update</label><br>
    <textarea name="new_note" rows="6" cols="60" required
        placeholder="Add additional details here (this will be appended)..."></textarea>
    <br><br>

    <?php if (!empty($old['image_path'])): ?>
        <p>Current image:</p>
        <img src="<?= htmlspecialchars($old['image_path']) ?>" alt="Current complaint image" style="max-width:300px;">
        <br><br>
    <?php endif; ?>

    <label>Replace image (optional)</label><br>
    <input type="file" name="image" accept="image/*">
    <br><br>

    <button type="submit">Save Update</button>
    <a href="index.php?action=dashboard">Cancel</a>
</form>