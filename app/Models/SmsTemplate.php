<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsTemplate extends Model
{
    protected $fillable = [
        'name',
        'content_bangla',
        'content_english',
        'variables',
        'category',
        'is_active',
        'description',
        'character_limit',
        'priority',
        'tags',
        'unicode_support',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'variables' => 'array',
        'tags' => 'array',
        'is_active' => 'boolean',
        'unicode_support' => 'boolean',
        'character_limit' => 'integer',
        'priority' => 'integer'
    ];

    /**
     * Get the user who created this template
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this template
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
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
     * Get content by language
     */
    public function getContent($language = 'bangla')
    {
        if ($language === 'english') {
            return $this->content_english;
        }
        return $this->content_bangla;
    }

    /**
     * Check if content exceeds character limit
     */
    public function exceedsCharacterLimit($content)
    {
        return strlen($content) > $this->character_limit;
    }

    /**
     * Get character count for content
     */
    public function getCharacterCount($content)
    {
        return strlen($content);
    }
}