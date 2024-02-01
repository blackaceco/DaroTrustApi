<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUsForm extends Model
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
        'subject',
        'name',
        'email',
        'phone',
        'message',
        'ipAddress',
        'status',
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
     * The "boot" function
     */
    public static function boot()
    {
        parent::boot();

        // getting & saving the admin's ip address automatically.
        static::creating(function ($model) {
            $model->ipAddress = request()->ip();
        });
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
            'subject' => "required|string|max:255",
            'name' => "required|string|max:255",
            'email' => "required|string|max:255",
            // 'phone' => "required|string|max:255",
            'message' => "required|string|max:255",
        ];
    }
}
