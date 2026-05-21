/**
 * Advanced School Management System ERP
 * Main JavaScript File
 */

$(document).ready(function() {
    
    // Sidebar Toggle
    $('#sidebarToggle, #sidebarToggleBottom').on('click', function() {
        $('.sidebar').toggleClass('toggled');
        $('#content-wrapper').toggleClass('toggled');
        
        // Save state
        const isToggled = $('.sidebar').hasClass('toggled');
        localStorage.setItem('sidebarToggled', isToggled);
    });
    
    // Restore sidebar state
    if (localStorage.getItem('sidebarToggled') === 'true') {
        $('.sidebar').addClass('toggled');
        $('#content-wrapper').addClass('toggled');
    }
    
    // Mobile sidebar toggle
    $(document).on('click', function(e) {
        if ($(window).width() < 992) {
            if (!$(e.target).closest('.sidebar').length && !$(e.target).closest('#sidebarToggle').length) {
                $('.sidebar').removeClass('show');
            }
        }
    });
    
    // Scroll to top button
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 100) {
            $('.scroll-to-top').fadeIn();
        } else {
            $('.scroll-to-top').fadeOut();
        }
    });
    
    $('.scroll-to-top').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop: 0}, 300);
    });
    
    // Tooltip initialization
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Popover initialization
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Form validation
    $('form.needs-validation').on('submit', function(event) {
        if (this.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        $(this).addClass('was-validated');
    });
    
    // Confirm delete actions
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        var title = $(this).data('title') || 'Delete Item';
        var text = $(this).data('text') || 'Are you sure you want to delete this item? This action cannot be undone.';
        
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });
    
    // Auto-hide flash messages
    setTimeout(function() {
        $('.alert-dismissible').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
    
    // File input preview
    $(document).on('change', '.file-input-preview', function() {
        var input = this;
        var previewId = $(this).data('preview');
        
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#' + previewId).attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    });
    
    // Copy to clipboard
    $(document).on('click', '.btn-copy', function() {
        var text = $(this).data('copy');
        navigator.clipboard.writeText(text).then(function() {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Text copied to clipboard',
                timer: 1500,
                showConfirmButton: false
            });
        });
    });
    
    // Dynamic class/section loading
    $(document).on('change', '.class-select', function() {
        var classId = $(this).val();
        var sectionSelect = $(this).closest('form').find('.section-select');
        
        if (classId && sectionSelect.length) {
            sectionSelect.prop('disabled', true);
            $.ajax({
                url: BASE_URL + 'ajax/get-sections.php',
                type: 'POST',
                data: { class_id: classId },
                dataType: 'json',
                success: function(response) {
                    sectionSelect.empty().append('<option value="">Select Section</option>');
                    if (response.success && response.sections) {
                        $.each(response.sections, function(i, section) {
                            sectionSelect.append('<option value="' + section.section_id + '">' + section.section_name + '</option>');
                        });
                    }
                    sectionSelect.prop('disabled', false);
                }
            });
        }
    });
    
    // Dynamic subject loading
    $(document).on('change', '.class-select-subject', function() {
        var classId = $(this).val();
        var subjectSelect = $(this).closest('form').find('.subject-select');
        
        if (classId && subjectSelect.length) {
            subjectSelect.prop('disabled', true);
            $.ajax({
                url: BASE_URL + 'ajax/get-subjects.php',
                type: 'POST',
                data: { class_id: classId },
                dataType: 'json',
                success: function(response) {
                    subjectSelect.empty().append('<option value="">Select Subject</option>');
                    if (response.success && response.subjects) {
                        $.each(response.subjects, function(i, subject) {
                            subjectSelect.append('<option value="' + subject.subject_id + '">' + subject.subject_name + '</option>');
                        });
                    }
                    subjectSelect.prop('disabled', false);
                }
            });
        }
    });
    
    // Number input validation
    $(document).on('input', '.number-only', function() {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
    });
    
    // Decimal input validation
    $(document).on('input', '.decimal-only', function() {
        $(this).val($(this).val().replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1'));
    });
    
    // Phone input formatting
    $(document).on('input', '.phone-input', function() {
        var val = $(this).val().replace(/\D/g, '');
        if (val.length > 10) val = val.substring(0, 15);
        $(this).val(val);
    });
    
    // Date picker initialization
    if ($('.datepicker').length) {
        initDatePicker('.datepicker');
    }
    
    // Select2 initialization
    if ($('.select2').length) {
        initSelect2('.select2');
    }
    
    // DataTable initialization for tables with .datatable class
    if ($('.datatable').length) {
        initDataTable('.datatable');
    }
    
    // Chart.js defaults
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Nunito', sans-serif";
        Chart.defaults.font.size = 11;
        Chart.defaults.color = '#858796';
    }
    
    // Bulk select checkbox
    $('#selectAll').on('change', function() {
        $('.select-row').prop('checked', $(this).is(':checked'));
    });
    
    // Form reset confirmation
    $(document).on('click', '.btn-reset', function(e) {
        var form = $(this).closest('form');
        if (form.find('input:not([type=hidden]), select, textarea').filter(function() {
            return $(this).val() !== '';
        }).length > 0) {
            e.preventDefault();
            Swal.fire({
                title: 'Reset Form?',
                text: 'All entered data will be cleared.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#858796',
                cancelButtonColor: '#4e73df',
                confirmButtonText: 'Yes, reset',
                cancelButtonText: 'Keep changes'
            }).then((result) => {
                if (result.isConfirmed) {
                    form[0].reset();
                }
            });
        }
    });
    
    // AJAX loading indicator
    $(document).ajaxStart(function() {
        $('body').append('<div class="ajax-loading"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
    }).ajaxStop(function() {
        $('.ajax-loading').remove();
    });
    
    // Search autocomplete delay
    var searchTimeout;
    $(document).on('input', '.search-autocomplete', function() {
        clearTimeout(searchTimeout);
        var input = $(this);
        searchTimeout = setTimeout(function() {
            var query = input.val();
            if (query.length >= 2) {
                var target = input.data('target');
                $(target).load(input.data('url'), { query: query });
            }
        }, 300);
    });
    
    // Toggle password visibility
    $(document).on('click', '.toggle-password', function() {
        var input = $($(this).data('target'));
        var icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Keep session alive
    setInterval(function() {
        $.get(BASE_URL + 'ajax/keep-session.php');
    }, 600000); // Every 10 minutes
    
});

// ====================
// Utility Functions
// ====================

function formatNumber(num, decimals = 0) {
    return parseFloat(num).toFixed(decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

function formatCurrency(amount, symbol = '$') {
    return symbol + formatNumber(amount, 2);
}

function formatDate(date, format = 'MMM DD, YYYY') {
    const d = new Date(date);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    const replacements = {
        'YYYY': d.getFullYear(),
        'MM': String(d.getMonth() + 1).padStart(2, '0'),
        'MMM': months[d.getMonth()],
        'DD': String(d.getDate()).padStart(2, '0'),
        'HH': String(d.getHours()).padStart(2, '0'),
        'mm': String(d.getMinutes()).padStart(2, '0'),
        'ss': String(d.getSeconds()).padStart(2, '0')
    };
    
    return format.replace(/YYYY|MM|MMM|DD|HH|mm|ss/g, match => replacements[match]);
}

function debounce(func, wait) {
    var timeout;
    return function executedFunction() {
        var context = this;
        var args = arguments;
        var later = function() {
            timeout = null;
            func.apply(context, args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showLoading(element) {
    $(element).addClass('loading').prop('disabled', true);
    $(element).data('original-text', $(element).html());
    $(element).html('<span class="spinner-border spinner-border-sm me-2"></span> Loading...');
}

function hideLoading(element) {
    $(element).removeClass('loading').prop('disabled', false);
    var originalText = $(element).data('original-text');
    if (originalText) {
        $(element).html(originalText);
    }
}
