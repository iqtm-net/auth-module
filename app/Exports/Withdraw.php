<?php

namespace App\Exports;

use App\Withdraw_order;
use App\User;
use App\Store;
use Picqer;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithHeadings;

ini_set('memory_limit', '-1'); 


class Withdraw implements FromCollection, WithHeadings, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $status;

    public function __construct($status,$withdraw_ids)
    {
        $this->status = $status;
        $this->withdraw_ids = $withdraw_ids;
    }

    public function collection()
    {   
        $status = $this->status;
        $withdraw_ids = $this->withdraw_ids;
        $Result = [];  

        foreach($withdraw_ids as $withdraw_id){

            $Get = Withdraw_order::find($withdraw_id);
            $Member = table_byAccountType($Get->memebr_role, $Get->memebr_id);

            $Result[] = [
                $Member->first_name." ".$Member->last_name, 
                $Get->memebr_role,
                ($status == 1) ? (string)$Get->balance : (string)$Member->balance,
                $Get->created_at,
                $Get->updated_at
            ]; 
        } 

        return collect($Result);
        

        // if ($this->status == "CurrentBalances"){
        //     $users0 = User::select('phone_number','first_name','last_name','balance')->get()->toArray();
        //     foreach($users0 as $user){
        //         if ($user['balance'] > 0) { $users[] = $user; }
        //     }

        //     $stores0 = Store::select('phone_number','first_name','last_name','balance')->get()->toArray();
        //     foreach($stores0 as $store){
        //         if ($store['balance'] > 0) { $stores[] = $store; }
        //     }
        // }
        // elseif ($this->status == "Withdrawn_And_Unwithdrawn"){
        //     $users0 = User::select('phone_number','first_name','last_name','balance')->get()->toArray();
        //     foreach($users0 as $user){ if ($user['balance'] > 0) { $users1[] = $user; } }

        //     $stores0 = Store::select('phone_number','first_name','last_name','balance')->get()->toArray();
        //     foreach($stores0 as $store){ if ($store['balance'] > 0) { $stores1[] = $store; } }

        //     $users2 = Withdraw_order::
        //     where('memebr_role', 'users')
        //         ->join("users", "users.id" , "=" ,'withdraw_orders.memebr_id')
        //         ->where('status', 1)
        //         ->select('users.phone_number','users.first_name','users.last_name','withdraw_orders.balance')
        //         ->get()->toArray();

        //     $stores2 = Withdraw_order::
        //     where('memebr_role', 'stores')
        //         ->join("stores", "stores.id" , "=" ,'withdraw_orders.memebr_id')
        //         ->where('status', 1)
        //         ->select('stores.phone_number','stores.first_name','stores.last_name','withdraw_orders.balance')
        //         ->get()->toArray();

        //     $users = array_merge($users1,$users2);
        //     $stores = array_merge($stores1,$stores2);
        // }
        // else{
        //     $users = Withdraw_order::
        //     where('memebr_role', 'users')
        //         ->join("users", "users.id" , "=" ,'withdraw_orders.memebr_id')
        //         ->when($this->status == 'Accepted', function ($q) { return $q->where('status', 1)->select('users.phone_number','users.first_name','users.last_name','withdraw_orders.balance'); })
        //         ->when($this->status == 'UnAccepted', function ($q) { return $q->where('status', 0)->select('users.phone_number','users.first_name','users.last_name','users.balance'); })
        //         ->get()->toArray();

        //     $stores = Withdraw_order::
        //     where('memebr_role', 'stores')
        //         ->join("stores", "stores.id" , "=" ,'withdraw_orders.memebr_id')
        //         ->when($this->status == 'Accepted', function ($q) { return $q->where('status', 1)->select('stores.phone_number','stores.first_name','stores.last_name','withdraw_orders.balance'); })
        //         ->when($this->status == 'UnAccepted', function ($q) { return $q->where('status', 0)->select('stores.phone_number','stores.first_name','stores.last_name','stores.balance'); })
        //         ->get()->toArray();
        // }


        // $WD_Orders = array_merge($users,$stores);

        // foreach ($WD_Orders as $get2){

        //     $ResultsF1['Wallet Number'] = $get2['phone_number'];
        //     $ResultsF1['Name'] = $get2['first_name']." ".$get2['last_name'];
        //     $ResultsF1['Amount'] = $get2['balance'];
        //     $ResultsF2[] = $ResultsF1;
        // }
        // return $ResultsF2;

    }

    public function headings(): array
    {
        return ['Member', 'Role', 'Balance', 'Request Date', 'Withdraw Date'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                //DEFAULT ROW HEIGHT AND WIDTH
                $event->sheet->getDelegate()->getDefaultRowDimension()->setRowHeight(30);
                $event->sheet->getDelegate()->getDefaultColumnDimension()->setWidth(30);

                //TEXT MIDDLE
                $event->sheet->getDelegate()->getParent()->getDefaultStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getParent()->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                //ROWS WIDTH
                //$event->sheet->getDelegate()->getColumnDimension('B')->setWidth(25); 
            },
        ];
    }
}
