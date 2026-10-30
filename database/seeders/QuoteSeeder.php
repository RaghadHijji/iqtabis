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
            ['Wisdom', 'وعين الرضا عن كل عيبٍ كليلةٌ، ولكنّ عين السخط تبدي المساويا', 'عبد الله بن معاوية'],
            ['Wisdom', 'وإنما الأمم الأخلاق ما بقيت، فإن همُ ذهبت أخلاقهم ذهبوا', 'أحمد شوقي'],
            ['Wisdom', 'أعزّ مكانٍ في الدنى سرج سابحٍ، وخير جليسٍ في الزمان كتابُ', 'المتنبي'],
            ['Wisdom', 'Imagination is more important than knowledge.', 'Albert Einstein'],
            ['Facts', 'يحتاج ضوء الشمس نحو ثماني دقائق ليصل إلى الأرض.', 'حقيقة علمية'],
            ['Facts', 'يدق قلب الإنسان نحو مئة ألف مرة في اليوم.', 'حقيقة علمية'],
            ['Humor', 'I can resist everything except temptation.', 'Oscar Wilde'],
            ['Religious', 'فَإِنَّ مَعَ الْعُسْرِ يُسْرًا', 'القرآن الكريم، سورة الشرح'],
            ['Religious', 'وَقُل رَّبِّ زِدْنِي عِلْمًا', 'القرآن الكريم، سورة طه'],
            ['Religious', 'تبسّمك في وجه أخيك لك صدقة', 'حديث شريف، رواه الترمذي'],
            ['Religious', 'خيركم من تعلّم القرآن وعلّمه', 'حديث شريف، رواه البخاري'],
            ['Motivation', 'إذا غامرتَ في شرفٍ مرومِ، فلا تقنع بما دون النجومِ', 'المتنبي'],
            ['Motivation', 'إذا الشعب يومًا أراد الحياة، فلا بد أن يستجيب القدر', 'أبو القاسم الشابي'],
            ['Motivation', 'وما نيل المطالب بالتمني، ولكن تؤخذ الدنيا غلابا', 'أحمد شوقي'],
            ['Love', 'نقّل فؤادك حيث شئت من الهوى، ما الحب إلا للحبيب الأولِ', 'أبو تمام'],
            ['Love', 'إن العيون التي في طرفها حَوَرٌ، قتلننا ثم لم يحيين قتلانا', 'جرير'],
            ['Love', 'Love all, trust a few, do wrong to none.', 'William Shakespeare'],
            ['Sad', 'عيدٌ بأية حالٍ عدت يا عيدُ، بما مضى أم بأمرٍ فيك تجديدُ', 'المتنبي'],
            ['Sad', 'أعلّل النفس بالآمال أرقبها، ما أضيق العيش لولا فسحة الأملِ', 'الطغرائي'],
            ['Sad', 'وظلم ذوي القربى أشدّ مضاضةً، على المرء من وقع الحسام المهنّدِ', 'طرفة بن العبد'],
            ['Humor', 'I am so clever that sometimes I don\'t understand a single word of what I am saying.', 'Oscar Wilde'],
            ['Humor', 'The trouble with having an open mind, of course, is that people will insist on coming along and trying to put things in it.', 'Terry Pratchett'],
            ['Wisdom', 'إذا رأيت نيوب الليث بارزةً، فلا تظنّنّ أن الليث يبتسمُ', 'المتنبي'],
            ['Wisdom', 'لسانك حصانك، إن صنته صانك', 'مثل عربي'],
            ['Facts', 'العسل لا يفسد، ووُجد عسل صالح للأكل في مقابر مصرية عمرها آلاف السنين.', 'حقيقة علمية'],
            ['Facts', 'للأخطبوط ثلاثة قلوب.', 'حقيقة علمية'],
            ['Facts', 'يتكوّن هيكل الإنسان البالغ من 206 عظام.', 'حقيقة علمية'],
            ['Motivation', 'ومن لم يذق مرّ التعلّم ساعةً، تجرّع ذلّ الجهل طول حياته', 'الإمام الشافعي'],
            ['Love', 'أحبّك حبّين: حبّ الهوى، وحبًّا لأنك أهلٌ لذاكا', 'رابعة العدوية'],
            ['Love', 'قفا نبكِ من ذكرى حبيبٍ ومنزلِ، بسقط اللوى بين الدخول فحوملِ', 'امرؤ القيس'],
            ['Love', 'يا ليلُ الصبُّ متى غدُه، أقيامُ الساعة موعدُه', 'الحصري القيرواني'],
            ['Sad', 'يذكّرني طلوعُ الشمس صخرًا، وأذكره لكل غروب شمسِ', 'الخنساء'],
            ['Humor', 'Life is far too important a thing ever to talk seriously about it.', 'Oscar Wilde'],
            ['Humor', 'To love oneself is the beginning of a lifelong romance.', 'Oscar Wilde'],
            ['Sad', 'رجع بخُفَّي حُنين', 'مثل عربي'],
            ['Facts', 'يغلي الماء عند 100 درجة مئوية عند مستوى سطح البحر.', 'حقيقة علمية'],
            ['Poetry', 'الخيل والليل والبيداء تعرفني، والسيف والرمح والقرطاس والقلم', 'المتنبي'],
            ['Poetry', 'أنا الذي نظر الأعمى إلى أدبي، وأسمعت كلماتي من به صممُ', 'المتنبي'],
            ['Poetry', 'ستبدي لك الأيام ما كنت جاهلًا، ويأتيك بالأخبار من لم تزوّدِ', 'طرفة بن العبد'],
            ['Poetry', 'دع الأيام تفعل ما تشاء، وطب نفسًا إذا حكم القضاءُ', 'الإمام الشافعي'],
            ['Poetry', 'أنا البحر في أحشائه الدرّ كامنٌ، فهل سألوا الغوّاص عن صدفاتي', 'حافظ إبراهيم'],
            ['Poetry', 'سلامٌ من صبا بردى أرقّ، ودمعٌ لا يُكفكف يا دمشقُ', 'أحمد شوقي'],
            ['Poetry', 'الحبّ في الأرض بعضٌ من تخيّلنا، لو لم نجده عليها لاخترعناه', 'نزار قباني'],
            ['Poetry', 'على هذه الأرض ما يستحقّ الحياة', 'محمود درويش'],
        ];

        foreach ($quotes as $i => [$category, $content, $author]) {
            $quote = Quote::firstOrNew(['content' => $content]);
            $quote->forceFill([
                'author' => $author,
                'category_id' => Category::where('name', $category)->value('id'),
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
