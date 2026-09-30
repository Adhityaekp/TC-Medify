<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('kategori_item_master_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_item_id')->constrained('kategori_items')->cascadeOnDelete();
            $table->foreignId('master_item_id')->constrained('master_items')->cascadeOnDelete();
            $table->timestamps();

            // satu item tidak boleh masuk kategori yang sama dua kali
            $table->unique(['kategori_item_id', 'master_item_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('kategori_item_master_item');
    }
};
