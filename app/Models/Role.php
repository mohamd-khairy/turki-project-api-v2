<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['role', 'role_description'];

    public function users() {
        $this->belongsToMany(User::class, 'user_roles', 'role_id');
    }

    public function permissions() {
        $this->belongsToMany(Permissions::class, 'role_permissions', 'role_id');
    }

    public function getRoleIdByName(string $roleName){
        Role::where('role', $roleName)->get()->first();
    }


}
