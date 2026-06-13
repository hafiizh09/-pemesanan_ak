let audioEnabled = false;
let lastOrderCount = 0;
let knownOrderIds = new Set();

const formatIDR = (num) => new Intl.NumberFormat('id-ID').format(num);

/**
 * Sanitasi string agar aman dirender ke innerHTML
 * Mencegah XSS dari data yang berasal dari user input
 */
const escapeHtml = (str) => {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
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
        setTimeout(() => {
            container.classList.remove('show');
        }, 3000);
    });
};

window.enableAudio = () => {
    audioEnabled = true;
    const btn = document.getElementById('enable-audio');
    btn.innerHTML = '<i data-lucide="volume-2" class="w-3.5 h-3.5"></i> <span class="hidden sm:inline">Audio Active</span>';
    btn.classList.remove('border-gray-200', 'text-gray-600', 'hover:border-brand-600', 'hover:text-brand-600');
    btn.classList.add('bg-emerald-50', 'text-emerald-600', 'border-emerald-200');
    // Mainkan audio kosong sekalian untuk unlock autoplay policy
    document.getElementById('alert-sound').play().catch(e => console.log('Audio init failed', e));
    lucide.createIcons();
};

function playAlert() {
    if (audioEnabled) {
        if (window.NOTIFICATION_AUDIO_URL) {
            const audio = document.getElementById('alert-sound');
            if (audio) {
                audio.currentTime = 0;
                audio.play().catch(e => console.log('Custom audio play failed', e));
            }
        } else {
            // Fallback beep ringan via Web Audio API.
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gainNode = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, ctx.currentTime); // A5
            gainNode.gain.setValueAtTime(0.1, ctx.currentTime);
            osc.connect(gainNode);
            gainNode.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.1);
        }
    }
}

let lastOrdersDataString = "";

async function fetchOrders() {
    try {
        const res = await fetch('../api/get_orders.php');
        const data = await res.json();
        
        if (data.success) {
            const newDataString = JSON.stringify(data.data);
            
            // Only re-render if the data has actually changed
            if (newDataString !== lastOrdersDataString) {
                renderOrders(data.data);
                lastOrdersDataString = newDataString;
            }
            
            // Cek pesanan baru untuk notifikasi
            const currentOrderIds = new Set(data.data.map(o => o.id));
            let hasNew = false;
            
            for (let id of currentOrderIds) {
                if (!knownOrderIds.has(id)) {
                    hasNew = true;
                    knownOrderIds.add(id);
                }
            }
            
            if (hasNew) playAlert();
        }
    } catch (e) {
        console.error("Gagal mengambil data pesanan:", e);
    } finally {
        setTimeout(fetchOrders, 3000); // Polling setiap 3 detik
    }
}

function renderOrders(orders) {
    const container = document.getElementById('orders-container');
    
    if (orders.length === 0) {
        container.innerHTML = `
            <div class="col-span-full flex flex-col items-center justify-center text-neutral-400 h-64">
                <i data-lucide="inbox" class="w-12 h-12 mb-4 opacity-50"></i>
                <p class="text-sm font-medium">Listening for new orders...</p>
            </div>
        `;
        lucide.createIcons();
        return;
    }

    container.innerHTML = orders.map(order => {
        const time = new Date(order.waktu_pesan).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        
        // Items HTML
        const itemsHtml = order.items.map(i => `
            <div class="flex justify-between items-start text-sm border-b border-gray-100 py-3 last:border-0">
                <div class="flex-1 pr-4">
                    <span class="font-bold text-brand-600 bg-brand-50 px-1.5 py-0.5 rounded-md mr-1">${i.qty}x</span> 
                    <span class="font-medium text-gray-900">${i.nama_menu}</span>
                    ${i.catatan ? `<p class="text-xs text-gray-500 mt-1.5 p-2 bg-yellow-50/50 rounded-lg border border-yellow-100 text-balance flex items-start gap-1"><i data-lucide="message-square" class="w-3 h-3 mt-0.5 text-yellow-600 shrink-0"></i> <span>${escapeHtml(i.catatan)}</span></p>` : ''}
                </div>
            </div>
        `).join('');

        // Action buttons HTML
        let actionHtml = '';
        if (order.status_pesanan === 'pending') {
            actionHtml = `
                <button onclick="updateStatus(${order.id}, 'proses')" class="flex-1 bg-brand-600 text-white py-3.5 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-brand-700 transition-snappy shadow-lg shadow-brand-500/30 flex justify-center items-center gap-2"><i data-lucide="chef-hat" class="w-4 h-4"></i> Proses</button>
                <button onclick="updateStatus(${order.id}, 'tolak')" class="flex-1 bg-white text-red-600 border border-red-200 py-3.5 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-red-50 transition-snappy flex justify-center items-center gap-2"><i data-lucide="x" class="w-4 h-4"></i> Tolak</button>
            `;
        } else if (order.status_pesanan === 'diproses') {
            if (order.status_bayar === 'unpaid' && order.metode_bayar === 'cash') {
                actionHtml = `
                    <button onclick="showToast('Selesaikan konfirmasi pembayaran CASH terlebih dahulu', 'error')" class="w-full bg-gray-200 text-gray-500 py-4 rounded-xl text-sm font-bold uppercase tracking-widest cursor-not-allowed flex justify-center items-center gap-2"><i data-lucide="check-circle" class="w-5 h-5"></i> Tandai Selesai</button>
                `;
            } else {
                actionHtml = `
                    <button onclick="updateStatus(${order.id}, 'selesai')" class="w-full bg-emerald-500 text-white py-4 rounded-xl text-sm font-bold uppercase tracking-widest hover:bg-emerald-600 transition-snappy shadow-lg shadow-emerald-500/30 flex justify-center items-center gap-2"><i data-lucide="check-circle" class="w-5 h-5"></i> Tandai Selesai</button>
                `;
            }
        }

        // Payment status badge
        const payBadge = order.status_bayar === 'paid' 
            ? `<span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-lg flex items-center gap-1"><i data-lucide="check" class="w-3 h-3"></i> LUNAS</span>`
            : `<span class="bg-red-50 text-red-600 text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-lg flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3"></i> BELUM BAYAR</span>`;
            
        // Payment Button
        let payAction = '';
        if (order.status_bayar === 'unpaid') {
            let proofHtml = '';
            if (order.metode_bayar === 'qris') {
                if (order.bukti_transfer) {
                    proofHtml = `<button onclick="viewTransferProof('${order.bukti_transfer}')" class="mt-2 w-full py-2 bg-blue-50 border border-blue-200 rounded-xl text-xs font-bold text-blue-600 hover:bg-blue-100 transition-colors flex justify-center items-center gap-2"><i data-lucide="image" class="w-4 h-4"></i> Lihat Bukti Transfer</button>`;
                } else {
                    proofHtml = `<div class="mt-2 text-center text-[11px] text-red-500 font-bold bg-red-50 border border-red-100 rounded-xl py-2">Belum Unggah Bukti</div>`;
                }
            }
            payAction = `
                ${proofHtml}
                <button onclick="updateStatus(${order.id}, 'bayar')" class="mt-4 w-full py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:border-brand-500 hover:text-brand-600 hover:bg-brand-50 transition-colors flex justify-center items-center gap-2"><i data-lucide="wallet" class="w-4 h-4"></i> Konfirmasi Pembayaran (${order.metode_bayar.toUpperCase()})</button>
            `;
        }

        const statusLabel = order.status_pesanan === 'pending'
            ? `<div class="absolute -top-3 -right-3 bg-brand-500 shadow-lg shadow-brand-500/40 w-10 h-10 rounded-full flex items-center justify-center animate-bounce"><i data-lucide="bell" class="w-5 h-5 text-white"></i></div>`
            : '';

        let kembalianHtml = '';
        if (order.metode_bayar === 'cash' && order.uang_dibayar !== null) {
            const uangDibayar = parseFloat(order.uang_dibayar);
            const totalHarga = parseFloat(order.total_harga);
            const kembalian = uangDibayar - totalHarga;
            
            if (kembalian === 0) {
                kembalianHtml = `
                    <div class="flex justify-between items-center mt-2 mb-5 px-4 py-2 border border-brand-100 rounded-xl">
                        <span class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Dibayar (CASH)</span>
                        <span class="text-[11px] font-bold text-brand-600 bg-brand-50 px-2 py-1 rounded-md">UANG PAS</span>
                    </div>
                `;
            } else {
                kembalianHtml = `
                    <div class="flex justify-between items-center mt-2 mb-5 px-4 py-2 border border-gray-100 rounded-xl bg-gray-50/50">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Dibayar (CASH)</span>
                            <span class="text-xs font-bold text-gray-700 tabular-nums">Rp ${formatIDR(uangDibayar)}</span>
                        </div>
                        <div class="flex flex-col text-right">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kembalian</span>
                            <span class="text-sm font-bold text-brand-600 tabular-nums">Rp ${formatIDR(kembalian)}</span>
                        </div>
                    </div>
                `;
            }
        } else if (order.metode_bayar === 'cash') {
            kembalianHtml = `<div class="mt-2 mb-5"></div>`;
        }

        return `
            <div class="bg-white border border-gray-100 p-6 rounded-[24px] flex flex-col relative transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                ${statusLabel}
                <div class="flex justify-between items-start mb-5">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-2xl font-bold tracking-tight text-gray-900 leading-none">Meja ${order.nomor_meja}</h3>
                            ${order.status_pesanan === 'pending' ? '<span class="bg-red-500 text-white text-[9px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-md animate-pulse">Pesanan Baru</span>' : ''}
                        </div>
                        <p class="text-[11px] font-medium text-gray-500 mt-2">Order #${order.id} • ${time}</p>
                    </div>
                    ${payBadge}
                </div>
                
                <div class="flex-1 bg-gray-50/50 p-4 rounded-2xl border border-gray-100 mb-6">
                    ${itemsHtml}
                </div>
                
                <div class="mt-auto">
                    <div class="flex justify-between items-center ${kembalianHtml ? '' : 'mb-5'} bg-brand-50 px-4 py-3 rounded-xl border border-brand-100">
                        <span class="text-sm font-bold text-brand-600 uppercase tracking-widest">Total Tagihan</span>
                        <span class="text-lg font-bold text-brand-700 tabular-nums">Rp ${formatIDR(order.total_harga)}</span>
                    </div>
                    ${kembalianHtml}
                    
                    <div class="flex gap-3">
                        ${actionHtml}
                    </div>
                    ${payAction}
                </div>
            </div>
        `;
    }).join('');
    
    lucide.createIcons();
}

window.updateStatus = async (orderId, action) => {
    try {
        const res = await fetch('../api/update_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: orderId, action: action })
        });
        const data = await res.json();
        if (data.success) {
            // Optimistic update dengan trigger re-fetch
            fetchOrders();
        } else {
            showToast(data.message || 'Gagal mengubah status');
        }
    } catch (e) {
        showToast("Terjadi kesalahan jaringan.");
    }
}

// Mulai polling saat halaman dimuat
fetchOrders();

window.viewTransferProof = (imageUrl) => {
    let modal = document.getElementById('proof-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'proof-modal';
        modal.className = 'fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm';
        modal.innerHTML = `
            <div class="bg-white rounded-3xl p-6 max-w-sm w-full mx-4 shadow-2xl relative">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-gray-900 text-lg">Bukti Transfer</h3>
                    <button onclick="closeProofModal()" class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-500 rounded-full hover:bg-gray-200 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                <div class="bg-gray-50 p-2 border border-gray-100 rounded-2xl flex items-center justify-center max-h-[60vh] overflow-hidden">
                    <img id="proof-modal-img" src="" alt="Bukti Transfer" class="max-w-full max-h-[50vh] object-contain rounded-xl">
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    // Null/undefined check sebelum assign src
    document.getElementById('proof-modal-img').src = imageUrl ? '../' + imageUrl : '';
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    lucide.createIcons();
};

window.closeProofModal = () => {
    const modal = document.getElementById('proof-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
};
