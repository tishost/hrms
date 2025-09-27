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

        $mail = $this->subject($subject);

        if ($this->template->isHtml()) {
            $mail->html($content);
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
            $text = str_replace('{{' . $key . '}}', $value, $text);
        }

        return $text;
    }
}
