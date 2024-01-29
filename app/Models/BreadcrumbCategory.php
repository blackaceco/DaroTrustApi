<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreadcrumbCategory extends Model
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
        'websiteId',
        'path',
        'page',
        'level',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'websiteId',
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
    public function website()
    {
        return $this->belongsTo(Website::class, 'websiteId');
    }

    public function breadcrumbs()
    {
        return $this->hasMany(Breadcrumb::class, 'breadcrumbCategoryId');
    }

    public function breadcrumb_locale()
    {
        return $this->hasMany(Breadcrumb::class, 'breadcrumbCategoryId')->where('languageId', request()->languageId);
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
            'path' => (is_null($existId) ? "required":"nullable") . "|string|max:255",
            'breadcrumb' => (is_null($existId) ? "required":"nullable") . "|array",
            'breadcrumb.id' => "nullable",
            'breadcrumb.languageId' => "required|exists:languages,id",
            'breadcrumb.title' => "required|string|max:255",
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
            'website' => [
                'id' => $this->website->id,
                'title' => $this->website->title,
            ],
            'path' => $this->path,
            'page' => $this->page,
            'level' => $this->level,
            'createdAt' => CarbonPrinter($this->createdAt, 'datetime'),
            'updatedAt' => CarbonPrinter($this->updatedAt, 'datetime'),
        ];
    }
}
