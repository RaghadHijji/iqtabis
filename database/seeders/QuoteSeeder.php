<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    /**
     * Real quotes with who said them, shared by the "Iqtabis team" account.
     * Run: php artisan db:seed --class=QuoteSeeder (safe to run again, no duplicates).
     */
    public function run(): void
    {
        $team = User::firstOrCreate(
            ['email' => 'team@iqtabis.test'],
            ['name' => 'فريق اقتبس', 'password' => str()->random(32)],
        );

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

        foreach ($quotes as [$category, $content, $author]) {
            Quote::firstOrCreate(
                ['content' => $content],
                [
                    'author' => $author,
                    'category_id' => Category::where('name', $category)->value('id'),
                    'user_id' => $team->id,
                ],
            );
        }
    }
}
