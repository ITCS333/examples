// --- INITIALIZATION ---
// We wrap the initial setup in a DOMContentLoaded event to ensure all HTML elements are ready.
document.addEventListener('DOMContentLoaded', () => {
    const orderForm = document.getElementById('order-form');
    const searchInput = document.getElementById('search-input');
    const sortSelect = document.getElementById('sort-select');

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
            updateOrder(order);
        } else {
            addOrder(order);
        }

        orderForm.reset();
        document.getElementById('items-container').innerHTML = '';
        addItemRow(); 
        cancelEdit();
        renderOrders();
    });

    // Add event listener for the search input
    searchInput.addEventListener('input', renderOrders);

    // Add event listener for the sort dropdown
    sortSelect.addEventListener('change', renderOrders);
});


// --- DATA FUNCTIONS ---

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
    const existingOrder = orders.find(o => o.id === order.id);
    if (existingOrder) {
        order.status = existingOrder.status;
    }
    orders.unshift(order);
    saveOrders(orders);
}

/**
 * Updates an existing order
 * @param {Object} updatedOrder The order object with updated details
 */
function updateOrder(updatedOrder) {
     let orders = getOrders();
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

// --- UI FUNCTIONS ---

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
    itemsContainer.innerHTML = '';
    order.items.forEach(item => {
        addItemRow();
        const lastRow = itemsContainer.lastChild;
        lastRow.querySelector('input[name="item-name"]').value = item.name;
        lastRow.querySelector('input[name="item-price"]').value = item.price;
    });

    document.getElementById('form-title').textContent = 'Edit Order';
    document.getElementById('submit-button').textContent = 'Update Order';
    document.getElementById('cancel-edit-button').style.display = 'inline-block';
    
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
 * Renders all orders to the page in a table, applying search and sort.
 */
function renderOrders() {
    const searchInput = document.getElementById('search-input');
    const sortSelect = document.getElementById('sort-select');
    const searchTerm = searchInput.value.toLowerCase();
    const sortDirection = sortSelect.value;
    let orders = getOrders();

    // 1. Filter (Search) by customer phone
    if (searchTerm) {
        orders = orders.filter(order => order.phone.includes(searchTerm));
    }

    // 2. Sort by customer number (phone)
    orders.sort((a, b) => {
        const phoneA = a.phone;
        const phoneB = b.phone;
        if (sortDirection === 'asc') {
            return phoneA.localeCompare(phoneB);
        } else { // 'desc'
            return phoneB.localeCompare(phoneA);
        }
    });

    const tableBody = document.getElementById('orders-table-body');
    tableBody.innerHTML = ''; // Clear previous content

    if (orders.length === 0) {
        const row = tableBody.insertRow();
        row.innerHTML = `<td colspan="6" style="text-align: center;">No orders found.</td>`;
        return;
    }
    
    orders.forEach(order => {
        const totalPrice = order.items.reduce((sum, item) => sum + item.price, 0);
        const row = tableBody.insertRow();
        row.className = order.status; // e.g., 'pending' or 'completed'

        // Column: Customer Phone
        row.insertCell().textContent = order.phone;

        // Column: Items
        const itemsCell = row.insertCell();
        const itemsList = document.createElement('ul');
        itemsList.style.margin = '0';
        itemsList.style.paddingLeft = '1.2em';
        order.items.forEach(item => {
            const li = document.createElement('li');
            li.textContent = `${item.name} - ${item.price.toFixed(3)} BHD`;
            itemsList.appendChild(li);
        });
        itemsCell.appendChild(itemsList);

        // Column: Total Price
        row.insertCell().textContent = `${totalPrice.toFixed(3)} BHD`;
        
        // Column: Notes
        row.insertCell().textContent = order.notes || '—';

        // Column: Status
        const statusCell = row.insertCell();
        statusCell.innerHTML = `<span class="status-${order.status}">${order.status}</span>`;

        // Column: Actions
        const actionsCell = row.insertCell();
        actionsCell.innerHTML = `
            <div style="font-size: 8pt">
	        <div role="group">
                    <button class="secondary outline" onclick="startEdit(${order.id})">📝</button>
                    <button class="contrast" onclick="deleteOrder(${order.id})">❌</button>
                    <button onclick="toggleOrderStatus(${order.id})">
                        ${order.status === 'pending' ? '✅' : '⏳'}
                    </button>
		</div>
            </div>
        `;
    });
}

