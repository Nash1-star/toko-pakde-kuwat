<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure default operator Pakde Kuwat exists with PIN 1234
        $user = User::firstOrNew(['name' => 'Pakde Kuwat']);
        $user->pin = '1234';
        $user->role = 'operator';
        $user->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't remove in rollback to avoid accidental data loss
    }
};
