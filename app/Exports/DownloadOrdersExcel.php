<?php

namespace App\Exports;

use App\User_order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Picqer;
use App\Order_status_changes_history;
use Maatwebsite\Excel\Concerns\WithDrawings;
Use \Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
ini_set('memory_limit', '-1');


class DownloadOrdersExcel implements FromCollection, WithEvents

{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct($order_id)
    {
        $this->Order_ID = $order_id;
    }

    public function collection()
    {   
        $Result = [];
        $SumFee = [];
        
        array_push($Result,['']);
        array_push($Result, [
            'رقم الشحنه',
            'اسم المرسل',
            'رقم المرسل',
            'عنوان المرسل',
            'اسم المستلم',
            'تليفون المستلم',
            'عنوان المستلم',
            'محافظة المستلم',
            'السعر الكلى',
            'محتوى البريد',
            'تاريخ انشاء الاوردر بدون الوقت',
            'حالة الاوردر',
            'تاريخ اخر التحديث',
            'اسم مستخدم اخر تحديث',
        ]);

        foreach ($this->Order_ID as $order) { 
            
            $last_order_status_date = Order_status_changes_history::where('order_id', $order->id)->latest('id')->first();

            $last_order_status_date_rs = ($last_order_status_date) 
                ? $last_order_status_date->created_at->format('Y-m-d') 
                : Carbon::parse($order->created_at)->format('Y-m-d');

            array_push($Result, [
                $order->track_code,
                $order->sender_full_name,
                $order->sender_phone_number,
                $order->location_from_state . " | " . $order->location_from_region,
                $order->receiver_full_name,
                $order->reciever_phone_number,
                $order->location_to_state . " | " . $order->location_to_region,
                $order->location_to_state,
                $order->recieved_price + $order->Deliver_Fee,
                $order->product_name,
                Carbon::parse($order->created_at)->format('Y-m-d'),
                $order->status,
                $last_order_status_date_rs,
                "يتوفر قريبا",
            ]);

            $SumFee[] = $order->recieved_price + $order->Deliver_Fee;
        }

        array_push($Result, [ '', '', '', '', '', '', '', array_sum($SumFee), '', '' ]);

        return collect($Result);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                //DEFAULT ROW HEIGHT AND WIDTH
                $event->sheet->getDelegate()->getDefaultRowDimension()->setRowHeight(30);
                $event->sheet->getDelegate()->getDefaultColumnDimension()->setWidth(20);

                //TEXT MIDDLE
                $event->sheet->getDelegate()->getParent()->getDefaultStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getParent()->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                //ROWS WIDTH
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(25);
            },
        ];
    }
}
