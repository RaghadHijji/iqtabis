<?php

namespace Database\Seeders;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoActivitySeeder extends Seeder
{
    /**
     * A few sample members who like and comment on the shared quotes,
     * so the site doesn't look empty in the demo.
     * Run after QuoteSeeder: php artisan db:seed --class=DemoActivitySeeder (safe to run again, no duplicates).
     */
    public function run(): void
    {
        $members = collect([
            'sara@iqtabis.test' => 'سارة',
            'noura@iqtabis.test' => 'نورة',
            'fahad@iqtabis.test' => 'فهد',
            'abdullah@iqtabis.test' => 'عبدالله',
            'reem@iqtabis.test' => 'ريم',
        ])->map(function ($name, $email) {
            $user = User::firstOrCreate(['email' => $email], ['name' => $name, 'password' => str()->random(32)]);
            $user->hasVerifiedEmail() || $user->markEmailAsVerified();

            return $user;
        });

        // quote => [who liked it, [who commented => what they wrote]]
        $activity = [
            'أَلَا بِذِكْرِ اللَّهِ تَطْمَئِنُّ الْقُلُوبُ' => [
                ['sara', 'noura', 'fahad', 'abdullah', 'reem'],
                ['noura' => 'آية تريح القلب، جزاكم الله خيرًا.'],
            ],
            'على قدر أهل العزم تأتي العزائم، وتأتي على قدر الكرام المكارم' => [
                ['fahad', 'abdullah', 'sara', 'reem'],
                ['abdullah' => 'المتنبي ما له مثيل', 'sara' => 'أحتاج هذا البيت كل صباح'],
            ],
            'ومن يتهيّب صعود الجبال، يعش أبد الدهر بين الحفر' => [
                ['reem', 'fahad', 'noura'],
                ['reem' => 'حفظته من أيام المدرسة وما زال يحمّسني'],
            ],
            'ما كل ما يتمنى المرء يدركه، تجري الرياح بما لا تشتهي السفن' => [
                ['noura', 'sara'],
                ['fahad' => 'صدق، بس نحاول ونتوكل على الله'],
            ],
            'وإنما الأمم الأخلاق ما بقيت، فإن همُ ذهبت أخلاقهم ذهبوا' => [
                ['abdullah', 'reem', 'noura'],
                ['noura' => 'بيت يختصر كل شيء'],
            ],
            'أعزّ مكانٍ في الدنى سرج سابحٍ، وخير جليسٍ في الزمان كتابُ' => [
                ['sara', 'reem'],
                ['sara' => 'وأنا أقول: خير جليس كتاب وكوب قهوة'],
            ],
            'Imagination is more important than knowledge.' => [
                ['fahad', 'abdullah'],
                [],
            ],
            'I can resist everything except temptation.' => [
                ['reem', 'sara', 'noura'],
                ['reem' => 'هذا أنا قدام الحلا'],
            ],
        ];

        foreach ($activity as $content => [$likers, $comments]) {
            $quote = Quote::where('content', $content)->first();
            if (! $quote) {
                continue;
            }

            $quote->likes()->syncWithoutDetaching(
                collect($likers)->map(fn ($who) => $members["$who@iqtabis.test"]->id)->all()
            );

            foreach ($comments as $who => $body) {
                $quote->comments()->firstOrCreate(['user_id' => $members["$who@iqtabis.test"]->id, 'body' => $body]);
            }
        }
    }
}
