@extends('layouts.admin')
@section('title', 'Manage Brands')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-white font-weight-bold" style="font-family:'Rajdhani',sans-serif;">
                <i class="bi bi-award text-gold me-2"></i>Brand Management
            </h1>
            <p class="text-muted small mb-0">Create, edit, toggle, and manage motorcycle part manufacturers &amp; brands.</p>
        </div>
        <button class="btn btn-gold btn-sm" data-bs-toggle="modal" data-bs-target="#addBrandModal">
            <i class="bi bi-plus-lg me-1"></i>Add New Brand
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="dark-card p-4">
        <div class="table-responsive">
            <table class="table table-dark align-middle mb-0">
                <thead>
                    <tr style="border-bottom:1px solid var(--mb-border);color:var(--mb-muted);font-size:.85rem;">
                        <th>Brand</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Linked Products</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($brands as $brand)
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:40px;height:40px;background:var(--mb-surface);border:1px solid var(--mb-border);border-radius:8px;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;">
                                    @if($brand->logo_url)
                                        <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" style="width:100%;height:100%;object-fit:cover;">
                                    @else
                                        <span style="font-weight:700;color:var(--mb-gold);font-size:1.1rem;">{{ substr($brand->name, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div class="fw-bold text-white">{{ $brand->name }}</div>
                            </div>
                        </td>
                        <td style="font-family:monospace;color:var(--mb-gold);font-size:.85rem;">{{ $brand->slug }}</td>
                        <td style="color:var(--mb-muted);font-size:.85rem;max-width:280px;" class="text-truncate">
                            {{ $brand->description ?? 'No description provided.' }}
                        </td>
                        <td>
                            <span class="badge bg-dark border border-secondary text-light px-2 py-1">
                                {{ $brand->products_count }} {{ Str::plural('product', $brand->products_count) }}
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.brands.toggle', $brand->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm border-0 p-0">
                                    <span class="badge bg-{{ $brand->is_active ? 'success' : 'secondary' }}">
                                        {{ $brand->is_active ? 'Active' : 'Disabled' }}
                                    </span>
                                </button>
                            </form>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-light me-1" onclick="editBrand({{ $brand->id }}, '{{ addslashes($brand->name) }}', '{{ addslashes($brand->logo_url ?? '') }}', '{{ addslashes($brand->description ?? '') }}', {{ $brand->is_active ? 1 : 0 }})">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <form method="POST" action="{{ route('admin.brands.destroy', $brand->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete brand &quot;{{ addslashes($brand->name) }}&quot;?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-tags fs-1 d-block mb-2 text-gold"></i>
                            No brands found. Click <strong>Add New Brand</strong> to create one.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── Add Brand Modal ────────────────────────────────────────────── --}}
<div class="modal fade" id="addBrandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:#1e1e1e;border:1px solid #333;color:#fff;border-radius:12px;">
            <form method="POST" action="{{ route('admin.brands.store') }}">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-weight-bold"><i class="bi bi-plus-circle text-gold me-2"></i>Add New Brand</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label text-muted">Brand Name *</label>
                        <input type="text" name="name" class="form-control bg-dark text-white border-secondary" placeholder="e.g. Akrapovič, Brembo, RAI" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Logo Image URL</label>
                        <input type="url" name="logo_url" class="form-control bg-dark text-white border-secondary" placeholder="https://example.com/logo.png">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Description</label>
                        <textarea name="description" class="form-control bg-dark text-white border-secondary" rows="3" placeholder="Brief manufacturer background..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Create Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Edit Brand Modal ────────────────────────────────────────────── --}}
<div class="modal fade" id="editBrandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:#1e1e1e;border:1px solid #333;color:#fff;border-radius:12px;">
            <form method="POST" id="editBrandForm" action="">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-weight-bold"><i class="bi bi-pencil-square text-gold me-2"></i>Edit Brand</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label text-muted">Brand Name *</label>
                        <input type="text" name="name" id="edit-brand-name" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Logo Image URL</label>
                        <input type="url" name="logo_url" id="edit-brand-logo" class="form-control bg-dark text-white border-secondary">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Description</label>
                        <textarea name="description" id="edit-brand-description" class="form-control bg-dark text-white border-secondary" rows="3"></textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="edit-brand-active" value="1" class="form-check-input">
                        <label class="form-check-label text-white" for="edit-brand-active">Active on store and in filters</label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Update Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editBrand(id, name, logoUrl, description, isActive) {
    const form = document.getElementById('editBrandForm');
    form.action = `/admin/brands/${id}`;
    document.getElementById('edit-brand-name').value = name;
    document.getElementById('edit-brand-logo').value = logoUrl || '';
    document.getElementById('edit-brand-description').value = description || '';
    document.getElementById('edit-brand-active').checked = (isActive == 1);

    const modal = new bootstrap.Modal(document.getElementById('editBrandModal'));
    modal.show();
}
</script>
@endsection
