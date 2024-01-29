<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'adminId',
        'websiteId',
        'ipAddress',
        'entityId',
        'entity',
        'action',
        'oldValue',
        'newValue',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'adminId',
        'websiteId',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'oldValue' => "json",
        'newValue' => "json",
        'createdAt' => "datetime",
    ];

    /**
     * The "boot" function
     */
    public static function boot()
    {
        parent::boot();

        // getting & saving the admin's ip address automatically.
        static::creating(function ($model) {
            $model->adminId = auth()->id();
            $model->ipAddress = request()->ip();
        });
    }

    /**
     * Relations
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'adminId');
    }

    public function website()
    {
        return $this->belongsTo(Website::class, 'websiteId');
    }
}
