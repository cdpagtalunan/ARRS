<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
Use \Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\Exportable;

// use Maatwebsite\Excel\Concerns\RegistersEventListeners;


use PhpOffice\PhpSpreadsheet\Cell\DataType;


class Recon implements  FromView, WithTitle, WithEvents, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;

    protected $date;

    
    function __construct($date)
    {
        $this->date = $date;

        
    }

    public function view(): View {
       
            return view('exports.recon', ['date' => $this->date]);
        
	}

    public function title(): string
    {
        return 'Grinding Inventory';
    }

    //for designs
    public function registerEvents(): array
    {
        
        $style1 = array(
            'font' => array(
                'name'      =>  'Arial',
                'size'      =>  12,
                // 'color'      =>  'red',
                'italic'      =>  true
            )
        );
        return [
            AfterSheet::class => function(AfterSheet $event) use ($style1)  {
             
                    $event->sheet->setCellValue('A5', 'test');

                
            },
         
        ];
    }

 
}
