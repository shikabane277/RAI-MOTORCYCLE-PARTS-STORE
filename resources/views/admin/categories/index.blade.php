@extends('layouts.admin')
@section('title', 'Categories')
@section('page-title', 'Category Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <p style="color:var(--mb-muted);font-size:.9rem;" class="mb-0">
            Create, edit, and organize product categories. Configured categories appear live in the <strong>Shop All</strong> header menu and home page.
        </p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-gold">
        <i class="bi bi-plus-lg me-1"></i> Add New Category
    </a>
</div>

<div class="dark-card overflow-hidden">
    <div class="table-responsive">
        <table class="table table-dark-custom mb-0">
            <thead>
                <tr>
                    <th>Icon</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Parent Category</th>
                    <th>Sort Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td style="font-size:1.4rem;">{{ $cat->icon ?? '🔩' }}</td>
                    <td style="font-weight:700;color:var(--mb-heading);">{{ $cat->name }}</td>
                    <td style="font-size:.85rem;color:var(--mb-gold);font-family:monospace;">{{ $cat->slug }}</td>
                    <td style="font-size:.85rem;color:var(--mb-muted);">{{ $cat->parent?->name ?? '— Top Level —' }}</td>
                    <td style="font-size:.85rem;">{{ $cat->sort_order ?? 0 }}</td>
                    <td>
                        @if($cat->is_active)
                            <span class="badge bg-success" style="font-size:.75rem;">Active</span>
                        @else
                            <span class="badge bg-secondary" style="font-size:.75rem;">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-dark-surface btn-sm" title="Edit Category"><i class="bi bi-pencil-square"></i> Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Delete category {{ $cat->name }}?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-dark-surface btn-sm text-danger" title="Delete Category"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4" style="color:var(--mb-muted);">No categories found. Click 'Add New Category' above to create one.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
