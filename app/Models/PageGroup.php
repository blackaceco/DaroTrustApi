<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageGroup extends Model
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
        'page',
        'type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'websiteId',
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

    public function items()
    {
        return $this->belongsToMany(Item::class, 'page_group_items', 'pageGroupId', 'itemId');
    }

    public function features()
    {
        return $this->belongsToMany(SchemaFeature::class, 'page_group_schema_items', 'pageGroupId', 'schemaFeatureId');
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
            'page' => (is_null($existId) ? "required":"nullable") . "|string|max:255",
            'type' => (is_null($existId) ? "required":"nullable") . "|string|max:255",
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
            'items' => $this->items,
            'page' => $this->page,
            'type' => $this->type,
            'createdAt' => CarbonPrinter($this->createdAt, 'datetime'),
            'updatedAt' => CarbonPrinter($this->updatedAt, 'datetime'),
        ];
    }
}
