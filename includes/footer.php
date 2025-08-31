    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="text-gradient mb-3">
                        <i class="fas fa-shield-halved me-2"></i>HackVault Pro
                    </h5>
                    <p class="text-light-emphasis">
                        Professional cybersecurity and programming tools for ethical hackers, 
                        bug bounty hunters, and security researchers worldwide.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-github fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-discord fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-telegram fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-primary mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-light-emphasis text-decoration-none">Home</a></li>
                        <li><a href="products.php" class="text-light-emphasis text-decoration-none">Products</a></li>
                        <li><a href="contact.php" class="text-light-emphasis text-decoration-none">Contact</a></li>
                        <li><a href="admin/" class="text-light-emphasis text-decoration-none">Admin</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="text-primary mb-3">Categories</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light-emphasis text-decoration-none">Network Security</a></li>
                        <li><a href="#" class="text-light-emphasis text-decoration-none">Web Security</a></li>
                        <li><a href="#" class="text-light-emphasis text-decoration-none">Password Security</a></li>
                        <li><a href="#" class="text-light-emphasis text-decoration-none">Malware Analysis</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 mb-4">
                    <h6 class="text-primary mb-3">Payment Methods</h6>
                    <div class="d-flex gap-2 mb-3">
                        <span class="badge bg-warning"><i class="fab fa-bitcoin"></i> BTC</span>
                        <span class="badge bg-info"><i class="fab fa-ethereum"></i> ETH</span>
                        <span class="badge bg-secondary"><i class="fas fa-coins"></i> LTC</span>
                    </div>
                    <p class="text-light-emphasis small">
                        We accept cryptocurrency payments for secure and anonymous transactions.
                    </p>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="row align-items-center center-md">
                <div class="col-md-6">
                    
                    <p class="mb-0 text-light-emphasis">
                        &copy; <?php echo date('Y'); ?> HackVault Pro. All rights reserved.
                    </p>

                </div>
                <!-- <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-light-emphasis small">
                        <i class="fas fa-info-circle me-1"></i>
                        For educational and authorized testing purposes only.
                    </p>
                </div> -->
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Add fade-in animation to cards when they come into view
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.card').forEach(card => {
            observer.observe(card);
        });

        // Copy wallet address functionality
        function copyToClipboard(text, button) {
            navigator.clipboard.writeText(text).then(() => {
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
                button.classList.remove('btn-outline-light');
                button.classList.add('btn-success');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-light');
                }, 2000);
            });
        }
    </script>
</body>
</html>