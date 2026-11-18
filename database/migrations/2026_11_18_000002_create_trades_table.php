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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('strategy_id')->nullable()->constrained()->nullOnDelete();
            $table->string('strategy_name')->nullable();

            // Core Trade Details
            $table->string('instrument', 50); // e.g. NIFTY, BANKNIFTY, RELIANCE, BTCUSDT
            $table->string('direction', 10)->default('BUY'); // BUY / SELL (LONG / SHORT)
            $table->dateTime('entry_datetime');
            $table->dateTime('exit_datetime')->nullable();

            $table->decimal('quantity', 14, 4);
            $table->decimal('entry_price', 14, 2);
            $table->decimal('exit_price', 14, 2)->nullable();
            $table->decimal('stop_loss', 14, 2)->nullable();
            $table->decimal('target', 14, 2)->nullable();

            $table->string('exit_reason', 100)->nullable(); // e.g. Target Hit, Stop loss hit, Trailing SL, Manual exit
            $table->decimal('gross_pnl', 14, 2)->default(0.00); // Profit / Loss
            $table->decimal('charges', 14, 2)->default(0.00); // Brokerage + STT + Taxes + Exchange fees
            $table->decimal('net_pnl', 14, 2)->default(0.00); // Net Profit / Loss = gross_pnl - charges

            $table->string('screenshot')->nullable(); // Path to WebP converted screenshot
            $table->text('lessons')->nullable(); // What worked well, what to avoid
            $table->text('remarks')->nullable(); // General remarks / notes
            $table->timestamp('journal_datetime')->useCurrent(); // Server date time when stored in db

            $table->string('trade_status', 20)->default('CLOSED'); // CLOSED, OPEN
            $table->timestamps();

            $table->index(['user_id', 'entry_datetime']);
            $table->index(['user_id', 'instrument']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
