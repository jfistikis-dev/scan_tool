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


    public function ajaxBarcodeSearch () {
        
        $product = (new ProductModel())->getProductByBarcode( (string) $this->request->getGet('barcode') );
        
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
    
    public function ajaxList()  {
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
            } else {
                $builder->orderBy('id', 'desc');
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
    public function export() {
        
        $request = service('request');
        $search = $request->getGet('search') ?? '';

        $model = new ProductModel();
        $builder = $model->builder();
        $builder->select('products.id, products.barcode, products.measuring_unit_id, products.description, products.stock, products.reorder_quantity, products.buying_price, products.selling_price');
        $builder->select( 'measuring_units.name as measuring_unit');
        $builder->select( 'brands.name as category_name');
        
        $builder->join('measuring_units', 'measuring_units.id = products.measuring_unit_id', 'left');
        $builder->join('brands', 'brands.id = products.brand_id', 'left');
        

        // same filter as ajaxList
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('products.description', $search)
                    ->orLike('barcode', $search)
                    ->groupEnd();
        }

        // same ordering as the table
        $orderCol = $request->getGet('order_column');
        $orderDir = strtolower($request->getGet('order_dir') ?? '') === 'asc' ? 'asc' : 'desc';
        $allowed  = ['id','barcode','measuring_unit_id','description','stock','reorder_quantity','buying_price'];
        if (!in_array($orderCol, $allowed, true)) {
            $orderCol = 'id';
        }

        $rows = $builder->orderBy('products.' . $orderCol, $orderDir)->get()->getResultArray();

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
        $sheet->setCellValue('J1', 'Κόστος Αποθ/τος');


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
             $sheet->setCellValue("J{$rowNum}", number_format( $r['selling_price'] * $r['stock'], 2, '.', ',') );
            $rowNum++;
        }

        $writer = new Xlsx($spreadsheet);

        $filename = 'product_export_' . date('Ymd_His') . '.xlsx';

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

    public function ajaxProductList () {

        $model = new ProductModel();
        
        $get = $this->request->getGet();
        $first_key = array_key_first($get);
        $first_value = $get[$first_key] ?? null;
        
        $items =  $model->findAll();
        if ( $first_value) {
            $items =  $model->like('description',  $first_value)->OrLike('barcode', $first_value)->findAll();
        }
              

        $data = [];
        foreach ($items as $r) {
            $data[] = [
                'id'                => $r['id'],
                'name'              => $r['description'],
                'barcode'           => $r['barcode'],
              
                'description'       => $r['description'],
                'stock'             => $r['stock'],
                'reorder_quantity'  => $r['reorder_quantity'],
                'buying_price'      => $r['buying_price'],
            ];
        }

        return $this->response->setJSON( $data ); 
        
    }

    
    public function ajaxListByDate() {

        $request = $this->request;

        $draw   = (int) $request->getPost('draw');
        $start  = max(0, (int) $request->getPost('start'));
        $length = (int) $request->getPost('length');

        $length = $length < 1 ? 10 : min($length, 500);

        [$filteredQuery, $recordsTotal, $recordsFiltered] = $this->buildDateLogQuery(
            $request->getPost('from_date'),
            $request->getPost('to_date'),
            filter_var($request->getPost('groupByProduct'), FILTER_VALIDATE_BOOLEAN),
            (int) ($request->getPost('order')[0]['column'] ?? 1),
            $request->getPost('order')[0]['dir'] ?? 'DESC'
        );

        $rows = $filteredQuery
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => (int) $recordsFiltered,
            'data'            => $rows,
        ]);
    }

    // Export the date-filtered table (same criteria, grouping and ordering) to Excel
    public function exportByDate() {

        $request = $this->request;

        $groupByProduct = filter_var($request->getGet('groupByProduct'), FILTER_VALIDATE_BOOLEAN);

        [$query] = $this->buildDateLogQuery(
            $request->getGet('from_date'),
            $request->getGet('to_date'),
            $groupByProduct,
            (int) ($request->getGet('order_column') ?? 1),
            $request->getGet('order_dir') ?? 'DESC'
        );

        $rows = $query->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // header : same columns as #dateProductTable
        $headers = ['ID', 'Ημερομηνία', 'Προϊόν', 'Αρχικό απόθεμα', 'Ποσότητα', 'Τελικό απόθεμα', 'Τιμή μονάδας', 'Αξία συναλλαγής', 'Τύπος'];
        $sheet->fromArray($headers, null, 'A1');

        $rowNum = 2;
        foreach ($rows as $r) {

            if ($groupByProduct) {
                $quantity  = (float) $r['quantity'];   // already signed (net)
                $unitPrice = '';
                $type      = '+' . (float) $r['quantity_in'] . ' / -' . (float) $r['quantity_out'];
            } else {
                $sign      = (int) $r['type_id'] === PRODUCT_LOG_TYPE_SELLING ? -1 : 1;
                $quantity  = $sign * (float) $r['quantity'];
                $unitPrice = $r['unit_price'];
                $type      = match ((int) $r['type_id']) {
                    PRODUCT_LOG_TYPE_BUYING  => 'Προσθήκη',
                    PRODUCT_LOG_TYPE_SELLING => 'Αφαίρεση',
                    PRODUCT_LOG_TYPE_UPDATE  => 'Ενημέρωση',
                    default                  => 'Άλλο',
                };
            }

            $createdAt = $r['created_at'] ? date('d-m-Y H:i:s', strtotime($r['created_at'])) : '';

            $sheet->setCellValue("A{$rowNum}", $r['id']);
            $sheet->setCellValue("B{$rowNum}", $createdAt);
            $sheet->setCellValue("C{$rowNum}", $r['description']);
            $sheet->setCellValue("D{$rowNum}", $r['old_stock']);
            $sheet->setCellValue("E{$rowNum}", $quantity);
            $sheet->setCellValue("F{$rowNum}", $r['new_stock']);
            $sheet->setCellValue("G{$rowNum}", $unitPrice);
            $sheet->setCellValue("H{$rowNum}", $r['transaction_value']);
            $sheet->setCellValue("I{$rowNum}", $type);
            $rowNum++;
        }

        $writer = new Xlsx($spreadsheet);

        $filename = 'product_log_export_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Shared query for the date tab, used by both the table (ajaxListByDate) and the Excel export (exportByDate)
     * so they always return the same rows in the same order.
     *
     * Returns [ordered builder without limit, recordsTotal, recordsFiltered].
     */
    private function buildDateLogQuery(?string $fromInput, ?string $toInput, bool $groupByProduct, int $orderIndex, string $orderDir): array {

        // Dates arrive as dd-mm-yyyy from the datepicker.
        $parseDate = static function ($value): ?\DateTime {
            $date = $value ? \DateTime::createFromFormat('!d-m-Y', $value) : false;
            return $date ?: null;
        };

        $fromDate = $parseDate($fromInput);
        $toDate   = $parseDate($toInput);

        // Include the whole final day: created_at < (to_date + 1 day).
        $fromDate = $fromDate?->format('Y-m-d 00:00:00');
        $toDate   = $toDate?->modify('+1 day')->format('Y-m-d 00:00:00');

        $db = \Config\Database::connect();
        $transactionTable = 'product_logs';

        $buying  = PRODUCT_LOG_TYPE_BUYING;
        $selling = PRODUCT_LOG_TYPE_SELLING;

        $unitPriceSql = "
            CASE
                WHEN t.type_id = {$buying}  THEN t.buying_price
                WHEN t.type_id = {$selling} THEN t.selling_price
                ELSE t.buying_price
            END
        ";

        // Build the base query.
        $buildQuery = function () use ($db, $transactionTable, $fromDate, $toDate) {
            $builder = $db->table($transactionTable . ' t')
                ->join('products p', 'p.id = t.product_id', 'left');

            if ($fromDate && $toDate) {
                $builder->where('t.created_at >=', $fromDate)
                        ->where('t.created_at <', $toDate);
            }

            return $builder;
        };

        // Total transaction records before date filtering.
        $recordsTotal = $db->table($transactionTable)->countAllResults();

        // Prepare the filtered query.
        $filteredQuery = $buildQuery();

        if ($groupByProduct) {
            // Quantities are stored as positive numbers for both types,
            // so additions (type 1) count as + and subtractions (type 2) as -.
            $signSql = "CASE WHEN t.type_id = {$selling} THEN -1 ELSE 1 END";

            // Stock before the first / after the last movement of the period.
            $periodSql = '';
            if ($fromDate && $toDate) {
                $periodSql = ' AND l.created_at >= ' . $db->escape($fromDate)
                           . ' AND l.created_at < '  . $db->escape($toDate);
            }
            $stockAtSql = static fn (string $column, string $dir) => "(
                SELECT l.{$column}
                FROM {$transactionTable} l
                WHERE l.product_id = t.product_id
                  AND l.type_id IN ({$buying}, {$selling}){$periodSql}
                ORDER BY l.id {$dir}
                LIMIT 1
            )";

            // One row per product, with quantities netted over the period.
            // Field names match the transaction-level rows so the same table can render them.
            $filteredQuery
                ->select("
                    t.product_id AS id,
                    t.product_id,
                    p.description,
                    NULL AS type_id,
                    1 AS is_grouped,
                    COUNT(*) AS transactions,
                    MIN(t.created_at) AS first_transaction,
                    MAX(t.created_at) AS created_at,
                    SUM(CASE WHEN t.type_id = {$buying}  THEN t.quantity ELSE 0 END) AS quantity_in,
                    SUM(CASE WHEN t.type_id = {$selling} THEN t.quantity ELSE 0 END) AS quantity_out,
                    SUM(({$signSql}) * t.quantity) AS quantity,
                    SUM(({$signSql}) * t.quantity * ({$unitPriceSql})) AS transaction_value,
                    NULL AS unit_price,
                    {$stockAtSql('old_stock', 'ASC')} AS old_stock,
                    {$stockAtSql('new_stock', 'DESC')} AS new_stock
                ", false)
                ->whereIn('t.type_id', [$buying, $selling])
                ->groupBy(['t.product_id', 'p.description']);

            // Count products represented in the selected period.
            $recordsFiltered = $buildQuery()
                ->select('COUNT(DISTINCT t.product_id) AS cnt', false)
                ->whereIn('t.type_id', [$buying, $selling])
                ->get()
                ->getRow()
                ->cnt ?? 0;
        } else {
            // Original transaction-level event log.
            $filteredQuery->select("
                t.id,
                t.type_id,
                t.product_id,
                t.quantity,
                t.buying_price,
                t.selling_price,
                t.old_stock,
                t.new_stock,
                t.created_at,
                p.description,
                ($unitPriceSql) AS unit_price,
                (t.quantity * ($unitPriceSql)) AS transaction_value
            ", false);

            $recordsFiltered = $buildQuery()->countAllResults();
        }

        // Sorting: indexes follow the columns of #dateProductTable.
        $orderColumns = $groupByProduct
            ? [
                0 => 't.product_id',
                1 => 'created_at',
                2 => 'p.description',
                3 => 'old_stock',
                4 => 'quantity',
                5 => 'new_stock',
                7 => 'transaction_value',
            ]
            : [
                0 => 't.id',
                1 => 't.created_at',
                2 => 'p.description',
                3 => 't.old_stock',
                4 => 't.quantity',
                5 => 't.new_stock',
                6 => 'unit_price',
                7 => 'transaction_value',
                8 => 't.type_id',
            ];

        $orderBy   = $orderColumns[$orderIndex] ?? $orderColumns[1];
        $direction = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

        $filteredQuery->orderBy($orderBy, $direction, false);

        return [$filteredQuery, $recordsTotal, $recordsFiltered];
    }


}
