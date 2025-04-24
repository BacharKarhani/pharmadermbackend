<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'fname',
        'lname',
        'username',
        'email',
        'password',
        'gender',
        'birthdate',
        'role_id',
    ];

    /**
     * Hidden fields in JSON responses
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast fields to native types
     */
    protected $casts = [
        'birthdate' => 'date',
    ];

    /**
     * Relationship: User belongs to a Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }
}
