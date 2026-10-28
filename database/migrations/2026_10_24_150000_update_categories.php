<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * New category list: Religious first, Science added, Funny renamed to Humor, Success removed.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedInteger('position')->default(0)->after('name');
        });

        DB::table('categories')->where('name', 'Funny')->update(['name' => 'Humor']);

        foreach (['Religious', 'Science'] as $name) {
            if (! DB::table('categories')->where('name', $name)->exists()) {
                DB::table('categories')->insert(['name' => $name, 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        // quotes in "Success" move to "Motivation" before the category is removed
        $success = DB::table('categories')->where('name', 'Success')->value('id');
        $motivation = DB::table('categories')->where('name', 'Motivation')->value('id');
        if ($success && $motivation) {
            DB::table('quotes')->where('category_id', $success)->update(['category_id' => $motivation]);
        }
        DB::table('categories')->where('name', 'Success')->delete();

        // the order they show in on the site
        foreach (['Religious', 'Motivation', 'Love', 'Sad', 'Humor', 'Wisdom', 'Science'] as $i => $name) {
            DB::table('categories')->where('name', $name)->update(['position' => $i + 1]);
        }
    }

    public function down(): void
    {
        DB::table('categories')->where('name', 'Humor')->update(['name' => 'Funny']);
        if (! DB::table('categories')->where('name', 'Success')->exists()) {
            DB::table('categories')->insert(['name' => 'Success', 'created_at' => now(), 'updated_at' => now()]);
        }

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
