<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplate extends Model
{
    protected $fillable = [
        'key',
        'name',
        'subject',
        'content',
        'category',
        'is_active',
        'priority',
        'description',
        'tags',
        'trigger_event',
        'trigger_conditions'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
        'trigger_conditions' => 'array'
    ];

    /**
     * Get tags as array
     */
    public function getTagsAttribute($value)
    {
        if (is_string($value)) {
            // Try to decode as JSON first
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            // If not valid JSON, treat as comma-separated string
            $value = str_replace(['"', "'"], '', $value);
            return array_filter(array_map('trim', explode(',', $value)));
        }
        return $value ?: [];
    }

    /**
     * Set tags from array or string
     */
    public function setTagsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['tags'] = json_encode(array_filter($value));
        } elseif (is_string($value)) {
            // If it's a comma-separated string, convert to array then to JSON
            $tags = array_filter(array_map('trim', explode(',', $value)));
            $this->attributes['tags'] = json_encode($tags);
        } else {
            $this->attributes['tags'] = json_encode([]);
        }
    }


    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get template by name
     */
    public static function getByName($name)
    {
        return self::where('name', $name)->active()->first();
    }

    /**
     * Get content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Check if content is HTML
     */
    public function isHtml()
    {
        return $this->content !== strip_tags($this->content);
    }

    /**
     * Get trigger information
     */
    public function getTriggerInfo()
    {
        if (!$this->trigger_event) {
            return null;
        }
        
        return \App\Config\EmailTriggers::getTrigger($this->trigger_event);
    }

    /**
     * Get available variables for this template's trigger
     */
    public function getAvailableVariables()
    {
        $triggerInfo = $this->getTriggerInfo();
        return $triggerInfo ? $triggerInfo['variables'] : [];
    }

    /**
     * Scope for templates with specific trigger
     */
    public function scopeWithTrigger($query, $triggerEvent)
    {
        return $query->where('trigger_event', $triggerEvent);
    }

    /**
     * Scope for templates without trigger (manual only)
     */
    public function scopeWithoutTrigger($query)
    {
        return $query->whereNull('trigger_event');
    }
}