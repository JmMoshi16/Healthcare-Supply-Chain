<div class="alert-box alert-success">
    <h2 style="margin-top: 0;">👋 Welcome to Healthcare Supply Chain!</h2>
    <p>Your account has been successfully created.</p>
</div>

<h3>Account Details:</h3>
<table>
    <tr>
        <th>Full Name:</th>
        <td><?= htmlspecialchars($user['fullname']) ?></td>
    </tr>
    <tr>
        <th>Email:</th>
        <td><?= htmlspecialchars($user['email']) ?></td>
    </tr>
    <tr>
        <th>Role:</th>
        <td><strong><?= ucfirst($user['role']) ?></strong></td>
    </tr>
    <tr>
        <th>Account Created:</th>
        <td><?= date('M d, Y H:i:s') ?></td>
    </tr>
</table>

<div style="margin-top: 20px;">
    <h4>Getting Started:</h4>
    <ul>
        <li>Log in to your account using your email and password</li>
        <li>Complete your profile information</li>
        <li>Familiarize yourself with the dashboard</li>
        <li>Review system documentation and guidelines</li>
    </ul>
</div>

<div style="margin-top: 20px;">
    <h4>Your Permissions:</h4>
    <?php if ($user['role'] === 'superadmin'): ?>
        <p>As a <strong>Super Administrator</strong>, you have full access to:</p>
        <ul>
            <li>User management</li>
            <li>Medicine and batch management</li>
            <li>Stock transactions</li>
            <li>System settings and configuration</li>
            <li>Reports and analytics</li>
        </ul>
    <?php elseif ($user['role'] === 'manager'): ?>
        <p>As a <strong>Manager</strong>, you have access to:</p>
        <ul>
            <li>Medicine and batch management</li>
            <li>Stock transactions</li>
            <li>Reports and analytics</li>
            <li>Staff oversight</li>
        </ul>
    <?php else: ?>
        <p>As a <strong>Staff Member</strong>, you have access to:</p>
        <ul>
            <li>View medicine inventory</li>
            <li>View batch information</li>
            <li>Record stock transactions</li>
            <li>View reports</li>
        </ul>
    <?php endif; ?>
</div>

<a href="<?= $_ENV['APP_URL'] ?>/login" class="btn">Login to Your Account</a>

<div style="margin-top: 20px; padding: 15px; background: #d1ecf1; border-left: 4px solid #17a2b8; border-radius: 6px;">
    <p style="margin: 0;"><strong>Need Help?</strong> Contact your system administrator or refer to the user guide.</p>
</div>
