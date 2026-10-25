<?=$this->extend("common/main")?>

<?=$this->section("content")?>
	
	
	<!-- HEADER TOP BLACK -->
	<div class="row navbar navbar-dark sticky-top bg-dark p-0 shadow">&nbsp;</div>
	
	
	<div class="row featurette class-tamio" >

        <!-- MAIN PANEL -->
		<div class="col-md-8 " >
			<div class="card mt-3 h-100 mh-100" > 
				<div class="card-body" >
                    <div class="d-flex justify-content-between d-inline-block">
                        <h1 class="card-title mb-0">TAMEIO<sup class="text-danger ">&nbsp;<small><b>1v2</b></small></sup></h1>
                        <button type="button" class="btn h-auto btn-empty-basket"><i class="bi bi-arrow-clockwise"></i></button>
                    </div>

					<hr>
                    <div style="overflow-y: scroll;height:calc(100vh - 240px);">
                        <ul class="list-group basket">

                            <div class="card mb-3 list-group-item  p-2 add-basket-item" style="border-style: dashed">

                                <div class="card-body text-center rounded btn-add-item-to-basket" role="button">
                                   <h5 ><i class="bi bi-cart-plus"></i> - Προσθήκη είδους</h5>
                                </div>
                            </div>

                        </ul>

                        <!-- TOAST SUCCESS MESSAGE -->
                        <div aria-live="polite" aria-atomic="true" class="position-relative ">
                            <div class="toast-container position-absolute p-3 top-0 start-50 translate-middle-x" id="toastPlacement" data-original-class="toast-container position-absolute p-3">
                                <div class="toast fade" id="myToast" role="alert" aria-live="assertive" aria-atomic="true">
                                    <div class="toast-header">
                                        <strong class="me-auto"><i class="bi-gift-fill"></i> Eπιτυχία!</strong>
                                    </div>
                                    <div class="toast-body">
                                        Η πώληση, μόλις καταχωρήθηκε με επιτυχία. Μπορείς να την δείς <a href="#">εδώ!</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                </div>
			
			</div>
        </div>

		<!-- RIGHT PANEL -->
		<div class="col-md-4">

            <div class="card border-0">
                <button class="btn btn-primary mt-3 btn-lg gap-2 btn-exit" onclick="window.location='<?php echo base_url(); ?>'" type="button"><i class="bi bi-arrow-bar-left"></i> Έξοδος</button>
            </div>
            <!-- Product Log -->
            <div class="card mt-3" >
                <div class="card-body" >
                    <?= view("components/product_log", [
                        'url' => base_url('products/ajaxBarcodeSearch?barcode='),
                        'height'    => 'calc(100vh - 450px)'
                    ]) ?>
                </div>
                    
            </div>

            <!-- Total Sum -->
            <div class="mt-3 text-white card border-0" >

                <div class="row" style="height: 125px;margin: 0px;" >
                    <!-- save basekt button -->
                    <div style="position:absolute;top: 0;left: 0;z-index: 9;margin-top: -5px;margin-left: 20px;">
                        <div class="rounded-circle bg-white " style="margin-left:-18px;width: 124px;height:135px">
                            <button id="submitSale" class="btn btn-outline-primary  text-center rounded-circle  pb-2 btn-save-basket" style="font-size:65px;margin-top:8px;margin-left:-6px;width: 120px;height:120px">
                                <i class="bi bi-cart-check"></i>
                            </button>
                        </div>
                    </div>

                    <!-- total sum -->
                    <div class="bg-light card col-10 ms-auto position-relative border-secondary" >

                        <div class="card-body position-absolute top-50 end-0 translate-middle-y text-end text-secondary
                                    d-flex align-items-end flex-column"
                            style="max-width: 100%; box-sizing: border-box;">
                            <h1 class="pb-0 mb-0 border-bottom"><span class="total-sum">0.00</span></h1>

                            <div >
                                <h5><span class="total-pcs">0</span>&nbsp;Τεμ </h5>
                            </div>

                            <div class="border-top">
                                <small>Με έκπτωση 10% : </small><span class=""><b><span class="total-discount">0.00</span> €</b></span>
                            </div>


                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- Toast messages -->
		<?= $this->include('common/toast') ?>


    </div>


    <!-- modal with products -->
    <div class="col-6">
        <?= view('/components/live_modal', [
            'button'        => 'btn-add-item-to-basket',
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


    <!-- managing basket functions  -->
    <script>

        let basketProductList = [];
		let _transmissionOfBarcodeStarted = false;

        
        function __updateProductPriceInHtml($node) {

            let barcode = String($node.closest('.basket-item').data('barcode'));
            let productHtml = __getProductFromHtml(barcode);

            if (!productHtml) return;

            let product = __getProductFromList(barcode);

            if (!product) return;

            // Read the exact quantity entered by the user
            let quantity = SFNumberFunctions._parseNumber(  productHtml.find(".quantity-input").val()  );

            let price = SFNumberFunctions._parseNumber( productHtml.find(".price-input").val() );

            // REPLACE the quantity; do not increment it
            product.basketQuantity = quantity;
            product.basketPrice = price;

            console.log (product.basketQuantity)

            __renderBasketDisplay();
        }

        function __getProductFromList( barcode ) {
            return basketProductList.find(
                        product => String(product.data.barcode) === String(barcode)
                    ) || null;
        }
        
        function __getProductFromHtml ( barcode ) {
            
            let foundItem = null;
            $(".basket>.basket-item").each ( function () {
                let barcodeStr = $(this).data("barcode").toString();
                if ( barcodeStr === barcode ) {
                    foundItem = $(this);
                }
            })
            return foundItem;
        }

        function __removeProductFromBasket ( product ) {
            
            // remove from list first
            let productIndex = basketProductList.findIndex(p => p.data.barcode === product.data.barcode);
            basketProductList.splice( productIndex, 1 );
            
            // remove from html
            __getProductFromHtml( product.data.barcode ).remove();
            if ( product.data.id == $(".product-log-list").attr("data-product-id") ) {  __clearProductLogs(); }
                
            // update basket display
            __renderBasketDisplay ();
            
        }

        function __addProductToBasket ( product ) {
            
            product.basketQuantity  = 1;
            product.basketPrice     = product.data.selling_price;
            
            // add to list
            basketProductList.push( product );
            // add to html
            $(".basket").find(".add-basket-item").before( product.html );

            // update price in html
            let $productHtml = __getProductFromHtml( product.data.barcode );
            $productHtml.find(".price-input").val( SFNumberFunctions._displayNumber( product.basketPrice , 2 )) ;

            // update basket display
            __renderBasketDisplay ();
        }

        function __updateProductQuantity(product, quantity = 1) {

            let productIndex = basketProductList.findIndex(
                p => String(p.data.barcode) === String(product.data.barcode)
            );

            if (productIndex === -1) return;

            let currentQuantity = SFNumberFunctions._parseNumber(
                basketProductList[productIndex].basketQuantity
            );

            let increment = SFNumberFunctions._parseNumber(quantity);

            basketProductList[productIndex].basketQuantity =
                currentQuantity + increment;

            let productHtml = __getProductFromHtml(
                String(product.data.barcode)
            );

            productHtml.find(".quantity-input").val(
                SFNumberFunctions._displayNumber(
                    basketProductList[productIndex].basketQuantity,
                    2
                )
            );

            __renderBasketDisplay();
        }

        function __renderBasketDisplay () {

            let totalSum    = 0;
            let totalPcs    = 0;
            let totalDiscount = 0;

            basketProductList.forEach ( product => {
                totalSum        += parseFloat( product.basketPrice ) * parseFloat( product.basketQuantity );
                totalPcs        += parseFloat( product.basketQuantity );
                totalDiscount   += ( parseFloat( product.basket_price ) - parseFloat( product.basketPrice ) ) * parseFloat( product.basketQuantity );
                
                // update total price in List
                product.totalPrice = product.basketPrice   * product.basketQuantity ;
                // update total price in html
                let $productHtml = __getProductFromHtml( product.data.barcode );
                $productHtml.find(".basket-item-sum").text ( SFNumberFunctions._displayCurrency ( product.totalPrice, 2  ) );
                
            });

            $(".total-sum").text( SFNumberFunctions._displayCurrency(totalSum, 2) );
            $(".total-pcs").text( SFNumberFunctions._displayNumber(totalPcs, 2) );
            $(".total-discount").text( SFNumberFunctions._displayNumber(totalDiscount, 2) );


        }
        
        function __clearBasket () {
            $(".list-group.basket>.basket-item").each( function () {
                let barcode = $(this).data("barcode");
                
                let product = __getProductFromList ( barcode.toString() );
                __removeProductFromBasket ( product );
            })
        }
    
        function __toEuropeanDate(datetimeStr) {
            // Split date and time
            const [datePart, timePart] = datetimeStr.split(' ');
            const [year, month, day] = datePart.split('-');
            
            // Return in European format
            return `${day}-${month}-${year} ${timePart}`;
        }
    </script>

    <!-- DOM event functions -->
    <script>
        // when scanned a new item  
       $(document).ready(function () {
            
       let barcode = '';
            let timer;

            $(document).on('keydown', function(e) {
                // Reset timer if the user types/scans again soon
                clearTimeout(timer);
			
				if ( barcode.startsWith('@') || barcode == '@' ) {
					_transmissionOfBarcodeStarted = true;
				}
				
                // Check if key is Enter or Tab — scanner done
                if ((e.key === 'Enter' || e.key === 'Tab' ) && barcode.length > 3) {
                                        
					console.log('Scanned barcode:', barcode);
					$barcodeStr = barcode.substring (1); // cut the '@'
					_transmissionOfBarcodeStarted = false;
					
					ajaxGetItemData ( $barcodeStr );
           
                    barcode = '';
                    return;
                }

                // Ignore special keys
                if (e.key.length === 1) {
                    barcode += e.key;
                }

                // Reset barcode , if no key pressed for 150ms 
                timer = setTimeout(() => barcode = '', 100);
            });

        });

        // delete an item from list ... 
        $("body").on( "click", ".delete-area", function (event ) {
            event.preventDefault();
            event.stopPropagation();
            
			Swal.fire({

                title: "Είστε σίγουρος?",
                text: "Δεν είναι δυνατή η επαναφορά!",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ναί, διαγραφή!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        let product = __getProductFromList ( $(this).parent().data("barcode").toString() );
                        __removeProductFromBasket ( product );
                        Swal.fire({title: "Διαγράφηκε!",text: "Η εγγραφή διαγράφηκε με επιτυχία.",icon: "success"});
                    }
            });
           
        });


        // change item's quantity or price
        $(document).on("input change keyup", "input", function (e) {
           
           if ( this.classList.contains("quantity-input") ||  this.classList.contains("price-input") ) {
                __updateProductPriceInHtml($(this));
            }

        });
		
		
		// on setting focus... select the text!
		$(document).on( 'focus', '.price-input,.quantity-input', function() {
			const input = this;
			const length = input.value.length;
			input.setSelectionRange(length, length);
			
			setTimeout(() => $(this).select(), 100); 
		});
		

        // empty the basket 
        $(document).on( "click", ".btn-empty-basket", function () {
            __clearBasket ();
            __clearProductLogs ();
        });

        // save basket
        $("body").on( "click", ".btn-save-basket", async function () {
            

            await $.ajax({
                method: "POST",
                url: "sales",  
                data: { products: basketProductList },
                dataType: "json",
                success: function (serverData) {
                    __clearBasket ();
                    __clearProductLogs ();

                    if ( serverData.error ) { return show_toast(serverData.message, "error"); }
                    else { show_toast(serverData.success, "success");  }
                    
                    $(':focus').blur();
                }

            })
        });

        $("body").on("click", ".basket>.basket-item", function (event ) {
            __ajaxGetProductLog ( $(this).data("barcode").toString() );
        });

        // add new product button
        $("body").on("click", ".basket>.basket-item", function (event){


        })

        
        // An ITEM SELECTED from modal list 
        $("body").on ("click", "#description-modal ul li a.dropdown-item", function () {
            let barcode = $(this).attr('data-barcode')
            ajaxGetItemData ( barcode  )
        });


        function ajaxGetItemData ( $barcodeStr ) {
            
            $.ajax({
                method: "POST",
                url: "sales/ajax-sales-entry",
                data: { barcode: $barcodeStr },
                dataType: "json",
                success: function (serverData) {
                    
                    __clearProductLogs ();
                    
                    if ( serverData.data != null ) {
                        let product = __getProductFromList ( $barcodeStr );
                        
                        product != null ? __updateProductQuantity ( product, 1) : __addProductToBasket( serverData );

                        __renderProductLogsFromProductData ( serverData );

                    
                    } else {
                        Swal.fire({title: "Εύρεση προϊόντος", icon: "error",text: "Το προϊόν ... δεν βρέθηκε στην βάση!!."});
                    }
                }
            });


        }

    </script>

<?=$this->endSection()?>
