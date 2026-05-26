<?php
$title = 'Email Management';
ob_start();
?>

<link rel="stylesheet" href="/assets/css/email-management.css">

<div class="email-management-container">
    <!-- Hero Header with Gradient -->
    <div class="email-hero-header">
        <div class="email-hero-content">
            <div class="email-hero-icon">
                <div class="icon-pulse"></div>
                <i class="bi bi-envelope-heart"></i>
            </div>
            <div class="email-hero-text">
                <h1 class="email-hero-title">Email Management Center</h1>
                <p class="email-hero-subtitle">Configure, test, and monitor automated email notifications</p>
            </div>
        </div>
        <div class="email-hero-stats">
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="emailsSentToday">0</div>
                    <div class="stat-label">Sent Today</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="emailsPending">0</div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon danger">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="emailsFailed">0</div>
                    <div class="stat-label">Failed</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid Layout -->
    <div class="email-grid-layout">
        
        <!-- Left Column -->
        <div class="email-left-column">
            
            <!-- Configuration Card -->
            <div class="email-card config-card">
                <div class="card-header">
                    <div class="card-header-left">
                        <div class="card-icon">
                            <i class="bi bi-gear-fill"></i>
                        </div>
                        <div>
                            <h3 class="card-title">Configuration</h3>
                            <p class="card-subtitle">SMTP & Email Settings</p>
                        </div>
                    </div>
                    <div class="status-indicator <?= filter_var($_ENV['ALERT_ENABLE_EMAIL'] ?? true, FILTER_VALIDATE_BOOLEAN) ? 'active' : 'inactive' ?>">
                        <span class="status-dot"></span>
                        <span class="status-text"><?= filter_var($_ENV['ALERT_ENABLE_EMAIL'] ?? true, FILTER_VALIDATE_BOOLEAN) ? 'Active' : 'Inactive' ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="config-grid">
                        <div class="config-item">
                            <div class="config-label">
                                <i class="bi bi-server"></i>
                                <span>SMTP Host</span>
                            </div>
                            <div class="config-value"><?= $_ENV['MAIL_HOST'] ?? 'Not configured' ?></div>
                        </div>
                        <div class="config-item">
                            <div class="config-label">
                                <i class="bi bi-plug"></i>
                                <span>Port</span>
                            </div>
                            <div class="config-value"><?= $_ENV['MAIL_PORT'] ?? 'Not configured' ?></div>
                        </div>
                        <div class="config-item">
                            <div class="config-label">
                                <i class="bi bi-shield-lock"></i>
                                <span>Encryption</span>
                            </div>
                            <div class="config-value"><?= strtoupper($_ENV['MAIL_ENCRYPTION'] ?? 'TLS') ?></div>
                        </div>
                        <div class="config-item">
                            <div class="config-label">
                                <i class="bi bi-envelope-at"></i>
                                <span>From Address</span>
                            </div>
                            <div class="config-value truncate"><?= $_ENV['MAIL_FROM_ADDRESS'] ?? 'Not configured' ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Email Card -->
            <div class="email-card test-card">
                <div class="card-header">
                    <div class="card-header-left">
                        <div class="card-icon test">
                            <i class="bi bi-send-check"></i>
                        </div>
                        <div>
                            <h3 class="card-title">Test Configuration</h3>
                            <p class="card-subtitle">Verify email delivery</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="test-email-form">
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="bi bi-envelope"></i>
                                Recipient Email
                            </label>
                            <div class="input-wrapper">
                                <input type="email" 
                                       id="testEmail" 
                                       class="input-modern" 
                                       placeholder="Enter email address"
                                       value="<?= auth()['email'] ?>">
                                <div class="input-icon">
                                    <i class="bi bi-at"></i>
                                </div>
                            </div>
                        </div>
                        <button class="btn-modern btn-primary" onclick="sendTestEmail()">
                            <span class="btn-icon">
                                <i class="bi bi-send-fill"></i>
                            </span>
                            <span class="btn-text" id="testBtnText">Send Test Email</span>
                            <span class="btn-loader" id="testBtnLoader" style="display:none;">
                                <span class="spinner"></span>
                            </span>
                        </button>
                    </div>
                    <div id="testResult" class="alert-container"></div>
                </div>
            </div>

            <!-- Alert Settings Card -->
            <div class="email-card settings-card">
                <div class="card-header">
                    <div class="card-header-left">
                        <div class="card-icon settings">
                            <i class="bi bi-sliders"></i>
                        </div>
                        <div>
                            <h3 class="card-title">Alert Settings</h3>
                            <p class="card-subtitle">Notification thresholds</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="settings-list">
                        <div class="setting-item">
                            <div class="setting-icon warning">
                                <i class="bi bi-calendar-x"></i>
                            </div>
                            <div class="setting-content">
                                <div class="setting-label">Expiry Alert Days</div>
                                <div class="setting-value"><?= $_ENV['ALERT_EXPIRY_DAYS'] ?? '30,15,7' ?> days</div>
                            </div>
                        </div>
                        <div class="setting-item">
                            <div class="setting-icon danger">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div class="setting-content">
                                <div class="setting-label">Low Stock Threshold</div>
                                <div class="setting-value"><?= $_ENV['ALERT_LOW_STOCK_THRESHOLD'] ?? '10' ?> units</div>
                            </div>
                        </div>
                        <div class="setting-item">
                            <div class="setting-icon info">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div class="setting-content">
                                <div class="setting-label">Admin Email</div>
                                <div class="setting-value truncate"><?= $_ENV['ALERT_ADMIN_EMAIL'] ?? 'Not set' ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="settings-note">
                        <i class="bi bi-info-circle"></i>
                        <span>Edit .env file to change these settings</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column -->
        <div class="email-right-column">
            
            <!-- Manual Triggers Card -->
            <div class="email-card triggers-card">
                <div class="card-header">
                    <div class="card-header-left">
                        <div class="card-icon triggers">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <div>
                            <h3 class="card-title">Manual Triggers</h3>
                            <p class="card-subtitle">Send alerts on demand</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="triggers-grid">
                        
                        <div class="trigger-card expiry" onclick="sendAlert('expiry')">
                            <div class="trigger-icon-wrapper">
                                <div class="trigger-icon">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                </div>
                                <div class="trigger-glow"></div>
                            </div>
                            <div class="trigger-content">
                                <h4 class="trigger-title">Expiry Alerts</h4>
                                <p class="trigger-description">Notify about expiring medicines</p>
                            </div>
                            <div class="trigger-arrow">
                                <i class="bi bi-arrow-right"></i>
                            </div>
                        </div>

                        <div class="trigger-card lowstock" onclick="sendAlert('lowstock')">
                            <div class="trigger-icon-wrapper">
                                <div class="trigger-icon">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                                <div class="trigger-glow"></div>
                            </div>
                            <div class="trigger-content">
                                <h4 class="trigger-title">Low Stock Alerts</h4>
                                <p class="trigger-description">Notify about low inventory</p>
                            </div>
                            <div class="trigger-arrow">
                                <i class="bi bi-arrow-right"></i>
                            </div>
                        </div>

                        <div class="trigger-card daily" onclick="sendAlert('daily')">
                            <div class="trigger-icon-wrapper">
                                <div class="trigger-icon">
                                    <i class="bi bi-calendar-day"></i>
                                </div>
                                <div class="trigger-glow"></div>
                            </div>
                            <div class="trigger-content">
                                <h4 class="trigger-title">Daily Report</h4>
                                <p class="trigger-description">Send today's inventory report</p>
                            </div>
                            <div class="trigger-arrow">
                                <i class="bi bi-arrow-right"></i>
                            </div>
                        </div>

                        <div class="trigger-card weekly" onclick="sendAlert('weekly')">
                            <div class="trigger-icon-wrapper">
                                <div class="trigger-icon">
                                    <i class="bi bi-calendar-week"></i>
                                </div>
                                <div class="trigger-glow"></div>
                            </div>
                            <div class="trigger-content">
                                <h4 class="trigger-title">Weekly Report</h4>
                                <p class="trigger-description">Send this week's summary</p>
                            </div>
                            <div class="trigger-arrow">
                                <i class="bi bi-arrow-right"></i>
                            </div>
                        </div>

                    </div>
                    <div id="alertResult" class="alert-container"></div>
                </div>
            </div>

            <!-- Automation Schedule Card -->
            <div class="email-card schedule-card">
                <div class="card-header">
                    <div class="card-header-left">
                        <div class="card-icon schedule">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <h3 class="card-title">Automation Schedule</h3>
                            <p class="card-subtitle">Automated email timeline</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker daily">
                                <i class="bi bi-sun-fill"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-title">Daily Alerts</div>
                                <div class="timeline-description">Every day at 8:00 AM</div>
                                <div class="timeline-tags">
                                    <span class="tag">Expiry</span>
                                    <span class="tag">Low Stock</span>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker weekday">
                                <i class="bi bi-briefcase-fill"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-title">Daily Reports</div>
                                <div class="timeline-description">Weekdays at 8:00 AM</div>
                                <div class="timeline-tags">
                                    <span class="tag">Managers</span>
                                    <span class="tag">Admins</span>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker weekly">
                                <i class="bi bi-calendar-week-fill"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-title">Weekly Reports</div>
                                <div class="timeline-description">Every Monday at 8:00 AM</div>
                                <div class="timeline-tags">
                                    <span class="tag">Summary</span>
                                    <span class="tag">Analytics</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cron-setup-alert">
                        <div class="cron-icon">
                            <i class="bi bi-terminal"></i>
                        </div>
                        <div class="cron-content">
                            <div class="cron-title">Setup Required</div>
                            <div class="cron-description">Configure cron job to run <code>cron_alerts.php</code> daily</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<script src="/assets/js/email-management.js"></script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
