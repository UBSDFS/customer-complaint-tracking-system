<?php
echo "<pre>";
print_r($user);
echo "</pre>";
?>

<h2>Edit Profile</h2>

<form method="POST" action="index.php?action=updateProfile">

    <label>Email</label>
    <input type="text" name="email"
        value="<?= htmlspecialchars($user['email'] ?? '') ?>">

    <br><br>

    <label>First Name</label>
    <input type="text" name="first_name"
        value="<?= htmlspecialchars($user['first_name'] ?? '') ?>">

    <br><br>

    <label>Last Name</label>
    <input type="text" name="last_name"
        value="<?= htmlspecialchars($user['last_name'] ?? '') ?>">

    <br><br>

    <button type="submit">Save Changes</button>
</form>

<br>

<a href="index.php?action=dashboard">Cancel</a>