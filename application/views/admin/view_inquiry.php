<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'View Inquiry'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
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
        .header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .header .subtitle {
            opacity: 0.9;
        }
        .back-link {
            display: inline-block;
            color: white;
            text-decoration: none;
            margin-top: 1rem;
            opacity: 0.9;
            transition: opacity 0.3s;
        }
        .back-link:hover {
            opacity: 1;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #34d399;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #f87171;
        }
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .card-header {
            background: #f8f9fa;
            padding: 1.5rem;
            border-bottom: 2px solid #e2e8f0;
        }
        .card-header h2 {
            font-size: 1.5rem;
            color: #333;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .card-body {
            padding: 2rem;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #555;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        .form-group input[readonly] {
            background: #f8f9fa;
            cursor: not-allowed;
        }
        .status-selector {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .status-option {
            flex: 1;
            min-width: 120px;
        }
        .status-option input[type="radio"] {
            display: none;
        }
        .status-option label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            text-align: center;
        }
        .status-option input[type="radio"]:checked + label {
            border-color: currentColor;
            box-shadow: 0 0 0 3px currentColor;
            opacity: 1;
        }
        .status-option.pending label {
            background: #fef3c7;
            color: #92400e;
        }
        .status-option.contacted label {
            background: #dbeafe;
            color: #1e40af;
        }
        .status-option.approved label {
            background: #d1fae5;
            color: #065f46;
        }
        .status-option.rejected label {
            background: #fee2e2;
            color: #991b1b;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        .info-item {
            display: flex;
            flex-direction: column;
        }
        .info-item .label {
            font-size: 0.875rem;
            color: #666;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .info-item .value {
            font-size: 1rem;
            color: #333;
            font-weight: 500;
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-contacted { background: #dbeafe; color: #1e40af; }
        .badge-approved { background: #d1fae5; color: #065f46; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }
        .btn-secondary {
            background: #e2e8f0;
            color: #333;
        }
        .btn-secondary:hover {
            background: #cbd5e0;
        }
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .loading {
            display: none;
            margin-left: 0.5rem;
        }
        .loading.show {
            display: inline-block;
        }
        @media (max-width: 768px) {
            .form-grid,
            .info-grid {
                grid-template-columns: 1fr;
            }
            .status-selector {
                flex-direction: column;
            }
            .status-option {
                min-width: 100%;
            }
            .actions {
                flex-direction: column;
            }
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-file-invoice"></i> <?php echo isset($page_title) ? $page_title : 'View Inquiry'; ?></h1>
        <p class="subtitle">View and manage inquiry details</p>
        <a href="<?php echo base_url('index.php/admin'); ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    <div class="container">
        <div id="alert-container"></div>
        
        <?php if (isset($error) && !isset($inquiry)): ?>
            <div class="alert alert-error">
                <strong><i class="fas fa-exclamation-triangle"></i> Error:</strong> <?php echo $error; ?>
            </div>
            <a href="<?php echo base_url('index.php/admin'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        <?php elseif (isset($inquiry)): ?>
            
            <!-- Application Information -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-info-circle"></i> Application Information</h2>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="label">Application ID</span>
                            <span class="value">#<?php echo $inquiry['id']; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Submitted Date</span>
                            <span class="value"><?php echo date('d M Y, h:i A', strtotime($inquiry['created_at'])); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Current Status</span>
                            <span class="value">
                                <span class="badge badge-<?php echo $inquiry['status']; ?>">
                                    <?php echo ucfirst($inquiry['status']); ?>
                                </span>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">IP Address</span>
                            <span class="value"><?php echo htmlspecialchars($inquiry['ip_address'] ?? 'N/A'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Edit Form -->
            <form id="editForm">
                <!-- Personal Details -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-user"></i> Personal Details</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name">
                                    <i class="fas fa-user"></i> Full Name
                                </label>
                                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($inquiry['name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="ic_number">
                                    <i class="fas fa-id-card"></i> IC Number
                                </label>
                                <input type="text" id="ic_number" name="ic_number" value="<?php echo htmlspecialchars($inquiry['ic_number'] ?? 'N/A'); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">
                                    <i class="fas fa-envelope"></i> Email Address
                                </label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($inquiry['email']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">
                                    <i class="fas fa-phone"></i> Phone Number
                                </label>
                                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($inquiry['phone']); ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Loan Details -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-money-bill-wave"></i> Loan Details</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="loan_type">
                                    <i class="fas fa-list"></i> Loan Type
                                </label>
                                <select id="loan_type" name="loan_type" required>
                                    <option value="personal" <?php echo ($inquiry['loan_type'] == 'personal') ? 'selected' : ''; ?>>Personal Loan</option>
                                    <option value="business" <?php echo ($inquiry['loan_type'] == 'business') ? 'selected' : ''; ?>>Business Loan</option>
                                    <option value="home" <?php echo ($inquiry['loan_type'] == 'home') ? 'selected' : ''; ?>>Home Loan</option>
                                    <option value="car" <?php echo ($inquiry['loan_type'] == 'car') ? 'selected' : ''; ?>>Car Loan</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="loan_amount">
                                    <i class="fas fa-dollar-sign"></i> Loan Amount (RM)
                                </label>
                                <input type="number" id="loan_amount" name="loan_amount" value="<?php echo htmlspecialchars($inquiry['loan_amount']); ?>" min="1000" step="1000" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="monthly_income">
                                    <i class="fas fa-wallet"></i> Monthly Income (RM)
                                </label>
                                <input type="number" id="monthly_income" name="monthly_income" value="<?php echo htmlspecialchars($inquiry['monthly_income'] ?? ''); ?>" min="0" step="100">
                            </div>
                            
                            <div class="form-group full-width">
                                <label for="message">
                                    <i class="fas fa-comment"></i> Additional Message
                                </label>
                                <textarea id="message" name="message" rows="4"><?php echo htmlspecialchars($inquiry['message'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Status Update -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-tasks"></i> Update Status</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-flag"></i> Application Status
                            </label>
                            <div class="status-selector">
                                <div class="status-option pending">
                                    <input type="radio" id="status_pending" name="status" value="pending" <?php echo ($inquiry['status'] == 'pending') ? 'checked' : ''; ?>>
                                    <label for="status_pending">
                                        <i class="fas fa-hourglass-half"></i> Pending
                                    </label>
                                </div>
                                <div class="status-option contacted">
                                    <input type="radio" id="status_contacted" name="status" value="contacted" <?php echo ($inquiry['status'] == 'contacted') ? 'checked' : ''; ?>>
                                    <label for="status_contacted">
                                        <i class="fas fa-phone-alt"></i> Contacted
                                    </label>
                                </div>
                                <div class="status-option approved">
                                    <input type="radio" id="status_approved" name="status" value="approved" <?php echo ($inquiry['status'] == 'approved') ? 'checked' : ''; ?>>
                                    <label for="status_approved">
                                        <i class="fas fa-check-circle"></i> Approved
                                    </label>
                                </div>
                                <div class="status-option rejected">
                                    <input type="radio" id="status_rejected" name="status" value="rejected" <?php echo ($inquiry['status'] == 'rejected') ? 'checked' : ''; ?>>
                                    <label for="status_rejected">
                                        <i class="fas fa-times-circle"></i> Rejected
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="actions">
                    <button type="submit" class="btn btn-primary" id="saveBtn">
                        <i class="fas fa-save"></i> Save Changes
                        <span class="loading" id="saveLoading">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </button>
                    <a href="<?php echo base_url('index.php/admin'); ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
            
        <?php endif; ?>
    </div>
    
    <script>
        const inquiryId = <?php echo isset($inquiry) ? $inquiry['id'] : 'null'; ?>;
        const baseUrl = '<?php echo base_url(); ?>';
        
        // Handle form submission
        document.getElementById('editForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const saveBtn = document.getElementById('saveBtn');
            const saveLoading = document.getElementById('saveLoading');
            
            // Disable button and show loading
            saveBtn.disabled = true;
            saveLoading.classList.add('show');
            
            try {
                const formData = new FormData(this);
                
                const response = await fetch(`${baseUrl}index.php/admin/update/${inquiryId}`, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', result.message);
                    // Reload page after 1 second
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert('error', result.message);
                    saveBtn.disabled = false;
                    saveLoading.classList.remove('show');
                }
            } catch (error) {
                showAlert('error', 'An error occurred while saving changes: ' + error.message);
                saveBtn.disabled = false;
                saveLoading.classList.remove('show');
            }
        });
        
        // Handle delete
        document.getElementById('deleteBtn')?.addEventListener('click', async function() {
            if (!confirm('Are you sure you want to delete this inquiry? This action cannot be undone.')) {
                return;
            }
            
            const deleteBtn = this;
            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            
            try {
                const response = await fetch(`${baseUrl}index.php/admin/delete/${inquiryId}`, {
                    method: 'POST'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', result.message + ' - Redirecting...');
                    setTimeout(() => {
                        window.location.href = `${baseUrl}index.php/admin`;
                    }, 1500);
                } else {
                    showAlert('error', result.message);
                    deleteBtn.disabled = false;
                    deleteBtn.innerHTML = '<i class="fas fa-trash"></i> Delete Inquiry';
                }
            } catch (error) {
                showAlert('error', 'An error occurred while deleting: ' + error.message);
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = '<i class="fas fa-trash"></i> Delete Inquiry';
            }
        });
        
        // Show alert function
        function showAlert(type, message) {
            const alertContainer = document.getElementById('alert-container');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
            
            alertContainer.innerHTML = `
                <div class="alert ${alertClass}">
                    <strong><i class="fas ${icon}"></i> ${type === 'success' ? 'Success!' : 'Error!'}</strong> ${message}
                </div>
            `;
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            // Auto-hide success messages after 5 seconds
            if (type === 'success') {
                setTimeout(() => {
                    alertContainer.innerHTML = '';
                }, 5000);
            }
        }
    </script>
</body>
</html>
