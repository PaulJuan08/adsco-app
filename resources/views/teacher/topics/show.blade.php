@extends('layouts.teacher')

@section('title', 'Topic Details - ' . $topic->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topic-show.css') }}">
@endpush

@section('content')
    <!-- Topic Details Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-file-alt card-icon"></i>
                <h2 class="card-title">Topic Details</h2>
            </div>
            <div class="top-actions">
                <!-- Edit Button -->
                <a href="{{ route('teacher.topics.edit', Crypt::encrypt($topic->id)) }}" class="top-action-btn">
                    <i class="fas fa-edit"></i> Edit
                </a>
                
                <!-- Delete Button -->
                <form action="{{ route('teacher.topics.destroy', Crypt::encrypt($topic->id)) }}" method="POST" id="deleteForm" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="top-action-btn delete-btn" id="deleteButton">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </form>
                
                <!-- Back Button -->
                <a href="{{ route('teacher.topics.index') }}" class="top-action-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Topic Preview -->
            <div class="topic-preview">
                <div class="topic-preview-avatar">
                    {{ strtoupper(substr($topic->title, 0, 1)) }}
                </div>
                <div class="topic-preview-title">{{ $topic->title }}</div>
                <div class="topic-preview-meta">
                    <div class="topic-preview-badge {{ $topic->is_published ? 'published' : 'draft' }}">
                        <i class="fas {{ $topic->is_published ? 'fa-check-circle' : 'fa-clock' }}"></i>
                        {{ $topic->is_published ? 'Published' : 'Draft' }}
                    </div>
                    <span class="topic-preview-id">
                        <i class="fas fa-hashtag"></i> ID: {{ $topic->id }}
                    </span>
                </div>
            </div>

            <!-- Display success/error messages -->
            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
            @endif

            <!-- Two Column Layout -->
            <div class="two-column-layout">
                <!-- Left Column - Main Content -->
                <div class="form-column">
                    <!-- Topic Description -->
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="fas fa-align-left"></i> Topic Description
                        </div>
                        
                        <div class="description-box">
                            {{ $topic->description ?: 'No description provided for this topic.' }}
                        </div>
                    </div>

                    <!-- Resources Section -->
                    @if($topic->pdf_file || $topic->video_link || $topic->attachment)
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="fas fa-paperclip"></i> Resources & Attachments
                        </div>
                        
                        <!-- PDF Document -->
                        @if($topic->pdf_file)
                        <div class="resource-card">
                            <div class="resource-header">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div class="file-icon file-pdf">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <div>
                                        <div class="resource-title">PDF Document</div>
                                        <div style="font-size: 0.75rem; color: #718096;">
                                            <i class="fas fa-file"></i>
                                            {{ basename($topic->pdf_file) }}
                                        </div>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button onclick="openPdfModal('{{ asset($topic->pdf_file) }}')" 
                                            class="resource-action-btn primary">
                                        <i class="fas fa-eye"></i> View PDF
                                    </button>
                                    <a href="{{ asset($topic->pdf_file) }}" download 
                                       class="resource-action-btn secondary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                            <div class="resource-content">
                                <div class="resource-description">
                                    PDF document associated with this topic.
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Video Link -->
                        @if($topic->video_link)
                        <div class="resource-card">
                            <div class="resource-header">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div class="file-icon file-video">
                                        <i class="fas fa-video"></i>
                                    </div>
                                    <div>
                                        <div class="resource-title">Video Content</div>
                                        <div style="font-size: 0.75rem; color: #718096;">
                                            <i class="fas fa-link"></i>
                                            @php
                                                $host = parse_url($topic->video_link, PHP_URL_HOST);
                                                echo $host ? str_replace('www.', '', $host) : 'Video Link';
                                            @endphp
                                        </div>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button onclick="openVideoModal('{{ $topic->video_link }}')" 
                                            class="resource-action-btn primary">
                                        <i class="fas fa-play"></i> Play Video
                                    </button>
                                    <a href="{{ $topic->video_link }}" target="_blank" 
                                       class="resource-action-btn secondary">
                                        <i class="fas fa-external-link-alt"></i> Open Link
                                    </a>
                                </div>
                            </div>
                            <div class="resource-content">
                                <div class="resource-description">
                                    <div style="color: #4a5568; margin-bottom: 0.5rem;">Video URL:</div>
                                    <div style="word-break: break-all; font-family: monospace; font-size: 0.8125rem; background: #f8fafc; padding: 0.625rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                                        {{ $topic->video_link }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Attachment -->
                        @if($topic->attachment)
                        <div class="resource-card">
                            <div class="resource-header">
                                @php
                                    // Helper functions for file icons
                                    function getFileIcon($url) {
                                        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
                                        $extension = strtolower($extension);
                                        
                                        switch($extension) {
                                            case 'pdf': return 'fas fa-file-pdf';
                                            case 'doc':
                                            case 'docx': return 'fas fa-file-word';
                                            case 'xls':
                                            case 'xlsx': return 'fas fa-file-excel';
                                            case 'ppt':
                                            case 'pptx': return 'fas fa-file-powerpoint';
                                            case 'jpg':
                                            case 'jpeg':
                                            case 'png':
                                            case 'gif': return 'fas fa-file-image';
                                            case 'zip':
                                            case 'rar': return 'fas fa-file-archive';
                                            case 'mp4':
                                            case 'webm':
                                            case 'ogg':
                                            case 'mov': return 'fas fa-file-video';
                                            case 'mp3':
                                            case 'wav':
                                            case 'ogg': return 'fas fa-file-audio';
                                            default: return 'fas fa-file-alt';
                                        }
                                    }
                                    
                                    function getFileColorClass($url) {
                                        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
                                        $extension = strtolower($extension);
                                        
                                        switch($extension) {
                                            case 'pdf': return 'file-pdf';
                                            case 'doc':
                                            case 'docx': return 'file-word';
                                            case 'xls':
                                            case 'xlsx': return 'file-excel';
                                            case 'ppt':
                                            case 'pptx': return 'file-powerpoint';
                                            case 'jpg':
                                            case 'jpeg':
                                            case 'png':
                                            case 'gif': return 'file-image';
                                            case 'zip':
                                            case 'rar': return 'file-zip';
                                            case 'mp4':
                                            case 'webm':
                                            case 'ogg':
                                            case 'mov': return 'file-video';
                                            default: return 'file-generic';
                                        }
                                    }
                                    
                                    function getFileType($url) {
                                        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
                                        $extension = strtolower($extension);
                                        
                                        switch($extension) {
                                            case 'pdf': return 'PDF Document';
                                            case 'doc': return 'Word Document (DOC)';
                                            case 'docx': return 'Word Document (DOCX)';
                                            case 'xls': return 'Excel Spreadsheet (XLS)';
                                            case 'xlsx': return 'Excel Spreadsheet (XLSX)';
                                            case 'ppt': return 'PowerPoint Presentation (PPT)';
                                            case 'pptx': return 'PowerPoint Presentation (PPTX)';
                                            case 'jpg':
                                            case 'jpeg': return 'JPEG Image';
                                            case 'png': return 'PNG Image';
                                            case 'gif': return 'GIF Image';
                                            case 'mp4': return 'MP4 Video';
                                            case 'webm': return 'WebM Video';
                                            case 'zip': return 'ZIP Archive';
                                            case 'rar': return 'RAR Archive';
                                            default: return 'File Attachment';
                                        }
                                    }
                                    
                                    $icon = getFileIcon($topic->attachment);
                                    $colorClass = getFileColorClass($topic->attachment);
                                    $fileType = getFileType($topic->attachment);
                                @endphp
                                
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div class="file-icon {{ $colorClass }}">
                                        <i class="{{ $icon }}"></i>
                                    </div>
                                    <div>
                                        <div class="resource-title">Additional Attachment</div>
                                        <div style="font-size: 0.75rem; color: #718096;">
                                            <i class="fas fa-paperclip"></i>
                                            {{ $fileType }}
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ $topic->attachment }}" target="_blank" 
                                       class="resource-action-btn primary">
                                        <i class="fas fa-external-link-alt"></i> Open File
                                    </a>
                                </div>
                            </div>
                            <div class="resource-content">
                                <div class="resource-description">
                                    <a href="{{ $topic->attachment }}" target="_blank" style="color: #667eea; text-decoration: none; word-break: break-all;">
                                        {{ basename($topic->attachment) }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Publish Button for Draft Topics -->
                    @if(!$topic->is_published)
                    <div class="publish-section">
                        <form action="{{ route('teacher.topics.publish', Crypt::encrypt($topic->id)) }}" method="POST" id="publishForm" style="display: inline-block;">
                            @csrf
                            <button type="submit" class="publish-btn" id="publishButton">
                                <i class="fas fa-upload"></i> Publish Topic
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

                <!-- Right Column - Sidebar -->
                <div class="sidebar-column">
                    <!-- Topic Information Card -->
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="fas fa-info-circle"></i> Topic Information
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-hashtag"></i> Topic ID</span>
                            <span class="info-value">#{{ $topic->id }}</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-calendar-alt"></i> Created</span>
                            <div style="text-align: right;">
                                <span class="info-value">{{ $topic->created_at->format('M d, Y') }}</span>
                                <div class="info-subvalue">{{ $topic->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-clock"></i> Last Updated</span>
                            <div style="text-align: right;">
                                <span class="info-value">{{ $topic->updated_at->format('M d, Y') }}</span>
                                <div class="info-subvalue">{{ $topic->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-check-circle"></i> Status</span>
                            <span class="info-value">
                                @if($topic->is_published)
                                    <span style="color: #48bb78;">Published</span>
                                @else
                                    <span style="color: #ed8936;">Draft</span>
                                @endif
                            </span>
                        </div>
                        
                        @php
                            $resources = 0;
                            if($topic->pdf_file) $resources++;
                            if($topic->video_link) $resources++;
                            if($topic->attachment) $resources++;
                        @endphp
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-paperclip"></i> Resources</span>
                            <span class="info-value">{{ $resources }} file(s)</span>
                        </div>
                    </div>
                    
                    <!-- Courses Card -->
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="fas fa-book"></i> Assigned Courses
                        </div>
                        
                        @if($topic->courses && $topic->courses->count() > 0)
                            <div style="margin-bottom: 0.75rem;">
                                <span class="info-label"><i class="fas fa-layer-group"></i> Total Courses</span>
                                <span class="info-value" style="display: block; margin-top: 0.25rem; font-size: 1.25rem;">{{ $topic->courses->count() }}</span>
                            </div>
                            <div style="margin-top: 0.5rem;">
                                @foreach($topic->courses as $course)
                                    <a href="{{ route('teacher.courses.show', Crypt::encrypt($course->id)) }}" 
                                       class="course-tag">
                                        {{ $course->course_code }}
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state" style="padding: 1.5rem 0.5rem;">
                                <i class="fas fa-book-open" style="font-size: 2rem;"></i>
                                <h3 style="margin-top: 0.5rem;">No Courses Assigned</h3>
                                <p style="font-size: 0.75rem;">This topic is not used in any courses yet.</p>
                                <a href="{{ route('teacher.topics.edit', Crypt::encrypt($topic->id)) }}" 
                                   style="display: inline-block; margin-top: 0.75rem; padding: 0.5rem 1rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px; text-decoration: none; font-size: 0.75rem; font-weight: 600;">
                                    <i class="fas fa-plus" style="margin-right: 0.375rem;"></i> Assign to Course
                                </a>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Quick Actions Card (Teacher-specific) -->
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="fas fa-bolt"></i> Quick Actions
                        </div>
                        
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <a href="{{ route('teacher.courses.index') }}" 
                               style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #2d3748; transition: all 0.2s ease;">
                                <div style="width: 32px; height: 32px; border-radius: 6px; background: rgba(102, 126, 234, 0.1); color: #667eea; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-book"></i>
                                </div>
                                <span style="flex: 1; font-weight: 500; font-size: 0.8125rem;">My Courses</span>
                                <i class="fas fa-chevron-right" style="color: #a0aec0; font-size: 0.75rem;"></i>
                            </a>
                            
                            <a href="{{ route('teacher.topics.create') }}" 
                               style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #2d3748; transition: all 0.2s ease;">
                                <div style="width: 32px; height: 32px; border-radius: 6px; background: rgba(72, 187, 120, 0.1); color: #48bb78; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <span style="flex: 1; font-weight: 500; font-size: 0.8125rem;">Create New Topic</span>
                                <i class="fas fa-chevron-right" style="color: #a0aec0; font-size: 0.75rem;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Preview Modal -->
    <div class="pdf-modal-overlay" id="pdfModal">
        <div class="pdf-modal-container">
            <div class="modal-header">
                <h3 style="margin: 0; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-file-pdf"></i> PDF Preview
                </h3>
                <button class="modal-close" onclick="closePdfModal()">&times;</button>
            </div>
            <div style="flex: 1; position: relative; background: #f8fafc;">
                <iframe id="pdfIframe" style="width: 100%; height: 100%; border: none;"></iframe>
                <div id="pdfLoading" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; display: none;">
                    <div style="width: 50px; height: 50px; border: 4px solid #e2e8f0; border-top: 4px solid #667eea; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
                    <p style="color: #4a5568;">Loading PDF...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Video Player Modal -->
    <div class="video-modal-overlay" id="videoModal">
        <div class="video-modal-container">
            <div class="modal-header">
                <h3 style="margin: 0; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-video"></i> Video Player
                </h3>
                <button class="modal-close" onclick="closeVideoModal()">&times;</button>
            </div>
            <div class="video-player-container">
                <iframe id="videoPlayer" class="video-player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                <div id="videoLoading" class="video-loading" style="display: none;">
                    <div class="video-loading-spinner"></div>
                    <p>Loading video...</p>
                </div>
            </div>
            <div style="padding: 1rem; background: #1a202c; color: white; font-size: 0.8125rem; border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                <div id="videoUrlDisplay" style="word-break: break-all; opacity: 0.8;"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle publish button click with SweetAlert2
        const publishButton = document.getElementById('publishButton');
        if (publishButton) {
            publishButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Publish Topic?',
                    text: 'Once published, this topic will be visible to students.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#48bb78',
                    cancelButtonColor: '#a0aec0',
                    confirmButtonText: 'Yes, Publish',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        publishButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publishing...';
                        publishButton.disabled = true;
                        document.getElementById('publishForm').submit();
                    }
                });
            });
        }
        
        // Handle delete button click with SweetAlert2
        const deleteButton = document.getElementById('deleteButton');
        if (deleteButton) {
            deleteButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Delete Topic?',
                    text: 'This action cannot be undone. All topic data will be permanently removed.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f56565',
                    cancelButtonColor: '#a0aec0',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                        deleteButton.disabled = true;
                        document.getElementById('deleteForm').submit();
                    }
                });
            });
        }
        
        // Show notifications from session
        @if(session('success'))
            showNotification('{{ session('success') }}', 'success');
        @endif
        
        @if(session('error'))
            showNotification('{{ session('error') }}', 'error');
        @endif
        
        // Setup modal event listeners
        setupModals();
    });
    
    // Toast Notification Function
    function showNotification(message, type = 'info') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            icon: type,
            title: message,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }

    // PDF Modal Functions
    function openPdfModal(pdfUrl) {
        const modal = document.getElementById('pdfModal');
        const iframe = document.getElementById('pdfIframe');
        const loading = document.getElementById('pdfLoading');
        
        modal.style.display = 'flex';
        loading.style.display = 'block';
        
        iframe.src = pdfUrl;
        
        iframe.onload = () => {
            loading.style.display = 'none';
        };
        
        iframe.onerror = () => {
            loading.style.display = 'none';
            showNotification('Failed to load PDF. Please try downloading the file instead.', 'error');
            closePdfModal();
        };
    }

    function closePdfModal() {
        const modal = document.getElementById('pdfModal');
        const iframe = document.getElementById('pdfIframe');
        const loading = document.getElementById('pdfLoading');
        
        modal.style.display = 'none';
        iframe.src = '';
        loading.style.display = 'none';
    }

    // Video Modal Functions
    function openVideoModal(videoUrl) {
        const modal = document.getElementById('videoModal');
        const player = document.getElementById('videoPlayer');
        const loading = document.getElementById('videoLoading');
        const urlDisplay = document.getElementById('videoUrlDisplay');
        
        modal.style.display = 'flex';
        loading.style.display = 'block';
        
        const embedUrl = getEmbedUrl(videoUrl);
        
        if (embedUrl) {
            player.src = embedUrl;
            player.style.display = 'block';
            urlDisplay.textContent = 'Source: ' + videoUrl;
        } else {
            loading.innerHTML = `
                <div style="color: white; text-align: center;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 1rem; color: #ed8936;"></i>
                    <p>This video cannot be embedded directly.</p>
                    <a href="${videoUrl}" target="_blank" style="color: #667eea; text-decoration: underline; display: inline-block; margin-top: 0.5rem;">
                        Open video in new tab
                    </a>
                </div>
            `;
            player.style.display = 'none';
            urlDisplay.textContent = 'Source: ' + videoUrl;
        }
        
        player.onload = () => {
            loading.style.display = 'none';
        };
        
        player.onerror = () => {
            loading.innerHTML = `
                <div style="color: white; text-align: center;">
                    <i class="fas fa-exclamation-circle" style="font-size: 3rem; margin-bottom: 1rem; color: #f56565;"></i>
                    <p>Failed to load video.</p>
                    <a href="${videoUrl}" target="_blank" style="color: #667eea; text-decoration: underline; display: inline-block; margin-top: 0.5rem;">
                        Try opening in new tab
                    </a>
                </div>
            `;
            player.style.display = 'none';
        };
    }

    function getEmbedUrl(url) {
        // YouTube
        const youtubeRegex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i;
        const youtubeMatch = url.match(youtubeRegex);
        if (youtubeMatch) {
            return `https://www.youtube.com/embed/${youtubeMatch[1]}?autoplay=1&rel=0`;
        }
        
        // Vimeo
        const vimeoRegex = /vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|video\/|)(\d+)(?:|\/\?)/i;
        const vimeoMatch = url.match(vimeoRegex);
        if (vimeoMatch) {
            return `https://player.vimeo.com/video/${vimeoMatch[1]}?autoplay=1`;
        }
        
        // Direct video file (mp4, webm, etc.)
        if (url.match(/\.(mp4|webm|ogg|mov|avi|wmv|flv|mkv)(\?.*)?$/i)) {
            return url;
        }
        
        return null;
    }

    function closeVideoModal() {
        const modal = document.getElementById('videoModal');
        const player = document.getElementById('videoPlayer');
        const loading = document.getElementById('videoLoading');
        
        modal.style.display = 'none';
        player.src = '';
        loading.style.display = 'none';
        loading.innerHTML = '<div class="video-loading-spinner"></div><p>Loading video...</p>';
    }

    function setupModals() {
        // Close PDF modal when clicking outside
        document.getElementById('pdfModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePdfModal();
            }
        });

        // Close video modal when clicking outside
        document.getElementById('videoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeVideoModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePdfModal();
                closeVideoModal();
            }
        });
    }
</script>
@endpush