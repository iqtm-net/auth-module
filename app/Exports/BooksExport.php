<?php

namespace App\Exports;

use App\User_order;
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


class BooksExport implements FromCollection, WithEvents, WithDrawings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct($Fid, $Ftype, $Fstatus, $FDateFrom, $FDateTo)
    {
        $this->id = $Fid;
        $this->type = $Ftype;
        $this->status = $Fstatus;
        $this->DateFrom = $FDateFrom;
        $this->DateTo = $FDateTo;

        $Member = DB::table($Ftype)->where('id', $Fid)->first();

        if ($Ftype == "delivers" && $Member->GD){
            if ($Fstatus == 'waiting_unAccepted'){
                $get = User_order::
                whereNull('user_orders.deliver_id')
                    ->select('id', 'product_name', 'recieve_date', 'track_code', 'recieved_price', 'status', 'location_to_region', 'Deliver_Fee', 'App_Fee')
                    ->orderBy('created_at', 'ASC')
                    ->get();
            }
            else{
                $GetDelivers = Gd::
                where( function ($q) use($Fid) { return $q->where('deliver_id', $Fid)->orWhere('deliver_id', $Fid); })
                    ->join('delivers', 'delivers.phone_number', '=', 'gds.phone_numbers')
                    ->select('delivers.id')
                    ->get();

                $get = User_order::
                whereIn('user_orders.deliver_id', $GetDelivers)
                    ->when($Fstatus !== "All", function ($q) use($Fstatus) { return $q->where('status', $Fstatus); })
                    ->when($FDateFrom !== "All" && $FDateTo !== "All", function ($q) use($FDateFrom,$FDateTo) { return $q->whereBetween('created_at', [$FDateFrom, $FDateTo]); })
                    ->select('id', 'product_name', 'recieve_date', 'track_code', 'recieved_price', 'status', 'location_to_region', 'Deliver_Fee', 'App_Fee')
                    ->orderBy('created_at', 'ASC')
                    ->get();
            }

        }
        else{
            $get = User_order::
            when($Ftype == "delivers", function ($q) use($Fid) { return $q->where('handeled_by_id', $Fid )->select('id', 'product_name', 'recieve_date', 'track_code', 'recieved_price', 'status', 'location_to_region', 'Deliver_Fee', 'App_Fee'); })
                ->when($Ftype == "stores", function ($q) use($Fid) { return $q->where('user_id', $Fid )->where('account_type',3)->select('id', 'product_name', 'recieve_date', 'track_code', 'recieved_price', 'status', 'location_to_region'); })
                ->when($Ftype == "users", function ($q) use($Fid) { return $q->where('user_id', $Fid )->where('account_type',2)->select('id', 'product_name', 'recieve_date', 'track_code', 'recieved_price', 'status', 'location_to_region'); })
                ->when($Fstatus !== "All", function ($q) use($Fstatus) { return $q->where('status', $Fstatus); })
                ->when($FDateFrom !== "All" && $FDateTo !== "All", function ($q) use($FDateFrom,$FDateTo) { return $q->whereBetween('created_at', [$FDateFrom, $FDateTo]); })

                ->orderBy('created_at', 'ASC')
                ->get();
        }

        $this->DATA = $get;
    }

    public function drawings()
    {

        $get = $this->DATA;

        $Fi = [];

        Session::put('key', 2);
        Session::put('Logo', 1);

        foreach($get as $Order) {

            //LOGO
            $Logo = new Drawing();
            $Logo->setName('BarCode');
            $Logo->setDescription($Order->track_code);
            $Logo->setPath('images/hodhod_cover.png');
            $Logo->setHeight(80);

            Session::put('Logo', Session::get('Logo')+3);
            $Logo->setCoordinates('A' . Session::get('Logo'));

            $Fi[] = $Logo;

            //BARCODE
            $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
            file_put_contents('images/BarCodes/' . $Order->track_code . '.png', $generator->getBarcode($Order->track_code, $generator::TYPE_CODE_128));
            $drawing = new Drawing();
            $drawing->setName('BarCode');
            $drawing->setDescription($Order->track_code);
            $drawing->setPath('images/BarCodes/' . $Order->track_code . '.png');
            $drawing->setHeight(40);

            Session::put('key', Session::get('key')+3);
            $drawing->setCoordinates('K' . Session::get('key'));

            $Fi[] = $drawing;

        }

        return $Fi;

    }

    public function collection()
    {

        $get = $this->DATA;

        $Member = DB::table($this->type)->where('id', $this->id)->first();

        //HEADING
        $Result = [];

        if ($this->type == 'delivers') {
            array_push($Result,['','ألأسم الكامل', 'مجموع عمولة المندوب', 'مجموع قيم البريد', 'مجموع العمولة مع التوصيل', 'مجموع ارباح المنصة', 'تاريخ الكشف' ]);
            array_push($Result,['',$Member->first_name." ".$Member->last_name, $get->sum('Deliver_Fee'), $get->sum('recieved_price'), $get->sum('Deliver_Fee') + $get->sum('App_Fee') + $get->sum('recieved_price'), $get->sum('App_Fee'), Carbon::now() ]);
            }
        else{
            array_push($Result,['', 'ألأسم الكامل', 'مجموع الارباح', 'تاريخ الكشف' ]);
            array_push($Result,['', $Member->first_name." ".$Member->last_name, $get->sum('recieved_price'), Carbon::now() ]);
            }

        array_push($Result,['']);

        //LIST
        if ($get->count() > 0){
            foreach ($get as $key => $get1){

                    if ($this->type == 'delivers') {
                        array_push($Result,['', 'اسم البريد', 'تاريخ الاستلام', 'رمز التتبع', 'قيمة البريد', 'الحالة', 'المكان المرسل اليه البريد', 'عمولة المندوب', 'عمولة المنصة', 'اجمالي الرصيد',]);
                    }
                    else{
                        array_push($Result,[ '', 'اسم البريد', 'تاريخ الاستلام', 'رمز التتبع', 'قيمة البريد', 'الحالة', 'المكان المرسل اليه البريد']);
                    }
                    //echo var_dump($get1);

                    //array_unshift($get1);

                    $get1['id'] = '';
                    if ($this->type == 'delivers') { $get1['اجمالي الرصيد'] = $get1->Deliver_Fee + $get1->recieved_price; }
                    if ($get1->status == 'waiting') { $get1['status'] = 'تم القبول';}
                    if ($get1->status == 'pending') { $get1['status'] = 'قيد التوصيل';}
                    if ($get1->status == 'delivered') { $get1['status'] = 'تم التسليم';}
                    if ($get1->status == 'issued') { $get1['status'] = 'لم يتم التسليم';}

                    $Result[] = $get1;

                    array_push($Result,['']);
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
                $event->sheet->getDelegate()->getDefaultColumnDimension()->setWidth(14);

                //TEXT MIDDLE
                $event->sheet->getDelegate()->getParent()->getDefaultStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getParent()->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                //ROWS WIDTH
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(25);
                //$event->sheet->getDelegate()->getColumnDimension('D')->setWidth(8);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(34);
            },
        ];
    }

}
