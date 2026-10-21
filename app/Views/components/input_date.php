<?php 
    $placeholder    = isset($placeholder) ? $placeholder : '';
    $name           = isset($name) ? esc($name) : 'number-input';
    $add_delete_btn = (isset($add_delete_btn) && $add_delete_btn);
    $class          = isset($class) ? $class : '' ;
    
?>

    <div class="input-group <?= $class ?>">
        
        <!-- The actual input field -->
        <input  type="text"  id="<?= $name ?>" name="<?= $name ?>" class="form-control" autocomplete="off" readonly placeholder="<?= $placeholder ?>">

        <?php if ( $add_delete_btn ): ?>
            <button id="<?= $name ?>-btnClear" class="btn btn-outline-danger btn-medium">x</button>
        <?php endif; ?>
    </div>
           



    <script>
        $(function () {
            
            // Clear barcode button
            <?php if ( $add_delete_btn ): ?>
                $(document).on('click', "#<?= $name ?>-btnClear",  function() {
                    $(this).prev().val('') 
                    $(this).removeClass('btn-danger').addClass('btn-outline-secondary');
                });
            <?php endif; ?>

             $("#<?= $name ?>").datepicker({ 
                dateFormat: "dd-mm-yy",
                changeMonth: true,
                changeYear: true,
                yearRange: "2000:2150"
            });
            
        });
    </script>
