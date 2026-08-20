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
  .shopee-upload-card:hover {
    border-color: #f56c6c !important;
    background: rgba(245, 108, 108, 0.08) !important;
  }
  .thumb-card-box {
    width: 100px;
    height: 100px;
    border-radius: 8px;
    overflow: hidden;
    position: relative;
    border: 1px solid var(--mb-border);
    background: var(--mb-surface);
  }
  .thumb-card-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .thumb-card-box .badge-cover {
    position: absolute;
    bottom: 4px;
    left: 4px;
    font-size: 0.62rem;
    background: var(--mb-gold);
    color: #000;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
  }
  .btn-xs {
    font-size: 0.72rem;
    padding: 2px 6px;
    border-radius: 4px;
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
                    <a href="#section-media" class="shopee-nav-link"><i class="bi bi-images"></i> 2. Product Images</a>
                    <a href="#section-variations" class="shopee-nav-link"><i class="bi bi-diagram-3"></i> 3. Connected Variants</a>
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
                    <input type="text" name="name" id="product_name" class="form-control" placeholder="e.g. RAI Titanium Disc Rotor Bolts Kit" maxlength="200" required>
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
                    <textarea name="description" class="form-control" rows="4" placeholder="Include fitment compatibility, thread size specs, torque recommendation, and pack info..."></textarea>
                </div>
            </div>

            {{-- ── 2. Product Images (Shopee 1:1 Upload Grid) ── --}}
            <div class="shopee-section-card" id="section-media">
                <div class="shopee-section-title">
                    <i class="bi bi-images text-gold"></i> 2. Product Images
                </div>
                
                <div class="mb-2">
                    <label class="form-label font-bold mb-1">Product Gallery Images</label>
                    <div style="font-size:.85rem;color:var(--mb-muted);" class="mb-3">
                        <span class="text-danger fw-bold">*</span> 1:1 Image (Square aspect ratio recommended)
                    </div>

                    {{-- Shopee Upload Grid --}}
                    <div class="d-flex flex-wrap gap-3 align-items-center mb-2">
                        <label for="image_files_input" class="shopee-upload-card" style="width:100px;height:100px;border:2px dashed #f56c6c;border-radius:8px;background:var(--mb-surface);display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;text-align:center;padding:8px;">
                            <i class="bi bi-image-fill text-danger fs-3 mb-1"></i>
                            <span style="font-size:.78rem;color:#f56c6c;font-weight:600;" id="image-counter-text">Add Image<br>(0/9)</span>
                        </label>

                        <input type="file" name="image_files[]" id="image_files_input" class="d-none" multiple accept="image/*">
                        <div id="image-thumbnails-wrapper" class="d-flex flex-wrap gap-3"></div>
                    </div>

                    <div class="text-danger mt-2" style="font-size:.78rem;" id="image-warning-text">
                        Image is missing, please make sure at least this product has one cover image.
                    </div>
                </div>
            </div>

            {{-- ── 3. Connected Variants & Sub-Variants Builder ── --}}
            <div class="shopee-section-card" id="section-variations">
                <div class="shopee-section-title justify-content-between flex-wrap gap-2">
                    <span><i class="bi bi-diagram-3-fill text-gold"></i> 3. Sales &amp; Custom Variants</span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-gold btn-sm" id="btn-add-tier2-suboption">
                            <i class="bi bi-node-plus me-1"></i> + Add Tier 2 Sub-Option
                        </button>
                        <button type="button" class="btn btn-gold btn-sm" id="btn-add-manual-variant">
                            <i class="bi bi-plus-lg me-1"></i> + Add Variant Row
                        </button>
                    </div>
                </div>

                <p style="font-size:.85rem;color:var(--mb-muted);" class="mb-3">
                    Add custom variant rows (e.g. <code>5x25/CNC/4pcs</code>) or use <strong>+ Add Tier 2 Sub-Option</strong> to connect sub-options (e.g. <code>5x25/CNC/4pcs - Gold</code>). Each row has its own dedicated photo file upload!
                </p>

                {{-- Shopee Batch Setting Tool --}}
                <div class="batch-edit-bar mb-3">
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

                {{-- Connected Variants Table with Photo Upload Column --}}
                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0" id="variant-manual-table">
                        <thead>
                            <tr>
                                <th style="width:110px;" class="text-center">Variant Photo</th>
                                <th style="min-width:220px;">Connected Variant &amp; Sub-Variant Label *</th>
                                <th style="width:130px;">Price (₱) *</th>
                                <th style="width:110px;">Stock Qty *</th>
                                <th style="width:130px;">SKU (Optional)</th>
                                <th style="width:50px;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="variant-manual-rows">
                            {{-- Initial Connected Sub-Variant Rows --}}
                        </tbody>
                    </table>
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
    // Character counter
    const nameInput = document.getElementById('product_name');
    const nameCounter = document.getElementById('name-counter');
    if (nameInput) {
        nameInput.addEventListener('input', () => {
            nameCounter.textContent = `${nameInput.value.length} / 200`;
        });
    }

    let variantRowCounter = 0;
    const tbody = document.getElementById('variant-manual-rows');
    const btnAddVariant = document.getElementById('btn-add-manual-variant');
    const btnGenerateConnected = document.getElementById('btn-generate-connected');
    const btnApplyBatch = document.getElementById('btn-apply-batch');

    function createVariantRow(label = '', price = '250.00', stock = '50') {
        const tr = document.createElement('tr');
        tr.className = 'manual-variant-row';
        const rowId = variantRowCounter++;

        tr.innerHTML = `
            <td class="text-center align-middle">
                <div class="d-flex flex-column align-items-center gap-1">
                    <div class="variant-img-preview" id="v-preview-${rowId}" style="width:42px;height:42px;border-radius:6px;overflow:hidden;border:1px solid var(--mb-border);background:var(--mb-surface);display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-image text-muted fs-5"></i>
                    </div>
                    <label class="btn btn-outline-gold btn-xs py-0 px-1" style="font-size:.68rem;cursor:pointer;">
                        <i class="bi bi-upload"></i> Photo
                        <input type="file" name="variants[${rowId}][image_file]" accept="image/*" class="d-none variant-file-input" data-target="v-preview-${rowId}">
                    </label>
                </div>
            </td>
            <td class="align-middle">
                <input type="text" name="variants[${rowId}][variant_name]" class="form-control form-control-sm font-bold text-gold" value="${label}" placeholder="e.g. 5x25/CNC/4pcs - Gold" required>
            </td>
            <td class="align-middle">
                <input type="number" step="0.01" name="variants[${rowId}][price]" class="form-control form-control-sm input-price" value="${price}" required>
            </td>
            <td class="align-middle">
                <input type="number" name="variants[${rowId}][stock_qty]" class="form-control form-control-sm input-stock" value="${stock}" min="0" required>
            </td>
            <td class="align-middle">
                <input type="text" name="variants[${rowId}][sku]" class="form-control form-control-sm" placeholder="Auto SKU">
            </td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-sm btn-link text-danger btn-remove-row" style="text-decoration:none;"><i class="bi bi-trash fs-5"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
    }

    // Start with 1 clean empty row (NO pre-populated presets)
    createVariantRow('', '', '50');

    // Add single manual variant row
    if (btnAddVariant) {
        btnAddVariant.addEventListener('click', function() {
            createVariantRow('', '', '50');
        });
    }

    // Add Connected Tier 2 Sub-Option row
    const btnAddTier2 = document.getElementById('btn-add-tier2-suboption');
    if (btnAddTier2) {
        btnAddTier2.addEventListener('click', function() {
            const rows = tbody.querySelectorAll('.manual-variant-row');
            let parentLabel = '';
            if (rows.length > 0) {
                const lastVal = rows[rows.length - 1].querySelector('input[name*="[variant_name]"]').value.trim();
                if (lastVal) {
                    parentLabel = lastVal.split(' - ')[0];
                }
            }
            const defaultCombo = parentLabel ? `${parentLabel} - Sub Option` : 'Main Option - Sub Option';
            createVariantRow(defaultCombo, '', '50');
        });
    }

    // Batch Apply Tool
    if (btnApplyBatch) {
        btnApplyBatch.addEventListener('click', function() {
            const bPrice = document.getElementById('batch-price').value;
            const bStock = document.getElementById('batch-stock').value;

            if (bPrice !== '') {
                document.querySelectorAll('.input-price').forEach(input => input.value = bPrice);
            }
            if (bStock !== '') {
                document.querySelectorAll('.input-stock').forEach(input => input.value = bStock);
            }
        });
    }

    // Remove variant row
    tbody.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-row')) {
            const row = e.target.closest('.manual-variant-row');
            if (tbody.querySelectorAll('.manual-variant-row').length > 1) {
                row.remove();
            } else {
                alert('At least one variant is required.');
            }
        }
    });

    // Image preview for variant rows when uploading
    tbody.addEventListener('change', function(e) {
        if (e.target.classList.contains('variant-file-input')) {
            const targetId = e.target.dataset.target;
            const previewBox = document.getElementById(targetId);
            if (e.target.files && e.target.files[0] && previewBox) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    previewBox.innerHTML = `<img src="${evt.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        }
    });

    // Main Shopee Gallery Image Upload Dropzone Handler
    const fileInput = document.getElementById('image_files_input');
    const thumbnailsWrapper = document.getElementById('image-thumbnails-wrapper');
    const counterText = document.getElementById('image-counter-text');
    const warningText = document.getElementById('image-warning-text');

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const selectedFiles = Array.from(e.target.files).slice(0, 9);
            thumbnailsWrapper.innerHTML = '';

            if (selectedFiles.length > 0) {
                warningText.style.display = 'none';
                counterText.innerHTML = `Add Image<br>(${selectedFiles.length}/9)`;
            } else {
                warningText.style.display = 'block';
                counterText.innerHTML = `Add Image<br>(0/9)`;
            }

            selectedFiles.forEach((file, idx) => {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    const box = document.createElement('div');
                    box.className = 'thumb-card-box';
                    box.innerHTML = `
                        <img src="${evt.target.result}" alt="Photo ${idx+1}">
                        ${idx === 0 ? '<span class="badge-cover">COVER</span>' : ''}
                    `;
                    thumbnailsWrapper.appendChild(box);
                };
                reader.readAsDataURL(file);
            });
        });
    }
});
</script>
@endsection
