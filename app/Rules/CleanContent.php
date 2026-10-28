<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Refuses quotes about politics or with insults (word lists in config/moderation.php).
 */
class CleanContent implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $text = ' '.self::normalize((string) $value).' ';
        $words = preg_split('/\s+/u', trim($text));

        foreach (['politics', 'insults'] as $list) {
            foreach (config("moderation.$list", []) as $bad) {
                $bad = self::normalize($bad);

                $found = str_contains($bad, ' ')
                    ? str_contains($text, " $bad ")
                    : collect($words)->contains(fn ($word) => self::sameWord($word, $bad));

                if ($found) {
                    $fail($list === 'politics'
                        ? __('Quotes about politics are not allowed.')
                        : __('Please keep it respectful: insults and bad words are not allowed.'));

                    return;
                }
            }
        }
    }

    /**
     * The word itself, or with ال / و / ب / ف / ل / وال in front of it.
     */
    private static function sameWord(string $word, string $bad): bool
    {
        foreach (['', 'ال', 'و', 'ب', 'ف', 'ل', 'وال', 'بال', 'فال', 'لل'] as $prefix) {
            if ($word === $prefix.$bad) {
                return true;
            }
        }

        return false;
    }

    /**
     * Lower case, no diacritics or tatweel, one shape for alef / ya / ta marbuta, punctuation to spaces.
     */
    private static function normalize(string $text): string
    {
        $text = mb_strtolower($text);
        $text = preg_replace('/[\x{064B}-\x{0652}\x{0640}]/u', '', $text);
        $text = str_replace(['أ', 'إ', 'آ', 'ٱ', 'ى', 'ة'], ['ا', 'ا', 'ا', 'ا', 'ي', 'ه'], $text);
        $text = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $text);

        return trim(preg_replace('/\s+/u', ' ', $text));
    }
}
