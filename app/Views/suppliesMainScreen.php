<?=$this->extend("common/main")?>

<?=$this->section("content")?>


<!-- HEADER TOP BLACK -->
<div class="row navbar navbar-dark sticky-top bg-dark p-0 shadow">&nbsp;</div>

<!-- The actuall window of this page -->
<div class="row featurette class-supplies p-3 g-3 " >

    <!-- Title & Exit buttons -->
    <div class="d-flex justify-content-between pb-2 position-relative" >

        <div class="display-5">
            <button class="btn btn-primary fs-3" type="button" data-bs-toggle="offcanvas" data-bs-backdrop="true" data-bs-target="#basketOffCanvas" aria-controls="basketOffCanvas">
                <i class="bi bi-card-checklist" style="pointer-events: none;"></i>
            </button>
            - Καταχώρηση
        </div>
        <!--<div id="dymoLabel"><button type="button" href="#" class="btn btn-primary mt-3 btn-lg gap-2"></button> </div>-->
        <div class="card border-0">
            <button onclick="location=window.location='<?= base_url(); ?>'" class="btn btn-primary mt-2 btn-lg gap-2 btn-exit" type="button">
                <i class="bi bi-arrow-bar-left"></i> Έξοδος
            </button>
        </div>
    </div>

    <!-- MAIN PANEL -->
    <div class="border rounded row p-3" >


        <!-- Log area -->
        <div class="col-md-4 position-relative">
            <div class="card mt-2" >
                <div class="card-body" >
                    <?= view("components/product_log", [
                        'url' => base_url('products?barcode='),
                        'height'    => 'calc(100vh - 400px)'
                    ]) ?>
                </div>
            </div>
        </div>


        <!-- Product area -->
        <div class="col-md-8">

            
            <form id="productForm" action="<?= base_url('supplies' ) ?>" method="post"  enctype="multipart/form-data">

                <?= csrf_field() ?>
                <input type="hidden" name="stock" value="">

                <!-- Barcode & Textcode -->
                <div class="row mb-3 mt-2">
                    <div class="col-12">
                        <?= view('/components/input_barcode', ['name'=> 'barcode']) ?>
                    </div>  
                </div>
                
                <!-- Name -->
                <div class="row g-3 mb-3">
                    <div class="col-12 form-floating">
                   
                    <?= view('components/input_text', [
                            'name'          => 'description',
                            'placeholder'   => 'Περιγραφή προϊόντος',
                            'label'         => 'Περιγραφή προϊόντος',
                            'disabled'      => false,
                            'remember_me'   => true,
                            'add_delete_btn'=> true
                        ]);  ?>    
                    </div>
                </div>
                
                 <!-- Supplier & brand select -->
                <div class="row g-3 mb-3 ">
                    
                    <!-- Supplier select -->
                    <div class="col-6">
                        <?= view('/components/select_live', [
                            'name'          => 'supplier_id', 
                            'v_name'        => 'Προμηθευτή',
                            'v_name_plural' => 'Προμηθευτές',
                            'rest_url'      => 'suppliers', // search, sort, delete, insert
                            'rest_actions'  => [
                                'create'    => 'create',
                                'delete'    => 'delete', 
                                'sort'      => 'sort',
                                'search'    => '', 
                                ],
                            'items'         => $suppliers ,
                            'delete_text'   => 'Child Brands and products will be delete too !! Do you want to proceed? '
                            
                            ]) ?>
                    </div>
                    
                    <div class="col-1 pt-2">&nbsp;</div> 

                    <!-- Brand select -->
                    <div class="col-5">
                        <?= view('/components/select_live', [
                            'name'          => 'brand_id', 
                            'v_name'        => 'Κατηγορία', 
                            'v_name_plural' => 'Κατηγορίες',
                            'rest_url'      => 'brands', // search, sort, delete, insert
                            'rest_actions'  => [
                                'create'    => 'create',
                                'delete'    => 'delete', 
                                'sort'      => 'sort',
                                'search'    => '', 
                                ],
                            'depends_on_id' => 'supplier_id',
                            'items'         => $brands,
                            'delete_text'   => 'Είστε σίγουρος για την διαγραφή της μάρκας? '
                            
                            ]) ?>
                    </div>

                </div>

                <!-- Purchase moq / threshold -->
                <div class="row g-3 mb-3">
                
                        <?= session()->get('measuring_unit-remember-me') ?>
                    <!-- Measuring unit -->
                    <div class="col-4 form-floating">
                        <?= view('/components/select_live', [
                                'name'          => 'measuring_unit_id', 
                                'v_name'        => 'Μον. Μέτρησης',
                                'v_name_plural' => 'Μον. Μέτρησης',
                                'rest_url'      => 'measuring_units', // search, sort, delete, insert
                                'rest_actions'  => [
                                    'create'    => 'create',
                                    'delete'    => 'delete', 
                                    'sort'      => 'sort',
                                    'search'    => '', 
                                    ],
                                'depends_on_id'     => '',
                                'depends_on_name'   => '',
                                'items'         => $m_units
                                
                                
                                ]) ?>
                    </div>
                        
                        
                    <!-- reorder quantity -->
                    <div class="col-3 form-floating hide-on-raw-insert">
                        <?= view('components/input_number', [
                            'name'          => 'reorder_quantity',
                            'placeholder'   => 'Ποσότητα. αναπ/λίας',
                            'value'         => '',
                            'label'         => 'Ποσότητα. αναπ/λίας',
                            'disabled'      => false,
                            'remember_me'   => true,
                            'class'         => ' form-control '


                        ]);  ?>    
                    </div>

                    <!-- Selling price -->
                    <div class="col-3 form-floating">
                        <?= view('components/input_number', [
                                'name'          => 'selling_price',
                                'placeholder'   => 'Τιμή πώλησης (€)',
                                'value'         => '',
                                'label'         => 'Τιμή πώλησης (€)',
                                'disabled'      => false,
                                'remember_me'   => true,
                                'class'         => ' form-control '
                            ]);  ?>    
                    </div>

                     <!-- stock -->
                    <div class="col-2 form-floating">
                        <?= view('components/input_number', [
                                'name'          => 'show_stock',
                                'placeholder'   => 'Στοκ',
                                'value'         => '0',
                                'label'         => 'Στοκ',
                                'disabled'      => true,
                                'remember_me'   => false,
                                'add_delete_btn'=> false,
                                'class'         => ' form-control '
                            ]);  ?>    
                    </div>
                
                
                </div>
                
                <hr/>

                <!-- Purchase price / Retail price -->
                <div class="row g-3 mb-3 hide-on-raw-insert">
                    <div class="col form-floating">
                            <?=view('components/input_number', [
                                'name'          => 'quantity',
                                'placeholder'   => 'Ποσότητα/Τεμάχια',
                                'value'         => '',
                                'label'         => 'Ποσότητα/Τεμάχια',
                                'disabled'      => false,
                                'remember_me'   => false,
                                'add_delete_btn'=> true,
                                'class'         => ' form-control '
                            ]); ?>
                        </div>    
                    <div class="col form-floating">
                            
                            <?= view('components/input_number', [
                                'name'          => 'buying_price',
                                'placeholder'   => 'Τιμή αγοράς (€)',
                                'value'         => '',
                                'label'         => 'Τιμή αγοράς (€)',
                                'disabled'      => false,
                                'remember_me'   => true,
                                'class'         => ' form-control '
                            ]); ?>

                        
                    </div>
                    
                    <div class="col form-floating">
                         <?= view('components/input_number', [
                            'name'          => 'wholesale_discount',
                            'class'         => 'input-percentage form-control ',
                            'placeholder'   => 'Έκπτωση %',
                            'value'         => '',
                            'label'         => 'Έκπτωση %',
                            'min'           => '0',
                            'max'           => '100',
                            'disabled'      => false,
                            'remember_me'   => true,

                        ]);  ?>
                    </div>
                    
                    <div class="col form-floating">
                        <input type="input" class="form-control text-end " id="sum" name="sum" disabled>
                        <label for="sum">Σύνολο</label>
                    </div>
                </div>

               
                <hr/>
                <!-- Save button & clear form -->
                <div class="row g-3 mb-3">
                    <!-- Save button -->
                    <div class="col-10 d-grid">
                        <button class="btn btn-primary btn-save pt-2 pb-2" type="submit"  value="Submit">
                            <span class="fs-4"> <i class="bi bi-save2"></i>&nbsp;&nbsp;|&nbsp;&nbsp;Αποθήκευση</span>
                        </button>
                    </div>

                    <div class="col-1 fs-1 text-center">|</div>

                    <!-- Clear form button -->
                    <div class="col-1 pt-1">
                        <button class="btn btn-danger bg-danger bg-gradient text-white fs-3" onClick="window.location.reload();" type="button"><i class="bi bi-arrow-clockwise"></i></button>
                    </div>
                </div>

            <?php echo form_close() ?>
        </div>

    </div>
    
    <!-- Toast messages -->
	<?= $this->include('common/toast') ?>

</div>

<!-- Canvas located left and treated as the buying basket a.k.a invoice -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="basketOffCanvas" data-bs-backdrop="true" aria-labelledby="offcanvasWithBackdropLabel">

    <div class="offcanvas-header">

        <div >
            <h4 class="offcanvas-title" ></h4>
            <div class="invoice-details"></div>
        </div>
        <button type="button" class="btn-close text-reset" class="width:50%" data-bs-dismiss="offcanvas" aria-label="Close"></button>

    </div>
    <hr class="my-1"/>
    <div class="offcanvas-body px-2 pt-1">

        <ul class="list-group list-group-flush">


        </ul>

    </div>

    <div class="offcanvas-footer p-2 border-top">
        <div class = "row" >
            <div class="col-7">
                <div class="form-floating">
                    <input type="text" class="form-control only-number number-input input-currency" id="invoice-discount" name="invoice-discount" placeholder="Έκπτωση Τιμολογίου">
                    <label for="invoice-discount">Έκπτωση Τιμολογίου</label>
                </div>
            </div>
            <div class="col-2 pt-2"><button type="button" id=btn-discount class="btn btn-primary"><i class="bi bi-arrow-clockwise"></i></button></div>
            <div class="col-3 pt-2">|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" id=btn-close-invoice class="btn btn-danger text-center"><i class="bi bi-check-lg"></i></button></div>
        </div>


    </div>

</div>
</div>
<!-- Barcode generator -->
<div class="modal fade" id="barcodeModal" tabindex="-1" aria-labelledby="barcodeModalLabel" aria-hidden="true"
     data-generate-url="<?= esc(base_url('supplies/ajaxGenerateBarcode'), 'attr') ?>">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="barcodeModalLabel">Δημιουργία barcode EAN-13</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p id="barcodeGeneratorStatus" role="status">Δημιουργία νέου barcode...</p>
                <svg id="generatedBarcodeImage" class="img-fluid" role="img" aria-label="Generated EAN-13 barcode"></svg>
                <p id="generatedBarcodeValue" class="fs-4 fw-bold mb-0"></p>
                <div id="barcodeGeneratorError" class="alert alert-danger mt-3 d-none" role="alert"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="generateAnotherBarcode">Δημιουργία άλλου</button>
                <button type="button" class="btn btn-primary" id="useGeneratedBarcode" disabled>Χρήση barcode</button>
            </div>
        </div>
    </div>
</div>

<script>

    //When document is ready .. init input number formats
    $(document).ready(function() {
        SFNumberFunctions._initInputFormat();
    });
    
    // before submitting the form check that the following inputs have input : barcode, brand, category, quantity
    $( "#productForm" ).submit(function( event ) {

        event.preventDefault();
        event.stopPropagation();
        
        let required_fields = [ "#barcode", "#supplier_id_select",  "#brand_id_select",  "#measuring_unit_id_select", "input[name='quantity']", "input[name='description']" ];
        let submit_form = true;

        required_fields.forEach( function ( field ) {
            if ( $( field ).val() == '' ) {
                $( field ).addClass("is-invalid");
                event.preventDefault();
                submit_form = false;
            }
            else {
                $( field ).removeClass("is-invalid").addClass("is-valid");
            }
        })

        if ( !submit_form ) {
            alert("Πρεπει να συμπληρωσετε τα απαραιτητα πεδια");
            return false;   
        }

        let data = [];
        $(this).find('input, select').each(function() {
            // do not add checkboxes #
            if ( $(this).hasClass('form-check-input') || typeof $(this).attr('name') == 'undefined' )  { return true; }
            
            if ( $(this).hasClass('number-input')) { 
                data.push({ name: $(this).attr('name'), value: $(this).val()} ); return true; 
            }
            else {
                // normal components
                data.push( { name: $(this).attr('name'), value: $(this).val() } ); 
            }
            
        });

        console.log ( data );

        $.ajax({
            type: "POST",
            url: "<?= base_url('supplies' ) ?>",
            data:  data,
            dataType: "json",

            success: function(serverData) {
                if ( serverData != "" && serverData != null ) {
                    show_toast(serverData.success, 'success');
                }
                __clearProductLogs ();
                __clearProductValues ( true );
               
            },

            error: function(serverData) {
             
                show_toast(serverData.error, 'danger');
             
            }

        })
        
    });

    

</script>

<!-- Calculate sum script -->
<script>

    // Cache elements for better performance
    const $quantity     = $("input[name='quantity']");
    const $buyingPrice  = $("input[name='buying_price']");
    const $discount     = $("input[name='wholesale_discount']");
    const $sum          = $("#sum");

    function calculateSum() {
        let quantity        = SFNumberFunctions._parseNumber($quantity.val());
        let buying_price    = SFNumberFunctions._parseNumber($buyingPrice.val());
        let discount        = SFNumberFunctions._parseNumber($discount.val());

        // Validate inputs
        if (quantity > 0 && buying_price > 0 && !isNaN(quantity) && !isNaN(buying_price)) {
            let sum = quantity * buying_price;
            
            // Apply discount if valid
            if (discount > 0 && discount <= 100) {
                sum = sum * (1 - discount / 100);
            }
            
            $sum.val(SFNumberFunctions._displayCurrency(sum, 2));
        } else {
            $sum.val(""); // Clear when invalid
        }
    }

    // Attach event listeners
    $quantity.add($buyingPrice).add($discount).on("input keyup change", calculateSum);

    // Optional: Calculate on page load if values exist
    calculateSum();

</script>

<!-- Fetch product if barcode exists -->
<script>
    $("input[name='barcode']").on('change',  function ( event ) {
        
        event.preventDefault(); 
        let barcode = $(this).val();
        if ( barcode === '' ) return;
        

        $.ajax({
            url: "<?= base_url('products/ajaxBarcodeSearch/') ?>" + barcode,
            method: "get",
            dataType: "json",

            success: function(serverData) {
                __clearProductLogs ();

                if ( serverData.data != "" && serverData.data != null ) { 
                    __fillProductValues ( serverData.data ); 
                    __renderProductLogsFromProductData ( serverData );
                }
                else { 
                    __clearProductValues ( ); 
                }
            }
        });
        
    });

    function __fillProductValues ( product ) {
        //console.log ( product );
        $("input[name='description']").val( product.description ).trigger('change');
        $("input[name='stock']").val( product.stock );
        $("input[name='show_stock']").val( SFNumberFunctions._displayNumber (product.stock, 2 ) );
        $("input[name='reorder_quantity']").val( SFNumberFunctions._displayNumber (product.reorder_quantity,2 ) ).trigger('change')
        $("input[name='selling_price']").val( SFNumberFunctions._displayNumber(product.selling_price, 2) ).trigger('change'); 
        $("input[name='buying_price']").val( SFNumberFunctions._displayNumber (product.buying_price, 2) ).trigger('change');
        $("input[name='wholesale_discount']").val( SFNumberFunctions._displayNumber  (product.wholesale_discount,2) ).trigger('change');

        $("select[name='measuring_unit_id']").val(product.measuring_unit_id ).trigger('change');
        $("select[name='supplier_id']").val(product.supplier_id ).trigger('change');
        $("select[name='supplier_id']").trigger('change');
        $("select[name='brand_id']").data ('ajaxPopulateList').call($("select[name='brand_id']"), function() {
            $("select[name='brand_id']").val(product.brand_id );
 
        });
    }

    function __clearProductValues ( clear_all = false ) {


        $('#productForm').find('input, select').each ( function () {
            
            if ( $(this).attr('name') === 'barcode'  &&  clear_all== false ) { return true; }
            
            let $input = $(this);
            let $checkbox = $input.parents('.conponent-input').find('input[type="checkbox"]');
            !$checkbox.prop('checked') ? $input.val("").trigger('change') : '';

            $input.removeClass('is-valid');
            


        })
       
    }

    
</script>

<script type="module">
    import { renderEan13Barcode } from <?= json_encode(base_url('js/modules/ean13Barcode.js')) ?>;

    const barcodeModal = document.getElementById('barcodeModal');
    const barcodeStatus = document.getElementById('barcodeGeneratorStatus');
    const barcodeValue = document.getElementById('generatedBarcodeValue');
    const barcodeImage = document.getElementById('generatedBarcodeImage');
    const barcodeError = document.getElementById('barcodeGeneratorError');
    const useBarcodeButton = document.getElementById('useGeneratedBarcode');
    const generateBarcodeButton = document.getElementById('generateAnotherBarcode');
    let generatedBarcode = '';

    async function generateBarcode() {
        generatedBarcode = '';
        useBarcodeButton.disabled = true;
        generateBarcodeButton.disabled = true;
        barcodeStatus.textContent = 'Δημιουργία νέου barcode...';
        barcodeValue.textContent = '';
        barcodeImage.replaceChildren();
        barcodeError.textContent = '';
        barcodeError.classList.add('d-none');

        try {
            const response = await fetch(barcodeModal.dataset.generateUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.error || 'Could not generate a barcode.');
            }

            generatedBarcode = result.barcode;
            renderEan13Barcode(barcodeImage, generatedBarcode);
            barcodeValue.textContent = generatedBarcode;
            barcodeStatus.textContent = 'Επιλέξτε το barcode για να το προσθέσετε στο προϊόν.';
            useBarcodeButton.disabled = false;
        } catch (error) {
            barcodeStatus.textContent = 'Η δημιουργία barcode απέτυχε.';
            barcodeError.textContent = error.message;
            barcodeError.classList.remove('d-none');
        } finally {
            generateBarcodeButton.disabled = false;
        }
    }

    barcodeModal.addEventListener('show.bs.modal', generateBarcode);
    generateBarcodeButton.addEventListener('click', generateBarcode);
    useBarcodeButton.addEventListener('click', function () {
        if (!generatedBarcode) {
            return;
        }

        const barcodeInput = document.getElementById('barcode');
        barcodeInput.value = generatedBarcode;
        barcodeInput.dispatchEvent(new Event('input', { bubbles: true }));
        bootstrap.Modal.getOrCreateInstance(barcodeModal).hide();
    });
</script>

<?=$this->endSection()?>
