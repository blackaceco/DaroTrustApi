<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NavigationGroup extends Model
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
        'navigation',
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

    public function items()
    {
        return $this->belongsToMany(NavigationItem::class, 'navigation_group_items', 'navigationGroupId', 'itemId');
    }

    public function schemas()
    {
        return $this->belongsToMany(NavigationItemSchema::class, 'navigation_group_schema_items', 'navigationGroupId', 'itemId');
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
            'navigation' => "required|string|max:255"
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
            'navigation' => $this->navigation,
            'website' => $this->website,
            'items' => $this->items,
            'schemas' => $this->schemas,
            'createdAt' => CarbonPrinter($this->createdAt, 'datetime'),
            'updatedAt' => CarbonPrinter($this->updatedAt, 'datetime'),
        ];
    }
}
