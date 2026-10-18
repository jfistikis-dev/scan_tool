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
        
        $validation         = service('validation');

        // first validate the products[x] array
        if ( !$this->validate([
            'products' => 'required|is_array'
        ]) ) {
            return $this->response->setJSON( ['error' => $validation->getErrors()] );
        }  
        
        // checkin at least one product exists
        $products = $this->request->getPost('products'); // ή getJSON(true) αν στέλνουν JSON
        if (! is_array($products) || count($products) < 1) {
            $this->response->setJSON( ['error' => $validation->getErrors()] );
        }

        $productModel       = new ProductModel();
        $productLogModel    = new ProductLogModel();
        $basket_products    = $this->request->getPost( "products");
        $group_uid          = date('Ymd_His') . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 5);


        // Updating product's stock && create a sales history
        foreach ($basket_products as $product ) {
            $productModel->set( 'stock', $product['data']['stock'] - $product['basketQuantity'] )->where('id', $product['data']['id'] )->update();
            
            $productLogModel->insert([
                'type_id'           => PRODUCT_LOG_TYPE_SELLING,
                'measuring_unit_id' => $product['data']['measuring_unit_id'],
                'brand_id'          => $product['data']['brand_id'],
                'supplier_id'       => $product['data']['supplier_id'],
                "product_id"        => $product['data']['id'],
                "quantity"          => $product['basketQuantity'],
                "selling_price"     => $product['basketPrice'],
                "old_stock"         => $product['data']['stock'],
                "new_stock"         => $product['data']['stock'] - $product['basketQuantity'],
                "group_uid"         => $group_uid
            ]);
        }
        return  $this->response->setJSON( [ 'success' => lang('Notification.title_save_success') ] );

    }
}
