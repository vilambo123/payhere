    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><i class="fas fa-hand-holding-usd"></i> <?php echo isset($site['name']) ? htmlspecialchars($site['name']) : 'QuickLoan'; ?></h3>
                    <p>Your trusted partner for fast and reliable financial solutions. We help you achieve your dreams with flexible loan options.</p>
                    <div class="social-links">
                        <a href="<?php echo isset($social['facebook']) ? htmlspecialchars($social['facebook']) : '#'; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="<?php echo isset($social['twitter']) ? htmlspecialchars($social['twitter']) : '#'; ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                        <a href="<?php echo isset($social['instagram']) ? htmlspecialchars($social['instagram']) : '#'; ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="<?php echo isset($social['linkedin']) ? htmlspecialchars($social['linkedin']) : '#'; ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#about">About Us</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Loan Types</h4>
                    <ul>
                        <li><a href="#apply">Personal Loan</a></li>
                        <li><a href="#apply">Business Loan</a></li>
                        <li><a href="#apply">Home Loan</a></li>
                        <li><a href="#apply">Car Loan</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Contact Us</h4>
                    <ul class="contact-info">
                        <li><i class="fas fa-phone"></i> <?php echo isset($site['phone']) ? htmlspecialchars($site['phone']) : '+60 3-1234 5678'; ?></li>
                        <li><i class="fas fa-envelope"></i> <?php echo isset($site['email']) ? htmlspecialchars($site['email']) : 'info@quickloan.com'; ?></li>
                        <li><i class="fas fa-map-marker-alt"></i> <?php echo isset($site['address']) ? htmlspecialchars($site['address']) : 'Kuala Lumpur, Malaysia'; ?></li>
                        <li><i class="fas fa-clock"></i> Mon-Fri: 9AM - 6PM</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo isset($site['name']) ? htmlspecialchars($site['name']) : 'QuickLoan'; ?>. All rights reserved. | <a href="#">Privacy Policy</a> | <a href="#">Terms & Conditions</a></p>
            </div>
        </div>
    </footer>

    <script src="<?php echo base_url('public/js/script.js'); ?>"></script>
</body>
</html>
