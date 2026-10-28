<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    /**
     * Real quotes with who said them, shared by a few sample members.
     * Run: php artisan db:seed --class=QuoteSeeder (safe to run again, no duplicates).
     * Running it again also moves quotes from the old "Iqtabis team" account to these members.
     */
    public function run(): void
    {
        $members = collect([
            'lama@iqtabis.test' => 'لمى',
            'khalid@iqtabis.test' => 'خالد',
            'haya@iqtabis.test' => 'هيا',
            'yousef@iqtabis.test' => 'يوسف',
            'jood@iqtabis.test' => 'جود',
            'mashael@iqtabis.test' => 'مشاعل',
            'nawaf@iqtabis.test' => 'Nawaf',
            'shahad@iqtabis.test' => 'شهد',
            'turki@iqtabis.test' => 'تركي',
            'ghala@iqtabis.test' => 'Ghala',
            'bader@iqtabis.test' => 'بدر',
            'raneem@iqtabis.test' => 'رنيم',
        ])->map(function ($name, $email) {
            $user = User::firstOrCreate(['email' => $email], ['name' => $name, 'password' => str()->random(32)]);
            $user->name === $name || $user->update(['name' => $name]);
            $user->hasVerifiedEmail() || $user->markEmailAsVerified();

            return $user;
        })->values();

        $quotes = [
            ['Religious', 'أَلَا بِذِكْرِ اللَّهِ تَطْمَئِنُّ الْقُلُوبُ', 'القرآن الكريم، سورة الرعد'],
            ['Religious', 'الكلمة الطيبة صدقة', 'حديث شريف، متفق عليه'],
            ['Motivation', 'على قدر أهل العزم تأتي العزائم، وتأتي على قدر الكرام المكارم', 'المتنبي'],
            ['Motivation', 'ومن يتهيّب صعود الجبال، يعش أبد الدهر بين الحفر', 'أبو القاسم الشابي'],
            ['Love', 'أراك عصيّ الدمع شيمتك الصبر، أما للهوى نهيٌ عليك ولا أمر؟', 'أبو فراس الحمداني'],
            ['Sad', 'ما كل ما يتمنى المرء يدركه، تجري الرياح بما لا تشتهي السفن', 'المتنبي'],
            ['Sad', 'لكل شيءٍ إذا ما تمّ نقصانُ، فلا يُغرّ بطيب العيش إنسانُ', 'أبو البقاء الرندي'],
            ['Wisdom', 'وعين الرضا عن كل عيبٍ كليلةٌ، ولكنّ عين السخط تبدي المساويا', 'الإمام الشافعي'],
            ['Wisdom', 'وإنما الأمم الأخلاق ما بقيت، فإن همُ ذهبت أخلاقهم ذهبوا', 'أحمد شوقي'],
            ['Wisdom', 'أعزّ مكانٍ في الدنى سرج سابحٍ، وخير جليسٍ في الزمان كتابُ', 'المتنبي'],
            ['Wisdom', 'Imagination is more important than knowledge.', 'Albert Einstein'],
            ['Facts', 'يحتاج ضوء الشمس نحو ثماني دقائق ليصل إلى الأرض.', 'حقيقة علمية'],
            ['Facts', 'يدق قلب الإنسان نحو مئة ألف مرة في اليوم.', 'حقيقة علمية'],
            ['Humor', 'I can resist everything except temptation.', 'Oscar Wilde'],
        ];

        foreach ($quotes as $i => [$category, $content, $author]) {
            $quote = Quote::firstOrNew(['content' => $content]);
            $quote->forceFill([
                'author' => $quote->author ?? $author,
                'category_id' => $quote->category_id ?? Category::where('name', $category)->value('id'),
                'user_id' => $members[$i % $members->count()]->id,
            ])->save();
        }

        // the old shared "Iqtabis team" account: its other quotes move to the members too, then it goes
        if ($team = User::where('email', 'team@iqtabis.test')->first()) {
            $team->quotes()->get()->each(fn ($quote, $i) => $quote->forceFill(['user_id' => $members[$i % $members->count()]->id])->save());
            $team->delete();
        }
    }
}
