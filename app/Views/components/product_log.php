<div class="d-flex flex-column">
    Τελευταίες εγγραφές προϊόντος
    <div class="fs-7 mt-n3 title-product-description"></div>
</div>
<hr/>
<div class="row" style="overflow-y: scroll;height:<?= $height ?>;" >
    <ul class="list-group list-group-flush product-log-list" data-product-id="0"></ul>
</div>

<script>

    function __ajaxGetProductLog ( barcode ) {
            $.ajax({
            url: "<?= $url ?>" + barcode,
            method: "get",
            dataType: "json",

            success: function(serverData) {
                __clearProductLogs ();
                $(".title-product-description").html('<i class="bi bi-arrow-right-short"></i> <span class="fw-bold text-success">' + serverData.data.description + '</span>');
                serverData.log != "" && serverData.log != null ? __renderProductLogs ( serverData.log ) : null;
            }

        });
    }
    
    function __clearProductLogs () {
        let $list = $(".product-log-list");
        $list.attr("data-product-id", "0");
        $(".title-product-description").html('');
        $list.empty();
    }

    function __renderProductLogsFromProductData ( programData  ) {
        if ( programData == null || programData.length == 0 ) return;
        if ( programData.log == null || programData.log.length == 0 ) return;

        $(".title-product-description").html('<i class="bi bi-arrow-right-short"></i> <span class="fw-bold text-success">' + programData.data.description + '</span>');
        __renderProductLogs ( programData.log );

    }

    function __renderProductLogs ( log ) {
    
        //console.log ( log )
        if ( log == null || log.length == 0 ) return;
        
        let $list = $(".product-log-list");
        let html = "";

        $list.empty();
        
        //console.log ( log );

        log.forEach ( function ( log_entry ) {
            if ( log_entry.type_id == "<?=  PRODUCT_LOG_TYPE_BUYING ?>") {
                html += "<li class='text-sm ";
                html += log_entry.quantity >= 0 ? 'bg-success' : 'bg-warning' ;
                html += " text-white px-2 py-1 rounded mb-1 opacity-75' style='font-size:0.8rem'>";
                html += __toEuropeanDate ( log_entry.created_at );     
                html += log_entry.quantity > 0 ? " - ΑΓΟΡΑ : " : " - ΡΥΘΜ. : ";
                html += SFNumberFunctions._displayNumber(log_entry.quantity, 2) + " " + log_entry.measuring_unit_name + " @ " + SFNumberFunctions._displayCurrency(log_entry.buying_price, 2);
                html += '</li>';
            }
            else if  ( log_entry.type_id == "<?=  PRODUCT_LOG_TYPE_SELLING ?>") {
                html += "<li class='text-sm bg-danger text-white px-2 py-1 rounded mb-1 opacity-75' style='font-size:0.8rem'>";
                html += __toEuropeanDate ( log_entry.created_at );    
                html += " - ΠΩΛΗΣΗ : " ;
                html += SFNumberFunctions._displayNumber ( log_entry.quantity ) + " " + log_entry.measuring_unit_name + " @ " + SFNumberFunctions._displayCurrency(log_entry.selling_price, 2);
                html += '</li>';
            }
            else {
                html += "<li class='text-sm bg-info";
                html += " text-white px-2 py-1 rounded mb-1 opacity-75' style='font-size:0.8rem'>";
                html += __toEuropeanDate ( log_entry.created_at ) + " - ΑΠΟΓΡ : ";     
                html += SFNumberFunctions._displayNumber(log_entry.old_stock, 2) + " -> " + SFNumberFunctions._displayNumber(log_entry.new_stock, 2) + " ΣΤΟΚ";
                html += '</li>';

            }
            $(".product-log-list").attr("data-product-id", log_entry.product_id);
            
        });

        $list.append(html);

    }
    
    function __toEuropeanDate(datetimeStr) {
        // Split date and time
        const [datePart, timePart] = datetimeStr.split(' ');
        const [year, month, day] = datePart.split('-');
        
        // Return in European format
        return `${day}-${month}-${year} ${timePart}`;
    }

</script>