<?php

namespace App\Http\Requests;

use App\Rules\CleanContent;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * A comment is 2 to 300 characters and follows the same content rules as quotes (FR-16, FR-22).
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'min:2', 'max:300', new CleanContent],
        ];
    }
}
