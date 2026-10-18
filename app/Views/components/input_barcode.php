<div class="d-flex flex-row align-items-center">
    <!-- Barcode -->
    <div class="input-group w-100 <?= $name ?>-input-div" >
        <button class="btn btn-secondary input-group-text fs-5" type="button" data-bs-toggle="modal" data-bs-target="#barcodeModal" ><i class="bi bi-upc-scan p-1"></i></button>
        <?php if (isset($add_product_popup)) : ?>
            <button class="btn btn-secondary input-group-text fs-5 btn-product-list" type="button"  ><i class="bi bi-search p-1"></i></button>
        <?php endif; ?>
        <input type="input" class="form-control only-number" id="<?= $name ?>" name = "<?= $name ?>" placeholder="barcode" >
        <button class="btn btn-outline-secondary btn-delete-barcode" type="button"><i class="bi bi-arrow-left"></i></button>
    </div>


    <!-- Generate button -->
    <!--<div class="pt-0 ps-3 w-100">
        <button type="button" class="btn btn-secondary " id="btn-bardode">
            <i class="bi bi-upc-scan"></i> - Δημιουργία
        </button>
    </div> -->
</div>

<!-- product list -->
<?php if (isset($add_product_popup)) : ?>
    <div class="col-6">
        <?= view('/components/live_modal', [
            'button'        => 'btn-product-list',
            'name'          => 'description', 
            'v_name'        => 'Προϊόν',
            'v_name_plural' => 'Προϊόντα',      
            'rest_url'      => 'products/ajaxProductList', // search, sort, delete, insert
            'rest_actions'  => [
                'sort'      => 'sort',
                'search'    => '', 
                ],
            'items'         => $products ,
            
            ]) ?>
    </div>

<?php endif; ?>


<script type="module">
    import { initBarcodeScanner } from '<?= base_url('/js/modules/barcodeScanner.js'); ?>';

    // initialize for your input 
    $(function () { initBarcodeScanner('#<?= $name ?>');  });
</script>

<script>
    $(function () {
        // Clear barcode button
        $('.<?= $name ?>-input-div .btn-delete-barcode').on('click', function() {
            $(this).siblings('input').val('');
        });

        //change color on button IF text input is empty
        $("input[name='<?= $name ?>']").on('input change', function() {
            var button = $(this).siblings('.btn-delete-barcode');
            if ($(this).val() == '') {
                button.removeClass('btn-danger').addClass('btn-outline-secondary');
            } else {
                button.removeClass('btn-outline-secondary').addClass('btn-danger');
                $(this).removeClass('is-invalid');
            }
        });
    });
</script>







