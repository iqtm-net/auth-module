<?php

namespace App\Exports;

use App\User_order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Picqer;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
ini_set('memory_limit', '-1');


class ClientStatistic implements FromCollection, WithEvents

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
            'اسم البريد',
            'اسم المرسل',
            'رقم هاتف المرسل',
            'عنوان المرسل',
            'اسم المستلم',
            'رقم هاتف المستلم',
            'عنوان المستلم',
            'قيمة البريد مع التوصيل',
            'رمز التتبع' ,
            'تاريخ الانشاء'
        ]);
        // var_dump($this->Order_ID);
        foreach ($this->Order_ID as $order) { 
            // echo $order;
            array_push($Result, [
                $order->product_name,
                $order->sender_full_name,
                $order->sender_phone_number,
                $order->location_from_state . " | " . $order->location_from_region,
                $order->receiver_full_name,
                $order->reciever_phone_number,
                $order->location_to_state . " | " . $order->location_to_region,
                $order->recieved_price + $order->Deliver_Fee,
                $order->track_code,
                $order->created_at,
            ]);

            $SumFee[] = $order->recieved_price + $order->Deliver_Fee;
        }

        array_push($Result, [ '', '', '', '', '', '', '', array_sum($SumFee), '', '' ]);

        return $Result;
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
