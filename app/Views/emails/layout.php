<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $subject ?? 'Healthcare Supply Chain' ?></title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 30px 20px;
        }
        .alert-box {
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .alert-warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
        }
        .alert-danger {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        .alert-info {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            color: #0c5460;
        }
        .alert-success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
        }
        .email-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🏥 Healthcare Supply Chain</h1>
        </div>
        <div class="email-body">
            <?= $content ?>
        </div>
        <div class="email-footer">
            <p><strong>Healthcare Supply Chain Management System</strong></p>
            <p>This is an automated email. Please do not reply.</p>
            <p>&copy; <?= date('Y') ?> Healthcare Supply Chain. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
