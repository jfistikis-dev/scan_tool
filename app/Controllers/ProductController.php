<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\ProductLogModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class ProductController extends BaseController
{
    public function index(): string    {
        $data['title'] = "scan-ME :: Λίστα προϊόντων";
        return view('productsMainScreen', $data);
    }


    public function ajaxBarcodeSearch ( $barcode ) {

        $product = (new ProductModel())->getProductByBarcode( $barcode );
        
        if ( $product ) {
            $data[ 'data' ] = $product;
            $data[ 'log' ]  = (new ProductLogModel())
                    ->select('product_logs.*, measuring_units.name as measuring_unit_name')
                    ->join( "measuring_units", "measuring_units.id = product_logs.measuring_unit_id", "left" )
                    ->where( "product_id", $product[ 'id' ] )
                    ->orderBy( "id", "DESC" )
                    ->findAll( 15, 0 );
        } else {
            $data[ 'data' ] = null;
            $data[ 'log' ]  = null;
        }

        return $this->response->setJSON( $data );
        
    }
    
    public function ajaxList()
    {
        $request = service('request');
        $post = $request->getPost();

        $model = new ProductModel();

        // DataTables params
        $draw = intval($post['draw'] ?? 0);
        $start = intval($post['start'] ?? 0);
        $length = intval($post['length'] ?? 10);

        // Ordering
        $orderColIndex = $post['order'][0]['column'] ?? null;
        $orderDir = $post['order'][0]['dir'] ?? 'asc';
        $columns = $post['columns'] ?? [];

        // Global search for 'name' only (requirement)
        $searchValue = $post['search']['value'] ?? '';

        // Base query
        $builder = $model->builder();
        $builder->select('products.id, products.barcode, products.measuring_unit_id, products.description, products.stock, products.reorder_quantity, products.buying_price');
        $builder->select( 'measuring_units.name as measuring_unit');
        $builder->join('measuring_units', 'measuring_units.id = products.measuring_unit_id', 'left');
        

        // total records
        $recordsTotal = $builder->countAllResults(false); // don't reset

        // apply search: only name column per requirement
        if (!empty($searchValue)) {
            $builder->groupStart()
                    ->like('products.description', $searchValue)
                    ->orLike('barcode', $searchValue)
                    ->groupEnd();
        }

        // records after filtering
        $recordsFiltered = $builder->countAllResults(false);

        // apply ordering (map column index to column name)
        if ($orderColIndex !== null && isset($columns[$orderColIndex]['data'])) {
            $colName = $columns[$orderColIndex]['data'];
            // protect against invalid columns
            $allowed = ['id','barcode','measuring_unit_id','description','stock','reorder_quantity','buying_price'];
            if (in_array($colName, $allowed)) {
                $builder->orderBy($colName, $orderDir);
            }
        } else {
            $builder->orderBy('id', 'desc');
        }

        // limit
        if ($length != -1) {
            $builder->limit($length, $start);
        }

        $rows = $builder->get()->getResultArray();

        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                'id'                => $r['id'],
                'barcode'           => $r['barcode'],
                'measuring_unit'    => $r['measuring_unit'],
                'description'       => $r['description'],
                'stock'             => $r['stock'],
                'reorder_quantity'  => $r['reorder_quantity'],
                'buying_price'      => $r['buying_price'],
            ];
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function destroy( $id ){
        
        // delete log ... 
        (new ProductLogModel())->where('product_id', $id )->delete();
        
        // delete product..
        (new ProductModel())->delete($id);
        
        return $this->response->setJSON(['success' => 'To προϊόν διαγράφηκε με επιτυχία!']);
    }

    // Export current filtered results to Excel
    public function export()
    {
        $request = service('request');
        $search = $request->getGet('search') ?? '';

        $model = new ProductModel();
        $builder = $model->builder();
        $builder->select('products.id, products.barcode, products.measuring_unit_id, products.description, products.stock, products.reorder_quantity, products.buying_price, products.selling_price');
        $builder->select( 'measuring_units.name as measuring_unit');
        $builder->select( 'brands.name as category_name');
        
        $builder->join('measuring_units', 'measuring_units.id = products.measuring_unit_id', 'left');
        $builder->join('brands', 'brands.id = products.brand_id', 'left');
        

        if (!empty($search)) {
            $builder->like('name', $search);
        }

        $rows = $builder->orderBy('id','desc')->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // header
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Barcode');
        $sheet->setCellValue('C1', 'Περιγραφή');
        $sheet->setCellValue('D1', 'Κατηγορία');
        $sheet->setCellValue('E1', 'Στοκ');
        $sheet->setCellValue('F1', 'Μ.Μέτρησης');
        $sheet->setCellValue('G1', 'Ποσό αναπ/λιας');
        $sheet->setCellValue('H1', 'Τιμή αγοράς');
        $sheet->setCellValue('I1', 'Τιμή πώλησης');

        $rowNum = 2;
        foreach ($rows as $r) {
            $sheet->setCellValue("A{$rowNum}", $r['id']);
            $sheet->setCellValue("B{$rowNum}", $r['barcode']);
            $sheet->setCellValue("C{$rowNum}", $r['description']);
            $sheet->setCellValue("D{$rowNum}", $r['category_name']);
            $sheet->setCellValue("E{$rowNum}", $r['stock']);
            $sheet->setCellValue("F{$rowNum}", $r['measuring_unit']);
            $sheet->setCellValue("G{$rowNum}", $r['reorder_quantity']);
            $sheet->setCellValue("H{$rowNum}", $r['buying_price']);
            $sheet->setCellValue("I{$rowNum}", $r['selling_price']);
            $rowNum++;
        }

        $writer = new Xlsx($spreadsheet);

        $filename = 'people_export_' . date('Ymd_His') . '.xlsx';

        // send to browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    
    public function import()  {

        try {

            $validation = service('validation');
            $security   = service('security');
            
            // Get CSRF token from header or POST data
            $csrfConfig     = config('Security');
            $csrfName       = $csrfConfig->tokenName;
            $csrfToken      = $this->request->getPost($csrfName) ?? $this->request->getHeaderLine('X-CSRF-TOKEN');
            
            // Verify CSRF token
            if (!$csrfToken || !hash_equals($security->getHash(), $csrfToken)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid CSRF token'
                ])->setStatusCode(403);
            }
            
            if (!$this->request->isAJAX() ||  $this->request->getMethod() !== 'POST' ) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid request! This is not AJAX or POST request'
                ]);
            }
            
            // Validate file upload
            $validation->setRule('excel_file', 'Excel File', 'uploaded[excel_file]|ext_in[excel_file,xlsx,xls]|max_size[excel_file,10240]');
            
            if (!$validation->withRequest($this->request)->run()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => implode(' ', $validation->getErrors())
                ]);
            }
            
            $file = $this->request->getFile('excel_file');
            
            if (!$file->isValid() || $file->hasMoved()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid file upload'
                ]);
            }
            
            // Move file to writable directory
            $uploadPath = WRITEPATH . 'uploads/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $fileName = $file->getRandomName();
            $file->move($uploadPath, $fileName);
            $filePath = $uploadPath . $fileName;
            
            // Load spreadsheet
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Get data from Excel
            $data = $worksheet->toArray();
            
            $productModel = new ProductModel();
            $updated = 0;
            $errors = [];

            // A: ID, B: Barcode, C: Description, D: Category, E: Stock, F: Measuring Unit, G: Reorder Quantity, H: Buying Price, I: Selling Price            
            foreach ($data as $index => $row) {
                
                if ( !is_numeric($row[0]) ) { continue; } // skipping header
                
                // check that the barcode exists !! if not ... do not process
                $product = $productModel->where('barcode', $row[1])->first();
                if ( !$product ) { $errors[] = "Row " . ($index + 2) . `: Product with barcode $row[1] not found`; continue; }
                
                $data = [
                    'product_id'        => $product['id'],      // for security reasons ... we trust what's inside our DB rather than the excel file 
                    'barcode'           => $product['barcode'], //in terms of product id & barcode 
                    'quantity'          => 0,
                    'description'       => $row[2],
                    'stock'             => is_numeric($row[4]) ? number_format ($row[4], 2, '.', '') : 0,
                    'reorder_quantity'  => is_numeric($row[6]) ? number_format ($row[6], 2, '.', '') : 0,
                    'buying_price'      => is_numeric($row[7]) ? number_format ($row[7], 2, '.', '') : 0,
                    'selling_price'     => is_numeric($row[8]) ? number_format ($row[8], 2, '.', '') : 0,
                ];

                // save the product        
                (new ProductModel())->__saveProductRaw ( $data );
        
                // create a new product log
                (new ProductLogModel())->__saveProductLogRaw( $data, PRODUCT_LOG_TYPE_INVENTORY );
                
                $updated++;
            }
            
            // Clean up uploaded file
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            $message = "Επιτυχής ενημέρωση! <br>";
            $message .= "Ενημερώθηκαν $updated προϊόντα.";
            
            if (!empty($errors)) {
                $message .= "<br><br>Σφάλματα (" . count($errors) . "):<br>";
                $message .= implode("<br>", array_slice($errors, 0, 5)); // Show first 5 errors
                
                if (count($errors) > 5) {
                    $message .= "<br>... και άλλα " . (count($errors) - 5) . " σφάλματα";
                }
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'updated' => $updated,
                'errors' => $errors
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Σφάλμα: ' . $e->getMessage()
            ]);
        }
    

    }

}
