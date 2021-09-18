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


class DownloadOrderExcel implements FromCollection, WithEvents
    //, WithDrawings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct($order_id)
    {
        $this->Order_ID = $order_id;
    }

    /*public function drawings()
    {
        $Fi = [];

        //List

        $Order = User_order::find($this->Order_ID);

        //LOGO
        $Logo = new Drawing();
        $Logo->setName('Logo');
        $Logo->setDescription($Order->track_code);
        $Logo->setPath('images/hodhod_cover.png');
        $Logo->setHeight(80);
        $Logo->setCoordinates('A2');

        $Fi[] = $Logo;

        //BARCODE
        $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
        file_put_contents('images/BarCodes/' . $Order->track_code . '.png', $generator->getBarcode($Order->track_code, $generator::TYPE_CODE_128));
        $drawing = new Drawing();
        $drawing->setName('BarCode');
        $drawing->setDescription($Order->track_code);
        $drawing->setPath('images/BarCodes/' . $Order->track_code . '.png');
        $drawing->setHeight(40);
        $drawing->setCoordinates('G3');

        $Fi[] = $drawing;

        return $Fi;

    }*/

    public function collection()
    {
        $Result = [];

        array_push($Result,['']);

        $orders =  explode(',', $this->Order_ID);

        foreach ($orders as $order) {

            $order_data = User_order::where('track_code', $order)->first();

            array_push($Result, ['اسم البريد', 'اسم المستلم', 'رقم هاتف المستلم', 'عنوان المستلم', 'قيمة البريد مع التوصيل', 'رمز التتبع', 'تاريخ الانشاء']);

            array_push($Result, [
                $order_data->product_name,
                $order_data->receiver_full_name,
                $order_data->reciever_phone_number,
                $order_data->location_to_state . " | " . $order_data->location_to_region,
                $order_data->recieved_price + $order_data->Deliver_Fee,
                $order_data->track_code,
                $order_data->created_at,
            ]);

            array_push($Result, ['']);
        }

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
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(34);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(25);
            },
        ];
    }
}
