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
        // Check if the username column already exists
        if (!Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                // Add username column as nullable first
                $table->string('username')->nullable()->after('name');
            });
        }
        
        // Update existing users with default usernames based on their email or name
        DB::table('users')->orderBy('id')->whereNull('username')->chunk(100, function($users) {
            foreach($users as $user) {
                $defaultUsername = '';
                
                // Use email prefix if available
                if($user->email && !empty(trim($user->email))) {
                    $emailParts = explode('@', $user->email);
                    $defaultUsername = $emailParts[0];
                } else {
                    // Use name as fallback, convert to lowercase and replace spaces
                    $defaultUsername = strtolower(str_replace(' ', '_', $user->name));
                }
                
                // Make sure username is unique
                $originalUsername = $defaultUsername;
                $counter = 1;
                $newUsername = $defaultUsername;
                
                // Check if username already exists and append number if needed
                while(DB::table('users')->where('username', $newUsername)->where('id', '!=', $user->id)->exists()) {
                    $newUsername = $originalUsername . $counter;
                    $counter++;
                }
                
                DB::table('users')->where('id', $user->id)->update(['username' => $newUsername]);
            }
        });
        
        // Now make the username column unique if it's not already
        if (Schema::hasColumn('users', 'username')) {
            // Remove any potential duplicate usernames first
            $this->removeDuplicateUsernames();
            
            // Check if unique constraint already exists
            $indexExists = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'users' 
                AND CONSTRAINT_NAME = 'users_username_unique'
                AND CONSTRAINT_TYPE = 'UNIQUE'
            ");
            
            if (empty($indexExists)) {
                Schema::table('users', function (Blueprint $table) {
                    $table->unique('username');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
    
    /**
     * Remove duplicate usernames by appending counter
     */
    private function removeDuplicateUsernames()
    {
        $usernames = DB::table('users')->select('username')->whereNotNull('username')->get();
        $usedUsernames = [];
        
        foreach ($usernames as $user) {
            if (isset($usedUsernames[$user->username])) {
                // This username is a duplicate, need to fix it
                $originalUsername = $user->username;
                $counter = 1;
                do {
                    $newUsername = $originalUsername . $counter;
                    $counter++;
                } while (DB::table('users')->where('username', $newUsername)->exists());
                
                // Update the user with the new unique username
                DB::table('users')->where('username', $originalUsername)->where('username', $originalUsername)->update(['username' => $newUsername]);
            }
            $usedUsernames[$user->username] = true;
        }
    }
};
