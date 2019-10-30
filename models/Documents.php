<?php

// Make sure to install these packages for PDF functions to work:
// sudo apt-get install xfonts-base xfonts-75dpi urw-fonts

use Knp\Snappy\Pdf;

class Documents extends MVPHP {
	
	public function createPDF($html, $filename, $margin=[ 'top'=> '0mm', 'right' => '0mm', 'bottom' => '0mm', 'left' => '0mm' ], $orientation='portrait') {
		
		$pdf = new Pdf(APP_PATH.'/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
		
		$pdf->setOption('orientation', $orientation);
		$pdf->setOption('margin-top', $margin['top']);
		$pdf->setOption('margin-right', $margin['right']);
		$pdf->setOption('margin-bottom', $margin['bottom']);
		$pdf->setOption('margin-left', $margin['left']);
		
		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; filename="'.$filename.'.pdf"');
		
		echo $pdf->getOutput($html);
		
	}
	
}