const orderForm = document.getElementById('order-form');

// Load and display orders on page load
renderOrders();
// Add initial item row to the form
addItemRow();

// Handle form submission for adding/editing orders
orderForm.addEventListener('submit', (e) => {
    e.preventDefault();
    
    const orderId = document.getElementById('order-id').value;
    const phone = document.getElementById('phone').value;
    const notes = document.getElementById('notes').value;
    
    const items = [];
    const itemRows = document.querySelectorAll('.item-row');
    itemRows.forEach(row => {
        const name = row.querySelector('input[name="item-name"]').value;
        const price = parseFloat(row.querySelector('input[name="item-price"]').value);
        if (name && !isNaN(price) && price > 0) {
            items.push({ name, price });
        }
    });

    if (items.length === 0) {
        alert("Please add at least one valid item.");
        return;
    }

    const order = {
        id: orderId ? parseInt(orderId) : Date.now(),
        phone,
        items,
        notes,
        status: 'pending' // Default status
    };

    if (orderId) {
        // Update existing order
        updateOrder(order);
    } else {
        // Add new order
        addOrder(order);
    }

    // Reset form
    orderForm.reset();
    document.getElementById('items-container').innerHTML = '';
    addItemRow(); // Add one row back
    cancelEdit(); // Revert form to "Add" mode
    renderOrders();
});


// --- Data Functions ---

/**
 * Fetches all orders from localStorage
 * @returns {Array} An array of order objects
 */
function getOrders() {
    return JSON.parse(localStorage.getItem('orders')) || [];
}

/**
 * Saves an array of orders to localStorage
 * @param {Array} orders The array of orders to save
 */
function saveOrders(orders) {
    localStorage.setItem('orders', JSON.stringify(orders));
}

/**
 * Adds a new order to the list
 * @param {Object} order The order object to add
 */
function addOrder(order) {
    const orders = getOrders();
    // Get status from existing order if it's an update
    const existingOrder = orders.find(o => o.id === order.id);
    if (existingOrder) {
        order.status = existingOrder.status;
    }
    orders.unshift(order); // Add new orders to the top
    saveOrders(orders);
}

/**
 * Updates an existing order
 * @param {Object} updatedOrder The order object with updated details
 */
function updateOrder(updatedOrder) {
     let orders = getOrders();
    // Preserve the status of the order being updated
    const originalOrder = orders.find(o => o.id === updatedOrder.id);
    if (originalOrder) {
        updatedOrder.status = originalOrder.status;
    }
    orders = orders.map(order => order.id === updatedOrder.id ? updatedOrder : order);
    saveOrders(orders);
}

/**
 * Deletes an order by its ID
 * @param {number} id The ID of the order to delete
 */
function deleteOrder(id) {
    if (confirm('Are you sure you want to delete this order?')) {
        let orders = getOrders();
        orders = orders.filter(order => order.id !== id);
        saveOrders(orders);
        renderOrders();
    }
}

/**
 * Toggles the completion status of an order
 * @param {number} id The ID of the order to toggle
 */
function toggleOrderStatus(id) {
    let orders = getOrders();
    const order = orders.find(order => order.id === id);
    if (order) {
        order.status = order.status === 'pending' ? 'completed' : 'pending';
        saveOrders(orders);
        renderOrders();
    }
}

// --- UI Functions ---

/**
 * Adds a new row for item name and price in the form
 */
function addItemRow() {
    const container = document.getElementById('items-container');
    const itemRow = document.createElement('div');
    itemRow.className = 'item-row';
    itemRow.innerHTML = `
        <label>
            <input type="text" name="item-name" placeholder="Item Name" required>
        </label>
        <label>
            <input type="number" name="item-price" placeholder="Price" step="0.1" min="0" required>
        </label>
        <button type="button" class="secondary outline" onclick="this.parentElement.remove()">×</button>
    `;
    container.appendChild(itemRow);
}

/**
 * Populates the form with an order's data for editing
 * @param {number} id The ID of the order to edit
 */
function startEdit(id) {
    const order = getOrders().find(o => o.id === id);
    if (!order) return;

    document.getElementById('order-id').value = order.id;
    document.getElementById('phone').value = order.phone;
    document.getElementById('notes').value = order.notes;

    const itemsContainer = document.getElementById('items-container');
    itemsContainer.innerHTML = ''; // Clear existing item rows
    order.items.forEach(item => {
        addItemRow();
        const lastRow = itemsContainer.lastChild;
        lastRow.querySelector('input[name="item-name"]').value = item.name;
        lastRow.querySelector('input[name="item-price"]').value = item.price;
    });

    document.getElementById('form-title').textContent = 'Edit Order';
    document.getElementById('submit-button').textContent = 'Update Order';
    document.getElementById('cancel-edit-button').style.display = 'inline-block';
    
    // Scroll to the form
    document.getElementById('order-form').scrollIntoView({ behavior: 'smooth' });
}

/**
 * Cancels the edit mode and resets the form
 */
function cancelEdit() {
    document.getElementById('order-form').reset();
    document.getElementById('order-id').value = '';
    const itemsContainer = document.getElementById('items-container');
    itemsContainer.innerHTML = '';
    addItemRow();

    document.getElementById('form-title').textContent = 'Add New Order';
    document.getElementById('submit-button').textContent = 'Add Order';
    document.getElementById('cancel-edit-button').style.display = 'none';
}

/**
 * Renders all orders to the page
 */
function renderOrders() {
    const orders = getOrders();
    const container = document.getElementById('orders-container');
    container.innerHTML = '';

    if (orders.length === 0) {
        container.innerHTML = `<div class="no-orders"><p>No orders yet. Add one using the form above!</p></div>`;
        return;
    }
    
    orders.forEach(order => {
        const totalPrice = order.items.reduce((sum, item) => sum + item.price, 0);
        const article = document.createElement('article');
        article.className = `order-card ${order.status}`;
        article.dataset.orderId = order.id;

        article.innerHTML = `
            <hgroup>
                <h3>Order for: ${order.phone}</h3>
                <p>Status: <strong>${order.status.toUpperCase()}</strong></p>
            </hgroup>
            <div>
                <strong>Items:</strong>
                <ul>
                    ${order.items.map(item => `<li>${item.name} - ${item.price.toFixed(3)} BHD</li>`).join('')}
                </ul>
                <p class="total-price">Total: ${totalPrice.toFixed(3)} BHD</p>
            </div>
            ${order.notes ? `<p><strong>Notes:</strong> ${order.notes}</p>` : ''}
            <footer class="order-actions">
                <button class="secondary outline" onclick="startEdit(${order.id})">Edit</button>
                <button class="contrast" onclick="deleteOrder(${order.id})">Delete</button>
                <button onclick="toggleOrderStatus(${order.id})">
                    ${order.status === 'pending' ? 'Mark Complete' : 'Mark Pending'}
                </button>
            </footer>
        `;
        container.appendChild(article);
    });
}
