// ================================
// Admin JavaScript File
// Functions specific to admin panel
// ================================

// Delete category with confirmation
function deleteCategory(id, name) {
    if (confirm('Delete category "' + name + '"? All food items in this category will also be deleted.')) {
        window.location.href = 'categories.php?delete=' + id;
    }
}

// Delete food item with confirmation
function deleteFood(id, name) {
    if (confirm('Delete food item "' + name + '"?')) {
        window.location.href = 'foods.php?delete=' + id;
    }
}

// Update order status via AJAX
function updateOrderStatus(orderId, newStatus) {
    const data = 'order_id=' + orderId + '&status=' + newStatus;
    
    makeRequest('../api/update_order_status.php', 'POST', data, function(error, response) {
        if (error) {
            alert('Error updating status');
            return;
        }
        
        if (response.success) {
            showAlert('Order status updated successfully', 'success');
            setTimeout(function() {
                location.reload();
            }, 1000);
        } else {
            alert('Error: ' + response.message);
        }
    });
}

// View order details in modal
function viewOrderDetails(orderId) {
    // Fetch order details via AJAX
    makeRequest('../api/get_order_details.php?order_id=' + orderId, 'GET', null, function(error, response) {
        if (error || !response.success) {
            alert('Error loading order details');
            return;
        }
        
        // Create modal
        const modal = createOrderModal(response.data);
        document.body.appendChild(modal);
    });
}

// Create order details modal
function createOrderModal(orderData) {
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
    
    let itemsHTML = '';
    orderData.items.forEach(function(item) {
        itemsHTML += '<p>• ' + item.food_name + ' x ' + item.quantity + ' - ' + formatPrice(item.price * item.quantity) + '</p>';
    });
    
    modal.innerHTML = `
        <div class="modal-header">Order #${orderData.order.id} Details</div>
        <div>
            <p><strong>Customer:</strong> ${orderData.order.customer_name}</p>
            <p><strong>Phone:</strong> ${orderData.order.customer_phone}</p>
            <p><strong>Status:</strong> ${orderData.order.status}</p>
            <p><strong>Time:</strong> ${formatDate(orderData.order.order_time)}</p>
            <hr>
            <h4>Items:</h4>
            ${itemsHTML}
            ${orderData.order.notes ? '<p><strong>Notes:</strong> ' + orderData.order.notes + '</p>' : ''}
            <hr>
            <p><strong>Total:</strong> ${formatPrice(orderData.order.total_amount)}</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="this.closest('.modal-overlay').remove()">Close</button>
        </div>
    `;
    
    overlay.appendChild(modal);
    return overlay;
}

// Toggle food availability
function toggleFoodAvailability(foodId, currentStatus) {
    const newStatus = currentStatus === '1' ? '0' : '1';
    const data = 'food_id=' + foodId + '&available=' + newStatus;
    
    makeRequest('../api/toggle_food_availability.php', 'POST', data, function(error, response) {
        if (error) {
            alert('Error updating availability');
            return;
        }
        
        if (response.success) {
            location.reload();
        } else {
            alert('Error: ' + response.message);
        }
    });
}

// Search functionality for tables
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toLowerCase();
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < cells.length; j++) {
            const cell = cells[j];
            if (cell) {
                const textValue = cell.textContent || cell.innerText;
                if (textValue.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        row.style.display = found ? '' : 'none';
    }
}

// Export table to CSV
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    const rows = table.querySelectorAll('tr');
    let csv = [];
    
    for (let i = 0; i < rows.length; i++) {
        const row = [];
        const cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }
        
        csv.push(row.join(','));
    }
    
    downloadCSV(csv.join('\n'), filename);
}

// Download CSV file
function downloadCSV(csv, filename) {
    const csvFile = new Blob([csv], { type: 'text/csv' });
    const downloadLink = document.createElement('a');
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}