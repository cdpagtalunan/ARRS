<?php

namespace App\Exports\Sheets;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
Use \Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;

// use Maatwebsite\Excel\Concerns\RegistersEventListeners;


use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ReconTemplate implements  FromView, WithTitle, WithEvents, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;

    protected $date;
    protected $recon_details;
    protected $recon_cat;
    protected $rec_to;
    protected $rec_from;

    
    function __construct($date, $recon_details, $recon_cat, $rec_to, $rec_from)
    {
        $this->date = $date;
        $this->recon_details = $recon_details;
        $this->recon_cat = $recon_cat;
        $this->rec_to = $rec_to;
        $this->rec_from = $rec_from;
    }

    public function view(): View {
       
            return view('exports.recon', ['date' => $this->date]);
        
	}

    public function title(): string
    {
        return $this->recon_cat['classification'].'-'.$this->recon_cat['department'];
    }


    //for designs
    public function registerEvents(): array
    {
        $center_center = array(
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        );
        $left_center = array(
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        );
        $bold  = array(
            'font' => array(
                'bold'      =>  true
            )
        );

        $styleBorderAll = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        return [
            AfterSheet::class => function(AfterSheet $event) use ($center_center, $bold, $styleBorderAll, $left_center)  {
                $recon = $this->recon_details;
                // json_decode($recon_details['DPI-PPSCN']['valid'], true)
         
                    $event->sheet->setCellValue('A1', 'Summary of Received Invoices');
                    $event->sheet->getDelegate()->mergeCells('A1:M1');
                    $event->sheet->getDelegate()->getStyle('A1:M1')->applyFromArray($center_center);
                    $event->sheet->getDelegate()->getStyle('A1')->getFont()->setSize(22);

                    $event->sheet->getColumnDimension('A')->setWidth(40);
                    $event->sheet->getColumnDimension('B')->setWidth(26);
                    $event->sheet->getColumnDimension('C')->setWidth(15);
                    $event->sheet->getColumnDimension('D')->setWidth(15);
                    $event->sheet->getColumnDimension('E')->setWidth(15);
                    $event->sheet->getColumnDimension('F')->setWidth(50);
                    $event->sheet->getColumnDimension('G')->setWidth(15);
                    $event->sheet->getColumnDimension('H')->setWidth(20);
                    $event->sheet->getColumnDimension('I')->setWidth(15);
                    $event->sheet->getColumnDimension('J')->setWidth(15);
                    $event->sheet->getColumnDimension('K')->setWidth(19);
                    $event->sheet->getColumnDimension('L')->setWidth(20);
                    $event->sheet->getColumnDimension('M')->setWidth(15);
                    // $event->sheet->getColumnDimension('M')->setWidth(3);
                    $event->sheet->getColumnDimension('N')->setWidth(3);
                    $event->sheet->getColumnDimension('O')->setWidth(45);
                    $event->sheet->getColumnDimension('P')->setWidth(25);

                    $event->sheet->setCellValue('A2', 'Month');
                    $event->sheet->setCellValue('B2', $this->rec_from. ' to '. $this->rec_to);
                    // $event->sheet->setCellValue('G2', 'In-charge');
                    // $event->sheet->getDelegate()->mergeCells('G2:H2');
                    // $event->sheet->getDelegate()->getStyle('G2')->applyFromArray($center_center);
                    // $event->sheet->setCellValue('L2', 'Date');
                    // $event->sheet->setCellValue('M2', 'Time');



                    $event->sheet->setCellValue('A3', 'Section');
                    $event->sheet->setCellValue('B3', $this->recon_cat['department']);

                    // $event->sheet->setCellValue('I3', 'Prepared by:');
                    
                    $event->sheet->setCellValue('A4', 'Classification Code');
                    $event->sheet->setCellValue('B4', $this->recon_cat['classification']);

                    // $event->sheet->setCellValue('I4', 'Checked by:');

                    $event->sheet->setCellValue('A6', 'Dollar Account (US$)');

                    $event->sheet->getDelegate()->getStyle('A1:A6')->applyFromArray($bold);
                    $event->sheet->getDelegate()->getStyle('I2:I4')->applyFromArray($bold);
                    $event->sheet->getDelegate()->getStyle('L2:M2')->applyFromArray($bold);

                    $event->sheet->setCellValue('A7', 'LOGISTICS-PURCHASING DATA (Extracted from EPRPO-Receiving Module)');
                    $event->sheet->getDelegate()->mergeCells('A7:H7');
                    $event->sheet->getDelegate()->getStyle('A7')->getFont()->setSize(12);
                    $event->sheet->getDelegate()->getStyle('A7')->applyFromArray($center_center);

                    // $event->sheet->setCellValue('J7', 'Reconciliation by End-User');
                    // $event->sheet->getDelegate()->mergeCells('J7:M7');
                    // $event->sheet->getDelegate()->getStyle('J7')->getFont()->setSize(12);
                    // $event->sheet->getDelegate()->getStyle('J7')->applyFromArray($center_center);

                    $event->sheet->setCellValue('O7', 'Summary per Supplier');
                    $event->sheet->getDelegate()->mergeCells('O7:P7');
                    $event->sheet->getDelegate()->getStyle('O7')->getFont()->setSize(12);
                    $event->sheet->getDelegate()->getStyle('O7')->applyFromArray($center_center);


                    $event->sheet->setCellValue('I7', 'Remarks');
                    $event->sheet->getDelegate()->mergeCells('I7:I8');
                    $event->sheet->getDelegate()->getStyle('I7')->applyFromArray($center_center);

                    $event->sheet->setCellValue('A8', 'Item Name');
                    $event->sheet->setCellValue('B8', 'Description');
                    $event->sheet->setCellValue('C8', 'Invoice No.');
                    $event->sheet->setCellValue('D8', 'Delivery Date');
                    $event->sheet->setCellValue('E8', 'Received Quantity');
                    $event->sheet->setCellValue('F8', 'Supplier Name');
                    $event->sheet->setCellValue('G8', 'Unit Price');
                    $event->sheet->setCellValue('H8', 'Amount');

                    $event->sheet->setCellValue('J7', 'PO Number.');
                    $event->sheet->getDelegate()->mergeCells('J7:J8');
                    $event->sheet->getDelegate()->getStyle('J7')->applyFromArray($center_center);

                    $event->sheet->setCellValue('K7', 'PR Number');
                    $event->sheet->getDelegate()->mergeCells('K7:K8');
                    $event->sheet->getDelegate()->getStyle('K7')->applyFromArray($center_center);

                    $event->sheet->setCellValue('L7', 'Allocation');
                    $event->sheet->getDelegate()->mergeCells('L7:L8');
                    $event->sheet->getDelegate()->getStyle('L7')->applyFromArray($center_center);

                    $event->sheet->setCellValue('M7', 'Result');
                    $event->sheet->getDelegate()->mergeCells('M7:M8');
                    $event->sheet->getDelegate()->getStyle('M7')->applyFromArray($center_center);

                    // $event->sheet->setCellValue('L8', 'Amount');
                    

                    $event->sheet->setCellValue('O8', 'Supplier');
                    $event->sheet->setCellValue('P8', 'Total Amount');
                    
                    
                    
                    $event->sheet->getDelegate()->getStyle('A8:M8')->getAlignment()->setWrapText(true);
                    $event->sheet->getDelegate()->getStyle('A8:P8')->applyFromArray($center_center);
                    
                    $start_row = 9;
                    
                    // if(in_array(0 , json_decode($recon['valid'], true))){
                    //     $event->sheet->setCellValue("A$start_row", "DATA STILL HAS PENDING FOR RECONCILIATION");
                    //     $event->sheet->getDelegate()->mergeCells("A$start_row:M$start_row");
                    //     $event->sheet->getDelegate()->getStyle("A$start_row:M$start_row")->applyFromArray($center_center);
                    //     $event->sheet->getDelegate()->getStyle("A$start_row")->applyFromArray($bold);


                        
                    // }
                    // else{
                        /*
                            * USD ACCOUNTS
                        */

                        for($x = 0; $x < count($recon['usd']['recons']); $x++){
                            $amount = 0;
                            $usd_recon = $recon['usd']['recons'][$x];
                            $event->sheet->setCellValue("A$start_row", $usd_recon->prod_name);
                            $event->sheet->setCellValue("B$start_row", $usd_recon->prod_desc);
                            $event->sheet->setCellValue("C$start_row", $usd_recon->invoice_no);
                            $event->sheet->setCellValue("D$start_row", $usd_recon->delivery_date);
                            $event->sheet->setCellValue("E$start_row", $usd_recon->received_qty);
                            $event->sheet->setCellValue("F$start_row", $usd_recon->supplier);
                            $event->sheet->setCellValue("G$start_row", $usd_recon->unit_price);
                            $amount = $usd_recon->unit_price * $usd_recon->received_qty;
                            $event->sheet->setCellValue("H$start_row", round($amount, 2), DataType::TYPE_NUMERIC);
                            $event->sheet->setCellValue("J$start_row", $usd_recon->po_num);
                            $event->sheet->setCellValue("K$start_row", $usd_recon->pr_num);
                            $event->sheet->setCellValue("L$start_row", $usd_recon->allocation);
                            $event->sheet->getDelegate()->getStyle("L$start_row")->getAlignment()->setWrapText(true);


                            // * Reconciliation by end user
                            // $event->sheet->setCellValue("J$start_row", $usd_recon->recon_invoice_no);
                            // $event->sheet->setCellValue("K$start_row", $usd_recon->recon_received_qty);
                            // $event->sheet->setCellValue("L$start_row", $usd_recon->recon_amount);
                            

                            // if($amount == $usd_recon->recon_amount){
                            if($usd_recon->recon_status == 1){
                                $event->sheet->setCellValue("M$start_row", "TRUE");
                            }
                            else{
                                $event->sheet->setCellValue("M$start_row", "FALSE");
                            }
                            $event->sheet->getDelegate()->getStyle("M$start_row")->applyFromArray($bold);

                            $start_row++;
                        }
                    
                        $start_row_supplier = 9;
                        for($z = 0; $z < count($recon['usd']['supplier']); $z++){
                            $usd_supplier = $recon['usd']['supplier'][$z];

                            $event->sheet->setCellValue("O$start_row_supplier", $usd_supplier);
                            $event->sheet->setCellValue("P$start_row_supplier", '=SUMIF(F9:F'.$start_row.', "'.$usd_supplier.'", H9:H'.$start_row.')');
                            $start_row_supplier++;
                        }
                        $event->sheet->setCellValue("O$start_row_supplier", "TOTAL");
                        $last_range = $start_row_supplier -1;
                        $event->sheet->setCellValue("P$start_row_supplier", '=SUM(P9:P'.$last_range.')');

                        $event->sheet->getDelegate()->getStyle("O7:P$start_row_supplier")->applyFromArray($styleBorderAll);
                        $event->sheet->getDelegate()->getStyle("O7:P$start_row_supplier")->applyFromArray($left_center);

                        $event->sheet->getDelegate()->getStyle("A7:M$start_row")->applyFromArray($styleBorderAll);
                        $event->sheet->getDelegate()->getStyle("A7:M$start_row")->applyFromArray($left_center);

                        /*
                            * PESO ACCOUNTS 
                        */
                        $start_row++;
                        $event->sheet->setCellValue("A$start_row", "Peso Account (PHP)");
                        $event->sheet->getDelegate()->getStyle("A$start_row")->applyFromArray($bold);
                        $start_row++;
                        $border_first_range = $start_row;
                        $event->sheet->setCellValue("A$start_row", 'LOGISTICS-PURCHASING DATA (Extracted from EPRPO-Receiving Module)');
                        $event->sheet->getDelegate()->mergeCells("A$start_row:H$start_row");
                        $event->sheet->getDelegate()->getStyle("A$start_row")->getFont()->setSize(12);
                        $event->sheet->getDelegate()->getStyle("A$start_row")->applyFromArray($center_center);

                        // $event->sheet->setCellValue("J$start_row", 'Reconciliation by End-User');
                        // $event->sheet->getDelegate()->mergeCells("J$start_row:M$start_row");
                        // $event->sheet->getDelegate()->getStyle("J$start_row")->getFont()->setSize(12);
                        // $event->sheet->getDelegate()->getStyle("J$start_row")->applyFromArray($center_center);

                        $event->sheet->setCellValue("O$start_row", 'Summary per Supplier');
                        $event->sheet->getDelegate()->mergeCells("O$start_row:P$start_row");
                        $event->sheet->getDelegate()->getStyle("O$start_row")->getFont()->setSize(12);
                        $event->sheet->getDelegate()->getStyle("O$start_row")->applyFromArray($center_center);


                        $event->sheet->setCellValue("I$start_row", 'Remarks');
                        $event->sheet->getDelegate()->getStyle("I$start_row")->applyFromArray($center_center);
                        $startrow1 = $start_row + 1;
                        $event->sheet->getDelegate()->mergeCells("I$start_row:I$startrow1");

                        $event->sheet->setCellValue("J$start_row", 'PO Number');
                        $event->sheet->getDelegate()->getStyle("J$start_row")->applyFromArray($center_center);
                        // $startrow2 = $start_row + 1;
                        $event->sheet->getDelegate()->mergeCells("J$start_row:J$startrow1");

                        $event->sheet->setCellValue("K$start_row", 'PR Number');
                        $event->sheet->getDelegate()->getStyle("K$start_row")->applyFromArray($center_center);
                        // $startrow2 = $start_row + 1;
                        $event->sheet->getDelegate()->mergeCells("K$start_row:K$startrow1");

                        $event->sheet->setCellValue("L$start_row", 'Allocation');
                        $event->sheet->getDelegate()->getStyle("L$start_row")->applyFromArray($center_center);
                        // $startrow2 = $start_row + 1;
                        $event->sheet->getDelegate()->mergeCells("L$start_row:L$startrow1");

                        $event->sheet->setCellValue("M$start_row", 'Result');
                        $event->sheet->getDelegate()->getStyle("M$start_row")->applyFromArray($center_center);
                        // $startrow2 = $start_row + 1;
                        $event->sheet->getDelegate()->mergeCells("M$start_row:M$startrow1");

                        $start_row++;

                        $event->sheet->setCellValue("A$start_row", 'Item Name');
                        $event->sheet->setCellValue("B$start_row", 'Description');
                        $event->sheet->setCellValue("C$start_row", 'Invoice No.');
                        $event->sheet->setCellValue("D$start_row", 'Delivery Date');
                        $event->sheet->setCellValue("E$start_row", 'Received Quantity');
                        $event->sheet->setCellValue("F$start_row", 'Supplier Name');
                        $event->sheet->setCellValue("G$start_row", 'Unit Price');
                        $event->sheet->setCellValue("H$start_row", 'Amount');

                        // $event->sheet->setCellValue("J$start_row", 'Invoice No.');
                        // $event->sheet->setCellValue("K$start_row", 'Received Quantity');
                        // $event->sheet->setCellValue("L$start_row", 'Amount');
                       


                        $event->sheet->setCellValue("O$start_row", 'Supplier');
                        $event->sheet->setCellValue("P$start_row", 'Total Amount');

                        
                        
                        $event->sheet->getDelegate()->getStyle("A$start_row:P$start_row")->getAlignment()->setWrapText(true);
                        $event->sheet->getDelegate()->getStyle("A$start_row:P$start_row")->applyFromArray($center_center);

                        $start_row++;
                        $start_row_supplier_php = $start_row;
                        $summary_supplier_first_range = $start_row;
                        for($y = 0; $y < count($recon['php']['recons']); $y++){
                            $amount = 0;
                            $php_recon = $recon['php']['recons'][$y];
                            $event->sheet->setCellValue("A$start_row", $php_recon->prod_name);
                            $event->sheet->setCellValue("B$start_row", $php_recon->prod_desc);
                            $event->sheet->setCellValue("C$start_row", $php_recon->invoice_no);
                            $event->sheet->setCellValue("D$start_row", $php_recon->delivery_date);
                            $event->sheet->setCellValue("E$start_row", $php_recon->received_qty);
                            $event->sheet->setCellValue("F$start_row", $php_recon->supplier);
                            $event->sheet->setCellValue("G$start_row", $php_recon->unit_price);
                            $amount = $php_recon->unit_price * $php_recon->received_qty;
                            $event->sheet->setCellValue("H$start_row", round($amount, 2), DataType::TYPE_NUMERIC);

                            // * Reconciliation by end user
                            // $event->sheet->setCellValue("J$start_row", $php_recon->recon_invoice_no);
                            // $event->sheet->setCellValue("K$start_row", $php_recon->recon_received_qty);
                            // $event->sheet->setCellValue("L$start_row", round($php_recon->recon_amount, 2) );
                            $event->sheet->setCellValue("J$start_row", $php_recon->po_num);
                            $event->sheet->setCellValue("K$start_row", $php_recon->pr_num);
                            $event->sheet->setCellValue("L$start_row", $php_recon->allocation);
                            $event->sheet->getDelegate()->getStyle("L$start_row")->getAlignment()->setWrapText(true);


                            if($php_recon->recon_status == 1){
                            // if($amount == $php_recon->recon_amount){
                                $event->sheet->setCellValue("M$start_row", "TRUE");
                            }
                            else{
                                $event->sheet->setCellValue("M$start_row", "FALSE");
                            }
                            $event->sheet->getDelegate()->getStyle("M$start_row")->applyFromArray($bold);

                            $start_row++;


                        }
                        for($w = 0; $w < count($recon['php']['supplier']); $w++){
                            $php_supplier = $recon['php']['supplier'][$w];

                            $event->sheet->setCellValue("O$start_row_supplier_php", $php_supplier);
                            $event->sheet->setCellValue("P$start_row_supplier_php", '=SUMIF(F'.$start_row.':F'.$summary_supplier_first_range.', "'.$php_supplier.'", H'.$summary_supplier_first_range.':H'.$start_row.')');
                            $start_row_supplier_php++;
                        }
                        $event->sheet->setCellValue("O$start_row_supplier_php", "TOTAL");
                        $last_range1 = $start_row_supplier_php - 1;
                        $event->sheet->setCellValue("P$start_row_supplier_php", '=SUM(P'.$summary_supplier_first_range.':P'.$last_range1.')');

                        $event->sheet->getDelegate()->getStyle("A$border_first_range:M$start_row")->applyFromArray($styleBorderAll);
                        $event->sheet->getDelegate()->getStyle("A$border_first_range:M$start_row")->applyFromArray($left_center);
                        $event->sheet->getDelegate()->getStyle("O$border_first_range:P$start_row_supplier_php")->applyFromArray($styleBorderAll);
                        $event->sheet->getDelegate()->getStyle("O$border_first_range:P$start_row_supplier_php")->applyFromArray($left_center);
    
                    // }    


            },

         
        ];
    }

 
}
