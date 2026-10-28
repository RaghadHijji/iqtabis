<?php

namespace App\Http\Requests;

use App\Rules\CleanContent;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * A quote is 3 to 155 characters, has a real category (FR-05), may say who said it (author)
     * and passes the content rules: no politics, no insults (FR-16).
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:3', 'max:155', new CleanContent],
            'author' => ['nullable', 'string', 'max:80', new CleanContent],
            'category_id' => ['required', 'exists:categories,id'],
        ];
    }
}
