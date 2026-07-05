// assets/js/main.js - Xử lý gọi API và render sản phẩm

// ===== HELPER: Build query string từ object =====
function buildQuery(params) {
    return Object.entries(params)
        .filter(([, v]) => v !== undefined && v !== null && v !== '')
        .map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`)
        .join('&');
}

// ===== RENDER PRODUCT CARD =====
function renderProductCard(p) {
    const conditionMap = {
        new:      { label: 'Mới',     cls: 'badge-new' },
        like_new: { label: 'Như mới', cls: 'badge-like-new' },
        good:     { label: 'Còn tốt', cls: 'badge-good' },
        fair:     { label: 'Khá cũ',  cls: 'badge-fair' },
    };
    const cond = conditionMap[p.condition_item] || { label: p.condition_item, cls: '' };

    const imgHTML = p.image
        ? `<img class="product-img" src="${escHtml(p.image)}" alt="${escHtml(p.title)}" loading="lazy">`
        : `<div class="product-img-placeholder">📦</div>`;

    return `
    <div class="product-card" onclick="window.location='product-detail.html?id=${p.id}'">
        ${imgHTML}
        <div class="product-info">
            <div class="product-title">${escHtml(p.title)}</div>
            <div class="product-price">${p.price_formatted}</div>
            <div class="product-meta">
                <span class="badge ${cond.cls}">${cond.label}</span>
                ${p.category_name ? `<span class="badge" style="background:#f1f5f9;color:#475569;">${escHtml(p.category_name)}</span>` : ''}
            </div>
            ${p.location ? `<div class="product-location">📍 ${escHtml(p.location)}</div>` : ''}
        </div>
    </div>`;
}

// ===== RENDER PAGINATION =====
function renderPagination(paginationId, current, totalPages, onPageChange) {
    const el = document.getElementById(paginationId);
    if (!el || totalPages <= 1) { if (el) el.innerHTML = ''; return; }

    let html = '';

    if (current > 1)
        html += `<button class="page-btn" onclick="(${onPageChange})(${current - 1})">‹</button>`;

    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || Math.abs(i - current) <= 2) {
            html += `<button class="page-btn ${i === current ? 'active' : ''}" 
                             onclick="(${onPageChange})(${i})">${i}</button>`;
        } else if (Math.abs(i - current) === 3) {
            html += `<span style="display:flex;align-items:center;padding:0 4px;color:var(--text-muted);">…</span>`;
        }
    }

    if (current < totalPages)
        html += `<button class="page-btn" onclick="(${onPageChange})(${current + 1})">›</button>`;

    el.innerHTML = html;
}

// ===== LOAD SẢN PHẨM (danh sách / trang chủ) =====
function loadProducts(gridId, paginationId, params = {}, page = 1) {
    const grid = document.getElementById(gridId);
    if (!grid) return;

    grid.innerHTML = '<div class="loading"><div class="spinner"></div><p>Đang tải...</p></div>';

    const query = buildQuery({ ...params, page });

    fetch(`api/products.php?${query}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success || data.data.length === 0) {
                grid.innerHTML = `
                    <div class="empty" style="grid-column:1/-1;">
                        <div class="empty-icon">📭</div>
                        <p>Không có sản phẩm nào.</p>
                    </div>`;
                if (paginationId) document.getElementById(paginationId).innerHTML = '';
                return;
            }

            grid.innerHTML = data.data.map(renderProductCard).join('');

            if (paginationId) {
                const pg = data.pagination;
                renderPagination(
                    paginationId,
                    pg.page,
                    pg.total_pages,
                    `function(p){ loadProducts('${gridId}','${paginationId}',${JSON.stringify(params)},p); window.scrollTo(0,0); }`
                );
            }
        })
        .catch(() => {
            grid.innerHTML = `
                <div class="empty" style="grid-column:1/-1;">
                    <div class="empty-icon">⚠️</div>
                    <p>Không thể tải sản phẩm. Vui lòng thử lại.</p>
                </div>`;
        });
}

// ===== TÌM KIẾM SẢN PHẨM =====
function searchProducts(gridId, paginationId, countId, params = {}, page = 1) {
    const grid  = document.getElementById(gridId);
    const count = document.getElementById(countId);
    if (!grid) return;

    grid.innerHTML = '<div class="loading"><div class="spinner"></div><p>Đang tìm kiếm...</p></div>';

    const query = buildQuery({ ...params, page });

    fetch(`api/search.php?${query}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                grid.innerHTML = `<div class="empty" style="grid-column:1/-1;"><div class="empty-icon">🔍</div><p>${escHtml(data.error || 'Lỗi tìm kiếm')}</p></div>`;
                if (count) count.innerHTML = '';
                return;
            }

            const total = data.pagination.total;
            if (count) {
                count.innerHTML = total > 0
                    ? `Tìm thấy <strong>${total}</strong> kết quả cho "<strong>${escHtml(data.keyword)}</strong>"`
                    : `Không tìm thấy kết quả nào cho "<strong>${escHtml(data.keyword)}</strong>"`;
            }

            if (data.data.length === 0) {
                grid.innerHTML = `
                    <div class="empty" style="grid-column:1/-1;">
                        <div class="empty-icon">😕</div>
                        <p>Không có sản phẩm phù hợp. Thử từ khóa khác nhé!</p>
                    </div>`;
                if (paginationId) document.getElementById(paginationId).innerHTML = '';
                return;
            }

            grid.innerHTML = data.data.map(renderProductCard).join('');

            if (paginationId) {
                const pg = data.pagination;
                renderPagination(
                    paginationId,
                    pg.page,
                    pg.total_pages,
                    `function(p){ searchProducts('${gridId}','${paginationId}','${countId}',${JSON.stringify(params)},p); window.scrollTo(0,0); }`
                );
            }
        })
        .catch(() => {
            grid.innerHTML = `<div class="empty" style="grid-column:1/-1;"><div class="empty-icon">⚠️</div><p>Lỗi kết nối. Vui lòng thử lại.</p></div>`;
        });
}

// ===== UTILITY: Escape HTML =====
function escHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
