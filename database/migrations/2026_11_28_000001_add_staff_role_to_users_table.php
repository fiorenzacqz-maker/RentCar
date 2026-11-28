<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'staff', 'customer'])->default('customer')->change();
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Staff diturunkan menjadi customer agar nilai lama (admin/customer) tetap valid.
        \Illuminate\Support\Facades\DB::table('users')->where('role', 'staff')->update(['role' => 'customer']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'customer'])->default('customer')->change();
        });
    }
};
