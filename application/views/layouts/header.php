<!DOCTYPE html>
<html lang="<?php echo isset($current_lang) ? $current_lang : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo isset($meta_description) ? $meta_description : 'Financial Loan Solutions'; ?>">
    <title><?php echo isset($page_title) ? $page_title : 'Financial Loan Solutions'; ?></title>
    <link rel="stylesheet" href="<?php echo base_url('public/css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Language Translations for JavaScript -->
    <script>
        window.translations = <?php echo isset($translations_json) ? $translations_json : '{}'; ?>;
        window.currentLang = '<?php echo isset($current_lang) ? $current_lang : 'en'; ?>';
    </script>
    
    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1342586990777024');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=1342586990777024&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <div class="logo">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span><?php _e('site_name'); ?></span>
                </div>
                <div class="nav-menu" id="navMenu">
                    <a href="#home" class="nav-link" data-lang="nav_home"><?php _e('nav_home'); ?></a>
                    <a href="#services" class="nav-link" data-lang="nav_services"><?php _e('nav_services'); ?></a>
                    <a href="#apply" class="nav-link" data-lang="nav_apply"><?php _e('nav_apply'); ?></a>
                    <a href="#contact" class="nav-link" data-lang="nav_contact"><?php _e('nav_contact'); ?></a>
                    
                    <!-- Language Selector -->
                    <div class="language-selector">
                        <button class="lang-btn" id="langBtn">
                            <i class="fas fa-globe"></i>
                            <span id="currentLangText"><?php echo strtoupper(isset($current_lang) ? $current_lang : 'EN'); ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="lang-dropdown" id="langDropdown">
                            <a href="#" class="lang-option" data-lang="en">
                                <span class="flag-code">EN</span> English
                            </a>
                            <a href="#" class="lang-option" data-lang="my">
                                <span class="flag">🇲🇾</span> Bahasa Melayu
                            </a>
                            <a href="#" class="lang-option" data-lang="zh">
                                <span class="flag">🇨🇳</span> 简体中文
                            </a>
                        </div>
                    </div>
                    
                    <a href="#apply" class="btn btn-primary" data-lang="nav_apply"><?php _e('nav_apply'); ?></a>
                </div>
                <div class="hamburger" id="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </nav>
