<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo isset($meta_description) ? $meta_description : 'Financial Loan Solutions'; ?>">
    <title><?php echo isset($page_title) ? $page_title : 'Financial Loan Solutions'; ?></title>
    <link rel="stylesheet" href="<?php echo base_url('public/css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <div class="logo">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span><?php echo isset($site['name']) ? htmlspecialchars($site['name']) : 'QuickLoan'; ?></span>
                </div>
                <div class="nav-menu" id="navMenu">
                    <a href="#home" class="nav-link">Home</a>
                    <a href="#services" class="nav-link">Services</a>
                    <a href="#how-it-works" class="nav-link">How It Works</a>
                    <a href="#benefits" class="nav-link">Benefits</a>
                    <a href="#contact" class="nav-link">Contact</a>
                    <a href="#apply" class="btn btn-primary">Apply Now</a>
                </div>
                <div class="hamburger" id="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </nav>
