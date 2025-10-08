<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Admin Dashboard'; ?></title>
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
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stat-card .icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .stat-card .value {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
        }
        .stat-card .label {
            color: #666;
            font-size: 0.9rem;
        }
        .filters {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
        }
        .filters label {
            font-weight: 600;
        }
        .filters select {
            padding: 0.5rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 5px;
            font-size: 1rem;
        }
        .filters button {
            padding: 0.5rem 1.5rem;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }
        .filters button:hover {
            background: #5568d3;
        }
        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #f8f9fa;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e2e8f0;
        }
        td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        tr:hover {
            background: #f8f9fa;
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
        .badge-personal { background: #e0e7ff; color: #3730a3; }
        .badge-business { background: #fce7f3; color: #831843; }
        .badge-home { background: #dcfce7; color: #14532d; }
        .badge-car { background: #fef3c7; color: #78350f; }
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        .btn {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary { background: #667eea; color: white; }
        .btn-success { background: #10b981; color: white; }
        .btn-danger { background: #ef4444; color: white; }
        .btn:hover { opacity: 0.8; }
        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        .empty {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        .back-link {
            display: inline-block;
            color: white;
            text-decoration: none;
            margin-top: 1rem;
            opacity: 0.9;
        }
        .back-link:hover {
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-chart-line"></i> Admin Dashboard</h1>
        <p class="subtitle">Manage loan inquiries and applications</p>
        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <a href="<?php echo base_url(); ?>" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Website
            </a>
            <a href="<?php echo base_url('index.php/settings'); ?>" class="back-link">
                <i class="fas fa-cog"></i> Settings
            </a>
        </div>
    </div>
    
    <div class="container">
        <?php if (isset($error)): ?>
            <div class="error">
                <strong>Error:</strong> <?php echo $error; ?>
                <br><br>
                <strong>Troubleshooting:</strong>
                <ul>
                    <li>Make sure MySQL is running in XAMPP</li>
                    <li>Check if database 'loan_system' exists</li>
                    <li>Verify database configuration in application/config/database.php</li>
                    <li>Visit <a href="<?php echo base_url('test-database.php'); ?>">test-database.php</a> to diagnose</li>
                </ul>
            </div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <?php if (isset($statistics) && !empty($statistics)): ?>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">📊</div>
                <div class="value"><?php echo $statistics['total']; ?></div>
                <div class="label">Total Inquiries</div>
            </div>
            <div class="stat-card">
                <div class="icon">⏳</div>
                <div class="value"><?php echo $statistics['pending']; ?></div>
                <div class="label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="icon">📞</div>
                <div class="value"><?php echo $statistics['contacted']; ?></div>
                <div class="label">Contacted</div>
            </div>
            <div class="stat-card">
                <div class="icon">✅</div>
                <div class="value"><?php echo $statistics['approved']; ?></div>
                <div class="label">Approved</div>
            </div>
            <div class="stat-card">
                <div class="icon">👤</div>
                <div class="value"><?php echo $statistics['personal']; ?></div>
                <div class="label">Personal Loans</div>
            </div>
            <div class="stat-card">
                <div class="icon">💼</div>
                <div class="value"><?php echo $statistics['business']; ?></div>
                <div class="label">Business Loans</div>
            </div>
            <div class="stat-card">
                <div class="icon">🏠</div>
                <div class="value"><?php echo $statistics['home']; ?></div>
                <div class="label">Home Loans</div>
            </div>
            <div class="stat-card">
                <div class="icon">🚗</div>
                <div class="value"><?php echo $statistics['car']; ?></div>
                <div class="label">Car Loans</div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Filters -->
        <div class="filters">
            <form method="GET" action="" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                <div>
                    <label>Status:</label>
                    <select name="status">
                        <option value="">All</option>
                        <option value="pending" <?php echo (isset($current_status) && $current_status == 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="contacted" <?php echo (isset($current_status) && $current_status == 'contacted') ? 'selected' : ''; ?>>Contacted</option>
                        <option value="approved" <?php echo (isset($current_status) && $current_status == 'approved') ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo (isset($current_status) && $current_status == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>
                
                <div>
                    <label>Loan Type:</label>
                    <select name="loan_type">
                        <option value="">All</option>
                        <option value="personal" <?php echo (isset($current_loan_type) && $current_loan_type == 'personal') ? 'selected' : ''; ?>>Personal</option>
                        <option value="business" <?php echo (isset($current_loan_type) && $current_loan_type == 'business') ? 'selected' : ''; ?>>Business</option>
                        <option value="home" <?php echo (isset($current_loan_type) && $current_loan_type == 'home') ? 'selected' : ''; ?>>Home</option>
                        <option value="car" <?php echo (isset($current_loan_type) && $current_loan_type == 'car') ? 'selected' : ''; ?>>Car</option>
                    </select>
                </div>
                
                <button type="submit"><i class="fas fa-filter"></i> Filter</button>
                <a href="<?php echo base_url('index.php/admin'); ?>" class="btn btn-primary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </form>
        </div>
        
        <!-- Inquiries Table -->
        <div class="table-container">
            <?php if (isset($inquiries) && count($inquiries) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>IC Number</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Loan Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inquiries as $inquiry): ?>
                    <tr>
                        <td><?php echo $inquiry['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($inquiry['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($inquiry['ic_number'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($inquiry['email']); ?></td>
                        <td><?php echo htmlspecialchars($inquiry['phone']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $inquiry['loan_type']; ?>">
                                <?php echo ucfirst($inquiry['loan_type']); ?>
                            </span>
                        </td>
                        <td><strong>RM <?php echo number_format($inquiry['loan_amount'], 2); ?></strong></td>
                        <td>
                            <span class="badge badge-<?php echo $inquiry['status']; ?>">
                                <?php echo ucfirst($inquiry['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d M Y', strtotime($inquiry['created_at'])); ?></td>
                        <td class="actions">
                            <a href="<?php echo base_url('index.php/admin/view/' . $inquiry['id']); ?>" class="btn btn-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty">
                <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                <h3>No inquiries found</h3>
                <p>New loan applications will appear here.</p>
                <br>
                <a href="<?php echo base_url(); ?>" class="btn btn-primary">Go to Landing Page</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Auto-refresh statistics every 30 seconds
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
