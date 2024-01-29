<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Breadcrumb extends Model
{
    use HasFactory;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'breadcrumbCategoryId',
        'languageId',
        'title',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'breadcrumbCategoryId',
        // 'languageId',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    /**
     * Relations
     */
    public function category()
    {
        return $this->belongsTo(BreadcrumbCategory::class, 'breadcrumbCategoryId');
    }

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
        return [
            'breadcrumbCategoryId' => (is_null($existId) ? "required":"nullable") . "|exists:breadcrumb_categories,id",
            'languageId' => (is_null($existId) ? "required":"nullable") . "|exists:languages,id",
            'title' => (is_null($existId) ? "required":"nullable") . "|string|max:255",
        ];
    }

    /**
     * Custom::
     * Return list of fields for storing in activity logs
     */
    public function fieldValues()
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'language' => $this->language,
            'title' => $this->featureTitle,
            'createdAt' => CarbonPrinter($this->createdAt, 'datetime'),
            'updatedAt' => CarbonPrinter($this->updatedAt, 'datetime'),
        ];
    }
}
