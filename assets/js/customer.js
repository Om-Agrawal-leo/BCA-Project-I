/**
 * Creates a temporary alert message.
 * @param {string} message The message to display.
 * @param {string} type 'info', 'success', 'error', 'warning'.
 * @param {number} duration Duration in milliseconds.
 */
function showAlert(message, type = 'info', duration = 3000) {
    const alertBox = document.createElement('div');
    alertBox.className = `alert alert-${type}`;
    // These styles ensure the alert is visible on top of other content.
    alertBox.style.position = 'fixed';
    alertBox.style.top = '20px';
    alertBox.style.right = '20px';
    alertBox.style.zIndex = '1001';
    alertBox.style.boxShadow = '0 5px 15px rgba(0,0,0,0.2)';
    alertBox.textContent = message;
    
    document.body.appendChild(alertBox);
    
    setTimeout(() => {
        alertBox.style.transition = 'opacity 0.5s';
        alertBox.style.opacity = '0';
        setTimeout(() => alertBox.remove(), 500);
    }, duration);
}

/**
 * Makes an AJAX request.
 * @param {string} url The URL to request.
 * @param {string} method 'GET' or 'POST'.
 * @param {string|null} data The data to send.
 * @param {function} callback The function to call on completion.
 */
function makeRequest(url, method, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    
    if (method === 'POST') {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    }
    
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 400) {
            try {
                const response = JSON.parse(xhr.responseText);
                callback(null, response);
            } catch (e) {
                console.error("JSON parsing error:", e, "Response:", xhr.responseText);
                callback('Error parsing server response');
            }
        } else {
            callback('Request failed with status: ' + xhr.status);
        }
    };
    
    xhr.onerror = function() {
        callback('Network error');
    };
    
    xhr.send(data);
}

/**
 * Formats a number as currency.
 * @param {number|string} price The price to format.
 * @returns {string}
 */
function formatPrice(price) {
    return 'Rs. ' + Number(price).toFixed(2);
}

/**
 * Formats a date string.
 * @param {string} dateString The date string to format.
 * @returns {string}
 */
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// ================================
// Customer JavaScript File
// Functions specific to customer interface
// ================================

// Add item to cart
function addToCart(foodId, foodName) {
    const data = 'food_id=' + foodId;
    
    showAlert('Adding to cart...', 'info');
    
    makeRequest('../api/add_to_cart.php', 'POST', data, function(error, response) {
        if (error) {
            showAlert('Error adding to cart', 'error');
            return;
        }
        
        if (response.success) {
            showAlert(foodName + ' added to cart!', 'success');
            
            // Update cart count badge
            setTimeout(function() {
                location.reload();
            }, 1000);
        } else {
            showAlert(response.message, 'error');
        }
    });
}

// Update cart quantity
function updateCartQuantity(foodId, change) {
    const data = 'food_id=' + foodId + '&change=' + change;
    
    makeRequest('../api/update_cart.php', 'POST', data, function(error, response) {
        if (error) {
            showAlert('Error updating cart', 'error');
            return;
        }
        
        if (response.success) {
            location.reload();
        }
    });
}

// Remove item from cart
function removeFromCart(foodId, foodName) {
    if (confirm('Remove ' + foodName + ' from cart?')) {
        const data = 'food_id=' + foodId;
        
        makeRequest('../api/remove_from_cart.php', 'POST', data, function(error, response) {
            if (error) {
                showAlert('Error removing item', 'error');
                return;
            }
            
            if (response.success) {
                showAlert('Item removed from cart', 'success');
                setTimeout(function() {
                    location.reload();
                }, 1000);
            }
        });
    }
}

// Search food items
function searchFood() {
    const searchInput = document.getElementById('searchInput');
    const searchValue = searchInput.value.trim();
    
    if (searchValue) {
        window.location.href = 'dashboard.php?search=' + encodeURIComponent(searchValue);
    }
}

// Clear search
function clearSearch() {
    window.location.href = 'dashboard.php';
}

// Track order status
function trackOrder(orderId) {
    makeRequest('../api/track_order.php?order_id=' + orderId, 'GET', null, function(error, response) {
        if (error || !response.success) {
            showAlert('Error tracking order', 'error');
            return;
        }
        
        const order = response.data;
        alert('Order #' + order.id + '\nStatus: ' + order.status + '\nTime: ' + formatDate(order.order_time));
    });
}

// Auto-refresh order status on orders page
function startOrderTracking() {
    setInterval(function() {
        const currentPage = window.location.pathname;
        if (currentPage.includes('orders.php')) {
            location.reload();
        }
    }, 10000); // Refresh every 10 seconds
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add Enter key support for search
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchFood();
            }
        });
    }
    
    // Start order tracking if on orders page
    if (window.location.pathname.includes('orders.php')) {
        startOrderTracking();
    }
});

// Validate checkout form
function validateCheckoutForm() {
    const form = document.getElementById('checkoutForm');
    if (!form) return true;
    
    // Add custom validation here
    return true;
}

// Quick add to cart with quantity
function quickAddToCart(foodId, foodName) {
    const quantity = prompt('Enter quantity for ' + foodName + ':', '1');
    
    if (quantity && parseInt(quantity) > 0) {
        for (let i = 0; i < parseInt(quantity); i++) {
            addToCart(foodId, foodName);
        }
    }
}

// Filter foods by category
function filterByCategory(categoryId) {
    window.location.href = 'dashboard.php?category=' + categoryId;
}

// View food details
function viewFoodDetails(foodId) {
    makeRequest('../api/get_food_details.php?food_id=' + foodId, 'GET', null, function(error, response) {
        if (error || !response.success) {
            alert('Error loading food details');
            return;
        }
        
        const food = response.data;
        const modal = createFoodModal(food);
        document.body.appendChild(modal);
    });
}

// Create food details modal
function createFoodModal(food) {
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay';
    overlay.onclick = function() {
        this.remove();
    };
    
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.onclick = function(e) {
        e.stopPropagation();
    };
    
    modal.innerHTML = `
        <div class="modal-header">${food.name}</div>
        <div>
            <p><strong>Category:</strong> ${food.category_name}</p>
            <p><strong>Description:</strong> ${food.description || 'No description available'}</p>
            <p><strong>Price:</strong> ${formatPrice(food.price)}</p>
            <p><strong>Availability:</strong> ${food.available === '1' ? 'Available' : 'Not Available'}</p>
        </div>
        <div class="modal-footer">
            ${food.available === '1' ? 
                '<button class="btn btn-success" onclick="addToCart(' + food.id + ', \'' + food.name + '\'); this.closest(\'.modal-overlay\').remove();">Add to Cart</button>' : 
                '<button class="btn btn-secondary" disabled>Not Available</button>'}
            <button class="btn btn-primary" onclick="this.closest('.modal-overlay').remove()">Close</button>
        </div>
    `;
    
    overlay.appendChild(modal);
    return overlay;
}