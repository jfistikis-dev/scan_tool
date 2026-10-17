<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BrandModel;

class BrandController extends BaseController
{
    public function index()
    {
        //
        $supplier_id = $this->request->getVar('supplier_id_select');
        return $this->getBrandList ( $supplier_id );
    }

    public function create () {

        if (!$this->validate([
            'name'          => 'required',
            'parent_id'   => 'required|numeric',
        ])) {
            print_r ( $this->validator->getErrors() );
        } else {

            $supplier_id    = $this->request->getVar('parent_id');   
            $brand_name           = $this->request->getVar('name');   

            $brandModel = new BrandModel();
            $recFound = $brandModel
                    ->where ("name", $brand_name )
                    ->where("supplier_id", $supplier_id  )
                    ->findAll();

            if ( empty ( $recFound )  ) { // this brand name not found so ... insert
                $brandModel->insert( ["name" => $brand_name, "supplier_id" =>  $supplier_id ]);
            }

            return $this->getBrandList ( $supplier_id );

        }

    }

    public function destroy ( $brand_id ) {

        $brand = (new BrandModel())->find( $brand_id );
        
        (new BrandModel())->where('id', $brand_id )->delete();

        return $this->getBrandList ( $brand['supplier_id'] ) ;
    
    }

    public function sort () {

        // a typical string would be .. :
        //item[]=8&item[]=1&item[]=10&item[]=7&item[]=9&item[]=11&item[]=12

        $session 		= session();
        
        if (!$this->validate( [
			'sort'   => 'required',
            'sort.*' => 'is_natural_no_zero', // each element must be a positive integer (ID)
		] )) {
            return redirect()->back()->withInput();
        }

        $sort       = $this->request->getPost( "sort");
        $brandModel = new BrandModel();

        foreach ($sort as $position => $id) { $brandModel->update($id, ['sort' => $position]); }

        return redirect()->back()->withInput();

        // return nothing ... sorting was performed!

    }

    private function getBrandList ( $supplier_id ) {

        return json_encode( (new BrandModel())
                    ->select(["id", "name"])
                    ->where("supplier_id", $supplier_id)
                    ->orderBy("sort", "ASC")
                    ->findAll() ) ;
    }


    public function search () {

        $name = $this->request->getGet('name'); 
        $query = (new BrandModel())->select(["id", "name"])->orderBy("sort", "ASC");

        return strlen( $name ) <= 0 ? 
            json_encode( $query->findAll() ) : 
            json_encode( $query->like('name', $name )->findAll() ) ;
        

    }
}
