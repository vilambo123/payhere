<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Settings'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: #f5f5f5;
            color: #333;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header h1 { font-size: 2rem; margin-bottom: 0.5rem; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e2e8f0;
        }
        .tab {
            padding: 1rem 2rem;
            background: white;
            border: none;
            border-radius: 8px 8px 0 0;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        .tab.active {
            background: #667eea;
            color: white;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
        }
        .settings-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .settings-section h3 {
            margin-bottom: 1.5rem;
            color: #667eea;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 0.5rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 5px;
            font-size: 1rem;
            font-family: inherit;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
        }
        .btn-success {
            background: #10b981;
            color: white;
        }
        .message {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            display: none;
        }
        .message.success {
            background: #d1fae5;
            color: #065f46;
            display: block;
        }
        .message.error {
            background: #fee2e2;
            color: #991b1b;
            display: block;
        }
        .loan-type-item {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 2px solid #e2e8f0;
        }
        .loan-type-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .back-link {
            display: inline-block;
            color: white;
            text-decoration: none;
            margin-top: 1rem;
            opacity: 0.9;
        }
        .back-link:hover { opacity: 1; }
        .info-box {
            background: #e0f2fe;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            border-left: 4px solid #0284c7;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-cog"></i> Settings Management</h1>
        <p>Configure your website settings, contact information, and loan products</p>
        <a href="<?php echo base_url('index.php/admin'); ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    <div class="container">
        <div id="messageBox" class="message"></div>
        
        <div class="tabs">
            <button class="tab active" onclick="showTab('site')">
                <i class="fas fa-globe"></i> Site Settings
            </button>
            <button class="tab" onclick="showTab('loan')">
                <i class="fas fa-money-bill-wave"></i> Loan Types
            </button>
        </div>
        
        <!-- Site Settings Tab -->
        <div id="siteTab" class="tab-content active">
            <div class="info-box">
                <strong><i class="fas fa-info-circle"></i> Info:</strong> 
                Changes here will automatically update throughout the website (header, footer, contact forms, etc.)
            </div>
            
            <form id="siteSettingsForm">
                <div class="settings-grid">
                    <!-- Company Information -->
                    <div class="settings-section">
                        <h3><i class="fas fa-building"></i> Company Information</h3>
                        
                        <div class="form-group">
                            <label for="site_name">Company Name</label>
                            <input type="text" id="site_name" name="setting_site_name" 
                                   value="<?php echo isset($all_settings['site_name']) ? htmlspecialchars($all_settings['site_name']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="site_email">Email Address</label>
                            <input type="email" id="site_email" name="setting_site_email" 
                                   value="<?php echo isset($all_settings['site_email']) ? htmlspecialchars($all_settings['site_email']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="site_phone">Phone Number</label>
                            <input type="text" id="site_phone" name="setting_site_phone" 
                                   value="<?php echo isset($all_settings['site_phone']) ? htmlspecialchars($all_settings['site_phone']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="site_address">Address</label>
                            <input type="text" id="site_address" name="setting_site_address" 
                                   value="<?php echo isset($all_settings['site_address']) ? htmlspecialchars($all_settings['site_address']) : ''; ?>">
                        </div>
                    </div>
                    
                    <!-- Social Media -->
                    <div class="settings-section">
                        <h3><i class="fas fa-share-alt"></i> Social Media Links</h3>
                        
                        <div class="form-group">
                            <label for="social_facebook">Facebook URL</label>
                            <input type="url" id="social_facebook" name="setting_social_facebook" 
                                   value="<?php echo isset($all_settings['social_facebook']) ? htmlspecialchars($all_settings['social_facebook']) : ''; ?>"
                                   placeholder="https://facebook.com/yourpage">
                        </div>
                        
                        <div class="form-group">
                            <label for="social_twitter">Twitter URL</label>
                            <input type="url" id="social_twitter" name="setting_social_twitter" 
                                   value="<?php echo isset($all_settings['social_twitter']) ? htmlspecialchars($all_settings['social_twitter']) : ''; ?>"
                                   placeholder="https://twitter.com/yourprofile">
                        </div>
                        
                        <div class="form-group">
                            <label for="social_instagram">Instagram URL</label>
                            <input type="url" id="social_instagram" name="setting_social_instagram" 
                                   value="<?php echo isset($all_settings['social_instagram']) ? htmlspecialchars($all_settings['social_instagram']) : ''; ?>"
                                   placeholder="https://instagram.com/yourprofile">
                        </div>
                        
                        <div class="form-group">
                            <label for="social_linkedin">LinkedIn URL</label>
                            <input type="url" id="social_linkedin" name="setting_social_linkedin" 
                                   value="<?php echo isset($all_settings['social_linkedin']) ? htmlspecialchars($all_settings['social_linkedin']) : ''; ?>"
                                   placeholder="https://linkedin.com/company/yourcompany">
                        </div>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save All Settings
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Loan Types Tab -->
        <div id="loanTab" class="tab-content">
            <div class="info-box">
                <strong><i class="fas fa-info-circle"></i> Info:</strong> 
                Loan types shown here will appear on the landing page services section and in the application form dropdown.
            </div>
            
            <?php if (isset($loan_types) && !empty($loan_types)): ?>
                <?php foreach ($loan_types as $loan): ?>
                <div class="loan-type-item">
                    <div class="loan-type-header">
                        <h4><?php echo htmlspecialchars($loan['display_name']); ?></h4>
                        <span class="badge <?php echo $loan['is_active'] ? 'badge-success' : 'badge-danger'; ?>">
                            <?php echo $loan['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </div>
                    
                    <form class="loan-type-form" data-loan-id="<?php echo $loan['id']; ?>">
                        <div class="settings-grid">
                            <div>
                                <div class="form-group">
                                    <label>Display Name</label>
                                    <input type="text" name="display_name" value="<?php echo htmlspecialchars($loan['display_name']); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" rows="3"><?php echo htmlspecialchars($loan['description']); ?></textarea>
                                </div>
                            </div>
                            
                            <div>
                                <div class="form-group">
                                    <label>Min Amount (RM)</label>
                                    <input type="number" name="min_amount" value="<?php echo $loan['min_amount']; ?>" step="1000">
                                </div>
                                
                                <div class="form-group">
                                    <label>Max Amount (RM)</label>
                                    <input type="number" name="max_amount" value="<?php echo $loan['max_amount']; ?>" step="1000">
                                </div>
                                
                                <div class="form-group">
                                    <label>Interest Rate (% p.a.)</label>
                                    <input type="number" name="min_interest_rate" value="<?php echo $loan['min_interest_rate']; ?>" step="0.1">
                                </div>
                                
                                <div class="form-group">
                                    <label>Max Tenure (Years)</label>
                                    <input type="number" name="max_tenure_years" value="<?php echo $loan['max_tenure_years']; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="is_active" <?php echo $loan['is_active'] ? 'checked' : ''; ?>>
                                        Active (Show on website)
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Update <?php echo htmlspecialchars($loan['type_name']); ?> Loan
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No loan types found.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab
            if (tabName === 'site') {
                document.getElementById('siteTab').classList.add('active');
                event.target.classList.add('active');
            } else if (tabName === 'loan') {
                document.getElementById('loanTab').classList.add('active');
                event.target.classList.add('active');
            }
        }
        
        function showMessage(type, message) {
            const msgBox = document.getElementById('messageBox');
            msgBox.className = 'message ' + type;
            msgBox.textContent = message;
            msgBox.scrollIntoView({ behavior: 'smooth' });
            
            setTimeout(() => {
                msgBox.style.display = 'none';
            }, 5000);
        }
        
        // Site settings form
        document.getElementById('siteSettingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            
            fetch('<?php echo base_url('index.php/settings/update'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('success', data.message);
                } else {
                    showMessage('error', data.message);
                }
                
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Save All Settings';
            })
            .catch(error => {
                showMessage('error', 'An error occurred: ' + error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Save All Settings';
            });
        });
        
        // Loan type forms
        document.querySelectorAll('.loan-type-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const loanId = this.dataset.loanId;
                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                
                fetch('<?php echo base_url('index.php/settings/update_loan_type/'); ?>' + loanId, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('success', data.message);
                    } else {
                        showMessage('error', data.message);
                    }
                    
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtn.innerHTML.replace('Updating...', 'Update');
                    submitBtn.innerHTML = submitBtn.innerHTML.replace('fa-spinner fa-spin', 'fa-save');
                })
                .catch(error => {
                    showMessage('error', 'An error occurred: ' + error);
                    submitBtn.disabled = false;
                });
            });
        });
    </script>
</body>
</html>
