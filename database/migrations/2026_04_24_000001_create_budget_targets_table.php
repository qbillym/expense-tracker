<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('target_amount', 15, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'start_date']);
            $table->index(['user_id', 'end_date']);
            $table->index(['user_id', 'locked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_targets');
    }
};
