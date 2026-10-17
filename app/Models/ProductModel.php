<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'products';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    //protected $returnType           =  \App\Entities\ProductEntity::class;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = [

        "supplier_id","brand_id","measuring_unit_id","barcode","description","stock",
        "buying_price","selling_price","wholesale_discount", "reorder_quantity",
        

        ];

    // Dates
    protected $useTimestamps        = true;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    
    public function getProductByBarcode(string $barcode) : array | null {
        
        if ( $barcode == "" ) return null;
        
        $product = $this
            ->select( "products.*, products.id as product_id, suppliers.id as supplier_id, suppliers.name as supplier, brands.name as brand, brands.id as brand_id, measuring_units.name as measuring_name")
            ->where("barcode", $barcode)
            ->join ( "suppliers", "products.supplier_id = suppliers.id" )
            ->join ( "brands", "products.brand_id = brands.id" )
            ->join ( "measuring_units", "products.measuring_unit_id = measuring_units.id" )
            ->first();

        return $product;

    }

    public function generateUniqueEan13Barcode(): ?string
    {
        for ($attempt = 0; $attempt < 100; $attempt++) {
            $digits = str_pad((string) random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
            $sum = 0;

            for ($position = 0; $position < 12; $position++) {
                $sum += (int) $digits[$position] * ($position % 2 === 0 ? 1 : 3);
            }

            $checkDigit = (10 - ($sum % 10)) % 10;
            $barcode = $digits . $checkDigit;

            if ($this->where('barcode', $barcode)->first() === null) {
                return $barcode;
            }
        }

        return null;
    }

    public function __saveProductRaw ( array $data ): void {

        // get existing product
        $product = $this->where('barcode', $data['barcode'])->first();

        // update stock
        $data['stock'] = bcadd ( floatval($data['stock'] ),  floatval($data['quantity'] ), 2 );
        unset ( $data['quantity']);
        
        // save/update data 
        $product != null ? 
                $this->set($data)->where('id', $product['id'])->update() : 
                $this->insert($data); 
    }
}
