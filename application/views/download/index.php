<!DOCTYPE html>
<html lang="<?php echo isset($current_lang) ? $current_lang : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Download App'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- Download Page - Modern Play Store Style -->
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body {
        background: #f1f3f4 !important;
        margin: 0;
        padding: 0;
        font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    
    .download-page {
        background: #f1f3f4;
        min-height: 100vh;
        padding: 0;
    }
    
    .download-navbar {
        background: #fff;
        padding: 12px 0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        margin-bottom: 0;
    }
    
    .download-nav-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .download-logo {
        display: flex;
        align-items: center;
        gap: 16px;
        font-size: 1.25rem;
        font-weight: 500;
        color: #5f6368;
    }
    
    .download-logo i {
        color: #34a853;
        font-size: 2rem;
    }
    
    .download-breadcrumb {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .download-breadcrumb a {
        color: #1a73e8;
        text-decoration: none;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 20px;
        transition: background 0.2s;
    }
    
    .download-breadcrumb a:hover {
        background: rgba(26, 115, 232, 0.08);
    }
    
    .download-container {
        max-width: 1000px;
        margin: 24px auto;
        background: #fff;
        border-radius: 8px;
        overflow: visible;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    .download-hero {
        background: linear-gradient(135deg, #1a73e8 0%, #34a853 100%);
        padding: 0;
        position: relative;
        height: 280px;
        overflow: hidden;
    }
    
    .download-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at 30% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
    }
    
    .hero-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        max-width: 900px;
        margin: 0 auto;
        padding: 48px 40px;
        height: 100%;
    }
    
    .hero-text h1 {
        font-size: 2.25rem;
        font-weight: 400;
        color: #fff;
        margin: 0 0 12px 0;
        letter-spacing: -0.5px;
    }
    
    .hero-text p {
        font-size: 1rem;
        color: rgba(255,255,255,0.9);
        margin: 0;
        max-width: 500px;
    }
    
    .hero-image {
        width: 200px;
        height: 200px;
        position: relative;
    }
    
    .hero-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        filter: drop-shadow(0 8px 24px rgba(0,0,0,0.2));
    }
    
    .app-info {
        display: flex;
        gap: 32px;
        padding: 32px 40px;
        border-bottom: 1px solid #dadce0;
        align-items: flex-start;
    }
    
    .app-icon {
        width: 140px;
        height: 140px;
        background: linear-gradient(135deg, #1a73e8 0%, #34a853 100%);
        border-radius: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 4rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
        flex-shrink: 0;
    }
    
    .app-details {
        flex: 1;
    }
    
    .app-details h2 {
        font-size: 1.75rem;
        font-weight: 400;
        color: #202124;
        margin: 0 0 8px 0;
    }
    
    .app-developer {
        color: #34a853;
        font-size: 0.875rem;
        margin-bottom: 16px;
        font-weight: 500;
    }
    
    .app-stats {
        display: flex;
        gap: 48px;
        margin: 16px 0 0 0;
    }
    
    .stat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }
    
    .stat-value {
        font-size: 0.875rem;
        font-weight: 700;
        color: #202124;
        display: flex;
        align-items: center;
        gap: 6px;
        letter-spacing: 0.2px;
    }
    
    .stat-label {
        font-size: 0.75rem;
        color: #5f6368;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .rating-stars {
        color: #fbbc04;
        font-size: 0.875rem;
        margin-left: 4px;
    }
    
    .download-button-container {
        padding: 24px 40px 32px 40px;
        border-bottom: 1px solid #dadce0;
        text-align: center;
    }
    
    .download-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        background: #1a73e8;
        color: white;
        padding: 14px 32px;
        border-radius: 4px;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
        box-shadow: none;
        text-transform: none;
        letter-spacing: 0.25px;
        min-width: 200px;
    }
    
    .download-btn:hover {
        background: #1765cc;
        box-shadow: 0 1px 2px rgba(0,0,0,0.3), 0 1px 3px rgba(0,0,0,0.15);
    }
    
    .download-btn:active {
        background: #1557b0;
        box-shadow: 0 1px 2px rgba(0,0,0,0.3);
    }
    
    .download-btn i {
        font-size: 1.25rem;
    }
    
    .app-features {
        padding: 32px 40px;
        background: #fff;
    }
    
    .app-features h3 {
        font-size: 1.25rem;
        font-weight: 500;
        color: #202124;
        margin: 0 0 24px 0;
    }
    
    .features-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        max-width: 700px;
        margin: 0 auto;
    }
    
    .feature-item {
        display: flex;
        gap: 15px;
        align-items: flex-start;
    }
    
    .feature-icon {
        width: 40px;
        height: 40px;
        background: #e8f0fe;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1a73e8;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    
    .feature-content h4 {
        font-size: 1rem;
        font-weight: 500;
        color: #202124;
        margin: 0 0 4px 0;
    }
    
    .feature-content p {
        font-size: 0.875rem;
        color: #5f6368;
        line-height: 1.4;
        margin: 0;
    }
    
    .app-screenshots {
        padding: 32px 40px;
        background: #fff;
        border-top: 1px solid #dadce0;
    }
    
    .app-screenshots h3 {
        font-size: 1.25rem;
        font-weight: 500;
        color: #202124;
        margin: 0 0 20px 0;
    }
    
    .screenshots-scroll {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        padding: 10px 0;
    }
    
    .screenshot {
        width: 180px;
        height: 360px;
        background: linear-gradient(135deg, #1a73e8 0%, #34a853 100%);
        border-radius: 24px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        border: 8px solid #202124;
    }
    
    .security-note {
        background: #e8f0fe;
        border-left: 4px solid #1a73e8;
        padding: 16px 20px;
        margin: 24px 40px 40px 40px;
        border-radius: 4px;
    }
    
    .security-note h4 {
        color: #1a73e8;
        margin: 0 0 8px 0;
        font-size: 0.875rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .security-note p {
        color: #3c4043;
        font-size: 0.875rem;
        line-height: 1.5;
        margin: 0;
    }
    
    @media (max-width: 768px) {
        .download-navbar {
            padding: 12px 0;
        }
        
        .download-nav-content {
            padding: 0 16px;
            flex-direction: column;
            gap: 8px;
            align-items: flex-start;
        }
        
        .download-logo {
            font-size: 1.1rem;
        }
        
        .download-logo i {
            font-size: 1.5rem;
        }
        
        .download-breadcrumb a {
            font-size: 0.8rem;
            padding: 6px 12px;
        }
        
        .download-hero {
            height: auto;
            min-height: 200px;
        }
        
        .hero-content {
            flex-direction: column;
            text-align: center;
            padding: 32px 20px;
            gap: 20px;
        }
        
        .hero-text h1 {
            font-size: 1.5rem;
            line-height: 1.3;
        }
        
        .hero-text p {
            font-size: 0.9rem;
            max-width: 100%;
        }
        
        .hero-image {
            width: 120px;
            height: 120px;
        }
        
        .hero-image i {
            font-size: 5rem !important;
        }
        
        .download-container {
            margin: 12px;
            border-radius: 8px;
        }
        
        .app-info {
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 24px 20px;
            gap: 20px;
        }
        
        .app-icon {
            width: 120px;
            height: 120px;
            font-size: 3.5rem;
            margin: 0 auto;
        }
        
        .app-details h2 {
            font-size: 1.5rem;
        }
        
        .app-developer {
            font-size: 0.8rem;
        }
        
        .app-stats {
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        
        .stat-item {
            min-width: 80px;
        }
        
        .stat-value {
            font-size: 0.8rem;
        }
        
        .stat-label {
            font-size: 0.7rem;
        }
        
        .rating-stars {
            font-size: 0.75rem;
        }
        
        .download-button-container {
            padding: 20px 16px 24px 16px;
        }
        
        .download-btn {
            width: 100%;
            max-width: 100%;
            padding: 16px 24px;
            font-size: 1rem;
        }
        
        .app-features {
            padding: 24px 20px;
        }
        
        .app-features h3 {
            font-size: 1.1rem;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .features-grid {
            grid-template-columns: 1fr;
            gap: 20px;
            max-width: 100%;
        }
        
        .feature-item {
            gap: 12px;
        }
        
        .feature-icon {
            width: 36px;
            height: 36px;
            font-size: 1.1rem;
        }
        
        .feature-content h4 {
            font-size: 0.95rem;
        }
        
        .feature-content p {
            font-size: 0.85rem;
        }
        
        .app-screenshots {
            padding: 24px 16px;
        }
        
        .app-screenshots h3 {
            font-size: 1.1rem;
            text-align: center;
        }
        
        .screenshots-scroll {
            gap: 12px;
            padding: 10px 4px;
        }
        
        .screenshot {
            width: 160px;
            height: 320px;
            border-radius: 20px;
            border-width: 6px;
            font-size: 2rem;
        }
        
        .security-note {
            margin: 20px 16px 32px 16px;
            padding: 14px 16px;
        }
        
        .security-note h4 {
            font-size: 0.85rem;
        }
        
        .security-note p {
            font-size: 0.8rem;
            line-height: 1.4;
        }
    }
    
    /* Small mobile devices */
    @media (max-width: 480px) {
        .download-container {
            margin: 8px;
        }
        
        .hero-text h1 {
            font-size: 1.3rem;
        }
        
        .hero-text p {
            font-size: 0.85rem;
        }
        
        .hero-image {
            width: 100px;
            height: 100px;
        }
        
        .hero-image i {
            font-size: 4rem !important;
        }
        
        .app-icon {
            width: 100px;
            height: 100px;
            font-size: 3rem;
        }
        
        .app-details h2 {
            font-size: 1.25rem;
        }
        
        .app-stats {
            gap: 16px;
        }
        
        .stat-item {
            min-width: 70px;
        }
        
        .stat-value {
            font-size: 0.75rem;
        }
        
        .stat-label {
            font-size: 0.65rem;
        }
        
        .download-btn {
            font-size: 0.95rem;
            padding: 14px 20px;
        }
        
        .feature-item {
            gap: 10px;
        }
        
        .feature-icon {
            width: 32px;
            height: 32px;
            font-size: 1rem;
        }
        
        .screenshot {
            width: 140px;
            height: 280px;
            border-width: 5px;
            font-size: 1.8rem;
        }
    }
</style>

<div class="download-page">
    <!-- Google Play Store Style Navigation -->
    <div class="download-navbar">
        <div class="download-nav-content">
            <div class="download-logo">
                <i class="fab fa-google-play"></i>
                <span><?php _e('site_name'); ?></span>
            </div>
            <div class="download-breadcrumb">
                <a href="<?php echo base_url(); ?>">← <?php _e('nav_home'); ?></a>
            </div>
        </div>
    </div>
    
    <div class="container" style="padding-top: 24px;">
        <div class="download-container">
            <!-- Hero Banner -->
            <div class="download-hero">
                <div class="hero-content">
                    <div class="hero-text">
                        <h1><?php _e('download_page_title'); ?></h1>
                        <p><?php _e('download_page_subtitle'); ?></p>
                    </div>
                    <div class="hero-image">
                        <i class="fas fa-mobile-alt" style="font-size: 8rem; color: white;"></i>
                    </div>
                </div>
            </div>
            
            <!-- App Info -->
            <div class="app-info">
                <div class="app-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                
                <div class="app-details">
                    <h2><?php echo isset($site['name']) ? htmlspecialchars($site['name']) : 'QuickLoan'; ?></h2>
                    <div class="app-developer"><?php _e('site_name_full'); ?></div>
                    
                    <div class="app-stats">
                        <div class="stat-item">
                            <div class="stat-value">
                                4.8
                                <span class="rating-stars">★★★★★</span>
                            </div>
                            <div class="stat-label"><?php _e('download_rating'); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">50K+</div>
                            <div class="stat-label"><?php _e('download_downloads'); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">18+</div>
                            <div class="stat-label"><?php _e('download_age'); ?></div>
                        </div>
                    </div>
                    
                    <p style="color: #5f6368; line-height: 1.6;">
                        <?php _e('download_app_description'); ?>
                    </p>
                </div>
            </div>
            
            <!-- Download Button -->
            <div class="download-button-container">
                <a href="<?php echo htmlspecialchars($apk_url); ?>" class="download-btn" id="downloadBtn">
                    <i class="fas fa-download"></i>
                    <span><?php _e('download_button'); ?></span>
                </a>
            </div>
            
            <!-- Features -->
            <div class="app-features">
                <h3><?php _e('download_features_title'); ?></h3>
                <div class="features-grid">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="feature-content">
                            <h4><?php _e('download_feature1_title'); ?></h4>
                            <p><?php _e('download_feature1_desc'); ?></p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="feature-content">
                            <h4><?php _e('download_feature2_title'); ?></h4>
                            <p><?php _e('download_feature2_desc'); ?></p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="feature-content">
                            <h4><?php _e('download_feature3_title'); ?></h4>
                            <p><?php _e('download_feature3_desc'); ?></p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="feature-content">
                            <h4><?php _e('download_feature4_title'); ?></h4>
                            <p><?php _e('download_feature4_desc'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Screenshots -->
            <div class="app-screenshots">
                <h3><?php _e('download_screenshots_title'); ?></h3>
                <div class="screenshots-scroll">
                    <div class="screenshot">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="screenshot">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="screenshot">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="screenshot">
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
            </div>
            
            <!-- Security Note -->
            <div class="security-note">
                <h4>
                    <i class="fas fa-shield-alt"></i>
                    <?php _e('download_security_title'); ?>
                </h4>
                <p><?php _e('download_security_text'); ?></p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('downloadBtn').addEventListener('click', function(e) {
    // Track download click (optional analytics)
    console.log('Download button clicked');
});
</script>

</body>
</html>
