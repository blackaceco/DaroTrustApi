<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchemaFeatureType extends Model
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
        'schemaFeatureId',
        'valueKey',
        'valueType',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'schemaFeatureId',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'schemaFeatureId',
    ];

    /**
     * Relations
     */
    public function schemaFeature()
    {
        return $this->belongsTo(SchemaFeature::class, "schemaFeatureId");
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
            'schemaFeatureId' => "required|exists:schema_features,id",
            'valueKey' => "required|string|max:255",
            'valueType' => "required|string|max:255",
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
            'schemaFeature' => $this->schemaFeature,
            'valueKey' => $this->valueKey,
            'valueType' => $this->valueType,
            'createdAt' => CarbonPrinter($this->createdAt, 'datetime'),
            'updatedAt' => CarbonPrinter($this->updatedAt, 'datetime'),
        ];
    }
}
