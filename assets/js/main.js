// assets/js/main.js
// Dùng cho các trang công khai (index.html, products.html, search.html...).
// Hàm quan trọng nhất: loadProducts() - tải danh sách sản phẩm từ
// api/products.php và vẽ phân trang có thể bấm được thật sự (trước đây hàm
// này chưa hề được viết -> gọi loadProducts(...) trong index.html bị lỗi
// "loadProducts is not defined", nút phân trang chỉ là HTML/JS trống).

const CONDITION_LABEL = {
    new: "Mới",
    like_new: "Như mới",
    good: "Tốt",
    fair: "Khá"
};

/** Escape HTML để tránh lỗi hiển thị / XSS khi render tên sản phẩm, địa điểm... */
function escapeHtml(str) {
    const div = document.createElement("div");
    div.textContent = str ?? "";
    return div.innerHTML;
}

function productImageSrc(image) {
    if (!image) return "assets/images/placeholder.png";
    return image.startsWith("uploads/") ? image : "uploads/" + image;
}

/**
 * Tải và hiển thị danh sách sản phẩm + phân trang.
 * @param {string} gridId        id của container lưới sản phẩm
 * @param {string|null} paginationId  id của container phân trang (bỏ qua nếu null)
 * @param {object} options       { limit, category_id, sort, page }
 */
function loadProducts(gridId, paginationId, options = {}) {
    const grid = document.getElementById(gridId);
    const paginationEl = paginationId ? document.getElementById(paginationId) : null;
    if (!grid) return;

    const state = {
        page: options.page || 1,
        limit: options.limit || 12,
        category_id: options.category_id || 0,
        sort: options.sort || "newest"
    };

    function fetchPage(page) {
        state.page = page;
        grid.innerHTML = '<div class="loading"><div class="spinner"></div><p>Đang tải sản phẩm...</p></div>';

        const params = new URLSearchParams({
            page: state.page,
            limit: state.limit,
            sort: state.sort
        });
        if (state.category_id > 0) params.set("category_id", state.category_id);

        fetch(`api/products.php?${params.toString()}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    grid.innerHTML = '<p class="empty-state">Không tải được sản phẩm, vui lòng thử lại.</p>';
                    if (paginationEl) paginationEl.innerHTML = "";
                    return;
                }
                renderGrid(data.data);
                if (paginationEl) renderPagination(data.pagination);
            })
            .catch(() => {
                grid.innerHTML = '<p class="empty-state">Có lỗi xảy ra, vui lòng thử lại.</p>';
                if (paginationEl) paginationEl.innerHTML = "";
            });
    }

    function renderGrid(products) {
        if (!products.length) {
            grid.innerHTML = '<p class="empty-state">Chưa có sản phẩm nào.</p>';
            return;
        }
        grid.innerHTML = products.map(p => `
            <a class="product-card" href="product-detail.html?id=${p.id}">
                <div class="product-card-img">
                    <img src="${productImageSrc(p.image)}" alt="${escapeHtml(p.title)}" loading="lazy">
                    ${p.condition_item ? `<span class="product-condition ${escapeHtml(p.condition_item)}">${CONDITION_LABEL[p.condition_item] || ""}</span>` : ""}
                </div>
                <div class="product-card-body">
                    <div class="product-card-title">${escapeHtml(p.title)}</div>
                    <div class="product-price">${escapeHtml(p.price_formatted)}</div>
                    <div class="product-meta">
                        <span>${escapeHtml(p.location || "")}</span>
                        <span>👁 ${p.views ?? 0}</span>
                    </div>
                </div>
            </a>
        `).join("");
    }

    function renderPagination(pg) {
        if (!paginationEl) return;
        const totalPages = pg.total_pages;
        const current = pg.page;

        if (!totalPages || totalPages <= 1) {
            paginationEl.innerHTML = "";
            return;
        }

        let html = "";
        html += `<a href="#" data-page="${current - 1}" class="${current === 1 ? "disabled" : ""}" aria-label="Trang trước">‹</a>`;

        for (let i = 1; i <= totalPages; i++) {
            const isEdge = i === 1 || i === totalPages;
            const isNearCurrent = Math.abs(i - current) <= 1;
            if (totalPages > 7 && !isEdge && !isNearCurrent) {
                if (i === 2 || i === totalPages - 1) {
                    html += `<span>…</span>`;
                }
                continue;
            }
            html += `<a href="#" data-page="${i}" class="${i === current ? "active" : ""}">${i}</a>`;
        }

        html += `<a href="#" data-page="${current + 1}" class="${current === totalPages ? "disabled" : ""}" aria-label="Trang sau">›</a>`;

        paginationEl.innerHTML = html;

        paginationEl.querySelectorAll("a[data-page]").forEach(a => {
            a.addEventListener("click", function (e) {
                e.preventDefault();
                if (this.classList.contains("disabled") || this.classList.contains("active")) return;
                const page = parseInt(this.dataset.page, 10);
                if (!page || page < 1 || page > totalPages) return;
                fetchPage(page);
                grid.scrollIntoView({ behavior: "smooth", block: "start" });
            });
        });
    }

    fetchPage(state.page);
}