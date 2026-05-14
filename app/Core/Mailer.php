<?php

namespace App\Core;

/**
 * Email Service - Production-ready email system
 * Supports SMTP (Gmail, Outlook, Custom) with fallback to PHP mail()
 */
class Mailer
{
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private string $encryption;
    private string $fromAddress;
    private string $fromName;
    private bool $enabled;
    private array $errors = [];

    public function __construct()
    {
        $this->host = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
        $this->port = (int)($_ENV['MAIL_PORT'] ?? 587);
        $this->username = $_ENV['MAIL_USERNAME'] ?? '';
        $this->password = $_ENV['MAIL_PASSWORD'] ?? '';
        $this->encryption = $_ENV['MAIL_ENCRYPTION'] ?? 'tls';
        $this->fromAddress = $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@healthcare.com';
        $this->fromName = $_ENV['MAIL_FROM_NAME'] ?? 'Healthcare Supply Chain';
        $this->enabled = filter_var($_ENV['ALERT_ENABLE_EMAIL'] ?? true, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Send email using SMTP
     */
    public function send(string $to, string $subject, string $body, array $options = []): bool
    {
        if (!$this->enabled) {
            $this->log("Email disabled in configuration");
            return false;
        }

        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Invalid email address: {$to}";
            return false;
        }

        $isHtml = $options['html'] ?? true;
        $cc = $options['cc'] ?? [];
        $bcc = $options['bcc'] ?? [];
        $attachments = $options['attachments'] ?? [];

        try {
            // Use stream socket for SMTP
            if ($this->username && $this->password) {
                return $this->sendViaSMTP($to, $subject, $body, $isHtml, $cc, $bcc);
            } else {
                // Fallback to PHP mail()
                return $this->sendViaPhpMail($to, $subject, $body, $isHtml);
            }
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
            $this->log("Email send failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send via SMTP using fsockopen
     */
    private function sendViaSMTP(string $to, string $subject, string $body, bool $isHtml, array $cc, array $bcc): bool
    {
        $socket = @fsockopen($this->host, $this->port, $errno, $errstr, 30);
        
        if (!$socket) {
            throw new \Exception("Could not connect to SMTP server: {$errstr} ({$errno})");
        }

        // Read server response
        $this->readSMTPResponse($socket);

        // EHLO
        fputs($socket, "EHLO {$this->host}\r\n");
        $this->readSMTPResponse($socket);

        // STARTTLS
        if ($this->encryption === 'tls') {
            fputs($socket, "STARTTLS\r\n");
            $this->readSMTPResponse($socket);
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            fputs($socket, "EHLO {$this->host}\r\n");
            $this->readSMTPResponse($socket);
        }

        // AUTH LOGIN
        fputs($socket, "AUTH LOGIN\r\n");
        $this->readSMTPResponse($socket);
        fputs($socket, base64_encode($this->username) . "\r\n");
        $this->readSMTPResponse($socket);
        fputs($socket, base64_encode($this->password) . "\r\n");
        $this->readSMTPResponse($socket);

        // MAIL FROM
        fputs($socket, "MAIL FROM: <{$this->fromAddress}>\r\n");
        $this->readSMTPResponse($socket);

        // RCPT TO
        fputs($socket, "RCPT TO: <{$to}>\r\n");
        $this->readSMTPResponse($socket);

        // DATA
        fputs($socket, "DATA\r\n");
        $this->readSMTPResponse($socket);

        // Email headers and body
        $headers = $this->buildHeaders($to, $subject, $isHtml, $cc, $bcc);
        fputs($socket, $headers . "\r\n\r\n" . $body . "\r\n.\r\n");
        $this->readSMTPResponse($socket);

        // QUIT
        fputs($socket, "QUIT\r\n");
        fclose($socket);

        $this->log("Email sent successfully to: {$to}");
        return true;
    }

    /**
     * Fallback to PHP mail()
     */
    private function sendViaPhpMail(string $to, string $subject, string $body, bool $isHtml): bool
    {
        $headers = "From: {$this->fromName} <{$this->fromAddress}>\r\n";
        $headers .= "Reply-To: {$this->fromAddress}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        
        if ($isHtml) {
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        } else {
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        }

        $result = mail($to, $subject, $body, $headers);
        
        if ($result) {
            $this->log("Email sent via PHP mail() to: {$to}");
        } else {
            $this->log("Failed to send email via PHP mail() to: {$to}");
        }
        
        return $result;
    }

    /**
     * Build email headers
     */
    private function buildHeaders(string $to, string $subject, bool $isHtml, array $cc, array $bcc): string
    {
        $headers = "From: {$this->fromName} <{$this->fromAddress}>\r\n";
        $headers .= "To: {$to}\r\n";
        
        if (!empty($cc)) {
            $headers .= "Cc: " . implode(', ', $cc) . "\r\n";
        }
        
        if (!empty($bcc)) {
            $headers .= "Bcc: " . implode(', ', $bcc) . "\r\n";
        }
        
        $headers .= "Subject: {$subject}\r\n";
        $headers .= "Date: " . date('r') . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        
        if ($isHtml) {
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        } else {
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        }
        
        return $headers;
    }

    /**
     * Read SMTP server response
     */
    private function readSMTPResponse($socket): string
    {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }
        return $response;
    }

    /**
     * Send to multiple recipients
     */
    public function sendBulk(array $recipients, string $subject, string $body, array $options = []): array
    {
        $results = [
            'success' => [],
            'failed' => []
        ];

        foreach ($recipients as $recipient) {
            if ($this->send($recipient, $subject, $body, $options)) {
                $results['success'][] = $recipient;
            } else {
                $results['failed'][] = $recipient;
            }
        }

        return $results;
    }

    /**
     * Get errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Log email activity
     */
    private function log(string $message): void
    {
        $logFile = __DIR__ . '/../../storage/logs/email.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[{$timestamp}] {$message}\n", FILE_APPEND);
    }

    /**
     * Test email configuration
     */
    public function test(string $to): bool
    {
        $subject = "Test Email - Healthcare Supply Chain";
        $body = "
            <h2>Email Configuration Test</h2>
            <p>This is a test email from Healthcare Supply Chain Management System.</p>
            <p>If you received this, your email configuration is working correctly!</p>
            <p><strong>Timestamp:</strong> " . date('Y-m-d H:i:s') . "</p>
        ";
        
        return $this->send($to, $subject, $body);
    }
}
