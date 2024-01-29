<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Website extends Model
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
        'title',
        'slug',
        'propertyId',
    ];

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

    // Prevent converting names of relationships from camelCase to snake_case.
    public static $snakeAttributes = false;

    /**
     * Relations
     */
    public function languages()
    {
        return $this->belongsToMany(Language::class, 'website_languages', 'websiteId', 'languageId')
            ->withPivot(['active', 'default', 'id'])->wherePivot('active', true);
    }

    public function websiteLanguages()
    {
        return $this->hasMany(WebsiteLanguage::class, 'websiteId');
    }

    public function schemaFeatures()
    {
        return $this->hasMany(SchemaFeature::class, 'websiteId');
    }

    public function navigations()
    {
        return $this->hasMany(NavigationGroup::class, 'websiteId');
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
            'title' => (is_null($existId) ? "required" : "nullable") . "|string|max:255",
            'slug' => (is_null($existId) ? "required" : "nullable") . "|string|max:255",
            'propertyId' => "nullable|string|max:255",
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
            'title' => $this->title,
            'slug' => $this->slug,
            'propertyId' => $this->propertyId,
            'languages' => $this->languages,
            'createdAt' => CarbonPrinter($this->createdAt, 'datetime'),
            'updatedAt' => CarbonPrinter($this->updatedAt, 'datetime'),
        ];
    }
}
