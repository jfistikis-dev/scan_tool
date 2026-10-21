<?=$this->extend("common/main")?>

<?=$this->section("content")?>
	
	
	<!-- HEADER TOP BLACK -->
	<div class="row navbar navbar-dark sticky-top bg-dark p-0 shadow">&nbsp;</div>
		
	<div class="p-3 g-3 " >

        <!-- Title & Exit buttons -->
        <div class="d-flex pb-2 justify-content-between position-relative" >

            <div class="display-5">
                <button class="btn btn-primary fs-3" type="button" data-bs-toggle="offcanvas" data-bs-backdrop="true" data-bs-target="#basketOffCanvas" aria-controls="basketOffCanvas">
                    <i class="bi bi-card-checklist" style="pointer-events: none;"></i>
                </button>
                - Προϊοντα
            </div>
            
            <div class="card border-0">
                <button onclick="location=window.location='<?= base_url(); ?>'" class="btn btn-primary mt-2 btn-lg gap-2 btn-exit" type="button">
                    <i class="bi bi-arrow-bar-left"></i> Έξοδος
                </button>
            </div>
        </div>
        
        <div class="border rounded row p-3">
            <!-- MAIN PANEL -->
            <div class="col-md-8 " >
                <div class="card mt-3 h-100 mh-100" > 
                   
                    <div class="card-body">

                        <!-- Tabs -->
                        <ul class="nav nav-tabs mb-3" id="productSearchTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active"
                                        id="text-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#textSearchPane"
                                        type="button"
                                        role="tab">
                                    <i class="bi bi-search me-1"></i>
                                    Αναζήτηση προϊόντων
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link"
                                        id="date-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#dateSearchPane"
                                        type="button"
                                        role="tab">
                                    <i class="bi bi-calendar-range me-1"></i>
                                    Αναζήτηση βάσει ημερομηνίας
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="productSearchTabContent">

                            <!-- TAB 1: Existing text search -->
                            <div class="tab-pane fade show active"
                                id="textSearchPane"
                                role="tabpanel">

                                <div class="row mb-3 g-2">
                                    <div class="col-md-8 col-sm-12">
                                        <div class="input-group">
                                            <input type="text"
                                                id="searchName"
                                                class="form-control"
                                                placeholder="Εύρεση από περιγραφή...">

                                            <button id="btnSearch"
                                                    type="button"
                                                    class="btn btn-primary">
                                                <i class="bi bi-search"></i>
                                            </button>

                                            <button id="btnClear"
                                                    type="button"
                                                    class="btn btn-outline-danger">
                                                x
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12 d-flex justify-content-md-end gap-2">
                                        <a id="exportBtn"
                                        class="btn btn-success"
                                        href="#">
                                            <i class="bi bi-download me-1"></i>
                                            Εξαγωγή
                                        </a>

                                        <button id="importBtn"
                                                type="button"
                                                class="btn btn-info text-white">
                                            <i class="bi bi-upload me-1"></i>
                                            Εισαγωγή
                                        </button>
                                    </div>
                                </div>

                                <!-- Existing import area -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div id="importUploadArea">
                                            <div class="card border-primary">
                                                <div class="card-header bg-primary text-white">
                                                    <h6 class="mb-0">
                                                        <i class="fas fa-upload"></i>
                                                        Εισαγωγή Αρχείου Excel
                                                    </h6>
                                                </div>

                                                <div class="card-body text-center p-3"
                                                    id="dropZone">
                                                    <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                                                    <p class="lead">
                                                        Σύρετε και αποθέστε το αρχείο Excel εδώ
                                                    </p>
                                                    <p class="text-muted">
                                                        ή κάντε κλικ για επιλογή αρχείου
                                                    </p>

                                                    <input type="file"
                                                        id="excelFile"
                                                        accept=".xlsx,.xls"
                                                        class="d-none">

                                                    <button type="button"
                                                            id="browseFile"
                                                            class="btn btn-outline-primary mt-2">
                                                        <i class="fas fa-search"></i>
                                                        Περιήγηση
                                                    </button>
                                                </div>

                                                <div class="card-footer">
                                                    <div id="uploadProgress" class="d-none">
                                                        <div class="progress">
                                                            <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                                role="progressbar"
                                                                style="width: 0%">
                                                            </div>
                                                        </div>
                                                        <p class="text-center mt-2 mb-0"
                                                        id="progressText">
                                                            Ανέβασμα αρχείου...
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- Existing product table -->
                                <div class="table-responsive">
                                    <table id="productTable"
                                        class="table display mb-2 fs-7"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Barcode</th>
                                                <th>Περιγραφή</th>
                                                <th>Στοκ</th>
                                                <th>Μ.Μέτρησης</th>
                                                <th>Ποσ. αναπ/λιας</th>
                                                <th>Τιμή αγοράς</th>
                                                <th>Ενέργειες</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>

                            </div>

                            <!-- TAB 2: Date search -->
                            <div class="tab-pane fade"
                                id="dateSearchPane"
                                role="tabpanel">

                                <div class="row g-3 align-items-end mb-3">

                                    <div class="col-md-3 col-sm-12">
                                        <?= view("components/input_date", [
                                            'placeholder'    => "Ημερ/νια από ...",
                                            'name'           => 'from_date',
                                            'add_delete_btn' => true,
                                            'class'          => 'pe-2'
                                        ]) ?>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <?= view("components/input_date", [
                                            'placeholder'    => "Ημερ/νια έως ...",
                                            'name'           => 'to_date',
                                            'add_delete_btn' => true,
                                            'class'          => 'pe-2'
                                        ]) ?>
                                    </div>
                                    
                                    <div class="form-check col-md-2 col-sm-12 mb-2">
                                        <input class="form-check-input" type="checkbox" name="groupByProduct" id="groupByProduct">
                                        <label class="form-check-label" for="groupByProduct">
                                           Ομαδοποίηση
                                        </label>
                                    </div>

                                    <div class="col-md-4 col-sm-12 d-flex gap-2">
                                        <button type="button"
                                                id="btnDateSearch"
                                                class="btn btn-primary">
                                            <i class="bi bi-search me-1"></i>
                                            Αναζήτηση
                                        </button>

                                        <a href="#"
                                        id="exportDateBtn"
                                        class="btn btn-success">
                                            <i class="bi bi-download me-1"></i>
                                            Εξαγωγή
                                        </a>
                                    </div>

                                </div>
                              
                                <hr>

                                <!-- Independent date-filtered table -->
                               <div class="table-responsive">
                                    <table id="dateProductTable"
                                        class="table display table-striped table-hover mb-2 fs-7"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Ημερομηνία</th>
                                                <th>Προϊόν</th>
                                                <th>Αρχικό απόθεμα</th>
                                                <th>Ποσότητα</th>
                                                <th>Τελικό απόθεμα</th>
                                                <th>Τιμή μονάδας</th>
                                                <th>Αξία συναλλαγής</th>
                                                <th>Τύπος</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>

                            </div>

                        </div>
                    </div>
	
                </div>
            </div>
            <!-- RIGHT PANEL -->
            <div class="col-md-4">

                <!-- Product Log -->
                <div class="card mt-3" >
                    <div class="card-body" >
                        <?= view("components/product_log", [
                        'url'       => base_url('products/ajaxBarcodeSearch/'),
                        'height'    => 'calc(100vh - 350px)'
                    ]) ?>
                    </div>
                        
                </div>

            
            </div>
            
        </div>

        <!-- Toast messages -->
		<?= $this->include('common/toast') ?>

    </div>



    <script>

        $(document).ready(function(){
            
            let barcode = '';
            let timer;
            
            var table = $('#productTable').DataTable({
                serverSide: true,
                processing: true,
                searching: false,
                ajax: {
                    url: '<?= base_url('/products/ajaxList' ) ?>',
                    type: 'POST',
                    data: function(d){
                        // DataTables sends lots of params; we only need to attach name filter
                        d.search.value = $('#searchName').val();
                    }
                },
                columns: [
                    { data: 'id' },
                    { data: null, 
                         className: 'text-start',
                        render : function (data, type, row) {
                            const barcodeValid =  /^\d{10,13}$/.test(row.barcode);
                            return barcodeValid ? 
                                '<button data-barcode="'+ row.barcode +'" class="btn btn-outline-secondary smallBtn pdfDownloadBarcode"><i class="bi bi-upc-scan"></i></button>' + row.barcode :
                                row.barcode ; 
                           
                        }
                    },
                    { data: 'description' },
                    { data: null, 
                        orderable: true,
                        searchable: false,
                        render: function(data, type, row){
                            return SFNumberFunctions._displayNumber(row.stock) ;
                        }
                    },
                    { data: 'measuring_unit' },
                    { data: null, 
                        orderable: true,
                        searchable: false,
                        render: function(data, type, row){
                            return SFNumberFunctions._displayNumber(row.reorder_quantity) ;
                        }
                    },
                    { data: null, 
                        orderable: true,
                        searchable: false,
                        render: function(data, type, row){
                            return SFNumberFunctions._displayNumber(row.buying_price) ;
                        }
                    },

                    {
                        data: null,
                        title: 'Ενέργειες',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row){
                            return `
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-danger btn-delete fs-7" data-id="${row.id}">Διαγραφή</button>
                                </div>
                            `;
                        }
                    }
                ],
                pageLength: 10,
                lengthMenu: [ 10, 25, 50, 100 ],
                order: [[0, 'desc']],
            });
                
            // Search button
            $('#btnSearch').on('click', function(){
                table.ajax.reload();
            });

            // Clear button
            $('#btnClear').on('click', function(){
                $('#searchName').val('');
                table.ajax.reload();
            });

            // Barcode scanner on search
            $(document).on('keydown', function(e) {
                // Reset timer if the user types/scans again soon
                clearTimeout(timer);

                // Check if key is Enter or Tab — scanner done
                if (e.key === 'Enter' || e.key === 'Tab') {
                    if (barcode.length > 3) { // avoid false triggers
                        
                        console.log('Scanned barcode:', barcode);
                        
                        $barcodeStr = barcode.substring (1);
                        $("#searchName").val($barcodeStr);
                        table.ajax.reload();
                    
                    }
                    barcode = '';
                    return;
                }

                // Ignore special keys
                if (e.key.length === 1) {
                    barcode += e.key;
                }

                // Reset if no key pressed for 100ms (manual typing cutoff)
                timer = setTimeout(() => barcode = '', 100);
            });
        
            // Export button
            $('#exportBtn').on('click', function(e){
                e.preventDefault();
                var s = $('#searchName').val();
                var url = '<?php echo base_url() ?>/products/export' + (s ? ('?search=' + encodeURIComponent(s)) : '');
                window.location.href = url;
            });

            // handle delete
            $('#productTable').on('click', '.btn-delete', function( event ){
                
                event.preventDefault();
                event.stopPropagation();

                var id = $(this).data('id');

                if (confirm('Είστε σίγουρος για αυτήν την διαγραφή ?')) {
                    $.ajax({
                        url: "<?= base_url ('/products' ) ?>/" + id,
                        type: 'POST',
                        data: { _method: 'DELETE' },
                        success: function(resp){
                            table.ajax.reload(null, false); // reload without resetting page
                            show_toast (resp.success, 'success');
                        },
                        error: function(){
                            show_toast ('Πρόβλημα με την διαγραφή του προϊόντος.', 'error');
                        }
                    });
                }
            });

            // handle product log
            $('#productTable').on('click', 'tr', function(){
                __ajaxGetProductLog ( $(this).find('td:eq(1)').text() );
            })
            
            // download pdf with barcode..
             $('#productTable').on('click', 'tr .pdfDownloadBarcode', async function(){
                
                let barcode = $(this).attr('data-barcode');
                
                try {

                    await printBarcode( barcode  );
                } catch (error) {
                    alert(error.message);
                }
            })
            
            async function printBarcode(number) {

                // Open the window immediately from the user's click
                // to reduce the risk of popup blocking.
                const printWindow = window.open('', '_blank');
                const bu = '<?= site_url("") ?>';
                const barcodeValid = typeof number === "string" && /^\d{10,13}$/.test(number);

                if (!barcodeValid) {
                    throw new Error('Barcode must contain 10-13 digits.');
                }

                if (!printWindow) {
                    throw new Error('Please allow pop-ups for this site.');
                }

               
                try {
                    printWindow.document.write( '<p>Generating barcode PDF...</p>');
                    printWindow.document.close();

                    const response = await fetch( bu + '/api/barcode/pdf', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ number: number })
                    });

                    if (!response.ok) {
                        let message = 'PDF generation failed';

                        try {
                            const result = await response.json();
                            message = result.error || message;
                        } catch {}

                        throw new Error(message);
                    }

                    const blob = await response.blob();
                    const pdfUrl = URL.createObjectURL(blob);

                    printWindow.location.href = pdfUrl;

                    // Release the temporary browser URL after the
                    // document has had time to load.
                    printWindow.addEventListener('pagehide', () => {
                        URL.revokeObjectURL(pdfUrl);
                    }, { once: true });

                    return true;
                } catch (error) {
                    if (!printWindow.closed) {
                        printWindow.close();
                    }

                    throw error;
                }
            }


            


            // upload excel file : #
                       
            let $importBtn = $('#importBtn');
            let $importArea = $('#importUploadArea');
            let $dropZone = $('#dropZone');
            let $fileInput = $('#excelFile');
            let $browseFile = $('#browseFile');
            let $uploadProgress = $('#uploadProgress');
            let $progressBar = $uploadProgress.find('.progress-bar');
            let $progressText = $('#progressText');
            
            // Function to show import area with animation
            function showImportArea() {
                $importArea.removeClass('hiding');
                $importArea.addClass('show');
                
                // Trigger reflow to restart animation
                void $importArea[0].offsetWidth;
            }
            
            // Function to hide import area with animation
            function hideImportArea() {
                $importArea.removeClass('show');
                $importArea.addClass('hiding');
                
                // Remove hiding class after animation completes
                setTimeout(function() {
                    $importArea.removeClass('hiding');
                }, 300);
            }
            
            // Toggle import area with smooth animation
            $importBtn.on('click', function() {
                if ($importArea.hasClass('show')) {
                    hideImportArea();
                } else {
                    showImportArea();
                }
            });
            
            // Browse file button
            $browseFile.on('click', function(e) {
                e.stopPropagation(); // Prevent triggering dropzone click
                $fileInput.click();
            });
            
            // File input change
            $fileInput.on('change', function(e) {
                if (this.files.length > 0) {
                    uploadFile(this.files[0]);
                }
            });
            
            // Drag and drop functionality
            $dropZone.on('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('border border-primary bg-light');
            });
            
            $dropZone.on('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('border border-primary bg-light');
            });
            
            $dropZone.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('border border-primary bg-light');
                
                let files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    uploadFile(files[0]);
                }
            });
            
            // Click to upload (with better event handling)
            $dropZone.on('click', function(e) {
                // Don't trigger if clicking on browse button or file input
                if (!$(e.target).is('#browseFile') && 
                    !$(e.target).is('#excelFile') &&
                    !$(e.target).closest('#browseFile').length) {
                    $fileInput.click();
                }
            });
            
            // Upload function
            function uploadFile(file) {
                // Validate file type
                if (!file.name.match(/\.(xlsx|xls)$/i)) {
                    alert('Παρακαλώ επιλέξτε αρχείο Excel (.xlsx ή .xls)');
                    return;
                }
                
                // Validate file size (max 10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('Το αρχείο είναι πολύ μεγάλο. Μέγιστο μέγεθος: 10MB');
                    return;
                }
                
                // Show progress bar
                $uploadProgress.removeClass('d-none');
                $progressBar.css('width', '0%');
                $progressText.text('Ανέβασμα αρχείου...');
                
                // Prepare form data
                let formData = new FormData();
                formData.append('excel_file', file);
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                
                // Send AJAX request
                $.ajax({
                    url: '<?= site_url("products/import") ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        let xhr = new XMLHttpRequest();
                        
                        // Upload progress
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                let percent = Math.round((e.loaded / e.total) * 100);
                                $progressBar.css('width', percent + '%');
                                $progressText.text('Ανέβασμα αρχείου: ' + percent + '%');
                            }
                        });
                        
                        return xhr;
                    },
                    success: function(response) {
                        try {
                            let result = typeof response === 'string' ? JSON.parse(response) : response;
                            
                            if (result.success) {
                                $progressBar.css('width', '100%');
                                $progressText.html('<span class="text-success">' + result.message + '</span>');
                                
                                show_toast (result.message, 'success');

                                // Hide upload area after 2 seconds and reload page
                                setTimeout(function() {
                                    hideImportArea();
                                    $uploadProgress.addClass('d-none');
                                    table.ajax.reload();
                                }, 2000);
                            } else {
                                $progressText.html('<span class="text-danger">Σφάλμα: ' + result.message + '</span>');
                                $progressBar.css('width', '100%').addClass('bg-danger');
                            }
                        } catch (e) {
                            $progressText.html('<span class="text-danger">Σφάλμα στην επεξεργασία της απάντησης</span>');
                            $progressBar.css('width', '100%').addClass('bg-danger');
                        }
                    },
                    error: function(xhr, status, error) {
                        $progressText.html('<span class="text-danger">Σφάλμα δικτύου: ' + error + '</span>');
                        $progressBar.css('width', '100%').addClass('bg-danger');
                    }
                });
            }
        
        });

    </script>



    <script>
        
    /*
    * TAB 2: Independent date-filtered DataTable
    *
    * Expected backend parameters:
    *   from_date
    *   to_date
    *
    * The endpoint must return the same DataTables JSON structure
    * as ajaxList, with compatible row fields.
    */

    
    var dateTable = $('#dateProductTable').DataTable({
        serverSide: true,
        processing: true,
        searching: false,
        deferLoading: 0,

        ajax: {
            url: '<?= base_url('/products/ajaxListByDate') ?>',
            type: 'POST',
            data: function(d) {
                d.from_date         = $('[name="from_date"]').val();
                d.to_date           = $('[name="to_date"]').val();
                d.groupByProduct = $('[name="groupByProduct"]').is(':checked');
            }
        },

        columns: [
            { data: 'id' },

            {
                data: 'created_at',
                render: function(data, type, row) {
                    if (!data) return '';

                    // Keep original value for sorting and DataTables operations.
                    if (type !== 'display') return data;

                    const parts = data.split(' ');
                    const dateParts = parts[0].split('-');

                    if (dateParts.length !== 3) return data;

                    const formattedDate =
                        dateParts[2] + '-' +
                        dateParts[1] + '-' +
                        dateParts[0];

                    return formattedDate + (parts[1] ? ' ' + parts[1] : '');
                }
            },

            { data: 'description' },

            {
                data: 'old_stock',
                className: 'text-end',
                render: function(data) {
                    return SFNumberFunctions._displayNumber(data);
                }
            },

            {
                data: 'quantity',
                className: 'text-end',
                render: function(data, type, row) {
                    const qty = SFNumberFunctions._displayNumber(data);

                    if (Number(row.type_id) === 1) {
                        return '<span class="text-success fw-bold">+' + qty + '</span>';
                    }

                    if (Number(row.type_id) === 2) {
                        return '<span class="text-danger fw-bold">-' + qty + '</span>';
                    }

                    return qty;
                }
            },

            {
                data: 'new_stock',
                className: 'text-end',
                render: function(data) {
                    return SFNumberFunctions._displayNumber(data);
                }
            },

            {
                data: 'unit_price',
                className: 'text-end',
                render: function(data) {
                    return SFNumberFunctions._displayNumber(data);
                }
            },

            {
                data: 'transaction_value',
                className: 'text-end',
                render: function(data) {
                    return SFNumberFunctions._displayNumber(data);
                }
            },

            {
                data: 'type_id',
                render: function(data) {
                    if (Number(data) === 1) {
                        return '<span class="badge bg-success">Προσθήκη</span>';
                    }

                    if (Number(data) === 2) {
                        return '<span class="badge bg-danger">Αφαίρεση</span>';
                    }

                    return '<span class="badge bg-secondary">Άλλο</span>';
                }
            }
        ],

        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [[1, 'desc']]
    });


    // Search by transaction date
    $('#btnDateSearch').on('click', function() {
        const fromDate = $('[name="from_date"]').val();
        const toDate   = $('[name="to_date"]').val();

        if (!fromDate || !toDate) {
            alert('Παρακαλώ συμπληρώστε και τις δύο ημερομηνίες.');
            return;
        }

        if (fromDate > toDate) {
            alert('Η ημερομηνία από δεν μπορεί να είναι μεταγενέστερη της ημερομηνίας έως.');
            return;
        }

        dateTable.ajax.reload();
    });


    // Export date-filtered transactions
    $('#exportDateBtn').on('click', function(e) {
        e.preventDefault();

        const fromDate = $('[name="from_date"]').val();
        const toDate   = $('[name="to_date"]').val();

        if (!fromDate || !toDate) {
            alert('Παρακαλώ συμπληρώστε και τις δύο ημερομηνίες.');
            return;
        }

        if (fromDate > toDate) {
            alert('Η ημερομηνία από δεν μπορεί να είναι μεταγενέστερη της ημερομηνίας έως.');
            return;
        }

        const params = new URLSearchParams({
            from_date: fromDate,
            to_date: toDate
        });

        window.location.href =
            '<?= base_url('/products/exportByDate') ?>?' + params.toString();
    });



    </script>


<?=$this->endSection()?>
