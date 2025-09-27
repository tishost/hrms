<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{
    /**
     * Display template list overview page with email and SMS templates
     */
    public function index()
    {
        $this->checkSuperAdmin();
        
        // Get email templates grouped by category
        $systemEmailTemplates = EmailTemplate::where('category', 'system')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get();
            
        $ownerEmailTemplates = EmailTemplate::where('category', 'owner')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get();
            
        $tenantEmailTemplates = EmailTemplate::where('category', 'tenant')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        // Get SMS templates grouped by category
        $systemSmsTemplates = SmsTemplate::where('category', 'system')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get();
            
        $ownerSmsTemplates = SmsTemplate::where('category', 'owner')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get();
            
        $tenantSmsTemplates = SmsTemplate::where('category', 'tenant')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.templates.index', compact(
            'systemEmailTemplates',
            'ownerEmailTemplates', 
            'tenantEmailTemplates',
            'systemSmsTemplates',
            'ownerSmsTemplates',
            'tenantSmsTemplates'
        ));
    }

    /**
     * Display email templates list
     */
    public function emailIndex()
    {
        $this->checkSuperAdmin();
        
        // Get templates grouped by category
        $systemTemplates = EmailTemplate::where('category', 'system')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get();
            
        $ownerTemplates = EmailTemplate::where('category', 'owner')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get();
            
        $tenantTemplates = EmailTemplate::where('category', 'tenant')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.templates.email.index', compact(
            'systemTemplates',
            'ownerTemplates', 
            'tenantTemplates'
        ));
    }

    /**
     * Display SMS templates list
     */
    public function smsIndex()
    {
        $this->checkSuperAdmin();
        
        // Get templates grouped by category
        $systemTemplates = SmsTemplate::where('category', 'system')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get();
            
        $ownerTemplates = SmsTemplate::where('category', 'owner')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get();
            
        $tenantTemplates = SmsTemplate::where('category', 'tenant')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.templates.sms.index', compact(
            'systemTemplates',
            'ownerTemplates', 
            'tenantTemplates'
        ));
    }

    /**
     * Show the form for creating a new email template
     */
    public function createEmail()
    {
        $this->checkSuperAdmin();
        
        $availableTriggers = \App\Config\EmailTriggers::getAvailableTriggers();
        $triggerCategories = \App\Config\EmailTriggers::getCategories();
        
        return view('admin.templates.email.create', compact('availableTriggers', 'triggerCategories'));
    }

    /**
     * Show the form for creating a new SMS template
     */
    public function createSms()
    {
        $this->checkSuperAdmin();
        
        $availableTriggers = \App\Config\EmailTriggers::getAvailableTriggers();
        $triggerCategories = \App\Config\EmailTriggers::getCategories();
        
        return view('admin.templates.sms.create', compact('availableTriggers', 'triggerCategories'));
    }

    /**
     * Store a newly created email template
     */
    public function storeEmail(Request $request)
    {
        $this->checkSuperAdmin();
        
        // Determine which content field to use
        $isHtmlMode = $request->has('html_content') && !empty($request->html_content);
        
        if ($isHtmlMode) {
            $request->validate([
                'name' => 'required|string|max:255|unique:email_templates,name',
                'subject' => 'required|string|max:255',
                'html_content' => 'required|string',
                'category' => 'required|in:system,owner,tenant',
                'priority' => 'integer|min:1|max:10',
                'description' => 'nullable|string|max:500',
                'tags' => 'nullable|string',
                'trigger_event' => 'nullable|string',
                'trigger_conditions' => 'nullable|array',
                'is_active' => 'boolean'
            ]);
            $content = $request->html_content;
        } else {
            $request->validate([
                'name' => 'required|string|max:255|unique:email_templates,name',
                'subject' => 'required|string|max:255',
                'content' => 'required|string',
                'category' => 'required|in:system,owner,tenant',
                'priority' => 'integer|min:1|max:10',
                'description' => 'nullable|string|max:500',
                'tags' => 'nullable|string',
                'trigger_event' => 'nullable|string',
                'trigger_conditions' => 'nullable|array',
                'is_active' => 'boolean'
            ]);
            $content = $request->content;
        }

        $template = EmailTemplate::create([
            'key' => strtolower(str_replace(' ', '_', $request->name)),
            'name' => $request->name,
            'subject' => $request->subject,
            'content' => $content,
            'category' => $request->category,
            'priority' => $request->priority ?? 5,
            'description' => $request->description,
            'tags' => $request->tags,
            'trigger_event' => $request->trigger_event,
            'trigger_conditions' => $request->trigger_conditions,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Email template created successfully!');
    }

    /**
     * Store a newly created SMS template
     */
    public function storeSms(Request $request)
    {
        $this->checkSuperAdmin();
        
        $request->validate([
            'name' => 'required|string|max:255|unique:sms_templates,name',
            'content' => 'required|string|max:1000',
            'category' => 'required|in:system,owner,tenant',
            'priority' => 'integer|min:1|max:10',
            'description' => 'nullable|string|max:500',
            'tags' => 'nullable|string',
            'character_limit' => 'integer|min:1|max:1000',
            'unicode_support' => 'boolean',
            'trigger_event' => 'nullable|string',
            'trigger_conditions' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        $template = SmsTemplate::create([
            'key' => strtolower(str_replace(' ', '_', $request->name)),
            'name' => $request->name,
            'content' => $request->content,
            'category' => $request->category,
            'priority' => $request->priority ?? 5,
            'description' => $request->description,
            'tags' => $request->tags,
            'character_limit' => $request->character_limit ?? 160,
            'unicode_support' => $request->has('unicode_support'),
            'trigger_event' => $request->trigger_event,
            'trigger_conditions' => $request->trigger_conditions,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.templates.index')
            ->with('success', 'SMS template created successfully!');
    }

    /**
     * Display the specified email template
     */
    public function showEmail(EmailTemplate $emailTemplate)
    {
        $this->checkSuperAdmin();
        
        return view('admin.templates.email.show', compact('emailTemplate'));
    }

    /**
     * Display the specified SMS template
     */
    public function showSms(SmsTemplate $smsTemplate)
    {
        $this->checkSuperAdmin();
        
        return view('admin.templates.sms.show', compact('smsTemplate'));
    }

    /**
     * Show the form for editing the specified email template
     */
    public function editEmail(EmailTemplate $emailTemplate)
    {
        $this->checkSuperAdmin();
        
        $availableTriggers = \App\Config\EmailTriggers::getAvailableTriggers();
        $triggerCategories = \App\Config\EmailTriggers::getCategories();
        
        return view('admin.templates.email.edit', compact('emailTemplate', 'availableTriggers', 'triggerCategories'));
    }

    /**
     * Show the form for editing the specified SMS template
     */
    public function editSms(SmsTemplate $smsTemplate)
    {
        $this->checkSuperAdmin();
        
        $availableTriggers = \App\Config\EmailTriggers::getAvailableTriggers();
        $triggerCategories = \App\Config\EmailTriggers::getCategories();
        
        return view('admin.templates.sms.edit', compact('smsTemplate', 'availableTriggers', 'triggerCategories'));
    }

    /**
     * Update the specified email template
     */
    public function updateEmail(Request $request, EmailTemplate $emailTemplate)
    {
        $this->checkSuperAdmin();
        
        // Determine which content field to use
        $isHtmlMode = $request->has('html_content') && !empty($request->html_content);
        
        if ($isHtmlMode) {
            $request->validate([
                'name' => 'required|string|max:255|unique:email_templates,name,' . $emailTemplate->id,
                'subject' => 'required|string|max:255',
                'html_content' => 'required|string',
                'category' => 'required|in:system,owner,tenant',
                'priority' => 'integer|min:1|max:10',
                'description' => 'nullable|string|max:500',
                'tags' => 'nullable|string',
                'trigger_event' => 'nullable|string',
                'trigger_conditions' => 'nullable|array',
                'is_active' => 'boolean'
            ]);
            $content = $request->html_content;
        } else {
            $request->validate([
                'name' => 'required|string|max:255|unique:email_templates,name,' . $emailTemplate->id,
                'subject' => 'required|string|max:255',
                'content' => 'required|string',
                'category' => 'required|in:system,owner,tenant',
                'priority' => 'integer|min:1|max:10',
                'description' => 'nullable|string|max:500',
                'tags' => 'nullable|string',
                'trigger_event' => 'nullable|string',
                'trigger_conditions' => 'nullable|array',
                'is_active' => 'boolean'
            ]);
            $content = $request->content;
        }

        $emailTemplate->update([
            'name' => $request->name,
            'subject' => $request->subject,
            'content' => $content,
            'category' => $request->category,
            'priority' => $request->priority ?? 5,
            'description' => $request->description,
            'tags' => $request->tags,
            'trigger_event' => $request->trigger_event,
            'trigger_conditions' => $request->trigger_conditions,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Email template updated successfully!');
    }

    /**
     * Update the specified SMS template
     */
    public function updateSms(Request $request, SmsTemplate $smsTemplate)
    {
        $this->checkSuperAdmin();
        
        $request->validate([
            'name' => 'required|string|max:255|unique:sms_templates,name,' . $smsTemplate->id,
            'content' => 'required|string|max:1000',
            'category' => 'required|in:system,owner,tenant',
            'priority' => 'integer|min:1|max:10',
            'description' => 'nullable|string|max:500',
            'tags' => 'nullable|string',
            'character_limit' => 'integer|min:1|max:1000',
            'unicode_support' => 'boolean',
            'trigger_event' => 'nullable|string',
            'trigger_conditions' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        $smsTemplate->update([
            'name' => $request->name,
            'content' => $request->content,
            'category' => $request->category,
            'priority' => $request->priority ?? 5,
            'description' => $request->description,
            'tags' => $request->tags,
            'character_limit' => $request->character_limit ?? 160,
            'unicode_support' => $request->has('unicode_support'),
            'trigger_event' => $request->trigger_event,
            'trigger_conditions' => $request->trigger_conditions,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.templates.index')
            ->with('success', 'SMS template updated successfully!');
    }

    /**
     * Remove the specified email template
     */
    public function destroyEmail(EmailTemplate $emailTemplate)
    {
        $this->checkSuperAdmin();
        
        $emailTemplate->delete();

        return redirect()->route('admin.templates.index')
            ->with('success', 'Email template deleted successfully!');
    }

    /**
     * Remove the specified SMS template
     */
    public function destroySms(SmsTemplate $smsTemplate)
    {
        $this->checkSuperAdmin();
        
        $smsTemplate->delete();

        return redirect()->route('admin.templates.index')
            ->with('success', 'SMS template deleted successfully!');
    }

    /**
     * Toggle email template active status
     */
    public function toggleEmailStatus(EmailTemplate $emailTemplate)
    {
        $this->checkSuperAdmin();
        
        $emailTemplate->update([
            'is_active' => !$emailTemplate->is_active
        ]);

        $status = $emailTemplate->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('admin.templates.index')
            ->with('success', "Email template {$status} successfully!");
    }

    /**
     * Toggle SMS template active status
     */
    public function toggleSmsStatus(SmsTemplate $smsTemplate)
    {
        $this->checkSuperAdmin();
        
        $smsTemplate->update([
            'is_active' => !$smsTemplate->is_active
        ]);

        $status = $smsTemplate->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('admin.templates.index')
            ->with('success', "SMS template {$status} successfully!");
    }

    /**
     * Check if user is super admin
     */
    private function checkSuperAdmin()
    {
        if (!Auth::check() || !Auth::user()->hasRole('super_admin')) {
            abort(403, 'Unauthorized access');
        }
    }
}
