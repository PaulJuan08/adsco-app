@extends('layouts.teacher')

@section('title', 'Course Details - Teacher Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/course-show.css') }}">
@endpush

@section('content')
    <!-- Course Profile Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-book card-icon"></i>
                <h2 class="card-title">Course Details</h2>
            </div>
            <div class="top-actions">
                <!-- Edit Button -->
                <a href="{{ route('teacher.courses.edit', Crypt::encrypt($course->id)) }}" class="top-action-btn">
                    <i class="fas fa-edit"></i> Edit
                </a>
                
                <!-- Delete Button -->
                <form action="{{ route('teacher.courses.destroy', Crypt::encrypt($course->id)) }}" method="POST" id="deleteForm" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="top-action-btn delete-btn" id="deleteButton">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </form>
                
                <!-- Back Button -->
                <a href="{{ route('teacher.courses.index') }}" class="top-action-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Course Avatar & Basic Info -->
            <div class="course-avatar-section">
                <div class="course-details-avatar">
                    {{ strtoupper(substr($course->course_code, 0, 1)) }}
                </div>
                <h3 class="course-title">{{ $course->title }}</h3>
                <p class="course-code">{{ $course->course_code }}</p>
                
                <div class="course-status-container">
                    <div class="status-badge {{ $course->is_published ? 'status-published' : 'status-draft' }}">
                        <i class="fas {{ $course->is_published ? 'fa-check-circle' : 'fa-clock' }}"></i>
                        {{ $course->is_published ? 'Published' : 'Draft' }}
                    </div>
                </div>
            </div>
            
            <!-- Detailed Information -->
            <div class="details-grid">
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-info-circle"></i>
                        Course Information
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Title</div>
                        <div class="detail-value">{{ $course->title }}</div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Code</div>
                        <div class="detail-value">{{ $course->course_code }}</div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Credits</div>
                        <div class="detail-value">{{ $course->credits ?? 3 }} units</div>
                    </div>
                    
                    @if($course->department)
                    <div class="detail-row">
                        <div class="detail-label">Department</div>
                        <div class="detail-value">{{ $course->department }}</div>
                    </div>
                    @endif
                    
                    @if($course->semester)
                    <div class="detail-row">
                        <div class="detail-label">Semester</div>
                        <div class="detail-value">{{ ucfirst($course->semester) }}</div>
                    </div>
                    @endif

                    <div class="detail-row">
                        <div class="detail-label">Students Enrolled</div>
                        <div class="detail-value">
                            <span style="font-weight: 600; color: var(--primary);">{{ $course->students_count ?? 0 }}</span>
                            <span style="font-size: 0.875rem; color: var(--gray-500); margin-left: 0.5rem;">students</span>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-clock"></i>
                        Course Timeline
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Course ID</div>
                        <div class="detail-value">#{{ $course->id }}</div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Created</div>
                        <div class="detail-value">
                            {{ $course->created_at->format('M d, Y') }}
                            <div class="detail-subvalue">
                                <i class="fas fa-clock"></i> {{ $course->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Last Updated</div>
                        <div class="detail-value">
                            {{ $course->updated_at->format('M d, Y') }}
                            <div class="detail-subvalue">
                                <i class="fas fa-clock"></i> {{ $course->updated_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Topics Count</div>
                        <div class="detail-value">
                            <span style="font-weight: 600; color: var(--warning);">{{ $course->topics_count ?? 0 }}</span>
                            <span style="font-size: 0.875rem; color: var(--gray-500); margin-left: 0.5rem;">topics</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Course Description -->
            <div class="detail-section">
                <div class="detail-section-title">
                    <i class="fas fa-align-left"></i>
                    Course Description
                </div>
                
                <div class="description-box">
                    {{ $course->description ?: 'No description provided for this course.' }}
                </div>
            </div>

            <!-- Learning Outcomes (if exists) -->
            @if($course->learning_outcomes)
            <div class="detail-section">
                <div class="detail-section-title">
                    <i class="fas fa-tasks"></i>
                    Learning Outcomes
                </div>
                
                <div class="description-box" style="background: var(--info-light); border-left-color: var(--info);">
                    {{ $course->learning_outcomes }}
                </div>
            </div>
            @endif

            <!-- Publish Button for Draft Courses -->
            @if(!$course->is_published)
            <div style="margin-top: 1.5rem; text-align: center;">
                <form action="{{ route('teacher.courses.publish', Crypt::encrypt($course->id)) }}" method="POST" id="publishForm" style="display: inline-block;">
                    @csrf
                    <button type="submit" class="top-action-btn" id="publishButton" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); border: none; padding: 0.75rem 2rem;">
                        <i class="fas fa-upload"></i> Publish Course
                    </button>
                </form>
            </div>
            @endif
            
            <!-- Success/Error Messages -->
            @if(session('success'))
            <div class="message-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
            @endif
            
            @if(session('error'))
            <div class="message-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
            @endif
        </div>
    </div>

    <!-- Topics Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-list card-icon"></i>
                <h2 class="card-title">Course Topics</h2>
                <span class="topics-count-badge">{{ $course->topics_count ?? 0 }}</span>
            </div>
            <div class="top-actions">
                <button onclick="openAddTopicModal()" class="top-action-btn" style="background: rgba(79, 70, 229, 0.15); border: none;">
                    <i class="fas fa-plus"></i> Add Topics
                </button>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Search Bar -->
            @if($course->topics && $course->topics->count() > 0)
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search topics..." id="topicSearch">
            </div>
            @endif
            
            <!-- Topics List -->
            <div class="topics-section" id="topicsList">
                @if($course->topics && $course->topics->count() > 0)
                    @foreach($course->topics as $topic)
                    <div class="topic-card" id="topic-{{ $topic->id }}">
                        <div class="topic-header">
                            <div>
                                <div class="topic-title">{{ $topic->title }}</div>
                                <div style="font-size: 0.6875rem; color: #a0aec0;">
                                    <i class="fas fa-clock"></i>
                                    Added {{ $topic->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="action-dropdown">
                                <button class="action-btn-small" onclick="removeTopic({{ $topic->id }}, '{{ addslashes($topic->title) }}')">
                                    <i class="fas fa-times"></i> Remove
                                </button>
                            </div>
                        </div>
                        <div class="topic-content">
                            <div class="topic-description">
                                {{ $topic->description ?? 'No description provided.' }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h3>No Topics Yet</h3>
                    <p>Start by adding topics to this course</p>
                    <button onclick="openAddTopicModal()" style="margin-top: 1rem; padding: 0.5rem 1.25rem; background: linear-gradient(135deg, var(--primary) 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-size: 0.8125rem; font-weight: 600; cursor: pointer;">
                        <i class="fas fa-plus" style="margin-right: 0.375rem;"></i>Add First Topic
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Topic Modal -->
    <div class="modal-overlay" id="addTopicModal">
        <div class="modal-container">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="fas fa-plus-circle" style="margin-right: 0.5rem; color: var(--primary);"></i>
                    Add Topics to Course
                </div>
                <button class="modal-close" onclick="closeAddTopicModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="search-container" style="margin-bottom: 1rem;">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search available topics..." id="modalTopicSearch" onkeyup="searchTopics()">
                </div>
                
                <div id="availableTopicsList" class="topics-list">
                    <div class="loading" style="text-align: center; padding: 2rem;">
                        <div class="spinner" style="width: 32px; height: 32px; border: 3px solid #e2e8f0; border-top-color: var(--primary); border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 0.75rem;"></div>
                        <div style="color: #718096; font-size: 0.875rem;">Loading topics...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeAddTopicModal()">Cancel</button>
                <button class="btn btn-primary" onclick="addSelectedTopics()">
                    <i class="fas fa-check" style="margin-right: 0.375rem;"></i>
                    Add Selected
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle publish button click
        const publishButton = document.getElementById('publishButton');
        if (publishButton) {
            publishButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Publish Course?',
                    text: 'This course will be visible to enrolled students.',
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
        
        // Handle delete button click
        const deleteButton = document.getElementById('deleteButton');
        if (deleteButton) {
            deleteButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Delete Course?',
                    text: 'This action cannot be undone. All course data will be permanently removed.',
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
        
        @if(session('warning'))
            showNotification('{{ session('warning') }}', 'warning');
        @endif
    });

    // Topics management
    let selectedTopics = [];
    let allAvailableTopics = [];
    let currentCourseTopics = {!! $course->topics->pluck('id')->toJson() !!};

    // Get encrypted ID from PHP
    const encryptedCourseId = '{{ Crypt::encrypt($course->id) }}';

    function openAddTopicModal() {
        document.getElementById('addTopicModal').classList.add('active');
        loadAvailableTopics();
    }

    function closeAddTopicModal() {
        document.getElementById('addTopicModal').classList.remove('active');
        selectedTopics = [];
    }

    function getCsrfToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag && metaTag.content) {
            return metaTag.content;
        }
        return '{{ csrf_token() }}';
    }

    function loadAvailableTopics() {
        const routeUrl = `/teacher/courses/${encryptedCourseId}/available-topics`;
        
        fetch(routeUrl, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (data.error) throw new Error(data.message || data.error);
            
            allAvailableTopics = Array.isArray(data) ? data : [];
            renderAvailableTopics(allAvailableTopics);
        })
        .catch(error => {
            console.error('Error loading topics:', error);
            
            document.getElementById('availableTopicsList').innerHTML = `
                <div class="no-topics" style="text-align: center; padding: 2rem;">
                    <i class="fas fa-exclamation-circle" style="font-size: 2rem; color: #f56565; margin-bottom: 0.75rem;"></i>
                    <div style="color: #c53030; font-weight: 600;">Error Loading Topics</div>
                    <div style="font-size: 0.8125rem; color: #718096; margin-top: 0.5rem;">
                        ${error.message}
                    </div>
                    <button onclick="loadAvailableTopics()" 
                            style="margin-top: 1rem; padding: 0.5rem 1rem; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer;">
                        <i class="fas fa-redo"></i> Retry
                    </button>
                </div>
            `;
        });
    }

    function renderAvailableTopics(topics) {
        const container = document.getElementById('availableTopicsList');
        
        if (!Array.isArray(topics) || topics.length === 0) {
            container.innerHTML = `
                <div class="empty-state" style="text-align: center; padding: 2rem;">
                    <i class="fas fa-folder-open" style="font-size: 2.5rem; color: #cbd5e0;"></i>
                    <h3 style="font-size: 1rem; color: #718096; margin-top: 0.75rem;">No Topics Available</h3>
                    <p style="font-size: 0.8125rem; color: #a0aec0; margin-top: 0.25rem;">
                        All topics are already added to this course.
                    </p>
                    <a href="{{ route('teacher.topics.create') }}" 
                       style="display: inline-block; margin-top: 1rem; padding: 0.5rem 1.25rem; background: var(--primary); color: white; border-radius: 8px; text-decoration: none; font-size: 0.8125rem;">
                        <i class="fas fa-plus" style="margin-right: 0.375rem;"></i>Create New Topic
                    </a>
                </div>
            `;
            return;
        }

        container.innerHTML = topics.map(topic => {
            const description = topic.description || topic.content || 'No description provided.';
            const truncatedDesc = description.length > 120 ? 
                description.substring(0, 120) + '...' : 
                description;
            
            const isSelected = selectedTopics.includes(topic.id);
            
            return `
                <div class="topic-item ${isSelected ? 'selected' : ''}" 
                     onclick="toggleTopic(${topic.id})">
                    <div class="topic-item-header">
                        <div class="topic-item-title">${topic.title || 'Untitled Topic'}</div>
                        <button class="add-btn" onclick="event.stopPropagation(); addSingleTopic(${topic.id})">
                            <i class="fas fa-plus"></i> Add
                        </button>
                    </div>
                    <div class="topic-item-description">
                        ${truncatedDesc}
                    </div>
                </div>
            `;
        }).join('');
    }

    function toggleTopic(topicId) {
        if (selectedTopics.includes(topicId)) {
            selectedTopics = selectedTopics.filter(id => id !== topicId);
        } else {
            selectedTopics.push(topicId);
        }
        
        const topicItem = document.querySelector(`.topic-item[onclick*="toggleTopic(${topicId})"]`);
        if (topicItem) {
            topicItem.classList.toggle('selected');
        }
    }

    function addSingleTopic(topicId) {
        if (currentCourseTopics.includes(topicId)) {
            showNotification('This topic is already added to the course.', 'warning');
            return;
        }

        fetch(`/teacher/courses/${encryptedCourseId}/add-topic`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ topic_id: topicId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentCourseTopics.push(topicId);
                allAvailableTopics = allAvailableTopics.filter(topic => topic.id !== topicId);
                renderAvailableTopics(allAvailableTopics);
                addTopicToDisplay(data.topic);
                showNotification('Topic added successfully!', 'success');
            } else {
                showNotification(data.message || 'Failed to add topic.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', 'error');
        });
    }

    function addSelectedTopics() {
        if (selectedTopics.length === 0) {
            showNotification('Please select at least one topic to add.', 'warning');
            return;
        }

        fetch(`/teacher/courses/${encryptedCourseId}/add-topics`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ topic_ids: selectedTopics })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentCourseTopics = [...currentCourseTopics, ...selectedTopics];
                allAvailableTopics = allAvailableTopics.filter(topic => 
                    !selectedTopics.includes(topic.id)
                );
                renderAvailableTopics(allAvailableTopics);
                
                if (data.topics && Array.isArray(data.topics)) {
                    data.topics.forEach(topic => addTopicToDisplay(topic));
                }
                
                selectedTopics = [];
                
                if (allAvailableTopics.length === 0) {
                    closeAddTopicModal();
                }
                
                showNotification('Topics added successfully!', 'success');
            } else {
                showNotification(data.message || 'Failed to add topics.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', 'error');
        });
    }

    function addTopicToDisplay(topic) {
        const topicsList = document.getElementById('topicsList');
        const emptyState = topicsList.querySelector('.empty-state');
        
        if (emptyState) {
            topicsList.innerHTML = '';
        }
        
        const topicElement = document.createElement('div');
        topicElement.className = 'topic-card';
        topicElement.id = `topic-${topic.id}`;
        topicElement.innerHTML = `
            <div class="topic-header">
                <div>
                    <div class="topic-title">${topic.title || 'Untitled Topic'}</div>
                    <div style="font-size: 0.6875rem; color: #a0aec0;">
                        <i class="fas fa-clock"></i>
                        Just added
                    </div>
                </div>
                <div class="action-dropdown">
                    <button class="action-btn-small" onclick="removeTopic(${topic.id}, '${(topic.title || 'Untitled Topic').replace(/'/g, "\\'")}')">
                        <i class="fas fa-times"></i> Remove
                    </button>
                </div>
            </div>
            <div class="topic-content">
                <div class="topic-description">
                    ${topic.description || topic.content || 'No description provided.'}
                </div>
            </div>
        `;
        
        topicsList.appendChild(topicElement);
        
        // Update topics count
        updateTopicsCount();
    }

    function removeTopic(topicId, topicTitle) {
        Swal.fire({
            title: 'Remove Topic?',
            text: `Are you sure you want to remove "${topicTitle}" from this course?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f56565',
            cancelButtonColor: '#a0aec0',
            confirmButtonText: 'Yes, Remove',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/teacher/courses/${encryptedCourseId}/remove-topic`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ topic_id: topicId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentCourseTopics = currentCourseTopics.filter(id => id !== topicId);
                        
                        const topicElement = document.getElementById(`topic-${topicId}`);
                        if (topicElement) topicElement.remove();
                        
                        if (data.topic) {
                            allAvailableTopics.push(data.topic);
                            renderAvailableTopics(allAvailableTopics);
                        }
                        
                        const topicsList = document.getElementById('topicsList');
                        if (topicsList.children.length === 0) {
                            topicsList.innerHTML = `
                                <div class="empty-state">
                                    <i class="fas fa-folder-open"></i>
                                    <h3>No Topics Yet</h3>
                                    <p>Start by adding topics to this course</p>
                                    <button onclick="openAddTopicModal()" style="margin-top: 1rem; padding: 0.5rem 1.25rem; background: linear-gradient(135deg, var(--primary) 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-size: 0.8125rem; font-weight: 600; cursor: pointer;">
                                        <i class="fas fa-plus" style="margin-right: 0.375rem;"></i>Add First Topic
                                    </button>
                                </div>
                            `;
                        }
                        
                        updateTopicsCount();
                        showNotification('Topic removed successfully!', 'success');
                    } else {
                        showNotification(data.message || 'Failed to remove topic.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred. Please try again.', 'error');
                });
            }
        });
    }

    function updateTopicsCount() {
        const topicsCount = document.querySelectorAll('.topic-card').length;
        const countBadge = document.querySelector('.topics-count-badge');
        if (countBadge) {
            countBadge.textContent = topicsCount;
        }
        
        // Update stats card if it exists
        const topicsStat = document.querySelector('.stat-number[data-stat="topics"]');
        if (topicsStat) {
            topicsStat.textContent = topicsCount;
        }
    }

    function searchTopics() {
        const searchTerm = document.getElementById('modalTopicSearch').value.toLowerCase();
        const filteredTopics = allAvailableTopics.filter(topic => {
            const title = topic.title ? topic.title.toLowerCase() : '';
            const description = topic.description ? topic.description.toLowerCase() : '';
            const content = topic.content ? topic.content.toLowerCase() : '';
            
            return title.includes(searchTerm) || 
                   description.includes(searchTerm) || 
                   content.includes(searchTerm);
        });
        renderAvailableTopics(filteredTopics);
    }

    // Search functionality for main topics list
    const topicSearch = document.getElementById('topicSearch');
    if (topicSearch) {
        topicSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const topicCards = document.querySelectorAll('.topic-card');
            
            topicCards.forEach(card => {
                const title = card.querySelector('.topic-title').textContent.toLowerCase();
                const description = card.querySelector('.topic-description').textContent.toLowerCase();
                
                card.style.display = title.includes(searchTerm) || description.includes(searchTerm) ? 'block' : 'none';
            });
        });
    }

    // Close modal on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && document.getElementById('addTopicModal').classList.contains('active')) {
            closeAddTopicModal();
        }
    });

    // Close modal when clicking outside
    document.getElementById('addTopicModal').addEventListener('click', (e) => {
        if (e.target === document.getElementById('addTopicModal')) {
            closeAddTopicModal();
        }
    });

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
</script>
@endpush