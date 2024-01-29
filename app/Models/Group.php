<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
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
        'groupTypeId',
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

    public function groupType()
    {
        return $this->belongsTo(GroupType::class, 'groupTypeId');
    }

    public function details()
    {
        return $this->hasMany(GroupDetail::class, 'groupId');
    }

    /**
     * New relation added 2023-11-21
     */
    public function locale_details()
    {
        return $this->hasMany(GroupDetail::class, 'groupId')->where('languageId', request()->languageId);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'group_items', 'groupId', 'itemId');
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
            'featureTitle' => (is_null($existId) ? "required":"nullable") . "|string|max:255",
            'groupTypeId' => (is_null($existId) ? "required":"nullable") . "|exists:group_types,id",

            'details' => "required|array",
            'details.*' => "required|array",
            'details.*.languageId' => "required|exists:languages,id",
            // 'details.*.title' => "required|string|max:255",

            'details.*.key' => "nullable|string|max:255",
            'details.*.value' => "nullable|string",
            'details.*.valueType' => "nullable|string|max:255",
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
            'featureTitle' => $this->featureTitle,
            'details' => $this->details,
            'createdAt' => CarbonPrinter($this->createdAt, 'datetime'),
            'updatedAt' => CarbonPrinter($this->updatedAt, 'datetime'),
        ];
    }
}
