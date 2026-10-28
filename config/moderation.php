<?php

/*
| Content rules for quotes (FR-16).
| A quote that contains one of these words is refused when it is shared or edited.
| Add more words here; Arabic words are matched without diacritics and with ال / و / ب / ف / ل in front.
*/

return [

    // politics is not allowed on the site
    'politics' => [
        'سياسة', 'سياسه', 'سياسي', 'سياسية', 'سياسيه', 'حكومة', 'حكومه', 'انتخابات', 'انتخاب', 'برلمان',
        'حزب', 'احزاب', 'انقلاب', 'مظاهرة', 'مظاهرات', 'معارضة',
        'politics', 'political', 'government', 'election', 'elections', 'parliament', 'democrat', 'republican',
    ],

    // insults and swearing
    'insults' => [
        'غبي', 'غبية', 'اغبياء', 'حمار', 'حمير', 'حقير', 'حقيرة', 'تافه', 'تافهة', 'وسخ', 'وسخة', 'زبالة',
        'لعنة', 'ملعون', 'يلعن', 'خنزير', 'كلب', 'احمق', 'قذر', 'قذرة', 'سافل', 'سافلة',
        'منحط', 'واطي', 'نذل', 'خايس', 'معفن',
        'idiot', 'stupid', 'dumb', 'moron', 'loser', 'damn', 'crap', 'jerk', 'shut up',
        'hate you',
    ],

    // the owner can edit a quote or a comment for this many minutes after posting it, then it's locked
    'edit_minutes' => 10,

    // a quote with this many reports is hidden from everyone
    'hide_after_reports' => 3,

    // the reasons a user can pick when reporting a quote or a comment, with their labels
    'report_reasons' => [
        'offensive' => 'Insults or offensive words',
        'politics' => 'Politics',
        'spam' => 'Spam or ads',
        'other' => 'Something else',
    ],
];
