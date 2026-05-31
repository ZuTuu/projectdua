@extends('layouts.app')

@section('title', 'POS Kasir')

@section('content')
<div class="pos-layout">
    <div class="pos-section">
        <h3>🧾 Produk</h3>

        <form method="GET" action="{{ route('admin.pos.index') }}" style="margin-bottom: 14px;">
            <div style="display:flex; gap:10px; align-items:center;">
                <input type="text" name="search" value="{{ $query }}" placeholder="Cari produk / kategori..." style="flex:1; padding: 12px 14px; border: 1px solid var(--purple-200); border-radius: 10px; outline: none;" />
                <button class="btn-pos" style="background: var(--purple-800); color:#fff; padding: 12px 16px;" type="submit">Cari</button>
            </div>
        </form>

        <div class="product-grid">
            @forelse($produks as $p)
                <div class="product-card">
                    <div class="product-name">{{ $p->nama_produk }}</div>
                    <div class="product-meta">Kategori: {{ $p->kategori ?? '-' }}</div>
                    <div class="product-meta">Harga: Rp {{ number_format($p->harga, 0, ',', '.') }}</div>

                    <input type="hidden" class="product-id" value="{{ $p->id }}">
                    <button type="button" class="btn-pos btn-add" onclick="addItem({{ $p->id }}, '{{ addslashes($p->nama_produk) }}', {{ (float)$p->harga }})">+ Tambah</button>
                </div>
            @empty
                <div class="empty-state">Produk tidak ditemukan.</div>
            @endforelse
        </div>
    </div>

    <div class="pos-section">
        <h3>🧺 Keranjang & Pembayaran</h3>

        @if(session('success'))
            <div style="background:#d4edda; border-left:5px solid #28a745; padding: 12px 14px; border-radius: 10px; color:#155724; font-weight:800; margin-bottom: 12px;">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background:#f8d7da; border-left:5px solid #dc3545; padding: 12px 14px; border-radius: 10px; color:#721c24; font-weight:800; margin-bottom: 12px;">
                ❌ {{ session('error') }}
            </div>
        @endif
        @error('items')
            <div style="background:#f8d7da; border-left:5px solid #dc3545; padding: 12px 14px; border-radius: 10px; color:#721c24; font-weight:800; margin-bottom: 12px;">{{ $message }}</div>
        @enderror

        <div style="max-height: 360px; overflow:auto; border: 1px solid var(--purple-50); border-radius: 12px;">
            <table class="pos-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th style="text-align:right;">Qty</th>
                        <th style="text-align:right;">Subtotal</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="cart-body">
                </tbody>
            </table>
            <div id="cart-empty" class="empty-state" style="display:block; border:none;" >Keranjang kosong. Tambahkan produk dari sisi kiri.</div>
        </div>

        <form method="POST" action="{{ route('admin.pos.checkout') }}" enctype="multipart/form-data" id="pos-form" style="margin-top: 14px;">
            @csrf

            <input type="hidden" name="payment_method" id="payment_method" value="cash">
            <input type="hidden" name="items_json" id="items-json" value="[]">

            <div style="margin-bottom: 12px;">
                <div style="font-weight: 900; color: var(--purple-900); margin-bottom: 6px;">Metode bayar</div>
                <div style="display:flex; gap:10px; flex-direction: column;">
                    <label style="display:flex; gap:10px; align-items:center;">
                        <input type="radio" name="payment_method_radio" value="cash" checked onclick="setPayment('cash')"> 💵 Tunai / COD
                    </label>
                    <label style="display:flex; gap:10px; align-items:center;">
                        <input type="radio" name="payment_method_radio" value="transfer" onclick="setPayment('transfer')"> 💳 Transfer / QRIS
                    </label>
                </div>
            </div>

            <div id="transfer-area" style="display:none; margin-bottom: 12px;">
                <div style="font-weight: 900; color: var(--purple-900); margin-bottom: 6px;">Upload bukti pembayaran (opsional)</div>
                <input type="file" name="bukti_bayar" accept="image/*" style="width:100%; padding: 10px; border:1px solid var(--purple-200); border-radius: 10px;" />
            </div>

            <div class="summary-row"><span>Total</span><span class="total-price" id="total-display">Rp 0</span></div>

            <button type="submit" class="btn-pos-primary" id="checkout-btn" disabled style="opacity:0.6;">Buat Transaksi</button>
        </form>

        <button type="button" class="btn-pos-secondary" style="margin-top: 10px;" onclick="clearCart()">Kosongkan</button>
    </div>
</div>

<script>
    const cart = new Map(); // produk_id => {produk_id,nama,harga,qty}

    function formatIDR(n) {
        return new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', maximumFractionDigits:0 }).format(n);
    }

    function setPayment(method) {
        document.getElementById('payment_method').value = method;
        const transferArea = document.getElementById('transfer-area');
        transferArea.style.display = method === 'transfer' ? 'block' : 'none';
    }

    function addItem(produkId, nama, harga) {
        if (cart.has(produkId)) {
            cart.get(produkId).qty += 1;
        } else {
            cart.set(produkId, { produk_id: produkId, nama: nama, harga: harga, qty: 1 });
        }
        renderCart();
    }

    function changeQty(produkId, delta) {
        if (!cart.has(produkId)) return;
        const row = cart.get(produkId);
        row.qty += delta;
        if (row.qty <= 0) cart.delete(produkId);
        renderCart();
    }

    function clearCart() {
        cart.clear();
        renderCart();
    }

    function renderCart() {
        const tbody = document.getElementById('cart-body');
        tbody.innerHTML = '';

        const empty = document.getElementById('cart-empty');
        const checkoutBtn = document.getElementById('checkout-btn');

        const itemsArr = [];
        let total = 0;

        cart.forEach((row, key) => {
            const subtotal = row.harga * row.qty;
            total += subtotal;

            itemsArr.push({ produk_id: row.produk_id, qty: row.qty });

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${row.nama}</td>
                <td style="text-align:right;">
                    <div class="qty-controls">
                        <button type="button" class="pos-qty-btn" onclick="changeQty(${row.produk_id}, -1)">-</button>
                        <span style="min-width:28px; text-align:center; font-weight:900;">${row.qty}</span>
                        <button type="button" class="pos-qty-btn" onclick="changeQty(${row.produk_id}, 1)">+</button>
                    </div>
                </td>
                <td style="text-align:right; font-weight:900; color:var(--purple-800);">${formatIDR(subtotal)}</td>
                <td style="text-align:right;">
                    <button type="button" class="pos-qty-btn" onclick="changeQty(${row.produk_id}, -999)">🗑️</button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        document.getElementById('items-json').value = JSON.stringify(itemsArr);
        document.getElementById('total-display').innerText = formatIDR(total);

        if (cart.size === 0) {
            empty.style.display = 'block';
            checkoutBtn.disabled = true;
            checkoutBtn.style.opacity = 0.6;
        } else {
            empty.style.display = 'none';
            checkoutBtn.disabled = false;
            checkoutBtn.style.opacity = 1;
        }
    }

    // init
    renderCart();
    setPayment('cash');

    document.getElementById('pos-form').addEventListener('submit', function(e) {
        if (cart.size === 0) {
            e.preventDefault();
            alert('Keranjang masih kosong.');
            return;
        }

        const btn = document.getElementById('checkout-btn');
        btn.disabled = true;
        btn.innerText = 'Memproses...';
        btn.style.opacity = 0.6;
    });
</script>
@endsection