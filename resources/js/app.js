import 'bootstrap';
import Swiper from 'swiper/bundle';

// ── Scroll-aware header ───────────────────────────────────
window.addEventListener('scroll', () => {
    const header = document.querySelector('.site-header');
    if (header) {
        header.classList.toggle('scrolled', window.scrollY > 20);
    }
});

// ── Fade-up animations (IntersectionObserver) ─────────────
const observer = new IntersectionObserver(
    (entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                entry.target.style.setProperty('--i', entry.target.dataset.i ?? i);
                entry.target.classList.add('visible');
            }
        });
    },
    { threshold: 0.1, rootMargin: '0px 0px -40px 0px' }
);

document.querySelectorAll('.fade-up').forEach((el, i) => {
    el.dataset.i = i;
    observer.observe(el);
});

// ── Hero carousel (Swiper) ────────────────────────────────
const heroSwiper = document.querySelector('.hero-swiper');
if (heroSwiper) {
    new Swiper('.hero-swiper', {
        loop: true,
        autoplay: { delay: 5000, disableOnInteraction: false },
        effect: 'fade',
        fadeEffect: { crossFade: true },
        pagination: { el: '.swiper-pagination', clickable: true },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
    });
}

// ── Product card carousel rails ───────────────────────────
document.querySelectorAll('.product-swiper').forEach(el => {
    new Swiper(el, {
        slidesPerView: 1.2,
        spaceBetween: 16,
        breakpoints: {
            576:  { slidesPerView: 2.2 },
            768:  { slidesPerView: 3.2 },
            992:  { slidesPerView: 4 },
            1200: { slidesPerView: 4 },
        },
        navigation: {
            nextEl: el.closest('section')?.querySelector('.swiper-next') ?? '.swiper-button-next',
            prevEl: el.closest('section')?.querySelector('.swiper-prev') ?? '.swiper-button-prev',
        },
    });
});

// ── Fitment Finder AJAX ───────────────────────────────────
const fitmentMake  = document.getElementById('fitment-make');
const fitmentModel = document.getElementById('fitment-model');
const fitmentYear  = document.getElementById('fitment-year');
const fitmentBtn   = document.getElementById('fitment-submit');

async function fetchJSON(url) {
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    return res.json();
}

function populateSelect(select, items, valueKey = null, labelKey = null, placeholder = '— Select —') {
    select.innerHTML = `<option value="">${placeholder}</option>`;
    items.forEach(item => {
        const val   = valueKey  ? item[valueKey]  : item;
        const label = labelKey  ? item[labelKey]  : item;
        const opt   = document.createElement('option');
        opt.value   = val;
        opt.textContent = label;
        select.appendChild(opt);
    });
    select.disabled = false;
}

if (fitmentMake) {
    fitmentMake.addEventListener('change', async () => {
        const make = fitmentMake.value;
        if (!make) return;
        fitmentModel.innerHTML = '<option>Loading...</option>';
        fitmentModel.disabled = true;
        fitmentYear.innerHTML = '<option value="">— Select Year —</option>';
        fitmentYear.disabled = true;
        if (fitmentBtn) fitmentBtn.disabled = true;

        const models = await fetchJSON(`/api/fitment/models?make=${encodeURIComponent(make)}`);
        populateSelect(fitmentModel, models, null, null, '— Select Model —');
    });

    if (fitmentModel) {
        fitmentModel.addEventListener('change', async () => {
            const make  = fitmentMake.value;
            const model = fitmentModel.value;
            if (!make || !model) return;
            fitmentYear.innerHTML = '<option>Loading...</option>';
            fitmentYear.disabled = true;
            if (fitmentBtn) fitmentBtn.disabled = true;

            const years = await fetchJSON(`/api/fitment/years?make=${encodeURIComponent(make)}&model=${encodeURIComponent(model)}`);
            populateSelect(fitmentYear, years, 'year', 'year', '— Select Year —');
        });
    }

    if (fitmentYear) {
        fitmentYear.addEventListener('change', () => {
            if (fitmentBtn) {
                fitmentBtn.disabled = !fitmentYear.value;
            }
        });
    }

    if (fitmentBtn) {
        fitmentBtn.addEventListener('click', async () => {
            const selectedOption = fitmentYear.options[fitmentYear.selectedIndex];
            const modelId = selectedOption?.dataset?.modelId;
            if (!modelId) return;

            const url = `/api/fitment/set?make=${encodeURIComponent(fitmentMake.value)}&model=${encodeURIComponent(fitmentModel.value)}&year=${encodeURIComponent(fitmentYear.value)}&model_id=${modelId}`;
            const result = await fetchJSON(url);

            if (result.success) {
                document.querySelectorAll('.fitment-chip-label').forEach(el => el.textContent = result.label);
                document.querySelectorAll('.fitment-chip-wrap').forEach(el => el.classList.remove('d-none'));
                // Optionally reload to show filtered results
                if (window.location.pathname.startsWith('/shop')) {
                    window.location.reload();
                }
            }
        });
    }
}

// ── Clear fitment ─────────────────────────────────────────
document.querySelectorAll('.fitment-clear-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        await fetch('/api/fitment/clear');
        document.querySelectorAll('.fitment-chip-wrap').forEach(el => el.classList.add('d-none'));
        if (window.location.pathname.startsWith('/shop')) {
            window.location.href = window.location.pathname;
        }
    });
});

// ── Cart: Add to Cart AJAX ────────────────────────────────
document.querySelectorAll('.ajax-add-to-cart').forEach(form => {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = form.querySelector('[type="submit"]');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Adding...';
        btn.disabled = true;

        try {
            const res = await fetch('/cart/add', {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
            });
            const data = await res.json();

            // Update cart badge
            document.querySelectorAll('.cart-badge').forEach(badge => {
                badge.textContent = data.cart_count;
                badge.classList.remove('d-none');
            });

            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Added!';
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            }, 2000);
        } catch {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }
    });
});

// ── Qty stepper ───────────────────────────────────────────
document.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = btn.closest('.qty-control').querySelector('.qty-input');
        if (!input) return;
        const current = parseInt(input.value) || 1;
        const delta   = btn.dataset.action === 'plus' ? 1 : -1;
        const newVal  = Math.max(1, Math.min(99, current + delta));
        input.value = newVal;
        // Trigger change for hidden inputs
        input.dispatchEvent(new Event('change', { bubbles: true }));
    });
});

// ── Color/Variant swatch selection ────────────────────────
document.querySelectorAll('.color-swatch').forEach(swatch => {
    swatch.addEventListener('click', () => {
        const group = swatch.closest('.color-swatch-wrap');
        group?.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
        swatch.classList.add('active');

        // Update hidden variant_id
        const hiddenInput = document.getElementById('selected-variant-id');
        if (hiddenInput && swatch.dataset.variantId) {
            hiddenInput.value = swatch.dataset.variantId;
        }

        // Update price display
        const price = swatch.dataset.price;
        const salePrice = swatch.dataset.salePrice;
        const priceEl = document.querySelector('.selected-price');
        if (priceEl && price) {
            priceEl.innerHTML = salePrice
                ? `<span class="product-price">₱${parseFloat(salePrice).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                   <span class="product-price-original ms-2">₱${parseFloat(price).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>`
                : `<span class="product-price">₱${parseFloat(price).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>`;
        }

        // Update stock status
        const stock = parseInt(swatch.dataset.stock ?? '0');
        const stockEl = document.querySelector('.stock-status');
        if (stockEl) {
            if (stock <= 0) {
                stockEl.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Out of Stock</span>';
            } else if (stock <= 10) {
                stockEl.innerHTML = `<span class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Low Stock (${stock} left)</span>`;
            } else {
                stockEl.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i>In Stock</span>';
            }
        }
    });
});

// ── Admin sidebar toggle (mobile) ─────────────────────────
const adminToggle  = document.getElementById('admin-sidebar-toggle');
const adminSidebar = document.querySelector('.admin-sidebar');
if (adminToggle && adminSidebar) {
    adminToggle.addEventListener('click', () => {
        adminSidebar.classList.toggle('open');
    });
}

// ── Toast / alert auto-dismiss ────────────────────────────
document.querySelectorAll('.auto-dismiss').forEach(el => {
    setTimeout(() => el.classList.add('fade'), 3500);
    setTimeout(() => el.remove(), 4000);
});
