<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ClubPointController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RefundRequestController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\SubscribeController;
use App\Http\Controllers\Api\TranslationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\EncargadoController;
use App\Models\Encargado;

Route::group(['prefix' => 'v1', 'as' => 'api.'], function () {

    Route::group(['prefix' => 'payment', 'middleware' => 'auth:api'], function () {
        Route::any('/{gateway}/pay', [PaymentController::class, 'payment_initialize']);
    });

    Route::group(['prefix' => 'auth'], function () {
        // Banned User
        Route::group(['middleware' => 'unbanned'], function () {
            Route::post('login', [AuthController::class, 'login']);
            Route::post('signup', [AuthController::class, 'signup']);
            Route::post('verify', [AuthController::class, 'verify']);
            Route::post('resend-code', [AuthController::class, 'resend_code']);

            Route::post('verify-data', [AuthController::class, 'verifyData']);

            Route::post('password/create', [PasswordResetController::class, 'create']);
            Route::post('password/reset', [PasswordResetController::class, 'reset']);
        });

        Route::group(['middleware' => 'auth:api'], function () {
            Route::get('logout', [AuthController::class, 'logout']);
            Route::get('user', [AuthController::class, 'user']);
        });
    });

    Route::post('temp-id-cart', [AuthController::class, 'tempIdCart']);
    Route::post('temp-id-cart-update', [AuthController::class, 'tempIdCartUpdate']);

    Route::get('locale/{language_code}', [TranslationController::class, 'index']);
    Route::get('setting/home/{section}', [SettingController::class, 'home_setting']);
    Route::get('setting/footer', [SettingController::class, 'footer_setting']);
    Route::get('setting/header', [SettingController::class, 'header_setting']);
    Route::post('subscribe', [SubscribeController::class, 'subscribe']);

    Route::get('all-categories', [CategoryController::class, 'index']);
    Route::get('categories/first-level', [CategoryController::class, 'first_level_categories']);
    Route::get('all-brands', [BrandController::class, 'index']);
    Route::get('all-offers', [OfferController::class, 'index']);
    Route::get('offer/{slug}', [OfferController::class, 'show']);
    Route::get('page/{slug}', [PageController::class, 'show']);

    // Blogs
    Route::get('all-blog-categories', [BlogController::class, 'indexCategory']);
    Route::get('all-blogs/search', [BlogController::class, 'index']);
    Route::get('blog/details/{blog_slug}', [BlogController::class, 'show']);

    Route::group(['prefix' => 'product'], function () {
        Route::get('/details/{product_slug}', [ProductController::class, 'show']);
        Route::post('get-by-ids', [ProductController::class, 'get_by_ids']);
        Route::get('search', [ProductController::class, 'search']);
        Route::get('related/{product_id}', [ProductController::class, 'related']);
        Route::get('bought-together/{product_id}', [ProductController::class, 'bought_together']);
        Route::get('random/{limit}/{product_id?}', [ProductController::class, 'random_products']);
        Route::get('latest/{limit}', [ProductController::class, 'latest_products']);
        Route::get('reviews/{product_id}', [ReviewController::class, 'index']);
    });

    Route::group(['prefix' => 'collection'], function () {
        Route::get('/details/{collection_slug}', [CollectionController::class, 'details']);
    });

    Route::group(['prefix' => 'service'], function () {
        Route::get('/', [ServiceController::class, 'index']);
        Route::get('/details/{service_slug}', [ServiceController::class, 'show']);
        Route::post('get-by-ids', [ServiceController::class, 'get_by_ids']);
        Route::get('search', [ServiceController::class, 'search']);
        Route::get('related/{service_id}', [ServiceController::class, 'related']);
        Route::get('bought-together/{service_id}', [ServiceController::class, 'bought_together']);
        Route::get('random/{limit}/{service_id?}', [ServiceController::class, 'random_services']);
        Route::get('latest/{limit}', [ServiceController::class, 'latest_services']);
        Route::get('reviews/{service_id}', [ReviewController::class, 'index']);
    });

    Route::group(['prefix' => 'brand'], function () {
        Route::get('/details/{brand_slug}', [BrandController::class, 'show']);
    });

    Route::post('compared-list', [ProductController::class, 'productComparedList']);
    Route::get('search.ajax/{keyword}', [ProductController::class, 'ajax_search']);

    Route::get('all-countries', [AddressController::class, 'get_all_countries']);
    Route::get('all-codigo-ciiu', [AuthController::class, 'get_all_ciiu']);
    Route::get('all-codigo-postal', [AuthController::class, 'get_all_codigo_postal']);
    Route::get('all-collections', [AuthController::class, 'get_all_collections']);
    Route::get('all-subscriber', [AuthController::class, 'get_all_subscriber']);
    Route::get('states/{country_id}', [AddressController::class, 'get_states_by_country_id']);
    Route::get('cities/{state_id}', [AddressController::class, 'get_cities_by_state_id']);
    Route::get('localidades/{state_id}', [AddressController::class, 'get_localidad_by_state_id']);

    Route::post('carts', [CartController::class, 'index']);
    Route::post('carts/add', [CartController::class, 'add']);
    Route::post('carts/addCollection', [CartController::class, 'addCollection']);
    Route::post('carts/change-quantity', [CartController::class, 'changeQuantity']);
    Route::post('carts/destroy', [CartController::class, 'destroy']);

    Route::group(['prefix' => 'user'], function () {
        Route::post('address/create', [AddressController::class, 'createShippingAddress']);
        Route::post('address/createRegister', [AddressController::class, 'createShippingAddressRegister']);
        Route::post('info/updateAvatar', [UserController::class, 'updateAvatar']);
    });

    Route::post('payment/image', [OrderController::class, 'paymentImage']);

    Route::group(['middleware' => ['auth:api', 'unbanned']], function () {

        Route::group(['prefix' => 'checkout'], function () {
            Route::get('get-shipping-cost/{address_id}', [OrderController::class, 'get_shipping_cost']);
            Route::post('order/store', [OrderController::class, 'store']);
            Route::post('coupon/apply', [CouponController::class, 'apply']);
        });

        Route::group(['prefix' => 'user'], function () {

            Route::get('dashboard', [UserController::class, 'dashboard']);

            Route::get('chats', [ChatController::class, 'index']);
            Route::post('chats/send', [ChatController::class, 'send']);
            Route::get('chats/new-messages', [ChatController::class, 'new_messages']);

            Route::get('info', [UserController::class, 'info']);
            Route::post('info/update', [UserController::class, 'updateInfo']);
            Route::post('info/updateEmpresa', [UserController::class, 'updateInfoEmpresa']);
            Route::post('info/updatePassword', [UserController::class, 'updatePassword']);
            Route::post('info/updateTerms', [UserController::class, 'updateTerms']);



            Route::get('coupons', [CouponController::class, 'index']);

            Route::get('orders', [OrderController::class, 'index']);
            Route::post('orders', [OrderController::class, 'getOrders']);
            Route::get('orders/downloads', [OrderController::class, 'productDownloads']);
            Route::get('orders/product/download/{id}', [OrderController::class, 'download']);
            Route::get('order/{order_code}', [OrderController::class, 'show']);
            Route::get('order/cancel/{order_id}', [OrderController::class, 'cancel']);
            Route::get('order/invoice-download/{order_code}', [OrderController::class, 'invoice_download']);
            Route::get('review/check/{product_id}', [ReviewController::class, 'check_review_status']);
            Route::post('review/submit', [ReviewController::class, 'submit_review']);

            Route::apiResource('wishlists', WishlistController::class)->except(['update', 'show']);
            Route::get('wishlists/services', [WishlistController::class, 'favoriteServices']);
            Route::get('wishlists/brands', [WishlistController::class, 'favoriteBrands']);
            Route::post('wishlists/services', [WishlistController::class, 'storeService']);
            Route::post('wishlists/brands', [WishlistController::class, 'storeBrand']);
            Route::delete('wishlists/services', [WishlistController::class, 'destroyService']);
            Route::delete('wishlists/brands', [WishlistController::class, 'destroyBrand']);
            Route::apiResource('follow', FollowController::class)->except(['update', 'show']);

            Route::group(['prefix' => 'encargados'], function () {
                Route::post('/', [EncargadoController::class, 'store']);
                Route::put('/{id}', [EncargadoController::class, 'update']);
                Route::delete('/{id}', [EncargadoController::class, 'destroy']);
            });

            Route::get('addresses', [AddressController::class, 'addresses']);
            Route::post('address/create', [AddressController::class, 'createShippingAddress']);
            Route::post('address/update', [AddressController::class, 'updateShippingAddress']);
            Route::get('address/delete/{id}', [AddressController::class, 'deleteShippingAddress']);
            Route::post('address/setFavorite', [AddressController::class, 'setFavorite']);
            Route::get('address/default-shipping/{id}', [AddressController::class, 'defaultShippingAddress']);
            Route::get('address/default-billing/{id}', [AddressController::class, 'defaultBillingAddress']);


            Route::get('companies', [CompanyController::class, 'companies']);
            Route::post('companies/create', [CompanyController::class, 'createCompany']);
            Route::post('companies/update', [CompanyController::class, 'updateCompany']);
            Route::post('companies/setFavorite', [CompanyController::class, 'setFavorite']);
            Route::get('companies/delete/{id}', [CompanyController::class, 'deleteCompany']);

            # conversation
            Route::get('querries', [ConversationController::class, 'index']);
            Route::post('new-query', [ConversationController::class, 'store']);
            Route::get('querries/{id}', [ConversationController::class, 'show']);
            Route::post('new-message-query', [ConversationController::class, 'storeMessage']);

            # wallet
            Route::post('wallet/recharge', [WalletController::class, 'recharge']);
            Route::get('wallet/history', [WalletController::class, 'walletRechargeHistory']);

            # club points
            Route::get('earning/history', [ClubPointController::class, 'earningRechargeHistory']);
            Route::post('convert-point-into-wallet', [ClubPointController::class, 'convert_point_into_wallet']);

            // Refund Addon
            Route::get('refund-requests', [RefundRequestController::class, 'index']);
            Route::get('refund-request/create/{order_id}', [RefundRequestController::class, 'create']);
            Route::post('refund-request/store', [RefundRequestController::class, 'store']);
        });
    });


    //for shops
    Route::post('shop/register', [ShopController::class, 'shop_register']);
    Route::get('all-shops', [ShopController::class, 'index']);
    Route::get('shop/{slug}', [ShopController::class, 'show']);
    Route::get('shop/{slug}/home', [ShopController::class, 'shop_home']);
    Route::get('shop/{slug}/coupons', [ShopController::class, 'shop_coupons']);
    Route::get('shop/{slug}/products', [ShopController::class, 'shop_products']);
});

Route::fallback(function () {
    return response()->json([
        'data' => [],
        'success' => false,
        'status' => 404,
        'message' => 'Invalid Route'
    ]);
});
