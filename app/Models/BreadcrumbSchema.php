<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreadcrumbSchema extends Model
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
        'type',
        'page',
        'level',
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
     * Setters
     */
    public function setTypeAttribute($value)
    {
        // Define the allowed values for the 'type' column
        $allowedValues = ['feature', 'page'];

        // Check if the provided value is one of the allowed values
        if (in_array($value, $allowedValues)) {
            $this->attributes['type'] = $value;
        } else {
            // If the provided value is not allowed, set a default value or throw an exception
            // Here, we'll set it to 'default' as an example
            $this->attributes['type'] = 'page';
        }
    }

    /**
     * Relations
     */
    public function website()
    {
        return $this->belongsTo(Website::class, 'websiteId');
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
            'page' => (is_null($existId) ? "required":"nullable") . "|string|max:255",
            'level' => (is_null($existId) ? "required":"nullable") . "|numeric",
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
            'type' => $this->type,
            'page' => $this->page,
            'level' => $this->level,
            'createdAt' => CarbonPrinter($this->createdAt, 'datetime'),
            'updatedAt' => CarbonPrinter($this->updatedAt, 'datetime'),
        ];
    }
}
