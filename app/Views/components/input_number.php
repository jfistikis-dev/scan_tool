<?php 
    $placeholder    = isset($placeholder) ? $placeholder : '';
    $name           = isset($name) ? esc($name) : 'number-input';
    
    $label          = isset($label) ? $label : '';
    $size           = isset($size) ? $size : 12;
    $class          = isset($class) ? $class : '';
    $disabled       = (isset($disabled) && $disabled) ? 'disabled' : '';
    $remember_me    = (isset($remember_me) && $remember_me);
    $add_delete_btn = (isset($add_delete_btn) && $add_delete_btn);
    $is_floating    = $label != '';
    
    $min   = isset($min) ? 'data-min="'.$min.'"' : '';
    $max   = isset($max) ? 'data-max="'.$max.'"' : '';
    
    if ( esc(session($name. '-remember-me')) != null ) {
        $value = esc(session($name. '-remember-me'));
    } else if ( isset($value) ) {
        $value   = esc( $value );
    } else {
        $value = '';
    }
    // Generate a unique identifier for this instance
    $uniqueId = $name . '_' . uniqid();
?>

<?php if ( $label != '' ): ?>
    <div class="input-group conponent-input">
<?php endif; ?>

    <?php if ( $remember_me || $is_floating   ): ?>
        <div class="<?=  $is_floating  ? 'form-floating' : '' ?> position-relative">
    <?php endif; ?>

            <!-- Only active checkbox -->
            <?php if ($remember_me): ?>
                <div class="position-absolute top-0 start-0" style="z-index:100;margin-left:-5px;margin-top:-10px">
                    <input type="checkbox" class="form-check-input mt-n2" id="<?= $name ?>-remember-me-checkbox" name="<?= $name ?>-remember-me-checkbox"
                    <?php if (session()->get($name . '-remember-me')) echo 'checked'; ?> >
                </div>
            <?php endif; ?>

            <input type="text"
                id="<?= $uniqueId ?>"
                name="<?= $name ?>"
                value="<?= $value ?>"
                class="number-input <?= $class ?>" 
                autocomplete="off"
                placeholder="<?= $placeholder ?>"
                style="text-align: right;"
                size="<?= $size ?>" 
                <?= $min ?> 
                <?= $max ?>
                <?= $disabled ?>
                >
            <?php if ( $label != '' ): ?>
            <label for="<?= $uniqueId ?>"><?= $label ?></label>
            <?php endif; ?>
        
    <?php if ( $remember_me || $is_floating ): ?>
        </div>
    <?php endif; ?>
        
        <?php if ( $add_delete_btn ): ?>
            <!-- Delete text input button -->
            <button class="btn btn-outline-secondary btn-<?= $name ?>-delete" type="button" id="btn-<?= $name ?>-delete" type="button"><i class="bi bi-arrow-left"></i></button>
        <?php endif; ?>

<?php if ( $label != '' ): ?>
    </div>
<?php endif; ?>


<?php if ( $add_delete_btn ): ?>
    <script>
        $(function () {
            // Clear barcode button
            $('.btn-<?= $name ?>-delete').on('click', function() {
                $(this).parents('.input-group').find('input[type="text"]').val('') 
                $(this).removeClass('btn-danger').addClass('btn-outline-secondary');
            });

            //change color on button IF text input is empty
            $("input[name='<?= $name ?>']").on('input change', function() {
                var button = $(this).parents('.input-group').find('.btn-<?= $name ?>-delete');
                //console.log( button );
                if ($(this).val() == '') {
                    button.removeClass('btn-danger').addClass('btn-outline-secondary');
                } else {
                    button.removeClass('btn-outline-secondary').addClass('btn-danger');
                    $(this).removeClass('is-invalid');
                }
            });
            
        });
    </script>
<?php endif; ?>

<script>
    SFNumberFunctions._initInputFormat("#<?= esc($uniqueId, 'js') ?>");
</script>