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
    
    // Form Submission
    const loanApplicationForm = document.getElementById('loanApplicationForm');
    const formMessage = document.getElementById('formMessage');
    
    if (loanApplicationForm) {
        loanApplicationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
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
                try {
                    const result = JSON.parse(text);
                    if (result.success) {
                        formMessage.className = 'form-message success';
                        formMessage.textContent = result.message;
                        loanApplicationForm.reset();
                    } else {
                        formMessage.className = 'form-message error';
                        formMessage.textContent = result.message;
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    formMessage.className = 'form-message error';
                    formMessage.textContent = 'Server error: ' + text.substring(0, 100);
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
                formMessage.textContent = 'Connection error: ' + error.message + '. Please check your database connection.';
                
                // Reset button
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                
                // Scroll to message
                formMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
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
