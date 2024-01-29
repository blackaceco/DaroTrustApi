<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchemaFeature extends Model
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
        'min',
        'max',
        'featureTitle',
        'sortable',
        'groupable',
        'taggable',
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
        'groupable' => "boolean",
        'taggable' => "boolean",
    ];

    /**
     * Relations
     */
    public function pageGroups()
    {
        return $this->belongsToMany(PageGroup::class, 'page_group_schema_items', 'schemaFeatureId', 'pageGroupId')->withPivot('primary');
    }

    /**
     * UPDATE Hema, 2023-11-28
     */
    public function children()
    {
        return $this->belongsToMany(SchemaFeature::class, 'schema_feature_hierarchies', 'parentId', 'childId');
    }

    public function children_pivot()
    {
        return $this->hasMany(SchemaFeatureHierarchy::class, 'parentId');
    }

    public function types()
    {
        return $this->hasMany(SchemaFeatureType::class, 'schemaFeatureId');
    }

    public function groupTypes()
    {
        return $this->belongsToMany(GroupType::class, SchemaFeatureGroupType::class, 'schemaFeatureId', 'groupTypeId')->withPivot('multiple');
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
            'pageGroupId' => (is_null($existId) ? "required_without:parentId":"nullable") . "|exists:page_groups,id",
            'primary' => (is_null($existId) ? "required_with:pageGroupId":"nullable") . "|boolean",
            'parentId' => (is_null($existId) ? "required_without:pageGroupId":"nullable") . "|exists:schema_features,id",
            'min' => (is_null($existId) ? "required":"nullable") . "|numeric|min:0",
            'max' => (is_null($existId) ? "required":"nullable") . "|numeric|min:0",
            'featureTitle' => (is_null($existId) ? "required":"nullable") . "|string|max:255",
            'sortable' => (is_null($existId) ? "required":"nullable") . "|boolean",
            'groupable' => (is_null($existId) ? "required":"nullable") . "|boolean",
            'taggable' => (is_null($existId) ? "required":"nullable") . "|boolean",
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
            'website' => $this->website,
            'min' => $this->min,
            'max' => $this->max,
            'pageGroups' => $this->pageGroups,
            'children' => $this->children,
            'featureTitle' => $this->featureTitle,
            'sortable' => $this->sortable,
            'groupable' => $this->groupable,
            'taggable' => $this->taggable,
            'createdAt' => CarbonPrinter($this->createdAt, 'datetime'),
            'updatedAt' => CarbonPrinter($this->updatedAt, 'datetime'),
        ];
    }
}
