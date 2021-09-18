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


class DownloadGiftsExcel implements FromCollection, WithEvents, WithDrawings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    
    public function __construct($branch_id)
    {
        $this->branch_id = $branch_id;

        $get = Offer::where('brache_id', $branch_id)->get();
        $Result1 = [
            [''], [''], [''],
            ['offer_title','gender','mark','color','size','sells']
        ];
        $Result2 = [];
        
        foreach($get as $offer){ //Offers that match the branch_id

            $sells = Offers_order::where('offer_id', $offer->id)->get()->count();
            
            $order_f = [];
            $order_f['offer_title'] = $offer->product_name;
            $order_f['gender'] = $offer->gender;
            $order_f['mark'] = $offer->mark;
            $order_f['color'] = $offer->color;
            $order_f['size'] = $offer->size;
            $order_f['sells'] = $sells;

            $Result2[] = $order_f;
        }

        array_multisort(array_column($Result2, 'sells'), SORT_DESC, $Result2);

        $Result = array_merge($Result1,$Result2);
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
