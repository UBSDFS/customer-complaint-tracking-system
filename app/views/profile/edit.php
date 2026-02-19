<div class="profile-edit">
    <h2>Edit Profile</h2>

    <form method="POST" action="index.php?action=updateProfile">

        <div class="field">
            <label>Email</label>
            <input type="text" name="email"
                value="<?= htmlspecialchars($user['email'] ?? '') ?>">
        </div>

        <div class="field">
            <label>First Name</label>
            <input type="text" name="first_name"
                value="<?= htmlspecialchars($user['first_name'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Last Name</label>
            <input type="text" name="last_name"
                value="<?= htmlspecialchars($user['last_name'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Street Address</label>
            <input type="text" name="street_address"
                value="<?= htmlspecialchars($user['street_address'] ?? '') ?>">
        </div>

        <div class="field">
            <label>City</label>
            <input type="text" name="city"
                value="<?= htmlspecialchars($user['city'] ?? '') ?>">
        </div>

        <div class="field">
            <label>State</label>
            <input type="text" name="state"
                value="<?= htmlspecialchars($user['state'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Zip Code</label>
            <input type="text" name="zip"
                value="<?= htmlspecialchars($user['zip'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Phone Number</label>
            <input type="text" name="phone"
                value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Password</label>
            <input type="password" name="password">
        </div>

        <div class="actions">
            <button type="submit">Save Changes</button>
            <a class="cancel-link" href="index.php?action=dashboard">Cancel</a>
        </div>

    </form>
</div>


<style>
    .profile-edit {
        max-width: 520px;
        margin: 40px auto;
        padding: 28px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        border: 1px solid #e5e7eb;
    }

    .profile-edit h2 {
        margin-bottom: 20px;
        font-size: 22px;
    }

    .profile-edit .field {
        margin-bottom: 16px;
        display: flex;
        flex-direction: column;
    }

    .profile-edit label {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #374151;
    }

    .profile-edit input {
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        font-size: 14px;
        transition: border 0.2s ease;
    }

    .profile-edit input:focus {
        outline: none;
        border-color: #2563eb;
    }

    .profile-edit .actions {
        margin-top: 20px;
        display: flex;
        gap: 12px;
    }

    .profile-edit button {
        padding: 10px 18px;
        border-radius: 8px;
        border: none;
        background: #2563eb;
        color: white;
        font-weight: 600;
        cursor: pointer;
    }

    .profile-edit button:hover {
        background: #1e40af;
    }

    .profile-edit .cancel-link {
        align-self: center;
        font-size: 14px;
        color: #6b7280;
        text-decoration: none;
    }

    .profile-edit .cancel-link:hover {
        text-decoration: underline;
    }
</style>