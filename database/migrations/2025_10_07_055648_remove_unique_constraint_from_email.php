<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            // Remove unique constraint from email in users table
            $constraintName = $this->getEmailUniqueConstraintName();
            if ($constraintName) {
                Schema::table('users', function (Blueprint $table) use ($constraintName) {
                    $table->dropUnique([$constraintName]);
                });
            }
        } catch (\Exception $e) {
            // If constraint doesn't exist, continue
            // The unique constraint might already be removed or named differently
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unique('email');
        });
    }
    
    /**
     * Get the unique constraint name for email column
     */
    private function getEmailUniqueConstraintName(): ?string
    {
        $result = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'users' 
            AND CONSTRAINT_TYPE = 'UNIQUE'
            AND CONSTRAINT_NAME LIKE '%email%'
        ");
        
        if (!empty($result)) {
            return $result[0]->CONSTRAINT_NAME;
        }
        
        return null;
    }
};
