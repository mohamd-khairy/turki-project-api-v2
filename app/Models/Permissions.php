<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
    use HasFactory;

    protected $fillable = ['permission', 'permission_description'];

    public function roles() {
        $this->belongsToMany(Role::class, 'role_permissions', 'permission_id');
    }
}
