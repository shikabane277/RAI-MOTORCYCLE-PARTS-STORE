@extends('layouts.admin')
@section('title', 'Manage Filter Attributes & Specifications')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0   font-weight-bold" style="font-family:'Rajdhani',sans-serif;">
                <i class="bi bi-sliders text-gold me-2"></i>Filter Options &amp; Specifications
            </h1>
            <p class="text-muted small mb-0">Customize materials, colors, thread sizes, and brand options shown in store filters &amp; product options.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-gold btn-sm">
                <i class="bi bi-tags me-1"></i>Manage Brands
            </a>
            <button class="btn btn-gold btn-sm" data-bs-toggle="modal" data-bs-target="#addAttributeModal">
                <i class="bi bi-plus-lg me-1"></i>Add New Filter Option
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-4" id="attributeTabs" role="tablist" style="border-bottom:1px solid var(--mb-border);">
        <li class="nav-item" role="presentation">
            <button class="nav-link active text-gold fw-bold" id="materials-tab" data-bs-toggle="tab" data-bs-target="#materials" type="button" role="tab">
                🔩 Materials ({{ $materials->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link   fw-bold" id="colors-tab" data-bs-toggle="tab" data-bs-target="#colors" type="button" role="tab">
                🎨 Colors &amp; Swatches ({{ $colors->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link   fw-bold" id="threads-tab" data-bs-toggle="tab" data-bs-target="#threads" type="button" role="tab">
                📐 Thread Sizes ({{ $threadSizes->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link   fw-bold" id="brands-tab" data-bs-toggle="tab" data-bs-target="#brands" type="button" role="tab">
                🏷️ Brands ({{ $brands->count() }})
            </button>
        </li>
    </ul>

    <div class="tab-content" id="attributeTabsContent">
        
        {{-- ── 1. Materials ──────────────────────────────────────────────── --}}
        <div class="tab-pane fade show active" id="materials" role="tabpanel">
            <div class="dark-card p-4">
                <div class="table-responsive">
                    <table class="table table-dark align-middle mb-0">
                        <thead>
                            <tr style="border-bottom:1px solid var(--mb-border);color:var(--mb-muted);font-size:.85rem;">
                                <th>#</th>
                                <th>Material Name</th>
                                <th>Sort Order</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($materials as $mat)
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                                <td style="color:var(--mb-muted);">{{ $loop->iteration }}</td>
                                <td class="fw-bold  ">{{ $mat->name }}</td>
                                <td>{{ $mat->sort_order }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.attributes.toggle', $mat->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm border-0 p-0">
                                            <span class="badge bg-{{ $mat->is_active ? 'success' : 'secondary' }}">
                                                {{ $mat->is_active ? 'Active' : 'Disabled' }}
                                            </span>
                                        </button>
                                    </form>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-light me-1" onclick="editAttribute({{ $mat->id }}, '{{ addslashes($mat->name) }}', '{{ $mat->value }}', {{ $mat->sort_order }}, {{ $mat->is_active ? 1 : 0 }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.attributes.destroy', $mat->id) }}" class="d-inline" onsubmit="return confirm('Delete this material?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No materials found. Add one above.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── 2. Colors ────────────────────────────────────────────────── --}}
        <div class="tab-pane fade" id="colors" role="tabpanel">
            <div class="dark-card p-4">
                <div class="table-responsive">
                    <table class="table table-dark align-middle mb-0">
                        <thead>
                            <tr style="border-bottom:1px solid var(--mb-border);color:var(--mb-muted);font-size:.85rem;">
                                <th>Swatch</th>
                                <th>Color Name</th>
                                <th>Hex / CSS Value</th>
                                <th>Sort Order</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($colors as $color)
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                                <td>
                                    <div style="width:28px;height:28px;border-radius:50%;border:1px solid rgba(255,255,255,0.3);background:{{ $color->value ?? '#666' }};"></div>
                                </td>
                                <td class="fw-bold  ">{{ $color->name }}</td>
                                <td style="font-family:monospace;color:var(--mb-gold);font-size:.85rem;">{{ $color->value ?? '—' }}</td>
                                <td>{{ $color->sort_order }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.attributes.toggle', $color->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm border-0 p-0">
                                            <span class="badge bg-{{ $color->is_active ? 'success' : 'secondary' }}">
                                                {{ $color->is_active ? 'Active' : 'Disabled' }}
                                            </span>
                                        </button>
                                    </form>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-light me-1" onclick="editAttribute({{ $color->id }}, '{{ addslashes($color->name) }}', '{{ $color->value }}', {{ $color->sort_order }}, {{ $color->is_active ? 1 : 0 }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.attributes.destroy', $color->id) }}" class="d-inline" onsubmit="return confirm('Delete this color option?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No color options found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── 3. Thread Sizes ─────────────────────────────────────────── --}}
        <div class="tab-pane fade" id="threads" role="tabpanel">
            <div class="dark-card p-4">
                <div class="table-responsive">
                    <table class="table table-dark align-middle mb-0">
                        <thead>
                            <tr style="border-bottom:1px solid var(--mb-border);color:var(--mb-muted);font-size:.85rem;">
                                <th>#</th>
                                <th>Thread Size</th>
                                <th>Sort Order</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($threadSizes as $ts)
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                                <td style="color:var(--mb-muted);">{{ $loop->iteration }}</td>
                                <td class="fw-bold  ">{{ $ts->name }}</td>
                                <td>{{ $ts->sort_order }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.attributes.toggle', $ts->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm border-0 p-0">
                                            <span class="badge bg-{{ $ts->is_active ? 'success' : 'secondary' }}">
                                                {{ $ts->is_active ? 'Active' : 'Disabled' }}
                                            </span>
                                        </button>
                                    </form>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-light me-1" onclick="editAttribute({{ $ts->id }}, '{{ addslashes($ts->name) }}', '{{ $ts->value }}', {{ $ts->sort_order }}, {{ $ts->is_active ? 1 : 0 }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.attributes.destroy', $ts->id) }}" class="d-inline" onsubmit="return confirm('Delete this thread size?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No thread sizes found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── 4. Brands ───────────────────────────────────────────────── --}}
        <div class="tab-pane fade" id="brands" role="tabpanel">
            <div class="dark-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h5   mb-0">Store Brands</h3>
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-gold btn-sm"><i class="bi bi-gear me-1"></i>Manage All Brands</a>
                </div>
                <div class="row g-3">
                    @foreach($brands as $brand)
                    <div class="col-md-3">
                        <div class="p-3" style="background:var(--mb-surface);border-radius:8px;border:1px solid var(--mb-border);">
                            <div class="fw-bold   mb-1">{{ $brand->name }}</div>
                            <div style="font-size:.78rem;color:var(--mb-muted);">Slug: {{ $brand->slug }}</div>
                            <span class="badge bg-{{ $brand->is_active ? 'success' : 'secondary' }} mt-2">
                                {{ $brand->is_active ? 'Active in Filters' : 'Disabled' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ── Add Attribute Modal ────────────────────────────────────────────── --}}
<div class="modal fade" id="addAttributeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:#1e1e1e;border:1px solid #333;color:#fff;border-radius:12px;">
            <form method="POST" action="{{ route('admin.attributes.store') }}">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-weight-bold"><i class="bi bi-plus-circle text-gold me-2"></i>Add Filter Option</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label text-muted">Option Type</label>
                        <select name="type" class="form-select bg-dark   border-secondary" required onchange="toggleColorInput(this.value, 'add')">
                            <option value="material">Material (e.g. Titanium Gr5, Carbon Fiber)</option>
                            <option value="color">Color (e.g. Titanium Blue, Gold)</option>
                            <option value="thread_size">Thread Size (e.g. M6, M8, M10)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Name / Label</label>
                        <input type="text" name="name" class="form-control bg-dark   border-secondary" placeholder="e.g. Titanium Gr5 or Red" required>
                    </div>
                    <div class="mb-3" id="add-color-val-group" style="display:none;">
                        <label class="form-label text-muted">Color Hex Code / CSS Value</label>
                        <div class="d-flex gap-2">
                            <input type="color" id="add-color-picker" class="form-control form-control-color bg-dark border-secondary" value="#f5a623" onchange="document.getElementById('add-color-value').value=this.value">
                            <input type="text" name="value" id="add-color-value" class="form-control bg-dark   border-secondary" placeholder="#f5a623">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control bg-dark   border-secondary" value="0">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Save Option</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Edit Attribute Modal ────────────────────────────────────────────── --}}
<div class="modal fade" id="editAttributeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:#1e1e1e;border:1px solid #333;color:#fff;border-radius:12px;">
            <form method="POST" id="editAttributeForm" action="">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-weight-bold"><i class="bi bi-pencil-square text-gold me-2"></i>Edit Filter Option</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label text-muted">Option Name</label>
                        <input type="text" name="name" id="edit-name" class="form-control bg-dark   border-secondary" required>
                    </div>
                    <div class="mb-3" id="edit-color-val-group">
                        <label class="form-label text-muted">Color Hex Code / CSS Value</label>
                        <input type="text" name="value" id="edit-value" class="form-control bg-dark   border-secondary" placeholder="#f5a623">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Sort Order</label>
                        <input type="number" name="sort_order" id="edit-sort" class="form-control bg-dark   border-secondary">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="edit-active" value="1" class="form-check-input">
                        <label class="form-check-label  " for="edit-active">Active in shop filters</label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Update Option</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleColorInput(type, mode) {
    const group = document.getElementById(mode + '-color-val-group');
    if (group) group.style.display = (type === 'color') ? 'block' : 'none';
}

function editAttribute(id, name, value, sortOrder, isActive) {
    const form = document.getElementById('editAttributeForm');
    form.action = `/admin/attributes/${id}`;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-value').value = value || '';
    document.getElementById('edit-sort').value = sortOrder;
    document.getElementById('edit-active').checked = (isActive == 1);

    const modal = new bootstrap.Modal(document.getElementById('editAttributeModal'));
    modal.show();
}
</script>
@endsection
