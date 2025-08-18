        </div> <!-- End Content Area -->
    </div> <!-- End Main Content -->
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Custom Admin JavaScript -->
    <script>
        $(document).ready(function() {
            // Sidebar toggle
            $('#sidebarToggle').click(function() {
                $('#sidebar').toggleClass('collapsed');
            });
            
            // Mobile sidebar toggle
            if (window.innerWidth <= 768) {
                $('#sidebarToggle').click(function() {
                    $('#sidebar').toggleClass('show');
                });
                
                // Close sidebar when clicking outside
                $(document).click(function(e) {
                    if (!$(e.target).closest('#sidebar, #sidebarToggle').length) {
                        $('#sidebar').removeClass('show');
                    }
                });
            }
            
            // Initialize DataTables
            if ($('.data-table').length) {
                $('.data-table').DataTable({
                    responsive: true,
                    pageLength: 25,
                    order: [[0, 'desc']],
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    }
                });
            }
            
            // Auto-hide alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
            
            // Confirm delete actions
            $('.btn-delete').click(function(e) {
                if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
            
            // Form validation
            $('form').submit(function() {
                $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            });
            
            // Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Status update via AJAX
            $('.status-toggle').change(function() {
                var checkbox = $(this);
                var id = checkbox.data('id');
                var table = checkbox.data('table');
                var status = checkbox.is(':checked') ? 1 : 0;
                
                $.ajax({
                    url: 'ajax/update_status.php',
                    method: 'POST',
                    data: {
                        id: id,
                        table: table,
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            showNotification('Status updated successfully', 'success');
                        } else {
                            showNotification('Error updating status', 'error');
                            checkbox.prop('checked', !checkbox.is(':checked'));
                        }
                    },
                    error: function() {
                        showNotification('Error updating status', 'error');
                        checkbox.prop('checked', !checkbox.is(':checked'));
                    }
                });
            });
        });
        
        // Show notification function
        function showNotification(message, type = 'info') {
            var alertClass = 'alert-info';
            var icon = 'fa-info-circle';
            
            switch(type) {
                case 'success':
                    alertClass = 'alert-success';
                    icon = 'fa-check-circle';
                    break;
                case 'error':
                    alertClass = 'alert-danger';
                    icon = 'fa-exclamation-triangle';
                    break;
                case 'warning':
                    alertClass = 'alert-warning';
                    icon = 'fa-exclamation-triangle';
                    break;
            }
            
            var notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
                '<i class="fas ' + icon + ' me-2"></i>' + message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                '</div>');
            
            $('body').append(notification);
            
            setTimeout(function() {
                notification.fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 5000);
        }
        
        // Format numbers
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        
        // Format currency
        function formatCurrency(amount) {
            return 'RWF ' + formatNumber(amount);
        }
        
        // Confirm action
        function confirmAction(message, callback) {
            if (confirm(message)) {
                callback();
            }
        }
        
        // Export table data
        function exportTable(tableId, filename) {
            var table = document.getElementById(tableId);
            var csv = [];
            var rows = table.querySelectorAll('tr');
            
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll('td, th');
                
                for (var j = 0; j < cols.length; j++) {
                    row.push(cols[j].innerText);
                }
                
                csv.push(row.join(','));
            }
            
            var csvFile = new Blob([csv.join('\n')], {type: 'text/csv'});
            var downloadLink = document.createElement('a');
            downloadLink.download = filename + '.csv';
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
        
        // Print page
        function printPage() {
            window.print();
        }
        
        // Refresh page
        function refreshPage() {
            location.reload();
        }
    </script>
    
    <?php if (isset($additional_js)): ?>
        <?php echo $additional_js; ?>
    <?php endif; ?>
</body>
</html>
