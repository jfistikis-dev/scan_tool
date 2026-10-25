<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BrandModel;
use App\Models\InvoiceRowModel;
use App\Models\MeasuringUnitModel;
use App\Models\ProductModel;
use App\Models\ProductLogModel;
use App\Models\SupplierModel;

class SuppliesController extends BaseController
{
/**
 * Index page of the supplies view.
 * 
 * This function is responsible for displaying the main page of the supplies view.
 * It will display a list of all the suppliers and allow the user to select one of them.
 * 
 * @return \CodeIgniter\View\View The rendered view of the index page of the supplies view.
 */
    public function index() : string
    {
        //
        helper(['form', 'url']);
                
        $data['suppliers']  = ( new SupplierModel() )->select(["id", "name"])->orderBy("name", "ASC")->findAll();
        $data['brands']     = ( new BrandModel() )->select(["id", "name"])->orderBy("name", "ASC")->findAll();
        $data['m_units']    = ( new MeasuringUnitModel() )->select(["id", "name"])->orderBy("name", "ASC")->findAll();
        $data['products']   = ( new ProductModel() )->orderBy("description", "ASC")->findAll();
        $data['title']      = "scan-ME :: Αγορά / Προσθηκη προϊόντος";
        
        return view('suppliesMainScreen', $data);

    }

    public function ajaxGenerateBarcode()
    {
        $barcode = (new ProductModel())->generateUniqueEan13Barcode();

        if ($barcode === null) {
            return $this->response
                ->setJSON(['error' => 'Could not generate an available barcode. Please try again.'])
                ->setStatusCode(503);
        }

        return $this->response->setJSON(['barcode' => $barcode]);
    }
	
	function __parseFormattedNumber($string) {
		$string = trim((string) $string);
		$string = str_replace('.', '', $string);
		$string = str_replace(',', '.', $string);

		return (float) $string;
	}

    public function store()
    {
        
        $validation         = service('validation');
        $convertData        = ['measuring_unit_id', 'reorder_quantity', 'selling_price'];
        $remember_me_array  = ["supplier_id", "brand_id", "measuring_unit_id", "description","reorder_quantity","selling_price"];


        $rules = [
            'barcode'           => 'required',
            'supplier_id'       => 'required|numeric',
            'brand_id'          => 'required|numeric',
            'measuring_unit_id' => 'required|numeric',

            'description'       => 'required',

            'reorder_quantity'  => 'required|numeric',
            'selling_price'     => 'required|numeric',

        ];
        
        $data = $this->request->getPost();
        
        foreach ( $convertData as $value ) {
            $data[ $value ]    = $this->__parseFormattedNumber ( $this->request->getPost($value));
			
            $data[ $value ]    = $this->request->getPost($value)  == "" ? "0" :  $data[ $value ]; 
        }
       
        if (! $this->validateData($data, $rules)) {
            return $this->response->setJSON( ['error' => $validation->getErrors()] );
        }

        // id sent => update of that product (its barcode may have changed), no id => new product
        $productModel   = new ProductModel();
        $existing       = null;

        if ( !empty( $data['id'] ) ) {
            $existing = $productModel->find( $data['id'] );
            if ( !$existing ) {
                return $this->response->setJSON( ['error' => ['id' => 'Το προϊόν δεν βρέθηκε (ίσως διαγράφηκε).']] );
            }
        }

        // the barcode must not belong to another product
        $owner = $productModel->where('barcode', $data['barcode'])->first();
        if ( $owner && ( $existing === null || $owner['id'] != $existing['id'] ) ) {
            return $this->response->setJSON( ['error' => ['barcode' => 'Υπάρχει ήδη προϊόν με αυτό το barcode: ' . esc( $owner['description'] )]] );
        }

        // only the product details are saved here, stock & purchase prices change through addStock()
        $product = array_intersect_key( $data, array_flip( ['barcode', 'supplier_id', 'brand_id', 'measuring_unit_id', 'description', 'reorder_quantity', 'selling_price'] ) );

        $existing ?
            $productModel->update( $existing['id'], $product ) :
            $productModel->insert( $product + ['stock' => 0, 'buying_price' => 0, 'wholesale_discount' => 0] );

        $data['stock']              = $existing['stock'] ?? 0;
        $data['buying_price']       = $existing['buying_price'] ?? 0;
        $data['wholesale_discount'] = $existing['wholesale_discount'] ?? 0;
        $data['quantity']           = 0;

        // create a new product log
        (new ProductLogModel())->__saveProductLogRaw( $data, PRODUCT_LOG_TYPE_UPDATE );

       
        // deal with remember me fields
        foreach ( $remember_me_array as $item ) {
            
            isset ( $data[ $item . '-remember-me-checkbox' ] ) ? 
                    session()->setFlashdata ( $item . '-remember-me', $data[ $item ] ) : 
                    session()->remove( $item . '-remember-me' );
        
        }

       $renamed = $existing && $existing['barcode'] !== $data['barcode'];

       return $this->response->setJSON( [
            'success' => $renamed ?
                            'Το barcode άλλαξε από ' . esc( $existing['barcode'] ) . ' σε ' . esc( $data['barcode'] ) . '.<br>Εκτυπώστε νέες ετικέτες.' :
                            'To προϊόν αποθηκεύτηκε επιτυχώς.',
            'created' => $existing === null,
            'product' => $productModel->getProductByBarcode( $data['barcode'] ),
        ] );


    }

    // Buys stock for an existing product (the "Προσθήκη στοκ" modal). Negative quantities are stock adjustments.
    public function addStock()
    {
        $product = (new ProductModel())->find( $this->request->getPost('product_id') );

        if ( !$product ) {
            return $this->response->setJSON( ['error' => 'Το προϊόν δεν βρέθηκε. Αποθηκεύστε πρώτα το προϊόν.'] )->setStatusCode(404);
        }

        $data = [];
        foreach ( ['quantity', 'buying_price', 'wholesale_discount'] as $value ) {
            $data[ $value ] = $this->__parseFormattedNumber( $this->request->getPost($value) );
        }

        if ( $data['quantity'] == 0 ) {
            return $this->response->setJSON( ['error' => 'Η ποσότητα πρέπει να είναι διαφορετική του μηδενός.'] )->setStatusCode(422);
        }
        if ( $data['wholesale_discount'] < 0 || $data['wholesale_discount'] > 100 ) {
            return $this->response->setJSON( ['error' => 'Η έκπτωση πρέπει να είναι από 0 έως 100.'] )->setStatusCode(422);
        }

        // the product details come from the DB, not from the form
        $data += array_intersect_key( $product, array_flip( ['barcode', 'supplier_id', 'brand_id', 'measuring_unit_id', 'description', 'reorder_quantity', 'selling_price', 'stock'] ) );

        (new ProductModel())->__saveProductRaw ( $data );
        (new ProductLogModel())->__saveProductLogRaw( $data, PRODUCT_LOG_TYPE_BUYING );

        $stock = (new ProductModel())->find( $product['id'] )['stock'];

        $message  = $data['quantity'] > 0 ? 'Προστέθηκε στοκ: ' : 'Ρύθμιση στοκ: ';
        $message .= number_format( $data['quantity'], 2, ',', '.' ) . '<br>Νέο στοκ: ' . number_format( $stock, 2, ',', '.' );

        return $this->response->setJSON( [
            'success'            => $message,
            'stock'              => $stock,
            'buying_price'       => $data['buying_price'],
            'wholesale_discount' => $data['wholesale_discount'],
        ] );
    }

   

}
