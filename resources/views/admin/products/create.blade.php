@extends('layouts.admin')
@section('title', 'Add New Product — Shopee Seller Style')
@section('page-title', 'Add New Product')

@section('content')
<style>
  .shopee-section-card {
    background: var(--mb-card);
    border: 1px solid var(--mb-border);
    border-radius: 12px;
    padding: 1.75rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.15);
  }
  .shopee-section-title {
    font-family: 'Rajdhani', sans-serif;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--mb-gold);
    border-bottom: 1px solid var(--mb-border);
    padding-bottom: 0.75rem;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .shopee-nav-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1rem;
    color: var(--mb-muted);
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.2s ease;
  }
  .shopee-nav-link:hover, .shopee-nav-link.active {
    background: var(--mb-gold-dim);
    color: var(--mb-gold);
  }
  .batch-edit-bar {
    background: var(--mb-surface);
    border: 1px solid var(--mb-gold-border);
    border-radius: 8px;
    padding: 0.85rem 1.25rem;
    margin-bottom: 1rem;
  }
  .tag-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--mb-surface);
    border: 1px solid var(--mb-gold);
    color: var(--mb-gold);
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
  }
</style>

<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" id="shopee-product-form">
    @csrf

    <div class="row g-4">
        {{-- Left Sticky Section Quick Navigation Bar --}}
        <div class="col-lg-3 d-none d-lg-block">
            <div class="dark-card p-3 sticky-top" style="top:90px;z-index:10;">
                <div class="text-gold font-bold mb-3 px-2" style="font-family:'Rajdhani',sans-serif;font-size:1.05rem;">
                    <i class="bi bi-list-task me-1"></i> Product Sections
                </div>
                <nav class="d-flex flex-column gap-1">
                    <a href="#section-basic" class="shopee-nav-link active"><i class="bi bi-info-circle"></i> 1. Basic Information</a>
                    <a href="#section-media" class="shopee-nav-link"><i class="bi bi-images"></i> 2. Media Management</a>
                    <a href="#section-variations" class="shopee-nav-link"><i class="bi bi-diagram-3"></i> 3. Sales &amp; Variations</a>
                    <a href="#section-shipping" class="shopee-nav-link"><i class="bi bi-truck"></i> 4. Shipping &amp; Logistics</a>
                    <a href="#section-others" class="shopee-nav-link"><i class="bi bi-gear"></i> 5. Settings</a>
                </nav>
            </div>
        </div>

        {{-- Main Shopee Seller Centre Form Content --}}
        <div class="col-lg-9">

            {{-- ── 1. Basic Information ──────────────────────── --}}
            <div class="shopee-section-card" id="section-basic">
                <div class="shopee-section-title">
                    <i class="bi bi-info-circle-fill text-gold"></i> 1. Basic Information
                </div>

                <div class="mb-3">
                    <label class="form-label font-bold">Product Name *</label>
                    <input type="text" name="name" id="product_name" class="form-control" placeholder="e.g. RAI Titanium Disc Rotor Bolts Kit (5x25/CNC/4pcs)" maxlength="200" required>
                    <div class="d-flex justify-content-between mt-1" style="font-size:.78rem;color:var(--mb-muted);">
                        <span>Use descriptive keywords (Brand + Model + Size + Spec + Pack Qty)</span>
                        <span id="name-counter">0 / 200</span>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label font-bold">Category *</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">— Select Category —</option>
                            @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->parent_id ? '↳ ' : '' }}{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold">Brand</label>
                        <select name="brand_id" class="form-select">
                            <option value="">— No Brand / Generic —</option>
                            @foreach($brands as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label font-bold">Short Highlight Description</label>
                    <input type="text" name="short_description" class="form-control" placeholder="e.g. CNC-machined Grade 5 Titanium bolt set with anodized finish." maxlength="500">
                </div>

                <div class="mb-3">
                    <label class="form-label font-bold">Detailed Product Description</label>
                    <textarea name="description" class="form-control" rows="5" placeholder="Include fitment compatibility, thread size specs, torque recommendation, and pack info..."></textarea>
                </div>
            </div>

            {{-- ── 2. Media Management ──────────────────────── --}}
            <div class="shopee-section-card" id="section-media">
                <div class="shopee-section-title">
                    <i class="bi bi-images text-gold"></i> 2. Media Management (Photos)
                </div>
                
                <div class="p-3 mb-3" style="background:var(--mb-surface);border:1px dashed var(--mb-gold);border-radius:8px;">
                    <label class="form-label font-bold mb-1"><i class="bi bi-cloud-arrow-up text-gold me-1"></i> Upload Multiple Product Photos</label>
                    <p style="font-size:.82rem;color:var(--mb-muted);" class="mb-2">Select 1 to 9 high-resolution images from your device. First photo will be used as the Cover Image.</p>
                    <input type="file" name="image_files[]" id="image_files_input" class="form-control" multiple accept="image/*">
                </div>

                <div class="row g-2 mb-2" id="photo-preview-container">
                    {{-- Dynamically populated previews --}}
                </div>

                <div class="mb-3">
                    <label class="form-label font-bold small">Primary Cover Image URL (Optional)</label>
                    <input type="text" name="image_url" class="form-control form-control-sm" placeholder="https://example.com/cover.jpg">
                </div>
            </div>

            {{-- ── 3. Sales Information & Variations Matrix ── --}}
            <div class="shopee-section-card" id="section-variations">
                <div class="shopee-section-title justify-content-between">
                    <span><i class="bi bi-diagram-3-fill text-gold"></i> 3. Sales &amp; Variations (Shopee Style)</span>
                    <div class="form-check form-switch mb-0 fs-6">
                        <input class="form-check-input" type="checkbox" id="enable-variations-toggle" checked>
                        <label class="form-check-label font-normal text-white" for="enable-variations-toggle">Enable Variations</label>
                    </div>
                </div>

                {{-- Base Price & Single Stock fallback --}}
                <div id="single-pricing-section" class="d-none">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-bold">Standard Base Price (₱) *</label>
                            <input type="number" step="0.01" name="base_price" id="base_price_input" class="form-control" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-bold">Initial Stock Quantity *</label>
                            <input type="number" name="initial_stock" class="form-control" value="50">
                        </div>
                    </div>
                </div>

                {{-- 2-Tier Variations Generator --}}
                <div id="variations-builder-section">
                    <p style="font-size:.85rem;color:var(--mb-muted);" class="mb-3">
                        Define variation options like <strong>Size/Design/Qty</strong> (e.g. <code>5x25/CNC/4pcs</code>) and <strong>Color/Storage</strong> (e.g. <code>Gold</code>, <code>Titanium</code>, <code>8GB+256GB</code>).
                    </p>

                    {{-- Variation 1 Group --}}
                    <div class="p-3 mb-3" style="background:var(--mb-surface);border:1px solid var(--mb-border);border-radius:8px;">
                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-md-4">
                                <label class="form-label font-bold mb-0">Variation 1 Name</label>
                                <input type="text" id="v1-name" class="form-control form-control-sm" value="SIZE/DESIGN/QTY">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label font-bold mb-0">Variation Options (Comma Separated)</label>
                                <input type="text" id="v1-options" class="form-control form-control-sm" value="5x25/CNC/4pcs, 5x25/CON/4pcs, 5x20/HEXA/4pcs">
                            </div>
                        </div>
                    </div>

                    {{-- Variation 2 Group (Optional) --}}
                    <div class="p-3 mb-3" style="background:var(--mb-surface);border:1px solid var(--mb-border);border-radius:8px;">
                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-md-4">
                                <label class="form-label font-bold mb-0">Variation 2 Name (Optional)</label>
                                <input type="text" id="v2-name" class="form-control form-control-sm" value="Color / Finish" placeholder="e.g. Color, Finish, Material">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label font-bold mb-0">Variation 2 Options (Comma Separated)</label>
                                <input type="text" id="v2-options" class="form-control form-control-sm" value="Gold, Titanium, Black" placeholder="e.g. Gold, Titanium, Silver">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" class="btn btn-outline-gold btn-sm" id="btn-generate-matrix">
                            <i class="bi bi-gear-wide-connected me-1"></i> Generate Variation Matrix Table
                        </button>
                    </div>

                    {{-- Shopee Batch Setting Tool --}}
                    <div class="batch-edit-bar">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-3">
                                <span class="fw-bold text-gold" style="font-size:.9rem;"><i class="bi bi-magic me-1"></i> Batch Apply To All:</span>
                            </div>
                            <div class="col-md-3">
                                <input type="number" step="0.01" id="batch-price" class="form-control form-control-sm" placeholder="Price (₱) e.g. 250">
                            </div>
                            <div class="col-md-3">
                                <input type="number" id="batch-stock" class="form-control form-control-sm" placeholder="Stock Qty e.g. 50">
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-gold btn-sm w-100" id="btn-apply-batch">
                                    Apply To All Rows
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Variations Matrix Combination Table --}}
                    <div class="table-responsive">
                        <table class="table table-dark-custom mb-0" id="shopee-matrix-table">
                            <thead>
                                <tr>
                                    <th style="min-width:180px;">Variant Combination Name *</th>
                                    <th style="width:130px;">Price (₱) *</th>
                                    <th style="width:110px;">Stock *</th>
                                    <th style="width:140px;">SKU</th>
                                    <th style="width:50px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="shopee-matrix-rows">
                                {{-- Generated dynamically --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ── 4. Shipping & Logistics ────────────────────── --}}
            <div class="shopee-section-card" id="section-shipping">
                <div class="shopee-section-title">
                    <i class="bi bi-truck text-gold"></i> 4. Shipping &amp; Logistics
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label font-bold">Parcel Weight (grams) *</label>
                        <input type="number" name="weight_grams" class="form-control" value="150" placeholder="e.g. 150" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold">Shipping Courier Options</label>
                        <div class="p-2" style="background:var(--mb-surface);border-radius:6px;font-size:.85rem;color:var(--mb-text);">
                            <i class="bi bi-check2-square text-success me-1"></i> J&amp;T Express / Ninja Van Nationwide (₱89.00 flat rate)<br>
                            <i class="bi bi-check2-square text-success me-1"></i> Lalamove Express Same-Day Metro Manila (8 AM - 4 PM)
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── 5. Settings & Save ────────────────────────── --}}
            <div class="shopee-section-card" id="section-others">
                <div class="shopee-section-title">
                    <i class="bi bi-gear-fill text-gold"></i> 5. Product Status &amp; Settings
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label font-bold">Publishing Status</label>
                        <select name="status" class="form-select">
                            <option value="active" selected>Active &amp; Published</option>
                            <option value="draft">Draft (Hidden)</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end gap-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured">
                            <label class="form-check-label font-bold text-white" for="is_featured">Featured Product</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_new_arrival" value="1" id="is_new_arrival" checked>
                            <label class="form-check-label font-bold text-white" for="is_new_arrival">New Arrival</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 pt-2">
                    <button type="submit" class="btn btn-gold btn-lg px-5">
                        <i class="bi bi-cloud-check me-1"></i> Save &amp; Publish Product
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-dark-surface btn-lg">Cancel</a>
                </div>
            </div>

        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character Counter
    const nameInput = document.getElementById('product_name');
    const nameCounter = document.getElementById('name-counter');
    if (nameInput) {
        nameInput.addEventListener('input', () => {
            nameCounter.textContent = `${nameInput.value.length} / 200`;
        });
    }

    // Variations Matrix Generator
    const tbody = document.getElementById('shopee-matrix-rows');
    const btnGenerate = document.getElementById('btn-generate-matrix');
    const btnApplyBatch = document.getElementById('btn-apply-batch');

    function buildMatrix() {
        const v1Opts = document.getElementById('v1-options').value.split(',').map(s => s.trim()).filter(Boolean);
        const v2Opts = document.getElementById('v2-options').value.split(',').map(s => s.trim()).filter(Boolean);

        tbody.innerHTML = '';
        let rowIdx = 0;

        if (v1Opts.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-3">Please enter variation options above.</td></tr>`;
            return;
        }

        const combinations = [];
        if (v2Opts.length > 0) {
            v1Opts.forEach(o1 => {
                v2Opts.forEach(o2 => {
                    combinations.push(`${o1} - ${o2}`);
                });
            });
        } else {
            v1Opts.forEach(o1 => combinations.push(o1));
        }

        combinations.forEach(combo => {
            const tr = document.createElement('tr');
            tr.className = 'matrix-row';
            tr.innerHTML = `
                <td>
                    <input type="text" name="variants[${rowIdx}][variant_name]" class="form-control form-control-sm font-bold text-gold" value="${combo}" required>
                </td>
                <td>
                    <input type="number" step="0.01" name="variants[${rowIdx}][price]" class="form-control form-control-sm input-price" placeholder="250.00" value="250.00" required>
                </td>
                <td>
                    <input type="number" name="variants[${rowIdx}][stock_qty]" class="form-control form-control-sm input-stock" placeholder="50" value="50" min="0" required>
                </td>
                <td>
                    <input type="text" name="variants[${rowIdx}][sku]" class="form-control form-control-sm" placeholder="Auto SKU">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-link text-danger btn-remove-row" style="text-decoration:none;"><i class="bi bi-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
            rowIdx++;
        });
    }

    if (btnGenerate) {
        btnGenerate.addEventListener('click', buildMatrix);
        buildMatrix(); // Build initial Shopee default matrix
    }

    // Shopee Batch Apply
    if (btnApplyBatch) {
        btnApplyBatch.addEventListener('click', function() {
            const bPrice = document.getElementById('batch-price').value;
            const bStock = document.getElementById('batch-stock').value;

            if (bPrice !== '') {
                document.querySelectorAll('.input-price').forEach(input => input.value = bPrice);
                const baseInput = document.getElementById('base_price_input');
                if (baseInput) baseInput.value = bPrice;
            }
            if (bStock !== '') {
                document.querySelectorAll('.input-stock').forEach(input => input.value = bStock);
            }
        });
    }

    // Remove single row
    tbody.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-row')) {
            const row = e.target.closest('.matrix-row');
            if (row) row.remove();
        }
    });

    // Image previews
    const fileInput = document.getElementById('image_files_input');
    const previewContainer = document.getElementById('photo-preview-container');
    if (fileInput && previewContainer) {
        fileInput.addEventListener('change', function() {
            previewContainer.innerHTML = '';
            Array.from(fileInput.files).forEach((file, i) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-3 col-md-2';
                    col.innerHTML = `
                        <div style="aspect-ratio:1;border-radius:8px;overflow:hidden;border:2px solid ${i===0 ? 'var(--mb-gold)' : 'var(--mb-border)'};position:relative;">
                            <img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">
                            ${i===0 ? '<span class="badge bg-gold text-dark" style="position:absolute;bottom:4px;left:4px;font-size:.65rem;">COVER</span>' : ''}
                        </div>
                    `;
                    previewContainer.appendChild(col);
                };
                reader.readAsDataURL(file);
            });
        });
    }
});
</script>
@endsection
