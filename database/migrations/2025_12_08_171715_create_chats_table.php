<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Added for DB::statement

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user1_id')->constrained('users');
            $table->foreignId('user2_id')->constrained('users');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->unique(['user1_id', 'user2_id']);
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained('chats')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->string('file')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::table('chats', function (Blueprint $table) {
            $table->foreignId('last_message_id')->nullable()->constrained('messages')->onDelete('set null');
            $table->enum('status', ['active', 'closed'])->default('active')->after('appointment_id');
            $table->uuid('uuid')->unique()->after('id');
        });

        // Generate UUIDs for existing chats
        DB::statement('UPDATE chats SET uuid = UUID()');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign(['last_message_id']);
            $table->dropColumn('last_message_id');
            $table->dropColumn('status');
            $table->dropColumn('uuid');
        });

        Schema::dropIfExists('messages');
        Schema::dropIfExists('chats');
    }
};
