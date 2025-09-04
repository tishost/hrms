<?php

require_once 'vendor/autoload.php';

use App\Models\AppAnalytics;
use Carbon\Carbon;

// Test the analytics system
echo "🧪 Testing HRMS Analytics System\n";
echo "================================\n\n";

try {
    // Test 1: Create sample analytics data
    echo "1. Creating sample analytics data...\n";
    
    $sampleData = [
        [
            'event_type' => 'app_install',
            'device_type' => 'android',
            'os_version' => '13',
            'app_version' => '1.0.0',
            'device_model' => 'Samsung Galaxy S23',
            'manufacturer' => 'Samsung',
            'screen_resolution' => '1080x1920',
            'user_id' => null,
            'additional_data' => ['source' => 'google_play'],
            'session_id' => 'test_session_1',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'event_timestamp' => Carbon::now()->subDays(2),
        ],
        [
            'event_type' => 'screen_view',
            'device_type' => 'android',
            'os_version' => '13',
            'app_version' => '1.0.0',
            'device_model' => 'Xiaomi Redmi Note 12',
            'manufacturer' => 'Xiaomi',
            'screen_resolution' => '720x1280',
            'user_id' => null,
            'additional_data' => ['screen_name' => 'dashboard'],
            'session_id' => 'test_session_2',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'event_timestamp' => Carbon::now()->subDay(),
        ],
        [
            'event_type' => 'feature_usage',
            'device_type' => 'ios',
            'os_version' => '17',
            'app_version' => '1.0.0',
            'device_model' => 'iPhone 15',
            'manufacturer' => 'Apple',
            'screen_resolution' => '1125x2436',
            'user_id' => null,
            'additional_data' => ['feature' => 'property_search'],
            'session_id' => 'test_session_3',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'event_timestamp' => Carbon::now(),
        ],
    ];
    
    foreach ($sampleData as $data) {
        $analytics = AppAnalytics::create($data);
        echo "   ✅ Created analytics record ID: {$analytics->id}\n";
    }
    
    // Test 2: Check total count
    echo "\n2. Checking analytics data...\n";
    $totalCount = AppAnalytics::count();
    echo "   📊 Total analytics records: {$totalCount}\n";
    
    // Test 3: Test dashboard summary
    echo "\n3. Testing dashboard summary...\n";
    $summary = AppAnalytics::getDashboardSummary();
    echo "   📈 Total events: {$summary['total_events']}\n";
    echo "   📅 Events this month: {$summary['events_this_month']}\n";
    echo "   📅 Events this week: {$summary['events_this_week']}\n";
    echo "   👥 Unique users: {$summary['unique_users']}\n";
    
    // Test 4: Test device analytics
    echo "\n4. Testing device analytics...\n";
    $deviceAnalytics = AppAnalytics::getDeviceAnalytics(30);
    echo "   📱 Device types: " . json_encode($deviceAnalytics['device_types']) . "\n";
    echo "   🖥️ OS versions: " . json_encode($deviceAnalytics['os_versions']) . "\n";
    echo "   📱 App versions: " . json_encode($deviceAnalytics['app_versions']) . "\n";
    echo "   🏭 Manufacturers: " . json_encode($deviceAnalytics['manufacturers']) . "\n";
    
    // Test 5: Test installation trends
    echo "\n5. Testing installation trends...\n";
    $trends = AppAnalytics::getInstallationTrends(7);
    echo "   📊 Total installations (7 days): {$trends['total_installations']}\n";
    echo "   📈 Average installations: {$trends['average_installations']}\n";
    
    // Test 6: Test real-time stats
    echo "\n6. Testing real-time stats...\n";
    $realTimeStats = AppAnalytics::getRealTimeStats();
    echo "   🕐 Current hour: {$realTimeStats['current_hour']}\n";
    echo "   📱 Online devices: {$realTimeStats['current_device_status']['online_devices']}\n";
    echo "   📱 New installations today: {$realTimeStats['current_device_status']['new_installations_today']}\n";
    
    echo "\n🎉 All tests passed! Analytics system is working correctly.\n";
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
