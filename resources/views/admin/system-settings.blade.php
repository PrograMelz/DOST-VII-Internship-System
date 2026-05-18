@extends('layouts.admin')

@section('title', 'System Settings')

@section('content')
    <div class="content-header">
        <h1><i class="bi bi-gear"></i> System Settings</h1>
        <p>Configure system-wide settings and preferences</p>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">System Configuration</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.system-settings.update') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="system_short_name" class="form-label">System Short Name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="system_short_name"
                                    name="system_short_name"
                                    value="{{ old('system_short_name', optional($settings)->system_short_name) }}"
                                    maxlength="20"
                                    required
                                >
                                <div class="form-text">Short code used across the system.</div>
                            </div>

                            <div class="col-md-8">
                                <label for="system_long_name" class="form-label">System Long Name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="system_long_name"
                                    name="system_long_name"
                                    value="{{ old('system_long_name', optional($settings)->system_long_name) }}"
                                    maxlength="150"
                                    required
                                >
                                <div class="form-text">Full system title shown in pages and emails.</div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3">Color Scheme</h6>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="primary_color" class="form-label">Primary Color</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <input
                                            type="color"
                                            id="primary_color_picker"
                                            value="{{ old('primary_color', optional($settings)->primary_color ?? '#2E86C1') }}"
                                            style="border: none; background: transparent; padding: 0; width: 2rem;"
                                            oninput="document.getElementById('primary_color').value = this.value"
                                        >
                                    </span>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="primary_color"
                                        name="primary_color"
                                        value="{{ old('primary_color', optional($settings)->primary_color ?? '#2E86C1') }}"
                                        placeholder="#RRGGBB"
                                        required
                                    >
                                </div>
                                <div class="form-text">Main accent color (hex format, e.g. #2E86C1).</div>
                            </div>

                            <div class="col-md-4">
                                <label for="secondary_color" class="form-label">Secondary Color</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <input
                                            type="color"
                                            id="secondary_color_picker"
                                            value="{{ old('secondary_color', optional($settings)->secondary_color ?? '#1B4F72') }}"
                                            style="border: none; background: transparent; padding: 0; width: 2rem;"
                                            oninput="document.getElementById('secondary_color').value = this.value"
                                        >
                                    </span>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="secondary_color"
                                        name="secondary_color"
                                        value="{{ old('secondary_color', optional($settings)->secondary_color ?? '#1B4F72') }}"
                                        placeholder="#RRGGBB"
                                    >
                                </div>
                                <div class="form-text">Optional secondary accent color.</div>
                            </div>

                            <div class="col-md-4">
                                <label for="button_color" class="form-label">Button Color</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <input
                                            type="color"
                                            id="button_color_picker"
                                            value="{{ old('button_color', optional($settings)->button_color ?? '#3498DB') }}"
                                            style="border: none; background: transparent; padding: 0; width: 2rem;"
                                            oninput="document.getElementById('button_color').value = this.value"
                                        >
                                    </span>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="button_color"
                                        name="button_color"
                                        value="{{ old('button_color', optional($settings)->button_color ?? '#3498DB') }}"
                                        placeholder="#RRGGBB"
                                    >
                                </div>
                                <div class="form-text">Default color for primary action buttons.</div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3">Text Colors</h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="heading_text_color" class="form-label">Heading Text Color</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <input
                                            type="color"
                                            id="heading_text_color_picker"
                                            value="{{ old('heading_text_color', optional($settings)->heading_text_color ?? '#333333') }}"
                                            style="border: none; background: transparent; padding: 0; width: 2rem;"
                                            oninput="document.getElementById('heading_text_color').value = this.value"
                                        >
                                    </span>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="heading_text_color"
                                        name="heading_text_color"
                                        value="{{ old('heading_text_color', optional($settings)->heading_text_color ?? '#333333') }}"
                                        placeholder="#RRGGBB"
                                    >
                                </div>
                                <div class="form-text">Used for main page headings.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="body_text_color" class="form-label">Body Text Color</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <input
                                            type="color"
                                            id="body_text_color_picker"
                                            value="{{ old('body_text_color', optional($settings)->body_text_color ?? '#333333') }}"
                                            style="border: none; background: transparent; padding: 0; width: 2rem;"
                                            oninput="document.getElementById('body_text_color').value = this.value"
                                        >
                                    </span>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="body_text_color"
                                        name="body_text_color"
                                        value="{{ old('body_text_color', optional($settings)->body_text_color ?? '#333333') }}"
                                        placeholder="#RRGGBB"
                                    >
                                </div>
                                <div class="form-text">Used for normal paragraph and label text.</div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3">Branding</h6>

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="system_logo" class="form-label">System Logo</label>
                                <input
                                    type="file"
                                    class="form-control"
                                    id="system_logo"
                                    name="system_logo"
                                    accept="image/*"
                                >
                                <div class="form-text">
                                    Upload a logo image (JPEG, PNG, GIF, SVG, max 2 MB). Leave empty to keep the current logo.
                                </div>
                            </div>

                            @if (!empty(optional($settings)->system_logo))
                                <div class="col-md-4 d-flex align-items-end">
                                    <div>
                                        <label class="form-label d-block">Current Logo Preview</label>
                                        <img src="{{ asset($settings->system_logo) }}" alt="System Logo" class="img-fluid border rounded" style="max-height: 80px;">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection