@extends('layouts.admin.app')

@section('title', translate('theme_settings'))

@section('styles')
<style>
    .color-circle {
        width: 48px;
        height: 48px;
        border: none;
        border-radius: 50%;
        padding: 0;
        cursor: pointer;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .color-circle:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .color-circle::-webkit-color-swatch-wrapper {
        padding: 0;
    }

    .color-circle::-webkit-color-swatch {
        border: none;
        border-radius: 50%;
    }

    .form-label {
        font-weight: 500;
        color: #4a5568;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: none;
    }

    .card-header {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 1.5rem;
    }

    .card-body {
        padding: 2rem;
    }

    .btn-primary {
        background-color: #2563eb;
        border-color: #2563eb;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #1e40af;
        border-color: #1e40af;
    }

    .form-control, .js-select2-custom {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        padding: 0.75rem;
        transition: border-color 0.2s ease;
    }

    .form-control:focus, .js-select2-custom:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .page-header-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.5rem;
        font-weight: 600;
        color: #1a202c;
    }
</style>
@endsection

@section('content')
<div class="content container-fluid py-6">
    <div class="page-header mb-4">
        <h1 class="page-header-title">
            <i class="tio-brush me-2"></i> {{ translate('theme_settings') }}
        </h1>
        @include('admin-views.system-setup.system-settings-inline-menu')
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 text-primary">{{ translate('Theme Customization') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.system-setup.theme-settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="google_font" class="form-label">
                                {{ translate('font_type') }}
                            </label>
                                <select class="custom-select image-var-select" id="google_font" name="google_font">
                                    @foreach ($fonts as $font)
                                        <option 
                                            value="{{ $font }}" 
                                            style="font-family: '{{ $font }}', sans-serif;" 
                                            @selected($selectedFont == $font)>
                                            {{ $font }}
                                        </option>
                                    @endforeach
                                </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="color_theme" class="form-label">
                                {{ translate('Color Theme') }}
                            </label>
                            <div class="d-flex align-items-center gap-3">
                                <input type="color" id="color_theme" name="color_theme"
                                    value="{{ $color_theme ?? '#2563eb' }}" class="color-circle">
                                <span class="text-muted">{{ $color_theme ?? '#2563eb' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 text-end">
                    <button type="submit" class="btn btn-primary">
                        {{ translate('save_information') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection