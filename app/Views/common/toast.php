
<?php 
	$session    = \Config\Services::session();
	$error 		= $session->getFlashdata('error'); 
	$success 	= $session->getFlashdata('success');
	$warning 	= $session->getFlashdata('warning');
?>
        
<div id="successToast" class="d-none toast-container position-fixed bottom-0 end-1 z-index-2 mb-4" >
    <div class="toast fade show p-2 bg-white" role="alert" aria-live="assertive"  aria-atomic="true">
        <div class="toast-header border-0">
            
            <i class="bi bi-check-lg text-success me-2"></i>
            <small class="text-body"><? lang('Notification.system_message') ?></small>
            <span class="me-auto text-gradient text-success font-weight-bold"><?= lang('Notification.title_success') ?></span>
            
            <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast" aria-label="Close"></i>
        </div>
        <hr class="horizontal dark m-0">
        <div class="toast-body"></div>
    </div>
</div>

<div id="warningToast" class="d-none toast-container position-fixed bottom-0 end-1 z-index-2 mb-4">
    <div class="toast fade p-2 mt-2 bg-white show" role="alert" aria-live="assertive"  aria-atomic="true">
        <div class="toast-header border-0">
            <i class="material-icons text-warning me-2">campaign</i>
            <span class="me-auto text-gradient text-warning font-weight-bold"><?= lang('Notification.title_warning') ?></span>
            <small class="text-body"><?php lang('Notification.console_notification') ?></small>
            <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast" aria-label="Close" aria-hidden="true"></i>
        </div>
        <hr class="horizontal dark m-0">
        <div class="toast-body"></div>
    </div>
</div>

<div id="errorToast" class="d-none toast-container position-fixed bottom-0 end-1 z-index-2 mb-4">
    <div class="toast fade p-2 mt-2 bg-white show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header border-0">
            <i class="bi bi-x-octagon-fill text-danger pe-2"></i> - 
            <span class="me-auto text-gradient text-danger font-weight-bold ps-2"><?= lang('Notification.title_error') ?></span>
            <small class="text-body"><?php lang('Notification.console_notification') ?></small>
            <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast" aria-label="Close" aria-hidden="true"></i>
        </div>
        <hr class="horizontal dark m-0">
        <div class="toast-body"></div>
    </div>
</div>

<script>
    
    document.addEventListener("DOMContentLoaded", function () {
        <?php if (isset($success) && strlen($success) > 5): ?>
            show_toast('<?= esc($success) ?>');
        <?php endif; ?>

        <?php if (isset($warning) && strlen($warning) > 5): ?>
            show_toast('<?= esc($warning) ?>', 'warning');
        <?php endif; ?>

        <?php if (isset($error) && strlen($error) > 5): ?>
            show_toast('<?= esc($error) ?>', 'error');
        <?php endif; ?>

    });
  
    function show_toast(message, type="success") {
        
        let $toast = '';

        if ( type == "success" ) { $toast = $("#successToast");}
        else if ( type == "warning" ) { $toast = $("#warningToast"); }
        else { $toast = $("#errorToast"); }

        $toast.find(".toast-body").html(message);
        $toast.removeClass("d-none").stop(true, true).fadeOut(10).fadeIn(500);

        setTimeout(() => {$toast.fadeOut(1000); }, 4000);
    }
    
</script>
