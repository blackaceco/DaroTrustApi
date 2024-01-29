<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaDetail extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'metaId',
        'languageId',
        'title',
        'websiteName',
        'description',
        'image',
        'keywords',
    ];

    /**
     * Accessors
     */
    public function getImageAttribute()
    {
        if (auth('api')->check())
            return getFileLink($this->getRawOriginal('image'));

        return $this->getRawOriginal('image');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    /**
     * Custom::
     * The rules that should be validate
     *
     * @param String $existId = null
     * @return array
     */
    public static function validationRules($existId = null)
    {
        //
    }
}
