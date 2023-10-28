<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

use App\Services\TabbyApiService;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/




Route::namespace("API")->prefix("v1")->group(function () {

    // Auth
    Route::post('/sendOtpCode', [\App\Http\Controllers\API\AuthenticationController::class, 'sendOTP']);
    Route::post('/verfiyOtpCode', [\App\Http\Controllers\API\AuthenticationController::class, 'verfiyOTP']);
    Route::post('/verfiyOtpCode-v2', [\App\Http\Controllers\API\AuthenticationController::class, 'verfiyOTPv2']);
    Route::post('login', [\App\Http\Controllers\API\AuthenticationController::class, 'login']);
    Route::get('testlogin/{mobile}', [\App\Http\Controllers\API\AuthenticationController::class, 'testLogin']);
    Route::post('register', [\App\Http\Controllers\API\AuthenticationController::class, 'register']);


    Route::get('/driver/send-driver-otp/{orderId}', [\App\Http\Controllers\API\AuthenticationController::class, 'sendDriverOTP']);
    Route::get('/driver/verify-driver-otp/{orderId}/{otp}', [\App\Http\Controllers\API\AuthenticationController::class, 'verifyDriverOTP']);


    Route::get('/cleareverything', function () {

        $clearcache = Artisan::call('optimize:clear');
        echo "Cache cleared<br>" . $clearcache;
    });

    // Route::get('/amjad-test',[\App\Services\CallOrderNetsuiteApi::class, 'sendOrderToNSV2']);


    Route::post('/tabby-manual-payment/testHash', [TabbyApiService::class, 'testHash']);
    // Public
    Route::get('/final_result', [\App\Services\AlRajhiPaymentService::class, 'show_final_result']);
    Route::post('/final_result', [\App\Services\AlRajhiPaymentService::class, 'Get_Payment_Status_ARB']);


    Route::get('invoicestatus', [\App\Services\MyFatoorahApiService::class, 'Get_Payment_Status']);
    Route::get('invoicestatus_wallet', [\App\Services\MyFatoorahApiService::class, 'GetPaymentStatusWallet']);
    Route::get('invoicestatus/ksa', [\App\Services\MyFatoorahApiService::class, 'Get_Payment_Status_ksa']);
    Route::post('/order-pos-test', [\App\Services\FoodicsApiService::class, 'sendOrderFoodicsToNS']);
    Route::post('/web-hook/foodics/order_create', [\App\Services\FoodicsApiService::class, 'webhookFoodics']);

    Route::post('/webhook/ngenius/payment', [\App\Services\NgeniusPaymentService::class, 'webhookNgenius']);

    Route::post('/my-fatorah-test', [\App\Services\MyFatoorahApiService::class, 'Set_Payment_myfatoora']);

    Route::get('tabby/checkout/response', [\App\Services\TabbyApiService::class, 'response']);

    Route::get('tabby/checkout/success', [\App\Services\TabbyApiService::class, 'response']);
    Route::get('tabby/checkout/cancel', [\App\Services\TabbyApiService::class, 'response']);
    Route::get('tabby/checkout/failure', [\App\Services\TabbyApiService::class, 'response']);

    Route::get('checkout/response ', [\App\Services\TamaraApiService::class, 'response']);

    Route::get('checkout/success', [\App\Services\TamaraApiService::class, 'response']);
    Route::get('checkout/failure', [\App\Services\TamaraApiService::class, 'response']);
    Route::get('checkout/cancel', [\App\Services\TamaraApiService::class, 'response']);




    Route::post('payments/tamarapay ', [\App\Services\TamaraApiService::class, 'tamarapay']);

    Route::get('order-details/{order} ', [\App\Services\TamaraApiService::class, 'orderDetails']);


    Route::post('/checkout-Tamara', [\App\Services\TamaraApiService::class, 'checkoutTamara']);

    Route::prefix('auth')->group(function () {
        Route::middleware('auth:sanctum')->post('/new-user', [\App\Http\Controllers\API\AuthenticationController::class, 'createUser']);
        Route::post('/login', [\App\Http\Controllers\API\AuthenticationController::class, 'login']);
    });

    Route::middleware('auth:sanctum')->get('ordersTest/{order}', [\App\Http\Controllers\API\OrderController::class, 'getOrderByRefNoTest']);

    // Banners
    Route::prefix('banners')->group(function () {
        Route::get('/by-category/{category}', [\App\Http\Controllers\API\BannerController::class, 'getBannerByCategory']);
        Route::post('/add-banner', [\App\Http\Controllers\API\BannerController::class, 'createBanner']);
        Route::post('/update-banner/{banner}', [\App\Http\Controllers\API\BannerController::class, 'updateBanner']);
        Route::delete('/delete-banner/{banner}', [\App\Http\Controllers\API\BannerController::class, 'deleteBanner']);
        Route::get('/', [\App\Http\Controllers\API\BannerController::class, 'getBanners']);
        Route::get('/get-banners', [\App\Http\Controllers\API\BannerController::class, 'getBannersDashboard']);
        Route::get('/{banner}', [\App\Http\Controllers\API\BannerController::class, 'getBannerById']);
    });

    //Promotion
    Route::prefix('promotions')->group(function () {
        Route::get('/', [\App\Http\Controllers\API\PromotionController::class, 'getPromotions']);
        Route::get('/{promotionId}', [\App\Http\Controllers\API\PromotionController::class, 'getPromotionById']);
    });

    // Setting App
    Route::prefix('setting-app')->group(function () {
        Route::get('/version', [\App\Http\Controllers\API\SettingAppController::class, 'getVersion']);
        Route::get('/version/{version}', [\App\Http\Controllers\API\SettingAppController::class, 'getVersionById']);
    });
    //-----------------------------------------------------------------------------------------------------------------------------------
    // Filters
    Route::prefix('filters')->group(function () {

        Route::get('/', [\App\Http\Controllers\API\DiscoverController::class, 'list']);
        Route::get('/{discover}', [\App\Http\Controllers\API\DiscoverController::class, 'getById']);
        Route::get('/by-category/{category}', [\App\Http\Controllers\API\DiscoverController::class, 'listDiscover']);
        Route::post('/add-filter', [\App\Http\Controllers\API\DiscoverController::class, 'create']);
        Route::post('/update-filter/{discover}', [\App\Http\Controllers\API\DiscoverController::class, 'update']);
        Route::delete('/delete-filter/{discover}', [\App\Http\Controllers\API\DiscoverController::class, 'delete']);
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // Min Orders
    Route::prefix('min-orders')->group(function () {

        Route::get('/', [\App\Http\Controllers\API\MinOrderController::class, 'getAll']);
        Route::get('/{minOrder}', [\App\Http\Controllers\API\MinOrderController::class, 'getById']);
        Route::post('/add-min-orders', [\App\Http\Controllers\API\MinOrderController::class, 'add']);
        Route::post('/update-min-orders/{minOrder}', [\App\Http\Controllers\API\MinOrderController::class, 'update']);
        Route::delete('/delete-min-orders/{minOrder}', [\App\Http\Controllers\API\MinOrderController::class, 'delete']);
    });
    //-----------------------------------------------------------------------------------------------------------------------------------
    // Discount
    Route::prefix('discounts')->group(function () {
        Route::get('/', [\App\Http\Controllers\API\CouponController::class, 'getAll']);
        Route::get('/categories', [\App\Http\Controllers\API\CouponController::class, 'listCategories']);
        Route::get('/sub-categories', [\App\Http\Controllers\API\CouponController::class, 'listSubCategories']);
        Route::get('/products', [\App\Http\Controllers\API\CouponController::class, 'listProduct']);
        Route::get('/{discount}', [\App\Http\Controllers\API\CouponController::class, 'getCouponById']);
        Route::post('/add-discount', [\App\Http\Controllers\API\CouponController::class, 'createCoupon']);
        Route::post('/update-discount/{discount}', [\App\Http\Controllers\API\CouponController::class, 'updateCoupon']);
        Route::delete('/delete-discount/{discount}', [\App\Http\Controllers\API\CouponController::class, 'delete']);
    });

    // GiftCard

    Route::prefix('giftCards')->group(function () {
        Route::get('/', [\App\Http\Controllers\API\GiftCardController::class, 'getAll']);
        Route::get('/{GiftCard}', [\App\Http\Controllers\API\GiftCardController::class, 'getById']);
        Route::post('/add-giftCard', [\App\Http\Controllers\API\GiftCardController::class, 'createGiftCard']);
        Route::post('/update-giftCard/{giftcard}', [\App\Http\Controllers\API\GiftCardController::class, 'updateGiftCard']);
        Route::delete('/delete-giftCard/{giftcard}', [\App\Http\Controllers\API\GiftCardController::class, 'delete']);
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/redeem-giftCard', [\App\Http\Controllers\API\GiftCardController::class, 'redeemGiftCard']);
        });
    });
    //-----------------------------------------------------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------------------------------------------------
    Route::prefix('categories')->group(function () {
        Route::middleware('coordinates')->group(function () {
            Route::get('/categories-app', [\App\Http\Controllers\API\CategoryController::class, 'listAppCategories']);
            Route::get('/categories-app-v2', [\App\Http\Controllers\API\CategoryController::class, 'listAppCategoriesV2']);
        });

        Route::post('/add-category', [\App\Http\Controllers\API\CategoryController::class, 'create']);
        Route::post('/update-category/{categoryId}', [\App\Http\Controllers\API\CategoryController::class, 'update']);
        Route::delete('/delete-category/{categoryId}', [\App\Http\Controllers\API\CategoryController::class, 'delete']);
        Route::get('/{category}', [\App\Http\Controllers\API\CategoryController::class, 'getById']);
        Route::get('/', [\App\Http\Controllers\API\CategoryController::class, 'listCategories']);
    });


    //-----------------------------------------------------------------------------------------------------------------------------------

    // Sub Categories
    Route::prefix('sub-categories')->group(function () {
        Route::get('/{subCategory}', [\App\Http\Controllers\API\SubCategoryController::class, 'getById']);
        Route::get('by-category-id/{category}', [\App\Http\Controllers\API\SubCategoryController::class, 'listSubCategories']);
        Route::post('/add-sub-category', [\App\Http\Controllers\API\SubCategoryController::class, 'create']);
        Route::post('/update-sub-category/{subCategoryId}', [\App\Http\Controllers\API\SubCategoryController::class, 'update']);
        Route::delete('/delete-sub-category/{subCategoryId}', [\App\Http\Controllers\API\SubCategoryController::class, 'delete']);
        Route::get('/', [\App\Http\Controllers\API\SubCategoryController::class, 'list']);
    });
    //-----------------------------------------------------------------------------------------------------------------------------------
    // Products Details

    //Cutting
    Route::prefix('product-cuts')->group(function () {
        Route::get('/', [\App\Http\Controllers\API\ProductCutController::class, 'getAll']);
        Route::get('/{cut}', [\App\Http\Controllers\API\ProductCutController::class, 'getById']);
        Route::post('/add-cut', [\App\Http\Controllers\API\ProductCutController::class, 'add']);
        Route::post('/update-cut/{productCut}', [\App\Http\Controllers\API\ProductCutController::class, 'update']);
        Route::post('/update-status/{productCut}', [\App\Http\Controllers\API\ProductCutController::class, 'updateStatus']);
        Route::post('/delete-productCut/{productCut}', [\App\Http\Controllers\API\ProductCutController::class, 'delete']);
    });
    //------------------------------------------
    //Preparations
    Route::prefix('Product-preparations')->group(function () {
        Route::get('/', [\App\Http\Controllers\API\ProductPreparationController::class, 'getAll']);
        Route::get('/{preparation}', [\App\Http\Controllers\API\ProductPreparationController::class, 'getById']);
        Route::post('/add-preparations', [\App\Http\Controllers\API\ProductPreparationController::class, 'add']);
        Route::post('/update-status/{productPreparation}', [\App\Http\Controllers\API\ProductPreparationController::class, 'add']);
        Route::post('/update-preparations/{productPreparation}', [\App\Http\Controllers\API\ProductPreparationController::class, 'update']);
        Route::post('/delete-preparations/{productPreparation}', [\App\Http\Controllers\API\ProductPreparationController::class, 'delete']);
    });
    //------------------------------------------
    //Tags
    Route::prefix('Product-tags')->group(function () {
        Route::get('/', [\App\Http\Controllers\API\ProductTagController::class, 'getAll']);
        Route::get('/{tag}', [\App\Http\Controllers\API\ProductTagController::class, 'getById']);
        Route::post('/add-tags', [\App\Http\Controllers\API\ProductTagController::class, 'add']);
        Route::post('/update-status/{productTag}', [\App\Http\Controllers\API\ProductTagController::class, 'updateStatus']);
        Route::post('/update-tags/{productTag}', [\App\Http\Controllers\API\ProductTagController::class, 'update']);
        Route::post('/delete-tags/{productTag}', [\App\Http\Controllers\API\ProductTagController::class, 'delete']);
    });
    //------------------------------------------
    //Sizes
    Route::prefix('product-sizes')->group(function () {
        Route::get('/', [\App\Http\Controllers\API\ProductSizeController::class, 'getAll']);
        Route::get('/{size}', [\App\Http\Controllers\API\ProductSizeController::class, 'getById']);
        Route::get('/get-active-productSize', [\App\Http\Controllers\API\ProductSizeController::class, 'getActiveProductSizes']);
        Route::post('/add-size', [\App\Http\Controllers\API\ProductSizeController::class, 'add']);
        Route::post('/update-size/{productSize}', [\App\Http\Controllers\API\ProductSizeController::class, 'update']);
        Route::post('/delete-size/{productSize}', [\App\Http\Controllers\API\ProductSizeController::class, 'delete']);
    });
    //------------------------------------------
    //Shlwata
    Route::prefix('product-shlwatas')->group(function () {
        Route::get('/', [\App\Http\Controllers\API\ProductShlwataController::class, 'getAll']);
        Route::get('/{shlwata}', [\App\Http\Controllers\API\ProductShlwataController::class, 'getById']);
        Route::post('/add-product-shlwatas', [\App\Http\Controllers\API\ProductShlwataController::class, 'add']);
        Route::post('/update-product-shlwatas/{productShlwata}', [\App\Http\Controllers\API\ProductShlwataController::class, 'update']);
        Route::post('/delete-product-shlwatas/{productShlwata}', [\App\Http\Controllers\API\ProductShlwataController::class, 'delete']);
    });
    //-----------------------------------------------------------------------------------------------------------------------------------

    //Cities
    Route::prefix('cities')->group(function () {
        Route::get('/get-active-cities', [\App\Http\Controllers\API\CityController::class, 'getActiveCities']);
        Route::get('/get-city-ByCountry/{country}', [\App\Http\Controllers\API\CityController::class, 'getCityByCountry']);
        Route::post('/add-cities', [\App\Http\Controllers\API\CityController::class, 'add']);
        Route::post('/update-cities/{city}', [\App\Http\Controllers\API\CityController::class, 'update']);
        Route::post('/update-status/{city}', [\App\Http\Controllers\API\CityController::class, 'updateStatus']);
        Route::post('/delete-city/{city}', [\App\Http\Controllers\API\CityController::class, 'delete']);
        Route::get('/', [\App\Http\Controllers\API\CityController::class, 'getAll']);
        Route::get('/{city}', [\App\Http\Controllers\API\CityController::class, 'getById']);
        Route::get('/{country}', [\App\Http\Controllers\API\CityController::class, 'getCityByCountry']);
    });

    //-----------------------------------------------------------------------------------------------------------------------------------

    //Countries
    Route::prefix('countries')->group(function () {

        Route::get('/', [\App\Http\Controllers\API\CountryController::class, 'getAll']);
        Route::get('/get-active-countries', [\App\Http\Controllers\API\CountryController::class, 'getActiveCountries']);
        Route::get('/get-country-byCity/{city}', [\App\Http\Controllers\API\CountryController::class, 'getCountryByCity']);
        Route::get('/{country}', [\App\Http\Controllers\API\CountryController::class, 'getById']);
        Route::post('/add-countries', [\App\Http\Controllers\API\CountryController::class, 'create']);
        Route::post('/update-status/{country}', [\App\Http\Controllers\API\CountryController::class, 'updateStatus']);
        Route::post('/update-country/{country}', [\App\Http\Controllers\API\CountryController::class, 'update']);
        Route::post('/delete-country/{country}', [\App\Http\Controllers\API\CountryController::class, 'delete']);
    });

    //-----------------------------------------------------------------------------------------------------------------------------------

    //Payment Type
    Route::prefix('payment-types')->group(function () {
        Route::get('/', [\App\Http\Controllers\API\PaymentTypeController::class, 'getAll']);
        Route::get('/get-payment-types-Tamara', [\App\Http\Controllers\API\PaymentTypeController::class, 'getPaymentTypesTamara']);
        Route::get('/{paymentType}', [\App\Http\Controllers\API\PaymentTypeController::class, 'getById']);
        Route::post('/add-payment-type', [\App\Http\Controllers\API\PaymentTypeController::class, 'add']);
        Route::post('/update-payment-type/{paymentType}', [\App\Http\Controllers\API\PaymentTypeController::class, 'update']);
        Route::post('/delete-payment-type/{paymentType}', [\App\Http\Controllers\API\PaymentTypeController::class, 'delete']);
    });

    //-----------------------------------------------------------------------------------------------------------------------------------

    Route::prefix('delivery-period')->group(function () {
        Route::post('/add-period', [\App\Http\Controllers\API\DeliveryPeriodController::class, 'add']);
        Route::post('/update-period', [\App\Http\Controllers\API\DeliveryPeriodController::class, 'update']);
        Route::post('/delete/{dpId}', [\App\Http\Controllers\API\DeliveryPeriodController::class, 'delete']);
        Route::get('/{dpId}', [\App\Http\Controllers\API\DeliveryPeriodController::class, 'getById']);
        Route::get('/', [\App\Http\Controllers\API\DeliveryPeriodController::class, 'getAll']);
    });

    Route::prefix('delivery-date')->group(function () {
        Route::post('/add-not-included-date-bulk', [\App\Http\Controllers\API\DeliveryDatePeriodController::class, 'addCityDateBulk']);
        Route::post('/update/{DateCity}', [\App\Http\Controllers\API\DeliveryDatePeriodController::class, 'update']);
        Route::post('/deleteDDP/{dpId}', [\App\Http\Controllers\API\DeliveryDatePeriodController::class, 'delete']);
        Route::get('/{dpId}', [\App\Http\Controllers\API\DeliveryDatePeriodController::class, 'getById']);
        Route::get('/', [\App\Http\Controllers\API\DeliveryDatePeriodController::class, 'getDeliveryDatePeriod']);
    });


    //Products - ex. api/v1/products/add-product

    Route::middleware('auth:sanctum')->prefix('wishlists')->group(function () {
        Route::get('/', [\App\Http\Controllers\API\ProductController::class, 'getFavoriteProduct']);
        Route::get('/add-to-wishlist/{product}', [\App\Http\Controllers\API\ProductController::class, 'addFavoriteProduct']);
        Route::delete('/remove-from-wishlist/{favorite}', [\App\Http\Controllers\API\ProductController::class, 'removeFavoriteProduct']);
    });

    Route::prefix('products')->group(function () {
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/add-products', [\App\Http\Controllers\API\ProductController::class, 'create']);
            Route::post('/update-products/{product}', [\App\Http\Controllers\API\ProductController::class, 'update']);
            Route::post('/add-product-images/{productId}', [\App\Http\Controllers\API\ProductController::class, 'uploadProductImages']);
            Route::delete('/delete-product-images/{productImage}', [\App\Http\Controllers\API\ProductController::class, 'deleteImage']);
            Route::delete('/delete-product/{productId}', [\App\Http\Controllers\API\ProductController::class, 'delete']);
            Route::post('/{product}/rating', [\App\Http\Controllers\API\ProductController::class, 'ratingProduct']);
        });

        Route::middleware('coordinates')->get('/by-category/{category}', [\App\Http\Controllers\API\ProductController::class, 'getProductByCategory']);

        Route::middleware('coordinates')->get('getProduct/{productApp}', [\App\Http\Controllers\API\ProductController::class, 'getAppProductById']);

        Route::middleware('coordinates')->get('search/{name}', [\App\Http\Controllers\API\ProductController::class, 'search']);
        Route::get('/', [\App\Http\Controllers\API\ProductController::class, 'getAll']);
        Route::get('clicked/{product}', [\App\Http\Controllers\API\ProductController::class, 'isClicked']);
        Route::get('/by-subcategory/{subCategory}', [\App\Http\Controllers\API\ProductController::class, 'getProductBySubCategory']);
        Route::middleware('coordinates')->get('/best-seller', [\App\Http\Controllers\API\ProductController::class, 'bestSeller']);


        Route::get('/{product}', [\App\Http\Controllers\API\ProductController::class, 'getProductById']);
    });



    Route::prefix('orders')->middleware('auth:sanctum')->group(function () {
        Route::middleware('coordinates')->group(function () {
            Route::post('add-order', [\App\Http\Controllers\API\OrderController::class, 'createOrder']);
            Route::post('add-test-order', [\App\Http\Controllers\API\OrderTest2Controller::class, 'createOrder']);
        });
        Route::get('get-orders-v2', [\App\Http\Controllers\API\OrderController::class, 'getOrdersV2']);
        Route::get('get-order', [\App\Http\Controllers\API\OrderController::class, 'getOrdersDashboard']);
        Route::get('/{order}', [\App\Http\Controllers\API\OrderController::class, 'getOrderByRefNo']);
        Route::get('/', [\App\Http\Controllers\API\OrderController::class, 'getOrders']);
    });

    Route::prefix('carts')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [\App\Http\Controllers\API\CartController::class, 'getCart']);
        Route::post('add-to-cart', [\App\Http\Controllers\API\CartController::class, 'addToCart']);
        Route::post('add-to-cart-v2', [\App\Http\Controllers\API\CartController::class, 'addToCartV2']);
        Route::post('update-cart/{cartId}', [\App\Http\Controllers\API\CartController::class, 'updateCart']);
        Route::delete('delete-cart/{cartId}', [\App\Http\Controllers\API\CartController::class, 'deleteCart']);
        Route::middleware('coordinates')->post('check-coupon', [\App\Http\Controllers\API\CouponController::class, 'checkValidation']);
        Route::post('get-invoice-preview', [\App\Http\Controllers\API\CartController::class, 'getInvoicePreview']);
    });
    Route::get('/e922c951-94d8-4d59-b2cf-c9ed55a9e848', function (Request $request) {
        Artisan::call("test:funny");
    });


    //user
    //customer

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/customers-list', [\App\Http\Controllers\API\CouponController::class, 'listCustomer']);
        Route::post('/wallet', [\App\Http\Controllers\API\WalletController::class, 'updateCustomerWallet']);
        Route::get('/get-wallet', [\App\Http\Controllers\API\WalletController::class, 'getWalletLog']);
        Route::get('/wallet-by-id/{wallet}', [\App\Http\Controllers\API\WalletController::class, 'getWalletLogById']);

        Route::get('/wallet-by-customer/{id}', [\App\Http\Controllers\API\WalletController::class, 'getWalletLogByCustomerId']);
    });

    Route::prefix('customers')->middleware('auth:sanctum')->group(function () {

        Route::post('/selected-address/{address}', [\App\Http\Controllers\API\AuthenticationController::class, 'selectedAddressCustomer']);
        Route::get('/get-addresses', [\App\Http\Controllers\API\AuthenticationController::class, 'getAddress']);
        Route::post('/add-address', [\App\Http\Controllers\API\AuthenticationController::class, 'createAddressCustomer']);
        Route::post('/delete-address/{address}', [\App\Http\Controllers\API\AuthenticationController::class, 'deleteAddressCustomer']);
        Route::delete('/delete-customer', [\App\Http\Controllers\API\AuthenticationController::class, 'deleteCustomer']);
        Route::post('/edit-address/{address}', [\App\Http\Controllers\API\AuthenticationController::class, 'editAddressCustomer']);
        Route::post('/edit-profile', [\App\Http\Controllers\API\AuthenticationController::class, 'editProfile']);
        Route::get('/show-profile', [\App\Http\Controllers\API\AuthenticationController::class, 'showProfile']);
        Route::post('/charge-wallet', [\App\Http\Controllers\API\WalletController::class, 'chargeWallet']);
        Route::get('/wallet-logs', [\App\Http\Controllers\API\WalletController::class, 'customerWalletLog']);
        Route::post('/tabby-manual-payment/create', [TabbyApiService::class, 'createManualPayment']);
        Route::post('/tabby-manual-payment/update', [TabbyApiService::class, 'manualResponseUpdate']);
        Route::post('/tabby-manual-payment/updatev2', [TabbyApiService::class, 'manualResponseUpdateV2']);
    });



    Route::namespace("API")->prefix('test-location')->middleware('coordinates')->group(function () {

        Route::get('/test', [\App\Http\Controllers\API\ProductController::class, 'getProductBySubCategoryWithLocationTest']);
    });
});
