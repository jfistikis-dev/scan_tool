<!-- Html content -->
<div class="d-flex flex-row position-relative conponent-input">  
    
    <!-- Remember me checkbox -->
    <div class="position-absolute top-0 start-0" style="z-index:100;margin-left:-5px;margin-top:-10px">
        <input type="checkbox" class="form-check-input mt-n2" name="<?= $name ?>-remember-me-checkbox" 
        <?php if (session()->get($name . '-remember-me')) echo 'checked'; ?> >
    </div>
    
    <!-- Select box -->
    <div class="form-floating w-100 ">
        
        <select class="form-select"  id="<?= $name ?>_select" name="<?= $name ?>"  data-name="<?= $v_name ?>" aria-label="Floating label select example">
            <option selected></option>
            
            <?php if (isset( $items ) && !empty($items) && ( !isset($depends_on_id) || strlen($depends_on_id) <= 1) ) { ?>
                <?php foreach ( $items as $item ) : ?>
                    <option value="<?= $item['id'] ?>" <?php if (session()->get($name . '-remember-me')) echo 'selected="selected"'; ?>><?= $item['name'] ?></option>
                <?php endforeach; ?>
            <?php } ?>

        </select>
        
        <label for="<?= $name ?>_select"> Επιλογή <?= $v_name ?> απο λίστα </label>
        
    </div>

    <!-- Button that opens the supplier modal -->
    <div class="pt-2 ps-3">
        <button type="button" class="btn btn-secondary <?= $name ?>-btn-modal" ><i class="bi bi-card-heading"></i></button>
    </div>

</div>

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
                    <input type="text" class="form-control form-control-lg" autocomplete="off" id="<?= $name ?>-modal-input" placeholder="'Ονομα <?= $v_name ?>" aria-label="'Ονομα <?= $v_name ?>" aria-describedby="button-addon2">
                    <button class="btn btn-secondary <?= $name ?>-btn-modal-add" type="button">Προσθήκη</button>

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

        let parent          = null;
        let parent_exists   = false;
        let parent_id       = null;
        let parent_value    = null;
 
        
        <?php if ( isset ( $depends_on_id ) && strlen($depends_on_id) > 1 ) : ?>
            parent          = $("select[name='<?= $depends_on_id ?>']").length > 0 ? $("select[name='<?= $depends_on_id ?>']") : null;
            parent_exists   = parent == null ? false : true;
            parent_id       = parent == null ? null : parent.attr("id");
            parent_value    = null; 
        <?php endif; ?>
        
        let modal_name      = "<?= $name ?>-modal";
        
         // The OPEN MODAL button clicked 
        $("body").on("click", "." + name + "-btn-modal", async function (event) {
           
            if ( parent_exists ) { getParentValue (); }
            if ( parent_exists && parent_value == null ) {
                alert("Παρακαλώ επιλεξτε πρώτα έναν : <?= $v_name ?>!!"); return; 
            }

            await ajaxPopulateModalList(); 
            await ajaxSortModalList();
            
            showModal();
        });

        // the ADD button pressed!! -- 
        $("body").on("click", "." + name + "-btn-modal-add", function (event) {
                        
            var input   = $('#<?= $name ?>-modal-input');

            if ( input.val() == "" ) { alert("Πρέπει να εισαγειτε επιλογη"); return false ;}

            $.ajax({
                type: "post",
                url: "<?= base_url($rest_url  )  ?>/create",
                data: {
                    "name" : input.val(),
                    "parent_id": parent_value 
                 },
                success:function( serverData ) {

                    input.val(""); // delete the entered value since everything went as planned!!
                    if  ( serverData ) {
                        populateModalList ( JSON.parse( serverData ) );
                        updateDropdownList();
                    }

                },
                error:function(){
                    alert("error");
                }

            });

        })

        // The DELETE button of a list item was just pressed
        $("body").on ("click", "#"+modal_name+" ul li a.link-danger", function () {


            if (confirm("<?= $delete_text ?>")) {
                let delete_id = $(this).parents('li').data('id');

                $.ajax({
                    type: "DELETE",
                    url: "<?= base_url($rest_url )  ?>/" + delete_id,
                    success:function( serverData ) {

                        // remove this item from the list
                        populateModalList ( JSON.parse( serverData ) );
                        updateDropdownList();

                    },
                    error:function(){
                        alert("error");
                    }

                });
            } else { return false; }
            

        });

        // An ITEM SELECTED from modal list 
        $("body").on ("click", "#"+modal_name+" ul li a.dropdown-item", function () {

            // set the select box to this value
            $("select[name='<?= $name ?>']").val( $(this).closest("li").data("id") ).change();
            
            hideModal();
        });

        // Populate our list if parent changed
        if ( parent_exists ) {
            $("body").on("change", "#" + parent_id, function () {
                ajaxPopulateModalList( parent_value );
            })
        }

        // When modal closes when only one option ... set it to the select!
        $("#"+modal_name).on("hidden.bs.modal", function () {
            if ( $("#"+modal_name+" ul li").length == 1 ) {
                updateDropdownList();
            }
        });

        $("body").on("change", "#<?= $name ?>_select", function (event) {
            if ( $(this).val() != null && $(this).val() != '' ) {
                $(this).removeClass('is-invalid');
            }
        })

        // when docuemnt is loaded #
        $( document ).ready(function() {
            parent_exists ? setRememberValueToChild() : loadUniqueValueOnDropdown();
        });

        $("#<?= $name ?>_select").data( 'ajaxPopulateList', ajaxPopulateModalList );

        async function ajaxPopulateModalList( done ) {
            
            let url = rest_url + "/" + rest_actions.search;

            if ( parent_exists ) {
                getParentValue ();
                console.log ( rest_actions.search )
                if ( parent_value == null ) { emptyDropdown(); return; }
                url = rest_url + "?" + parent.attr("id") + "=" + parent_value;
            }

                       
            await $.ajax({
                type: "get",
                url: url,
                success: function(serverData) {
                    
                    populateModalList(JSON.parse(serverData));
                    updateDropdownList ( JSON.parse(serverData) );

                }
            });
            if (typeof done === 'function') done();
        }
    
        async function ajaxSortModalList () {

            var list = $("#"+modal_name+" ul.sortable");

            await list.sortable ({
                
                placeholder: "ui-state-highlight",
                cursor: "move",
                axis: 'y',
            
                update: function(event, ui) {
                    // grab IDs in new order
                    let sortedIDs = $(this)
                        .children('li')
                        .map(function() { return $(this).data('id'); })
                        .get();

                    
                    // send to server via AJAX
                    $.ajax({
                        url: "<?= base_url($rest_url . '/'. $rest_actions['sort'])  ?>", // your backend route
                        type: 'POST',
                        data: {
                            sort: sortedIDs,
                            _token: $('meta[name="csrf-token"]').attr('content') // if using Laravel/CSRF
                        },
                        success: function(response) {
                            //console.log('Order saved:', response);
                            updateDropdownList();
                        },
                        error: function(xhr) {
                            console.error('Error saving order:', xhr.responseText);
                        }
                    });
                }
            
            })
        }           
       
        function updateDropdownList ( items = null ) {

            var select = $("#<?= $name ?>_select");
            select.empty();
            select.append( "<option selected></option>" );

            if ( items == null ) { 
                items = []; 
                items = $("#"+modal_name+" ul li").map(function() {
                    return { id: $(this).data('id'), name: $(this).find('a.dropdown-item').text().trim() };
                }).get();
            }

            // fill the ul with available items
            for (let i = 0; i < items.length; i++) {
                select.append( "<option value='" + items[i].id + "'>" + items[i].name + "</option>" );
            }
            
            if ( items.length >= 1 ) { select.removeClass('is-invalid'); }
            loadUniqueValueOnDropdown();
              
        }

        function populateModalList ( list_items ) {

            var list = $("#"+modal_name+" ul");
            list.empty();

            $(list_items).each ( function ( index, item ) {
                
                //console.log(item);
                list.append("" +
                    "<li class='border-bottom' data-id='" + item.id + "'>" +
                    "   <div class='d-flex'>" +
                    "   <a class='dropdown-item h5 mb-0' href='#' style='padding-top:10px!important'><i class='bi bi-arrows-expand'></i> &nbsp;"+ item.name + "</a>" +
                    "   <div class='p-2 mr-3 px-4'><h5><a href='#' class='link-danger'><i class='bi bi-trash'></i></a></h5></div>" +
                    "</div>" +
                    "</li>");
            });

        }

        function getParentValue () {
            if ( parent_exists ) {
                let value    = parent.prop('tagName').toLowerCase() == 'select' ? 
                                        parent.val() : 
                                        parent.text();
                parent_value = value.length > 0 ?  value :  null;
            }
          
        }
        
        function emptyDropdown() {
            $("select[name='<?= $name ?>']").empty();
            $("select[name='<?= $name ?>']").append( "<option selected></option>" );
        }

        function loadUniqueValueOnDropdown () {
            // If there is only one option in the list ... make it the selected one!!
            if ( $("#<?= $name ?>_select option").length == 2 ) {
                $("#<?= $name ?>_select").val( $("#<?= $name ?>_select option:nth-child(2)").val() ).change();
                $("#<?= $name ?>_select").removeClass('is-invalid');
            } 
        }

        function setRememberValueToChild () {
             getParentValue ();
            let remember_value = '<?= session()->get($name . "-remember-me") ?>';
            if ( remember_value == '' || remember_value == null ) return;

            //console.log('remember_value = ' + remember_value);

            if (parent_value == null) {
                emptyDropdown();
                return;
            }

            ajaxPopulateModalList(function() {
                // 👇 runs only after dropdown is ready
                $("#<?= $name ?>_select").val(remember_value).change();
            });
        }

        function hideModal () { $("#"+modal_name).modal("hide"); }
      
        function showModal () { $("#"+modal_name).modal("show"); }


    })();


</script>