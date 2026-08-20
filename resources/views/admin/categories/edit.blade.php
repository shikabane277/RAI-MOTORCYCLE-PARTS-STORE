@extends('layouts.admin')
@section('title', 'Edit Category — ' . $category->name)
@section('page-title', 'Edit Category')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="dark-card p-4">
            <h2 style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700;color:var(--mb-heading);" class="mb-3">
                Edit Category: {{ $category->name }}
            </h2>

            <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label">Category Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Icon Emoji</label>
                        <input type="text" name="icon" class="form-control" value="{{ old('icon', $category->icon) }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Parent Category</label>
                        <select name="parent_id" class="form-select">
                            <option value="">— Top Level Category —</option>
                            @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $category->sort_order) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ $category->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active" style="color:var(--mb-text);">Active &amp; Visible in Storefront</label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-gold">Save Changes</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-dark-surface">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
