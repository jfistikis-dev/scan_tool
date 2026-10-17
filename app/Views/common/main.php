<!DOCTYPE html>
<html lang="en">
<head>

	<meta charset="UTF-8">
	<title><?= esc($title) ?></title>
	<meta name="description" content="The small framework with powerful features">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" type="image/png" href="/favicon.ico"/>
	
	<link rel="stylesheet" href='<?php echo base_url("css/retail.css"); ?>'>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


    <!-- jQuery first, then Bootstrap JS -->
    <script src="<?php echo base_url("js/jquery-3.6.0.min.js") ?>"></script>
    
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url("css/bootstrap/bootstrap.min.css" ) ?>" type="text/css"/>
    <script src="<?= base_url("js/bootstrap/bootstrap.bundle.min.js" ) ?>" ></script>
    
    <!-- Multiselect Bootstrap -->
    <script type="text/javascript" src="<?php echo base_url("js/bootstrap/bootstrap-multiselect.js") ?>" ></script>
    <link rel="stylesheet" href="<?php echo base_url("css/bootstrap/bootstrap-multiselect.css" ) ?>" type="text/css"/>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="<?= base_url("css/bootstrap/bootstrap-icons.css" ) ?>">
       
    <!-- input mask -->
    <script type="text/javascript" src="<?php echo base_url("js/jquery.inputmask.min.js") ?>" ></script> 
    <script type="text/javascript" src="<?php echo base_url("js/number-functions.js?v=" . filemtime(FCPATH . "js/number-functions.js")) ?>" ></script>

    <!-- Datatables -->
    <link rel="stylesheet" href="<?= base_url("css/dataTables.min.css") ?>">
    <script type="text/javascript" src="<?php echo base_url("js/dataTables.min.js") ?>" ></script> 
    
    <!-- Jquery UI -->
    <script src="<?= base_url("js/jquery-ui.min.js") ?>"></script>

    <script src="<?= base_url("js/sweetalert2.all.js") ?>"></script>

</head>
 
 
<!-- BODY & CONTENTS -->
<body >
    
	<div class="container-fluid h-100" >
	    
        <!-- Main content -->
        <?= $this->renderSection("content"); ?>
    
    </div>
</body>


 
<!-- FOOTER: DEBUG INFO + COPYRIGHTS -->
<footer class="text-center  bg-light text-muted fixed-bottom p-2 fs-7">
    &copy; <?= date('Y') ?> — Έξυπνη διαχείριση προϊόντων με barcode. Απλή. Γρήγορη. Αποτελεσματική. 
</footer>



</body>
</html>