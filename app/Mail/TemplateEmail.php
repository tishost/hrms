<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TemplateEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $template;
    public $variables;

    /**
     * Create a new message instance.
     */
    public function __construct(EmailTemplate $template, array $variables = [])
    {
        $this->template = $template;
        $this->variables = $variables;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->replaceVariables($this->template->subject);
        $content = $this->replaceVariables($this->template->content);

        $companyName = \App\Helpers\SystemHelper::getCompanyName();
        $mail = $this->subject($subject)
                    ->from(config('mail.from.address'), $companyName);

        if ($this->template->isHtml()) {
            // Ensure proper HTML structure for email clients
            $htmlContent = $this->wrapInEmailStructure($content);
            $mail->html($htmlContent);
        } else {
            $mail->text($content);
        }

        return $mail;
    }

    /**
     * Replace template variables with actual values
     */
    private function replaceVariables($text)
    {
        foreach ($this->variables as $key => $value) {
            // Replace both {variable} and {{variable}} formats
            $text = str_replace('{' . $key . '}', $value, $text);
            $text = str_replace('{{' . $key . '}}', $value, $text);
        }

        // Add common variables
        $text = str_replace('{company_name}', \App\Helpers\SystemHelper::getCompanyName(), $text);
        $text = str_replace('{site_url}', config('app.url'), $text);
        $text = str_replace('{support_email}', config('mail.from.address', 'support@barimanager.com'), $text);

        return $text;
    }

    /**
     * Wrap HTML content in proper email structure
     */
    private function wrapInEmailStructure($content)
    {
        // Check if content already has HTML structure
        if (strpos($content, '<!DOCTYPE html>') !== false || strpos($content, '<html') !== false) {
            return $content;
        }

        // Get company info
        $companyName = \App\Helpers\SystemHelper::getCompanyName();
        $companyLogo = \App\Helpers\SystemHelper::getCompanyLogo();
        $siteUrl = config('app.url');
        $supportEmail = config('mail.from.address', 'support@barimanager.com');
        
        // Create beautiful header
        $headerHtml = $this->createEmailHeader($companyName, $companyLogo, $siteUrl);
        
        // Create beautiful footer
        $footerHtml = $this->createEmailFooter($companyName, $siteUrl, $supportEmail);

        // Wrap content in proper HTML email structure
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . $this->template->subject . ' — ' . $companyName . '</title>
    <style>
        /* Email-safe CSS styles */
        body,table,td,a{ -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
        table,td{ mso-table-lspace:0pt; mso-table-rspace:0pt; }
        img{ -ms-interpolation-mode:bicubic; border:0; height:auto; line-height:100%; outline:none; text-decoration:none; }
        body{ margin:0; padding:0; width:100% !important; -webkit-font-smoothing:antialiased; -webkit-text-size-adjust:none; background-color:#f5f7fb; }
        @media screen and (max-width:600px){
            .container{ width:100% !important; padding:16px !important; }
            .hero h1{ font-size:22px !important; }
            .stack{ display:block !important; width:100% !important; }
        }
        /* Content styling */
        .content-area {
            font-family: "Segoe UI", Roboto, Arial, sans-serif;
            color: #333;
        }
        .btn {
            background: #1e88e5;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 6px;
            display: inline-block;
            font-weight: 600;
            font-size: 14px;
        }
        .btn:hover {
            background: #1565c0;
            color: white;
        }
        .alert {
            padding: 16px;
            margin: 16px 0;
            border-radius: 8px;
            border: 1px solid transparent;
        }
        .alert-success {
            background: #fbfdff;
            border: 1px solid #eef4ff;
            color: #333;
        }
        .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .alert-info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        .alert-danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        h1, h2, h3, h4, h5, h6 {
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 16px;
        }
        h2 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 600;
        }
        p {
            margin-bottom: 16px;
            color: #555;
        }
        a {
            color: #667eea;
            text-decoration: none;
        }
        a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }
        .btn-secondary:hover {
            background: linear-gradient(135deg, #495057 0%, #6c757d 100%);
        }
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .btn-success:hover {
            background: linear-gradient(135deg, #20c997 0%, #28a745 100%);
        }
        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
        }
        .btn-danger:hover {
            background: linear-gradient(135deg, #e83e8c 0%, #dc3545 100%);
        }
        .alert {
            padding: 16px 20px;
            margin: 20px 0;
            border-radius: 8px;
            border: 1px solid transparent;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border-color: #28a745;
            color: #155724;
        }
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            border-color: #dc3545;
            color: #721c24;
        }
        .alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border-color: #ffc107;
            color: #856404;
        }
        .alert-info {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            border-color: #17a2b8;
            color: #0c5460;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
        }
        table td, table th {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        ul, ol {
            margin-bottom: 16px;
            padding-left: 20px;
        }
        li {
            margin-bottom: 8px;
        }
        blockquote {
            border-left: 4px solid #007bff;
            margin: 16px 0;
            padding: 12px 16px;
            background: #f8f9fa;
            font-style: italic;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-primary { color: #007bff; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-warning { color: #ffc107; }
        .text-info { color: #17a2b8; }
        .text-muted { color: #6c757d; }
        .bg-primary { background-color: #007bff; color: white; }
        .bg-success { background-color: #28a745; color: white; }
        .bg-danger { background-color: #dc3545; color: white; }
        .bg-warning { background-color: #ffc107; color: #212529; }
        .bg-info { background-color: #17a2b8; color: white; }
        .bg-light { background-color: #f8f9fa; color: #212529; }
        .bg-dark { background-color: #343a40; color: white; }
        .border { border: 1px solid #dee2e6; }
        .rounded { border-radius: 4px; }
        .shadow { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        .mt-1 { margin-top: 0.25rem; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-3 { margin-top: 1rem; }
        .mt-4 { margin-top: 1.5rem; }
        .mt-5 { margin-top: 3rem; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 1rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        .mb-5 { margin-bottom: 3rem; }
        .p-1 { padding: 0.25rem; }
        .p-2 { padding: 0.5rem; }
        .p-3 { padding: 1rem; }
        .p-4 { padding: 1.5rem; }
        .p-5 { padding: 3rem; }
        
        /* Responsive styles */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                margin: 0 !important;
                padding: 10px !important;
            }
        }
    </style>
</head>
<body style="margin:0; padding:20px;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <!-- Container -->
                <table class="container" width="600" cellpadding="0" cellspacing="0" role="presentation" style="max-width:600px; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.05);">
                    
                    ' . $headerHtml . '

                    <!-- Hero / Body -->
                    <tr>
                        <td style="padding:28px; font-family: \'Segoe UI\', Roboto, Arial, sans-serif; color:#333;">
                            <div class="hero content-area" style="text-align:left;">
                                ' . $content . '
                            </div>
                        </td>
                    </tr>

                    ' . $footerHtml . '

                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    /**
     * Create beautiful email header
     */
    private function createEmailHeader($companyName, $companyLogo, $siteUrl)
    {
        // Get company settings from database
        $companySettings = \App\Models\SystemSetting::getMultiple([
            'company_name',
            'company_website',
            'company_logo',
            'company_phone',
            'company_email'
        ]);

        // Use database values or fallback to parameters
        $actualCompanyName = $companySettings['company_name'] ?? $companyName;
        $actualWebsite = $companySettings['company_website'] ?? $siteUrl;
        $actualLogo = $companySettings['company_logo'] ?? $companyLogo;

        // Create logo HTML with proper URL
        $logoHtml = '';
        if ($actualLogo) {
            $logoUrl = asset('storage/' . $actualLogo);
            $logoHtml = '<img src="' . $logoUrl . '" alt="' . $actualCompanyName . '" style="max-width:180px; height:auto; display:block; border:0;">';
        } else {
            $logoHtml = '<h1 style="margin: 0; color: #1e88e5; font-size: 24px; font-weight: 700;">' . $actualCompanyName . '</h1>';
        }

        return '<!-- Header -->
            <tr>
                <td style="padding:30px 20px; background-color:#ffffff; color:#1e88e5; text-align:center;">
                    <div style="display:inline-block; padding:0; border-radius:0; margin-bottom:10px;">
                        <a href="' . $actualWebsite . '" style="display:inline-block; text-decoration:none;">
                            ' . $logoHtml . '
                        </a>
                    </div>
                    <p style="margin:10px 0 0; font-size:16px; font-weight:normal; color:#555;">House Rent Management System</p>
                </td>
            </tr>';
    }

    /**
     * Create beautiful email footer
     */
    private function createEmailFooter($companyName, $siteUrl, $supportEmail)
    {
        // Get company settings from database
        $companySettings = \App\Models\SystemSetting::getMultiple([
            'company_name',
            'company_website',
            'company_phone',
            'company_email',
            'company_support_email',
            'company_facebook',
            'company_twitter',
            'company_linkedin',
            'company_instagram',
            'company_address'
        ]);

        // Use database values or fallback to parameters
        $actualCompanyName = $companySettings['company_name'] ?? $companyName;
        $actualWebsite = $companySettings['company_website'] ?? $siteUrl;
        $actualSupportEmail = $companySettings['company_support_email'] ?? $supportEmail;

        return '<!-- Footer -->
            <tr>
                <td style="text-align:center; padding:20px; background:#f1f6ff; font-size:12px; color:#666; font-family:Arial, sans-serif;">
                    <p style="margin:0 0 10px; font-size:14px; color:#333;">' . $actualCompanyName . ' — Smart Rental Solution</p>
                    <p style="margin:0 0 20px;">Need help? <a href="mailto:' . $actualSupportEmail . '" style="color:#1e88e5; text-decoration:none;">' . $actualSupportEmail . '</a></p>
                    
                    <!-- App Download -->
                    <div style="margin:20px 0;">
                        <a href="https://play.google.com/store/apps/details?id=com.barimanager" style="display:inline-block; margin:8px; text-decoration:none;">
                            <div style="background:#000; color:#fff; padding:8px 16px; border-radius:4px; font-size:12px; font-weight:bold; text-align:center; min-width:120px;">
                                📱 Google Play
                            </div>
                        </a>
                        <a href="https://apps.apple.com/app/idXXXXXXXX" style="display:inline-block; margin:8px; text-decoration:none;">
                            <div style="background:#000; color:#fff; padding:8px 16px; border-radius:4px; font-size:12px; font-weight:bold; text-align:center; min-width:120px;">
                                🍎 App Store
                            </div>
                        </a>
                    </div>
                    
                    <p style="margin:10px 0 0; color:#999; font-size:11px;">
                        You are receiving this email because you signed up at ' . $actualCompanyName . '.<br/>
                        If this wasn\'t you, <a href="' . $actualWebsite . '/unsubscribe" style="color:#1e88e5; text-decoration:none;">unsubscribe</a>.
                    </p>
                </td>
            </tr>';
    }
}
