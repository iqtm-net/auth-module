<?php

use Illuminate\Http\Request;
use App\Deliver;
use App\Admin;
use App\User;
use App\Store;
use App\Receiver;
use App\Taxi;
use App\Fee;
use App\User_order;
use App\Option;
use App\Orders_group;
use App\Event;
use App\Gd;
use App\prod_price;
use App\Dashboard_action_history;
use App\Notification;
use App\Events\RTNotify;
use App\Prod_price_local;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Namshi\JOSE\SimpleJWS;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;


function Result($data = "Successful" , $code = 200, $error =  null){

    $arr = [
        'data' => [$data],
        'status' => $code,
        'error' => $error
    ];

    return response()->json($arr, $code);
}

function Track_Code_Rand(){

    $AZ = ['A','B','C','D','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
    $ONETOTEEN = [1,2,3,4,5,6,7,8,9,0];
    return Arr::random($AZ).Arr::random($ONETOTEEN).Arr::random($ONETOTEEN).Arr::random($ONETOTEEN).Arr::random($ONETOTEEN).Arr::random($ONETOTEEN);
}

function AvailableRoles(){
    $guards = array_keys(config('auth.guards'));
    unset($guards[0]); // Remove "web" value
    return array_values($guards);
}

function GetAccountByPH($phone_number){

    foreach (AvailableRoles() as $key) {
        $check = DB::table($key)->where('phone_number', $phone_number)->first();
        if($check) { return [
            'user_type' => $key,
            'data' => $check,
        ]; }
    }
}


function user(){ 
    
    $token = JWTAuth::getToken();
    $HashedRoles = SimpleJWS::load($token)->getPayload();
    if(
        !array_key_exists("Account_Role", $HashedRoles)
        OR !array_key_exists("unique", $HashedRoles)
        OR !DB::table($HashedRoles['Account_Role'])->where('code', $HashedRoles['unique'])->first()
    ) { return false; }

    auth()->shouldUse($HashedRoles['Account_Role']);

    $role = Auth::guard()->user();
    $role['role'] = $HashedRoles['Account_Role'];

    return $role;

} 

function current_receiver(){
    return Option::where('option', 'receiving_company_id')->first()->value;
}

function PhoneFilter($ph){
    return "9647".explode("7", AR_TO_EN($ph), 2)[1];
}

function user_role(){
    return user()->role;
}
    
function user_role_number($role = null){
    
    $roles = [
        "users" => 2,
        "stores" => 3,
    ];
    
    return ($role == null) ? Arr::get($roles, user_role()) : Arr::get($roles, $role);
}

function Record_action($action){

    $new = new Dashboard_action_history;
    $new->user_role = user_role();
    $new->user_id = user()->id;
    $new->action = $action;
    $new->save();
    
}

function Location_Filter($location, $dimension){
    if($location !== 'null' OR !is_null($location)){
        return @explode(',', $location)[$dimension];
    }
    else{
        return @'00.00';
    }
}

function BaridiStatesInterface($state){
    
    $states = [
        "AlAnbar" => 3,
        "Babil" => 2,
        "Baghdad" => 0,
        "Erbil" => 4,
        "Basra" => 6,
        // "Dahuk" => 
        "AlDiwaniyah" => 10,
        "Diyala" => 5,
        // "Dhi Qar" =>  
        // "AsSulaymaniyah" => 
        // "Saladin" =>  
        "Kirkuk" => 9,
        "Karbala" => 7,
        // "AlMuthana" => 
        // "Maysan" => 
        "Najaf" => 8
        // "Nineveh" => 
        // "Wasit" => 
        // "Zakho" =>
    ];
    
    return Arr::get($states, $state);
}

function BaridiInterface(array $order){
    
    $fields = [
        "sender_phone_number" => $order['sender_phone_number'],
        "location_on_map_from" => $order['location_on_map_from'],
        "location_to_state" => BaridiStatesInterface($order['location_to_state']),
        "location_from_state" => BaridiStatesInterface($order['location_from_state']),
        "location_to_region" => $order['location_to_region'],
        "reciever_phone_number" => $order['reciever_phone_number'],
        "Deliver_Fee" => $order['Deliver_Fee'],
        "recieved_price" => $order['recieved_price'],
    ];

    $headers = array(
        'Content-Type:application/json',
        'accept:*/*',
        'api-key:8T2ELkPOBkjjxHB6T1uj'
    );
    
    $ci = curl_init();
    curl_setopt_array($ci, array(
        CURLOPT_URL => 'https://www.baridiapp.com/client/HodhodOrder',
        CURLOPT_POSTFIELDS => json_encode($fields),
        CURLOPT_POST => 1,
        CURLOPT_RETURNTRANSFER => 1,
        //CURLOPT_TIMEOUT => 0,
        //CURLOPT_SSL_VERIFYPEER => 1,
        //CURLOPT_USERAGENT => 1,
        //CURLOPT_VERBOSE => 1,
        //CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_HTTPHEADER => $headers
    ));
    $result = curl_exec($ci);
    curl_close($ci);
    
    return $result;

}


function MemberUpgrade(){

    $AZ = ['A','B','C','D','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
    $ONETOTEEN = [1,2,3,4,5,6,7,8,9,0];
    return Arr::random($AZ).Arr::random($ONETOTEEN).Arr::random($ONETOTEEN).Arr::random($ONETOTEEN).Arr::random($ONETOTEEN).Arr::random($ONETOTEEN);
}



function OrdersCounter($id, $account_Role, $status){

    if ($account_Role == 'delivers') { return User_order::where('deliver_id', $id)->where('status', $status)->get()->count(); }
    elseif ($account_Role == 'users')  { return User_order::where('user_id', $id)->where('account_type', 2)->where('status', $status)->get()->count(); }
    elseif ($account_Role == 'stores') { return User_order::where('user_id', $id)->where('account_type', 3)->where('status', $status)->get()->count(); }

}

function ThisWeekChart($column,$prop){

    $orders_this_week[] = User_order::whereBetween($column, [Carbon::now()->subDays(1), Carbon::now()])->get()->$prop;
    $orders_this_week[] = User_order::whereBetween($column, [Carbon::now()->subDays(2), Carbon::now()->subDays(1)])->get()->$prop;
    $orders_this_week[] = User_order::whereBetween($column, [Carbon::now()->subDays(3), Carbon::now()->subDays(2)])->get()->$prop;
    $orders_this_week[] = User_order::whereBetween($column, [Carbon::now()->subDays(4), Carbon::now()->subDays(3)])->get()->$prop;
    $orders_this_week[] = User_order::whereBetween($column, [Carbon::now()->subDays(5), Carbon::now()->subDays(4)])->get()->$prop;
    $orders_this_week[] = User_order::whereBetween($column, [Carbon::now()->subDays(6), Carbon::now()->subDays(5)])->get()->$prop;
    $orders_this_week[] = User_order::whereBetween($column, [Carbon::now()->subDays(7), Carbon::now()->subDays(6)])->get()->$prop;
    return $orders_this_week;
}

function Eventing($event, $redirect, $Panels = ['admins']){

    event(new RTNotify([ 'event' => $event, 'role' => $Panels, ]));

    $NewEvent = new Event;
    $NewEvent->eventer = user_role();
    $NewEvent->eventer_id = user()->id;
    $NewEvent->event = $event;
    $NewEvent->Panel = json_encode($Panels);
    $NewEvent->redirect = $redirect;
    $NewEvent->save();

}



function EventingPublic($eventer,$user_id,$event,$redirect, $Panels = ['admins']){

    foreach($Panels as $Panel){

        event(new RTNotify([ 'event' => $event, 'role' => $Panel, ]));

        $NewEvent = new Event;
        $NewEvent->eventer = $eventer;
        $NewEvent->eventer_id = $user_id;
        $NewEvent->event = $event;
        $NewEvent->Panel = $Panel;
        $NewEvent->redirect = $redirect;
        $NewEvent->save();
    }
}

function ResultNoSB($data = null , $code = 200, $error =  null){

    //$SeccCodeArray = [200, 201, 202, ];
    $arr = [
        'data' => $data,
        'status' => $code, //in_array($code, $SeccCodeArray) ? true : false,
        'error' => $error
    ];

    //return response($arr, $code);
    return response()->json($arr, $code);
}

function CostumVal(){

    return [
        'required' => 'field is required',
        'confirmed' => 'password not matched',
        'unique' => 'phone number already exists',
        'numeric' => 'only numbers allowed',
        'image' => 'file is not image',
        'mimes' => 'file is not image',
        'max' => 'maximum allowed size is 150 mb',
        'digits_between' => 'digits only between 0-13',
    ];

}

function send_message ($phone_number, $code) {

    $username = 'nodydark';
    $password = 'IQMa(!)$1$(#)Sms';
    
    $messages = array(
        array(
            'from'=>"+9647828772577",
            'to' => "+".$phone_number,
            'body'=> 'Your code is : '.$code,
            "routingGroup" => "PREMIUM"
        ),
    );

    $ch = curl_init( );
    $headers = array(
        'Content-Type:application/json',
        'Authorization:Basic '. base64_encode("$username:$password")
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt ( $ch, CURLOPT_URL, 'https://api.bulksms.com/v1/messages?auto-unicode=true&longMessageMaxParts=30' );
    curl_setopt ( $ch, CURLOPT_POST, 1 );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode($messages) );
    // Allow cUrl functions 20 seconds to execute
    curl_setopt ( $ch, CURLOPT_TIMEOUT, 20 );
    // Wait 10 seconds while trying to connect
    curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
    $output = array();
    $output['server_response'] = curl_exec( $ch );
    $curl_info = curl_getinfo( $ch );
    $output['http_status'] = $curl_info[ 'http_code' ];
    $output['error'] = curl_error($ch);
    curl_close( $ch );
    return $output;
}

function ar_english_country($val){

    $mapList = array(
        'iraq'=>'العراق',
        'Erbil'=>'أربيل',
        'Al Anbar'=>'الأنبار',
        'Babil'=>'بابل',
        'Baghdad'=>'بغداد',
        'Basra'=>'البصرة',
        'Dahuk'=>'دهوك',
        'Al Diwaniyah'=>'الديوانية',
        'Diyala'=>'ديالي',
        'Dhi Qar'=>'ذي قار',
        'As Sulaymaniyah'=>'السليمانية',
        'Saladin'=> 'صلاح الدّين',
        'Kirkuk'=>'كركوك',
        'Karbala'=>'كربلاء',
        'Al Muthana'=>'المثنى',
        'Maysan'=>'ميسان',
        'Najaf'=>'النجف',
        'Nineveh'=>'نينوى',
        'Wasit'=>'واسط',
        'Zakho'=>'زاخو',
    );

    $Fval = preg_replace('/\s+/', '', $val);

    if (!preg_match('/[^A-Za-z0-9]/', $Fval))  {
        foreach ($mapList as $key => $value) {
            if (preg_match("/$key/", $Fval)) { return $key; }
        }
    }

    else{
        $state = array_search($val, $mapList);
        if ($state !== 0) { return $state; }
    }

    return $Fval;
}

function AR_TO_EN($input)
{
    $persian = ['٠', '١', '٢', '٣', '٤', '٥' ,'٦' ,'٧', '٨', '٩'];
    $english = [ 0 ,  1 ,  2 ,  3 ,  4 ,  5 ,  6 ,  7 ,  8 ,  9 ];
    return str_replace($persian, $english, $input);
}

function valid($type){

    return [
        //common
            'type' => ['required' => Rule::in(['admins', 'receivers', 'users', 'stores', 'delivers', 'taxis'])],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|numeric|digits_between:10,13|unique:delivers|unique:admins|unique:users|unique:stores|unique:receivers|unique:taxis',
            'address_country' => 'required_unless:type,taxis|string|max:255',
            'address_state' => 'required_unless:type,taxis|string|max:255',
            'address_region' => 'required_unless:type,taxis|string|max:255',
            'password' => 'required|string|min:6|confirmed',

        //delivers
            'transport_type' => 'required_if:type,delivers|string|max:255',
            'size' => 'required_if:type,delivers|string|max:255',
            'shipping_type' => 'required_if:type,delivers|string|max:255',
            'IsCompany' => 'required_if:type,delivers|string|max:255',

        //stores
            'store_name' => 'required_if:type,stores|string|max:255',
            
        //taxis
            'car_type' => ['required_if:type,taxis' => Rule::in(['vip', 'normal'])],
            'car_color' => 'required_if:type,taxis|string|max:255',
            'car_docs_image' => 'required_if:type,taxis|string|max:255',
            'profile_picture' => 'required_if:type,taxis|string|max:255',
            'car_license_number' => 'required_if:type,taxis|string|max:255',
            'current_location' => 'required_if:type,taxis|string|max:255',
    ];
    
    
}

function craet($type, $requestData){

    if($type == "delivers"){
        return Deliver::create($requestData);
    }
    elseif($type == "users"){

        return User::create($requestData);
    }
    elseif($type == "stores"){

        return Store::create($requestData);
    }
    elseif($type == "receivers"){
        return Receiver::create($requestData);
    }
    elseif($type == "taxis"){

        return Taxi::create($requestData);
    }

}

function array_except($array, $keys) {
    return array_diff_key($array, array_flip((array) $keys));
}

function orders_stat($type,$account_id) {

    if ($type == "delivers") {
        $update = Deliver::find($account_id);
        $update->issued_ords = User_order::where('deliver_id',$account_id)->where('status','issued')->get()->count();
        $update->pending_ords = User_order::where('deliver_id',$account_id)->where('status','pending')->get()->count();
        $update->delivered_ords = User_order::where('deliver_id',$account_id)->where('status','delivered')->get()->count();
        $update->save();
    }

    elseif($type == "users"){
        $update = User::find($account_id);
        $update->issued_ords = User_order::where('user_id',$account_id)->where('status','issued')->get()->count();
        $update->pending_ords = User_order::where('user_id',$account_id)->where('status','pending')->get()->count();
        $update->delivered_ords = User_order::where('user_id',$account_id)->where('status','delivered')->get()->count();
        $update->save();
    }

    elseif($type == "stores"){
        $update = Store::find($account_id);
        $update->issued_ords = User_order::where('user_id',$account_id)->where('status','issued')->get()->count();
        $update->pending_ords = User_order::where('user_id',$account_id)->where('status','pending')->get()->count();
        $update->delivered_ords = User_order::where('user_id',$account_id)->where('status','delivered')->get()->count();
        $update->save();
    }
}

function PhoneFormat($ph){
    if (substr($ph, 0,5) !== '+9647'
        && substr($ph, 0,4) !== '9647'
        && substr($ph, 0,2) !== '07'
        && substr($ph, 0,1) !== '7')
        { return false;}
    else{ return true;}
}

function ValidatedColumnsExcept($type, $except){

    $Validations = [
        "bigint" => "required|Numeric|digits_between:0,20",
        "boolean" => "required|boolean",
        "string" => "required|max:200",
    ];

    $get_columns = DB::getSchemaBuilder()->getColumnListing($type); 
    
    $FArray = [];
    foreach ($get_columns as $column) {
        $ColumnType = DB::getSchemaBuilder()->getColumnType($type, $column);
        $FArray[$column] = (Arr::exists($Validations, $ColumnType)) ? $Validations[$ColumnType] : "required";
    }
    

    return Arr::except($FArray, $except);
}

function GetTBCol($table){

    //first get columns from database
    $get_columns = DB::getSchemaBuilder()->getColumnListing($table);

    return array_except($get_columns, ['id', 'email', 'remember_token', 'created_at', 'updated_at', 'confirmed', 'firebase_token', 'GD','stripe_id', 'card_brand', 'card_last_four','trial_ends_at', 'credit', 'debts', 'Deliver_Fee', 'Deliver_Fee_Global', 'balance','phone_number','discounts','credit','issued_ords','delivered_ords']);

}

function DeliverFee($from, $to, $Role, $CustomeFees){

    //if shipping is local
    if (ar_english_country($from) == ar_english_country($to)) {

        ($CustomeFees->local_deliver_fee) ? $CustomeFees->local_deliver_fee : "noo" ;

        $res = Prod_price_local::first();
        $res->type = "local";
        $res->Deliver_Fee = ($CustomeFees->local_deliver_fee) ? $CustomeFees->local_deliver_fee : $res->Deliver_Fee;

        return $res;
    }

    //if shipping is global
    else{
        
        $res = prod_price::where('type', $Role)->first();
        $res->type = "global";
        $res->Deliver_Fee = ($CustomeFees->global_deliver_fee) ? $CustomeFees->global_deliver_fee : $res->Deliver_Fee;

        return $res;
    }

}

function table_byAccountType($Role,$user_id){
    
    if($Role == '3' || $Role == 'stores'){
        return Store::find($user_id);
    }
    elseif($Role == '2' || $Role == 'users'){
        return User::find($user_id);
    }
    
    return false;
}

function images($request, $column, $path){

    return Storage::disk('s3')->url($request->file($column)->store($path, 's3'));

}

function base64images($request, $column, $path){

    $base64String= $request->get('image');
    $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$base64String));
    $imageName = Str::random(30).'.png';
    $p = Storage::disk('s3')->put($path."/".$imageName, $image, 'public'); 
    return $image_url = env('AWS_URL')."/".$path."/".$imageName;

}

function Notification($tkns,$title,$body,$phone_number, $order_id = null, $Record = true){

    $user_device_key = $tkns;
    $msg = array(
        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        'status'     => 'done',
        'id'         => '1',
        'largeIcon'    => 'large_icon',
        'smallIcon'    => 'small_icon',
        'title'      => $title,
        'body'         => $body,
        'count'      => 1,
        'prodectid'    => '225',
    );
    $fields = array(
        'registration_ids'  => $user_device_key,
        'data' => $msg,
        'notification' => [
            'title' =>  $msg['title'],
            'body'  => $msg['body']
        ]
    );
    $headers = array(
        'Authorization: key=AAAA75lYGIE:APA91bHV2JYjaSbxqjuL2EFVgCNWLbR5qOYoiRXRtcx2BJl0f1vGr-ufwOjFK3CYOqGk0x1kTsgbHhzCXvqSU78sRyCfjN0IfeadwuDrmFnfQiBqprTDQp1NIbzW4J9u0fUbzN6GRa34',
        'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $op = curl_exec($ch);
    curl_close($ch);

    if($Record){
        $post = new Notification;
        $post->MSG = $body;
        $post->title = $title;
        $post->MemberPhoneNumber = $phone_number;
        $post->order_id = $order_id;
        $post->save();
    }
    
    return $op;
    
}


function percentage($Value,$Total){
    return ($Total == 0 || $Value == 0) ? 0 : round(100*($Value/$Total));
}

function arr_multi_sum($Arr, $value){
    return array_sum(array_column($Arr, $value));
}