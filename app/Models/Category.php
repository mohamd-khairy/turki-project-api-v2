<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class Category extends Model
{

    protected $fillable = ['type_ar', 'type_en', 'description', 'color', 'backgroundColor', 'image', 'thumbnail', 'banner', 'sort'];
    protected $hidden = ['created_at', 'updated_at', 'image', 'thumbnail', 'pivot', 'banner'];

    protected $appends = ['image_url', 'thumbnail_url', 'banner_url'];

    public static function uploadImage(Request $request, $category, $validatedData)
    {
        if ($request->has('image')) {
            if (Storage::exists('public/' . $category->image)) {
                Storage::delete('public/' . $category->image);
                Storage::delete('public/' . $category->thumbnail);
            }
            $file = $request->file('image');
            $extension = $file->hashName();
            $path = storage_path('category_images/');
            //$path = storage_path('app/public/category_images/');

            $thumbPath = Image::make($request->file('image'))->resize(166, 130, function ($constraint) {
                $constraint->aspectRatio();
            });
            $file->move($path, 'img_' . $extension);
            $thumbPath->save($path . 'thumb_' . $extension);

            $validatedData['image'] = 'category_images/img_' . $extension;
            $validatedData['thumbnail'] = 'category_images/thumb_' . $extension;

            return $validatedData;
        }
        return false;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_categories', 'category_id');
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? config('app.url') . Storage::url($this->image) : null;
    }

    public function getBannerUrlAttribute()
    {
        return $this->banner ? config('app.url') . Storage::url($this->banner) : null;
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? config('app.url') . Storage::url($this->thumbnail) : null;
    }

    public function product()
    {
        return $this->hasMany(Product::class);
    }

    public function banners()
    {
        return $this->hasMany(Banner::class);
    }

    public function categoryCities()
    {
        return $this->belongsToMany(City::class, 'category_cities', 'category_id', 'city_id')->with("notDeliveryDateCity");
    }
}
