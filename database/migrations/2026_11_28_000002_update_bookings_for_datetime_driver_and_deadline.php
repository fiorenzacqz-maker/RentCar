<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('bookings', function (Blueprint $table) {
            // date -> datetime (mendukung jam pinjam & jam kembali)
            $table->dateTime('start_date')->change();
            $table->dateTime('end_date')->change();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('borrower_name', 100)->nullable()->after('car_id');
            $table->string('borrower_phone', 20)->nullable()->after('borrower_name');
            $table->boolean('with_driver')->default(false)->after('total_days');
            $table->decimal('driver_fee', 12, 2)->default(0)->after('with_driver');
            $table->dateTime('payment_deadline')->nullable()->after('booking_status');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['borrower_name', 'borrower_phone', 'with_driver', 'driver_fee', 'payment_deadline']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->date('start_date')->change();
            $table->date('end_date')->change();
        });
    }
};
