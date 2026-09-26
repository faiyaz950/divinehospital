<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Admin-edited copy for one content screen (see config/cms.php), stored as JSON.
 */
class SiteContent extends Model
{
    protected $fillable = ['key', 'value'];

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }
}
