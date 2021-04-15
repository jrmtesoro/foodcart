<?php

use Illuminate\Http\Request;
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
Route::get('home', ['as' => 'home', 'uses' => 'GuestController@home']);

Route::post('login', ['as' => 'login', 'uses' => 'UserController@login']);
Route::post('register/customer', ['as' => 'register.customer', 'uses' => 'UserController@registerCustomer']);

Route::post('partner', ['as' => 'restaurant.store', 'uses' => 'RestaurantController@store']);
Route::post('partner/status', ['uses' => 'PartnershipController@status']);

// Route::get('email/verify/{id}', ['as' => 'verification.verify', 'uses' => 'Auth\VerificationController@verify']);
Route::get('email/verify', ['as' => 'verification.verify', 'uses' => 'VerificationController@verify']);

Route::post('contactus', ['uses' => 'ContactUsController@store']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('logout', ['as' => 'logout', 'uses' => 'UserController@logout']);
    Route::get('email/resend', ['as' => 'verification.resend', 'uses' => 'VerificationController@resend']);
});

Route::group(['prefix' => 'password'], function () {
    Route::post('create', ['as' => 'forgot.create', 'uses' => 'ForgotPasswordController@create']);
    Route::get('find/{token}', ['as' => 'forgot.find', 'uses' => 'ForgotPasswordController@find']);
    Route::post('reset', ['as' => 'forgot.reset', 'uses' => 'ForgotPasswordController@reset']);
});

//GUEST
Route::get('tag', ['uses' => 'GuestController@guest_tag']);
Route::get('restaurant/{slug}', ['as' => 'guest.restaurant', 'uses' => 'GuestController@guest_restaurant']);
Route::post('restaurant', ['as' => 'restaurant.search', 'uses' => 'RestaurantController@search']);

Route::group(['middleware' => ['auth:api', 'verified']], function () {

    Route::get('home1', ['as' => 'home1', 'uses' => 'GuestController@home1']);

    //CUSTOMER
    Route::group(['prefix' => 'guest', 'middleware' => 'guest_api'], function () {
        //CART
        Route::post('cart', ['as' => 'cart.store', 'uses' => 'CartController@store']);
        Route::get('cart', ['as' => 'cart.index', 'uses' => 'CartController@index']);
        Route::post('cart/{cart}', ['as' => 'cart.update', 'uses' => 'CartController@update']);
        Route::delete('cart/{cart}', ['as' => 'cart.destroy', 'uses' => 'CartController@destroy']);
        Route::get('cart/empty', 'CartController@empty');

        //RATING
        Route::get('rating', ['as' => 'rating.index', 'uses' => 'RatingController@index']);
        Route::post('rating', ['as' => 'rating.store', 'uses' => 'RatingController@store']);

        //FAVORITE
        Route::post('favorite', ['as' => 'favorite.store', 'uses' => 'FavoriteController@store']);
        Route::get('favorite', ['as' => 'favorite.index', 'uses' => 'FavoriteController@index']);
        Route::delete('favorite/{restaurant_slug}', ['as' => 'favorite.destroy', 'uses' => 'FavoriteController@destroy']);

        Route::group(['prefix' => 'notification'], function () {
            Route::get('cart', ['as' => 'notification.cart', 'uses' => 'NotificationController@cart']);
        });

        Route::post('suborder/cancel', ['uses' => 'CustomerController@cancel_suborder']);
        Route::post('order/cancel', ['uses' => 'CustomerController@cancel_order']);

        Route::get('order/{suborder}/complete', ['uses' => 'OwnerOrderController@complete1']);
    });


    Route::group(['middleware' => 'guest_api'], function () {
        //PROFILE
        Route::get('profile', ['as' => 'customer.edit', 'uses' => 'CustomerController@edit']);
        Route::post('profile', ['as' => 'customer.update', 'uses' => 'CustomerController@update']);

        //CHECKOUT
        Route::get('checkout', ['as' => 'guest.checkout', 'uses' => 'GuestController@checkout']);
        Route::post('checkout', ['as' => 'order.store', 'uses' => 'OrderController@store']);

        //ORDER HISTORY
        Route::get('order/history', ['as' => 'order.index', 'uses' => 'OrderController@index']);
        Route::get('order/{code}', ['as' => 'order.show', 'uses' => 'OrderController@show']);
    });

    //OWNER
    Route::group(['prefix' => 'owner', 'middleware' => 'owner_api'], function () {

        //DASHBOARD
        Route::group(['prefix' => 'charts'], function () {
            Route::get('{filter}', ['uses' => 'DashboardController@owner_chart']);
        });

        //INFO
        Route::post('info', ['as' => 'owner.update.info', 'uses' => 'RestaurantController@info']);

        //PROFILE
        Route::get('profile', ['as' => 'owner.edit', 'uses' => 'RestaurantController@edit']);
        Route::post('profile', ['as' => 'owner.update', 'uses' => 'RestaurantController@update']);

        //MENU
        Route::resource('menu', 'MenuController')->except('update');
        Route::post('menu/{menu}', ['as' => 'menu.update', 'uses' => 'MenuController@update']);
        Route::post('menu/{menu}/restore', ['as' => 'menu.restore', 'uses' => 'MenuController@restore']);

        //CATEGORY
        Route::resource('category', 'CategoryController');
        Route::post('category/{category}', ['as' => 'category.update', 'uses' => 'CategoryController@update']);
        Route::post('category/{category}/restore', ['as' => 'category.restore', 'uses' => 'CategoryController@restore']);
        Route::get('get/category', ['uses' => 'CategoryController@getCategory']);
        Route::get('get/deletedCategory', ['uses' => 'CategoryController@deletedCategory']);

        //ORDER
        Route::get('order', ['as' => 'owner.order.index', 'uses' => 'OwnerOrderController@index']);
        Route::get('order/check', ['as' => 'owner.order.check', 'uses' => 'OwnerOrderController@check']);
        Route::get('order/{suborder}', ['uses' => 'OwnerOrderController@show']);
        Route::get('order/{suborder}/accept', ['as' => 'owner.order.accept', 'uses' => 'OwnerOrderController@accept']);
        Route::get('order/{suborder}/reject', ['as' => 'owner.order.reject', 'uses' => 'OwnerOrderController@reject']);
        Route::get('order/{suborder}/deliver', ['as' => 'owner.order.deliver', 'uses' => 'OwnerOrderController@deliver']);
        Route::get('order/{suborder}/complete', ['as' => 'owner.order.complete', 'uses' => 'OwnerOrderController@complete']);
        Route::get('order/{suborder}/cancel', ['as' => 'owner.order.cancel', 'uses' => 'OwnerOrderController@cancel']);

        //REPORT
        Route::get('report', ['as' => 'report.owner.index', 'uses' => 'ReportController@owner']);
        Route::get('report/{report_code}', ['as' => 'report.owner.owner_show', 'uses' => 'ReportController@owner_show']);
        Route::post('report', ['as' => 'report.store', 'uses' => 'ReportController@store']);

        //CHART
        Route::get('chart/order/total', ['as' => 'chart.order.total', 'uses' => 'ChartController@totalOrders']);
        Route::get('chart/order/sales', ['as' => 'chart.order.sales', 'uses' => 'ChartController@totalSales']);

        //SALES
        Route::get('sales/menu', ['as' => 'owner.sales.menu', 'uses' => 'OwnerSalesController@index']);
        Route::get('sales/menu/pdf', ['as' => 'owner.sales.menu.pdf', 'uses' => 'OwnerSalesController@pdf']);
        Route::get('sales/restaurant/pdf', ['as' => 'owner.sales.restaurant.pdf', 'uses' => 'OwnerSalesController@pdf1']);

        Route::group(['prefix' => 'notification'], function () {
            Route::get('orders', ['as' => 'notification.orders', 'uses' => 'NotificationController@orders']);
        });

        Route::get('logs', ['as' => 'owner.logs.index', 'uses' => 'LogsController@index']);
    });

    //ADMIN
    Route::group(['prefix' => 'admin', 'middleware' => 'admin_api'], function () {
        Route::get('profile', ['as' => 'admin.show', 'uses' => 'AdminController@show']);
        Route::post('profile', ['as' => 'admin.update', 'uses' => 'AdminController@update']);

        //DASHBOARD
        Route::group(['prefix' => 'charts'], function () {
            Route::get('{filter}', ['uses' => 'DashboardController@admin_chart']);
        });

        //TAG
        Route::group(['prefix' => 'tag'], function () {
            Route::get('/', ['as' => 'tag.index', 'uses' => 'TagController@index']);
            Route::post('/', ['as' => 'tag.store', 'uses' => 'TagController@store']);
            Route::get('{tag}/reject', ['as' => 'tag.reject', 'uses' => 'TagController@reject']);
            Route::get('{tag}/accept', ['as' => 'tag.accept', 'uses' => 'TagController@accept']);
        });

        //PARTNERSHIP
        Route::group(['prefix' => 'partnership'], function () {
            Route::get('/', ['as' => 'partnership.index', 'uses' => 'PartnershipController@index']);
            Route::get('{restaurant}', ['as' => 'partnership.show', 'uses' => 'PartnershipController@show']);
            Route::get('{restaurant}/accept', ['as' => 'partnership.accept', 'uses' => 'PartnershipController@accept']);
            Route::get('{restaurant}/reject', ['as' => 'partnership.reject', 'uses' => 'PartnershipController@reject']);
            Route::get('{restaurant}/review', ['as' => 'partnership.review', 'uses' => 'PartnershipController@review']);
        });

        //REPORTS
        Route::get('report', ['as' => 'report.admin.index', 'uses' => 'ReportController@index']);
        Route::get('report/{report_code}', ['uses' => 'ReportController@admin_show']);
        Route::get('report/{report_code}/investigate', ['as' => 'report.admin.investigate', 'uses' => 'ReportController@investigate']);
        Route::post('report/{report_code}/close', ['as' => 'report.admin.close', 'uses' => 'ReportController@close']);

        //RESTAURANTS
        Route::group(['prefix' => 'restaurant'], function() {
            Route::get('/', ['as' => 'restaurant.index', 'uses' => 'RestaurantController@index']);
            Route::get('{restaurant_id}', ['as' => 'restaurant.show', 'uses' => 'RestaurantController@show']);
            Route::get('{restaurant_id}/menu/{menu_id}', ['as' => 'restaurant.menu.show', 'uses' => 'RestaurantController@restaurant_menu']);
            Route::get('{restaurant_id}/order/{sub_order_id}', ['as' => 'admin.restaurant.order.view', 'uses' => 'RestaurantController@restaurant_order_view']);
        });

        //CUSTOMERS
        Route::group(['prefix' => 'customer'], function() {
            Route::get('/', ['as' => 'customer.index', 'uses' => 'CustomerController@index']);
            Route::get('{customer_id}', ['as' => 'customer.show', 'uses' => 'CustomerController@show']);
            Route::get('{customer_id}/order/{order_code}', ['as' => 'customer.order', 'uses' => 'CustomerController@customer_order']);
        });

        //SALES
        Route::group(['prefix' => 'sales'], function() {
            Route::get('restaurant', ['as' => 'admin.sales.restaurant', 'uses' => 'AdminSalesController@index']);
            Route::get('menu', ['as' => 'admin.sales.menu', 'uses' => 'AdminSalesController@index1']);
            Route::get('{restaurant_id}', ['as' => 'admin.sales.show', 'uses' => 'AdminSalesController@show']);
            
            Route::get('restaurant/pdf', ['as' => 'admin.restaurant.pdf', 'uses' => 'AdminSalesController@pdf']);
            Route::get('menu/pdf', ['as' => 'admin.menu.pdf', 'uses' => 'AdminSalesController@pdf1']);
        });

        //MENU
        Route::group(['prefix' => 'menu'], function() {
            Route::get('/', ['as' => 'admin.menu.index', 'uses' => 'AdminMenuController@index']);
            Route::get('{menu_id}', ['as' => 'admin.menu.show', 'uses' => 'AdminMenuController@show']);
        });

        //BAN 
        Route::group(['prefix' => 'ban'], function() {
            Route::get('/', ['as' => 'ban.index', 'uses' => 'BanController@index']);
            Route::get('{ban_id}', ['as' => 'ban.show', 'uses' => 'BanController@show']);
            Route::post('/', ['uses' => 'BanController@store']);
            Route::get('{user_id}/lift', ['uses' => 'BanController@destroy']);
        });

        //ORDER 
        Route::group(['prefix' => 'order'], function() {
            Route::get('/', ['as' => 'admin.order.index', 'uses' => 'AdminOrderController@index']);
            Route::get('{order_id}', ['as' => 'admin.order.show', 'uses' => 'AdminOrderController@show']);
        });

        //REQUEST
        Route::group(['prefix' => 'request'], function() {
            Route::get('/', ['as' => 'changerequest.index', 'uses' => 'ChangeRequestController@index']);
            Route::get('{request_change_id}/accept', ['uses' => 'ChangeRequestController@accept']);
            Route::get('{request_change_id}/reject', ['uses' => 'ChangeRequestController@reject']);
        });

        //USERS
        Route::group(['prefix' => 'users'], function (){
            Route::get('/', ['as' => 'admin.user.index', 'uses' => 'AdminUserController@index']);
            Route::post('/', ['as' => 'admin.user.store', 'uses' => 'AdminUserController@store']);
        });

        //LOGS
        Route::group(['prefix' => 'logs'], function (){
            Route::get('table', ['as' => 'logs.table', 'uses' => 'LogsController@table']);
        });

        //NOTIFICATION
        Route::get('notification/reports', ['as' => 'notification.reports', 'uses' => 'NotificationController@reports']);
        Route::get('notification/tags', ['as' => 'notification.tags', 'uses' => 'NotificationController@tags']);
        Route::get('notification/partnership', ['as' => 'notification.partnership', 'uses' => 'NotificationController@partnership']);
        Route::get('notification/requests', ['as' => 'notification.requests', 'uses' => 'NotificationController@requests']);
        Route::get('notification/admin', ['as' => 'notification.admin', 'uses' => 'NotificationController@admin']);

        Route::get('logs', ['as' => 'admin.logs.index', 'uses' => 'LogsController@index']);

    });

    //CHANGE PASSWORD
    Route::post('user/update', ['as' => 'user.update', 'uses' => 'UserController@update']);

    //DATATABLE
    Route::get('datatable/menu', ['as' => 'datatable.menu', 'uses' => 'DataTableController@getMenu']);
    Route::get('datatable/category', ['as' => 'datatable.category', 'uses' => 'DataTableController@getCategory']);
    Route::get('datatable/tag', ['as' => 'datatable.tag', 'uses' => 'DataTableController@getTag']);
    Route::get('datatable/partnership', ['as' => 'datatable.partnership', 'uses' => 'DataTableController@getPartnership']);
    Route::get('datatable/order', ['as' => 'datatable.order', 'uses' => 'DataTableController@getOrder']);
    Route::get('datatable/report', ['as' => 'datatable.report', 'uses' => 'DataTableController@getReport']);
    Route::get('datatable/restaurant', ['as' => 'datatable.restaurant', 'uses' => 'DataTableController@getRestaurant']);
    Route::get('datatable/restaurant/menu', ['as' => 'datatable.restaurant.menu', 'uses' => 'DataTableController@getRestaurantMenu']);
    Route::get('datatable/customer', ['as' => 'datatable.customer', 'uses' => 'DataTableController@getCustomer']);
    Route::get('datatable/customer/order', ['as' => 'datatable.customer.order', 'uses' => 'DataTableController@getCustomerOrder']);
    Route::get('datatable/admin/menu', ['as' => 'datatable.admin.order', 'uses' => 'DataTableController@getAdminMenu']);
    Route::get('datatable/admin/sales', ['as' => 'datatable.admin.sales', 'uses' => 'DataTableController@getAdminSales']);
    Route::get('datatable/admin/menusales', ['as' => 'datatable.admin.sales1', 'uses' => 'DataTableController@getAdminSales1']);
    Route::get('datatable/ban', ['as' => 'datatable.ban', 'uses' => 'DataTableController@getBan']);
    Route::get('datatable/logs', ['as' => 'datatable.logs', 'uses' => 'DataTableController@getLogs']);
    Route::get('datatable/owner/sales', ['as' => 'datatable.owner.sales', 'uses' => 'DataTableController@getOwnerSales']);
    Route::get('datatable/owner/restaurant_sales', ['as' => 'datatable.owner.sales_restaurant', 'uses' => 'DataTableController@getOwnerRestaurantSales']);
    Route::get('datatable/admin/orders', ['as' => 'datatable.admin.order', 'uses' => 'DataTableController@getAdminOrder']);
    Route::get('datatable/changerequest', ['uses' => 'DataTableController@getChangeRequest']);
    Route::get('datatable/allLogs', ['uses' => 'DataTableController@getAllLogs']);

    Route::get('datatable/restaurant/{restaurant_id}/logs', ['as' => 'datatable.admin.restaurant.logs', 'uses' => 'DataTableController@getRestaurantLogs']);
    Route::get('datatable/restaurant/{restaurant_id}/order', ['as' => 'datatable.admin.restaurant.order', 'uses' => 'DataTableController@getAdminRestaurantOrder']);
    Route::get('datatable/restaurant/{restaurant_id}/report', ['as' => 'datatable.admin.restaurant.report', 'uses' => 'DataTableController@getAdminRestaurantReport']);

    Route::get('datatable/customer/{customer_id}/logs', ['as' => 'datatable.admin.customer.logs', 'uses' => 'DataTableController@getCustomerLogs']);
    Route::get('datatable/customer/{customer_id}/report', ['uses' => 'DataTableController@getAdminCustomerReport']);

    Route::get('datatable/admin/users', ['uses' => 'DataTableController@getAdminUsers']);

    //STORE LOGS
    Route::post('logs', ['as' => 'logs.store', 'uses' => 'LogsController@store']);

    //REQUESTS
    Route::group(['prefix' => 'request'], function() {
        Route::post('/', ['uses' => 'ChangeRequestController@store']);
    });
});

Route::post('logs1', ['as' => 'logs.store1', 'uses' => 'LogsController@store1']);

Route::get('tester', 'GuestController@tester');
Route::get('change', 'GuestController@changePass');