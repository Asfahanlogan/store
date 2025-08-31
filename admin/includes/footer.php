                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Confirm before deleting
        function confirmDelete(message = 'Are you sure you want to delete this item?') {
            return confirm(message);
        }
        
        // Copy to clipboard
        function copyToClipboard(text, button) {
            navigator.clipboard.writeText(text).then(() => {
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
                button.classList.remove('btn-outline-secondary');
                button.classList.add('btn-success');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-secondary');
                }, 2000);
            });
        }
        
        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (!alert.classList.contains('alert-danger')) {
                    setTimeout(() => {
                        alert.style.transition = 'opacity 0.5s';
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            alert.remove();
                        }, 500);
                    }, 5000);
                }
            });
        });
    </script>
</body>
</html>