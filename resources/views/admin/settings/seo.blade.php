@extends('layouts.admin')

@section('title', 'SEO Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">🔍 SEO Settings</h4>
                    <p class="card-subtitle text-muted">Configure SEO settings for your landing page and website</p>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.settings.seo.update') }}" method="POST" id="seoSettingsForm">
                        @csrf
                        @method('PUT')

                        <!-- Basic SEO Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">📝 Basic SEO Settings</h5>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="seo_meta_title" class="form-label">Meta Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="seo_meta_title" name="seo_meta_title" 
                                           value="{{ $seoSettings['seo_meta_title'] }}" maxlength="60" required>
                                    <div class="form-text">
                                        <span id="title-count">0</span>/60 characters
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="seo_meta_description" class="form-label">Meta Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="seo_meta_description" name="seo_meta_description" 
                                              rows="3" maxlength="160" required>{{ $seoSettings['seo_meta_description'] }}</textarea>
                                    <div class="form-text">
                                        <span id="desc-count">0</span>/160 characters
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="seo_meta_keywords" class="form-label">Meta Keywords</label>
                                    <input type="text" class="form-control" id="seo_meta_keywords" name="seo_meta_keywords" 
                                           value="{{ $seoSettings['seo_meta_keywords'] }}" maxlength="255">
                                    <div class="form-text">Comma-separated keywords (optional but recommended)</div>
                                </div>
                            </div>
                        </div>

                        <!-- Open Graph Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">📘 Open Graph (Facebook, LinkedIn)</h5>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="seo_og_title" class="form-label">OG Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="seo_og_title" name="seo_og_title" 
                                           value="{{ $seoSettings['seo_og_title'] }}" maxlength="60" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="seo_og_description" class="form-label">OG Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="seo_og_description" name="seo_og_description" 
                                              rows="3" maxlength="160" required>{{ $seoSettings['seo_og_description'] }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="seo_og_image" class="form-label">OG Image URL</label>
                                    <input type="url" class="form-control" id="seo_og_image" name="seo_og_image" 
                                           value="{{ $seoSettings['seo_og_image'] }}" placeholder="https://example.com/image.jpg">
                                    <div class="form-text">Recommended size: 1200x630 pixels</div>
                                </div>
                            </div>
                        </div>

                        <!-- Twitter Card Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">🐦 Twitter Card Settings</h5>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="seo_twitter_title" class="form-label">Twitter Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="seo_twitter_title" name="seo_twitter_title" 
                                           value="{{ $seoSettings['seo_twitter_title'] }}" maxlength="60" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="seo_twitter_description" class="form-label">Twitter Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="seo_twitter_description" name="seo_twitter_description" 
                                              rows="3" maxlength="160" required>{{ $seoSettings['seo_twitter_description'] }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="seo_twitter_image" class="form-label">Twitter Image URL</label>
                                    <input type="url" class="form-control" id="seo_twitter_image" name="seo_twitter_image" 
                                           value="{{ $seoSettings['seo_twitter_image'] }}" placeholder="https://example.com/twitter-image.jpg">
                                    <div class="form-text">Recommended size: 1200x600 pixels</div>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced SEO Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">⚙️ Advanced SEO Settings</h5>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="seo_canonical_url" class="form-label">Canonical URL <span class="text-danger">*</span></label>
                                    <input type="url" class="form-control" id="seo_canonical_url" name="seo_canonical_url" 
                                           value="{{ $seoSettings['seo_canonical_url'] }}" required>
                                    <div class="form-text">Your main website URL (e.g., https://barimanager.com)</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="seo_google_analytics" class="form-label">Google Analytics Code</label>
                                    <input type="text" class="form-control" id="seo_google_analytics" name="seo_google_analytics" 
                                           value="{{ $seoSettings['seo_google_analytics'] }}" placeholder="G-XXXXXXXXXX">
                                    <div class="form-text">Google Analytics 4 measurement ID</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="seo_facebook_pixel" class="form-label">Facebook Pixel ID</label>
                                    <input type="text" class="form-control" id="seo_facebook_pixel" name="seo_facebook_pixel" 
                                           value="{{ $seoSettings['seo_facebook_pixel'] }}" placeholder="123456789012345">
                                    <div class="form-text">Facebook Pixel ID for tracking</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="seo_schema_org" class="form-label">JSON-LD Schema</label>
                                    <textarea class="form-control" id="seo_schema_org" name="seo_schema_org" 
                                              rows="8" placeholder='{"@context":"https://schema.org","@type":"SoftwareApplication",...}'>{{ $seoSettings['seo_schema_org'] }}</textarea>
                                    <div class="form-text">Structured data for rich snippets (JSON-LD format)</div>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Features Toggle -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">🎛️ SEO Features</h5>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="seo_breadcrumb_enabled" name="seo_breadcrumb_enabled" value="1" 
                                           {{ $seoSettings['seo_breadcrumb_enabled'] == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="seo_breadcrumb_enabled">
                                        Enable Breadcrumb Navigation
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="seo_sitemap_enabled" name="seo_sitemap_enabled" value="1" 
                                           {{ $seoSettings['seo_sitemap_enabled'] == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="seo_sitemap_enabled">
                                        Enable Sitemap Generation
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="seo_hreflang_enabled" name="seo_hreflang_enabled" value="1" 
                                           {{ $seoSettings['seo_hreflang_enabled'] == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="seo_hreflang_enabled">
                                        Enable Hreflang Tags
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="seo_lazy_loading_enabled" name="seo_lazy_loading_enabled" value="1" 
                                           {{ $seoSettings['seo_lazy_loading_enabled'] == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="seo_lazy_loading_enabled">
                                        Enable Image Lazy Loading
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="seo_minify_enabled" name="seo_minify_enabled" value="1" 
                                           {{ $seoSettings['seo_minify_enabled'] == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="seo_minify_enabled">
                                        Enable CSS/JS Minification
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="seo_compression_enabled" name="seo_compression_enabled" value="1" 
                                           {{ $seoSettings['seo_compression_enabled'] == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="seo_compression_enabled">
                                        Enable Gzip Compression
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Robots.txt Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">🤖 Robots.txt Configuration</h5>
                                <div class="mb-3">
                                    <label for="seo_robots_txt" class="form-label">Robots.txt Content</label>
                                    <textarea class="form-control" id="seo_robots_txt" name="seo_robots_txt" 
                                              rows="8">{{ $seoSettings['seo_robots_txt'] }}</textarea>
                                    <div class="form-text">Configure search engine crawling rules</div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Save SEO Settings
                                        </button>
                                        <button type="button" class="btn btn-info ms-2" id="previewSeo">
                                            <i class="fas fa-eye"></i> Preview SEO
                                        </button>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-success" onclick="generateSitemap()">
                                            <i class="fas fa-sitemap"></i> Generate Sitemap
                                        </button>
                                        <button type="button" class="btn btn-warning ms-2" onclick="generateRobotsTxt()">
                                            <i class="fas fa-robot"></i> Generate Robots.txt
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SEO Preview Modal -->
<div class="modal fade" id="seoPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🔍 SEO Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="seoPreviewContent">
                    <!-- Preview content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counters
    const titleInput = document.getElementById('seo_meta_title');
    const descInput = document.getElementById('seo_meta_description');
    const titleCount = document.getElementById('title-count');
    const descCount = document.getElementById('desc-count');

    function updateCount(input, counter) {
        counter.textContent = input.value.length;
        if (input.value.length > input.maxLength * 0.9) {
            counter.style.color = 'red';
        } else {
            counter.style.color = 'inherit';
        }
    }

    titleInput.addEventListener('input', () => updateCount(titleInput, titleCount));
    descInput.addEventListener('input', () => updateCount(descInput, descCount));

    // Initialize counts
    updateCount(titleInput, titleCount);
    updateCount(descInput, descCount);

    // SEO Preview
    document.getElementById('previewSeo').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('seoSettingsForm'));
        
        fetch('{{ route("admin.settings.seo.preview") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const preview = data.preview;
                const previewContent = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Google Search Preview</h6>
                            <div class="border p-3 bg-light rounded">
                                <div class="text-primary fw-bold">${preview.meta_title}</div>
                                <div class="text-success">${preview.canonical_url}</div>
                                <div class="text-muted small">${preview.meta_description}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Social Media Preview</h6>
                            <div class="border p-3 bg-light rounded">
                                <div class="fw-bold">${preview.og_title}</div>
                                <div class="text-muted small">${preview.og_description}</div>
                                <div class="text-info small">Twitter: ${preview.twitter_title}</div>
                            </div>
                        </div>
                    </div>
                `;
                document.getElementById('seoPreviewContent').innerHTML = previewContent;
                new bootstrap.Modal(document.getElementById('seoPreviewModal')).show();
            } else {
                alert('Error loading preview: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading preview');
        });
    });
});

function generateSitemap() {
    if (confirm('Generate sitemap.xml file?')) {
        fetch('{{ route("admin.settings.seo.sitemap") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.text())
        .then(() => {
            alert('Sitemap generated successfully!');
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error generating sitemap');
        });
    }
}

function generateRobotsTxt() {
    if (confirm('Generate robots.txt file?')) {
        fetch('{{ route("admin.settings.seo.robots") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.text())
        .then(() => {
            alert('Robots.txt generated successfully!');
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error generating robots.txt');
        });
    }
}
</script>
@endpush 