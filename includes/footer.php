<?php
/**
 * Page Footer Component
 * Includes all JavaScript files and closing tags
 */
$school = getSchoolInfo();
$schoolName = $school['school_name'] ?? SITE_NAME;
$year = date('Y');
?>
            </div> <!-- End of Main Content -->
            
            <!-- Footer -->
            <footer class="sticky-footer bg-white mt-4">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span class="text-muted">
                            <i class="fas fa-school me-1"></i>
                            <?php echo htmlspecialchars($schoolName); ?> &copy; <?php echo $year; ?> 
                            <span class="mx-2">|</span>
                            <span class="small">School Management System v<?php echo VERSION; ?></span>
                            <span class="mx-2">|</span>
                            <span class="small">All Rights Reserved</span>
                        </span>
                    </div>
                </div>
            </footer>
            
        </div> <!-- End of Content Wrapper -->
    </div> <!-- End of Wrapper -->
    
    <!-- Scroll to Top Button -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap 5 Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    
    <!-- Flatpickr Date Picker -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Main JavaScript -->
    <script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
    
    <!-- Page Specific Scripts -->
    <script>
    // Global configuration
    const BASE_URL = '<?php echo BASE_URL; ?>';
    
    // Initialize DataTables
    function initDataTable(selector, options = {}) {
        const defaults = {
            responsive: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            language: {
                search: '<i class="fas fa-search"></i>',
                searchPlaceholder: 'Search...',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                paginate: {
                    first: '<i class="fas fa-angle-double-left"></i>',
                    previous: '<i class="fas fa-angle-left"></i>',
                    next: '<i class="fas fa-angle-right"></i>',
                    last: '<i class="fas fa-angle-double-right"></i>'
                }
            },
            dom: '<"row mb-3"<"col-md-6"l><"col-md-6 text-end"f>>' +
                 '<"row"<"col-12"tr>>' +
                 '<"row mt-3"<"col-md-5"i><"col-md-7"p>>'
        };
        
        return $(selector).DataTable($.extend(defaults, options));
    }
    
    // Initialize Select2
    function initSelect2(selector = '.select2', options = {}) {
        $(selector).select2($.extend({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Select an option'
        }, options));
    }
    
    // Initialize Flatpickr
    function initDatePicker(selector = '.datepicker', options = {}) {
        flatpickr(selector, $.extend({
            dateFormat: 'Y-m-d',
            allowInput: true
        }, options));
    }
    
    // Show SweetAlert
    function showAlert(type, title, message = '') {
        Swal.fire({
            icon: type,
            title: title,
            text: message,
            confirmButtonText: 'OK',
            confirmButtonColor: '#4e73df'
        });
    }
    
    // Confirm Dialog
    function confirmDialog(title, text, callback) {
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: 'Yes, proceed!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                callback();
            }
        });
    }
    
    // AJAX Helper
    function ajaxRequest(url, data = {}, method = 'POST', successCallback = null, errorCallback = null) {
        $.ajax({
            url: url,
            type: method,
            data: data,
            dataType: 'json',
            success: function(response) {
                if (successCallback) successCallback(response);
            },
            error: function(xhr, status, error) {
                if (errorCallback) {
                    errorCallback(xhr, status, error);
                } else {
                    showAlert('error', 'Error', 'Something went wrong. Please try again.');
                }
            }
        });
    }
    
    // Print function
    function printSection(elementId) {
        const content = document.getElementById(elementId).innerHTML;
        const printWindow = window.open('', '_blank', 'width=800,height=600');
        printWindow.document.write('<html><head><title>Print</title>');
        printWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">');
        printWindow.document.write('<style>@media print { .no-print { display:none; } body { padding:20px; } }</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write(content);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    }
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert-dismissible').fadeOut('slow');
    }, 5000);
    </script>
</body>
</html>
