<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\App;
use App\Models\SupplierModel;
use App\Models\BrandModel;
use App\Models\ProductModel;

class SupplierController extends BaseController
{
    public function index() {
        return $this->getSupplierList ();
    }

    public function create () {

        if (!$this->validate([
            'name' => 'required',
        ])) {
            return  $this->validator->getErrors() ;
        } else {

            $name           = $this->request->getVar('name');
            $supplierModel  = new SupplierModel();
            $recFound       = $supplierModel->where ("name", $name )->findAll();

            if ( $recFound == null ) { // this supplier name not found so ... insert
                $supplierModel->insert( ["name" => $name ]);
            }

            return $this->getSupplierList ();

        }

    }

    public function destroy ( $supplier_id ) {
        
        if (empty($supplier_id)) {
            return $this->getSupplierList ();
        }

        // get the brand
        $brandModel = new BrandModel();
        $brands     = $brandModel->where('supplier_id', $supplier_id )->findAll();

        // delete all products
        $productModel = new ProductModel();
        foreach ($brands as $brand) {
            $brand_id = $brand['id'];
            $productModel->where(['supplier_id' => $supplier_id, 'brand_id' => $brand_id ] )->delete();
        }

        // delete all brands
        $brandModel->where('supplier_id', $supplier_id )->delete();

        // delete the supplier
        (new SupplierModel())->delete($supplier_id);
    
        
        return $this->getSupplierList ();
        
    }

    public function sort () {

        $session 		= session();
        
        if (!$this->validate( [
			'sort'   => 'required',
            'sort.*' => 'is_natural_no_zero', // each element must be a positive integer (ID)
		] )) {
			$session->setFlashdata('error', lang('School.error_new_school_save')); 
            return redirect()->back()->withInput();
        }

        $sort           = $this->request->getPost( "sort");
        $supplierModel  = new SupplierModel();

        foreach ($sort as $position => $id) { $supplierModel->update($id, ['sort' => $position]); }

        $session->setFlashdata('error', lang('School.error_new_school_save')); 
        return redirect()->back()->withInput();

    }

    public function search () {

        $name = $this->request->getGet('name'); 
        $query = (new SupplierModel())->select(["id", "name"])->orderBy("sort", "ASC");

        return strlen( $name ) <= 0 ? 
            json_encode( $query->findAll() ) : 
            json_encode( $query->like('name', $name )->findAll() ) ;
        

    }

    private function getSupplierList () {
        return  json_encode( (new SupplierModel())->select(["id", "name"])->orderBy("sort", "ASC")->findAll() ) ;
    }


}
