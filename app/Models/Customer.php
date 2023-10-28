<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasApiTokens, HasFactory, Notifiable , SoftDeletes;

    protected $fillable = [
        'mobile_country_code',
        'mobile',
        'name',
        'email',
        'avatar',
        'avatar_thumb',
        'age',
        'gender',
        'nationality',
        'is_active',
        'addresses',
        'wallet',
        'integrate_id',
        'deleted_at',
        'loyalty_points'
    ];

    protected $hidden = ['avatar',
        'avatar_thumb'];

    protected $appends = ['avatarUrl',
        'avatarThumbUrl',];
    protected $casts = [
        'disabledDate' => 'datetime'
    ];

    public function favorites()
    {
        return $this->belongsToMany(Favorite::class);
    }

    public function addresses()
    {
        return $this->belongsToMany(Address::class);
    }

    public function uploadAvatar(Request $request){

        if ($request->has('avatar')) {
            if (Storage::exists('public/'.$this->avatar)){
                Storage::delete('public/'.$this->avatar);
                Storage::delete('public/'.$this->avatar_thumb);
            }
            $file = $request->file('avatar');
            $extension = 'ava_' . time() .'_'. $file->hashName();
            $path = public_path('storage/uploads/avatars');

            $thumbPath = Image::make($request->file('avatar'))->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            });
            $file->move($path, $extension);
            $thumbPath->save($path . 'thumb_' . $extension);

            $this->avatar = 'uploads/avatars/' . $extension;
            $this->avatar_thumb = 'uploads/avatars/thumb_' . $extension;
            $this->update();
        }
    }

    public function getAvatarThumbUrlAttribute(){
        return config('app.url').Storage::url($this->avatar_thumb);
    }

    public function getAvatarUrlAttribute(){
        return config('app.url').Storage::url($this->avatar);
    }
}
