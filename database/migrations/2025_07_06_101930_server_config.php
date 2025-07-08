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
        Schema::create('server_config', function (Blueprint $table) {
        $table->string('server_name');
        $table->integer("max_players");
        $table->string("gamemode");
        $table->integer("stream_distance")->default(500.0);
        $table->boolean("announce");
        $table->boolean("cSharp");
        $table->string("port");
        $table->boolean("voice_chat");
        $table->integer("voice_chat_sample_rate")->default(48000);
        $table->string("bind")->default("0.0.0.0");
        $table->timestamps();
    });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
