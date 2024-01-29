<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NavigationItem extends Model
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
    ];

    /**
     * The "booted" method of the model.
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
        return $this->belongsTo(NavigationItemSchema::class, 'schemaId');
    }

    public function groups()
    {
        return $this->belongsToMany(NavigationGroup::class, 'navigation_group_items', 'itemId', 'navigationGroupId');
    }

    public function details()
    {
        return $this->hasMany(NavigationItemDetail::class, 'itemId');
    }

    public function locale_details()
    {
        return $this->hasMany(NavigationItemDetail::class, 'itemId')->where('languageId', request()->languageId);
    }

    public function children()
    {
        return $this->belongsToMany(NavigationItem::class, 'navigation_item_hierarchies', 'parentId', 'childId');
    }

    public function children_pivot()
    {
        return $this->hasMany(NavigationItemHierarchy::class, 'parentId');
    }

    public function parent_pivot()
    {
        return $this->hasMany(NavigationItemHierarchy::class, 'childId');
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
            'navigationGroupId' => (is_null($existId) ? "required_without:parentId":"nullable") . "|exists:navigation_groups,id",
            'parentId' => (is_null($existId) ? "required_without:navigationGroupId":"nullable") . "|exists:navigation_items,id",
            'schemaId' => (is_null($existId) ? "required":"nullable") . "|exists:navigation_item_schemas,id",
            'featureTitle' => (is_null($existId) ? "required":"nullable") . "|string|max:255",
            // 'order' => "required|numeric|min:0",
            // 'visible' => "required|boolean",

            'details' => "required|array",
            'details.*' => "required|array",
            'details.*.id' => "nullable|exists:navigation_item_details,id",
            'details.*.languageId' => "required|exists:languages,id",
            'details.*.key' => "required|string|max:255",
            'details.*.valueType' => "required|string|max:255",
            'details.*.value' => "required|string|max:255",
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
            'schema' => $this->schema,
            'details' => $this->details,
            'createdAt' => CarbonPrinter($this->createdAt, 'datetime'),
            'updatedAt' => CarbonPrinter($this->updatedAt, 'datetime'),
        ];
    }
}
