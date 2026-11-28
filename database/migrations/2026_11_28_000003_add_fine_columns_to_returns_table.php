<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->decimal('damage_fee', 12, 2)->default(0)->after('late_fee');
            $table->decimal('total_fine', 12, 2)->default(0)->after('damage_fee');
            // none = tidak ada denda, unpaid = belum dibayar, waiting = bukti diunggah (menunggu verifikasi), paid = lunas
            $table->enum('fine_status', ['none', 'unpaid', 'waiting', 'paid'])->default('none')->after('total_fine');
            $table->string('fine_payment_method', 50)->nullable()->after('fine_status');
            $table->string('fine_proof_image', 255)->nullable()->after('fine_payment_method');
            $table->dateTime('fine_paid_at')->nullable()->after('fine_proof_image');
        });

        // Data lama: denda keterlambatan yang sudah tercatat dianggap sudah dihitung ke total_fine.
        \Illuminate\Support\Facades\DB::table('returns')->where('late_fee', '>', 0)->update([
            'total_fine' => \Illuminate\Support\Facades\DB::raw('late_fee'),
            'fine_status' => 'unpaid',
        ]);
    }

    public function down(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->dropColumn(['damage_fee', 'total_fine', 'fine_status', 'fine_payment_method', 'fine_proof_image', 'fine_paid_at']);
        });
    }
};
