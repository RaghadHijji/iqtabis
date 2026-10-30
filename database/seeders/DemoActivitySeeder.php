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

        // more sample members, each liking a fixed handful of the shared quotes
        // (the same picks every run, so running it again adds nothing new)
        $readers = collect([
            'lujain' => 'لجين', 'maha' => 'مها', 'hessa' => 'حصة', 'aseel' => 'أسيل', 'dana' => 'دانة',
            'shoug' => 'شوق', 'wajd' => 'وجد', 'rahaf' => 'رهف', 'jana' => 'جنى', 'layan' => 'ليان',
            'atheer' => 'أثير', 'bayan' => 'بيان', 'faisal' => 'فيصل', 'saud' => 'سعود', 'majed' => 'ماجد',
            'omar' => 'عمر', 'ziyad' => 'زياد', 'rakan' => 'راكان', 'hamad' => 'حمد', 'meshal' => 'مشعل',
            'sultan' => 'سلطان', 'yazeed' => 'يزيد', 'talal' => 'طلال', 'hind' => 'هند', 'amal' => 'أمل',
        ])->map(function ($name, $handle) {
            $user = User::firstOrCreate(['email' => "$handle@iqtabis.test"], ['name' => $name, 'password' => str()->random(32)]);
            $user->hasVerifiedEmail() || $user->markEmailAsVerified();

            return $user;
        });

        $quotes = Quote::whereHas('user', fn ($query) => $query->where('email', 'like', '%@iqtabis.test'))->orderBy('id')->get();
        foreach ($quotes as $quote) {
            $likers = $readers->filter(fn ($reader, $handle) => crc32($handle.$quote->content) % 100 < 22);
            $quote->likes()->syncWithoutDetaching($likers->pluck('id')->all());
        }
    }
}
