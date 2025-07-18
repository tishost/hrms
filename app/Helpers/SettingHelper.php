<?php

use App\Models\SystemSetting;

if (! function_exists('setting')) {
    function setting($key, $default = null) {
        return SystemSetting::where('key', $key)->value('value') ?? $default;
    }
}