<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::table('orders')
                ->where('status', 'Confirmed')
                ->update(['status' => 'Pending']);

            DB::table('orders')
                ->where('status', 'AwaitingInstallation')
                ->update(['status' => 'Shipping']);

            DB::statement("ALTER TABLE orders MODIFY status ENUM('Pending','Shipping','Completed','Canceled','InstallationFailed') NULL DEFAULT 'Pending'");
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'assigned_staff_tech_id')) {
                $table->unsignedBigInteger('assigned_staff_tech_id')->nullable()->after('created_by');
            }

            if (!Schema::hasColumn('orders', 'installation_assigned_at')) {
                $table->timestamp('installation_assigned_at')->nullable()->after('assigned_staff_tech_id');
            }

            if (!Schema::hasColumn('orders', 'installation_completed_at')) {
                $table->timestamp('installation_completed_at')->nullable()->after('installation_assigned_at');
            }
        });

        if (!Schema::hasTable('order_status_histories')) {
            Schema::create('order_status_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('changed_by')->nullable();
                $table->string('from_status')->nullable();
                $table->string('to_status');
                $table->text('reason')->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                foreach (['installation_completed_at', 'installation_assigned_at', 'assigned_staff_tech_id'] as $column) {
                    if (Schema::hasColumn('orders', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });

            if (DB::getDriverName() === 'mysql') {
                DB::table('orders')
                    ->where('status', 'InstallationFailed')
                    ->update(['status' => 'Canceled']);

                DB::statement("ALTER TABLE orders MODIFY status ENUM('Pending','Shipping','Completed','Canceled') NULL DEFAULT 'Pending'");
            }
        }
    }
};
