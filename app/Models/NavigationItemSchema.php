<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NavigationItemSchema extends Model
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
        'featureTitle',
        'min',
        'max',
        'sortable',
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
    protected $casts = [
        'min' => "integer",
        'max' => "integer",
        'sortable' => "boolean",
    ];

    /**
     * Relations
     */
    public function website()
    {
        return $this->belongsTo(Website::class, 'websiteId');
    }

    public function details()
    {
        return $this->hasMany(NavigationItemDetailSchema::class, 'itemId');
    }

    public function navigationGroups()
    {
        return $this->belongsToMany(NavigationGroup::class, 'navigation_group_schema_items', 'itemId', 'navigationGroupId');
    }

    public function children()
    {
        return $this->belongsToMany(NavigationItemSchema::class, 'navigation_item_schema_hierarchies', 'parentId', 'childId');
    }

    public function children_pivot()
    {
        return $this->hasMany(NavigationItemSchemaHierarchy::class, 'parentId');
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
            'min' => (is_null($existId) ? "required":"nullable") . "|numeric|min:0",
            'max' => (is_null($existId) ? "required":"nullable") . "|numeric|min:0",
            'featureTitle' => (is_null($existId) ? "required":"nullable") . "|string|max:255",
            'sortable' => (is_null($existId) ? "required":"nullable") . "|boolean",

            'details' => "required|array",
            'details.*' => "required|array",
            'details.*.valueKey' => "required|string|max:255",
            'details.*.valueType' => "required|string|max:255",
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
            'min' => $this->min,
            'max' => $this->max,
            'details' => $this->details,
            'createdAt' => CarbonPrinter($this->createdAt, 'datetime'),
            'updatedAt' => CarbonPrinter($this->updatedAt, 'datetime'),
        ];
    }
}
