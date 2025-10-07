<!-- Download Page - Google Play Store Style -->
<style>
    .download-page {
        background: #f8f9fa;
        min-height: calc(100vh - 140px);
        padding: 60px 0;
    }
    
    .download-container {
        max-width: 1000px;
        margin: 0 auto;
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .download-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 40px;
        text-align: center;
        color: white;
    }
    
    .download-header h1 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    
    .download-header p {
        font-size: 1.2rem;
        opacity: 0.95;
    }
    
    .app-info {
        display: grid;
        grid-template-columns: 180px 1fr;
        gap: 40px;
        padding: 40px;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .app-icon {
        width: 180px;
        height: 180px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 5rem;
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
    }
    
    .app-details h2 {
        font-size: 2rem;
        color: #202124;
        margin-bottom: 0.5rem;
    }
    
    .app-developer {
        color: #01875f;
        font-size: 1rem;
        margin-bottom: 1rem;
        font-weight: 500;
    }
    
    .app-stats {
        display: flex;
        gap: 30px;
        margin: 20px 0;
        padding: 20px 0;
        border-top: 1px solid #e0e0e0;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .stat-item {
        text-align: center;
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: 600;
        color: #202124;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }
    
    .stat-label {
        font-size: 0.85rem;
        color: #5f6368;
        margin-top: 5px;
    }
    
    .download-button-container {
        padding: 0 40px 40px 40px;
    }
    
    .download-btn {
        display: inline-flex;
        align-items: center;
        gap: 15px;
        background: #01875f;
        color: white;
        padding: 16px 32px;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(1, 135, 95, 0.3);
    }
    
    .download-btn:hover {
        background: #016d4f;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(1, 135, 95, 0.4);
    }
    
    .download-btn i {
        font-size: 1.5rem;
    }
    
    .app-features {
        padding: 40px;
        background: #f8f9fa;
    }
    
    .app-features h3 {
        font-size: 1.5rem;
        color: #202124;
        margin-bottom: 20px;
    }
    
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .feature-item {
        display: flex;
        gap: 15px;
        align-items: flex-start;
    }
    
    .feature-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    
    .feature-content h4 {
        font-size: 1.1rem;
        color: #202124;
        margin-bottom: 5px;
    }
    
    .feature-content p {
        font-size: 0.9rem;
        color: #5f6368;
        line-height: 1.5;
    }
    
    .app-screenshots {
        padding: 40px;
    }
    
    .app-screenshots h3 {
        font-size: 1.5rem;
        color: #202124;
        margin-bottom: 20px;
    }
    
    .screenshots-scroll {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        padding: 10px 0;
    }
    
    .screenshot {
        width: 200px;
        height: 400px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .security-note {
        background: #e8f5e9;
        border-left: 4px solid #4caf50;
        padding: 20px;
        margin: 40px;
        border-radius: 8px;
    }
    
    .security-note h4 {
        color: #2e7d32;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .security-note p {
        color: #1b5e20;
        line-height: 1.6;
    }
    
    @media (max-width: 768px) {
        .app-info {
            grid-template-columns: 1fr;
            text-align: center;
        }
        
        .app-icon {
            margin: 0 auto;
        }
        
        .app-stats {
            flex-direction: column;
            gap: 15px;
        }
        
        .features-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="download-page">
    <div class="container">
        <div class="download-container">
            <!-- Header -->
            <div class="download-header">
                <h1><?php _e('download_page_title'); ?></h1>
                <p><?php _e('download_page_subtitle'); ?></p>
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
                                <i class="fas fa-star" style="color: #fbbc04;"></i>
                                4.8
                            </div>
                            <div class="stat-label"><?php _e('download_rating'); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">50K+</div>
                            <div class="stat-label"><?php _e('download_downloads'); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">
                                <i class="fas fa-mobile-alt" style="color: #01875f;"></i>
                                18+
                            </div>
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
