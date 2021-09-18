<?php

namespace App\Exports;

use App\User_order;
use App\Member_stack;
use App\Deliver_stack;
use App\Gd;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
Use \Carbon\Carbon;
use Picqer;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Facades\Session;
ini_set('memory_limit', '-1');


class Member_Stack_orders implements FromCollection, WithEvents, WithDrawings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    
    public function __construct($Ftype, $Fid)
    {
        $this->id = $Fid;
        $this->type = $Ftype;

        if($Ftype == 4){ // If Deliver
            $get_stack = Deliver_stack::where('deliver_stacks.deliver_id', $Fid)
            ->where('deliver_stacks.popped', 0)
            ->join('user_orders', 'user_orders.id', '=', 'deliver_stacks.order_id')
            ->select('product_name','receiver_full_name','reciever_phone_number','location_to_state','location_to_region','recieved_price', 'track_code', 'Deliver_Fee')
            ->get(); 
        }
        else{ // If Member
            $get_stack = Member_stack::where('member_stacks.member_id', $Fid)
            ->where('member_stacks.popped', 0)
            ->where('member_stacks.account_type', $Ftype)
            ->join('user_orders', 'user_orders.id', '=', 'member_stacks.order_id')
            ->select('product_name','receiver_full_name','reciever_phone_number','location_to_state','location_to_region','recieved_price', 'track_code', 'Deliver_Fee')
            ->get();
        }

        $this->DATA = $get_stack;

    }

    public function drawings()
    {

        $Logo = new Drawing();
        $Logo->setName('BarCode');
        $Logo->setPath('/images/hodhod_cover.png');
        $Logo->setHeight(123);
        $Logo->setCoordinates('D1');

        $Fi[] = $Logo;

        return $Fi;

    }

    public function collection()
    {

        $get = $this->DATA;
 
        // HEADING
        $Result = [];
        array_push($Result,['']);
        array_push($Result,['']);
        array_push($Result,['']);
        array_push($Result,['Product','Coustomer Name','Coustomer Phone','State','Region','Price With Delivery','Track Number','Total Prices']);

        //LIST
        if ($get->count() > 0){
            foreach ($get as $key => $get1){
                $get1['recieved_price'] = $get1->recieved_price + $get1->Deliver_Fee;
                $get1['Total Prices'] = ($key == 0) ? $get->sum('recieved_price') : null;
                unset($get1['Deliver_Fee']);
                $Result[] = $get1;
            }
        }

        return $Result;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                //DEFAULT ROW HEIGHT AND WIDTH
                $event->sheet->getDelegate()->getDefaultRowDimension()->setRowHeight(30);
                $event->sheet->getDelegate()->getDefaultColumnDimension()->setWidth(20);

                // //TEXT MIDDLE
                $event->sheet->getDelegate()->getParent()->getDefaultStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getParent()->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // //ROWS WIDTH
                // $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(25);
                // $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(25);
                // //$event->sheet->getDelegate()->getColumnDimension('D')->setWidth(8);
                // $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(40);
                // $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(34);
            },
        ];
    }

}
