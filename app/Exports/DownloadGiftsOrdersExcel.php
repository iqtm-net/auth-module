<?php

namespace App\Exports;

use App\User_order;
use App\Offer;
use App\Offers_order;
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


class DownloadGiftsOrdersExcel implements FromCollection, WithEvents, WithDrawings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    
    public function __construct($branch_id)
    {
        $this->branch_id = $branch_id;

        $get = Offer::where('brache_id', $branch_id)->get();
        $Result = [];
        array_push($Result,['']);
        array_push($Result,['']);
        array_push($Result,['']);
        array_push($Result,['offer_title','sender_name','sender_phone','receiver_full_name','receiver_phone_number','receiver_address','receiver_gender']);

        foreach($get as $offer){ //Offers that match the branch_id

            $Orders = Offers_order::where('offer_id', $offer->id)->select('account_type', 'account_id', 'receiver_full_name','receiver_phone_number','receiver_address','receiver_gender')->get();
            foreach($Orders as $order){ //Orders That match the offer_id

                $Sender = table_byAccountType($order->account_type, $order->account_id);
                
                $order_f = [];
                $order_f['offer_title'] = $offer->product_name;
                $order_f['sender_name'] = $Sender->first_name." ".$Sender->last_name;
                $order_f['sender_phone'] = $Sender->phone_number;
                $order_f['receiver_full_name'] = $order->receiver_full_name;
                $order_f['receiver_phone_number'] = $order->receiver_phone_number;
                $order_f['receiver_address'] = $order->receiver_address;
                $order_f['receiver_gender'] = $order->receiver_gender;

                $Result[] = $order_f;

            }
        }

        $this->DATA = $Result;

    }

    public function drawings()
    {

        $Logo = new Drawing();
        $Logo->setName('BarCode');
        $Logo->setPath('images/hodhod_cover.png');
        $Logo->setHeight(123);
        $Logo->setCoordinates('D1');

        $Fi[] = $Logo;

        return $Fi;

    }

    public function collection()
    {
        $Result = $this->DATA;
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
