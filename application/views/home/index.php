<!-- Hero Section -->
<section class="hero" id="home">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1><?php _e('hero_title'); ?></h1>
                <p class="hero-subtitle"><?php _e('hero_subtitle'); ?></p>
                <div class="hero-features">
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span><?php _e('feature_fast_title'); ?></span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span><?php _e('feature_low_title'); ?></span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span><?php _e('feature_flexible_title'); ?></span>
                    </div>
                </div>
                <div class="hero-cta">
                    <a href="#apply" class="btn btn-primary btn-large"><?php _e('hero_cta'); ?></a>
                    <a href="#how-it-works" class="btn btn-secondary btn-large"><?php _e('hero_learn_more'); ?></a>
                </div>
                <div class="hero-stats">
                    <div class="stat">
                        <h3>50,000+</h3>
                        <p><?php _e('stat_customers'); ?></p>
                    </div>
                    <div class="stat">
                        <h3>RM 500M+</h3>
                        <p><?php _e('stat_disbursed'); ?></p>
                    </div>
                    <div class="stat">
                        <h3>4.9/5</h3>
                        <p><?php _e('stat_rating'); ?></p>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <div class="hero-card">
                    <div class="card-glow"></div>
                    <i class="fas fa-shield-alt"></i>
                    <h3><?php _e('hero_secure_title'); ?></h3>
                    <p><?php _e('hero_secure_desc'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services" id="services">
    <div class="container">
        <div class="section-header">
            <h2><?php _e('services_title'); ?></h2>
            <p><?php _e('services_subtitle'); ?></p>
        </div>
        <div class="services-grid">
            <?php 
            if (isset($loan_types) && !empty($loan_types)):
                $icons = [
                    'personal' => 'fa-user-tie',
                    'business' => 'fa-briefcase',
                    'home' => 'fa-home',
                    'car' => 'fa-car'
                ];
                $index = 0;
                foreach ($loan_types as $loan):
                    $index++;
                    $icon = isset($icons[$loan['type_name']]) ? $icons[$loan['type_name']] : 'fa-dollar-sign';
                    $is_featured = ($loan['type_name'] == 'business'); // Make business featured
            ?>
            <div class="service-card <?php echo $is_featured ? 'featured' : ''; ?>">
                <?php if ($is_featured): ?>
                <div class="badge"><?php _e('service_most_popular'); ?></div>
                <?php endif; ?>
                <div class="service-icon">
                    <i class="fas <?php echo $icon; ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($loan['display_name']); ?></h3>
                <p><?php echo htmlspecialchars($loan['description']); ?></p>
                <ul class="service-features">
                    <li><i class="fas fa-check"></i> Up to RM <?php echo number_format($loan['max_amount'], 0); ?></li>
                    <li><i class="fas fa-check"></i> Interest from <?php echo number_format($loan['min_interest_rate'], 1); ?>% p.a.</li>
                    <li><i class="fas fa-check"></i> Tenure up to <?php echo $loan['max_tenure_years']; ?> years</li>
                </ul>
                <div style="text-align: center;">
                    <a href="#apply" class="btn <?php echo $is_featured ? 'btn-primary' : 'btn-outline'; ?>"><?php _e('nav_apply'); ?></a>
                </div>
            </div>
            <?php 
                endforeach;
            else:
            ?>
            <div class="empty-state">
                <p><?php _e('service_no_products'); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="how-it-works" id="how-it-works">
    <div class="container">
        <div class="section-header">
            <h2><?php _e('how_works_title'); ?></h2>
            <p><?php _e('how_works_subtitle'); ?></p>
        </div>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <div class="step-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h3><?php _e('step1_title'); ?></h3>
                <p><?php _e('step1_desc'); ?></p>
            </div>
            
            <div class="step-arrow">
                <i class="fas fa-arrow-right"></i>
            </div>
            
            <div class="step-card">
                <div class="step-number">2</div>
                <div class="step-icon">
                    <i class="fas fa-search-dollar"></i>
                </div>
                <h3><?php _e('step2_title'); ?></h3>
                <p><?php _e('step2_desc'); ?></p>
            </div>
            
            <div class="step-arrow">
                <i class="fas fa-arrow-right"></i>
            </div>
            
            <div class="step-card">
                <div class="step-number">3</div>
                <div class="step-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <h3><?php _e('step3_title'); ?></h3>
                <p><?php _e('step3_desc'); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="benefits" id="benefits">
    <div class="container">
        <div class="section-header">
            <h2><?php _e('features_title'); ?></h2>
            <p><?php _e('hero_description'); ?></p>
        </div>
        <div class="benefits-grid">
            <div class="benefit-card">
                <i class="fas fa-bolt"></i>
                <h3><?php _e('feature_fast_title'); ?></h3>
                <p><?php _e('feature_fast_desc'); ?></p>
            </div>
            <div class="benefit-card">
                <i class="fas fa-percentage"></i>
                <h3><?php _e('feature_low_title'); ?></h3>
                <p><?php _e('feature_low_desc'); ?></p>
            </div>
            <div class="benefit-card">
                <i class="fas fa-calendar-check"></i>
                <h3><?php _e('feature_flexible_title'); ?></h3>
                <p><?php _e('feature_flexible_desc'); ?></p>
            </div>
            <div class="benefit-card">
                <i class="fas fa-lock"></i>
                <h3><?php _e('feature_secure_title'); ?></h3>
                <p><?php _e('feature_secure_desc'); ?></p>
            </div>
            <div class="benefit-card">
                <i class="fas fa-headset"></i>
                <h3><?php _e('benefit_support_title'); ?></h3>
                <p><?php _e('benefit_support_desc'); ?></p>
            </div>
            <div class="benefit-card">
                <i class="fas fa-mobile-alt"></i>
                <h3><?php _e('benefit_online_title'); ?></h3>
                <p><?php _e('benefit_online_desc'); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Loan Calculator Section -->
<section class="calculator">
    <div class="container">
        <div class="calculator-wrapper">
            <div class="calculator-content">
                <h2><?php _e('calculator_title'); ?></h2>
                <p><?php _e('calculator_subtitle'); ?></p>
                
                <div class="calculator-form">
                    <div class="form-group">
                        <label for="loanAmount"><?php _e('calculator_amount'); ?> (RM)</label>
                        <input type="range" id="loanAmount" min="5000" max="1000000" value="50000" step="5000">
                        <div class="value-display" id="loanAmountValue">RM 50,000</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="loanTenure"><?php _e('calculator_term'); ?> (<?php _e('calculator_years'); ?>)</label>
                        <input type="range" id="loanTenure" min="1" max="35" value="5" step="1">
                        <div class="value-display" id="loanTenureValue">5 <?php _e('calculator_years'); ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="interestRate"><?php _e('calculator_interest'); ?> (% p.a.)</label>
                        <input type="range" id="interestRate" min="2.5" max="12" value="3.5" step="0.1">
                        <div class="value-display" id="interestRateValue">3.5%</div>
                    </div>
                    
                    <div class="calculator-result">
                        <h3><?php _e('calculator_monthly'); ?></h3>
                        <div class="monthly-payment" id="monthlyPayment">RM 909</div>
                        <p class="result-note"><?php _e('calculator_note'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Application Form Section -->
<section class="application-form" id="apply">
    <div class="container">
        <div class="section-header">
            <h2><?php _e('form_title'); ?></h2>
            <p><?php _e('form_subtitle'); ?></p>
        </div>
        
        <div class="form-wrapper">
            <form id="loanApplicationForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name"><?php _e('form_name'); ?> *</label>
                        <input type="text" id="name" name="name" required placeholder="<?php _e('form_name_placeholder'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email"><?php _e('form_email'); ?> *</label>
                        <input type="email" id="email" name="email" required placeholder="<?php _e('form_email_placeholder'); ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone"><?php _e('form_phone'); ?> *</label>
                        <input type="tel" id="phone" name="phone" required 
                               placeholder="<?php _e('form_phone_placeholder'); ?>"
                               pattern="[0-9+\-\s]+"
                               title="Malaysian phone number">
                        <small style="color: #666; font-size: 0.85rem;"><?php _e('form_phone_hint'); ?></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="ic_number"><?php _e('form_ic'); ?> *</label>
                        <input type="text" id="ic_number" name="ic_number" required 
                               placeholder="<?php _e('form_ic_placeholder'); ?>"
                               pattern="[0-9\-]+"
                               maxlength="14"
                               title="Malaysian IC Number">
                        <small style="color: #666; font-size: 0.85rem;"><?php _e('form_ic_hint'); ?></small>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="loan_type"><?php _e('form_loan_type'); ?> *</label>
                        <select id="loan_type" name="loan_type" required>
                            <option value=""><?php _e('form_loan_type_select'); ?></option>
                            <?php 
                            if (isset($loan_types) && !empty($loan_types)):
                                foreach ($loan_types as $loan):
                            ?>
                            <option value="<?php echo htmlspecialchars($loan['type_name']); ?>">
                                <?php echo htmlspecialchars($loan['display_name']); ?>
                            </option>
                            <?php 
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="loan_amount"><?php _e('form_loan_amount'); ?> *</label>
                        <input type="number" id="loan_amount" name="loan_amount" min="1000" required 
                               placeholder="<?php _e('form_loan_amount_placeholder'); ?>"
                               step="1000">
                        <small style="color: #666; font-size: 0.85rem;"><?php _e('form_loan_amount_hint'); ?></small>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="income"><?php _e('form_income'); ?></label>
                        <input type="number" id="income" name="income" min="1000"
                               placeholder="<?php _e('form_income_placeholder'); ?>"
                               step="500">
                        <small style="color: #666; font-size: 0.85rem;"><?php _e('form_income_hint'); ?></small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="message"><?php _e('form_message'); ?></label>
                    <textarea id="message" name="message" rows="4"></textarea>
                </div>
                
                <div class="form-group checkbox-group">
                    <label>
                        <input type="checkbox" name="terms" required>
                        <span><?php _e('form_terms'); ?></span>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-large"><?php _e('form_submit'); ?></button>
            </form>
            
            <div id="formMessage" class="form-message"></div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials">
    <div class="container">
        <div class="section-header">
            <h2><?php _e('testimonials_title'); ?></h2>
            <p><?php _e('testimonials_subtitle'); ?></p>
        </div>
        
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>"<?php _e('testimonial1_text'); ?>"</p>
                <div class="testimonial-author">
                    <div class="author-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="author-info">
                        <h4><?php _e('testimonial1_name'); ?></h4>
                        <span><?php _e('testimonial1_role'); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>"<?php _e('testimonial2_text'); ?>"</p>
                <div class="testimonial-author">
                    <div class="author-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="author-info">
                        <h4><?php _e('testimonial2_name'); ?></h4>
                        <span><?php _e('testimonial2_role'); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>"<?php _e('testimonial3_text'); ?>"</p>
                <div class="testimonial-author">
                    <div class="author-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="author-info">
                        <h4><?php _e('testimonial3_name'); ?></h4>
                        <span><?php _e('testimonial3_role'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2><?php _e('cta_title'); ?></h2>
            <p><?php _e('cta_subtitle'); ?></p>
            <a href="#apply" class="btn btn-light btn-large"><?php _e('cta_button'); ?></a>
        </div>
    </div>
</section>
