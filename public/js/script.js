// Mobile Navigation Toggle
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('navMenu');
    
    if (hamburger) {
        hamburger.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }
    
    // Close mobile menu when clicking on a link
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('active');
        });
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Loan Calculator
    const loanAmount = document.getElementById('loanAmount');
    const loanTenure = document.getElementById('loanTenure');
    const interestRate = document.getElementById('interestRate');
    const loanAmountValue = document.getElementById('loanAmountValue');
    const loanTenureValue = document.getElementById('loanTenureValue');
    const interestRateValue = document.getElementById('interestRateValue');
    const monthlyPayment = document.getElementById('monthlyPayment');
    
    function formatCurrency(amount) {
        return 'RM ' + amount.toLocaleString('en-MY', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }
    
    function calculateMonthlyPayment() {
        const principal = parseFloat(loanAmount.value);
        const years = parseFloat(loanTenure.value);
        const rate = parseFloat(interestRate.value) / 100 / 12;
        const numberOfPayments = years * 12;
        
        // Monthly payment formula: M = P[r(1+r)^n]/[(1+r)^n-1]
        const monthlyPaymentAmount = principal * (rate * Math.pow(1 + rate, numberOfPayments)) / (Math.pow(1 + rate, numberOfPayments) - 1);
        
        if (isFinite(monthlyPaymentAmount)) {
            monthlyPayment.textContent = formatCurrency(Math.round(monthlyPaymentAmount));
        } else {
            monthlyPayment.textContent = formatCurrency(Math.round(principal / numberOfPayments));
        }
    }
    
    if (loanAmount) {
        loanAmount.addEventListener('input', function() {
            loanAmountValue.textContent = formatCurrency(this.value);
            calculateMonthlyPayment();
        });
    }
    
    if (loanTenure) {
        loanTenure.addEventListener('input', function() {
            loanTenureValue.textContent = this.value + ' year' + (this.value > 1 ? 's' : '');
            calculateMonthlyPayment();
        });
    }
    
    if (interestRate) {
        interestRate.addEventListener('input', function() {
            interestRateValue.textContent = this.value + '%';
            calculateMonthlyPayment();
        });
    }
    
    // Initialize calculator
    if (loanAmount && loanTenure && interestRate) {
        calculateMonthlyPayment();
    }
    
    // Malaysian Validation Functions
    function validateMalaysianPhone(phone) {
        // Remove spaces, dashes, and plus signs for validation
        const cleanPhone = phone.replace(/[\s\-\+]/g, '');
        
        // Malaysian phone patterns:
        // Mobile: 01x-xxxx-xxxx (10-11 digits starting with 01)
        // Landline: 0x-xxxx-xxxx (9-10 digits starting with 0)
        // With country code: +60x-xxxx-xxxx or 60x-xxxx-xxxx
        
        const patterns = [
            /^01[0-9]{8,9}$/,           // 01xxxxxxxx or 01xxxxxxxxx
            /^0[2-9][0-9]{7,8}$/,       // 0xxxxxxxx or 0xxxxxxxxx (landline)
            /^601[0-9]{8,9}$/,          // 601xxxxxxxx (with country code, no +)
            /^60[2-9][0-9]{7,8}$/       // 60xxxxxxxx (landline with country code)
        ];
        
        return patterns.some(pattern => pattern.test(cleanPhone));
    }
    
    function validateMalaysianIC(ic) {
        // Malaysian IC format: YYMMDD-PB-###G
        // Remove dashes for validation
        const cleanIC = ic.replace(/[\s\-]/g, '');
        
        // Must be exactly 12 digits
        if (!/^[0-9]{12}$/.test(cleanIC)) {
            return false;
        }
        
        // Validate date part (YYMMDD)
        const year = parseInt(cleanIC.substring(0, 2));
        const month = parseInt(cleanIC.substring(2, 4));
        const day = parseInt(cleanIC.substring(4, 6));
        
        if (month < 1 || month > 12) return false;
        if (day < 1 || day > 31) return false;
        
        // Validate place of birth code (must be valid Malaysian state code)
        const birthPlace = parseInt(cleanIC.substring(6, 8));
        const validStateCodes = [
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, // Original states
            11, 12, 13, 14, 15, 16,         // Additional states
            21, 22, 23, 24,                 // Born in other countries
            59, 60,                         // Unknown/special cases
            71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82 // Other categories
        ];
        
        return validStateCodes.includes(birthPlace) || (birthPlace >= 1 && birthPlace <= 82);
    }
    
    function validateMalaysianPostcode(postcode) {
        // Malaysian postcode: 5 digits
        const cleanPostcode = postcode.replace(/\s/g, '');
        return /^[0-9]{5}$/.test(cleanPostcode);
    }
    
    function formatMalaysianPhone(phone) {
        // Format phone number for display
        const cleanPhone = phone.replace(/[\s\-\+]/g, '');
        
        if (cleanPhone.startsWith('60')) {
            // With country code
            if (cleanPhone.length === 11 || cleanPhone.length === 12) {
                return '+' + cleanPhone.substring(0, 2) + ' ' + 
                       cleanPhone.substring(2, 4) + '-' + 
                       cleanPhone.substring(4, 8) + '-' + 
                       cleanPhone.substring(8);
            }
        } else if (cleanPhone.startsWith('0')) {
            // Without country code
            if (cleanPhone.length === 10 || cleanPhone.length === 11) {
                return cleanPhone.substring(0, 3) + '-' + 
                       cleanPhone.substring(3, 7) + '-' + 
                       cleanPhone.substring(7);
            } else if (cleanPhone.length === 9) {
                return cleanPhone.substring(0, 2) + '-' + 
                       cleanPhone.substring(2, 6) + '-' + 
                       cleanPhone.substring(6);
            }
        }
        
        return phone; // Return original if can't format
    }
    
    function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        
        // Remove existing error
        const existingError = field.parentElement.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
        
        // Add error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.textContent = message;
        errorDiv.style.color = '#ef4444';
        errorDiv.style.fontSize = '0.875rem';
        errorDiv.style.marginTop = '0.25rem';
        
        field.parentElement.appendChild(errorDiv);
        field.style.borderColor = '#ef4444';
        
        // Focus the field
        field.focus();
    }
    
    function clearFieldError(fieldId) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        
        const existingError = field.parentElement.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
        field.style.borderColor = '#e2e8f0';
    }
    
    // Form Submission
    const loanApplicationForm = document.getElementById('loanApplicationForm');
    const formMessage = document.getElementById('formMessage');
    
    if (loanApplicationForm) {
        // Real-time validation on phone input
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('blur', function() {
                if (this.value) {
                    if (!validateMalaysianPhone(this.value)) {
                        showFieldError('phone', 'Please enter a valid Malaysian phone number (e.g., 012-345-6789 or +6012-345-6789)');
                    } else {
                        clearFieldError('phone');
                        // Auto-format the phone number
                        this.value = formatMalaysianPhone(this.value);
                    }
                }
            });
            
            phoneInput.addEventListener('input', function() {
                clearFieldError('phone');
            });
        }
        
        // Real-time validation on IC input (if exists)
        const icInput = document.getElementById('ic_number');
        if (icInput) {
            icInput.addEventListener('blur', function() {
                if (this.value) {
                    if (!validateMalaysianIC(this.value)) {
                        showFieldError('ic_number', 'Please enter a valid Malaysian IC number (e.g., 901231-01-5678)');
                    } else {
                        clearFieldError('ic_number');
                    }
                }
            });
            
            icInput.addEventListener('input', function() {
                clearFieldError('ic_number');
            });
        }
        
        // Real-time validation on postcode (if exists)
        const postcodeInput = document.getElementById('postcode');
        if (postcodeInput) {
            postcodeInput.addEventListener('blur', function() {
                if (this.value) {
                    if (!validateMalaysianPostcode(this.value)) {
                        showFieldError('postcode', 'Please enter a valid Malaysian postcode (5 digits, e.g., 50450)');
                    } else {
                        clearFieldError('postcode');
                    }
                }
            });
            
            postcodeInput.addEventListener('input', function() {
                clearFieldError('postcode');
            });
        }
        
        loanApplicationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear all previous errors
            document.querySelectorAll('.field-error').forEach(err => err.remove());
            document.querySelectorAll('input, select, textarea').forEach(field => {
                field.style.borderColor = '#e2e8f0';
            });
            
            // Validate Malaysian phone number
            const phone = document.getElementById('phone').value;
            if (!validateMalaysianPhone(phone)) {
                showFieldError('phone', 'Please enter a valid Malaysian phone number (e.g., 012-345-6789 or +6012-345-6789)');
                return;
            }
            
            // Validate IC if field exists
            const icField = document.getElementById('ic_number');
            if (icField && icField.value) {
                if (!validateMalaysianIC(icField.value)) {
                    showFieldError('ic_number', 'Please enter a valid Malaysian IC number (e.g., 901231-01-5678)');
                    return;
                }
            }
            
            // Validate postcode if field exists
            const postcodeField = document.getElementById('postcode');
            if (postcodeField && postcodeField.value) {
                if (!validateMalaysianPostcode(postcodeField.value)) {
                    showFieldError('postcode', 'Please enter a valid Malaysian postcode (5 digits)');
                    return;
                }
            }
            
            // Validate loan amount (minimum RM 1,000)
            const loanAmount = parseFloat(document.getElementById('loan_amount').value);
            if (loanAmount < 1000) {
                showFieldError('loan_amount', 'Minimum loan amount is RM 1,000');
                return;
            }
            
            // Validate monthly income if provided
            const incomeField = document.getElementById('income');
            if (incomeField && incomeField.value) {
                const income = parseFloat(incomeField.value);
                if (income < 1000) {
                    showFieldError('income', 'Please enter a valid monthly income (minimum RM 1,000)');
                    return;
                }
                
                // Debt-to-income ratio check (loan should not exceed 10x monthly income)
                if (loanAmount > income * 120) { // 10 years * 12 months
                    formMessage.className = 'form-message error';
                    formMessage.textContent = 'Warning: The loan amount seems high compared to your income. Our team will review your application.';
                    formMessage.style.display = 'block';
                    setTimeout(() => {
                        formMessage.style.display = 'none';
                    }, 5000);
                }
            }
            
            const formData = new FormData(this);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Submitting...';
            submitBtn.disabled = true;
            
            // Get the base URL dynamically
            const baseUrl = window.location.origin + window.location.pathname.replace('index.php', '');
            const submitUrl = baseUrl + 'index.php/submit-inquiry';
            
            console.log('Submitting to:', submitUrl); // Debug
            console.log('Form data:', data); // Debug
            
            // Send data to server
            fetch(submitUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)
            })
            .then(response => {
                console.log('Response status:', response.status); // Debug
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.text(); // Get as text first
            })
            .then(text => {
                console.log('Response text:', text); // Debug
                console.log('Response length:', text.length); // Debug
                
                // Check if response is empty
                if (!text || text.trim().length === 0) {
                    throw new Error('Empty response from server. Please check if MySQL is running and database is connected.');
                }
                
                try {
                    const result = JSON.parse(text);
                    console.log('Parsed result:', result); // Debug
                    
                    if (result.success) {
                        formMessage.className = 'form-message success';
                        formMessage.textContent = result.message;
                        formMessage.style.display = 'block';
                        loanApplicationForm.reset();
                        
                        // Scroll to success message
                        formMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else {
                        formMessage.className = 'form-message error';
                        formMessage.textContent = result.message;
                        formMessage.style.display = 'block';
                        
                        // Scroll to error message
                        formMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.error('Response text:', text);
                    
                    formMessage.className = 'form-message error';
                    
                    // More helpful error message
                    if (text.includes('Fatal error')) {
                        formMessage.textContent = 'Server Error: PHP Fatal Error. Check error logs or test-form-submit.php for details.';
                    } else if (text.includes('Warning')) {
                        formMessage.textContent = 'Server Warning: ' + text.substring(0, 200);
                    } else if (text.includes('<!DOCTYPE') || text.includes('<html')) {
                        formMessage.textContent = 'Server returned HTML instead of JSON. The page may have redirected or encountered an error.';
                    } else {
                        formMessage.textContent = 'Invalid server response. Response: ' + text.substring(0, 150);
                    }
                    
                    formMessage.style.display = 'block';
                }
                
                // Reset button
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                
                // Scroll to message
                formMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                
                // Hide message after 5 seconds
                setTimeout(() => {
                    formMessage.style.display = 'none';
                }, 5000);
            })
            .catch(error => {
                console.error('Fetch error:', error); // Debug
                formMessage.className = 'form-message error';
                
                // More specific error messages
                if (error.message.includes('Empty response')) {
                    formMessage.innerHTML = '<strong>Connection Error:</strong><br>' +
                        '1. Check if MySQL is running in XAMPP<br>' +
                        '2. Check if database "loan_system" exists<br>' +
                        '3. Try visiting: <a href="test-form-submit.php" target="_blank">test-form-submit.php</a>';
                } else if (error.message.includes('Network')) {
                    formMessage.textContent = 'Network error: Cannot reach server. Check if Apache is running in XAMPP.';
                } else {
                    formMessage.textContent = 'Error: ' + error.message;
                }
                
                formMessage.style.display = 'block';
                
                // Reset button
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                
                // Scroll to message
                formMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        });
    }
    
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 100) {
            navbar.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
        } else {
            navbar.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
        }
    });
    
    // Intersection Observer for fade-in animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe all sections
    document.querySelectorAll('section').forEach(section => {
        observer.observe(section);
    });
});
