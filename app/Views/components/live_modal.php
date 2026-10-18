<!-- Html content -->

<!-- The modal -->
<div class="modal fade" id="<?= $name ?>-modal" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Επιλογή <?= $v_name ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body ">

                <div class="input-group">
                    <input type="text" class="form-control form-control-lg" autocomplete="off" id="<?= $name ?>-modal-input" placeholder="Περιγραφή / Barcode" aria-label="'Ονομα <?= $v_name ?>" aria-describedby="button-addon2">
                    <button class="btn btn-secondary <?= $name ?>-btn-modal-add" type="button">Εύρεση</button>

                </div>

                <div class="mt-3">
                    Λίστα με <?= $v_name_plural ?>
                    <ul id="<?= $name ?>-modal-item-list" class=" dropdown-menu mt-1 py-0 sortable" style="display: block;position: static;min-width: 10rem;list-style: none;height: 300px; overflow: auto">
                    </ul>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Έξοδος</button>
            </div>
        </div>
    </div>
</div>

<!-- The script that handles the modal and the list -->
<script>
    (function() {
        
        const name          = "<?= $name ?>";
        const rest_url      = "<?= base_url() . $rest_url ?>";
        const rest_actions  = <?= json_encode($rest_actions) ?>;

             
        let modal_name      = "<?= $name ?>-modal";
        
        
         // The OPEN MODAL button clicked 
        $("body").on("click", ".<?=  $button ?>", async function (event) {
           
            await ajaxPopulateModalList(); 
            showModal();
        });

         $("body").on("click", ".<?= $name ?>-btn-modal-add", function () {

            let input = $("#" +modal_name+"-input");
            ajaxPopulateModalList(input.val());
         })

        // An ITEM SELECTED from modal list 
        $("body").on ("click", "#"+modal_name+" ul li a.dropdown-item", function () {
            //hideModal();
        });

        // ΑΝ ΙΤΕΜ HAS BEEN ENTERED FOR SEARCH
        $("body").on ("input","#" +modal_name+"-input", function (event) {
            ajaxPopulateModalList($(this).val());
        }) 

        // When modal closes when only one option ... set it to the select!
        $("#"+modal_name).on("hidden.bs.modal", function () {});


        async function ajaxPopulateModalList( search='' ) {
            
            let url = rest_url + "?"  + name + "=" + search
     
            await $.ajax({
                type: "get",
                url: url,
                success: function(serverData) {
                    //console.log (serverData)
                    populateModalList(serverData);

                }
            });
           
        }
    

        function populateModalList ( list_items ) {

            var list = $("#"+modal_name+"-item-list");
            list.empty();

            $(list_items).each ( function ( index, item ) {
                
                //console.log(item);
                list.append("" +
                    "<li class='border-bottom' data-id='" + item.id + "'>" +
                    "   <div class='d-flex'>" +
                    "   <a class='dropdown-item h6 mb-0' data-barcode='"+item.barcode+"' href='#' style='padding-top:5px!important'>"+ item.name + "</a>" +
                    "</div>" +
                    "</li>");
            });

        }


        //function hideModal () { $("#"+modal_name).modal("hide"); }
      
        function showModal () { $("#"+modal_name).modal("show"); }


    })();


</script>