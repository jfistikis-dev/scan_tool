<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\ProductModel;

class ProductLogModel extends Model
{
    protected $table            = 'product_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['type_id', 'product_id','supplier_id','brand_id','measuring_unit_id','group_uid', 'quantity','buying_price','selling_price','wholesale_discount','reorder_quantity', 'old_stock','new_stock' ];



    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];


    public function __saveProductLogRaw( $data, $type_of_log = PRODUCT_LOG_TYPE_BUYING ) {

        // first time we insert it ?? then stock was originally => 0 else => new stock ... became now old_stock!    
        $product_found    = ( new ProductModel() )->where('barcode' , $data['barcode'])->first();

        //create a log
        if ( $product_found != null ) {
            
            $last_log_found   = $this->where('product_id' , $product_found['id'])->orderBy('id', 'DESC' )->first();

            $data['product_id']     = $product_found['id'];
            $data['type_id']        = $type_of_log;
            $data['old_stock']      = $last_log_found == null ? 0 : $last_log_found['new_stock'];
            $data['new_stock']      = $data['stock'];
            $data['group_uid']      = date('Ymd_His') . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 5);

            //dd ( $data );
            $this->insert($data);
        }

    }
    
}



