<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                // Using big integer IDs because the app model expects numeric IDs
                $table->bigIncrements('id');

                // Polymorphic relation to notifiable models (e.g., users)
                $table->unsignedBigInteger('notifiable_id');
                $table->string('notifiable_type');

                // App-specific columns used by NotificationHelper when saving
                $table->string('title')->nullable();
                $table->text('body')->nullable();

                // Logical notification type used in the app (e.g., rent_reminder)
                $table->string('type')->index();

                // Data payload stored as JSON (saved as text for compatibility)
                $table->text('data')->nullable();

                // Optional rich-notification fields
                $table->string('image_url')->nullable();
                $table->string('action_url')->nullable();

                // FCM message id reference
                $table->string('fcm_message_id')->nullable();

                // Read status
                $table->timestamp('read_at')->nullable();

                $table->timestamps();

                // Helpful indexes
                $table->index(['notifiable_type', 'notifiable_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};


