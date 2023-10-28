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
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'username', 'email', 'mobile_country_code', 'mobile', 'password','age', 'country_code', 'gender',
        'email_verified_at', 'mobile_verified_at', 'is_active','avatar', 'avatar_thumb', 'name'
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];


    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'mobile_verified_at' => 'datetime',
    ];

    public function userRoles() {
        $this->belongsToMany(Role::class, 'user_roles', 'user_id');
    }

    public function assignRoles(Role ...$roles) {
        foreach ($roles as $role){
            UserRole::create([
                'user_id' => $this->id,
                'role_id' => $role->id
            ]);
        }
    }

    public function assignRolePermissions(Role $role, Permissions ...$permissions) {
        foreach ($permissions as $permission){
            RolePermissions::create([
                'permission_id' => $permission->id,
                'role_id' => $role->id
            ]);
        }
    }
}
