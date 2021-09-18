<?php

    namespace App\Http\Controllers\api\Dashboard\Admin;

    use App\Deliver;
    use App\Gd;
    use App\Deliver_car_doc;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Tymon\JWTAuth\JWTAuth;
    use Illuminate\Support\Facades\DB;

    class Delivers extends Controller
    { 
        public function get_deliveres_unconfirmed()
        {

            $get = Deliver::where('confirmed', 0)->latest()->paginate(20);
            return response()->json($get);
            //return ResultNoSB($get);
        }

        public function get_deliveres_confirmed()
        {

            $get = Deliver::where('confirmed', 1)->latest()->paginate(20);
            return response()->json($get);
            //return ResultNoSB($get);
        }

        public function NotifyUnconfirmedDelivers()
        {

            $get = Deliver::where('id', 130)->orWhere('id','389')->get();

            foreach($get as $Get){
                Notification($Get->firebase_token, 'test', 'test test' );
            }
            return response()->json($get);
            //return ResultNoSB($get);
        }

        public function confirm_deliver(Request $request)
        {
            $id = $request->only('id')['id'];

            $update = Deliver::where('id', $id)->first();//find($id);
                    if (!$update) { return Result("Deliver not found", 202); }

            $updateDocStatus = Deliver_car_doc::where('phone_number', $update->phone_number)->first();//find($id);
                    if (!$updateDocStatus) { return Result("Deliver_Doc not found", 203); }

            $update->confirmed = 1;
            $update->save();

            $updateDocStatus->status = 1;
            $updateDocStatus->save();

            Notification([$update->firebase_token], 'تم تفعيل الحساب', 'تمت مراجعة المستمسكات المرفوعة وقبول الحسابك' );

            return response()->json(null, 200);

        }

        public function confirm_deliver_by_phone(Request $request)
        {
            $phone_number = $request->only('phone_number')['phone_number'];

            $update = Deliver::where('phone_number', $phone_number)->first();//prepair account
                if (!$update) { return Result("Deliver not found", 202); }

            $updateDocStatus = Deliver_car_doc::where('phone_number', $phone_number)->first();//prepair Documents
                if (!$updateDocStatus) { return Result("Deliver_Doc not found", 203); }

            $update->confirmed = 1;
            $update->save();

            $updateDocStatus->status = 1;
            $updateDocStatus->save();


            return response()->json(null, 200);

        }

        public function unconfirm_deliver(Request $request)
        {

            $id = $request->only('id')['id'];
            $update = Deliver::find($id);

            if (!$update) { return Result("Deliver not found", 202); }

            $update->confirmed = 0;
            $update->save();

            return response()->json(null, 200);
        }

        public function GdRequests_unconfirmed()
        {
            return $GetGdDistinct = Gd::where('status', 0)->select('delivers.*')->distinct('deliver_id')->join('delivers', 'delivers.id', '=', 'gds.deliver_id')->paginate(10);

            return response()->json($GetGdDistinct, 200);
        }

        public function GdRequests_confirmed()
        {
            return $GetGdDistinct = Gd::where('status', 1)->select('delivers.*')->distinct('deliver_id')->join('delivers', 'delivers.id', '=', 'gds.deliver_id')->paginate(10);

            return response()->json($GetGdDistinct, 200);
        }

        public function AdminGdCarss($deliver_id)
        {

            $GetGdCars = Gd::where('deliver_id', $deliver_id)
            ->select('delivers.*')
            ->join('delivers', 'delivers.phone_number', '=', 'gds.phone_numbers')
            ->paginate(10);

            return response()->json($GetGdCars, 200);
        }

        public function ConfirmGD(Request $request)
        {

            $deliver_id = $request->only('id')['id'];

            $confirm = Deliver::find($deliver_id);

            //Validation
            if (!$confirm) { return Result(null, 400, "deliver id not found"); }

            if (!Gd::where('deliver_id', $deliver_id)->first()) { return Result(null, 400, "This Deliver Did not request admin premessions"); }

            $confirm->GD = 1;
            $confirm->save();

            //update gd status
            DB::table('gds')->where('deliver_id', $deliver_id)->update(['status' => 1]);

            //DB::table($key)->where('phone_number', $phone_number)->update($requestData);

            Notification([$confirm->firebase_token], 'خدمة GD', 'تم تفعيل خدمة ادارة الشركات GD لحسابك يمكنك اعادة تسجيل الدخول الان لتتمتع بالخدمة.');

            return Result("Seccuss", 200, null);
        }

        public function Car_Docs()
        {

            $Car_Docs = Deliver_car_doc::select('deliver_car_docs.*', 'delivers.id as DeliverId')
            ->join('delivers', 'delivers.phone_number', '=', 'deliver_car_docs.phone_number')
            ->latest()
            ->paginate(10);
            return response()->json($Car_Docs, 200);
        }


    }
