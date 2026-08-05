const menuData = [
    {
        title: 'Pizzas',
        description: 'Clásicas y especiales con ingredientes seleccionados.',
        items: [
            { id: 'pizza-margarita', name: 'Pizza Margarita', description: 'Tomate, mozzarella, albahaca fresca y aceite de oliva.', price: 4200 },
            { id: 'pizza-pepperoni', name: 'Pizza Pepperoni', description: 'Salsa de tomate, mozzarella y pepperoni crocante.', price: 4800 },
            { id: 'pizza-vegetal', name: 'Pizza Vegetal', description: 'Champiñones, pimientos, cebolla, aceitunas y tomates cherry.', price: 4600 }
        ]
    },
    {
        title: 'Hamburguesas',
        description: 'Jugosas, con pan tostado y aderezos caseros.',
        items: [
            { id: 'burger-clasica', name: 'Hamburguesa Clásica', description: 'Carne vacuna, queso cheddar, lechuga, tomate y pepinillos.', price: 5300 },
            { id: 'burger-doble', name: 'Hamburguesa Doble', description: 'Dos medallones, queso, bacon y salsa especial.', price: 7200 },
            { id: 'burger-vegetariana', name: 'Hamburguesa Vegetariana', description: 'Hamburguesa de vegetales, rúcula, palta y alioli.', price: 5400 }
        ]
    },
    {
        title: 'Ensaladas',
        description: 'Frescas y ligeras para acompañar tu pedido.',
        items: [
            { id: 'ensalada-caesar', name: 'Ensalada César', description: 'Lechuga, croutons, parmesano y aderezo césar.', price: 3600 },
            { id: 'ensalada-garden', name: 'Ensalada Garden', description: 'Mix de hojas verdes, zanahoria, pepino y tomates.', price: 3400 }
        ]
    },
    {
        title: 'Bebidas',
        description: 'Refrescos y bebidas para completar tu comida.',
        items: [
            { id: 'bebida-coca', name: 'Coca Cola', description: 'Lata 350 ml.', price: 1900 },
            { id: 'bebida-agua', name: 'Agua Mineral', description: 'Agua mineral con o sin gas.', price: 1300 },
            { id: 'bebida-jugo', name: 'Jugo Natural', description: 'Jugo natural de naranja o limón.', price: 2500 }
        ]
    }
];

const cart = [];
let cartItemIdCounter = 1;

const formatPrice = amount => `$${amount.toLocaleString('es-AR')}`;

function renderMenuSections() {
    const menuSections = document.getElementById('menuSections');
    menuSections.innerHTML = '';

    menuData.forEach(section => {
        const sectionElement = document.createElement('div');
        sectionElement.className = 'menu-section';

        sectionElement.innerHTML = `
            <div class="section-header">
                <div>
                    <h3>${section.title}</h3>
                    <p>${section.description}</p>
                </div>
            </div>
            <div class="section-list"></div>
        `;

        const listContainer = sectionElement.querySelector('.section-list');

        section.items.forEach(item => {
            const itemCard = document.createElement('div');
            itemCard.className = 'menu-item';
            itemCard.innerHTML = `
                <div class="item-info">
                    <h4>${item.name}</h4>
                    <p>${item.description}</p>
                    <input type="text" placeholder="Ingredientes no deseados (ej: cebolla, aceitunas)" class="input-notes" id="notes-${item.id}" aria-label="Ingredientes no deseados para ${item.name}">
                </div>
                <div class="item-meta">
                    <span class="item-price">${formatPrice(item.price)}</span>
                    <button class="button-primary" type="button" data-item-id="${item.id}">Agregar</button>
                </div>
            `;

            const addButton = itemCard.querySelector('button');
            addButton.addEventListener('click', () => {
                const notesInput = document.getElementById(`notes-${item.id}`);
                addToCart(item, notesInput.value.trim());
                notesInput.value = '';
            });

            listContainer.appendChild(itemCard);
        });

        menuSections.appendChild(sectionElement);
    });
}

function addToCart(item, notes) {
    const normalizedNotes = notes || 'Sin observaciones';
    const existingItem = cart.find(entry => entry.id === item.id && entry.notes === normalizedNotes);

    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        const cartItem = {
            cartItemId: cartItemIdCounter++,
            id: item.id,
            name: item.name,
            description: item.description,
            price: item.price,
            quantity: 1,
            notes: normalizedNotes,
            status: 'En preparación'
        };

        cart.push(cartItem);
    }

    renderCart();
}

function removeCartItem(cartItemId) {
    const index = cart.findIndex(entry => entry.cartItemId === cartItemId);
    if (index !== -1) {
        cart.splice(index, 1);
        renderCart();
    }
}

function changeCartQuantity(cartItemId, delta) {
    const item = cart.find(entry => entry.cartItemId === cartItemId);
    if (item) {
        item.quantity = Math.max(1, item.quantity + delta);
        renderCart();
    }
}

function toggleOrderStatus(cartItemId) {
    const item = cart.find(entry => entry.cartItemId === cartItemId);
    if (item) {
        item.status = item.status === 'En preparación' ? 'Terminado' : 'En preparación';
        renderCart();
    }
}

function renderCart() {
    const cartContent = document.getElementById('cartContent');
    const cartSummary = document.getElementById('cartSummary');
    const orderStatusOverview = document.getElementById('orderStatusOverview');

    cartContent.innerHTML = '';

    if (cart.length === 0) {
        cartContent.innerHTML = '<p>Aún no hay pedidos en el carrito. Agrega algo del menú.</p>';
    } else {
        cart.forEach(entry => {
            const cartItem = document.createElement('div');
            cartItem.className = 'cart-item';
            cartItem.innerHTML = `
                <h4>${entry.name}</h4>
                <p>${entry.description}</p>
                <div class="cart-meta">
                    <span>Precio: ${formatPrice(entry.price)}</span>
                    <span>Cantidad: ${entry.quantity}</span>
                    <span>Subtotal: ${formatPrice(entry.price * entry.quantity)}</span>
                </div>
                <small><strong>Ingredientes no deseados:</strong> ${entry.notes}</small>
                <div class="cart-actions">
                    <button class="button-secondary" type="button" data-action="decrease" data-id="${entry.cartItemId}">-</button>
                    <button class="button-secondary" type="button" data-action="increase" data-id="${entry.cartItemId}">+</button>
                    <button class="button-secondary" type="button" data-action="toggle" data-id="${entry.cartItemId}">${entry.status === 'En preparación' ? 'Marcar terminado' : 'Volver a preparar'}</button>
                    <button class="button-primary" type="button" data-action="remove" data-id="${entry.cartItemId}">Eliminar</button>
                </div>
            `;

            cartContent.appendChild(cartItem);
        });
    }

    const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
    const preparing = cart.filter(item => item.status === 'En preparación').length;
    const finished = cart.filter(item => item.status === 'Terminado').length;

    cartSummary.innerHTML = `
        <p><strong>Total de pedidos:</strong> ${cart.length}</p>
        <p><strong>Monto total:</strong> ${formatPrice(total)}</p>
        <p><strong>En preparación:</strong> ${preparing}</p>
        <p><strong>Terminado:</strong> ${finished}</p>
    `;

    orderStatusOverview.innerHTML = `
        <span>En preparación: ${preparing}</span>
        <span>Terminado: ${finished}</span>
    `;

    const statusText = document.getElementById('statusText');
    statusText.textContent = cart.length === 0
        ? 'Aquí verás el estado de tus pedidos: En preparación y Terminado.'
        : 'Usa los botones para cambiar el estado de cada pedido y llevar el control del carrito.';

    attachCartActions();
}

function attachCartActions() {
    document.querySelectorAll('.cart-actions button').forEach(button => {
        const action = button.dataset.action;
        const id = Number(button.dataset.id);

        if (action === 'remove') {
            button.addEventListener('click', () => removeCartItem(id));
        }
        if (action === 'increase') {
            button.addEventListener('click', () => changeCartQuantity(id, 1));
        }
        if (action === 'decrease') {
            button.addEventListener('click', () => changeCartQuantity(id, -1));
        }
        if (action === 'toggle') {
            button.addEventListener('click', () => toggleOrderStatus(id));
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    renderMenuSections();
    renderCart();
});