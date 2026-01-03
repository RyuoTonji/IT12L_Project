@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Edit Dish</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="productForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="branch_id" class="form-label">Branch *</label>
                            <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', $product->branch_id) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category *</label>
                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Dish Name *</label>
                            <input type="text"
                                name="name"
                                id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $product->name) }}"
                                required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description"
                                id="description"
                                class="form-control @error('description') is-invalid @enderror"
                                rows="3"
                                placeholder="Enter dish description, ingredients, or special notes">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price (₱) *</label>
                            <input type="number"
                                name="price"
                                id="price"
                                class="form-control @error('price') is-invalid @enderror"
                                value="{{ old('price', $product->price) }}"
                                step="0.01"
                                min="0"
                                required>
                            @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ENHANCED IMAGE UPLOAD SECTION WITH EXISTING IMAGE -->
                        <div class="mb-3">
                            <label for="image" class="form-label">
                                {{ $product->image ? 'Change Image' : 'Dish Image' }}
                            </label>
                            <input type="file"
                                name="image"
                                id="image"
                                class="form-control d-none @error('image') is-invalid @enderror"
                                accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">

                            <!-- Image Preview Container -->
                            <div class="image-upload-wrapper" id="imageUploadWrapper">
                                <div class="image-preview-box {{ $product->image ? 'has-image' : '' }}" id="imagePreviewBox">
                                    <div class="upload-placeholder" style="{{ $product->image ? 'display: none;' : '' }}">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p class="mb-1">Click to upload image</p>
                                        <small class="text-muted d-block">or drag and drop</small>
                                    </div>
                                    <img id="imagePreview"
                                        src="{{ $product->image ? asset('images/' . $product->image) : '' }}"
                                        alt="Preview"
                                        class="preview-image {{ $product->image ? '' : 'd-none' }}">
                                    <button type="button"
                                        class="btn btn-sm btn-danger remove-image-btn {{ $product->image ? '' : 'd-none' }}"
                                        id="removeImageBtn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @if($product->image)
                                <input type="hidden" name="remove_image" id="removeImageFlag" value="0">
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-info-circle"></i> Current image will be replaced if you upload a new one
                                </small>
                                @endif
                            </div>

                            @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Max size: 10MB. Supported formats: JPG, PNG, GIF, WebP
                            </small>

                            <!-- Client-side validation error -->
                            <div class="alert alert-danger mt-2 d-none" id="imageError"></div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox"
                                    name="is_available"
                                    id="is_available"
                                    class="form-check-input"
                                    value="1"
                                    {{ old('is_available', $product->is_available) ? 'checked' : '' }}>
                                <label for="is_available" class="form-check-label">
                                    Available for purchase
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Update Dish
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Enhanced Image Upload Styles */
    .image-upload-wrapper {
        margin-bottom: 0.5rem;
    }

    .image-preview-box {
        position: relative;
        width: 100%;
        height: 250px;
        border: 3px dashed #dee2e6;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        overflow: hidden;
        background: #f8f9fa;
    }

    .image-preview-box:hover {
        border-color: #A52A2A;
        background: #fff;
    }

    .image-preview-box.has-image {
        border-style: solid;
        border-color: #A52A2A;
        background: white;
    }

    .upload-placeholder {
        text-align: center;
        color: #6c757d;
        pointer-events: none;
    }

    .upload-placeholder i {
        font-size: 3rem;
        margin-bottom: 0.5rem;
        color: #A52A2A;
    }

    .upload-placeholder p {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.25rem;
    }

    .preview-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 10px;
    }

    .remove-image-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .remove-image-btn:hover {
        transform: scale(1.1);
    }

    /* Drag and Drop States */
    .image-preview-box.drag-over {
        border-color: #28a745;
        background: #d4edda;
    }

    /* Alert styling */
    #imageError {
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('image');
        const imagePreviewBox = document.getElementById('imagePreviewBox');
        const imagePreview = document.getElementById('imagePreview');
        const removeImageBtn = document.getElementById('removeImageBtn');
        const imageError = document.getElementById('imageError');
        const uploadPlaceholder = imagePreviewBox.querySelector('.upload-placeholder');
        const hasExistingImage = {
            {
                $product - > image ? 'true' : 'false'
            }
        };

        // Click to trigger file input
        imagePreviewBox.addEventListener('click', function(e) {
            if (!e.target.closest('.remove-image-btn')) {
                imageInput.click();
            }
        });

        // Handle file selection
        imageInput.addEventListener('change', function(e) {
            handleFileSelection(e.target.files[0]);
        });

        // Drag and drop functionality
        imagePreviewBox.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add('drag-over');
        });

        imagePreviewBox.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('drag-over');
        });

        imagePreviewBox.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('drag-over');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                // Set the file to the input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                imageInput.files = dataTransfer.files;

                handleFileSelection(file);
            }
        });

        // Handle file validation and preview
        function handleFileSelection(file) {
            if (!file) return;

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                showError('Invalid file type. Please upload JPG, PNG, GIF, or WebP.');
                imageInput.value = '';
                return;
            }

            // Validate file size (10MB = 10485760 bytes)
            const maxSize = 10 * 1024 * 1024;
            if (file.size > maxSize) {
                showError('File size exceeds 10MB limit. Please choose a smaller file.');
                imageInput.value = '';
                return;
            }

            // Clear any previous errors
            hideError();

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.classList.remove('d-none');
                imagePreviewBox.classList.add('has-image');
                uploadPlaceholder.style.display = 'none';
                removeImageBtn.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        }

        // Remove image
        removeImageBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            resetImageUpload();
        });

        function resetImageUpload() {
            imageInput.value = '';
            imagePreview.src = '';
            imagePreview.classList.add('d-none');
            imagePreviewBox.classList.remove('has-image');
            uploadPlaceholder.style.display = 'block';
            removeImageBtn.classList.add('d-none');
            hideError();
        }

        function showError(message) {
            imageError.textContent = message;
            imageError.classList.remove('d-none');
        }

        function hideError() {
            imageError.classList.add('d-none');
        }

        // Form validation
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
</script>
@endpush
@endsection