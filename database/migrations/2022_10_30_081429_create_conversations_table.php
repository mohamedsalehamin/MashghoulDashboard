<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

 class CreateConversationsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId("reservation_id");
            $table->longText("token");
            $table->boolean("is_started")->default(0);
            $table->timestamp("customer_start_at")->nullable();
            $table->timestamp("contractor_start_at")->nullable();
            $table->timestamp("customer_end_at")->nullable();
            $table->timestamp("contractor_end_at")->nullable();
            $table->timestamp("finished_at")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('conversations');
    }
};
