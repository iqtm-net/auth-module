<?php

/*
|-------------------------------------------------------------------------
| Application Routes
|-------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
| 
*/

//PUBLIC
$router->group(['prefix' => 'api', 'middleware' => ['throttle:180,60']], function($router)
{   
    //Authintication
    $router->post('login_in', 'api\Authintication\MainAuth@authenticate');
    $router->post('registers', 'api\Authintication\MainAuth@register');
    $router->post('confirm', 'api\Authintication\MainAuth@confirm_code');
    $router->post('resetpass', 'api\Authintication\MainAuth@resetpass');
    $router->post('vue_session_store', 'api\Authintication\MainAuth@vue_session_store');
    $router->get('vue_session_get', 'api\Authintication\MainAuth@vue_session_get');
    $router->post('vue_session_forget', 'api\Authintication\MainAuth@vue_session_forget');

    //Articles
    $router->get('Articles', 'api\Dashboard\Admin\Articles@get');
    $router->get('Articles/{id}', 'api\Dashboard\Admin\Articles@get_by_id');

    //Other
    $router->get('Policies', 'api\Policies@Fetch');
    $router->post('track_order', 'api\track_order@index');
    $router->get('prices', 'api\Order@prices');
    $router->post('TrackOrder', 'api\Order@SearchOrder');
    $router->get('Get_Store_warehouse', 'api\Order@Get_Store_warehouse');
    $router->post('submit_orders_By_Coustomer', 'api\Order@submit_orders_By_Coustomer');
    $router->post('Submit_Cart_By_Store', 'api\Order@Submit_Cart_By_Store');
    $router->post('Store_Delete_From_warehouse', 'api\Order@Store_Delete_From_warehouse');
    $router->get('Store_Statistic', 'api\Order@Store_Statistic');
    $router->get('Orders/{id}', 'api\Order@orders_by_id');
    $router->get('DownloadStat/{id}/{type}/{status}/{DateFrom}/{DateTo}', 'api\Dashboard\Admin\Orders@DownloadStat');
    $router->post('Report', 'api\Reporting@Reporting1');
    $router->get('get_client_infos/{cliend_id}', 'api\Authintication\MainAuth@get_client_infos');
    $router->get('client/statistic/{client_unique_code}/{download}', 'api\my_account@statistic');
    $router->post('client/txt_service', 'api\Order@txt_service');
    $router->get('client/search_orders/{keyword}', 'api\Order@search_orders');

    //Store Web
    $router->get('shopping/{subdomain_name}', 'api\Stores@main');
    $router->get('shopping/{subdomain_name}/branches', 'api\Stores@branches');
    $router->get('shopping/{subdomain_name}/items/{branch_id}', 'api\Stores@items');
    $router->get('shopping/{subdomain_name}/view_item/{item_id}', 'api\Stores@view_item');
    $router->post('shopping/customer/add_to_cart', 'api\Stores@add_to_cart');
    $router->post('shopping/customer/remove_from_cart', 'api\Stores@remove_from_cart');
    $router->get('shopping/customer/cart', 'api\Stores@cart');
    $router->post('shopping/customer/submit_cart', 'api\Stores@submit_cart');
    $router->post('shopping/customer/add_to_favourites', 'api\Stores@add_to_favourites');
    $router->get('shopping/customer/favourites', 'api\Stores@favourites');
    $router->post('shopping/customer/remove_from_favourites', 'api\Stores@remove_from_favourites');
    $router->get('shopping/{subdomain_name}/get_active_stores', 'api\Stores@get_active_stores');
    $router->get('shopping/{subdomain_name}/search/{keyword}', 'api\Stores@search');

    //Store Application
    $router->get('check_shared_link_code/{subdomain_name}', 'api\Stores@check_shared_link_code');
    $router->get('client/statistic/{client_unique_code}/{download}', 'api\my_account@statistic');
    $router->post('client/txt_service', 'api\Order@txt_service');
    $router->get('client/search_orders/{keyword}', 'api\Order@search_orders');
    $router->get('get_active_stores', 'api\Stores@get_active_stores');
    $router->get('get_active_store_branches/{subdomain_name}', 'api\Stores@get_active_store_branches');
    $router->get('get_branche_items_pag/{subdomain_name}/{branch_id}', 'api\Stores@get_branche_items_pag');
    $router->get('view_item/{item_id}', 'api\Stores@view_item_app');

});

//Delivers
$router->group(['prefix' => 'api', 'middleware' => ['throttle:10,120']], function($router)
{   
    $router->post('Deliver/check_deliver_code', 'api\Dashboard\Deliver\main@check_deliver_code');
    $router->post('Deliver/update_status', 'api\Dashboard\Deliver\main@update_status');
    $router->post('Deliver/add_delivers', 'api\Dashboard\Deliver\main@add_delivers');
});

//PRIVET
$router->group(['prefix' => 'api', 'middleware' => ['auth', 'throttle:180,60']], function($router)
{

    //All
    $router->post('prices', 'api\Order@GetPrice');
    $router->get('Report', 'api\Reporting@reports');
    $router->get('me', 'api\my_account@me'); ##
    $router->get('MyId', 'api\my_account@MyId'); ##
    $router->get('My_Notifications', 'api\my_notifications@My_Notifications');
    $router->post('me/update', 'api\Authintication\MainAuth@updateaccount');
    $router->post('DownloadOrderExcel', 'api\Order@DownloadOrderExcel');
    $router->post('new_shared_link_orders', 'api\Order@new_shared_link_orders');
    $router->post('DownloadWithdrawOrders', 'api\Dashboard\Admin\Accounts@DownloadWithdrawOrders');

    //ORDER User and store accounts
    $router->post('new_order', 'api\Order@new_order');
    $router->post('edit_order', 'api\Order@edit_order');
    $router->post('Check_Receiver_by_phone', 'api\Order@Check_Receiver_by_phone');
    $router->post('submit_orders', 'api\Order@submit_orders');
    $router->get('Get_Cart', 'api\Order@Get_Cart');
    $router->get('My_Orders', 'api\Order@My_Orders');
    $router->get('MemberOrders/Get_shared_orders_Cart', 'api\Order@Get_shared_orders_Cart');
    
    //Admin
    $router->get('Notifications/events', 'api\my_notifications@events');
    $router->get('Notifications/counter', 'api\my_notifications@getcounter');
    $router->post('Notifications/counter', 'api\my_notifications@update_counter');

    //Admin
    $router->post('Admin/Policies_Put', 'api\Policies@Put');
    $router->post('Admin/Policies_Delete', 'api\Policies@Delete');
    $router->get('Admin/options', 'api\Dashboard\Admin\Orders@options');
    $router->post('Admin/add_specialties', 'api\Dashboard\Admin\Orders@add_specialties');
    $router->post('Admin/delete_specialties', 'api\Dashboard\Admin\Orders@delete_specialties');
    $router->post('Admin/add_store_theme', 'api\Dashboard\Admin\Orders@add_store_theme');
    $router->post('Admin/deactivate_theme', 'api\Dashboard\Admin\Orders@deactivate_theme');
    $router->post('Admin/check_state', 'api\Dashboard\Admin\Orders@check_tate');
    $router->post('Admin/options', 'api\Dashboard\Admin\Orders@update_options');
    $router->get('Admin/notifications', 'api\Dashboard\Admin\Accounts@notifications');
    $router->post('Admin/notify_all', 'api\Dashboard\Admin\Accounts@SendNorifyAll');
    $router->post('Admin/notify', 'api\Dashboard\Admin\Accounts@SendNorify');
    $router->get('Admin/unconfirmed_delivers', 'api\Dashboard\Admin\Delivers@get_deliveres_unconfirmed');
    $router->get('Admin/confirmed_delivers', 'api\Dashboard\Admin\Delivers@get_deliveres_confirmed');
    $router->post('Admin/confirm_deliver', 'api\Dashboard\Admin\Delivers@confirm_deliver');
    $router->post('Admin/confirm_deliver_by_phone', 'api\Dashboard\Admin\Delivers@confirm_deliver_by_phone');
    $router->post('Admin/unconfirm_deliver', 'api\Dashboard\Admin\Delivers@unconfirm_deliver');
    $router->get('Admin/Car_Docs', 'api\Dashboard\Admin\Delivers@Car_Docs');
    $router->get('Admin/modify_account_get/{type}', 'api\Dashboard\Admin\Accounts@get_table_accounts'); //CAW
    $router->get('Admin/Account/{type}/{id}', 'api\Dashboard\Admin\Accounts@get_account');
    $router->get('Admin/get_account_by_phone/{type}/{phone_number}', 'api\Dashboard\Admin\Accounts@get_account_by_phone');
    $router->post('Admin/Account/{type}/{id}', 'api\Dashboard\Admin\Accounts@updateaccount');
    $router->get('Admin/search_for_account/{keyword}/{Role}', 'api\Dashboard\Admin\Accounts@search_for_account'); // CAW
    $router->get('Admin/home/{dateFrom}/{dateto}', 'api\Dashboard\Admin\Home@index');
    $router->get('Admin/discounts', 'api\Dashboard\Admin\Discounts@get');
    $router->post('Admin/add_discounts_code', 'api\Dashboard\Admin\Discounts@Add');
    $router->get('Admin/add_clients_discounts_code/{keyword}', 'api\Dashboard\Admin\Discounts@add_clients_discounts_code');
    $router->post('Admin/delete_discounts_code', 'api\Dashboard\Admin\Discounts@Delete');
    $router->post('Admin/pin_discounts_code', 'api\Dashboard\Admin\Discounts@pin_discounts_code');
    $router->get('Admin/pinned_discounts', 'api\Dashboard\Admin\Discounts@pinned_discounts');
    $router->get('Admin/GdRequests', 'api\Dashboard\Admin\Delivers@GdRequests_unconfirmed');
    $router->get('Admin/Gdconfirmed', 'api\Dashboard\Admin\Delivers@GdRequests_confirmed');
    $router->get('Admin/Account_Cart/{Cart_id}', 'api\Dashboard\Admin\Orders@Account_Cart');
    $router->get('Admin/Orders/{role}/{id}/{status}/{dateFrom}/{dateto}/{FromState}/{ToState}', 'api\Dashboard\Admin\Orders@get_orders');
    $router->get('Admin/Statistic/{role}/{id}/{dateFrom}/{dateto}/{FromState}/{ToState}', 'api\Dashboard\Admin\Orders@Statistic');
    $router->get('Admin/Orders/{id}', 'api\Dashboard\Admin\Orders@get_order_by_id');
    $router->post('Admin/Orders/{id}', 'api\Dashboard\Admin\Orders@update_order');
    $router->post('Admin/Delete_Orders', 'api\Dashboard\Admin\Orders@delete_orders');
    $router->post('Admin/Add_Support', 'api\Dashboard\Admin\Accounts@Add_Support');
    $router->get('Admin/get_premissions', 'api\Dashboard\Admin\Accounts@get_premissions');
    $router->post('Admin/Remove_Support', 'api\Dashboard\Admin\Accounts@Remove_Support');
    $router->post('Admin/Remove_Order', 'api\Dashboard\Admin\Orders@Remove_Order');
    $router->get('Admin/price', 'api\Dashboard\Admin\Orders@prices');
    $router->post('Admin/AddArticle', 'api\Dashboard\Admin\Articles@Add');
    $router->post('Admin/UpdateArticle', 'api\Dashboard\Admin\Articles@UpdateArticle');
    $router->post('Admin/DeleteArticle', 'api\Dashboard\Admin\Articles@DeleteArticle');
    $router->get('Admin/getprice/{id}', 'api\Dashboard\Admin\Orders@getprice');
    $router->post('Admin/Add_price', 'api\Dashboard\Admin\Orders@Add_price');
    $router->post('Admin/deleteprice', 'api\Dashboard\Admin\Orders@deleteprice');
    $router->post('Admin/update_price/{id}', 'api\Dashboard\Admin\Orders@update_price');
    $router->get('Admin/getprice_local/{id}', 'api\Dashboard\Admin\Orders@getprice_local');
    $router->post('Admin/Add_price_local', 'api\Dashboard\Admin\Orders@Add_price_local');
    $router->post('Admin/deleteprice_local', 'api\Dashboard\Admin\Orders@deleteprice_local');
    $router->post('Admin/update_price_local/{id}', 'api\Dashboard\Admin\Orders@update_price_local');
    $router->get('Admin/search_for_order/{role}/{id}/{keyword}', 'api\Dashboard\Admin\Orders@search_for_order');
    $router->get('Admin/order_status_history/{order_id}', 'api\Dashboard\Admin\Orders@order_status_history');
    $router->post('Admin/ChangeSelectedOrdersStatus', 'api\Dashboard\Admin\Orders@ChangeSelectedOrdersStatus');
    $router->post('Admin/AddPartialRefund', 'api\Dashboard\Admin\Orders@AddPartialRefund'); //DELIVERS AS WELL
    $router->post('Admin/orders/DownloadExcel', 'api\Dashboard\Admin\Orders@DownloadExcel');
    $router->get('Admin/ShowAllTopMembers', 'api\Dashboard\Admin\Home@ShowAllTopMembers');
    $router->get('withdraw_orders/{id}/{role}/{status}', 'api\Dashboard\Admin\Accounts@withdraw_orders');//admin');
    $router->post('Accept_withdraw', 'api\Dashboard\Admin\Accounts@Accept_withdraw');//admin');
    $router->post('Order_withdraw', 'api\Dashboard\Admin\Accounts@Order_withdraw'); ///XXXX
    $router->post('AddCreditToGd', 'api\Dashboard\Admin\Accounts@AddCreditToGd');
    $router->post('Admin/PrintLabels', 'api\Dashboard\Admin\Orders@PrintLabels');
    $router->post('Admin/FT', 'api\Dashboard\Admin\Orders@FT');
    $router->post('Admin/ChangeOrdersStatusByExcel', 'api\Dashboard\Admin\Orders@ChangeOrdersStatusByExcel');
    $router->post('Admin/action_history', 'api\Dashboard\Admin\Orders@ChangeOrdersStatusByExcel');
    $router->get('Admin/check_order_id/{order_id}', 'api\Dashboard\Admin\Orders@check_order_id');
    $router->get('Admin/action_history/{dateFrom}/{dateto}/{user_role}/{user_id}', 'api\Dashboard\Admin\Action_History@get_history');

    //Users, Stores, Delivers
    $router->get('DownloadSubmittedCartOrders', 'api\Order@DownloadSubmittedCartOrders');
    $router->post('MemberUpgrade', 'api\Dashboard\Admin\Accounts@MemberUpgrade');
    $router->get('MemberOrders/Cart_Orders/{Cart_id}', 'api\Order@Cart_Orders');
    $router->get('MemberOrders/Cart_Orders_pending/{Cart_id}', 'api\Order@Cart_Orders_waiting');
    $router->get('MemberOrders/wating_orders', 'api\Order@wating_orders');
    $router->get('MemberOrders/StatesOfPendingOrders', 'api\Order@pending_states_distinct');
    $router->get('MemberOrders/OrdersByStatus/{status}', 'api\Order@OrdersByStatus');
    $router->get('MemberOrders/waiting/{Cart_id}', 'api\Order@waiting');
    $router->get('MemberOrders/OrdersByState/{state}', 'api\Order@pending_orders_by_state');
    $router->get('MemberOrders/member_stack/{Account_type}/{Member_id}', 'api\Order@member_stack');
    $router->post('MemberOrders/Member_Pop_stack', 'api\Order@Member_Pop_stack');
    $router->post('MemberOrders/Download_Member_Stack', 'api\Order@Download_Member_Stack');
    $router->post('Rating', 'api\Rating@Rating');
    $router->get('GetRate/{Section}/{rated_id}', 'api\Rating@GetRate');

    //Users, Stores
    $router->post('MemberOrders/Delete', 'api\Order@DeleteOrderFromCart');
    $router->post('MemberCarts/Delete', 'api\Order@DeleteCart');
    $router->post('new_branch', 'api\Order@new_branch');
    $router->post('deactivate_branch', 'api\Order@deactivate_branch');
    $router->get('get_branches/{active}', 'api\Order@get_branches');
    $router->post('new_item_in_warehouse', 'api\Order@new_item_in_warehouse');
    $router->get('get_branche_items/{branch_id}', 'api\Order@get_branche_items');
    $router->post('update_item_in_warehouse', 'api\Order@update_item_in_warehouse');
    $router->post('rate_item', 'api\Order@rate_item');
    $router->get('get_discount_codes', 'api\Order@get_discount_codes');


    //Stores
    $router->post('client/upload_orders_via_excel', 'api\Order@upload_orders_via_excel');
    $router->get('stores_specialties', 'api\Dashboard\Admin\Orders@specialties');
    $router->get('stores_themes', 'api\Dashboard\Admin\Orders@get_stores_themes');
    $router->post('virtual_store_setup', 'api\Dashboard\Admin\Orders@virtual_store_setup');
    $router->post('virtual_store_check', 'api\Dashboard\Admin\Orders@virtual_store_check');
    $router->post('virtual_store_edit', 'api\Dashboard\Admin\Orders@virtual_store_edit');

    //Companies & Delivers
    $router->post('Admin/change_receiving_company', 'api\Dashboard\Admin\Companies@change_receiving_company');
    $router->get('Admin/current_receiving_company', 'api\Dashboard\Admin\Companies@current_receiving_company');
    $router->post('Admin/Add_Companie', 'api\Dashboard\Admin\Companies@Add_Companie');
    $router->get('Admin/get_companies', 'api\Dashboard\Admin\Companies@get_companies');
    $router->post('Admin/Transfer_order', 'api\Dashboard\Admin\Companies@Transfer_order'); //Companies
    $router->get('Admin/search_for_companies/{keyword}', 'api\Dashboard\Admin\Companies@search_for_companies');
    $router->get('Admin/status_tracking_history/{user_id}/{user_role}', 'api\Dashboard\Admin\Companies@status_tracking_history');
    $router->get('Admin/search_status_history_by_id/{order_id}', 'api\Dashboard\Admin\Companies@search_status_history_by_id');
    $router->post('Deliver/update_track_status', 'api\Dashboard\Deliver\Orders@update_track_status');
    $router->post('Deliver/Set_Delay_Status', 'api\Dashboard\Deliver\Orders@Set_Delay_Status');


});


$router->get('/', function ()  {
    return view('home');
});

$router->get('/HodHod_Api', function ()  {
    return view('Api');
});

$router->get('/{route:.*}/', function ()  {
    return view('home');
});
