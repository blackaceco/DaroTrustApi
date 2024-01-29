<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Validation\Rule;

class Item extends Model
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
        'schemaId',
        'featureTitle',
        'order',
        'visible',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'websiteId',
        'schemaId',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order' => "integer",
        'visible' => "boolean",
        'view' => "integer",
    ];

    /**
     * The "boot" function
     */
    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('visibles', function ($query) {
            $query->where('visible', true);
        });

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('order');
        });
    }

    /**
     * Relations
     */
    public function website()
    {
        return $this->belongsTo(Website::class, 'websiteId');
    }

    public function schema()
    {
        return $this->belongsTo(SchemaFeature::class, 'schemaId');
    }

    public function details()
    {
        return $this->hasMany(ItemDetail::class, 'itemId');
    }

    /**
     * New relation added
     */
    public function locale_details()
    {
        return $this->hasMany(ItemDetail::class, 'itemId')->where('languageId', request()->languageId);
    }

    public function pageGroups()
    {
        return $this->belongsToMany(PageGroup::class, 'page_group_items', 'itemId', 'pageGroupId');
    }

    public function children()
    {
        return $this->belongsToMany(Item::class, 'item_hierarchies', 'parentId', 'childId');
    }

    public function children_pivot()
    {
        return $this->hasMany(ItemHierarchy::class, 'parentId');
    }

    /**
     * UPDATE Hema, 2023-11-27
     */
    public function parent_pivot()
    {
        return $this->hasMany(ItemHierarchy::class, 'childId');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_items', 'itemId', 'groupId');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_items', 'itemId', 'tagId');
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
        /**
         * Last updated by Kamyar R. Muhammad  |  30 October 2023
         *
         * I were separate the creating & updating rules, the updating rules defined by Hema Sardar in the ItemController
         * and I were move the rules to here.
         */

        if (is_null($existId)) {
            // Creating fields
            return [
                // 'featureTitle' => "required|string|max:255",
                'languageId' => "required|exists:languages,id",
                'schemaFeatureId' => "required|exists:schema_features,id",
                'groupId' => "nullable|exists:groups,id",
                'pageGroupId' => "required_without:parentId|exists:page_groups,id",
                'parentId' => "required_without:pageGroupId|exists:items,id",

                // this should be just a single id and it will be nullable
                'groupId' => "nullable|exists:groups,id",

                // this should be an array of tag's ids and it will be nullable
                'tagIds' => "nullable|array",
                'tagIds.*' => "nullable|exists:tags,id",

                'values' => "required|array",
                'values.*' => "required|array",
                'values.*.valueType' => "nullable|string|max:255",
                'values.*.valueKey' => "nullable|string|max:255",
                'values.*.id' => "nullable|exists:item_details,id",
                'values.*.value' => "nullable|string",  // |max:255 was a bug and removed
            ];
        } else {
            // Updating fields
            return [
                // this should be just a single id and it will be nullable
                'groupId' => "nullable|exists:groups,id",

                // this should be an array of tag's ids and it will be nullable
                'tagIds' => "nullable|array",
                'tagIds.*' => "nullable|exists:tags,id",

                'languageId' => "required|exists:languages,id",
                'values' => "array",
                'values.*' => "array",
                'values.*.id' => "required|integer|exists:item_details,id",
                'values.*.value' => "required|string",  // |max:255 was a bug and removed
                'values.*.order' => "nullable|string|max:255",
                'newValues' => "array",
                'newValues.*' => "array",
                'newValues.*.valueType' => "required|string|max:255",
                'newValues.*.valueKey' => "required|string|max:255",
                'newValues.*.value' => "required|string",  // |max:255 was a bug and removed
            ];
        }
    }

    /**
     * Custom::
     * The rules that should be validate
     *
     * @return array
     */
    public static function updateOrderValidationRules($websiteId)
    {
        return [
            'id' => [
                "required", "array",
                Rule::exists('items', 'id')->where(function(QueryBuilder $query) use ($websiteId) {
                    $query->where('websiteId', $websiteId);
                }),
            ],
            // 'id.*' => [
            //     "required","integer",
            //     Rule::exists('items', 'id')->where(function(QueryBuilder $query) use ($websiteId) {
            //         $query->where('websiteId', $websiteId);
            //     }),
            // ],
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
            'featureTitle' => $this->featureTitle,
            'details' => $this->details,
            'website' => $this->website,
            'schema' => $this->schema,
            'pageGroups' => $this->pageGroups,
            'order' => $this->order,
            'visible' => $this->visible,
            'createdAt' => CarbonPrinter($this->createdAt, 'datetime'),
            'updatedAt' => CarbonPrinter($this->updatedAt, 'datetime'),
        ];
    }
}
