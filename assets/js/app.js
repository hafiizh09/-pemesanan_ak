const formatIDR = (num) => new Intl.NumberFormat('id-ID').format(num);

// Haptic feedback helper
const vibrate = (pattern) => {
    if (navigator.vibrate) {
        try { navigator.vibrate(pattern); } catch(e) {}
    }
};

window.showToast = (message, type = 'error') => {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    
    const icon = type === 'success' 
        ? `<svg class="w-5 h-5 toast-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>`
        : `<svg class="w-5 h-5 toast-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        
    container.innerHTML = `
        <div class="toast-content toast-${type}">
            ${icon}
            <span>${message}</span>
        </div>
    `;
    
    requestAnimationFrame(() => {
        container.classList.add('show');
        vibrate(type === 'error' ? [50, 50, 50] : [50]);
        setTimeout(() => {
            container.classList.remove('show');
        }, 3000);
    });
};

let cart = [];
let mejaId = document.body.dataset.mejaId;

let isDraggingCategory = false;
let catStartX, catStartY;
const catScroll = document.getElementById('category-scroll');
if (catScroll) {
    catScroll.addEventListener('touchstart', e => {
        isDraggingCategory = false;
        catStartX = e.touches[0].clientX;
        catStartY = e.touches[0].clientY;
    }, {passive: true});

    catScroll.addEventListener('touchmove', e => {
        let moveX = e.touches[0].clientX;
        let moveY = e.touches[0].clientY;
        if (Math.abs(moveX - catStartX) > 10 || Math.abs(moveY - catStartY) > 10) {
            isDraggingCategory = true;
        }
    }, {passive: true});
}

// Filter Categories
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        if (isDraggingCategory) {
            e.preventDefault();
            e.stopPropagation();
            return;
        }
        vibrate(10); // Light tap
        
        // Active state style
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('bg-brand-600/90', 'text-white', 'shadow-md', 'shadow-brand-500/20', 'active', 'border-transparent');
            b.classList.add('bg-white/50', 'border', 'border-white/60', 'text-gray-700', 'shadow-sm');
        });
        
        const target = e.currentTarget;
        target.classList.remove('bg-white/50', 'border', 'border-white/60', 'text-gray-700', 'shadow-sm');
        target.classList.add('bg-brand-600/90', 'text-white', 'shadow-md', 'shadow-brand-500/20', 'active', 'border-transparent');

        const cat = target.dataset.cat;
        
        // Animate filtering smoothly by fading the whole container
        const grid = document.getElementById('menu-grid');
        const items = document.querySelectorAll('.menu-item');
        
        if (grid) {
            grid.style.opacity = '0';
            
            setTimeout(() => {
                items.forEach(item => {
                    // Reset any old inline styles first
                    item.style.transition = 'none';
                    item.style.opacity = '1';
                    item.style.transform = 'none';
                    
                    if (cat === 'all' || item.dataset.cat === cat) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Force reflow and fade back in
                grid.offsetHeight;
                grid.style.opacity = '1';
            }, 300); // Wait for grid fade out
        }
    });
});

// Swipe Hint Overlay Logic
const swipeHint = document.getElementById('swipe-hint');

if (swipeHint) {
    if (!localStorage.getItem('swipe_hint_seen')) {
        swipeHint.classList.remove('hidden');
        // Trigger reflow
        swipeHint.offsetHeight;
        swipeHint.classList.remove('opacity-0');

        const hideHint = () => {
            if (swipeHint.style.opacity === '0' || swipeHint.classList.contains('opacity-0')) return;
            swipeHint.style.opacity = '0';
            swipeHint.classList.add('opacity-0');
            setTimeout(() => {
                if (swipeHint.parentNode) {
                    swipeHint.parentNode.removeChild(swipeHint);
                }
            }, 500);
            document.removeEventListener('touchstart', hideHint, true);
            document.removeEventListener('click', hideHint, true);
            localStorage.setItem('swipe_hint_seen', 'true');
        };

        // Hide when user touches or clicks anywhere on the screen
        document.addEventListener('touchstart', hideHint, { once: true, capture: true });
        document.addEventListener('click', hideHint, { once: true, capture: true });

        // Auto hide after exactly 3 seconds if no interaction
        setTimeout(hideHint, 3000);
    } else {
        // Already seen, remove from DOM completely
        swipeHint.remove();
    }
}

// Add to Cart
window.addToCart = (id, name, price) => {
    vibrate([20, 30, 20]); // Haptic bounce
    
    const existing = cart.find(i => i.id === id);
    if (existing) {
        existing.qty += 1;
    } else {
        cart.push({ id, name, price, qty: 1, note: '' });
    }
    updateCartUI();
    
    // Animation on floater
    const floater = document.getElementById('cart-floater');
    const btn = floater.querySelector('button');
    btn.classList.add('scale-[1.04]');
    setTimeout(() => btn.classList.remove('scale-[1.04]'), 150);
};

window.updateQty = (id, delta) => {
    vibrate(15);
    const item = cart.find(i => i.id === id);
    if (item) {
        item.qty += delta;
        if (item.qty <= 0) {
            cart = cart.filter(i => i.id !== id);
        }
        updateCartUI();
    }
};

window.updateNote = (id, note) => {
    const item = cart.find(i => i.id === id);
    if (item) {
        item.note = note;
    }
};

function updateCartUI() {
    const floater = document.getElementById('cart-floater');
    const countEl = document.getElementById('cart-count');
    const totalEl = document.getElementById('cart-total');
    const cartItems = document.getElementById('cart-items');

    const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
    const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);

    // Update Floater
    const activeBanner = document.getElementById('active-orders-banner');
    if (totalQty > 0) {
        floater.style.transform = 'translateY(0)';
        floater.style.opacity = '1';
        if (activeBanner) {
            activeBanner.classList.remove('bottom-[85px]', 'scale-100');
            activeBanner.classList.add('bottom-[160px]', 'scale-[0.96]');
        }
    } else {
        floater.style.transform = 'translateY(150%)';
        floater.style.opacity = '0';
        if (activeBanner) {
            activeBanner.classList.remove('bottom-[160px]', 'scale-[0.96]');
            activeBanner.classList.add('bottom-[85px]', 'scale-100');
        }
        if (document.getElementById('cart-sheet').classList.contains('open')) {
            toggleCart();
        }
    }
    
    countEl.textContent = totalQty;
    totalEl.textContent = `Rp ${formatIDR(totalPrice)}`;
    
    const sheetTotalEl = document.getElementById('sheet-total');
    if (sheetTotalEl) {
        sheetTotalEl.textContent = `Rp ${formatIDR(totalPrice)}`;
    }

    const uangPasCheckbox = document.getElementById('uang-pas-checkbox');
    const uangDibayarInput = document.getElementById('uang-dibayar');
    if (uangPasCheckbox && uangPasCheckbox.checked && uangDibayarInput) {
        uangDibayarInput.value = totalPrice;
    }

    // Render Cart Sheet Items
    if (cart.length === 0) {
        cartItems.innerHTML = `
            <div class="flex flex-col items-center justify-center h-40 text-gray-400">
                <i data-lucide="shopping-bag" class="w-10 h-10 mb-3 opacity-50"></i>
                <p class="text-[13px] font-medium">Keranjang masih kosong</p>
            </div>`;
        lucide.createIcons();
        return;
    }

    cartItems.innerHTML = cart.map(item => `
        <div class="flex flex-col gap-3 py-4 border-b border-gray-100 last:border-0">
            <div class="flex justify-between items-start">
                <div class="flex-1 pr-4">
                    <h4 class="font-bold text-[15px] text-gray-900 leading-snug mb-1">${item.name}</h4>
                    <span class="text-[13px] font-medium text-brand-600 tabular-nums">Rp ${formatIDR(item.price)}</span>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="updateQty(${item.id}, -${item.qty})" class="w-8 h-8 flex items-center justify-center text-red-500 hover:text-red-600 hover:bg-red-50 rounded-full transition-colors" title="Hapus pesanan">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                    <div class="flex items-center gap-3 bg-gray-50 border border-gray-100 rounded-full px-1.5 py-1.5">
                        <button onclick="updateQty(${item.id}, -1)" class="w-7 h-7 flex items-center justify-center text-gray-500 hover:text-black bg-white rounded-full shadow-sm transition-colors font-bold">-</button>
                        <span class="text-[13px] font-bold w-5 text-center tabular-nums text-gray-900">${item.qty}</span>
                        <button onclick="updateQty(${item.id}, 1)" class="w-7 h-7 flex items-center justify-center text-gray-500 hover:text-black bg-white rounded-full shadow-sm transition-colors font-bold">+</button>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="edit-3" class="w-3.5 h-3.5 text-gray-400"></i>
                </div>
                <input type="text" placeholder="Catatan (opsional, mis. es sedikit)" class="w-full text-[13px] bg-gray-50 text-gray-700 rounded-xl py-2 pl-9 pr-3 outline-none focus:ring-1 focus:ring-brand-500 transition-shadow" value="${item.note}" onchange="updateNote(${item.id}, this.value)">
            </div>
        </div>
    `).join('');
    
    lucide.createIcons();
}

window.toggleCart = () => {
    const sheet = document.getElementById('cart-sheet');
    const overlay = document.getElementById('cart-overlay');
    
    // Jika keranjang kosong, jangan izinkan membuka, TAPI izinkan menutup jika sedang terbuka
    if (cart.length === 0 && !sheet.classList.contains('open')) return;
    
    vibrate(10);
    
    sheet.classList.toggle('open');
    overlay.classList.toggle('open');
    
    document.body.style.overflow = sheet.classList.contains('open') ? 'hidden' : '';
};

window.submitOrder = async () => {
    if (cart.length === 0) return;
    vibrate([20, 20]);
    
    // Blokir pemesanan ganda DIHAPUS agar pelanggan bisa memesan berkali-kali
    
    const paymentMethod = document.getElementById('payment-method').value;
    
    let uangDibayar = null;
    if (paymentMethod === 'cash') {
        const inputUang = document.getElementById('uang-dibayar').value;
        uangDibayar = parseInt(inputUang);
        const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        
        if (isNaN(uangDibayar) || uangDibayar < totalPrice) {
            showToast('Nominal uang tidak valid atau kurang dari total pesanan.');
            return;
        }
    }

    const payload = {
        meja_id: mejaId,
        metode_bayar: paymentMethod,
        uang_dibayar: uangDibayar,
        items: cart.map(i => ({ menu_id: i.id, qty: i.qty, catatan: i.note }))
    };

    const btn = document.querySelector('#cart-sheet button[onclick="submitOrder()"]');
    const originalText = btn.textContent;
    btn.innerHTML = '<span class="flex items-center justify-center gap-2"><svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...</span>';
    btn.disabled = true;

    try {
        const res = await fetch('api/create_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        
        if (data.success) {
            vibrate([50, 100, 50]);
            
            // Simpan ke array active_orders
            let orders = [];
            const activeOrdersStr = localStorage.getItem('active_orders');
            if (activeOrdersStr) {
                try { orders = JSON.parse(activeOrdersStr); } catch(e){}
            }
            // Migrasi format lama
            const oldOrder = localStorage.getItem('active_order_id');
            if (oldOrder && !orders.includes(parseInt(oldOrder))) {
                orders.push(parseInt(oldOrder));
                localStorage.removeItem('active_order_id');
            }
            
            if (!orders.includes(data.order_id)) {
                orders.push(data.order_id);
            }
            localStorage.setItem('active_orders', JSON.stringify(orders));

            if (paymentMethod === 'qris') {
                window.location.href = `customer/qris_payment.php?id=${data.order_id}`;
            } else {
                window.location.href = `customer/order_status.php?id=${data.order_id}`;
            }
        } else {
            showToast(data.message || 'Gagal memproses pesanan.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    } catch (e) {
        showToast('Terjadi kesalahan sistem. Coba lagi.');
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
};

// --- Real-time Stock Management ---
let knownMenuStatus = {};

async function pollMenuStatus() {
    try {
        const res = await fetch('api/get_menu_status.php');
        const data = await res.json();
        
        if (data.success) {
            let cartModified = false;
            
            data.data.forEach(menu => {
                const id = parseInt(menu.id);
                const status = menu.status;
                const prevStatus = knownMenuStatus[id];
                
                if (prevStatus && prevStatus !== status) {
                    const actionDiv = document.getElementById(`menu-action-${id}`);
                    if (actionDiv) {
                        if (status === 'habis') {
                            actionDiv.innerHTML = `<span class="text-[10px] font-bold uppercase tracking-widest text-red-500 bg-red-50 border border-red-100 px-2 py-1.5 rounded-lg">Habis</span>`;
                            if (cart.find(i => i.id === id)) {
                                cart = cart.filter(i => i.id !== id);
                                cartModified = true;
                            }
                        } else {
                            window.location.reload();
                        }
                    }
                }
                
                knownMenuStatus[id] = status;
            });
            
            if (cartModified) {
                updateCartUI();
                showToast("Beberapa item yang habis telah dihapus dari keranjang Anda.");
            }
        }
    } catch (e) {
        console.error("Stock poll error", e);
    } finally {
        setTimeout(pollMenuStatus, 5000);
    }
}

fetch('api/get_menu_status.php').then(res => res.json()).then(data => {
    if (data.success) {
        data.data.forEach(m => knownMenuStatus[parseInt(m.id)] = m.status);
    }
    setTimeout(pollMenuStatus, 5000);
}).catch(e => console.error("Initial stock load error", e));

// --- Touch Swipe Navigation ---
let touchStartX = 0;
let touchEndX = 0;
let touchStartY = 0;
let touchEndY = 0;

const menuGrid = document.getElementById('menu-grid');
if (menuGrid) {
    menuGrid.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
        touchStartY = e.changedTouches[0].screenY;
    }, {passive: true});

    menuGrid.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
        touchEndY = e.changedTouches[0].screenY;
        handleSwipe();
    }, {passive: true});
}

function handleSwipe(e) {
    if (document.getElementById('cart-sheet').classList.contains('open')) return;
    
    // Calculate distance
    const diffX = touchEndX - touchStartX;
    const diffY = touchEndY - touchStartY;
    
    // Only trigger if horizontal swipe is significantly larger than vertical scroll
    if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
        if (diffX < 0) {
            // Swiped Left
            switchCategory(1);
        } else {
            // Swiped Right
            switchCategory(-1);
        }
    }
}

function switchCategory(direction) {
    const buttons = Array.from(document.querySelectorAll('.filter-btn'));
    const activeIndex = buttons.findIndex(btn => btn.classList.contains('active'));
    
    if (activeIndex === -1) return;
    
    let newIndex = activeIndex + direction;
    
    // Jangan berputar ke awal/akhir jika sudah di ujung
    if (newIndex >= buttons.length || newIndex < 0) return;
    
    const targetBtn = buttons[newIndex];
    if (targetBtn) {
        targetBtn.click();
        targetBtn.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }
}

let activeOrdersPollTimer;

async function checkAndUpdateActiveOrders() {
    let activeOrders = [];
    
    // Migrate old format if exists
    const oldOrder = localStorage.getItem('active_order_id');
    if (oldOrder) {
        activeOrders.push(parseInt(oldOrder));
        localStorage.removeItem('active_order_id');
    }

    const activeOrdersStr = localStorage.getItem('active_orders');
    if (activeOrdersStr) {
        try { 
            const parsed = JSON.parse(activeOrdersStr); 
            if (Array.isArray(parsed)) activeOrders = activeOrders.concat(parsed);
        } catch(e){}
    }
    
    // Unique
    activeOrders = [...new Set(activeOrders)];
    
    if (activeOrders.length === 0) {
        const banner = document.getElementById('active-orders-banner');
        if (banner) {
            banner.classList.add('translate-y-[150%]', 'opacity-0');
            setTimeout(() => {
                if (banner.parentNode) banner.remove();
                const overlay = document.getElementById('active-orders-overlay');
                const sheet = document.getElementById('active-orders-sheet');
                if(overlay && overlay.parentNode) overlay.remove();
                if(sheet && sheet.parentNode) sheet.remove();
            }, 500);
        }
        return;
    }

    let stillActiveOrders = [];
    let orderDetailsList = [];
    
    // Fetch status for all active orders
    await Promise.all(activeOrders.map(async (id) => {
        try {
            const res = await fetch(`api/get_order_status.php?order_id=${id}`);
            const data = await res.json();
            if (data.success) {
                const status = data.data.status_pesanan;
                if (status === 'pending' || status === 'diproses') {
                    stillActiveOrders.push(id);
                    orderDetailsList.push({ 
                        id: id, 
                        summary: data.data.items_summary || 'Menunggu Konfirmasi',
                        itemsList: data.data.items || null
                    });
                }
            }
        } catch(e) {}
    }));
    
    if (stillActiveOrders.length > 0) {
        localStorage.setItem('active_orders', JSON.stringify(stillActiveOrders));
        
        let banner = document.getElementById('active-orders-banner');
        if (!banner) {
            // First time showing
            const btnHtml = `
                <div class="fixed left-5 right-5 z-40 bg-brand-600 text-white p-4 rounded-2xl shadow-[0_10px_30px_rgba(234,88,12,0.3)] flex justify-between items-center transition-all duration-500 origin-bottom transform translate-y-[150%] opacity-0 bottom-[85px] scale-100" id="active-orders-banner">
                    <div class="flex items-center gap-3">
                        <div class="bg-white/20 p-2 rounded-xl flex items-center justify-center relative">
                            <i data-lucide="shopping-bag" class="w-5 h-5 text-white"></i>
                            <span id="active-orders-count-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-brand-600">${stillActiveOrders.length}</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-brand-100 mb-0.5">Pesanan Anda</p>
                            <p id="active-orders-count-text" class="text-xs font-bold text-white">${stillActiveOrders.length} Pesanan Aktif</p>
                        </div>
                    </div>
                    <button onclick="toggleActiveOrders()" class="px-4 py-2 bg-white text-brand-600 font-bold text-xs rounded-xl shadow-sm hover:bg-brand-50 transition-colors uppercase tracking-widest flex items-center gap-2 cursor-pointer relative z-50">
                        Lihat <i data-lucide="list" class="w-3 h-3"></i>
                    </button>
                </div>
            `;
            
            window.toggleActiveOrders = () => {
                const sheet = document.getElementById('active-orders-sheet');
                const overlay = document.getElementById('active-orders-overlay');
                if (!sheet || !overlay) return;
                
                sheet.classList.toggle('open');
                overlay.classList.toggle('open');
                document.body.style.overflow = sheet.classList.contains('open') ? 'hidden' : '';
            };

            const modalHtml = `
                <div id="active-orders-overlay" class="bottom-sheet-overlay" style="z-index: 90;" onclick="toggleActiveOrders()"></div>
                <div id="active-orders-sheet" class="bottom-sheet flex flex-col max-h-[85vh]" style="z-index: 100;">
                    <div class="drag-handle"></div>
                    <div class="flex justify-between items-center p-6 border-b border-gray-100 shrink-0">
                        <h3 class="text-xl font-bold tracking-tight text-gray-900">Pesanan Aktif</h3>
                        <button onclick="toggleActiveOrders()" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 text-gray-500 hover:bg-gray-100 transition-colors">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <div class="overflow-y-auto shrink p-6" id="active-orders-modal-items">
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', btnHtml + modalHtml);
            banner = document.getElementById('active-orders-banner');
            
            setTimeout(() => {
                if(banner) {
                    banner.classList.remove('translate-y-[150%]', 'opacity-0');
                    if (cart.reduce((sum, item) => sum + item.qty, 0) > 0) {
                        banner.classList.remove('bottom-[85px]', 'scale-100');
                        banner.classList.add('bottom-[160px]', 'scale-[0.96]');
                    }
                }
            }, 100);
        } else {
            // Update existing elements
            const badge = document.getElementById('active-orders-count-badge');
            if (badge) badge.innerText = stillActiveOrders.length;
            const textEl = document.getElementById('active-orders-count-text');
            if (textEl) textEl.innerText = stillActiveOrders.length + ' Pesanan Aktif';
        }
        
        // Update list items
        const modalItemsHtml = orderDetailsList.map(order => `
            <div class="bg-gray-50 rounded-2xl border border-gray-100 mb-3 overflow-hidden shadow-sm">
                <div class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-100 transition-colors" onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.arrow-icon').classList.toggle('rotate-180');">
                    <div class="flex-1 pr-3 overflow-hidden">
                        <p class="text-sm font-bold text-gray-900">Pesanan #${order.id}</p>
                    </div>
                    <div class="w-8 h-8 bg-white rounded-full shadow-sm flex items-center justify-center shrink-0 transition-transform duration-300 arrow-icon">
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                    </div>
                </div>
                <div class="hidden border-t border-gray-100 px-4 py-3 bg-white">
                    <div class="text-xs text-gray-700 mb-3 font-medium space-y-1.5">
                        ${order.itemsList ? order.itemsList.map(i => `<div class="flex justify-between"><span>${i.qty}x ${i.name}</span></div>`).join('') : order.summary}
                    </div>
                    <a href="customer/order_status.php?id=${order.id}" class="block text-center w-full py-2.5 bg-brand-50 text-brand-600 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-brand-100 transition-colors border border-brand-100">
                        Cek Status &raquo;
                    </a>
                </div>
            </div>
        `).join('');
        const modalContainer = document.getElementById('active-orders-modal-items');
        if (modalContainer) {
            const currentOrdersHash = orderDetailsList.map(o => o.id).join(',');
            if (modalContainer.dataset.ordersHash !== currentOrdersHash) {
                modalContainer.innerHTML = modalItemsHtml;
                modalContainer.dataset.ordersHash = currentOrdersHash;
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        }
    } else {
        localStorage.removeItem('active_orders');
        const banner = document.getElementById('active-orders-banner');
        if (banner) {
            banner.classList.add('translate-y-[150%]', 'opacity-0');
            setTimeout(() => {
                if (banner.parentNode) banner.remove();
                const overlay = document.getElementById('active-orders-overlay');
                const sheet = document.getElementById('active-orders-sheet');
                if(overlay && overlay.parentNode) overlay.remove();
                if(sheet && sheet.parentNode) sheet.remove();
            }, 500);
        }
    }
    
    // Poll every 5 seconds
    clearTimeout(activeOrdersPollTimer);
    activeOrdersPollTimer = setTimeout(checkAndUpdateActiveOrders, 5000);
}

document.addEventListener('DOMContentLoaded', () => {
    checkAndUpdateActiveOrders();
    
    const paymentMethodSelect = document.getElementById('payment-method');
    const cashInputContainer = document.getElementById('cash-input-container');
    const uangPasCheckbox = document.getElementById('uang-pas-checkbox');
    const uangDibayarInput = document.getElementById('uang-dibayar');
    
    if (paymentMethodSelect && cashInputContainer) {
        paymentMethodSelect.addEventListener('change', (e) => {
            if (e.target.value === 'cash') {
                cashInputContainer.style.display = 'block';
                // Trigger reflow
                cashInputContainer.offsetHeight;
                cashInputContainer.style.opacity = '1';
            } else {
                cashInputContainer.style.opacity = '0';
                setTimeout(() => cashInputContainer.style.display = 'none', 300);
            }
        });
    }

    if (uangPasCheckbox && uangDibayarInput) {
        uangPasCheckbox.addEventListener('change', (e) => {
            if (e.target.checked) {
                const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                uangDibayarInput.value = totalPrice;
                uangDibayarInput.readOnly = true;
                uangDibayarInput.classList.add('bg-gray-100', 'text-gray-500');
            } else {
                uangDibayarInput.value = '';
                uangDibayarInput.readOnly = false;
                uangDibayarInput.classList.remove('bg-gray-100', 'text-gray-500');
            }
        });
    }

    const track = document.getElementById('promo-track');
    const slider = document.getElementById('promo-slider');
    if (!track || !slider) return;
    
    const originalSlides = Array.from(track.children);
    const totalSlides = originalSlides.length;
    if (totalSlides <= 1) return;
    
    // Clone slide pertama dan taruh di akhir untuk efek seamless
    const firstClone = originalSlides[0].cloneNode(true);
    firstClone.setAttribute('data-clone', 'true');
    track.appendChild(firstClone);
    
    let currentIndex = 0;
    let autoSlideTimer;
    let isUserInteracting = false;
    let isTransitioning = false;
    
    const dots = document.querySelectorAll('#promo-dots button');
    
    function updateDots(index) {
        // Jika index sudah di clone (terakhir), tampilkan dot pertama
        const dotIndex = index >= totalSlides ? 0 : index;
        dots.forEach((dot, i) => {
            if (i === dotIndex) {
                dot.classList.remove('bg-white/50', 'w-2');
                dot.classList.add('bg-white', 'w-5');
            } else {
                dot.classList.remove('bg-white', 'w-5');
                dot.classList.add('bg-white/50', 'w-2');
            }
        });
    }
    
    function goToSlide(index, animate = true) {
        if (isTransitioning && animate) return;
        
        if (animate) {
            track.style.transition = 'transform 500ms ease-in-out';
        } else {
            track.style.transition = 'none';
        }
        
        track.style.transform = `translateX(-${index * 100}%)`;
        currentIndex = index;
        updateDots(index);
        
        if (animate) isTransitioning = true;
    }
    
    // Ketika transisi selesai, cek apakah kita di clone slide
    track.addEventListener('transitionend', () => {
        isTransitioning = false;
        
        // Jika sudah di clone (slide pertama yang di-duplikat di akhir),
        // langsung lompat ke slide pertama asli TANPA animasi
        if (currentIndex >= totalSlides) {
            goToSlide(0, false);
        }
    });
    
    function nextSlide() {
        goToSlide(currentIndex + 1, true);
    }
    
    function startAutoSlide() {
        clearInterval(autoSlideTimer);
        autoSlideTimer = setInterval(() => {
            if (!isUserInteracting) {
                nextSlide();
            }
        }, 3000);
    }
    
    function stopAutoSlide() {
        clearInterval(autoSlideTimer);
    }
    
    // Swipe support
    let touchStartX = 0;
    let touchDiff = 0;
    
    slider.addEventListener('touchstart', (e) => {
        isUserInteracting = true;
        stopAutoSlide();
        touchStartX = e.changedTouches[0].clientX;
    }, {passive: true});
    
    slider.addEventListener('touchend', (e) => {
        touchDiff = e.changedTouches[0].clientX - touchStartX;
        
        if (Math.abs(touchDiff) > 50) {
            if (touchDiff < 0) {
                // Swipe kiri = next
                nextSlide();
            } else {
                // Swipe kanan = prev
                if (currentIndex > 0) {
                    goToSlide(currentIndex - 1, true);
                }
            }
        }
        
        isUserInteracting = false;
        startAutoSlide();
    }, {passive: true});
    
    // Klik dot
    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            const target = parseInt(dot.dataset.dot);
            goToSlide(target, true);
            stopAutoSlide();
            startAutoSlide();
        });
    });
    
    // Mouse hover pause (desktop)
    slider.addEventListener('mouseenter', () => { isUserInteracting = true; stopAutoSlide(); });
    slider.addEventListener('mouseleave', () => { isUserInteracting = false; startAutoSlide(); });
    
    // Mulai!
    startAutoSlide();
});
