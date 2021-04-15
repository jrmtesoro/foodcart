<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'access'], function (){
    Route::get('/', ['as' => 'home', 'uses' => 'GuestController@home']);

    Route::get('login', function () {
        return view('guest.pages.login');
    })->name('login');
    
    Route::get('register', function () {
        if (session()->has('access_level')) {
            return redirect()->route('home');
        }
        return view('guest.pages.register');
    })->name('register');
    
    //GUEST
    Route::get('restaurant/{slug}', ['as' => 'guest.restaurant', 'uses' => 'GuestController@guest_restaurant']);
    
    //OWNER REGISTER
    Route::get('partner', ['as' => 'partner', 'uses' => 'RestaurantController@create']);
    Route::post('partner', ['as' => 'guest.partner', 'uses' => 'RestaurantController@store']);
    Route::post('partner/status', ['as' => 'guest.partner.status', 'uses' => 'PartnershipController@status']);
    
    //LOGIN AND REGISTER
    Route::post('login', ['as' => 'guest.login', 'uses' => 'UserController@login']);
    Route::post('register', ['as' => 'guest.register', 'uses' => 'UserController@register']);

    //RESTAURANT PUBLIC
    Route::get('restaurant', ['as' => 'restaurant.search', 'uses' => 'RestaurantController@search']);

    //FORGOT PASSWORD
    Route::get('password/reset', ['as' => 'forgot', 'uses' => 'ForgotController@forgotForm']);
    Route::post('password/email', ['as' => 'guest.forgot', 'uses' => 'ForgotController@forgot']);
    Route::get('password/find/{token}', ['as' => 'reset', 'uses' => 'ForgotController@find']);
    Route::post('password/reset', ['as' => 'guest.reset', 'uses' => 'ForgotController@reset']);

    //VERIFICATION
    Route::get('email/resend', ['as' => 'guest.resend', 'uses' => 'VerificationController@resend']);
    Route::get('email/verify', ['as' => 'guest.verify', 'uses' => 'VerificationController@verify']);
    Route::get('email/manual', ['as' => 'guest.manual', 'uses' => 'VerificationController@manual']);
});

//LOGOUT
Route::get('/logout', ['as' => 'logout', 'uses' => 'UserController@logout']);    

//CONTACT US
Route::get('contactus', ['as' => 'contact_us.index', 'uses' => 'ContactUsController@index']);
Route::post('contactus', ['as' => 'contact_us.store', 'uses' => 'ContactUsController@store']);

//INFO
Route::get('info', ['as' => 'owner.info', 'uses' => 'OwnerController@info']);
Route::post('info', ['as' => 'owner.update.info', 'uses' => 'OwnerController@updateInfo']);

//CUSTOMER
Route::group(['prefix' => 'guest', 'middleware' => 'guest_web'], function () {
    //CART
    Route::post('cart', ['as' => 'cart.store', 'uses' => 'CartController@store']);
    Route::get('cart', ['as' => 'cart.index', 'uses' => 'CartController@index']);
    Route::post('cart/{cart}', ['as' => 'cart.update', 'uses' => 'CartController@update']);
    Route::delete('cart/{cart}', ['as' => 'cart.destroy', 'uses' => 'CartController@destroy']);
    Route::get('cart/empty', ['as' => 'cart.empty', 'uses' => 'CartController@empty']);
});
Route::group(['middleware' => 'guest_web'], function() {
    //PROFILE
    Route::get('profile', ['as' => 'customer.edit', 'uses' => 'CustomerController@edit']);
    Route::post('profile', ['as' => 'customer.update', 'uses' => 'CustomerController@update']);
    Route::post('user/update', ['as' => 'customer.user.update', 'uses' => 'UserController@update']);

    //CHECKOUT
    Route::get('checkout', ['as' => 'guest.checkout', 'uses' => 'GuestController@checkout']);
    Route::post('checkout', ['as' => 'order.store', 'uses' => 'OrderController@store']);

    //ORDER HISTORY
    Route::get('order/history', ['as' => 'order.index', 'uses' => 'OrderController@index']);
    Route::get('order/{code}', ['as' => 'order.show', 'uses' => 'OrderController@show']);

    //RATING
    Route::post('rating', ['as' => 'rating.store', 'uses' => 'RatingController@store']);

    //FAVORITE
    Route::post('favorite', ['as' => 'favorite.store', 'uses' => 'FavoriteController@store']);
    Route::get('favorite', ['as' => 'favorite.index', 'uses' => 'FavoriteController@index']);
    Route::delete('favorite/{restaurant_slug}', ['as' => 'favorite.destroy', 'uses' => 'FavoriteController@destroy']);

    //REQUESTS
    Route::group(['prefix' => 'request'], function() {
        Route::post('/', ['as' => 'changerequest.store.customer', 'uses' => 'ChangeRequestController@store']);
    });

    //NOTIFICATION
    Route::group(['prefix' => 'notification'], function (){
        Route::get('cart', ['as' => 'guest.notification.cart', 'uses' => 'NotificationController@cart']);
    });

    //SUBORDER
    Route::get('order/{suborder}/complete', ['as' => 'guest.order.complete', 'uses' => 'CustomerController@accept_suborder']);
    
    Route::post('suborder/cancel', ['as' => 'guest.suborder.cancel', 'uses' => 'CustomerController@cancel_suborder']);
    Route::post('order/cancel', ['as' => 'guest.order.cancel', 'uses' => 'CustomerController@cancel_order']);

});

//OWNER
Route::group(['prefix' => 'owner', 'middleware' => 'owner_web'], function () {

    //DASHBOARD
    Route::get('dashboard', ['as' => 'owner.dashboard', 'uses' => 'OwnerController@dashboard']);
    Route::get('profile', ['as' => 'owner.profile', 'uses' => 'OwnerController@profile']);
    Route::post('profile', ['as' => 'owner.update', 'uses' => 'OwnerController@profileUpdate']);

    //DASHBOARD
    Route::group(['prefix' => 'charts'], function () {
        Route::get('{filter}', ['uses' => 'DashboardController@owner_chart']);
    });

    Route::post('user/update', ['as' => 'owner.user.update', 'uses' => 'UserController@update']);

    //MENU
    Route::resource('menu', 'MenuController')->except('update');
    Route::post('menu/{menu}', ['as' => 'menu.update', 'uses' => 'MenuController@update']);
    Route::post('menu/{menu}/restore', ['as' => 'menu.restore', 'uses' => 'MenuController@restore']);
    Route::get('datatable/menu', ['as' => 'datatable.menu', 'uses' => 'MenuController@datatable']);

    //CATEGORY
    Route::resource('category', 'CategoryController')->except('update');
    Route::post('category/{category}', ['as' => 'category.update', 'uses' => 'CategoryController@update']);
    Route::post('category/{category}/restore', ['as' => 'category.restore', 'uses' => 'CategoryController@restore']);
    Route::get('datatable/category', ['as' => 'datatable.category', 'uses' => 'CategoryController@datatable']);

    //ORDER
    Route::get('order', ['as' => 'owner.order.index', 'uses' => 'OwnerOrderController@index']);
    Route::get('order/{suborder}', ['as' => 'owner.order.show', 'uses' => 'OwnerOrderController@show']);
    Route::get('datatable/order', ['as' => 'datatable.order', 'uses' => 'OwnerOrderController@datatable']);
    Route::get('order/{suborder}/accept', ['as' => 'owner.order.accept', 'uses' => 'OwnerOrderController@accept']);
    Route::get('order/{suborder}/reject', ['as' => 'owner.order.reject', 'uses' => 'OwnerOrderController@reject']);
    Route::get('order/{suborder}/deliver', ['as' => 'owner.order.deliver', 'uses' => 'OwnerOrderController@deliver']);
    Route::get('order/{suborder}/complete', ['as' => 'owner.order.complete', 'uses' => 'OwnerOrderController@complete']);
    Route::get('order/{suborder}/cancel', ['as' => 'owner.order.cancel', 'uses' => 'OwnerOrderController@cancel']);

    //REPORT
    Route::post('report', ['as' => 'report.store', 'uses' => 'ReportController@store']);
    Route::get('report', ['as' => 'report.owner.index', 'uses' => 'ReportController@owner']);
    Route::get('report/{report_code}', ['as' => 'report.owner.show', 'uses' => 'ReportController@owner_show']);

    //LOGS
    Route::get('activity', ['as' => 'owner.logs.index', 'uses' => 'OwnerLogsController@index']);
    Route::get('datatable/logs', ['as' => 'owner.logs.datatable', 'uses' => 'OwnerLogsController@datatable']);

    //SALES
    Route::get('sales', ['as' => 'owner.sales.index', 'uses' => 'OwnerSalesController@index']);
    Route::get('sales/menu/pdf', ['as' => 'owner.sales.pdf.menu', 'uses' => 'OwnerSalesController@pdf']);
    Route::get('sales/restaurant/pdf', ['as' => 'owner.sales.pdf.restaurant', 'uses' => 'OwnerSalesController@pdf1']);

    //NOTIFICATION
    Route::group(['prefix' => 'notification'], function (){
        Route::get('orders', ['as' => 'owner.notification.orders', 'uses' => 'NotificationController@orders']);
    });

    //REQUESTS
    Route::group(['prefix' => 'request'], function() {
        Route::post('/', ['as' => 'changerequest.store', 'uses' => 'ChangeRequestController@store']);
    });

    Route::get('datatable/sales', ['as' => 'owner.sales.datatable', 'uses' => 'OwnerSalesController@datatable']);
    Route::get('datatable/sales/restaurant', ['as' => 'owner.sales.restaurant.datatable', 'uses' => 'OwnerSalesController@datatable1']);
});

//ADMIN
Route::group(['prefix' => 'admin', 'middleware' => 'admin_web'], function () {

    Route::get('dashboard', ['as' => 'admin.dashboard', 'uses' => 'AdminController@dashboard']);
    Route::get('profile', ['as' => 'admin.show', 'uses' => 'AdminController@show']);
    Route::post('profile', ['as' => 'admin.update', 'uses' => 'AdminController@update']);
    Route::post('user/update', ['as' => 'admin.user.update', 'uses' => 'UserController@update']);

    //DASHBOARD
    Route::group(['prefix' => 'charts'], function () {
        Route::get('{filter}', ['uses' => 'DashboardController@admin_chart']);
    });

    //TAG
    Route::group(['prefix' => 'tag'], function (){
        Route::get('/', ['as' => 'tag.index', 'uses' => 'TagController@index']);
        Route::post('/', ['as' => 'tag.store', 'uses' => 'TagController@store']);
        Route::get('{tag}/reject', ['as' => 'tag.reject', 'uses' => 'TagController@reject']);
        Route::get('{tag}/accept', ['as' => 'tag.accept', 'uses' => 'TagController@accept']);
    }); 
    Route::post('tag/{tag}', ['as' => 'tag.update', 'uses' => 'TagController@update']);
    Route::get('datatable/tag', ['as' => 'datatable.tag', 'uses' => 'TagController@datatable']);

    //PARTNERSHIP
    Route::group(['prefix' => 'partnership'], function (){
        Route::get('/', ['as' => 'partnership.index', 'uses' => 'PartnershipController@index']);
        Route::get('{restaurant}', ['as' => 'partnership.show', 'uses' => 'PartnershipController@show']);

        Route::get('{restaurant}/accept', ['as' => 'partnership.accept', 'uses' => 'PartnershipController@accept']);
        Route::get('{restaurant}/reject', ['as' => 'partnership.reject', 'uses' => 'PartnershipController@reject']);
        Route::get('{restaurant}/review', ['as' => 'partnership.review', 'uses' => 'PartnershipController@review']);
    });
    Route::get('datatable/partnership', ['as' => 'partnership.datatable', 'uses' => 'PartnershipController@datatable']);

    //RESTAURANT
    Route::group(['prefix' => 'restaurant'], function() {
        Route::get('/', ['as' => 'admin.restaurant.index', 'uses' => 'RestaurantController@index']);
        Route::get('{restaurant_id}', ['as' => 'admin.restaurant.show', 'uses' => 'RestaurantController@show']);
        Route::get('{restaurant_id}/menu/{menu_id}', ['as' => 'admin.restaurant.menu', 'uses' => 'RestaurantController@restaurant_menu']);
        Route::get('{restaurant_id}/order/{sub_order_id}', ['as' => 'admin.restaurant.order', 'uses' => 'RestaurantController@restaurant_order_view']);
    });
    Route::get('datatable/restaurant', ['as' => 'datatable.restaurant', 'uses' => 'RestaurantController@datatable']);
    Route::get('datatable/restaurant/menu', ['as' => 'datatable.restaurant.menu', 'uses' => 'RestaurantController@menu_datatable']);
    Route::get('datatable/restaurant/{restaurant_id}/logs', ['as' => 'admin.restaurant.logs.datatable', 'uses' => 'LogController@restaurant_datatable']);
    Route::get('datatable/restaurant/{restaurant_id}/order', ['as' => 'admin.restaurant.order.datatable', 'uses' => 'RestaurantController@restaurant_order']);
    Route::get('datatable/restaurant/{restaurant_id}/report', ['as' => 'admin.restaurant.report.datatable', 'uses' => 'RestaurantController@restaurant_report']);

    //CUSTOMERS
    Route::group(['prefix' => 'customer'], function() {
        Route::get('/', ['as' => 'customer.index', 'uses' => 'CustomerController@index']);
        Route::get('{customer_id}', ['as' => 'customer.show', 'uses' => 'CustomerController@show']);
        Route::get('{customer_id}/order/{order_code}', ['as' => 'customer.order.show', 'uses' => 'CustomerController@customer_order']);
    });
    Route::get('datatable/customer', ['as' => 'datatable.customer', 'uses' => 'CustomerController@datatable']);
    Route::get('datatable/customer/order', ['as' => 'datatable.customer.order', 'uses' => 'CustomerController@datatable_order']);
    Route::get('datatable/admin/customer/{customer_id}/logs', ['as' => 'admin.customer.logs.datatable', 'uses' => 'LogController@customer_datatable']);
    Route::get('datatable/admin/customer/{customer_id}/report', ['as' => 'admin.customer.report.datatable', 'uses' => 'CustomerController@customer_report']);

    //MENU
    Route::group(['prefix' => 'menu'], function() {
        Route::get('/', ['as' => 'admin.menu.index', 'uses' => 'AdminMenuController@index']);
        Route::get('{menu_id}', ['as' => 'admin.menu.show', 'uses' => 'AdminMenuController@show']);
    });
    Route::get('datatable/admin/menu', ['as' => 'datatable.admin.menu', 'uses' => 'AdminMenuController@datatable']);

    //SALES
    Route::group(['prefix' => 'sales'], function() {
        Route::get('/', ['as' => 'admin.sales.index', 'uses' => 'AdminSalesController@index']);
        Route::get('restaurant/pdf', ['as' => 'admin.sales.pdf', 'uses' => 'AdminSalesController@pdf']);
        Route::get('restaurant/pdf1', ['as' => 'admin.sales1.pdf', 'uses' => 'AdminSalesController@pdf1']);
    });
    Route::get('datatable/admin/sales', ['as' => 'datatable.admin.sales', 'uses' => 'AdminSalesController@datatable']);
    Route::get('datatable/admin/sales1', ['as' => 'datatable.admin.sales1', 'uses' => 'AdminSalesController@datatable1']);

    //BAN
    Route::group(['prefix' => 'ban'], function() {
        Route::get('/', ['as' => 'ban.index', 'uses' => 'BanController@index']);
        Route::get('{ban_id}', ['as' => 'ban.show', 'uses' => 'BanController@show']);
        Route::post('/', ['as' => 'ban.store', 'uses' => 'BanController@store']);
        Route::get('{user_id}/lift', ['as' => 'ban.destroy', 'uses' => 'BanController@destroy']);
    });
    Route::get('datatable/ban', ['as' => 'datatable.ban', 'uses' => 'BanController@datatable']);

    //REPORTS
    Route::get('report', ['as' => 'admin.report.index', 'uses' => 'ReportController@admin1']);
    Route::get('report/{report_code}', ['as' => 'admin.report.show', 'uses' => 'ReportController@admin_show']);
    Route::get('report/{report_code}/investigate', ['as' => 'admin.report.investigate', 'uses' => 'ReportController@investigate']);
    Route::post('report/{report_code}/close', ['as' => 'admin.report.close', 'uses' => 'ReportController@close']);
    Route::get('datatable/report', ['as' => 'datatable.report', 'uses' => 'ReportController@datatable']);

    //ORDERS
    Route::group(['prefix' => 'orders'], function (){
        Route::get('/', ['as' => 'admin.order.index.web', 'uses' => 'AdminOrderController@index']);
        Route::get('{order_id}', ['as' => 'admin.order.show.web', 'uses' => 'AdminOrderController@show']);
    });
    Route::get('datatable/admin/order', ['as' => 'admin.order.datatable', 'uses' => 'AdminOrderController@datatable']);

    //LOGS
    Route::get('activity', ['as' => 'admin.logs.index', 'uses' => 'AdminLogsController@index']);
    Route::get('datatable/logs', ['as' => 'admin.logs.datatable', 'uses' => 'AdminLogsController@datatable']);

    //NOTIFICATION
    Route::get('notification/reports', ['as' => 'admin.notification.reports', 'uses' => 'NotificationController@reports']);
    Route::get('notification/tags', ['as' => 'admin.notification.tags', 'uses' => 'NotificationController@tags']);
    Route::get('notification/partnership', ['as' => 'admin.notification.partnership', 'uses' => 'NotificationController@partnership']);
    Route::get('notification/admin', ['as' => 'admin.notification.admin', 'uses' => 'NotificationController@admin']);

    //USERS
    Route::group(['prefix' => 'users'], function (){
        Route::get('/', ['as' => 'admin.user.index', 'uses' => 'AdminUserController@index']);
        Route::get('create', ['as' => 'admin.user.create', 'uses' => 'AdminUserController@create']);
        Route::post('/', ['as' => 'admin.user.store', 'uses' => 'AdminUserController@store']);
    });
    Route::get('datatable/admin/users', ['as' => 'admin.user.datatable', 'uses' => 'AdminUserController@datatable']);

    //REQUESTS
    Route::group(['prefix' => 'request'], function() {
        Route::get('/', ['as' => 'changerequest.index', 'uses' => 'ChangeRequestController@index']);
        Route::get('{request_change_id}/accept', ['as' => 'changerequest.accept', 'uses' => 'ChangeRequestController@accept']);
        Route::get('{request_change_id}/reject', ['as' => 'changerequest.reject', 'uses' => 'ChangeRequestController@reject']);
    });

    //LOGS
    Route::group(['prefix' => 'logs'], function() {
        Route::get('/', ['as' => 'logs.index', 'uses' => 'LogController@index']);
    });
    Route::get('datatable/allLogs', ['as' => 'datatable.allLogs', 'uses' => 'LogController@datatable']);

    Route::get('datatable/changerequest', ['as' => 'datatable.changerequest', 'uses' => 'ChangeRequestController@datatable']);
});

//IMAGE MENU
Route::get('image/menu/{slug}', ['as' => 'photo.menu', 'uses' => 'PhotoController@restaurant_menu']);
Route::get('image/restaurant/{slug}', ['as' => 'photo.restaurant', 'uses' => 'PhotoController@restaurant_image']);
Route::get('image/permit/{slug}', ['as' => 'photo.permit', 'uses' => 'PhotoController@restaurant_permit']);
Route::get('image/report/{slug}', ['as' => 'photo.report', 'uses' => 'PhotoController@restaurant_report']);

Route::get('tester', function(){
    return view('owner.pages.pdf.report');
});

Route::get('email/customer/verification/{code}', function ($code) {
    $message = (new \App\Notifications\EmailVerification($code))
                ->toMail("ryantesoro@yahoo.com");
    $markdown = new Illuminate\Mail\Markdown(view(), config('mail.markdown'));
    return $markdown->render('vendor.notifications.email', $message->toArray());
})->name('customer.verification');

Route::get('email/admin/{email}/{password}', function ($email, $password) {
    $message = (new \App\Notifications\AdminAccountEmail($email, $password))
                ->toMail("ryantesoro@yahoo.com");
    $markdown = new Illuminate\Mail\Markdown(view(), config('mail.markdown'));
    return $markdown->render('vendor.notifications.email', $message->toArray());
})->name('admin.account.email');

Route::get('email/restaurant/verification/{email}/{password}', function ($email, $password) {
    $message = (new \App\Notifications\RestaurantAccept($email, $password))
                ->toMail("ryantesoro@yahoo.com");
    $markdown = new Illuminate\Mail\Markdown(view(), config('mail.markdown'));
    return $markdown->render('vendor.notifications.email', $message->toArray());
});

Route::get('email/customer/forgot/{token}', function ($token) {
    $message = (new \App\Notifications\PasswordResetRequest($token))
                ->toMail("ryantesoro@yahoo.com");
    $markdown = new Illuminate\Mail\Markdown(view(), config('mail.markdown'));
    return $markdown->render('vendor.notifications.email', $message->toArray());
})->name('customer.forgot');

