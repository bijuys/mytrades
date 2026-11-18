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
        Schema::create('newsletters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->string('subject');
            $table->longText('content');
            $table->string('target_audience', 30)->default('approved'); // 'approved', 'all'
            $table->unsignedInteger('recipients_count')->default(0);
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletters');
    }
};
