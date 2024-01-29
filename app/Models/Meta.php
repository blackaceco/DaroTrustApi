<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
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

    public function details()
    {
        return $this->hasMany(MetaDetail::class, 'metaId');
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

            // 'details' => (is_null($existId) ? "required":"nullable") . "|array",
            // 'details.*' => (is_null($existId) ? "required":"nullable") . "|array",
            // 'details.*.languageId' => (is_null($existId) ? "required":"nullable") . "|exists:languages,id",
            // 'details.*.title' => (is_null($existId) ? "required":"nullable") . "|string|max:255",
            // // 'details.*.description' => (is_null($existId) ? "required":"nullable") . "|string",
            // // 'details.*.image' => (is_null($existId) ? "required":"nullable") . "|string|max:255",
            // // 'details.*.keywords' => (is_null($existId) ? "required":"nullable") . "|string|max:255",
            // 'details.*.websiteName' => "nullable|string|max:255",
            // 'details.*.description' => "nullable|string",
            // 'details.*.image' => "nullable|string|max:255",
            // 'details.*.keywords' => "nullable|string|max:255",
            // 'details' => (is_null($existId) ? "required":"nullable") . "|array",
            'languageId' => "required|exists:languages,id",
            'title' => (is_null($existId) ? "required":"nullable") . "|string|max:255",
            'websiteName' => "nullable|string|max:255",
            'description' => "nullable|string",
            'image' => "nullable|string|max:255",
            'keywords' => "nullable|string",
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
            'page' => $this->page,
            'details' => $this->details,
            'createdAt' => CarbonPrinter($this->createdAt, 'datetime'),
            'updatedAt' => CarbonPrinter($this->updatedAt, 'datetime'),
        ];
    }
}
