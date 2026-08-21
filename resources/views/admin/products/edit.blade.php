@extends('layouts.admin')
@section('title', 'Edit Product — ' . $product->name)
@section('page-title', 'Edit Product')

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
  .shopee-upload-card:hover {
    border-color: #f56c6c !important;
    background: rgba(245, 108, 108, 0.08) !important;
  }
  .thumb-card-box {
    width: 90px;
    height: 90px;
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
  .option-pill-row {
    background: var(--mb-card);
    border: 1px solid var(--mb-border);
    border-radius: 6px;
    padding: 6px 12px;
  }
</style>

<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" id="shopee-product-form">
    @csrf
    @method('PUT')

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
                    <a href="#section-variations" class="shopee-nav-link"><i class="bi bi-diagram-3"></i> 3. Sales &amp; Custom Variants</a>
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
                    <input type="text" name="name" id="product_name" class="form-control" value="{{ old('name', $product->name) }}" maxlength="200" required>
                    <div class="d-flex justify-content-between mt-1" style="font-size:.78rem;color:var(--mb-muted);">
                        <span>Use descriptive keywords (Brand + Model + Size + Spec + Pack Qty)</span>
                        <span id="name-counter">{{ strlen($product->name) }} / 200</span>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label font-bold">Category *</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">— Select Category —</option>
                            @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ $product->category_id == $c->id ? 'selected' : '' }}>{{ $c->parent_id ? '↳ ' : '' }}{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold">Brand</label>
                        <select name="brand_id" class="form-select">
                            <option value="">— No Brand / Generic —</option>
                            @foreach($brands as $b)
                            <option value="{{ $b->id }}" {{ $product->brand_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label font-bold">Short Highlight Description</label>
                    <input type="text" name="short_description" class="form-control" value="{{ old('short_description', $product->short_description) }}" maxlength="500">
                </div>

                <div class="mb-3">
                    <label class="form-label font-bold">Detailed Product Description</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>

            {{-- ── 2. Product Images ──────────────────────────── --}}
            <div class="shopee-section-card" id="section-media">
                <div class="shopee-section-title">
                    <i class="bi bi-images text-gold"></i> 2. Product Images
                </div>
                
                <div class="row g-4 mb-2">
                    {{-- 2A. Separated Main Cover Image Upload Box --}}
                    <div class="col-md-4">
                        <label class="form-label font-bold text-gold">Main Cover Image *</label>
                        <div style="font-size:.78rem;color:var(--mb-muted);" class="mb-2">
                            Primary 1:1 image displayed on store listings &amp; search.
                        </div>

                        <div class="d-flex flex-column align-items-center justify-content-center p-3 text-center" style="border:2px dashed var(--mb-gold);border-radius:10px;background:var(--mb-surface);min-height:130px;position:relative;" id="cover-dropzone">
                            <div id="cover-preview-box" class="w-100 d-flex flex-column align-items-center">
                                @if($product->primary_image_url)
                                    <div class="thumb-card-box mx-auto mb-1" style="width:85px;height:85px;">
                                        <img src="{{ $product->primary_image_url }}" alt="Cover Image">
                                        <span class="badge-cover">MAIN COVER</span>
                                    </div>
                                @else
                                    <i class="bi bi-image-fill text-gold fs-2 mb-1"></i>
                                    <span style="font-size:.8rem;color:var(--mb-heading);font-weight:600;">Main Cover Image</span>
                                @endif
                            </div>
                            <label for="cover_image_file" class="btn btn-gold btn-xs mt-2 py-1 px-3" style="cursor:pointer;font-size:.75rem;">
                                <i class="bi bi-upload me-1"></i> Change Cover Photo
                                <input type="file" name="cover_image_file" id="cover_image_file" class="d-none" accept="image/*">
                            </label>
                        </div>
                    </div>

                    {{-- 2B. Additional Product Gallery Images (Accumulating!) --}}
                    <div class="col-md-8">
                        <label class="form-label font-bold">Additional Gallery Images</label>
                        <div style="font-size:.78rem;color:var(--mb-muted);" class="mb-2">
                            Add detail photos. Selecting new photos accumulates/appends cleanly!
                        </div>

                        <div class="d-flex flex-wrap gap-3 align-items-center mb-2">
                            <label for="image_files_input" class="shopee-upload-card" style="width:90px;height:90px;border:2px dashed #f56c6c;border-radius:8px;background:var(--mb-surface);display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;text-align:center;padding:8px;">
                                <i class="bi bi-plus-circle-fill text-danger fs-3 mb-1"></i>
                                <span style="font-size:.75rem;color:#f56c6c;font-weight:600;" id="image-counter-text">Add Gallery<br>Photos</span>
                            </label>

                            <input type="file" name="image_files[]" id="image_files_input" class="d-none" multiple accept="image/*">
                            <div id="image-thumbnails-wrapper" class="d-flex flex-wrap gap-3">
                                @if(is_array($product->images))
                                    @foreach($product->images as $gImg)
                                        @if($gImg !== $product->primary_image_url)
                                        <div class="thumb-card-box">
                                            <img src="{{ $gImg }}" alt="Gallery Image">
                                        </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── 3. Separated Tier 1 & Tier 2 Sub-Option Builder ── --}}
            @php
                $parsedGroups = $product->parsed_option_groups;
                $t1Name = $parsedGroups[0]['name'] ?? 'Size / Specification';
                $t2Name = $parsedGroups[1]['name'] ?? 'Color';
                $hasTier2 = count($parsedGroups) > 1;

                $t1Vals = array_map(fn($v) => $v['label'], $parsedGroups[0]['values'] ?? []);
                $t2Vals = $hasTier2 ? array_map(fn($v) => $v['label'], $parsedGroups[1]['values'] ?? []) : [];
            @endphp

            <div class="shopee-section-card" id="section-variations">
                <div class="shopee-section-title">
                    <i class="bi bi-diagram-3-fill text-gold"></i> 3. Sales &amp; Custom Product Variants
                </div>

                {{-- Tier 1 Options Container --}}
                <div class="p-3 mb-3" style="background:var(--mb-surface);border:1px solid var(--mb-border);border-radius:10px;">
                    <div class="mb-3">
                        <label class="form-label font-bold text-gold">Tier 1 Main Group Name *</label>
                        <input type="text" name="tier1_name" id="tier1_name" class="form-control form-control-sm" value="{{ $t1Name }}">
                    </div>

                    <label class="form-label font-bold small">Tier 1 Option Values *</label>
                    <div id="tier1-values-container" class="d-flex flex-column gap-2 mb-2">
                        {{-- Populated dynamically --}}
                    </div>
                    <button type="button" class="btn btn-outline-gold btn-sm" id="btn-add-tier1-val">
                        <i class="bi bi-plus-circle me-1"></i> + Add Tier 1 Option Value
                    </button>
                </div>

                {{-- Tier 2 Sub-Options Container (Optional) --}}
                <div class="p-3 mb-4" style="background:var(--mb-surface);border:1px solid var(--mb-border);border-radius:10px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 style="font-family:'Rajdhani',sans-serif;color:var(--mb-gold);font-weight:700;" class="mb-0">
                                <i class="bi bi-diagram-2-fill me-1"></i> Tier 2 Sub-Variation (Optional)
                            </h6>
                            <span style="font-size:.78rem;color:var(--mb-muted);">Enable to connect sub-options (e.g. 3 or more colors) to all Tier 1 options.</span>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="enable-tier2-toggle" {{ $hasTier2 ? 'checked' : '' }} style="cursor:pointer;">
                            <label class="form-check-label font-bold text-white small" for="enable-tier2-toggle">Enable Tier 2</label>
                        </div>
                    </div>

                    <div id="tier2-config-wrapper" style="{{ $hasTier2 ? 'display:block;' : 'display:none;' }}" class="mt-3 pt-2 border-top border-secondary">
                        <div class="mb-3">
                            <label class="form-label font-bold text-gold small">Tier 2 Sub-Group Name</label>
                            <input type="text" name="tier2_name" id="tier2_name" class="form-control form-control-sm" value="{{ $t2Name }}">
                        </div>

                        <label class="form-label font-bold small">Tier 2 Sub-Option Values</label>
                        <div id="tier2-values-container" class="d-flex flex-column gap-2 mb-2">
                            {{-- Populated dynamically --}}
                        </div>
                        <button type="button" class="btn btn-outline-gold btn-sm" id="btn-add-tier2-val">
                            <i class="bi bi-plus-circle me-1"></i> + Add Tier 2 Sub-Option Value
                        </button>
                    </div>
                </div>

                {{-- Combinations Matrix Table --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 style="font-family:'Rajdhani',sans-serif;color:var(--mb-heading);font-weight:700;" class="mb-0">
                        Connected Combination Rows &amp; Stock Pricing List
                    </h6>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0">
                        <thead>
                            <tr>
                                <th style="width:90px;" class="text-center">Photo</th>
                                <th style="min-width:160px;">Tier 1 Option *</th>
                                <th style="min-width:160px;{{ $hasTier2 ? '' : 'display:none;' }}" class="tier2-matrix-col">Tier 2 Sub-Option</th>
                                <th style="width:130px;">Price (₱) *</th>
                                <th style="width:110px;">Stock Qty *</th>
                                <th style="width:50px;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="variant-matrix-rows">
                            {{-- Populated dynamically --}}
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
                        <input type="number" name="weight_grams" class="form-control" value="{{ old('weight_grams', $product->weight_grams ?: 150) }}" required>
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
                            <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>Active &amp; Published</option>
                            <option value="draft" {{ $product->status === 'draft' ? 'selected' : '' }}>Draft (Hidden)</option>
                            <option value="archived" {{ $product->status === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end gap-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" {{ $product->is_featured ? 'checked' : '' }}>
                            <label class="form-check-label font-bold text-white" for="is_featured">Featured Product</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_new_arrival" value="1" id="is_new_arrival" {{ $product->is_new_arrival ? 'checked' : '' }}>
                            <label class="form-check-label font-bold text-white" for="is_new_arrival">New Arrival</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 pt-2">
                    <button type="submit" class="btn btn-gold btn-lg px-5">
                        <i class="bi bi-cloud-check me-1"></i> Save Changes
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-dark-surface btn-lg">Cancel</a>
                </div>
            </div>

        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Existing variants pre-loaded from backend
    const initialVariants = <?php echo json_encode($product->variants->map(function($v) {
        $vName = $v->variant_name ?: 'Standard';
        $parts = explode(' - ', $vName);
        return [
            'id' => $v->id,
            't1' => $parts[0] ?? $vName,
            't2' => $parts[1] ?? '',
            'price' => (float)$v->price,
            'stock' => (int)$v->stock_qty,
            'image' => $v->image_url,
        ];
    })); ?>;

    const initialT1Vals = <?php echo json_encode($t1Vals); ?>;
    const initialT2Vals = <?php echo json_encode($t2Vals); ?>;

    const tier1Container = document.getElementById('tier1-values-container');
    const tier2Container = document.getElementById('tier2-values-container');
    const matrixTbody = document.getElementById('variant-matrix-rows');
    const btnAddTier1Val = document.getElementById('btn-add-tier1-val');
    const btnAddTier2Val = document.getElementById('btn-add-tier2-val');
    const tier2Toggle = document.getElementById('enable-tier2-toggle');
    const tier2ConfigWrapper = document.getElementById('tier2-config-wrapper');

    let matrixRowCounter = 0;

    function addTier1ValInput(val = '') {
        const div = document.createElement('div');
        div.className = 'd-flex align-items-center gap-2 option-pill-row';
        div.innerHTML = `
            <i class="bi bi-tag text-gold"></i>
            <input type="text" class="form-control form-control-sm tier1-input-val font-bold text-gold" value="${val}" required>
            <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-remove-t1-val" style="text-decoration:none;"><i class="bi bi-x-circle fs-6"></i></button>
        `;
        tier1Container.appendChild(div);
        rebuildCombinationMatrix();
    }

    function addTier2ValInput(val = '') {
        const div = document.createElement('div');
        div.className = 'd-flex align-items-center gap-2 option-pill-row';
        div.innerHTML = `
            <i class="bi bi-palette text-gold"></i>
            <input type="text" class="form-control form-control-sm tier2-input-val" value="${val}">
            <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-remove-t2-val" style="text-decoration:none;"><i class="bi bi-x-circle fs-6"></i></button>
        `;
        tier2Container.appendChild(div);
        rebuildCombinationMatrix();
    }

    // Pre-populate initial option values
    if (initialT1Vals.length > 0) {
        initialT1Vals.forEach(v => addTier1ValInput(v));
    } else {
        addTier1ValInput('');
    }

    if (initialT2Vals.length > 0) {
        initialT2Vals.forEach(v => addTier2ValInput(v));
    } else {
        addTier2ValInput('');
    }

    if (btnAddTier1Val) btnAddTier1Val.addEventListener('click', () => addTier1ValInput(''));
    if (btnAddTier2Val) btnAddTier2Val.addEventListener('click', () => addTier2ValInput(''));

    if (tier2Toggle) {
        tier2Toggle.addEventListener('change', function() {
            const isEnabled = this.checked;
            tier2ConfigWrapper.style.display = isEnabled ? 'block' : 'none';
            document.querySelectorAll('.tier2-matrix-col').forEach(el => {
                el.style.display = isEnabled ? '' : 'none';
            });
            rebuildCombinationMatrix();
        });
    }

    tier1Container.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-t1-val')) {
            const row = e.target.closest('.option-pill-row');
            if (tier1Container.querySelectorAll('.option-pill-row').length > 1) {
                row.remove();
                rebuildCombinationMatrix();
            } else {
                alert('At least one Tier 1 option value is required.');
            }
        }
    });

    tier2Container.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-t2-val')) {
            const row = e.target.closest('.option-pill-row');
            if (tier2Container.querySelectorAll('.option-pill-row').length > 1) {
                row.remove();
                rebuildCombinationMatrix();
            } else {
                alert('At least one Tier 2 sub-option value is required when Tier 2 is enabled.');
            }
        }
    });

    function rebuildCombinationMatrix() {
        const isTier2Enabled = tier2Toggle && tier2Toggle.checked;
        const t1Vals = Array.from(tier1Container.querySelectorAll('.tier1-input-val'))
                            .map(input => input.value.trim())
                            .filter(Boolean);
        const t2Vals = Array.from(tier2Container.querySelectorAll('.tier2-input-val'))
                            .map(input => input.value.trim())
                            .filter(Boolean);

        const existingData = {};
        // Map pre-loaded variants
        initialVariants.forEach(v => {
            const key = `${v.t1}|||${v.t2}`;
            existingData[key] = { price: v.price, stock: v.stock, image: v.image };
        });

        // Also preserve user edits in current DOM
        matrixTbody.querySelectorAll('.manual-variant-row').forEach(tr => {
            const t1 = tr.dataset.t1;
            const t2 = tr.dataset.t2 || '';
            const key = `${t1}|||${t2}`;
            const pInput = tr.querySelector('.input-price');
            const sInput = tr.querySelector('.input-stock');
            if (pInput || sInput) {
                existingData[key] = {
                    price: pInput ? pInput.value : (existingData[key]?.price || ''),
                    stock: sInput ? sInput.value : (existingData[key]?.stock || '50'),
                    image: existingData[key]?.image || null
                };
            }
        });

        matrixTbody.innerHTML = '';
        matrixRowCounter = 0;

        document.querySelectorAll('.tier2-matrix-col').forEach(el => {
            el.style.display = isTier2Enabled ? '' : 'none';
        });

        if (t1Vals.length === 0) return;

        if (isTier2Enabled && t2Vals.length > 0) {
            t1Vals.forEach(v1 => {
                t2Vals.forEach(v2 => {
                    const key = `${v1}|||${v2}`;
                    const saved = existingData[key] || { price: '{{ $product->base_price }}', stock: '50', image: null };
                    createMatrixRow(v1, v2, saved.price, saved.stock, saved.image, true);
                });
            });
        } else {
            t1Vals.forEach(v1 => {
                const key = `${v1}|||`;
                const saved = existingData[key] || { price: '{{ $product->base_price }}', stock: '50', image: null };
                createMatrixRow(v1, '', saved.price, saved.stock, saved.image, false);
            });
        }
    }

    function createMatrixRow(t1, t2, price, stock, image, isTier2Enabled) {
        const tr = document.createElement('tr');
        tr.className = 'manual-variant-row';
        tr.dataset.t1 = t1;
        tr.dataset.t2 = t2;
        const rowId = matrixRowCounter++;

        tr.innerHTML = `
            <td class="text-center align-middle">
                <div class="d-flex flex-column align-items-center gap-1">
                    <div class="variant-img-preview" id="v-preview-${rowId}" style="width:38px;height:38px;border-radius:6px;overflow:hidden;border:1px solid var(--mb-border);background:var(--mb-surface);display:flex;align-items:center;justify-content:center;">
                        ${image ? `<img src="${image}" style="width:100%;height:100%;object-fit:cover;">` : '<i class="bi bi-image text-muted fs-6"></i>'}
                    </div>
                    <input type="hidden" name="variants[${rowId}][existing_image]" value="${image || ''}">
                    <label class="btn btn-outline-gold btn-xs py-0 px-1" style="font-size:.65rem;cursor:pointer;">
                        <i class="bi bi-upload"></i> Photo
                        <input type="file" name="variants[${rowId}][image_file]" accept="image/*" class="d-none variant-file-input" data-target="v-preview-${rowId}">
                    </label>
                </div>
            </td>
            <td class="align-middle">
                <input type="text" name="variants[${rowId}][tier1_option]" class="form-control form-control-sm font-bold text-gold" value="${t1}" readonly style="background:var(--mb-surface);">
            </td>
            <td class="align-middle tier2-matrix-col" style="${isTier2Enabled ? '' : 'display:none;'}">
                <input type="text" name="variants[${rowId}][tier2_option]" class="form-control form-control-sm" value="${t2}" readonly style="background:var(--mb-surface);">
            </td>
            <td class="align-middle">
                <input type="number" step="0.01" name="variants[${rowId}][price]" class="form-control form-control-sm input-price" value="${price}" required>
            </td>
            <td class="align-middle">
                <input type="number" name="variants[${rowId}][stock_qty]" class="form-control form-control-sm input-stock" value="${stock}" min="0" required>
            </td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-sm btn-link text-danger btn-remove-row" style="text-decoration:none;"><i class="bi bi-trash fs-5"></i></button>
            </td>
        `;
        matrixTbody.appendChild(tr);
    }

    tier1Container.addEventListener('input', rebuildCombinationMatrix);
    tier2Container.addEventListener('input', rebuildCombinationMatrix);

    matrixTbody.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-row')) {
            const row = e.target.closest('.manual-variant-row');
            if (matrixTbody.querySelectorAll('.manual-variant-row').length > 1) {
                row.remove();
            } else {
                alert('At least one combination is required.');
            }
        }
    });

    matrixTbody.addEventListener('change', function(e) {
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

    const coverInput = document.getElementById('cover_image_file');
    const coverPreviewBox = document.getElementById('cover-preview-box');
    if (coverInput && coverPreviewBox) {
        coverInput.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    coverPreviewBox.innerHTML = `
                        <div class="thumb-card-box mx-auto mb-1" style="width:85px;height:85px;">
                            <img src="${evt.target.result}" alt="Cover Image">
                            <span class="badge-cover">MAIN COVER</span>
                        </div>
                    `;
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    }

    const galleryInput = document.getElementById('image_files_input');
    const thumbnailsWrapper = document.getElementById('image-thumbnails-wrapper');
    let accumulatedFiles = new DataTransfer();

    if (galleryInput && thumbnailsWrapper) {
        galleryInput.addEventListener('change', function(e) {
            const newlySelected = Array.from(e.target.files);
            newlySelected.forEach(file => {
                if (accumulatedFiles.items.length < 8) {
                    accumulatedFiles.items.add(file);
                }
            });
            galleryInput.files = accumulatedFiles.files;
            renderGalleryThumbnails();
        });

        function renderGalleryThumbnails() {
            thumbnailsWrapper.innerHTML = '';
            const files = Array.from(accumulatedFiles.files);
            files.forEach((file, idx) => {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    const box = document.createElement('div');
                    box.className = 'thumb-card-box';
                    box.style.position = 'relative';
                    box.innerHTML = `
                        <img src="${evt.target.result}" alt="Gallery ${idx+1}">
                        <button type="button" class="btn btn-danger btn-xs py-0 px-1 btn-remove-gallery-img" data-index="${idx}" style="position:absolute;top:2px;right:2px;font-size:10px;line-height:1;border-radius:50%;">&times;</button>
                    `;
                    thumbnailsWrapper.appendChild(box);
                };
                reader.readAsDataURL(file);
            });
        }
    }
});
</script>
@endsection
