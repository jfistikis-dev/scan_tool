<?php 

    $color = 'secondary';
    if ( isset ($item['reorder_quantity']) && $item['reorder_quantity'] >= 1 ) {
        $color =  $item['stock'] >= $item['reorder_quantity'] ? 'success' : 'danger';
    }

?>

<div class='card d-flex flex-row w-100 mb-2 justify-content-center list-group-item p-0 basket-item' data-state='unselected' data-barcode = "<?= $item['barcode'] ?>" data-id="<?= $item['id'] ?>">
    
    <div class="bg-secondary text-white px-2 rounded-start d-flex align-items-center delete-area"><i class='bi bi-trash'></i></div>
	
	<div class='row g-0 flex-grow-1 p-2 pt-1'>  
        <div class='col-md-12'>
            <div class='card-body pt-1 pb-1'>
                
                <div class='row'>
                    <div class='col-md-8'>
                        <div class='card-title'>
                            <div class='fs-5'><?= $item['description'] ?></div>
                            <div class='fs-7'>ΑΠΟΘΕΜΑ :: <span class='apothema-num text-<?= $color ?>'><?= $item['stock'] ?> <?= $item['measuring_name'] ?></span></div>                                   
                        </div>
                        
                        <?= view('components/input_number', [
                                'class'         => 'quantity-input',                      
                                'name'          => 'quantity',
                                'value'         => '1',
                                'disabled'      => false,
                                'add_delete_btn'=> false,
                                'size'          => 8
                            ]); ?>

                        <span class="ps-1 pe-2"><?= $item['measuring_name'] ?></span>
                        <span class="ps-3 pe-4">*</span>
                        <?= view('components/input_number', [
                                'class'         => 'price-input',                      
                                'name'          => 'price',
                                'value'         => $item['selling_price'],
                                'disabled'      => false,
                                'add_delete_btn'=> false,
                                'size'          => 10
                            ]); ?>

                        &nbsp;&euro;
                    </div>
                    <div class='col-md-4 pt-4 text-end'>
                        <h1 class='mb-0 pb-0 '><span  class='basket-item-sum'><?= $item['selling_price'] ?></span></h1>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
	
</div>


