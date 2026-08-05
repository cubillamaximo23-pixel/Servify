const ADMIN_CREDENTIALS = {
    username: 'admin',
    password: 'servify123'
};

const defaultOrders = [
    { id: 1, name: 'Pizza Margarita', quantity: 2, status: 'En preparación' },
    { id: 2, name: 'Hamburguesa Doble', quantity: 1, status: 'Terminado' },
    { id: 3, name: 'Jugo Natural', quantity: 3, status: 'En preparación' }
];

const defaultMenuItems = [
    { id: 'item-1', section: 'Pizzas', name: 'Pizza Margarita', price: 4200 },
    { id: 'item-2', section: 'Hamburguesas', name: 'Hamburguesa Clásica', price: 5300 },
    { id: 'item-3', section: 'Ensaladas', name: 'Ensalada César', price: 3600 },
    { id: 'item-4', section: 'Bebidas', name: 'Coca Cola', price: 1900 }
];

let orders = [];
let menuItems = [];

function getElement(id) {
    return document.getElementById(id);
}

function loadAdminData() {
    const storedOrders = localStorage.getItem('servifyAdminOrders');
    const storedMenu = localStorage.getItem('servifyAdminMenu');

    orders = storedOrders ? JSON.parse(storedOrders) : defaultOrders.slice();
    menuItems = storedMenu ? JSON.parse(storedMenu) : defaultMenuItems.slice();
}

function saveAdminData() {
    localStorage.setItem('servifyAdminOrders', JSON.stringify(orders));
    localStorage.setItem('servifyAdminMenu', JSON.stringify(menuItems));
}

function renderDashboard() {
    const totalOrders = orders.length;
    const preparing = orders.filter(order => order.status === 'En preparación').length;
    const finished = orders.filter(order => order.status === 'Terminado').length;

    getElement('totalOrders').textContent = `Pedidos totales: ${totalOrders}`;
    getElement('preparingOrders').textContent = `En preparación: ${preparing}`;
    getElement('finishedOrders').textContent = `Terminados: ${finished}`;

    renderOrdersTable();
    renderMenuItemsTable();
}

function renderOrdersTable() {
    const tbody = getElement('ordersTable').querySelector('tbody');
    tbody.innerHTML = '';

    orders.forEach(order => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${order.name}</td>
            <td>${order.quantity}</td>
            <td>${order.status}</td>
            <td>
                <button class="button-secondary" type="button" data-order-id="${order.id}">Cambiar estado</button>
                <button class="button-primary" type="button" data-remove-id="${order.id}">Eliminar</button>
            </td>
        `;

        tbody.appendChild(row);
    });

    tbody.querySelectorAll('[data-order-id]').forEach(button => {
        button.addEventListener('click', () => {
            const id = Number(button.dataset.orderId);
            toggleOrderStatus(id);
        });
    });

    tbody.querySelectorAll('[data-remove-id]').forEach(button => {
        button.addEventListener('click', () => {
            const id = Number(button.dataset.removeId);
            removeOrder(id);
        });
    });
}

function renderMenuItemsTable() {
    const tbody = getElement('menuItemsTable').querySelector('tbody');
    tbody.innerHTML = '';

    menuItems.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.section}</td>
            <td>${item.name}</td>
            <td>$${item.price.toLocaleString('es-AR')}</td>
            <td><button class="button-secondary" type="button" data-delete-id="${item.id}">Eliminar</button></td>
        `;

        tbody.appendChild(row);
    });

    tbody.querySelectorAll('[data-delete-id]').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.deleteId;
            deleteMenuItem(id);
        });
    });
}

function toggleOrderStatus(orderId) {
    const order = orders.find(entry => entry.id === orderId);
    if (!order) return;

    order.status = order.status === 'En preparación' ? 'Terminado' : 'En preparación';
    saveAdminData();
    renderDashboard();
}

function removeOrder(orderId) {
    orders = orders.filter(entry => entry.id !== orderId);
    saveAdminData();
    renderDashboard();
}

function deleteMenuItem(itemId) {
    menuItems = menuItems.filter(entry => entry.id !== itemId);
    saveAdminData();
    renderDashboard();
}

function addMenuItem() {
    const nameInput = getElement('newItemName');
    const descriptionInput = getElement('newItemDescription');
    const priceInput = getElement('newItemPrice');
    const sectionInput = getElement('newItemSection');
    const message = getElement('adminMessage');

    const name = nameInput.value.trim();
    const description = descriptionInput.value.trim();
    const price = Number(priceInput.value);
    const section = sectionInput.value;

    if (!name || !description || price <= 0) {
        message.style.color = '#ff7373';
        message.textContent = 'Completa todos los campos antes de guardar.';
        message.style.display = 'block';
        return;
    }

    const newItem = {
        id: `menu-${Date.now()}`,
        section,
        name,
        price
    };

    menuItems.push(newItem);
    saveAdminData();
    renderDashboard();

    nameInput.value = '';
    descriptionInput.value = '';
    priceInput.value = '';
    sectionInput.value = 'Pizzas';

    message.style.color = '#a5ff9f';
    message.textContent = 'Producto agregado correctamente.';
    message.style.display = 'block';

    setTimeout(() => {
        message.style.display = 'none';
    }, 3000);
}

function showSection(sectionId) {
    getElement('loginSection').classList.toggle('hidden', sectionId !== 'loginSection');
    getElement('dashboardSection').classList.toggle('hidden', sectionId !== 'dashboardSection');
}

function loginAdmin() {
    const username = getElement('adminUser').value.trim();
    const password = getElement('adminPass').value;
    const errorText = getElement('loginError');

    if (username === ADMIN_CREDENTIALS.username && password === ADMIN_CREDENTIALS.password) {
        errorText.style.display = 'none';
        loadAdminData();
        renderDashboard();
        showSection('dashboardSection');
    } else {
        errorText.style.display = 'block';
    }
}

function logoutAdmin() {
    showSection('loginSection');
    getElement('adminUser').value = '';
    getElement('adminPass').value = '';
}

document.addEventListener('DOMContentLoaded', () => {
    getElement('loginButton').addEventListener('click', loginAdmin);
    getElement('logoutButton').addEventListener('click', logoutAdmin);
    getElement('addMenuItemButton').addEventListener('click', addMenuItem);
    showSection('loginSection');
});
