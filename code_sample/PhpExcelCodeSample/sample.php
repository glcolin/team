<?
// Starting the PHPExcel library
        $this->load->library('PHPExcel');
		$this->load->library('PHPExcel/IOFactory');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Camilla Name')
            ->setCellValue('B1', 'Status')
			->setCellValue('C1', 'Advertiser')
            ->setCellValue('D1', 'Campaign')
			->setCellValue('E1', 'Landing Page')
            ->setCellValue('F1', 'Visited');
		//Styling:
		$objPHPExcel->getActiveSheet()->getStyle("A1:F1")->getFont()->setBold(true);
		//Adjust columns width:
		foreach(range('A','F') as $columnID) {
		    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
		        ->setAutoSize(true);
		}
		//Loop through to write in excel file:
		$row = 2;
		if($data['reports']){
			foreach($data['reports'] as $report){
 				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $report->Name);
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $report->Status);
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $report->Advertiser);
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $report->Campaign);
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $report->LandingPage);
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $report->Joined);
				$row++;
			}
		}
		// We'll be outputting an excel file
		header('Content-type: application/vnd.ms-excel');
		// It will be called file.xls
		header('Content-Disposition: attachment; filename="analytics.xls"');
		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
		// Write file to the browser
		$objWriter->save('php://output');