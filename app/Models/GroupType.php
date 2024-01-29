<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupType extends Model
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
        'type',
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

    /**
     * Relations
     */
    public function details()
    {
        return $this->hasMany(GroupTypeDetail::class, 'groupTypeId');
    }

    /**
     * New relation added 2023-11-21
     */
    public function locale_details()
    {
        return $this->hasMany(GroupDetail::class, 'groupId')->where('languageId', request()->languageId);
    }

    public function groups()
    {
        return $this->hasMany(Group::class, 'groupTypeId');
    }

    public function schemaTypes()
    {
        return $this->hasMany(GroupSchemaType::class, 'groupTypeId');
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
            'type' => (is_null($existId) ? "required":"nullable") . "|string|max:255",

            'details' => "required|array",
            'details.*' => "required|array",
            'details.*.languageId' => "required|exists:languages,id",
            'details.*.title' => "required|string|max:255",
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
            'type' => $this->type,
            'details' => $this->details,
            'createdAt' => CarbonPrinter($this->createdAt, 'datetime'),
            'updatedAt' => CarbonPrinter($this->updatedAt, 'datetime'),
        ];
    }
}
