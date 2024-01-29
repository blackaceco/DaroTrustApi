<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemDetail extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'itemId',
        'languageId',
        'valueType',
        'key',
        'value',
        'order',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'itemId',
        // 'languageId',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order' => "integer",
    ];

    /**
     * The "booted" method of the model.
     */
    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('order');
        });
    }

    /**
     * Relations
     */
    public function language()
    {
        return $this->belongsTo(Language::class, 'languageId');
    }

    /**
     * Custom::
     * The rules that should be validate
     *
     * @param String $existId = null
     * @return array
     */
    public static function validationRules($existId = null)
    {
        return [];
    }

    /**
     * Attribute
     * Get detail value.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function value(): Attribute
    {
        $types = ['image', 'thumbnail', 'gallery', 'video', 'attachment'];
        return Attribute::make(
            get: fn ($value) => in_array($this->valueType, $types) ? getFileLink($value) : $value,
        );
    }
}
