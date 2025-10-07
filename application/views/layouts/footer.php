    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><i class="fas fa-hand-holding-usd"></i> <?php echo isset($site['name']) ? htmlspecialchars($site['name']) : 'QuickLoan'; ?></h3>
                    <p><?php _e('footer_about_text'); ?></p>
                    <div class="social-links">
                        <a href="<?php echo isset($social['facebook']) ? htmlspecialchars($social['facebook']) : '#'; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="<?php echo isset($social['twitter']) ? htmlspecialchars($social['twitter']) : '#'; ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                        <a href="<?php echo isset($social['instagram']) ? htmlspecialchars($social['instagram']) : '#'; ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="<?php echo isset($social['linkedin']) ? htmlspecialchars($social['linkedin']) : '#'; ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4><?php _e('footer_quick_links'); ?></h4>
                    <ul>
                        <li><a href="#home"><?php _e('nav_home'); ?></a></li>
                        <li><a href="#services"><?php _e('nav_services'); ?></a></li>
                        <li><a href="#apply"><?php _e('nav_apply'); ?></a></li>
                        <li><a href="#contact"><?php _e('nav_contact'); ?></a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4><?php _e('services_title'); ?></h4>
                    <ul>
                        <li><a href="#apply"><?php _e('loan_personal'); ?></a></li>
                        <li><a href="#apply"><?php _e('loan_business'); ?></a></li>
                        <li><a href="#apply"><?php _e('loan_home'); ?></a></li>
                        <li><a href="#apply"><?php _e('loan_car'); ?></a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4><?php _e('footer_contact'); ?></h4>
                    <ul class="contact-info">
                        <li><i class="fas fa-phone"></i> <?php echo isset($site['phone']) ? htmlspecialchars($site['phone']) : '+60 3-1234 5678'; ?></li>
                        <li><i class="fas fa-envelope"></i> <?php echo isset($site['email']) ? htmlspecialchars($site['email']) : 'info@quickloan.com'; ?></li>
                        <li><i class="fas fa-map-marker-alt"></i> <?php echo isset($site['address']) ? htmlspecialchars($site['address']) : 'Kuala Lumpur, Malaysia'; ?></li>
                        <li><i class="fas fa-clock"></i> Mon-Fri: 9AM - 6PM</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo isset($site['name']) ? htmlspecialchars($site['name']) : 'QuickLoan'; ?>. <?php _e('footer_rights'); ?></p>
            </div>
        </div>
    </footer>

    <script src="<?php echo base_url('public/js/script.js'); ?>"></script>
</body>
</html>
