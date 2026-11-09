<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StockOutLogModel;
use App\Models\ProductModel;
use App\Models\ProductLogModel;

class SalesController extends BaseController
{
	public function index(): string   {
        $data['title'] = "scan-ME :: Ταμείο / Πωλήσεις προϊόντων";
        $productModel       = new ProductModel();

        $data['products'] = $productModel->select('*, description AS name')->findAll();
        
	    return view('salesMainScreen', $data);
	}


    public function ajaxNewSalesEntry() {

        $barcode    = $this->request->getVar( "barcode" );
        $product    = (new ProductModel())->getProductByBarcode( trim($barcode) );
        
        return !$product ? 
                $this->response->setJSON( [ 
                    'data'   => null,
                    'log'    => null,
                    'html'   => '' 
                ] ): 
                $this->response->setJSON( [ 
                    'data'   => $product,
                    'html'   => view('components/sales_item_entry', [ 'item' => $product ] ), 
                    'log'    => (new ProductLogModel())
                                        ->select('product_logs.*, measuring_units.name as measuring_unit_name')
                                        ->join( "measuring_units", "measuring_units.id = product_logs.measuring_unit_id", "left" )
                                        ->where( "product_id", $product[ 'id' ] )
                                        ->orderBy( "id", "DESC" )
                                        ->findAll( 15, 0 )
                ] );


    }

	public function store () {

        // The basket is sent as a JSON body: { products: [ {id, quantity, price}, ... ] }
        // (JSON is not subject to PHP's max_input_vars limit, unlike form-encoded posts)
        $basket_products = $this->request->getJSON(true)['products'] ?? null;

        if ( !is_array($basket_products) || count($basket_products) < 1 ) {
            return $this->response->setJSON( ['error' => true, 'message' => 'Το καλάθι είναι άδειο.'] );
        }

        $productModel       = new ProductModel();
        $productLogModel    = new ProductLogModel();
        $group_uid          = date('Ymd_His') . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 5);

        $db = \Config\Database::connect();
        $db->transBegin();

        // Updating product's stock && create a sales history.
        // Stock / brand / supplier / unit are read from the DB, not from the browser,
        // so a stale page can't overwrite a newer stock value.
        foreach ($basket_products as $item ) {
            $product = $productModel->find( (int) ($item['id'] ?? 0) );

            if ( $product === null ) {
                $db->transRollback();
                return $this->response->setJSON( ['error' => true, 'message' => 'Το προϊόν δεν βρέθηκε στην βάση. Η πώληση δεν καταχωρήθηκε.'] );
            }

            $quantity  = (string) (float) ($item['quantity'] ?? 0);
            $old_stock = (string) $product['stock'];
            $new_stock = bcsub( $old_stock, $quantity, 2 );

            $productModel->update( $product['id'], [ 'stock' => $new_stock ] );

            $productLogModel->insert([
                'type_id'           => PRODUCT_LOG_TYPE_SELLING,
                'measuring_unit_id' => $product['measuring_unit_id'],
                'brand_id'          => $product['brand_id'],
                'supplier_id'       => $product['supplier_id'],
                "product_id"        => $product['id'],
                "quantity"          => $quantity,
                "selling_price"     => (float) ($item['price'] ?? 0),
                "old_stock"         => $old_stock,
                "new_stock"         => $new_stock,
                "group_uid"         => $group_uid
            ]);
        }

        if ( $db->transStatus() === false ) {
            $db->transRollback();
            return $this->response->setJSON( ['error' => true, 'message' => lang('Notification.title_error')] );
        }

        $db->transCommit();
        return  $this->response->setJSON( [ 'success' => lang('Notification.title_save_success') ] );

    }
}
