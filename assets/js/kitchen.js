// kitchen.js - Kitchen JavaScript (real-time)
// ================================
// Kitchen JavaScript File
// Real-time order updates for kitchen
// ================================

let lastUpdateTime = new Date();
let refreshInterval;
let previousOrderCount = 0;
let notificationSound = null;

// Initialize notification sound
function initNotificationSound() {
    notificationSound = new Audio('/food-ordering-system/assets/sounds/se.mp3');
    notificationSound.preload = 'auto';
}

// Initialize kitchen dashboard
function initKitchenDashboard() {
    initNotificationSound();
    updateLastRefreshTime();
    startAutoRefresh();
}

// Start auto-refresh
function startAutoRefresh() {
    refreshInterval = setInterval(function() {
        refreshOrders();
    }, 5000); // Refresh every 5 seconds
}

// Stop auto-refresh
function stopAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
}

// Refresh orders from server
function refreshOrders() {
    const indicator = document.getElementById('refreshIndicator');
    if (indicator) {
        indicator.style.display = 'inline-block';
    }
    
    makeRequest('get_orders.php', 'GET', null, function(error, response) {
        if (indicator) {
            indicator.style.display = 'none';
        }
        
        if (error) {
            console.error('Error refreshing orders:', error);
            return;
        }
        
        if (response.success) {
            updateOrdersDisplay(response.data);
            lastUpdateTime = new Date();
        }
    });
}

// Update orders display
function updateOrdersDisplay(orders) {
    const ordersGrid = document.getElementById('ordersGrid');
    if (!ordersGrid) return;
    
    // Check for new orders (count pending orders)
    const pendingOrders = orders.filter(function(order) {
        return order.status === 'Pending';
    });
    
    // Play sound if there are new pending orders
    if (pendingOrders.length > previousOrderCount && previousOrderCount !== 0) {
        playNotificationSound();
    }
    previousOrderCount = pendingOrders.length;
    
    if (orders.length === 0) {
        ordersGrid.innerHTML = `
            <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                <h2>No active orders</h2>
                <p>All orders have been completed!</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    orders.forEach(function(order) {
        html += createOrderCard(order);
    });
    
    ordersGrid.innerHTML = html;
}

// Create order card HTML
function createOrderCard(order) {
    let itemsHTML = '';
    order.items.forEach(function(item) {
        itemsHTML += '<div>• ' + item.food_name + ' (x' + item.quantity + ')</div>';
    });
    
    let notesHTML = '';
    if (order.notes) {
        notesHTML = `
            <div style="background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0;">
                <strong>Note:</strong> ${order.notes}
            </div>
        `;
    }
    
    let buttonHTML = '';
    if (order.status === 'Pending') {
        buttonHTML = '<button class="btn btn-info btn-sm" onclick="updateOrderStatus(' + order.id + ', \'Preparing\')">Start Preparing</button>';
    } else if (order.status === 'Preparing') {
        buttonHTML = '<button class="btn btn-success btn-sm" onclick="updateOrderStatus(' + order.id + ', \'Ready\')">Mark Ready</button>';
    } else if (order.status === 'Ready') {
        buttonHTML = '<button class="btn btn-success btn-sm" onclick="updateOrderStatus(' + order.id + ', \'Served\')">Mark Served</button>';
    }
    
    return `
        <div class="kitchen-order-card" id="order-${order.id}">
            <div class="order-number">Order #${order.id}</div>
            <div class="order-time">${formatDate(order.order_time)}</div>
            
            <div style="margin: 15px 0;">
                <strong>Customer:</strong> ${order.customer_name}<br>
                <strong>Phone:</strong> ${order.customer_phone}
            </div>
            
            <div style="margin: 15px 0;">
                <strong>Items:</strong>
                ${itemsHTML}
            </div>
            
            ${notesHTML}
            
            <div>
                <strong>Status:</strong>
                <span class="badge badge-${order.status.toLowerCase()}" style="background-color: ${getStatusColor(order.status)};">
                    ${order.status}
                </span>
            </div>
            
            <div class="status-buttons">
                ${buttonHTML}
            </div>
        </div>
    `;
}

// Get status color
function getStatusColor(status) {
    switch(status) {
        case 'Pending': return '#ff9800';
        case 'Preparing': return '#2196F3';
        case 'Ready': return '#4CAF50';
        case 'Served': return '#4CAF50';
        default: return '#757575';
    }
}

// Update order status
function updateOrderStatus(orderId, newStatus) {
    if (confirm('Update order status to ' + newStatus + '?')) {
        const data = 'order_id=' + orderId + '&status=' + newStatus;
        
        makeRequest('../api/update_order_status.php', 'POST', data, function(error, response) {
            if (error) {
                alert('Error updating status');
                return;
            }
            
            if (response.success) {
                showAlert('Status updated to ' + newStatus, 'success');
                refreshOrders();
            } else {
                alert('Error: ' + response.message);
            }
        });
    }
}

// Update last refresh time display
function updateLastRefreshTime() {
    setInterval(function() {
        const now = new Date();
        const seconds = Math.floor((now - lastUpdateTime) / 1000);
        const updateElement = document.getElementById('lastUpdate');
        
        if (updateElement) {
            if (seconds < 60) {
                updateElement.textContent = 'Updated ' + seconds + 's ago';
            } else {
                const minutes = Math.floor(seconds / 60);
                updateElement.textContent = 'Updated ' + minutes + 'm ago';
            }
        }
    }, 1000);
}

// Play notification sound
function playNotificationSound() {
    if (notificationSound) {
        notificationSound.currentTime = 0;
        notificationSound.play().catch(function(error) {
            console.log('Sound play failed:', error);
        });
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname.includes('kitchen')) {
        initKitchenDashboard();
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    stopAutoRefresh();
});
