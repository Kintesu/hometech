<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'requires_installation')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('requires_installation')->default(false)->after('stock_quantity');
            });
        }

        if (!Schema::hasTable('orders')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::table('orders')
                ->where('status', 'Confirmed')
                ->update(['status' => 'Pending']);

            DB::table('orders')
                ->where('status', 'AwaitingInstallation')
                ->update(['status' => 'Shipping']);

            DB::statement("ALTER TABLE orders MODIFY status ENUM('Pending','Shipping','Completed','Canceled') NULL DEFAULT 'Pending'");
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'received_amount')) {
                $table->decimal('received_amount', 15, 2)->nullable()->after('payment_method');
            }

            if (!Schema::hasColumn('orders', 'change_amount')) {
                $table->decimal('change_amount', 15, 2)->nullable()->after('received_amount');
            }

            if (!Schema::hasColumn('orders', 'delivery_address')) {
                $table->text('delivery_address')->nullable()->after('change_amount');
            }

            if (!Schema::hasColumn('orders', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('delivery_address');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                foreach (['received_amount', 'change_amount', 'delivery_address', 'created_by'] as $column) {
                    if (Schema::hasColumn('orders', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });

            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE orders MODIFY status ENUM('Pending','Confirmed','Shipping','Completed','Canceled') NULL DEFAULT 'Pending'");
            }
        }

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'requires_installation')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('requires_installation');
            });
        }
    }
};
