<?php

namespace App\Http\Controllers\API;
use App\Enums\OrderStateEnums;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubcategoryListWithProductResource;
use App\Models\City;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Cut;
use App\Models\DeliveryDatePeriod;
use App\Models\DeliveryPeriod;
use App\Models\ProductImage;
use App\Models\Wishlist;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PaymentType;
use App\Models\Preparation;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductCity;
use App\Models\ProductCut;
use App\Models\ProductPaymentType;
use App\Models\ProductPreparation;
use App\Models\ProductSize;
use App\Models\SubCategoryCity;
use App\Models\Size;
use App\Models\ProductRating;
use App\Models\SubCategory;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\BestSellerResource;
use App\Http\Resources\ProductDetailsResource;
use App\Http\Resources\ProductAppDetailsResource;
use App\Services\Google_Map_API\GeocodingService;
use App\Services\PointLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\DB;




class ProductController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {


    }

    public function autoAddress(Request $request)
    {

        $latitude = $request->query('lat');
        $longitude = $request->query('long');

        if($latitude == null || $latitude == "" || $longitude == null || $longitude == ""){
             return response()->json(['success' => false,'data'=> null,
                 'message'=> 'provide the lang,lat please!', 'description'=> '', 'code'=>'400'],400);
        }

    $gooMap = app(GeocodingService::class)->searchByCoordination($latitude, $longitude);
      //  dd($gooMap);
//    if ($a['longitude'] === 0.0000000)
//        $a->update([
//            'address' => $gooMap['formatted_address'],
//            'city_id' => 3,
//            'longitude' => $gooMap['location']['lng'],
//            'latitude' => $gooMap['location']['lat'],
//        ]);

    }

  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getAll(Request $request)
    {
        // get coordinates for query params.
        $latitude = $request->query('latitude');
        $longitude = $request->query('longitude');
       
        $perPage = 6;
        if ($request->has('per_page'))
            $perPage = $request->get('per_page');

        if ($perPage == 0)
            $perPage = 6;

        $products = Product::where('is_active', '1')->paginate($perPage);

        return response()->json(['success' => true,'data'=> ProductListResource::Collection($products),
        'message'=> 'Products retrieved successfully', 'description'=> 'list Of Products', 'code'=>'200'],200);

    }

    public function getProductById(Product $product)
    {
        return response()->json(['success' => true,'data'=> new ProductDetailsResource($product) ,
        'message'=> 'Products retrieved successfully', 'description'=>"", 'code'=>'200'],200);

    }


   public function getAppProductById(Request $request , $productApp)
  
    {
        
        if (Product::find($productApp) === null)
                    return response()->json(['data'=> [],
                        'success' => false, 'message'=> 'failed', 'description'=>'invalid product!', 'code'=>'400'],400);


        $point = $request->query('longitude') . " " . $request->query('latitude');
        $countryId = $request->query('countryId');
        $country = Country::where('code', $countryId)->get()->first();
        
         if ($country === null)
            return response()->json(['data'=> [],
                'success' => false, 'message'=> 'failed', 'description'=>'this service not available in your country!', 'code'=>'404'],404);
                
                $currentCity = null;
                try{
                    $currentCity = app(PointLocation::class)->getLocatedCity($country, $point);
                } catch (\Exception $e) {
                    return response()->json(['data'=> [],
                        'success' => false, 'message'=> 'failed', 'description'=>'this service not available in your city, contact support!', 'code'=>'404'],404);
                }
        
        
                if ($currentCity === null)
                    return response()->json(['data'=> [],
                        'success' => false, 'message'=> 'failed', 'description'=>'this service not available in your city!', 'code'=>'404'],404);

                    $productCity = ProductCity::where([['city_id', $currentCity->id],['product_id', $productApp]])->get()->first();
                  
                 if ($productCity === null)
                    return response()->json(['data'=> [],
                        'success' => false, 'message'=> 'failed', 'description'=>'product not found in this city!', 'code'=>'404'],404);

                  
        return response()->json(['success' => true,'data'=> new ProductAppDetailsResource($productCity->product) ,
        'message'=> 'Products retrieved successfully', 'description'=> 'list Of Products', 'code'=>'200'],200);

    }
    
    public function getProductByCategory(Request $request,$category){

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
             'success' => false, 'message'=> 'success', 'description'=>'this service not available in your country!', 'code'=>'400'],400);

     $currentCity = app(PointLocation::class)->getLocatedCity($country, $point);

     if ($currentCity != null){
        $subCategoryIds = SubCategoryCity::where('city_id', $currentCity->id)->distinct()->pluck('sub_category_id');
        $subcategories = SubCategory::whereIn('id', $subCategoryIds)->where('category_id', $category->id)->distinct()->orderBy('sort','ASC')->get();
        $productIds = ProductCity::where('city_id', $currentCity->id)->distinct()->pluck('product_id');
        foreach($subcategories as $d) {
        $arr = array_intersect($d->products()->pluck('id')->toArray(), $productIds->toArray());
        $d->products = Product::whereIn('id',$arr)->active()->with('productImages', 'tags')->orderBy('sort','asc')->get();  
        }
           
     }
     else
         $subcategories = [];
            
         $data = SubcategoryListWithProductResource::Collection($subcategories);

        return response()->json(['success' => true ,'data'=> $data,
            'message'=> 'Sub-categories retrieved successfully', 'description'=> 'list Of Sub-categories', 'code'=>'200'],200);
    }

    public function getProductBySubCategory(Request $request, SubCategory $subCategory){
        if ($subCategory === null)
            return response()->json(['data'=> [],
                'success' => true, 'message'=> 'success', 'description'=>' no subcategory with this id', 'code'=>'200'],200);

        $perPage = 6;
        if ($request->has('per_page'))
            $perPage = $request->get('per_page');

        if ($perPage == 0)
            $perPage = 6;

        if (auth()->user() != null){
            $customer = Customer::find(auth()->user()->id);
            $address = $customer->address()->where('is_default', 1)->get()->last();
            $products = Product::where([['city_id', $address->city_id],['sub_category_id', $subCategory->id]])->paginate($perPage);

        }else {
            $products = Product::where('sub_category_id', $subCategory->id)->paginate($perPage);
        }

        return response()->json(['data'=>  ProductListResource::Collection($products),
            'success' => true, 'message'=> 'success', 'description'=>'', 'code'=>'200'],200);
    }

 public function create(Request $request)
    {
        $requestData = Validator::make($request->post(), [
            'name_ar' => 'required|max:255',
            'name_en' => 'required|max:255',
            'description_ar' => 'required|max:255',
            'description_en' => 'required|max:255',
            'weight' => 'required|max:255',
            'calories' => 'required|max:255',
            'price' => 'required|numeric',
            'sale_price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'is_active' => 'required|bool',
            // 'is_available' => 'required|bool',
            'is_kwar3' => 'required|bool',
            'is_Ras' => 'required|bool',
            'is_lyh' => 'required|bool',
            'is_karashah' => 'required|bool',
            'is_shalwata' => 'required|bool',
            'is_delivered' => 'required|bool',
            'is_picked_up' => 'required|bool',
            'integrate_id' => 'sometimes|numeric',
            'preparation_ids' => array('nullable', 'regex:(^([-+] ?)?[0-9]+(,[0-9]+)*$)'),
            'size_ids' => array('required', 'regex:(^([-+] ?)?[0-9]+(,[0-9]+)*$)'),
            'cut_ids' => array('nullable', 'regex:(^([-+] ?)?[0-9]+(,[0-9]+)*$)'),
            'payment_type_ids' => array('required', 'regex:(^([-+] ?)?[0-9]+(,[0-9]+)*$)'),
            'city_ids' => array('required', 'regex:(^([-+] ?)?[0-9]+(,[0-9]+)*$)'),
        ]);

        if ($requestData->fails())
            return response()->json(['success' => false, 'data' => null, 'message' => "failed, check your input!", 'description' => $requestData->failed(), 'code' => "400"], 400);


         $productCreationData = $request->only(['name_ar', 'name_en', 'description_ar', 'description_en', 'weight', 'calories', 'price', 'sale_price', 'category_id', 'sub_category_id', 'is_shalwata', 'is_active', 'is_delivered', 'is_picked_up', 'integrate_id']);

        $validatedData = $requestData->validated();

        $subCategory = SubCategory::where([['category_id', $validatedData['category_id']], ['id', $validatedData['sub_category_id']]])->get()->first();
        if ($subCategory == null)
            return response()->json(['success' => false, 'data' => null, 'message' => "failed, check your selected sub category!", 'description' => "", 'code' => "400"], 400);

        $payment_type_ids = explode(',', $validatedData['payment_type_ids']);
        $city_ids = explode(',', $validatedData['city_ids']);
     
       
        $preparation_ids = explode(',', $validatedData['preparation_ids']);
        $size_ids = explode(',', $validatedData['size_ids']);
        $cut_ids = explode(',', $validatedData['cut_ids']);
        

        $cities = City::whereIn('id', $city_ids)->get();
        $preparations = Preparation::whereIn('id', $preparation_ids)->get();
        $sizes = Size::whereIn('id', $size_ids)->get();
        $cuts= Cut::whereIn('id', $cut_ids)->get();
        $paymentTypes = PaymentType::whereIn('id', $payment_type_ids)->get();

        $product = Product::create($productCreationData);

        $product->productCities()->attach($cities);
        $product->productSizes()->attach($sizes);
        $product->productCuts()->attach($cuts);
        $product->productPreparations()->attach($preparations);
        $product->productPaymentTypes()->attach($paymentTypes);

      //  Product::uploadImage($request, $product, $validatedData);
      //  $res = $this->sendOrderToNS($product, $request);
        return response()->json(['data' => $product, 'message' => "success", 'description' => "", 'code' => "200"], 200);
    }

//   public function create(Request $request)
//     {
//         $validatedData = $request->validate([
//             'name_ar' => 'required|max:255',
//             'name_en' => 'required|max:255',
//             'description_ar' => 'required|max:255',
//             'description_en' => 'required|max:255',
//             'weight' => 'required|max:255',
//             'calories' => 'required|max:255',
//             'price' => 'required|numeric',
//             'sale_price' => 'required|numeric',
//             'category_id' => 'required|exists:categories,id',
//             'sub_category_id' => 'nullable|exists:sub_categories,id',
//             'is_active' => 'required|bool',
//             'is_shalwata' => 'required|bool',
//             'is_delivered' => 'required|bool',
//             'is_picked_up' => 'required|bool',
//             'integrate_id' => 'sometimes|numeric',
//             'preparation_ids' => array('nullable', 'regex:(^([-+] ?)?[0-9]+(,[0-9]+)*$)'),
//             'size_ids' => array('nullable', 'regex:(^([-+] ?)?[0-9]+(,[0-9]+)*$)'),
//             'cut_ids' => array('nullable', 'regex:(^([-+] ?)?[0-9]+(,[0-9]+)*$)'),
//             'payment_type_ids' => array('required', 'regex:(^([-+] ?)?[0-9]+(,[0-9]+)*$)'),
//             'city_ids' => array('required', 'regex:(^([-+] ?)?[0-9]+(,[0-9]+)*$)'), //you can not remove the regex!
//             'not_dates' => array('array', 'required'),
//             'not_dates.*.date_mm-dd' => array('required', 'regex:(^\d(0[1-9]|1?[012])\-(0?[1-9]|[12][0-9]|3[01])*$)', 'distinct'), // 01-29 or 12-29
//             'not_dates.*.delivery_period_ids' => array('required', 'array'),
//             'not_dates.*.delivery_period_ids.*.delivery_period_id' => array('required', 'exists:delivery_periods,id'),
// //            'not_dates.*.cities' => array('required', 'array'),
//             'not_dates.*.delivery_period_ids.*.city_id' => array('required', 'exists:cities,id'),
//         ]);

//         $productCreationData = $request->only(['name_ar', 'name_en', 'description_ar', 'description_en', 'weight', 'calories', 'price', 'sale_price', 'category_id', 'sub_category_id', 'is_shalwata', 'is_active', 'is_delivered', 'is_picked_up', 'integrate_id']);


//         $subCategory = SubCategory::where([['category_id', $validatedData['category_id']], ['id', $validatedData['sub_category_id']]])->get()->first();
//         if ($subCategory == null)
//             return response()->json(['success' => false, 'data' => null, 'message' => "failed, check your selected sub category!", 'description' => "", 'code' => "400"], 400);

//         $payment_type_ids = explode(',', $validatedData['payment_type_ids']);
//         $city_ids = explode(',', $validatedData['city_ids']);
//         $preparation_ids = explode(',', $validatedData['preparation_ids']);
//         $size_ids = explode(',', $validatedData['size_ids']);
//         $cut_ids = explode(',', $validatedData['cut_ids']);

//         $cities = City::whereIn('id', $city_ids)->get();
//         $preparations = Preparation::whereIn('id', $preparation_ids)->get();
//         $sizes = Size::whereIn('id', $size_ids)->get();
//         $cuts= Cut::whereIn('id', $cut_ids)->get();
//         $paymentTypes = PaymentType::whereIn('id', $payment_type_ids)->get();

//         $product = Product::create($productCreationData);


//         $validCityIds = City::whereIn('id', $city_ids)->pluck('id');

//         foreach ($validCityIds as $validCityId){
//             $validatedData['city_ids'] = $validatedData['city_ids'] . ',' . $validCityId;
//         }

//         foreach ($validatedData['not_dates'] as $arr){
//             $deliveryDate = DeliveryDate::create([
//                 'product_id' => $product->id,
//                 'date' => $arr['date_mm-dd']
//             ]);

//             $deliveryDate->periods()->attach($arr['delivery_period_ids']);
// //            $deliveryDate->cities()->attach($arr['cities']);
//         }

//         $product->productCities()->attach($cities);
//         $product->productSizes()->attach($sizes);
//         $product->productCuts()->attach($cuts);
//         $product->productPreparations()->attach($preparations);
//         $product->productPaymentTypes()->attach($paymentTypes);

//         Product::uploadImage($request, $product, $validatedData);
//       //  $res = $this->sendOrderToNS($product, $request);
//         return response()->json(['data' => $product, 'message' => "success", 'description' => "", 'code' => "200"], 200);
//     }

    public function uploadProductImages($productId, Request $request){

        $product = Product::find($productId);

        if ($product == null) {
            return response()->json(['data' => null, 'success' => false, 'message' => "failed", 'description' => "please, provide a product id!", 'code' => "400"], 400);
        }

        $vaildateData = $request->validate([
            "images"    => "required",
            "images.*"  => "required|image|mimes:png,jpg,jpeg|max:2048",
        ]);


        foreach($request->file('images') as $image) {

            if ($request->has('images')) {

                $file = $image;

                $extension = $file->hashName();

                $path = storage_path('product_images/' . $product->id . '/' );


                $thumbPath = Image::make($file)->resize(166, 130, function ($constraint) {
                    $constraint->aspectRatio();
                });


                $file->move($path, 'img_' . $extension);

                $thumbPath->save($path . 'thumb_' . $extension);

                ProductImage::create(
                    ['image' => 'product_images/' . $product->id . '/img_'. $extension,
                        'thumbnail' => 'product_images/' . $product->id .'/thumb_'. $extension,
                        'product_id' => $product->id
                    ]);

                return response()->json(['data' => $product->productImages, 'success' => true, 'message' => "success", 'description' => "", 'code' => "200"], 200);
            }
        }

        return response()->json(['data' => [], 'success' => false, 'message' => "success", 'description' => "", 'code' => "400"], 400);
    }

    // TODO: update
    public function update(Request $request, Product $product)
    {
       $requestData = Validator::make($request->post(), [
           'name_ar' => 'string|max:255',
            'name_en' => 'string|max:255',
            'description_ar' => 'string|max:255',
            'description_en' => 'string|max:255',
            'weight' => 'nullable|string|max:255',
            'calories' => 'nullable|string|max:255',
            'price' => 'required|numeric',
            'sale_price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'is_active' => 'required|bool',
            'is_shalwata' => 'required|bool',
            // 'is_available' => 'required|bool',
            'is_kwar3' => 'required|bool',
            'is_Ras' => 'required|bool',
            'is_lyh' => 'required|bool',
            'is_karashah' => 'required|bool',
            'is_delivered' => 'required|bool',
            'is_picked_up' => 'required|bool',
            'integrate_id' => 'sometimes|numeric',
            'preparation_ids' => array('nullable', 'regex:(^([-+] ?)?[0-9]+(,[0-9]+)*$)'),
            'size_ids' => array('nullable', 'regex:(^([-+] ?)?[0-9]+(,[0-9]+)*$)'),
            'cut_ids' => array('nullable', 'regex:(^([-+] ?)?[0-9]+(,[0-9]+)*$)'),
            'payment_type_ids' => array('required', 'regex:(^([-+] ?)?[0-9]+(,[0-9]+)*$)'),
            'city_ids' => array('required', 'regex:(^([-+] ?)?[0-9]+(,[0-9]+)*$)'),
        ]);

        if ($requestData->fails())
            return response()->json(['success' => false, 'data' => null, 'message' => "failed, check your input!", 'description' => $requestData->failed(), 'code' => "400"], 400);


         $productCreationData = $request->only(['name_ar', 'name_en', 'description_ar', 'description_en', 'weight', 'calories', 'price', 'sale_price', 'category_id', 'sub_category_id', 'is_shalwata', 'is_active', 'is_delivered', 'is_picked_up', 'integrate_id']);

        $validatedData = $requestData->validated();

        $subCategory = SubCategory::where([['category_id', $validatedData['category_id']], ['id', $validatedData['sub_category_id']]])->get()->first();
        if ($subCategory == null)
            return response()->json(['success' => false, 'data' => null, 'message' => "failed, check your selected sub category!", 'description' => "", 'code' => "400"], 400);

        $payment_type_ids = explode(',', $validatedData['payment_type_ids']);
        $city_ids = explode(',', $validatedData['city_ids']);
       
        $preparation_ids = explode(',', $validatedData['preparation_ids']);
        $size_ids = explode(',', $validatedData['size_ids']);
        $cut_ids = explode(',', $validatedData['cut_ids']);
        

        $cities = City::whereIn('id', $city_ids)->get();
        $preparations = Preparation::whereIn('id', $preparation_ids)->get();
        $sizes = Size::whereIn('id', $size_ids)->get();
        $cuts= Cut::whereIn('id', $cut_ids)->get();
        $paymentTypes = PaymentType::whereIn('id', $payment_type_ids)->get();
        
        $product->productCities()->sync($cities);
        $product->productSizes()->sync($sizes);
        $product->productCuts()->sync($cuts);
        $product->productPreparations()->sync($preparations);
        $product->productPaymentTypes()->sync($paymentTypes);

        $product->update($productCreationData);
        
         return response()->json(['data' => $product, 'message' => "success", 'description' => "", 'code' => "200"], 200);

    }

    public function ratingProduct(Request $request, Product $product){

        if ($product == null) {
            return response()->json(['data' => null, 'success' => false, 'message' => "failed", 'description' => "please, provide a product!", 'code' => "400"], 400);
        }

        $orderProduct = OrderProduct::where('product_id', $product->id)->get();
        $order =  $orderProduct->order;

        if ($order->customer_id !== auth()->id()) {
            return response()->json(['data' => null, 'success' => false, 'message' => "failed", 'description' => "you dont own this product!", 'code' => "400"], 400);
        }

        $orderState = $order->orderState;

        if ($orderState->code !== OrderStateEnums::getKey("delivered")) {
            return response()->json(['data' => null, 'success' => false, 'message' => "failed", 'description' => "you can not rated this product now!", 'code' => "400"], 400);
        }

        $productRating = ProductRating::where([['customer_id', auth()->id()], ['product_id', $product->id]])->get()->first();

        if ($productRating !== null) {
            return response()->json(['data' => null, 'success' => false, 'message' => "failed", 'description' => "you already rate this product!", 'code' => "400"], 400);
        }

        $validate = $request->validate([
            'rating' => 'required|numeric|min:0.0|max:5.0',
            'comment' => 'required|string',
        ]);

        $productRating = ProductRating::create([
            'customer_id' => auth()->id(),
            'product_id' => $product->id,
            'rating' => $validate['rating'],
            'comment' => $validate['comment']]);

        if ($productRating === null)
            return response()->json(['data' => null, 'success' => false, 'message' => "failed", 'description' => "please, try again later!", 'code' => "500"], 500);

        $avgRating = ProductRating::where('product_id', $product->id)->avg('rating');

        $product->no_rating = $avgRating;
        if (!$product->update())
            return response()->json(['message'=> 'product rating has not updated'], 500);

        return response()->json(['data' => null, 'success' => true, 'message' => "success", 'description' => "", 'code' => "200"], 200);
    }

    public function addFavoriteProduct($product, Request $request){

        $product = Product::find($product);
        if ($product == null) {
            return response()->json(['data' => null, 'success' => false, 'message' => "failed", 'description' => "please, provide a product!", 'code' => "400"], 400);
        }

        $favorite = Wishlist::where([['customer_id', auth()->user()->id],['product_id', $product->id]])->get()->first();

        if ($favorite !== null) {
            return response()->json(['data' => null, 'success' => false, 'message' => "failed", 'description' => "you already added this product!", 'code' => "400"], 400);
        }

        Wishlist::create([
            'customer_id' => auth()->user()->id,
            'product_id' => $product->id
        ]);

        return response()->json(['data' => null, 'success' => true, 'message' => "success", 'description' => "", 'code' => "200"], 200);
    }

    public function removeFavoriteProduct($favorite, Request $request){

        $favorite = Wishlist::find($favorite);

        if ($favorite == null) {
            return response()->json(['data' => null, 'success' => false, 'message' => "failed", 'description' => "please, provide a favorite product id!", 'code' => "400"], 400);
        }

        if ($favorite->customer_id !== auth()->user()->id) {
            return response()->json(['data' => null, 'success' => false, 'message' => "failed", 'description' => "bad request!", 'code' => "400"], 400);
        }

        $favorite->delete();

        return response()->json(['data' => null, 'success' => true, 'message' => "success", 'description' => "", 'code' => "200"], 200);
    }

    public function getFavoriteProduct(Request $request){

        $favorite = Wishlist::where('customer_id', auth()->user()->id)->with('product')->paginate(PerPage($request));

        return response()->json(['data' => $favorite, 'success' => true, 'message' => "success", 'description' => "", 'code' => "200"], 200);
    }

    // public function bestSeller(Request $request){

    //     $products = Product::orderBy('no_sale', 'DESC')->take(10)->get();

    //     return response()->json(['data' => BestSellerResource::Collection($products) , 'success' => true, 'message' => "success", 'description' => "", 'code' => "200"], 200);
    // }
    
     public function bestSeller(Request $request){

           // get by location
           $point = $request->query('longitude') . " " . $request->query('latitude');
           $countryId = $request->query('countryId');
           $country = Country::where('code', $countryId)->get()->first();
      
           if ($country === null)
               return response()->json(['data'=> [],
                   'success' => true, 'message'=> 'success', 'description'=>'this service not available in your country!', 'code'=>'200'],200);
   
           $currentCity = app(PointLocation::class)->getLocatedCity($country, $point);
    
           if ($currentCity != null){
               $productIds = ProductCity::where('city_id', $currentCity->id)->distinct()->pluck('product_id');
               $products = Product::whereIn('id', $productIds)->orderBy('no_sale', 'DESC')->take(10)->get();
              
            //   $categories =  Category::categoryCities()->whereIn('city_id', $currentCity->id)->distinct()->get();
           }
           else
           $products = [];

         // $data = Product::orderBy('no_sale', 'DESC')->take(10)->get();

           return response()->json(['data' => BestSellerResource::Collection($products) , 'success' => true, 'message' => "success", 'description' => "", 'code' => "200"], 200);
   
              
       }

    public function isClicked(Product $product){

        $product->no_clicked += 1;
        if (!$product->update())
            return response()->json(['message' => 'course has not updated, contact support',
                'input' => $product->id], 500);

        return response()->json(['message'=> 'successfully updated'], 200);
    }

  
  function search(Request $request,$name)
    {
        $point = $request->query('longitude') . " " . $request->query('latitude');
        $countryId = $request->query('countryId');
        $country = Country::where('code', $countryId)->get()->first();

        if ($country === null)
            return response()->json(['data'=> [],
                'success' => true, 'message'=> 'success', 'description'=>'this service not available in your country!', 'code'=>'200'],200);

        $currentCity = null;
        try{
            $currentCity = app(PointLocation::class)->getLocatedCity($country, $point);
        } catch (\Exception $e) {
            return response()->json(['data'=> [],
                'success' => true, 'message'=> 'success', 'description'=>'this service not available in your city, contact support!', 'code'=>'200'],200);
        }


        if ($currentCity === null)
            return response()->json(['data'=> [],
                'success' => true, 'message'=> 'success', 'description'=>'this service not available in your city!', 'code'=>'200'],200);


              $productIds = ProductCity::where('city_id', $currentCity->id)->distinct()->pluck('product_id');
              $products = Product::where('is_active', '1')->whereIn('id', $productIds)->where('name_ar', 'LIKE', '%'. $name. '%')->orWhere('name_en', 'LIKE', '%'. $name. '%')->with('productImages')->get();
            

        if(count($products)){
         return Response()->json($products);
        }
        else
        {
        return response()->json(['Result' => 'No Data not found'], 404);
      }
    }


    public function deleteImage(ProductImage $productImage)
    {
        $id = $productImage->id;

        if($productImage->delete()){
            Storage::delete('public/product_image/'.$id.'/'.$productImage->image);
            return response()->json(['massage:'=>'Product Image has been deleted successfully'],200);
        }
        else{
            return response()->json(['message' => 'ERROR PLEASE TRY AGAIN LATER'], 500);
        }

    }

    public function delete($productId){
        if (!is_numeric($productId))
            return response()->json(['message' => 'id should be numeric', 'input' => $productId], 400);

        $product = Product::find($productId);
        if (is_null($product))
            return response()->json(['message' => 'no product found!', 'input' => $product], 404);

        if(!$product->delete())
            return response()->json(['message' => 'product has not deleted, contact support please',
                'input' => $productId], 500);

                return response()->json(['message' => 'Successfully Deleted!'], 200);

    }

}
