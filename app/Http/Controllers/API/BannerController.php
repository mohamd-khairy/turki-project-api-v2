<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\City;
use App\Models\Category;
use App\Models\Country;
use App\Models\BannerCity;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\BannerListResource;
use App\Http\Resources\CategoryListWithBannerResource;
use App\Http\Resources\CategoryListWithBannerTestResource;
use App\Http\Resources\BannerDetailsResource;
use App\Services\Google_Map_API\GeocodingService;
use App\Services\Google_Map_API\Polygon;
use App\Services\PointLocation;
use App\Services\PolygonCalculation;
use App\Services\sbPolygonEngine;
use geoPHP;
class BannerController extends Controller
{

    public function getBannerById(Banner $banner)
    {
        return response()->json(['success' => true, 'message' => '', 'description' => "", "code" => "200",
            "data" => new BannerDetailsResource($banner)], 200);
    }

    // public function getBannerByCategory($category){

    //     $category = Category::find($category);
    //     if ($category == null)
    //         return response()->json(['success' => false ,'data'=> null,
    //             'message'=> 'not found', 'description'=> '', 'code'=>'404'],404);

    //     if (auth()->user() != null){
    //         $customer = Customer::find(auth()->user()->id);
    //         $address = $customer->address()->where('is_default', 1)->get()->last();
    //         $categories = Category::where([['id', $category->id],['city_id', $address->city_id]])->get();
    //     }else {
    //         $categories = Category::where('id', $category->id)->get();
    //     }

        
    //     $data = CategoryListWithBannerResource::Collection($categories);

    //     return response()->json(['success' => true ,'data'=> $data,
    //         'message'=> 'retrieved successfully', 'description'=> '', 'code'=>'200'],200);
    // }

 public function getBannerByCategory(Request $request , $category){

         $active = $request->query('active');

        $category = Category::find($category);
        if ($category == null)
            return response()->json(['success' => false ,'data'=> null,
                'message'=> 'not found', 'description'=> '', 'code'=>'404'],404);
            
    // get by location

    $point = $request->query('longitude') . " " . $request->query('latitude');
    $countryId = $request->query('countryId');
    $country = Country::where('code', $countryId)->get()->first();

    if ($country === null)
        return response()->json(['data'=> [],
            'success' => true, 'message'=> 'success', 'description'=>'this service not available in your country!', 'code'=>'200'],200);

    $currentCity = app(PointLocation::class)->getLocatedCity($country, $point);


    if ($currentCity != null){
       
        $bannerIds = BannerCity::where('city_id', $currentCity->id)->distinct()->pluck('banner_id');
        
        $banners = Banner::whereIn('id', $bannerIds)->where('category_id', $category->id)->where('is_active', '1')->orderBy('id','DESC')->get();
       
    }
    else
    $banners = [];

        $data =  CategoryListWithBannerTestResource::collection($banners);
        return response()->json(['success' => true ,'data'=> $data,
        'message'=> 'Categories retrieved successfully', 'description'=> 'list Of Categories', 'code'=>'200'],200);

    }

//  public function getBanners(Request $request)
//     {
//         $banners = Banner::where('is_active', '1')->get();
//         return response()->json(['success' => true, 'message' => '', 'description' => "", "code" => "200",
//             "data" => BannerListResource::Collection($banners)], 200);
//     }
    
    public function getBanners(Request $request)
    {
        $active = $request->query('active');
        
         if($active == "1")
         {
             $bannersActive = Banner::where('is_active', '1')->get();
             
              return response()->json(['success' => true, 'message' => '', 'description' => "", "code" => "200",
            "data" => BannerListResource::Collection($bannersActive)], 200);
         }
         elseif($active == "0"){
             $banners = Banner::get();
             
              return response()->json(['success' => true, 'message' => '', 'description' => "", "code" => "200",
            "data" => BannerListResource::Collection($banners)], 200);
         }
        
        
    }

     public function getBannersDashboard(Request $request)
    {
        $banners = Banner::get();
        return response()->json(['success' => true, 'message' => '', 'description' => "", "code" => "200",
            "data" => BannerListResource::Collection($banners)], 200);
    }


    public function createBanner(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'sometimes',
            'category_id' => 'required|exists:categories,id',
            'city_ids' => array('required', 'regex:(^([-+] ?)?[0-9]+(,[0-9]+)*$)'),
            'title_color' => 'sometimes',
            'sub_title' => 'sometimes',
            'sub_title_color' => 'sometimes',
            'button_text' => 'sometimes',
            'button_text_color' => 'sometimes',
            'redirect_url' => 'sometimes',
            'redirect_mobile_url' => 'sometimes',
            'is_active' => 'required',
            'type' => 'sometimes',
            'image' => 'required',
            'product_id' => 'sometimes',

        ]);
     
      
        $imageName = $request->file('image')->hashName();

        $Category = Category::where('id', $validatedData['category_id'])->get()->first();
     
        if ($Category == null)
            return response()->json(['success' => false, 'data' => null, 'message' => "failed, check your selected  category!", 'description' => "", 'code' => "400"], 400);
           
        $city_ids = explode(',', $validatedData['city_ids']);    
        $cities = City::whereIn('id', $city_ids)->get();

        $validatedData['image'] = $imageName;
        $banner = Banner::create($validatedData);

        $banner->bannerCities()->attach($cities);
      
      
        $request->file('image')->storeAs('public/marketingBoxImages/'.$banner['id'], $imageName);

            if($banner){
                return response()->json(['success' => true, 'message' => '', 'description' => "", "code" => "200",
                    "data" => $banner], 200);
            }
        else
            return response()->json(['success' => false, 'message' => 'ERROR PLEASE TRY AGAIN LATER', 'description' => "", "code" => "400",
                "data" => $banner], 400);
    }

    public function updateBanner(Banner $banner, Request $request)
    {
        $validatedate = $request->validate([
            'title' => 'sometimes',
            'title_color' => 'sometimes',
            'category_id' => 'sometimes|exists:categories,id',
            'city_ids' => array('sometimes', 'regex:(^([-+] ?)?[0-9]+(,[0-9]+)*$)'),
            'sub_title' => 'sometimes',
            'sub_title_color' => 'sometimes',
            'button_text' => 'sometimes',
            'button_text_color' => 'sometimes',
            'redirect_url' => 'sometimes',
            'redirect_mobile_url' => 'sometimes',
            'is_active' => 'sometimes',
            'type' => 'sometimes',
            'image'=> 'sometimes|mimes:png,jpeg,jpg',
        ]);

        if ($request->has('image')){
            $imageName = $request->file('image')->hashName();
            $validatedate['image'] = $imageName;
        }

        $Category = Category::where('id', $validatedate['category_id'])->get()->first();

        if ($Category == null)
            return response()->json(['success' => false, 'data' => null, 'message' => "failed, check your selected  category!", 'description' => "", 'code' => "400"], 400);

        $city_ids = explode(',', $validatedate['city_ids']);
        $cities = City::whereIn('id', $city_ids)->get();


        Storage::delete('public/marketingBoxImages/'.$banner->id.'/'.$banner->image);
        
          $banner->bannerCities()->sync($cities);
        if($banner->update($validatedate)){

           //$request->file('image')->storeAs('public/marketingBoxImages/'.$banner->id, $imageName);
           
            if ($request->file('image')){
            $imageName = $request->file('image')->storeAs('public/marketingBoxImages/'.$banner->id, $imageName);
            $validatedate['image'] = $imageName;
        }

            return response()->json(['massage:'=>'Marketing box has been updated successfully','data'=> $banner],200);
        }
        else{
            return response()->json(['message' => 'ERROR PLEASE TRY AGAIN LATER'], 500);
        }


    }


    public function deleteBanner(Banner $banner)
    {
        $id = $banner->id;

        if($banner->delete()){
            Storage::delete('public/marketingBoxImages/'.$id.'/'.$banner->image);
            return response()->json(['massage:'=>'Marketing box has been deleted successfully'],200);
        }
        else{
            return response()->json(['message' => 'ERROR PLEASE TRY AGAIN LATER'], 500);
        }

    }
}
