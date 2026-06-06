<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->string('phone', 20)->nullable();
                $table->text('address')->nullable();
            });
        }

        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'supplier_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('supplier_id')->nullable()->after('category_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'supplier_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('supplier_id');
            });
        }

        Schema::dropIfExists('suppliers');
    }
};
