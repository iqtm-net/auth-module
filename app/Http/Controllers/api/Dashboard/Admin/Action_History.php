<?php

    namespace App\Http\Controllers\api\Dashboard\Admin;

    use Illuminate\Validation\Rule;
    use App\Dashboard_action_history;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;
    use Maatwebsite\Excel\Facades\Excel;
    use Illuminate\Database\Eloquent\Builder;
    use Tymon\JWTAuth\JWTAuth;
    Use \Carbon\Carbon;
    use Illuminate\Support\Arr;
    use Illuminate\Support\Facades\DB;
    use App\Imports\Change_orders_status;
    use Picqer;
    use File;

    class Action_History extends Controller
    {
        public function get_history($dateFrom,$dateto,$user_role,$user_id)
        {
            $get = Dashboard_action_history::
            when($user_role !== "All", function ($q) use($user_role,$user_id) {  return $q->where('user_id', $user_id)->where('user_role',$user_role); })
            ->when($dateFrom !== 'All' && $dateto !== 'All', function ($q) use($dateFrom, $dateto) {return $q->whereBetween('created_at', [$dateFrom, $dateto]); })
            ->paginate(50);

            return response()->json($get);
        }
    }
