<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MeasuringUnitModel;

class MeasuringUnitController extends BaseController
{
     public function index()
    {
        //
        return $this->getMeasuringUnitList ();
    }

    public function create () {

        if (!$this->validate([
            'name' => 'required',
        ])) {
            return  $this->validator->getErrors() ;
        } else {

            $name               = $this->request->getVar('name');
            $measuringUnitModel = new MeasuringUnitModel();
            $recFound       = $measuringUnitModel->where ("name", $name )->findAll();

            if ( $recFound == null ) { // this measuringUnitModel name not found so ... insert
                $measuringUnitModel->insert( ["name" => $name ]);
            }

            return $this->getMeasuringUnitList ();

        }

    }

    public function destroy ( $id ) {
           
        if (!empty($id)) {
            (new MeasuringUnitModel())->delete($id);
        }
        
        return $this->getMeasuringUnitList ();
           
        
    }

    public function sort () {

        $session 		= session();
        
        if (!$this->validate( [
			'sort'   => 'required',
            'sort.*' => 'is_natural_no_zero', // each element must be a positive integer (ID)
		] )) {

            return redirect()->back()->withInput();
        }

        $sort           = $this->request->getPost( "sort");
        $measuringUnitModel  = new MeasuringUnitModel();

        foreach ($sort as $position => $id) { $measuringUnitModel->update($id, ['sort' => $position]); }

        return redirect()->back()->withInput();

    }

    public function search () {

        $name = $this->request->getGet('name'); 
        $query = (new MeasuringUnitModel())->select(["id", "name"])->orderBy("sort", "ASC");

        return strlen( $name ) <= 0 ? 
            json_encode( $query->findAll() ) : 
            json_encode( $query->like('name', $name )->findAll() ) ;
        

    }

    private function getMeasuringUnitList () {
        return  json_encode( (new MeasuringUnitModel())->select(["id", "name"])->orderBy("sort", "ASC")->findAll() ) ;
    }


}
