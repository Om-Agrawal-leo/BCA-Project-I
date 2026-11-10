// ================================
// Main JavaScript File
// Common functions used across the application
// ================================

// Show alert message
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    // Insert at top of body
    document.body.insertBefore(alertDiv, document.body.firstChild);
    
    // Auto remove after 3 seconds
    setTimeout(function() {
        alertDiv.remove();
    }, 3000);
}

// Confirm delete action
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this?');
}

// Format price
function formatPrice(price) {
    return '$' + parseFloat(price).toFixed(2);
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return date.toLocaleDateString('en-US', options);
}

// Validate email format
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Validate phone number (basic)
function validatePhone(phone) {
    const re = /^[0-9]{10,15}$/;
    return re.test(phone.replace(/[-\s]/g, ''));
}

// Show loading indicator
function showLoading(element) {
    const loadingHTML = '<span class="loading"></span> Loading...';
    if (element) {
        element.innerHTML = loadingHTML;
        element.disabled = true;
    }
}

// Hide loading indicator
function hideLoading(element, originalText) {
    if (element) {
        element.innerHTML = originalText;
        element.disabled = false;
    }
}

// AJAX helper function
function makeRequest(url, method, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    
    if (method === 'POST') {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    }
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                callback(null, response);
            } catch (e) {
                callback(null, { success: true, data: xhr.responseText });
            }
        } else {
            callback({ error: 'Request failed' }, null);
        }
    };
    
    xhr.onerror = function() {
        callback({ error: 'Network error' }, null);
    };
    
    if (method === 'POST' && data) {
        xhr.send(data);
    } else {
        xhr.send();
    }
}

// Convert object to URL parameters
function objectToUrlParams(obj) {
    return Object.keys(obj)
        .map(key => encodeURIComponent(key) + '=' + encodeURIComponent(obj[key]))
        .join('&');
}

// Session timeout warning (30 minutes)
let sessionTimeout;
let warningTimeout;

function resetSessionTimer() {
    clearTimeout(sessionTimeout);
    clearTimeout(warningTimeout);
    
    // Warn at 28 minutes
    warningTimeout = setTimeout(function() {
        if (confirm('Your session will expire in 2 minutes. Click OK to stay logged in.')) {
            resetSessionTimer();
        }
    }, 28 * 60 * 1000);
    
    // Logout at 30 minutes
    sessionTimeout = setTimeout(function() {
        alert('Your session has expired. Please login again.');
        window.location.href = '/food-ordering-system/customer/logout.php';
    }, 30 * 60 * 1000);
}

// Initialize session timer on page load
if (document.body.classList.contains('logged-in')) {
    resetSessionTimer();
    
    // Reset on user activity
    document.addEventListener('click', resetSessionTimer);
    document.addEventListener('keypress', resetSessionTimer);
}