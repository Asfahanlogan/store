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
            
            // Initialize charts if they exist
            initializeCharts();
        });
        
        // Initialize Chart.js charts
        function initializeCharts() {
            // Payment Methods Pie Chart
            const paymentMethodsChart = document.getElementById('paymentMethodsChart');
            if (paymentMethodsChart) {
                const ctx = paymentMethodsChart.getContext('2d');
                const cryptoStats = <?php echo json_encode($crypto_stats ?? []); ?>;
                
                if (Object.keys(cryptoStats).length > 0) {
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: Object.keys(cryptoStats),
                            datasets: [{
                                data: Object.values(cryptoStats),
                                backgroundColor: [
                                    '#f59e0b', // Warning (BTC)
                                    '#06b6d4', // Info (ETH)
                                    '#8b5cf6', // Secondary (LTC)
                                    '#10b981', // Success
                                    '#ef4444'  // Danger
                                ],
                                borderWidth: 2,
                                borderColor: '#1e293b'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: '#e2e8f0',
                                        font: {
                                            size: 12
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }
            
            // Sales Line Chart
            const salesChart = document.getElementById('salesChart');
            if (salesChart) {
                const ctx = salesChart.getContext('2d');
                const payments = <?php echo json_encode($payments ?? []); ?>;
                
                if (payments.length > 0) {
                    // Group payments by date
                    const salesByDate = {};
                    payments.forEach(payment => {
                        const date = payment.created_at.split(' ')[0];
                        if (!salesByDate[date]) {
                            salesByDate[date] = 0;
                        }
                        salesByDate[date] += parseFloat(payment.amount);
                    });
                    
                    const dates = Object.keys(salesByDate).sort();
                    const sales = dates.map(date => salesByDate[date]);
                    
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: dates.map(date => new Date(date).toLocaleDateString()),
                            datasets: [{
                                label: 'Daily Sales ($)',
                                data: sales,
                                borderColor: '#6366f1',
                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#6366f1',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    labels: {
                                        color: '#e2e8f0'
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        color: '#e2e8f0'
                                    },
                                    grid: {
                                        color: 'rgba(226, 232, 240, 0.1)'
                                    }
                                },
                                y: {
                                    ticks: {
                                        color: '#e2e8f0',
                                        callback: function(value) {
                                            return '$' + value.toFixed(2);
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(226, 232, 240, 0.1)'
                                    }
                                }
                            }
                        }
                    });
                }
            }
        }
    </script>
</body>
</html>