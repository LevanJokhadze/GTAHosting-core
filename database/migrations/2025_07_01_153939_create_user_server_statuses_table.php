<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
    Schema::create('user_server_statuses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('server_id')->constrained('servers')->onDelete('cascade');
    $table->string('server_name');
    $table->boolean('is_active')->default(false); // ✅ დაემატა!
    $table->timestamps();

    $table->unique(['user_id', 'server_id']);
});

    }

    public function down(): void
    {
        Schema::dropIfExists('user_server_statuses');
    }
};
