// Global error suppression and blocking for third-party analytics (Stape, etc.)
(function() {
    // Block all requests to stape.info completely
    const blockStapeRequests = function(url) {
        const urlString = typeof url === 'string' ? url : (url && url.url ? url.url : '');
        return urlString && (urlString.includes('stape.info') || urlString.includes('ap.stape.info'));
    };
    
    // Intercept fetch requests to block stape.info completely
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        if (blockStapeRequests(args[0])) {
            // Return a fake successful response immediately without making the request
            return Promise.resolve(new Response(null, { 
                status: 200, 
                statusText: 'OK',
                headers: { 'Content-Type': 'application/json' }
            }));
        }
        return originalFetch.apply(this, args);
    };
    
    // Intercept XMLHttpRequest to block stape.info
    const originalXHROpen = XMLHttpRequest.prototype.open;
    const originalXHRSend = XMLHttpRequest.prototype.send;
    
    XMLHttpRequest.prototype.open = function(method, url, ...rest) {
        if (blockStapeRequests(url)) {
            this._blocked = true;
            // Store the URL but don't actually open the connection
            this._url = url;
            return;
        }
        return originalXHROpen.apply(this, [method, url, ...rest]);
    };
    
    XMLHttpRequest.prototype.send = function(...args) {
        if (this._blocked) {
            // Simulate a successful response without making the request
            setTimeout(() => {
                Object.defineProperty(this, 'status', { value: 200, writable: false });
                Object.defineProperty(this, 'statusText', { value: 'OK', writable: false });
                Object.defineProperty(this, 'readyState', { value: 4, writable: false });
                if (this.onload) this.onload();
                if (this.onreadystatechange) this.onreadystatechange();
            }, 0);
            return;
        }
        return originalXHRSend.apply(this, args);
    };
    
    // Suppress console errors for stape.info
    const originalError = console.error;
    console.error = function(...args) {
        const errorMsg = args[0] ? String(args[0]) : '';
        if (errorMsg.includes('stape.info') || errorMsg.includes('ap.stape.info') || 
            errorMsg.includes('422') || errorMsg.includes('Unprocessable')) {
            // Check if it's related to stape
            const fullMsg = args.join(' ');
            if (fullMsg.includes('stape') || fullMsg.includes('ap.stape')) {
                return; // Suppress stape-related errors
            }
        }
        originalError.apply(console, args);
    };
    
    // Suppress network errors in console
    window.addEventListener('error', function(e) {
        if (e.target && (
            (e.target.src && e.target.src.includes('stape.info')) ||
            (e.message && e.message.includes('stape.info'))
        )) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, true);
    
    // Suppress unhandled promise rejections from stape
    window.addEventListener('unhandledrejection', function(e) {
        if (e.reason && (
            (typeof e.reason === 'string' && e.reason.includes('stape.info')) ||
            (e.reason.message && e.reason.message.includes('stape.info'))
        )) {
            e.preventDefault();
            return false;
        }
    });
})();

// Meta Page View Tracking
(function() {
    // Helper function to get cookie value
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }
    
    // Helper function to detect device type
    function getDeviceType() {
        const width = window.innerWidth || document.documentElement.clientWidth;
        if (width < 768) return 'mobile';
        if (width < 1024) return 'tablet';
        return 'desktop';
    }
    
    // Helper function to detect browser
    function getBrowser() {
        const ua = navigator.userAgent;
        if (ua.indexOf('Chrome') > -1) return 'Chrome';
        if (ua.indexOf('Firefox') > -1) return 'Firefox';
        if (ua.indexOf('Safari') > -1) return 'Safari';
        if (ua.indexOf('Edge') > -1) return 'Edge';
        if (ua.indexOf('Opera') > -1) return 'Opera';
        return 'Unknown';
    }
    
    // Helper function to detect OS
    function getOS() {
        const ua = navigator.userAgent;
        if (ua.indexOf('Windows') > -1) return 'Windows';
        if (ua.indexOf('Mac') > -1) return 'macOS';
        if (ua.indexOf('Linux') > -1) return 'Linux';
        if (ua.indexOf('Android') > -1) return 'Android';
        if (ua.indexOf('iOS') > -1 || ua.indexOf('iPhone') > -1 || ua.indexOf('iPad') > -1) return 'iOS';
        return 'Unknown';
    }
    
    // -------------------------------------------------------
    // Ad source detection & persistence
    // -------------------------------------------------------
    /**
     * Detect traffic source from URL params.
     * Priority: explicit ?source_from= > fbclid > ttclid > gclid > utm_source
     * @param {URLSearchParams} params
     * @returns {string} 'fb'|'tt'|'go'|'uk'
     */
    function detectAdSource(params) {
        const explicit = params.get('source_from');
        if (explicit && ['fb','tt','go','uk'].includes(explicit)) return explicit;

        if (params.get('fbclid')) return 'fb';
        if (params.get('ttclid')) return 'tt';
        if (params.get('gclid'))  return 'go';

        const utm = (params.get('utm_source') || '').toLowerCase();
        if (utm.includes('facebook') || utm.includes('fb') || utm === 'fb') return 'fb';
        if (utm.includes('tiktok')   || utm.includes('tt') || utm === 'tt') return 'tt';
        if (utm.includes('google')   || utm.includes('go') || utm === 'go') return 'go';

        return 'uk';
    }

    /**
     * Traffic source for conversion pixels: URL first, then sessionStorage if still unknown.
     */
    function resolveAdSourceForPixels() {
        try {
            const p = new URLSearchParams(window.location.search);
            let s = typeof detectAdSource === 'function' ? detectAdSource(p) : 'uk';
            if (s === 'uk') {
                const st = sessionStorage.getItem('source_from');
                if (st && ['fb', 'tt', 'go'].indexOf(st) !== -1) {
                    s = st;
                }
            }
            return s;
        } catch (e) {
            return 'uk';
        }
    }

    // Expose globally so form submit handlers outside this IIFE can use it
    window.detectAdSource = detectAdSource;
    window.resolveAdSourceForPixels = resolveAdSourceForPixels;

    // Persist ad params in sessionStorage on first landing so they survive multi-step forms
    (function persistAdParams() {
        try {
            const p = new URLSearchParams(window.location.search);
            const src = detectAdSource(p);
            if (src !== 'uk') sessionStorage.setItem('source_from', src);
            if (p.get('adscmp')) sessionStorage.setItem('adscmp', p.get('adscmp'));
            if (p.get('adst'))   sessionStorage.setItem('adst',   p.get('adst'));
            if (p.get('adnm'))   sessionStorage.setItem('adnm',   p.get('adnm'));
        } catch(e) {}
    })();
    // -------------------------------------------------------

    // Track page view
    function trackPageView() {
        try {
            // Get Meta tracking data
            const urlParams = new URLSearchParams(window.location.search);
            const fbclid = urlParams.get('fbclid');
            const fbp = getCookie('_fbp');
            
            // Prepare page view data
            const pageViewData = {
                page_url: window.location.href,
                page_path: window.location.pathname,
                page_title: document.title,
                referrer_url: document.referrer || null,
                meta_click_id: fbclid || null,
                meta_profile_id: fbp || null,
                device_type: getDeviceType(),
                browser: getBrowser(),
                os: getOS(),
                screen_width: window.screen.width,
                screen_height: window.screen.height
            };
            
            // Get base URL
            const baseUrl = window.baseUrl || (window.location.origin + window.location.pathname.replace('index.php', ''));
            const trackUrl = baseUrl + 'index.php/track-page-view';
            
            // Send page view data (use sendBeacon for better reliability, fallback to fetch)
            if (navigator.sendBeacon) {
                const blob = new Blob([JSON.stringify(pageViewData)], { type: 'application/json' });
                navigator.sendBeacon(trackUrl, blob);
            } else {
                // Fallback to fetch
                fetch(trackUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(pageViewData),
                    keepalive: true // Keep request alive even if page unloads
                }).catch(err => {
                    // Silently fail - don't break page load
                    console.debug('Page view tracking failed:', err);
                });
            }
        } catch (error) {
            // Silently fail - don't break page load
            console.debug('Page view tracking error:', error);
        }
    }
    
    // Track page view when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', trackPageView);
    } else {
        // DOM already loaded
        trackPageView();
    }
    
    // Track form step progression (FIHW - Filled In Half Way)
    function trackFormStep() {
        try {
            // Get Meta tracking data
            const urlParams = new URLSearchParams(window.location.search);
            const fbclid = urlParams.get('fbclid');
            const fbp = getCookie('_fbp');
            
            // Prepare form step data
            const formStepData = {
                page_url: window.location.href,
                page_path: window.location.pathname,
                page_title: document.title || 'Loan Application Form',
                action: 'FIHW', // Filled In Half Way
                referrer_url: document.referrer || null,
                meta_click_id: fbclid || null,
                meta_profile_id: fbp || null,
                device_type: getDeviceType(),
                browser: getBrowser(),
                os: getOS(),
                screen_width: window.screen.width,
                screen_height: window.screen.height
            };
            
            // Get base URL
            const baseUrl = window.baseUrl || (window.location.origin + window.location.pathname.replace('index.php', ''));
            const trackUrl = baseUrl + 'index.php/track-form-step';
            
            // Send form step data (use sendBeacon for better reliability, fallback to fetch)
            if (navigator.sendBeacon) {
                const blob = new Blob([JSON.stringify(formStepData)], { type: 'application/json' });
                navigator.sendBeacon(trackUrl, blob);
            } else {
                // Fallback to fetch
                fetch(trackUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formStepData),
                    keepalive: true // Keep request alive even if page unloads
                }).catch(err => {
                    // Silently fail - don't break form functionality
                    console.debug('Form step tracking failed:', err);
                });
            }
        } catch (error) {
            // Silently fail - don't break form functionality
            console.debug('Form step tracking error:', error);
        }
    }
    
    // Make trackFormStep available globally
    window.trackFormStep = trackFormStep;
})();

// Malaysian Validation Functions (Global Scope)
// These functions need to be accessible to all form validation code
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

// Modal Popup and Form Synchronization
(function() {
    'use strict';
    
    // Session storage key for tracking if modal was closed
    const MODAL_CLOSED_KEY = 'inquiry_modal_closed';
    
    // Check for URL parameters
    function hasUrlParameters() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.toString().length > 0;
    }
    
    // Check if modal was already closed in this session
    function wasModalClosed() {
        try {
            return sessionStorage.getItem(MODAL_CLOSED_KEY) === 'true';
        } catch (e) {
            // If sessionStorage is not available, return false
            return false;
        }
    }
    
    // Mark modal as closed in session storage
    function markModalAsClosed() {
        try {
            sessionStorage.setItem(MODAL_CLOSED_KEY, 'true');
        } catch (e) {
            // If sessionStorage is not available, silently fail
            console.debug('Could not save modal closed state:', e);
        }
    }
    
    // Show modal if URL parameters exist and modal wasn't closed
    function showModalIfNeeded() {
        if (hasUrlParameters() && !wasModalClosed()) {
            const modal = document.getElementById('inquiryModal');
            if (modal) {
                modal.style.display = 'flex';
                modal.classList.add('show');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
                
                // Ensure popup form only shows step 1 when triggered by URL parameters
                const popupForm = document.getElementById('popupLoanApplicationForm');
                if (popupForm) {
                    // Hide any other steps if they exist (should only be step 1)
                    const allSteps = popupForm.querySelectorAll('.form-step');
                    allSteps.forEach((step, index) => {
                        const stepNum = parseInt(step.getAttribute('data-step')) || (index + 1);
                        if (stepNum === 1) {
                            // Show only step 1 - ensure it's visible
                            step.classList.add('active');
                            step.style.display = 'block';
                            step.style.opacity = '1';
                            // Ensure all form fields and labels are visible
                            step.querySelectorAll('input, select, textarea, label').forEach(element => {
                                element.style.display = '';
                                element.style.visibility = 'visible';
                                element.style.opacity = '1';
                            });
                        } else {
                            // Hide any other steps
                            step.classList.remove('active');
                            step.style.display = 'none';
                        }
                    });
                    
                    // Clear any previous form data when showing popup with URL parameters
                    popupForm.reset();
                    const popupFormMessage = document.getElementById('popupFormMessage');
                    if (popupFormMessage) {
                        popupFormMessage.style.display = 'none';
                        popupFormMessage.textContent = '';
                    }
                    
                    // Clear any field errors
                    popupForm.querySelectorAll('.field-error').forEach(err => err.remove());
                    popupForm.querySelectorAll('input, select, textarea').forEach(field => {
                        field.style.borderColor = '#e2e8f0';
                    });
                }
            }
        }
    }
    
    // Close modal and mark as closed
    function closeModal() {
        const modal = document.getElementById('inquiryModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.style.overflow = ''; // Restore scrolling
            // Mark as closed so it won't show again in this session
            markModalAsClosed();
            // Scroll to top of the page
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
    
    // Flag to prevent infinite loops during syncing
    let isSyncing = false;
    
    // Sync data from popup form to main form
    function syncToMainForm(popupFieldId, mainFieldId) {
        const popupField = document.getElementById(popupFieldId);
        const mainField = document.getElementById(mainFieldId);
        
        if (popupField && mainField) {
            // Sync on input/change
            popupField.addEventListener('input', function() {
                if (isSyncing || mainField.type === 'file') {
                    return;
                }
                // Only sync if value actually changed
                if (mainField.value !== this.value) {
                    isSyncing = true;
                    mainField.value = this.value;
                    // Only trigger input event, not change (change fires on blur)
                    mainField.dispatchEvent(new Event('input', { bubbles: true }));
                    isSyncing = false;
                }
            });
            
            popupField.addEventListener('change', function() {
                if (isSyncing || mainField.type === 'file') {
                    return;
                }
                // Only sync if value actually changed
                if (mainField.value !== this.value) {
                    isSyncing = true;
                    mainField.value = this.value;
                    // Don't dispatch change event to avoid infinite loop
                    // The value change itself is enough
                    isSyncing = false;
                }
            });
        }
    }
    
    // Sync data from main form to popup form
    function syncToPopupForm(mainFieldId, popupFieldId) {
        const mainField = document.getElementById(mainFieldId);
        const popupField = document.getElementById(popupFieldId);
        
        if (mainField && popupField) {
            mainField.addEventListener('input', function() {
                if (isSyncing || popupField.type === 'file') {
                    return;
                }
                // Only sync if value actually changed
                if (popupField.value !== this.value) {
                    isSyncing = true;
                    popupField.value = this.value;
                    // Only trigger input event, not change (change fires on blur)
                    popupField.dispatchEvent(new Event('input', { bubbles: true }));
                    isSyncing = false;
                }
            });
            
            mainField.addEventListener('change', function() {
                if (isSyncing || popupField.type === 'file') {
                    return;
                }
                // Only sync if value actually changed
                if (popupField.value !== this.value) {
                    isSyncing = true;
                    popupField.value = this.value;
                    // Don't dispatch change event to avoid infinite loop
                    // The value change itself is enough
                    isSyncing = false;
                }
            });
        }
    }
    
    // Sync all form fields
    function syncAllFields() {
        // Field mappings: popup field ID -> main field ID
        const fieldMappings = [
            ['popup_name', 'name'],
            ['popup_email', 'email'],
            ['popup_phone', 'phone'],
            ['popup_ic_number', 'ic_number'],
            ['popup_loan_type', 'loan_type'],
            ['popup_loan_amount', 'loan_amount'],
            ['popup_income', 'income'],
            ['popup_message', 'message'],
            ['popup_company_name', 'company_name'],
            ['popup_job_position', 'job_position'],
            ['popup_employment_type', 'employment_type'],
            ['popup_years_employed', 'years_employed'],
            ['popup_company_address', 'company_address'],
            ['popup_company_phone', 'company_phone']
        ];
        
        // Sync popup to main
        fieldMappings.forEach(([popupId, mainId]) => {
            syncToMainForm(popupId, mainId);
        });
        
        // Sync main to popup
        fieldMappings.forEach(([popupId, mainId]) => {
            syncToPopupForm(mainId, popupId);
        });
    }
    
    // Handle file upload sync (special case)
    function syncFileUpload() {
        const popupFileInput = document.getElementById('popup_proof_of_income');
        const mainFileInput = document.getElementById('proof_of_income');
        
        if (popupFileInput && mainFileInput) {
            popupFileInput.addEventListener('change', function(e) {
                if (this.files && this.files.length > 0) {
                    // Create a new FileList-like object for main input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(this.files[0]);
                    mainFileInput.files = dataTransfer.files;
                    
                    // Trigger file preview on main form
                    if (mainFileInput.files && mainFileInput.files.length > 0) {
                        const file = mainFileInput.files[0];
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const previewImage = document.getElementById('previewImage');
                            const fileName = document.getElementById('fileName');
                            const fileSize = document.getElementById('fileSize');
                            const filePreview = document.getElementById('filePreview');
                            
                            if (previewImage) previewImage.src = e.target.result;
                            if (fileName) fileName.textContent = file.name;
                            if (fileSize) fileSize.textContent = formatFileSize(file.size);
                            if (filePreview) filePreview.style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    }
                }
            });
            
            mainFileInput.addEventListener('change', function(e) {
                if (this.files && this.files.length > 0) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(this.files[0]);
                    popupFileInput.files = dataTransfer.files;
                    
                    // Trigger file preview on popup form
                    if (popupFileInput.files && popupFileInput.files.length > 0) {
                        const file = popupFileInput.files[0];
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const popupPreviewImage = document.getElementById('popupPreviewImage');
                            const popupFileName = document.getElementById('popupFileName');
                            const popupFileSize = document.getElementById('popupFileSize');
                            const popupFilePreview = document.getElementById('popupFilePreview');
                            
                            if (popupPreviewImage) popupPreviewImage.src = e.target.result;
                            if (popupFileName) popupFileName.textContent = file.name;
                            if (popupFileSize) {
                                // Format file size
                                const bytes = file.size;
                                if (bytes === 0) {
                                    popupFileSize.textContent = '0 Bytes';
                                } else {
                                    const k = 1024;
                                    const sizes = ['Bytes', 'KB', 'MB'];
                                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                                    popupFileSize.textContent = Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
                                }
                            }
                            if (popupFilePreview) popupFilePreview.style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    }
                }
            });
        }
    }
    
    // Validate popup form step
    function validatePopupStep(step) {
        const popupForm = document.getElementById('popupLoanApplicationForm');
        if (!popupForm) return false;
        
        const stepElement = popupForm.querySelector(`.form-step[data-step="${step}"]`);
        if (!stepElement) return false;
        
        const requiredFields = stepElement.querySelectorAll('[data-required="true"], [required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            const value = field.value ? field.value.trim() : '';
            const fieldType = field.type || field.tagName.toLowerCase();
            
            // Clear previous error
            clearPopupFieldError(field.id);
            
            // Check if field is empty
            if (!value) {
                showPopupFieldError(field.id, 'This field is required');
                isValid = false;
                return;
            }
            
            // Name validation (same as main form step 1)
            if (field.id === 'popup_name' && value) {
                if (value.length < 3) {
                    showPopupFieldError(field.id, 'Name must be at least 3 characters long');
                    isValid = false;
                    return;
                }
                if (value.length > 100) {
                    showPopupFieldError(field.id, 'Name must not exceed 100 characters');
                    isValid = false;
                    return;
                }
            }
            
            // Email validation
            if (fieldType === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    showPopupFieldError(field.id, 'Please enter a valid email address');
                    isValid = false;
                    return;
                }
            }
            
            // Phone validation
            if (field.id === 'popup_phone' && value) {
                if (!validateMalaysianPhone(value)) {
                    showPopupFieldError(field.id, 'Please enter a valid Malaysian phone number (e.g., 012-345-6789)');
                    isValid = false;
                    return;
                }
            }
            
            // IC validation
            if (field.id === 'popup_ic_number' && value) {
                if (!validateMalaysianIC(value)) {
                    showPopupFieldError(field.id, 'Please enter a valid Malaysian IC number (e.g., 901231-01-5678)');
                    isValid = false;
                    return;
                }
            }
            
            // Number validation for numeric fields
            if (fieldType === 'number' && value) {
                const numValue = parseFloat(value);
                if (isNaN(numValue)) {
                    showPopupFieldError(field.id, 'Please enter a valid number');
                    isValid = false;
                    return;
                }
                
                // Loan amount validation
                if (field.id === 'popup_loan_amount' && numValue < 1000) {
                    showPopupFieldError(field.id, 'Minimum loan amount is RM 1,000');
                    isValid = false;
                    return;
                }
                
                // Income validation
                if (field.id === 'popup_income' && numValue < 1000) {
                    showPopupFieldError(field.id, 'Please enter a valid monthly income (minimum RM 1,000)');
                    isValid = false;
                    return;
                }
            }
        });
        
        return isValid;
    }
    
    // Validate all popup form steps
    function validateAllPopupSteps() {
        const popupForm = document.getElementById('popupLoanApplicationForm');
        if (!popupForm) return false;
        
        const steps = popupForm.querySelectorAll('.form-step');
        for (let i = 0; i < steps.length; i++) {
            const step = parseInt(steps[i].getAttribute('data-step'));
            if (!validatePopupStep(step)) {
                return false;
            }
        }
        return true;
    }
    
    // Show error for popup field
    function showPopupFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        
        field.style.borderColor = '#ef4444';
        
        // Remove existing error
        const existingError = field.parentElement.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
        
        // Add error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.style.color = '#ef4444';
        errorDiv.style.fontSize = '0.875rem';
        errorDiv.style.marginTop = '0.25rem';
        errorDiv.textContent = message;
        field.parentElement.appendChild(errorDiv);
        
        // Focus on field after a small delay for smooth scrolling
        setTimeout(() => {
            field.focus();
        }, 100);
    }
    
    // Clear error for popup field
    function clearPopupFieldError(fieldId) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        
        field.style.borderColor = '#e2e8f0';
        const errorDiv = field.parentElement.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    }
    
    // Initialize popup form (only Step 1 - Personal Information)
    function initPopupFormSteps() {
        const popupForm = document.getElementById('popupLoanApplicationForm');
        if (!popupForm) return;
        
        const popupFormMessage = document.getElementById('popupFormMessage');
        
        // Add real-time validation for popup form fields
        const popupPhoneInput = document.getElementById('popup_phone');
        if (popupPhoneInput) {
            popupPhoneInput.addEventListener('blur', function() {
                if (this.value && !validateMalaysianPhone(this.value)) {
                    showPopupFieldError('popup_phone', 'Please enter a valid Malaysian phone number (e.g., 012-345-6789)');
                } else {
                    clearPopupFieldError('popup_phone');
                }
            });
            
            popupPhoneInput.addEventListener('input', function() {
                clearPopupFieldError('popup_phone');
            });
        }
        
        const popupIcInput = document.getElementById('popup_ic_number');
        if (popupIcInput) {
            popupIcInput.addEventListener('blur', function() {
                if (this.value && !validateMalaysianIC(this.value)) {
                    showPopupFieldError('popup_ic_number', 'Please enter a valid Malaysian IC number (e.g., 901231-01-5678)');
                } else {
                    clearPopupFieldError('popup_ic_number');
                }
            });
            
            popupIcInput.addEventListener('input', function() {
                clearPopupFieldError('popup_ic_number');
            });
        }
        
        // Handle popup form submission (Step 1 only - Personal Information)
        popupForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear all previous errors
            popupForm.querySelectorAll('.field-error').forEach(err => err.remove());
            popupForm.querySelectorAll('input, select, textarea').forEach(field => {
                field.style.borderColor = '#e2e8f0';
            });
            
            // Validate Step 1 only (personal information) - same validations as main form step 1
            if (!validatePopupStep(1)) {
                if (popupFormMessage) {
                    popupFormMessage.className = 'form-message error';
                    popupFormMessage.textContent = 'Please complete all required fields correctly.';
                    popupFormMessage.style.display = 'block';
                }
                return;
            }
            
            // Validate Malaysian phone number
            const phone = document.getElementById('popup_phone').value;
            if (!validateMalaysianPhone(phone)) {
                showPopupFieldError('popup_phone', 'Please enter a valid Malaysian phone number (e.g., 012-345-6789 or +6012-345-6789)');
                if (popupFormMessage) {
                    popupFormMessage.className = 'form-message error';
                    popupFormMessage.textContent = 'Please enter a valid phone number.';
                    popupFormMessage.style.display = 'block';
                }
                return;
            }
            
            // Validate IC
            const icField = document.getElementById('popup_ic_number');
            if (icField && icField.value) {
                if (!validateMalaysianIC(icField.value)) {
                    showPopupFieldError('popup_ic_number', 'Please enter a valid Malaysian IC number (e.g., 901231-01-5678)');
                    if (popupFormMessage) {
                        popupFormMessage.className = 'form-message error';
                        popupFormMessage.textContent = 'Please enter a valid IC number.';
                        popupFormMessage.style.display = 'block';
                    }
                    return;
                }
            }
            
            // Validate name format (same as main form)
            const nameField = document.getElementById('popup_name');
            if (nameField && nameField.value) {
                const name = nameField.value.trim();
                // Check minimum length
                if (name.length < 3) {
                    showPopupFieldError('popup_name', 'Name must be at least 3 characters long');
                    if (popupFormMessage) {
                        popupFormMessage.className = 'form-message error';
                        popupFormMessage.textContent = 'Please enter a valid name (at least 3 characters).';
                        popupFormMessage.style.display = 'block';
                    }
                    return;
                }
                // Check maximum length
                if (name.length > 100) {
                    showPopupFieldError('popup_name', 'Name must not exceed 100 characters');
                    if (popupFormMessage) {
                        popupFormMessage.className = 'form-message error';
                        popupFormMessage.textContent = 'Name must not exceed 100 characters.';
                        popupFormMessage.style.display = 'block';
                    }
                    return;
                }
            }
            
            // Prepare form data (only Step 1 fields)
            const formData = {
                name: document.getElementById('popup_name').value,
                email: document.getElementById('popup_email').value,
                phone: phone,
                ic_number: icField ? icField.value : ''
            };
            
            // Get Meta tracking data
            const urlParams = new URLSearchParams(window.location.search);
            const fbclid = urlParams.get('fbclid');
            
            // Helper function to get cookie (if not in scope)
            function getCookieValue(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
                return null;
            }
            
            const fbp = getCookieValue('_fbp');
            if (fbclid) formData.meta_click_id = fbclid;
            if (fbp) formData.meta_profile_id = fbp;
            
            // Detect traffic source and pass ad tracking params (URL + session for multi-step)
            const _src = (typeof window.resolveAdSourceForPixels === 'function')
                ? window.resolveAdSourceForPixels()
                : ((typeof window.detectAdSource === 'function') ? window.detectAdSource(urlParams) : (sessionStorage.getItem('source_from') || 'uk'));
            formData.source_from = _src;
            const _adscmp = urlParams.get('adscmp') || sessionStorage.getItem('adscmp') || '';
            const _adst   = urlParams.get('adst')   || sessionStorage.getItem('adst')   || '';
            const _adnm   = urlParams.get('adnm')   || sessionStorage.getItem('adnm')   || '';
            if (_adscmp) formData.adscmp = _adscmp;
            if (_adst)   formData.adst   = _adst;
            if (_adnm)   formData.adnm   = _adnm;
            
            // Show loading state
            const submitBtn = popupForm.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.textContent : 'Submit';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Saving...';
            }
            
            if (popupFormMessage) {
                popupFormMessage.style.display = 'none';
            }
            
            // Get base URL
            const baseUrl = window.baseUrl || (window.location.origin + window.location.pathname.replace('index.php', ''));
            const submitUrl = baseUrl + 'index.php/submit-popup-inquiry';
            
            // Submit to server
            fetch(submitUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(formData).toString()
            })
            .then(async response => {
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // If not JSON, get text and try to parse, or return error
                    const text = await response.text();
                    console.error('Non-JSON response from server:', text);
                    throw new Error('Server returned invalid response. Please check database connection.');
                }
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    if (popupFormMessage) {
                        popupFormMessage.className = 'form-message success';
                        popupFormMessage.textContent = data.message || 'Thank you! Your information has been saved.';
                        popupFormMessage.style.display = 'block';
                    }
                    
                    // Track Facebook Meta Pixel Lead event for popup submission
                    try {
                        if (typeof fbq !== 'undefined') {
                            const fbEventId = 'lead_' + (data.inquiry_id || Date.now()) + '_' + Date.now();
                            const fbEventTime = Math.floor(Date.now() / 1000);
                            const fbPixelId = '1342586990777024';
                            
                            // Build user data for advanced matching
                            var _fbLeadUserData = {};
                            try { var _eid = localStorage.getItem('_fb_eid'); if (_eid) _fbLeadUserData['external_id'] = _eid; } catch(e) {}
                            try { if (window._fb_login_id) _fbLeadUserData['fb_login_id'] = window._fb_login_id; } catch(e) {}

                            // Track Lead event with custom event ID
                            fbq('track', 'Lead', Object.assign({
                                content_name: 'Loan Application - Initial Inquiry',
                                content_category: 'Loan Inquiry'
                            }, _fbLeadUserData), {
                                eventID: fbEventId
                            });
                            
                            // Log event to database if inquiry_id is available
                            if (data.inquiry_id) {
                                const fbEventResponse = JSON.stringify({
                                    event_name: 'Lead',
                                    event_id: fbEventId,
                                    event_time: fbEventTime,
                                    pixel_id: fbPixelId,
                                    status: 'tracked',
                                    timestamp: new Date().toISOString(),
                                    inquiry_id: data.inquiry_id
                                });
                                
                                // Get base URL for API call
                                const baseUrl = window.baseUrl || (window.location.origin + window.location.pathname.replace('index.php', ''));
                                fetch(baseUrl + 'index.php/api/log-fb-event', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        inquiry_id: data.inquiry_id,
                                        fb_event_id: fbEventId,
                                        fb_event_name: 'Lead',
                                        fb_event_time: fbEventTime,
                                        fb_pixel_id: fbPixelId,
                                        fb_event_response: fbEventResponse
                                    })
                                }).catch(err => {
                                    console.debug('Failed to log Facebook Lead event:', err);
                                });
                            }
                        }
                    } catch(e) {
                        // Silently fail if Facebook Pixel is blocked
                        console.debug('Facebook Pixel Lead event failed:', e);
                    }

                    // TikTok Pixel — Lead (popup FIHW), only for TikTok traffic
                    if (_src === 'tt' && typeof ttq !== 'undefined') {
                        try {
                            ttq.track('Lead', {
                                content_name: 'Loan Application - Initial Inquiry',
                                content_type: 'loan'
                            });
                        } catch (ttErr) {
                            console.debug('TikTok Pixel Lead failed:', ttErr);
                        }
                    }
                    
                    // Copy data to main form
                    const fieldMappings = [
                        ['popup_name', 'name'],
                        ['popup_email', 'email'],
                        ['popup_phone', 'phone'],
                        ['popup_ic_number', 'ic_number']
                    ];
                    
                    fieldMappings.forEach(([popupId, mainId]) => {
                        const popupField = document.getElementById(popupId);
                        const mainField = document.getElementById(mainId);
                        if (popupField && mainField && popupField.value) {
                            mainField.value = popupField.value;
                        }
                    });
                    
                    // Close modal first, then scroll to the main form once the modal has fully hidden
                    setTimeout(() => {
                        closeModal();
                        // Wait for the modal hide transition to finish before scrolling
                        setTimeout(() => {
                            const formEl = document.getElementById('loanApplicationForm') || document.getElementById('apply');
                            if (formEl) {
                                const navbarH = (document.querySelector('.navbar') || document.querySelector('nav') || { offsetHeight: 70 }).offsetHeight;
                                const top = formEl.getBoundingClientRect().top + window.pageYOffset - navbarH - 16;
                                window.scrollTo({ top: top, behavior: 'smooth' });
                            } else {
                                window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
                            }
                        }, 350);
                    }, 1500);
                } else {
                    // Show error message
                    if (popupFormMessage) {
                        popupFormMessage.className = 'form-message error';
                        popupFormMessage.textContent = data.message || 'Failed to save your information. Please try again.';
                        popupFormMessage.style.display = 'block';
                    }
                    
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }
                }
            })
            .catch(error => {
                console.error('Popup form submission error:', error);
                if (popupFormMessage) {
                    popupFormMessage.className = 'form-message error';
                    
                    // More specific error messages
                    if (error.message.includes('invalid response') || error.message.includes('database')) {
                        popupFormMessage.innerHTML = '<strong>Database Connection Error:</strong><br>' +
                            '1. Check if MySQL is running in XAMPP<br>' +
                            '2. Check if database connection is configured correctly<br>' +
                            '3. Please try again or contact support';
                    } else if (error.message.includes('Network') || error.message.includes('fetch')) {
                        popupFormMessage.textContent = 'Network error: Cannot reach server. Check if Apache is running in XAMPP.';
                    } else {
                        popupFormMessage.textContent = error.message || 'An error occurred. Please try again.';
                    }
                    popupFormMessage.style.display = 'block';
                }
                
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            });
        });
    }
    
    // Initialize popup form visibility on page load
    function ensurePopupFormVisible() {
        const popupForm = document.getElementById('popupLoanApplicationForm');
        if (popupForm) {
            const step1 = popupForm.querySelector('.form-step[data-step="1"]');
            if (step1) {
                // Ensure step 1 is visible and all fields are shown
                step1.classList.add('active');
                step1.style.display = 'block';
                step1.style.opacity = '1';
                // Ensure all form elements are visible
                step1.querySelectorAll('input, select, textarea, label, .form-group, .form-row').forEach(element => {
                    element.style.display = '';
                    element.style.visibility = 'visible';
                    element.style.opacity = '1';
                });
            }
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            ensurePopupFormVisible(); // Ensure form is visible first
            showModalIfNeeded();
            syncAllFields();
            syncFileUpload();
            initPopupFormSteps();
        });
    } else {
        ensurePopupFormVisible(); // Ensure form is visible first
        showModalIfNeeded();
        syncAllFields();
        syncFileUpload();
        initPopupFormSteps();
    }
    
    // Close modal handlers
    const closeModalBtn = document.getElementById('closeModal');
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }
    
    const modalOverlay = document.getElementById('inquiryModal');
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                closeModal();
            }
        });
    }
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('inquiryModal');
            if (modal && modal.style.display !== 'none') {
                closeModal();
            }
        }
    });
})();

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
            const href = this.getAttribute('href');
            
            // Skip if href is just "#" or empty
            if (!href || href === '#' || href.length <= 1) {
                e.preventDefault(); // Prevent default but don't scroll
                return;
            }
            
            e.preventDefault();
            
            try {
                const target = document.querySelector(href);
                if (target) {
                    // Calculate offset for fixed navbar (70px height)
                    const navbarHeight = 70;
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            } catch (err) {
                // Invalid selector, ignore
                console.warn('Invalid selector:', href);
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
        if (!loanAmount || !loanTenure || !interestRate || !monthlyPayment) return;
        
        const principal = parseFloat(loanAmount.value);
        const years = parseFloat(loanTenure.value);
        const rate = parseFloat(interestRate.value) / 100 / 12;
        const numberOfPayments = years * 12;
        
        // Monthly payment formula: M = P[r(1+r)^n]/[(1+r)^n-1]
        let monthlyPaymentAmount;
        if (rate > 0) {
            monthlyPaymentAmount = principal * (rate * Math.pow(1 + rate, numberOfPayments)) / (Math.pow(1 + rate, numberOfPayments) - 1);
        } else {
            monthlyPaymentAmount = principal / numberOfPayments;
        }
        
        if (isFinite(monthlyPaymentAmount) && monthlyPaymentAmount > 0) {
            const amount = Math.round(monthlyPaymentAmount);
            // Smooth number transition
            monthlyPayment.style.opacity = '0.6';
            monthlyPayment.style.transform = 'translateY(-5px)';
            setTimeout(() => {
                monthlyPayment.textContent = formatCurrency(amount);
                monthlyPayment.style.opacity = '1';
                monthlyPayment.style.transform = 'translateY(0)';
            }, 150);
        }
    }
    
    if (loanAmount) {
        loanAmount.addEventListener('input', function() {
            loanAmountValue.textContent = formatCurrency(this.value);
            calculateMonthlyPayment();
        });
    }
    
    if (loanTenure && loanTenureValue) {
        loanTenure.addEventListener('input', function() {
            const yearsText = this.value + ' ' + (this.value == 1 ? 'year' : 'years');
            loanTenureValue.textContent = yearsText;
            calculateMonthlyPayment();
        });
    }
    
    if (interestRate && interestRateValue) {
        interestRate.addEventListener('input', function() {
            interestRateValue.textContent = parseFloat(this.value).toFixed(1) + '%';
            calculateMonthlyPayment();
        });
    }
    
    // Initialize calculator
    if (loanAmount && loanTenure && interestRate) {
        calculateMonthlyPayment();
    }
    
    // Note: Malaysian Validation Functions are now defined in global scope above
    
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
    
    // Multi-Step Form Navigation
    let currentStep = 1;
    const totalSteps = 4;
    
    // Map field names to their respective steps
    const fieldToStepMap = {
        // Step 1 fields
        'name': 1,
        'email': 1,
        'phone': 1,
        'ic_number': 1,
        // Step 2 fields
        'loan_type': 2,
        'loan_amount': 2,
        'income': 2,
        'message': 2,
        // Step 3 fields
        'company_name': 3,
        'job_position': 3,
        'employment_type': 3,
        'years_employed': 3,
        'company_address': 3,
        'company_phone': 3,
        // Step 4 fields
        'proof_of_income': 4
    };
    
    /**
     * Determine which step has the error based on error message or field names
     * @param {string} errorMessage - The error message from server
     * @returns {number} - Step number (1-4) or null if cannot determine
     */
    function getErrorStep(errorMessage) {
        if (!errorMessage) return null;
        
        const lowerMessage = errorMessage.toLowerCase();
        
        // Check for field names in error message
        for (const [field, step] of Object.entries(fieldToStepMap)) {
            if (lowerMessage.includes(field.toLowerCase()) || 
                lowerMessage.includes(field.replace('_', ' '))) {
                return step;
            }
        }
        
        // Check for common error keywords that might indicate a step
        if (lowerMessage.includes('name') || 
            lowerMessage.includes('email') || 
            lowerMessage.includes('phone') || 
            lowerMessage.includes('ic') ||
            lowerMessage.includes('identity')) {
            return 1;
        }
        
        if (lowerMessage.includes('loan') || 
            lowerMessage.includes('amount') || 
            lowerMessage.includes('income')) {
            return 2;
        }
        
        if (lowerMessage.includes('company') || 
            lowerMessage.includes('job') || 
            lowerMessage.includes('employment') ||
            lowerMessage.includes('work')) {
            return 3;
        }
        
        if (lowerMessage.includes('proof') || 
            lowerMessage.includes('file') || 
            lowerMessage.includes('upload') ||
            lowerMessage.includes('document')) {
            return 4;
        }
        
        return null;
    }
    
    // Initialize step indicator on page load
    function initStepIndicator() {
        updateStepIndicator(1);
    }
    
    // Call initialization when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initStepIndicator);
    } else {
        initStepIndicator();
    }
    
    function updateStepIndicator(step) {
        // Only target step cards within the form's step-indicator (not "How It Works" section)
        const stepIndicator = document.querySelector('.form-steps .step-indicator');
        if (!stepIndicator) {
            console.warn('Step indicator not found');
            return;
        }
        
        const stepCards = stepIndicator.querySelectorAll('.step-card');
        stepCards.forEach((stepEl) => {
            const stepNum = parseInt(stepEl.getAttribute('data-step'));
            if (isNaN(stepNum)) return;
            
            stepEl.classList.remove('active', 'completed');
            if (stepNum < step) {
                stepEl.classList.add('completed');
            } else if (stepNum === step) {
                stepEl.classList.add('active');
            }
        });
    }
    
    function showStep(step) {
        // Hide all steps and remove required attributes from ALL fields
        document.querySelectorAll('.form-step').forEach(stepEl => {
            stepEl.classList.remove('active');
        });
        
        // Remove required from ALL fields to prevent browser validation on hidden fields
        document.querySelectorAll('[data-required="true"]').forEach(field => {
            field.removeAttribute('required');
        });
        
        // Show current step (but don't add required attribute - we use custom validation only)
        const currentStepEl = document.querySelector(`.form-step[data-step="${step}"]`);
        if (currentStepEl) {
            currentStepEl.classList.add('active');
            currentStep = step;
            updateStepIndicator(step);
            
            // DO NOT add required attribute - we use custom validation only
            // This prevents browser from trying to validate hidden fields
            
            // Scroll to top of form
            currentStepEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            // Save current step to localStorage
            saveFormData();
        }
    }
    
    function validateStep(step) {
        const stepEl = document.querySelector(`.form-step[data-step="${step}"]`);
        if (!stepEl) return true;
        
        // Clear previous errors in this step
        stepEl.querySelectorAll('.field-error').forEach(err => err.remove());
        
        // Validate fields with data-required attribute OR required attribute
        const requiredFields = stepEl.querySelectorAll('[data-required="true"], [required]');
        let isValid = true;
        let firstInvalidField = null;
        
        requiredFields.forEach(field => {
            let value;
            let fieldError = '';
            
            if (field.type === 'file') {
                value = field.files.length;
                if (!value) {
                    fieldError = 'Please upload a file';
                }
            } else if (field.type === 'number') {
                // For number fields, check if value exists and is valid
                value = field.value.trim();
                if (!value) {
                    fieldError = 'This field is required';
                } else {
                    const numValue = parseFloat(value);
                    if (isNaN(numValue)) {
                        fieldError = 'Please enter a valid number';
                        value = '';
                    } else {
                        // Check min attribute if exists
                        if (field.hasAttribute('min')) {
                            const min = parseFloat(field.getAttribute('min'));
                            if (numValue < min) {
                                fieldError = 'Minimum value is ' + min;
                                value = '';
                            }
                        }
                        // Check max attribute if exists
                        if (field.hasAttribute('max') && !fieldError) {
                            const max = parseFloat(field.getAttribute('max'));
                            if (numValue > max) {
                                fieldError = 'Maximum value is ' + max;
                                value = '';
                            }
                        }
                    }
                }
            } else if (field.tagName === 'SELECT') {
                value = field.value;
                if (!value) {
                    fieldError = 'Please select an option';
                }
            } else if (field.type === 'email') {
                value = field.value.trim();
                if (!value) {
                    fieldError = 'This field is required';
                } else {
                    // Validate email format
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        fieldError = 'Please enter a valid email address';
                        value = '';
                    }
                }
            } else if (field.type === 'tel') {
                value = field.value.trim();
                if (!value) {
                    fieldError = 'This field is required';
                } else {
                    // Basic phone validation (at least 9 digits)
                    const phoneDigits = value.replace(/[^\d]/g, '');
                    if (phoneDigits.length < 9) {
                        fieldError = 'Please enter a valid phone number';
                        value = '';
                    }
                }
            } else if (field.id === 'ic_number') {
                value = field.value.trim();
                if (!value) {
                    fieldError = 'This field is required';
                } else {
                    // Validate IC format (should have 12 digits, optionally with dashes)
                    const icDigits = value.replace(/[^\d]/g, '');
                    if (icDigits.length !== 12) {
                        fieldError = 'Please enter a valid IC number (12 digits)';
                        value = '';
                    }
                }
            } else {
                value = field.value.trim();
                if (!value) {
                    fieldError = 'This field is required';
                }
            }
            
            if (!value || fieldError) {
                isValid = false;
                field.style.borderColor = '#ef4444';
                const fieldName = field.labels && field.labels[0] ? field.labels[0].textContent.replace('*', '').trim() : 'This field';
                showFieldError(field.id, fieldError || (fieldName + ' is required'));
                
                // Store first invalid field for scrolling
                if (!firstInvalidField) {
                    firstInvalidField = field;
                }
            } else {
                field.style.borderColor = '#e2e8f0';
                clearFieldError(field.id);
            }
        });
        
        // Scroll to first invalid field if any
        if (firstInvalidField) {
            firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => {
                firstInvalidField.focus();
            }, 300);
        }
        
        return isValid;
    }
    
    function validateAllSteps() {
        let isValid = true;
        for (let i = 1; i <= totalSteps; i++) {
            if (!validateStep(i)) {
                isValid = false;
                // Show the first invalid step
                if (isValid === false && currentStep !== i) {
                    showStep(i);
                }
                break;
            }
        }
        return isValid;
    }
    
    // Step navigation buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-next') || e.target.closest('.btn-next')) {
            const btn = e.target.classList.contains('btn-next') ? e.target : e.target.closest('.btn-next');
            const nextStep = parseInt(btn.getAttribute('data-next'));
            
            // Validate current step before moving to next
            if (validateStep(currentStep)) {
                // Mark current step as completed
                const currentStepEl = document.querySelector(`.step[data-step="${currentStep}"]`);
                if (currentStepEl) {
                    currentStepEl.classList.add('completed');
                }
                
                // Track form step progression (FIHW - Filled In Half Way)
                trackFormStep();
                
                // Track form step progression (FIHW - Filled In Half Way)
                trackFormStep();
                
                showStep(nextStep);
            } else {
                // Show error message if validation fails
                const formMessage = document.getElementById('formMessage');
                if (formMessage) {
                    formMessage.className = 'form-message error';
                    formMessage.textContent = 'Please complete all required fields before proceeding.';
                    formMessage.style.display = 'block';
                    setTimeout(() => {
                        formMessage.style.display = 'none';
                    }, 3000);
                }
            }
        }
        
        if (e.target.classList.contains('btn-prev') || e.target.closest('.btn-prev')) {
            const btn = e.target.classList.contains('btn-prev') ? e.target : e.target.closest('.btn-prev');
            const prevStep = parseInt(btn.getAttribute('data-prev'));
            // No validation needed when going back
            showStep(prevStep);
        }
    });
    
    // File Upload - Convert to Base64
    const proofOfIncomeInput = document.getElementById('proof_of_income');
    const filePreview = document.getElementById('filePreview');
    const previewImage = document.getElementById('previewImage');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const removeFileBtn = document.getElementById('removeFile');
    let base64Image = null;
    let imageType = null;
    let imageSize = 0;
    
    if (proofOfIncomeInput) {
        proofOfIncomeInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Please upload an image file (JPG, PNG, etc.)');
                this.value = '';
                return;
            }
            
            // Validate file size (max 2MB)
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes
            if (file.size > maxSize) {
                alert('File size exceeds 2MB. Please upload a smaller image.');
                this.value = '';
                return;
            }
            
            imageSize = file.size;
            imageType = file.type;
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                base64Image = e.target.result;
                previewImage.src = base64Image;
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                filePreview.style.display = 'block';
            };
            reader.readError = function() {
                alert('Error reading file. Please try again.');
                proofOfIncomeInput.value = '';
            };
            reader.readAsDataURL(file);
        });
    }
    
    if (removeFileBtn) {
        removeFileBtn.addEventListener('click', function() {
            proofOfIncomeInput.value = '';
            base64Image = null;
            imageType = null;
            imageSize = 0;
            filePreview.style.display = 'none';
        });
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
    
    // Form Submission
    const loanApplicationForm = document.getElementById('loanApplicationForm');
    const formMessage = document.getElementById('formMessage');
    
    // Form Data Persistence using localStorage
    const FORM_STORAGE_KEY = 'loanApplicationFormData';
    const FORM_DATA_EXPIRY_HOURS = 24; // Data persists for 24 hours (whole day)
    
    // Function to save form data to localStorage
    function saveFormData() {
        const form = document.getElementById('loanApplicationForm');
        if (!form) return;
        
        const formData = {};
        const formFields = form.querySelectorAll('input, select, textarea');
        
        formFields.forEach(field => {
            // Skip file inputs (can't be stored in localStorage)
            if (field.type === 'file') return;
            
            const fieldName = field.name || field.id;
            if (fieldName) {
                if (field.type === 'checkbox') {
                    formData[fieldName] = field.checked;
                } else {
                    formData[fieldName] = field.value;
                }
            }
        });
        
        // Save current step
        formData._currentStep = currentStep;
        
        // Add timestamp for expiration check (24 hours from now)
        formData._timestamp = Date.now();
        formData._expiresAt = Date.now() + (FORM_DATA_EXPIRY_HOURS * 60 * 60 * 1000);
        
        try {
            localStorage.setItem(FORM_STORAGE_KEY, JSON.stringify(formData));
        } catch (e) {
            console.warn('Could not save form data to localStorage:', e);
        }
    }
    
    // Function to restore form data from localStorage
    function restoreFormData() {
        const form = document.getElementById('loanApplicationForm');
        if (!form) return;
        
        try {
            const savedData = localStorage.getItem(FORM_STORAGE_KEY);
            if (!savedData) return;
            
            const formData = JSON.parse(savedData);
            
            // Check if data has expired (older than 24 hours)
            const now = Date.now();
            if (formData._expiresAt && now > formData._expiresAt) {
                // Data expired, clear it
                clearFormData();
                return;
            }
            
            // Restore form fields
            Object.keys(formData).forEach(key => {
                // Skip internal keys
                if (key.startsWith('_')) return;
                
                const field = form.querySelector(`[name="${key}"], #${key}`);
                if (field && field.type !== 'file') {
                    if (field.type === 'checkbox') {
                        field.checked = formData[key] === true;
                    } else {
                        field.value = formData[key] || '';
                    }
                }
            });
            
            // Restore current step if valid
            if (formData._currentStep && formData._currentStep >= 1 && formData._currentStep <= totalSteps) {
                showStep(formData._currentStep);
            }
        } catch (e) {
            console.warn('Could not restore form data from localStorage:', e);
        }
    }
    
    // Function to clear saved form data
    function clearFormData() {
        try {
            localStorage.removeItem(FORM_STORAGE_KEY);
        } catch (e) {
            console.warn('Could not clear form data from localStorage:', e);
        }
    }
    
    if (loanApplicationForm) {
        // Restore form data on page load
        restoreFormData();
        
        // Save form data on input/change events
        const formFields = loanApplicationForm.querySelectorAll('input, select, textarea');
        formFields.forEach(field => {
            if (field.type === 'file') {
                // For file inputs, save on change (note: file data can't be stored)
                field.addEventListener('change', saveFormData);
            } else {
                // For other inputs, save on input (real-time) and change
                field.addEventListener('input', saveFormData);
                field.addEventListener('change', saveFormData);
            }
        });
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
            
            // IMPORTANT: Remove required attribute from ALL fields FIRST
            // This prevents browser HTML5 validation from trying to focus hidden fields
            document.querySelectorAll('[data-required="true"]').forEach(field => {
                field.removeAttribute('required');
            });
            
            // Also remove novalidate to ensure no HTML5 validation
            if (!loanApplicationForm.hasAttribute('novalidate')) {
                loanApplicationForm.setAttribute('novalidate', 'novalidate');
            }
            
            // Clear all previous errors
            document.querySelectorAll('.field-error').forEach(err => err.remove());
            document.querySelectorAll('input, select, textarea').forEach(field => {
                field.style.borderColor = '#e2e8f0';
            });
            
            // Validate current step first (in case user skipped validation)
            if (!validateStep(currentStep)) {
                formMessage.className = 'form-message error';
                formMessage.textContent = 'Please complete all required fields in the current step before submitting.';
                formMessage.style.display = 'block';
                formMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
            
            // Validate all steps before submission (using our custom validation)
            if (!validateAllSteps()) {
                formMessage.className = 'form-message error';
                formMessage.textContent = 'Please complete all required fields in all steps.';
                formMessage.style.display = 'block';
                formMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
            
            // Validate Malaysian phone number
            const phone = document.getElementById('phone').value;
            if (!validateMalaysianPhone(phone)) {
                showFieldError('phone', 'Please enter a valid Malaysian phone number (e.g., 012-345-6789 or +6012-345-6789)');
                showStep(1);
                return;
            }
            
            // Validate IC if field exists
            const icField = document.getElementById('ic_number');
            if (icField && icField.value) {
                if (!validateMalaysianIC(icField.value)) {
                    showFieldError('ic_number', 'Please enter a valid Malaysian IC number (e.g., 901231-01-5678)');
                    showStep(1);
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
            const loanAmountField = document.getElementById('loan_amount');
            const loanAmount = loanAmountField ? parseFloat(loanAmountField.value) : 0;
            if (!loanAmount || loanAmount < 1000) {
                showFieldError('loan_amount', 'Minimum loan amount is RM 1,000');
                showStep(2);
                return;
            }
            
            // Validate monthly income
            const incomeField = document.getElementById('income');
            if (!incomeField || !incomeField.value) {
                showFieldError('income', 'Monthly income is required');
                showStep(2);
                return;
            }
            
            const income = parseFloat(incomeField.value);
            if (!income || income < 1000) {
                showFieldError('income', 'Please enter a valid monthly income (minimum RM 1,000)');
                showStep(2);
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
            
            // Validate final step (proof of income)
            if (!base64Image) {
                showFieldError('proof_of_income', 'Please upload proof of income (latest or last month)');
                showStep(4);
                return;
            }
            
            const formData = new FormData(this);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            
            // Add base64 image data (check for popup image first, then main form image)
            const finalBase64Image = (typeof window !== 'undefined' && window.popupBase64Image) ? window.popupBase64Image : base64Image;
            const finalImageType = (typeof window !== 'undefined' && window.popupImageType) ? window.popupImageType : imageType;
            const finalImageSize = (typeof window !== 'undefined' && window.popupImageSize) ? window.popupImageSize : imageSize;
            
            if (finalBase64Image) {
                data['proof_of_income_base64'] = finalBase64Image;
                data['proof_of_income_type'] = finalImageType;
                data['proof_of_income_size'] = finalImageSize;
                
                // Clear popup image variables after use
                if (typeof window !== 'undefined') {
                    window.popupBase64Image = null;
                    window.popupImageType = null;
                    window.popupImageSize = null;
                }
            }
            
            // Add Meta (Facebook) tracking data for Conversions API
            // Get fbclid from URL parameter (Facebook Click ID)
            const urlParams = new URLSearchParams(window.location.search);
            const fbclid = urlParams.get('fbclid');
            if (fbclid) {
                data['meta_click_id'] = fbclid;
            }
            
            // Get _fbp cookie value (Facebook Browser ID / Profile ID)
            function getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
                return null;
            }
            const fbp = getCookie('_fbp');
            if (fbp) {
                data['meta_profile_id'] = fbp;
            }
            
            // Detect traffic source and pass ad tracking params (URL + session for multi-step)
            data['source_from'] = (typeof window.resolveAdSourceForPixels === 'function')
                ? window.resolveAdSourceForPixels()
                : ((typeof window.detectAdSource === 'function') ? window.detectAdSource(urlParams) : (sessionStorage.getItem('source_from') || 'uk'));
            const _cmpFull = urlParams.get('adscmp') || sessionStorage.getItem('adscmp') || '';
            const _adstFull = urlParams.get('adst')  || sessionStorage.getItem('adst')  || '';
            const _adnmFull = urlParams.get('adnm')  || sessionStorage.getItem('adnm')  || '';
            if (_cmpFull)  data['adscmp'] = _cmpFull;
            if (_adstFull) data['adst']   = _adstFull;
            if (_adnmFull) data['adnm']   = _adnmFull;
            
            // Add Facebook Pixel event data for logging (generate event ID before submission)
            // This will be updated after successful submission with actual event response
            if (typeof fbq !== 'undefined') {
                const fbEventId = 'complete_registration_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                const fbEventTime = Math.floor(Date.now() / 1000);
                const fbPixelId = '1342586990777024';
                
                data['fb_event_id'] = fbEventId;
                data['fb_event_name'] = 'CompleteRegistration';
                data['fb_event_time'] = fbEventTime;
                data['fb_pixel_id'] = fbPixelId;
                // fb_event_response will be added after event fires
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Submitting...';
            submitBtn.disabled = true;
            
            // Get the base URL dynamically
            const baseUrl = window.baseUrl || (window.location.origin + window.location.pathname.replace('index.php', ''));
            const submitUrl = baseUrl + 'index.php/submit-inquiry';
            
            // Send data to server
            fetch(submitUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.text(); // Get as text first
            })
            .then(text => {
                
                // Check if response is empty
                if (!text || text.trim().length === 0) {
                    throw new Error('Empty response from server. Please check if MySQL is running and database is connected.');
                }
                
                try {
                    const result = JSON.parse(text);
                    
                    if (result.success) {
                        formMessage.className = 'form-message success';
                        formMessage.textContent = result.message;
                        formMessage.style.display = 'block';
                        
                        // Track Facebook Meta Pixel CompleteRegistration event and log response
                        try {
                            if (typeof fbq !== 'undefined') {
                                // Use the event ID that was sent with form submission or generate new one
                                const fbEventId = 'complete_registration_' + result.inquiry_id + '_' + Date.now();
                                const fbEventTime = Math.floor(Date.now() / 1000); // Unix timestamp
                                const fbPixelId = '1342586990777024'; // Facebook Pixel ID
                                
                                // Build user data for advanced matching
                                var _fbRegUserData = {};
                                try { var _eid = localStorage.getItem('_fb_eid'); if (_eid) _fbRegUserData['external_id'] = _eid; } catch(e) {}
                                try { if (window._fb_login_id) _fbRegUserData['fb_login_id'] = window._fb_login_id; } catch(e) {}

                                // Track CompleteRegistration event with custom event ID
                                fbq('track', 'CompleteRegistration', Object.assign({
                                    content_name: 'Loan Application',
                                    content_category: 'Loan Inquiry'
                                }, _fbRegUserData), {
                                    eventID: fbEventId
                                });
                                
                                // Prepare event response data for logging
                                const fbEventResponse = JSON.stringify({
                                    event_name: 'CompleteRegistration',
                                    event_id: fbEventId,
                                    event_time: fbEventTime,
                                    pixel_id: fbPixelId,
                                    status: 'tracked',
                                    timestamp: new Date().toISOString(),
                                    inquiry_id: result.inquiry_id
                                });
                                
                                // Log Facebook event data to database
                                if (result.inquiry_id) {
                                    const logUrl = baseUrl + 'index.php/api/log-fb-event';
                                    fetch(logUrl, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                        },
                                        body: JSON.stringify({
                                            inquiry_id: result.inquiry_id,
                                            fb_event_id: fbEventId,
                                            fb_event_name: 'CompleteRegistration',
                                            fb_event_time: fbEventTime,
                                            fb_pixel_id: fbPixelId,
                                            fb_event_response: fbEventResponse
                                        })
                                    }).catch(err => {
                                        console.debug('Failed to log Facebook event:', err);
                                    });
                                }
                            }
                        } catch(e) {
                            // Silently fail if Facebook Pixel is blocked
                            console.debug('Facebook Pixel CompleteRegistration event failed:', e);
                        }

                        // TikTok Pixel — CompleteRegistration (full application), only for TikTok traffic
                        try {
                            const ttSrc = (data['source_from'] || 'uk');
                            if (ttSrc === 'tt' && typeof ttq !== 'undefined') {
                                ttq.track('CompleteRegistration', {
                                    content_name: 'Loan Application',
                                    content_category: 'Loan Inquiry'
                                });
                            }
                        } catch (ttErr) {
                            console.debug('TikTok Pixel CompleteRegistration failed:', ttErr);
                        }
                        
                        // Clear saved form data on successful submission
                        clearFormData();
                        
                        loanApplicationForm.reset();
                        
                        // Reset to step 1
                        showStep(1);
                        
                        // Scroll to success message
                        formMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else {
                        formMessage.className = 'form-message error';
                        formMessage.textContent = result.message;
                        formMessage.style.display = 'block';
                        
                        // Jump to the step that has the error
                        const errorStep = getErrorStep(result.message);
                        if (errorStep && errorStep !== currentStep) {
                            showStep(errorStep);
                        } else if (currentStep === 4 && !errorStep) {
                            // If at step 4 and can't determine error step, go to step 1
                            showStep(1);
                        }
                        
                        // Scroll to error message
                        formMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                } catch (e) {
                    formMessage.className = 'form-message error';
                    
                    // More helpful error message
                    let errorMsg = '';
                    if (text.includes('Fatal error')) {
                        errorMsg = 'Server Error: PHP Fatal Error. Check error logs or test-form-submit.php for details.';
                    } else if (text.includes('Warning')) {
                        errorMsg = 'Server Warning: ' + text.substring(0, 200);
                    } else if (text.includes('<!DOCTYPE') || text.includes('<html')) {
                        errorMsg = 'Server returned HTML instead of JSON. The page may have redirected or encountered an error.';
                    } else {
                        errorMsg = 'Invalid server response. Response: ' + text.substring(0, 150);
                    }
                    
                    formMessage.textContent = errorMsg;
                    formMessage.style.display = 'block';
                    
                    // Jump to the step that has the error (if we can determine it)
                    const errorStep = getErrorStep(errorMsg);
                    if (errorStep && errorStep !== currentStep) {
                        showStep(errorStep);
                    } else if (currentStep === 4 && !errorStep) {
                        // If at step 4 and can't determine error step, go to step 1
                        showStep(1);
                    }
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
                
                // Jump to the step that has the error (if we can determine it)
                const errorStep = getErrorStep(formMessage.textContent);
                if (errorStep && errorStep !== currentStep) {
                    showStep(errorStep);
                } else if (currentStep === 4 && !errorStep) {
                    // If at step 4 and can't determine error step, go to step 1
                    showStep(1);
                }
                
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
    
    // Sequential animation for How It Works steps
    const howItWorksSection = document.querySelector('.how-it-works');
    if (howItWorksSection) {
        const stepsObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const stepCards = entry.target.querySelectorAll('.step-card[data-step]');
                    
                    stepCards.forEach((card, index) => {
                        setTimeout(() => {
                            card.classList.add('animate');
                        }, index * 200); // 200ms delay between each step
                    });
                    
                    stepsObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.2,
            rootMargin: '0px 0px -50px 0px'
        });
        
        stepsObserver.observe(howItWorksSection);
    }
    
    // =====================================================
    // Language Switcher
    // =====================================================
    
    const langBtn = document.getElementById('langBtn');
    const langDropdown = document.getElementById('langDropdown');
    const langBtnMobile = document.getElementById('langBtnMobile');
    const langDropdownMobile = document.getElementById('langDropdownMobile');
    const langOptions = document.querySelectorAll('.lang-option');
    
    // Function to sync language text between desktop and mobile
    function syncLanguageText(lang) {
        const langText = lang.toUpperCase();
        const currentLangText = document.getElementById('currentLangText');
        const currentLangTextMobile = document.getElementById('currentLangTextMobile');
        if (currentLangText) currentLangText.textContent = langText;
        if (currentLangTextMobile) currentLangTextMobile.textContent = langText;
    }
    
    // Toggle language dropdown (Desktop)
    if (langBtn && langDropdown) {
        langBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            langDropdown.classList.toggle('active');
            langBtn.classList.toggle('active');
            // Close mobile dropdown if open
            if (langDropdownMobile) {
                langDropdownMobile.classList.remove('active');
            }
            if (langBtnMobile) {
                langBtnMobile.classList.remove('active');
            }
        });
    }
    
    // Toggle language dropdown (Mobile)
    if (langBtnMobile && langDropdownMobile) {
        langBtnMobile.addEventListener('click', function(e) {
            e.stopPropagation();
            langDropdownMobile.classList.toggle('active');
            langBtnMobile.classList.toggle('active');
            // Close desktop dropdown if open
            if (langDropdown) {
                langDropdown.classList.remove('active');
            }
            if (langBtn) {
                langBtn.classList.remove('active');
            }
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const isDesktopClick = langBtn && (langBtn.contains(e.target) || langDropdown && langDropdown.contains(e.target));
        const isMobileClick = langBtnMobile && (langBtnMobile.contains(e.target) || langDropdownMobile && langDropdownMobile.contains(e.target));
        
        if (!isDesktopClick && langDropdown && langBtn) {
            langDropdown.classList.remove('active');
            langBtn.classList.remove('active');
        }
        
        if (!isMobileClick && langDropdownMobile && langBtnMobile) {
            langDropdownMobile.classList.remove('active');
            langBtnMobile.classList.remove('active');
        }
    });
    
    // Handle language selection
    langOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            const selectedLang = this.getAttribute('data-lang');
            
            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            
            // Switch language
            switchLanguage(selectedLang);
        });
    });
    
    /**
     * Switch language function
     */
    function switchLanguage(lang) {
        // Use baseUrl from window if available, otherwise construct it
        let baseUrl = window.baseUrl || '';
        
        // If baseUrl is not set, try to construct it
        if (!baseUrl) {
            baseUrl = window.location.protocol + '//' + window.location.host;
            const pathname = window.location.pathname;
            
            // Extract base path
            if (pathname.includes('index.php')) {
                const indexPos = pathname.indexOf('index.php');
                baseUrl += pathname.substring(0, indexPos);
            } else {
                // Get directory path
                const lastSlash = pathname.lastIndexOf('/');
                if (lastSlash > 0) {
                    baseUrl += pathname.substring(0, lastSlash + 1);
                } else {
                    baseUrl += '/';
                }
            }
        }
        
        // Ensure baseUrl ends with /
        if (!baseUrl.endsWith('/')) {
            baseUrl += '/';
        }
        
        // Construct the full URL
        const fullUrl = baseUrl + 'index.php/language/switch';
        
        // Make the request
        fetch(fullUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'lang=' + encodeURIComponent(lang),
            credentials: 'same-origin'
        })
        .then(response => {
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                // Try to read as text for debugging
                return response.text().then(text => {
                    console.error('Non-JSON response:', text);
                    throw new Error('Invalid response format. Expected JSON but got: ' + contentType);
                });
            }
            
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                // Reload page to apply new language
                window.location.reload();
            } else {
                console.error('Language switch failed:', data);
                alert('Failed to switch language: ' + (data && data.message ? data.message : 'Please try again.'));
            }
        })
        .catch(error => {
            console.error('Error switching language:', error);
            console.error('Attempted URL:', fullUrl);
            console.error('Base URL:', baseUrl);
            alert('An error occurred while switching language. Please check the console for details.');
        });
    }
    
    /**
     * Get translation from window.translations
     */
    function lang(key, params = {}) {
        if (typeof window.translations === 'undefined') {
            return key;
        }
        
        let text = window.translations[key] || key;
        
        // Replace parameters
        for (let param in params) {
            text = text.replace('{' + param + '}', params[param]);
        }
        
        return text;
    }
    
    // Make lang function globally available
    window.lang = lang;
    
    // Testimonials Auto-Slide with 3-card view
    const testimonialsSlider = document.getElementById('testimonialsSlider');
    const testimonialCards = testimonialsSlider ? testimonialsSlider.querySelectorAll('.testimonial-card') : [];
    const testimonialsDots = document.querySelectorAll('.testimonials-dots .dot');
    const testimonialsPrev = document.getElementById('testimonialsPrev');
    const testimonialsNext = document.getElementById('testimonialsNext');
    
    if (testimonialCards.length > 0) {
        let currentSlide = 0;
        let autoSlideInterval;
        const slideDuration = 5000; // 5 seconds
        let isAnimating = false;
        
        // Initialize first slide
        updateSlide(0, false);
        
        function updateSlide(index, animate = true) {
            if (isAnimating) return;
            isAnimating = true;
            
            const totalCards = testimonialCards.length;
            
            // Calculate previous and next indices with wrapping
            const prevIndex = (index - 1 + totalCards) % totalCards;
            const nextIndex = (index + 1) % totalCards;
            
            // Remove all classes
            testimonialCards.forEach((card, i) => {
                card.classList.remove('active', 'prev', 'next', 'rotating');
                
                if (i === index) {
                    card.classList.add('active');
                } else if (i === prevIndex) {
                    card.classList.add('prev');
                } else if (i === nextIndex) {
                    card.classList.add('next');
                }
            });
            
            // Update dots
            testimonialsDots.forEach((dot, i) => {
                dot.classList.remove('active');
                if (i === index) {
                    dot.classList.add('active');
                }
            });
            
            // Center the active card
            if (testimonialsSlider && testimonialCards[index]) {
                // Use requestAnimationFrame to ensure layout is ready
                requestAnimationFrame(() => {
                    const wrapper = testimonialsSlider.parentElement;
                    if (!wrapper) return;
                    
                    const wrapperRect = wrapper.getBoundingClientRect();
                    const wrapperWidth = wrapperRect.width;
                    const activeCard = testimonialCards[index];
                    const activeCardWidth = activeCard.offsetWidth;
                    
                    // Calculate the position of the active card relative to the slider start
                    let totalOffset = 0;
                    for (let i = 0; i < index; i++) {
                        const card = testimonialCards[i];
                        const cardWidth = card.offsetWidth;
                        const cardStyle = window.getComputedStyle(card);
                        const cardMarginLeft = parseInt(cardStyle.marginLeft) || 0;
                        const cardMarginRight = parseInt(cardStyle.marginRight) || 0;
                        totalOffset += cardWidth + cardMarginLeft + cardMarginRight;
                    }
                    
                    // Get the margin of the active card
                    const activeCardStyle = window.getComputedStyle(activeCard);
                    const activeCardMarginLeft = parseInt(activeCardStyle.marginLeft) || 0;
                    
                    // Position of the active card's left edge relative to slider start
                    const activeCardLeft = totalOffset + activeCardMarginLeft;
                    
                    // Position of the active card's center relative to slider start
                    const activeCardCenter = activeCardLeft + (activeCardWidth / 2);
                    
                    // Calculate translateX to center the active card in the wrapper
                    // We want the card's center to align with the wrapper's center
                    const wrapperCenter = wrapperWidth / 2;
                    const translateX = wrapperCenter - activeCardCenter;
                    
                    testimonialsSlider.style.transform = `translateX(${translateX}px)`;
                });
            }
            
            currentSlide = index;
            
            // Reset animation flag after transition
            setTimeout(() => {
                isAnimating = false;
            }, 800);
        }
        
        function nextSlide() {
            if (isAnimating) return;
            const next = (currentSlide + 1) % testimonialCards.length;
            updateSlide(next, true);
        }
        
        function prevSlide() {
            if (isAnimating) return;
            const prev = (currentSlide - 1 + testimonialCards.length) % testimonialCards.length;
            updateSlide(prev, true);
        }
        
        // Auto-slide functionality
        function startAutoSlide() {
            autoSlideInterval = setInterval(nextSlide, slideDuration);
        }
        
        function stopAutoSlide() {
            if (autoSlideInterval) {
                clearInterval(autoSlideInterval);
            }
        }
        
        // Start auto-slide
        startAutoSlide();
        
        // Pause on hover
        if (testimonialsSlider) {
            testimonialsSlider.addEventListener('mouseenter', stopAutoSlide);
            testimonialsSlider.addEventListener('mouseleave', startAutoSlide);
            
            // Recalculate centering on window resize
            let resizeTimeout;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    if (currentSlide !== null) {
                        updateSlide(currentSlide, false);
                    }
                }, 250);
            });
        }
        
        // Navigation arrows
        if (testimonialsNext) {
            testimonialsNext.addEventListener('click', () => {
                stopAutoSlide();
                nextSlide();
                startAutoSlide();
            });
        }
        
        if (testimonialsPrev) {
            testimonialsPrev.addEventListener('click', () => {
                stopAutoSlide();
                prevSlide();
                startAutoSlide();
            });
        }
        
        // Dot navigation
        testimonialsDots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                if (index !== currentSlide && !isAnimating) {
                    stopAutoSlide();
                    updateSlide(index, true);
                    startAutoSlide();
                }
            });
        });
        
        // Handle window resize
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                updateSlide(currentSlide, false);
            }, 250);
        });
    }
});
