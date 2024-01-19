<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use App\Exports\Sheets\ReconTemplate;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
Use \Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\WithEvents;
// use Maatwebsite\Excel\Concerns\WithDrawings;
// use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;




class Recon implements WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;

    protected $date;
    protected $recon_details;
    protected $user_cat;
    protected $rec_to;
    protected $rec_from;

    
    function __construct($date, $recon_details, $user_cat, $rec_to, $rec_from)
    {
        $this->date = $date;
        $this->recon_details = $recon_details;
        $this->user_cat = $user_cat;
        $this->rec_to = $rec_to;
        $this->rec_from = $rec_from;
    }

    //for multiple sheets
    public function sheets(): array
    {
        $sheets = [];
        for($y = 0; $y < count($this->user_cat); $y++){
            // for($x = 0; $x < count($this->recon_details); $x++){
                $sheets[] = new ReconTemplate($this->date, $this->recon_details[$this->user_cat[$y]['classification'].'-'.$this->user_cat[$y]['department']], $this->user_cat[$y], $this->rec_to, $this->rec_from);
            // }
        }
       
        // $sheets[] = new PMIRecordsSheet($this->date, $this->subcon_employees, 'SUBCON EMPLOYEES', 1);
    	// $sheets[] = new PMIRecordsPerSectionSheet($this->date, $this->subcon_employees_per_section, 'SUBCON EMPLOYEES PER SECTION', 1);
        // $sheets[] = new PMISuspectedEmployeesSheet($this->date, $this->suspected_employees, 'COVID-19 SUSPECTED EMPLOYEES');
    	// $sheets[] = new PMIPresentEmployeesWithoutCHASSheet($this->date, $this->present_employees_without_chas, 'PRESENT EMPLOYEES WITHOUT CHAS');
    	
        return $sheets;
    }



    // public function view(): View {
       
    //     return view('exports.pending_preshipment', ['date' => $this->date, 'pending_preshipments' => $this->pending_preshipment]);
    
    // }

    // public function title(): string
    // {
    //     return 'Pending Preshipment';
    // }

    // public function registerEvents(): array
    // {
    //     return [
    //         AfterSheet::class => function(AfterSheet $event) {
              
    //         },
    //     ];
    // }
}
