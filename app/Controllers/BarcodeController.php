<?php

namespace App\Controllers;

use App\Models\ProductModel;
use CodeIgniter\HTTP\ResponseInterface;
use FPDF;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Throwable;

class BarcodeController extends BaseController
{
    public function generate(): ResponseInterface
    {
        // Accept POST requests containing JSON.
        if ($this->request->getMethod() !== 'POST') {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Method not allowed']);
        }

        $data = $this->request->getJSON(true);

        if (!is_array($data) || !isset($data['number'])) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'A number is required']);
        }

        // Prefer sending the number as a string from the frontend.
        $number = $data['number'];

        if (!is_string($number) && !is_int($number)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Invalid number']);
        }

        $number = (string) $number;

        if (!preg_match('/^\d{10,13}$/', $number)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'error' => 'EAN-13 requires 12 or 13 digits'
                ]);
        }

        $ean13 = "";

        if ( strlen($number) === 13 ) {
            // Calculate the EAN-13 check digit.
            $base = substr($number, 0, 12);
            $sum = 0;

            for ($i = 0; $i < 12; $i++) {
                $digit = (int) $base[$i];

                $sum += ($i % 2 === 0)
                    ? $digit
                    : $digit * 3;
            }

            $checkDigit = (10 - ($sum % 10)) % 10;
            $ean13 = $base . $checkDigit;

            // Reject a 13-digit number with an invalid check digit.
            if ( $number !== $ean13) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'error' => 'Invalid EAN-13 check digit'
                    ]);
            }
        }

        $tempFile = null;

        try {
            // Generate the barcode PNG using Picqer v2.
            $generator = new BarcodeGeneratorPNG();

            if (strlen($number) === 13 )
                 $png = $generator->getBarcode( $ean13, $generator::TYPE_EAN_13, 3, 150 );
            else $png = $generator->getBarcode( $number, $generator::TYPE_CODE_128, 3, 150 );

            $tempFile = tempnam( sys_get_temp_dir(), 'barcode_' );

            if ($tempFile === false) {
                throw new \RuntimeException(
                    'Could not create temporary file'
                );
            }

            if (file_put_contents($tempFile, $png) === false) {
                throw new \RuntimeException(
                    'Could not save barcode image'
                );
            }

            $product = (new ProductModel())->where('barcode', $number)->first();
            $description = 'Product Description';
            
            if (is_array($product)) {
                $description = $product['description'] ?? $description;
            } elseif (is_object($product)) {
                $description = $product->description ?? $description;
            }


            // Create the PDF.
            // FPDF core fonts (Arial...) have no Greek glyphs, so we use DejaVu Sans
            // generated for cp1253 (app/ThirdParty/fpdf-fonts) and convert the UTF-8 text to cp1253.
            $pdf = new class('P', 'mm', 'A4') extends FPDF {
                public function __construct(...$args) {
                    parent::__construct(...$args);
                    $this->fontpath = APPPATH . 'ThirdParty/fpdf-fonts/';
                }
            };
            $pdf->AddFont('DejaVu', '',  'DejaVuSans.php');
            $pdf->AddFont('DejaVu', 'B', 'DejaVuSans-Bold.php');

            $pdf->SetMargins(15, 15, 15);
            $pdf->SetAutoPageBreak(false);
            $pdf->AddPage();

            $description = iconv('UTF-8', 'windows-1253//TRANSLIT', $description) ?: $description;

            $pdf->SetFont('DejaVu', 'B', 18);
            $pdf->Cell( 0, 12, $description, 0, 1, 'C');

            $pdf->SetFont('DejaVu', '', 14);

            $pdf->Cell(0, 10, strlen($number) === 13 ? $ean13 : $number, 0, 1, 'C');

            // Embed the barcode.
            $pdf->Image( $tempFile, 45, 55, 120, 0, 'PNG');

            $pdf->SetY(90);
            $pdf->SetFont('DejaVu', '', 12);
            $pdf->Cell( 0, 10, $ean13, 0, 1, 'C');

            // Generate PDF bytes in memory.
            $pdfBytes = $pdf->Output('S');

            return $this->response
                ->setStatusCode(200)
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader(
                    'Content-Disposition',
                    'inline; filename="barcode.pdf"'
                )
                ->setHeader('Cache-Control', 'no-store')
                ->setBody($pdfBytes);

        } catch (\Throwable $e) {
            log_message(
                'error',
                'Barcode PDF generation failed: {exception}',
                ['exception' => $e]
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'error' => 'Failed to generate PDF'
                ]);
        } finally {
            if (
                $tempFile !== null &&
                is_file($tempFile)
            ) {
                unlink($tempFile);
            }
        }
    }
}
