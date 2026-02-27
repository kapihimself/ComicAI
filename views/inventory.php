<?php
/** @var array<int, array<string, mixed>> $products */

use Siappos\Shared\Csrf;

$title = 'Manajemen Stok & Inventori';
?>

<div class="grid">
    <div class="col-12">
        <div class="header" style="justify-content: start; gap: 20px; box-shadow: none; border-bottom: none; margin-bottom: 0;">
            <button class="btn btn-primary" onclick="switchTab('stok')">Daftar Stok</button>
            <button class="btn" onclick="switchTab('kulakan')">Kulakan (Masuk)</button>
            <button class="btn" onclick="switchTab('opname')">Stok Opname (Cek Fisik)</button>
        </div>
    </div>
</div>

<!-- TAB 1: DAFTAR STOK -->
<div id="tab-stok" class="tab-content">
    <div class="panel">
        <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
            <h3>Posisi Stok Terkini</h3>
            <input type="text" id="search-stock" placeholder="Cari nama barang..." style="width: 300px;">
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f6f7fa; border-bottom: 2px solid var(--line);">
                    <th style="padding: 10px; text-align: left;">SKU</th>
                    <th style="padding: 10px; text-align: left;">Nama Produk</th>
                    <th style="padding: 10px; text-align: center;">Stok Fisik</th>
                    <th style="padding: 10px; text-align: center;">Satuan</th>
                    <th style="padding: 10px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="stock-table-body">
                <?php foreach ($products as $p): ?>
                <tr class="stock-row" data-name="<?= strtolower($p['name']) ?>">
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;"><?= htmlspecialchars($p['sku']) ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd; font-weight: 600;"><?= htmlspecialchars($p['name']) ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center; font-size: 1.1em;">
                        <?= (float)$p['stock_qty'] ?>
                    </td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;"><?= htmlspecialchars($p['unit']) ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;">
                        <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 12px;" onclick="viewCard(<?= $p['id'] ?>, '<?= htmlspecialchars($p['name']) ?>')">Kartu Stok</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- TAB 2: KULAKAN (MASUK) -->
<div id="tab-kulakan" class="tab-content" style="display: none;">
    <div class="grid">
        <div class="col-6" style="margin: 0 auto;">
            <div class="panel panel-accent">
                <h3>Catat Barang Masuk (Kulakan)</h3>
                <form method="POST" action="/?page=inventory-adjust">
                    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                    <input type="hidden" name="reason" value="purchase">

                    <label>Pilih Produk</label>
                    <select name="product_id" required>
                        <option value="">-- Pilih Produk --</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (Sisa: <?= (float)$p['stock_qty'] ?>)</option>
                        <?php endforeach; ?>
                    </select>

                    <label>Jumlah Masuk</label>
                    <input type="number" name="qty_change" step="0.01" min="0.01" placeholder="Contoh: 10" required>

                    <label>Nomor Nota / Supplier (Opsional)</label>
                    <input type="text" name="reference_id" placeholder="Cth: INV-SUP-001">

                    <label>Catatan</label>
                    <textarea name="notes" rows="2" placeholder="Catatan tambahan..."></textarea>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Simpan Stok Masuk</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- TAB 3: OPNAME (CEK FISIK) -->
<div id="tab-opname" class="tab-content" style="display: none;">
    <div class="grid">
        <div class="col-6" style="margin: 0 auto;">
            <div class="panel" style="border-color: var(--danger);">
                <h3>Koreksi Stok (Opname/Barang Rusak)</h3>
                <p class="muted">Gunakan ini jika stok di sistem tidak sesuai dengan fisik, atau barang rusak/hilang.</p>

                <form method="POST" action="/?page=inventory-adjust">
                    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">

                    <label>Pilih Produk</label>
                    <select name="product_id" required>
                        <option value="">-- Pilih Produk --</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (Sisa: <?= (float)$p['stock_qty'] ?>)</option>
                        <?php endforeach; ?>
                    </select>

                    <label>Jenis Koreksi</label>
                    <div class="template-grid">
                        <label class="template-card" style="border-color: var(--danger);">
                            <input type="radio" name="reason" value="adjustment" required>
                            <strong>Selisih Hitung</strong><br>
                            <small>Stok fisik lebih sedikit/banyak dari sistem.</small>
                        </label>
                        <label class="template-card" style="border-color: var(--danger);">
                            <input type="radio" name="reason" value="waste">
                            <strong>Barang Rusak/Basi</strong><br>
                            <small>Barang harus dibuang.</small>
                        </label>
                    </div>

                    <label>Jumlah Pengurangan/Penambahan</label>
                    <div style="display: flex; gap: 10px;">
                        <select name="direction" style="width: 120px;" required>
                            <option value="-">Kurangi (-)</option>
                            <option value="+">Tambah (+)</option>
                        </select>
                        <input type="number" name="qty_change_abs" step="0.01" min="0.01" placeholder="Jumlah..." required>
                    </div>
                    <p class="muted" style="font-size: 12px; margin-top: -10px;">Contoh: Jika barang rusak 2 pcs, pilih 'Kurangi' dan isi 2.</p>

                    <label>Catatan</label>
                    <textarea name="notes" rows="2" placeholder="Kenapa selisih?..." required></textarea>

                    <button type="submit" class="btn btn-danger" style="width: 100%;">Simpan Koreksi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL KARTU STOK -->
<div id="modal-card" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 999; padding: 20px;">
    <div class="panel" style="max-width: 600px; margin: 50px auto; max-height: 80vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 id="card-title" style="margin: 0;">Kartu Stok</h3>
            <button class="btn" onclick="closeCard()">Tutup</button>
        </div>
        <table style="width: 100%; font-size: 13px;">
            <thead>
                <tr style="border-bottom: 2px solid var(--line);">
                    <th style="text-align: left; padding: 5px;">Tanggal</th>
                    <th style="text-align: left; padding: 5px;">Tipe</th>
                    <th style="text-align: right; padding: 5px;">Masuk/Keluar</th>
                    <th style="text-align: right; padding: 5px;">Sisa</th>
                    <th style="text-align: left; padding: 5px;">Ref/Ket</th>
                </tr>
            </thead>
            <tbody id="card-body">
                <!-- Loaded via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<script>
function switchTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + tabId).style.display = 'block';

    // Simple active state for buttons
    document.querySelectorAll('.header .btn').forEach(btn => {
        if(btn.textContent.toLowerCase().includes(tabId)) {
            btn.classList.add('btn-primary');
        } else {
            btn.classList.remove('btn-primary');
        }
    });
}

document.getElementById('search-stock').addEventListener('input', function(e) {
    var term = e.target.value.toLowerCase();
    document.querySelectorAll('.stock-row').forEach(row => {
        var name = row.getAttribute('data-name');
        row.style.display = name.includes(term) ? '' : 'none';
    });
});

function viewCard(productId, productName) {
    document.getElementById('card-title').textContent = 'Kartu Stok: ' + productName;
    document.getElementById('modal-card').style.display = 'block';
    document.getElementById('card-body').innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 20px;">Memuat data...</td></tr>';

    fetch('/?page=stock-card&product_id=' + productId)
        .then(res => res.json())
        .then(data => {
            var html = '';
            if(data.length === 0) {
                html = '<tr><td colspan="5" style="text-align:center; padding: 20px;">Belum ada riwayat.</td></tr>';
            } else {
                data.forEach(row => {
                    var color = row.qty_change > 0 ? 'color: green;' : 'color: red;';
                    var change = parseFloat(row.qty_change) > 0 ? '+' + parseFloat(row.qty_change) : parseFloat(row.qty_change);

                    html += `
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 6px;">${row.created_at.substring(0, 16)}</td>
                            <td style="padding: 6px;">${translateReason(row.reason)}</td>
                            <td style="padding: 6px; text-align: right; ${color}"><strong>${change}</strong></td>
                            <td style="padding: 6px; text-align: right;">${parseFloat(row.final_stock)}</td>
                            <td style="padding: 6px; font-size: 0.9em;">
                                ${row.reference_id ? '['+row.reference_id+'] ' : ''}
                                ${row.notes || '-'}
                            </td>
                        </tr>
                    `;
                });
            }
            document.getElementById('card-body').innerHTML = html;
        })
        .catch(err => {
            document.getElementById('card-body').innerHTML = '<tr><td colspan="5" style="text-align:center; color: red;">Gagal memuat data.</td></tr>';
        });
}

function translateReason(reason) {
    const map = {
        'sale': 'Penjualan',
        'purchase': 'Kulakan',
        'adjustment': 'Opname',
        'waste': 'Rusak/Basi',
        'return': 'Retur'
    };
    return map[reason] || reason;
}

function closeCard() {
    document.getElementById('modal-card').style.display = 'none';
}
</script>
