<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'superAdmin',
        'status',  // ENUM::  active, inactive
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'superAdmin',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'superAdmin' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Relations
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'admin_roles', 'adminId', 'roleId');
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
            'name' => (is_null($existId) ? "required":"nullable") . "|string|max:255",
            'email' => (is_null($existId) ? "required":"nullable") . "|email|max:255|unique:admins,email,$existId",
            'password' => (is_null($existId) ? "required":"nullable") . "|string|min:8",
            'superAdmin' => (is_null($existId) ? "required":"nullable") . "|boolean",
            'status' => (is_null($existId) ? "required":"nullable") . "|string|in:active,inactive",
            // 'roles' => (is_null($existId) ? "required":"nullable") . "|array",
            // 'roles.*' => "required|exists:roles,id"
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
            'name' => $this->name,
            'email' => $this->email,
            'superAdmin' => $this->superAdmin,
            'status' => $this->status,
            // 'roles' => $this->roles,
            'createdAt' => CarbonPrinter($this->createdAt, 'datetime'),
            'updatedAt' => CarbonPrinter($this->updatedAt, 'datetime'),
        ];
    }
}
