<?php


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
        <label>Street Address</label>
    <input type="text" name="street_address"
        value="<?= htmlspecialchars($user['street_address'] ?? '') ?>">

    <br><br>
        <label>City</label>
    <input type="text" name="city"
        value="<?= htmlspecialchars($user['city'] ?? '') ?>">

    <br><br>
        <label>State</label>
    <input type="text" name="state"
        value="<?= htmlspecialchars($user['state'] ?? '') ?>">

    <br><br>
        <label>Zip Code</label>
    <input type="text" name="zip"
        value="<?= htmlspecialchars($user['zip'] ?? '') ?>">

        <label>Phone Number</label>
    <input type="text" name="phone"
        value="<?= htmlspecialchars($user['phone'] ?? '') ?>">

    <br><br>
        <label>Password</label>
    <input type="password" name="password"
        value="<?= htmlspecialchars($user['password'] ?? '') ?>">

    <br><br>

    <button type="submit">Save Changes</button>
</form>

<br>

<a href="index.php?action=dashboard">Cancel</a>