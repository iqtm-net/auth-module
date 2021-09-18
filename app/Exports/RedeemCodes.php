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


class RedeemCodes implements FromCollection, WithEvents, WithDrawings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    
    public function __construct($Codes)
    {

        $this->DATA = $Codes;

    }

    public function drawings()
    {

        $Logo = new Drawing();
        $Logo->setName('BarCode');
        $Logo->setPath('images/hodhod_cover.png');
        $Logo->setHeight(123);

        $Logo->setCoordinates('A1');

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
        
        //LIST
        foreach ($get as $get1){
            $Result[] = ['Points','Code']; //array_push($Result,['Points','Code']);
            $Result[] = $get1;
            $Result[] = [''];
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

                //TEXT MIDDLE
                $event->sheet->getDelegate()->getParent()->getDefaultStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getParent()->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                //Custome ROWS WIDTH
                // $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(25);
            },
        ];
    }

}
