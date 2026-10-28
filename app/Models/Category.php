<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'position'])]
class Category extends Model
{
    /**
     * A category has many quotes.
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }
}
