<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
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
        'title',
        'abbreviation',
        'direction',   //  ENUM :: rtl, ltr
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
     * Custom::
     * The rules that should be validate
     * 
     * @param String $existId = null
     * @return array
     */
    public static function validationRules($existId = null)
    {
        return [
            'title' => (is_null($existId) ? "required":"nullable") . "|string|max:255",
            'abbreviation' => (is_null($existId) ? "required":"nullable") . "|string|max:4|unique:languages,abbreviation",
            'direction' => (is_null($existId) ? "required":"nullable") . "|in:ltr,rtl",
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
            'title' => $this->title,
            'abbreviation' => $this->abbreviation,
            'direction' => $this->direction,
            'createdAt' => CarbonPrinter($this->createdAt, 'datetime'),
            'updatedAt' => CarbonPrinter($this->updatedAt, 'datetime'),
        ];
    }
}
