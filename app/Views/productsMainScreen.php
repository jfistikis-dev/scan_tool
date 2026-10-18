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
                    <div class="card-body" >
                        <div class="row mb-3">
                            <div class="col-md-8 d-flex align-items-center col-sm-12" >
                                <div class="input-group">
                                    <input type="text" id="searchName" class="form-control" placeholder="Εύρεση από περιγραφής...">
                                    <button id="btnSearch" class="btn btn-primary btn-medium">Αναζήτηση</button>
                                    <button id="btnClear" class="btn btn-outline-secondary btn-medium">Καθάρισμός</button>
                                </div>
                            </div>
                            <div class="col-md-4 text-end d-flex flex-column align-items-end col-sm-12">

                                <a id="exportBtn" class="btn btn-success btn-medium mb-2"  href="#">Export to Excel</a>
                                <a id="importBtn" class="btn btn-info btn-medium text-white"  href="#">Import from Excel</a>
                            </div>
                                    
                        </div>
                        <div clas="row">
                            <div class="col-md-12">
                                <!-- Import upload area  -->
                                
                                <div id="importUploadArea">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="fas fa-upload"></i> Εισαγωγή Αρχείου Excel</h6>
                                        </div>
                                        <div class="card-body text-center p-0" id="dropZone">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                                            <p class="lead">Σύρετε και αποθέστε το αρχείο Excel εδώ</p>
                                            <p class="text-muted">ή κάντε κλικ για επιλογή αρχείου</p>
                                            <input type="file" id="excelFile" accept=".xlsx,.xls" class="d-none">
                                            <button type="button" id="browseFile" class="btn btn-outline-primary mt-2">
                                                <i class="fas fa-search"></i> Περιήγηση
                                            </button>
                                        </div>
                                        <div class="card-footer">
                                            <div id="uploadProgress" class="d-none">
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                                        role="progressbar" style="width: 0%"></div>
                                                </div>
                                                <p class="text-center mt-2 mb-0" id="progressText">Ανέβασμα αρχείου...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            
                        </div>
                        <hr/>
                        <table id="productTable" class="table table-responsive display mb-2 fs-7" style="width:100%">
                            <thead>
                                <tr>
                                    <th data-data="id">ID</th>
                                    <th data-data="barcode">Barcode</th>
                                    <th data-data="description">Περιγραφή</th>
                                    <th data-data="stock">Στοκ</th>
                                    <th data-data="measuring_unit">Μ.Μέτρησης</th>
                                    <th data-data="reorder_quantity">Ποσ. αναπ/λιας</th>
                                    <th data-data="buying_price">Τιμή αγοράς</th>
                                    <th data-data="actions">Ενέργειες</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
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



<?=$this->endSection()?>
