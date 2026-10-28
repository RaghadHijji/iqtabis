<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The "Science" category becomes "Facts" (حقائق), and the two team quotes that were in it move to Wisdom,
     * because they are wise sayings, not facts.
     */
    public function up(): void
    {
        DB::table('categories')->where('name', 'Science')->update(['name' => 'Facts']);

        $wisdom = DB::table('categories')->where('name', 'Wisdom')->value('id');
        if ($wisdom) {
            DB::table('quotes')
                ->whereIn('content', ['أعزّ مكانٍ في الدنى سرج سابحٍ، وخير جليسٍ في الزمان كتابُ', 'Imagination is more important than knowledge.'])
                ->update(['category_id' => $wisdom]);
        }
    }

    public function down(): void
    {
        DB::table('categories')->where('name', 'Facts')->update(['name' => 'Science']);
    }
};
