<?php
$isEdit = !empty($employee);
$userId = $isEdit ? (int)$employee['user_id'] : 0;
$action = $isEdit ? 'adminEmployeeUpdate' : 'adminEmployeeStore';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?php echo $isEdit ? 'Edit Employee' : 'Add Employee'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/customer-complaint-tracking-system/public/assets/css/dashboard.css">
</head>

<body>
    <main class="dash">
        <section class="work-card" style="grid-column:1 / -1;">
            <div class="topbar">
                <div class="topbar-title">
                    <h1><?php echo $isEdit ? 'Edit Employee' : 'Add Employee'; ?></h1>
                    <p class="subtext">Tech/Admin employees live in employee_profiles.</p>
                </div>
                <div class="topbar-actions">
                    <a class="btn secondary" href="index.php?action=adminEmployees">Back</a>
                    <a class="btn secondary" href="index.php?action=logout">Logout</a>
                </div>
            </div>

            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="complaint-card">
                    <div class="details" style="color:#b91c1c;">
                        <?php echo htmlspecialchars($_SESSION['flash_error']);
                        unset($_SESSION['flash_error']); ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="complaint-card">
                <form method="POST" action="index.php?action=<?php echo $action; ?>" class="form">
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars((string)$userId); ?>">
                    <?php endif; ?>

                    <div class="grid-2">
                        <div class="field">
                            <label class="field-label">Email</label>
                            <input class="input" name="email" type="email" required
                                value="<?php echo htmlspecialchars($employee['email'] ?? ''); ?>">
                        </div>

                        <?php if (!$isEdit): ?>
                            <div class="field">
                                <label class="field-label">Temp Password</label>
                                <input class="input" name="password" type="text" required>
                                <p class="subtext">They can change it after login.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label class="field-label">First Name</label>
                            <input class="input" name="first_name" type="text" required
                                value="<?php echo htmlspecialchars($employee['first_name'] ?? ''); ?>">
                        </div>

                        <div class="field">
                            <label class="field-label">Last Name</label>
                            <input class="input" name="last_name" type="text" required
                                value="<?php echo htmlspecialchars($employee['last_name'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label class="field-label">Phone Ext</label>
                            <input class="input" name="phone_ext" type="text"
                                value="<?php echo htmlspecialchars($employee['phone_ext'] ?? ''); ?>">
                        </div>

                        <div class="field">
                            <label class="field-label">Level</label>
                            <?php $lvl = $employee['level'] ?? 'tech'; ?>
                            <select class="select" name="level">
                                <option value="tech" <?php echo $lvl === 'tech' ? 'selected' : ''; ?>>Tech</option>
                                <option value="admin" <?php echo $lvl === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>
                    </div>

                    <div class="actions">
                        <button class="btn primary" type="submit"><?php echo $isEdit ? 'Save Changes' : 'Create Employee'; ?></button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>

</html>