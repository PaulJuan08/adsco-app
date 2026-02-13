@extends('layouts.teacher')

@section('title', 'Create New Quiz')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/quiz-form.css') }}">
@endpush

@section('content')
<div class="top-header">
    <div class="greeting">
        <h1>Create New Quiz</h1>
        <p>Create a new quiz with questions and options</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- Create Quiz Form Card -->
<div class="form-container">
    <div class="card-header">
        <div class="card-title-group">
            <i class="fas fa-plus-circle card-icon"></i>
            <h2 class="card-title">Quiz Information</h2>
        </div>
        <a href="{{ route('teacher.quizzes.index') }}" class="view-all-link">
            <i class="fas fa-arrow-left"></i> Back to Quizzes
        </a>
    </div>
    
    <div class="card-body">
        <!-- Quiz Preview - Live Preview -->
        <div class="quiz-preview">
            <div class="quiz-preview-avatar" id="previewAvatar">
                📝
            </div>
            <div class="quiz-preview-title" id="previewTitle">
                New Quiz
            </div>
            <div class="quiz-preview-meta">
                <span class="quiz-preview-badge">
                    <i class="fas fa-check-circle"></i> 
                    Draft
                </span>
            </div>
        </div>

        <!-- Error Display -->
        @if($errors->any())
        <div class="error-alert">
            <div class="error-alert-header">
                <i class="fas fa-exclamation-circle"></i>
                <span>Please fix the following errors:</span>
            </div>
            <ul class="error-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Two Column Layout -->
        <div class="two-column-layout">
            <!-- Left Column - Form -->
            <div class="form-column">
                <form action="{{ route('teacher.quizzes.store') }}" method="POST" id="quiz-form">
                    @csrf
                    
                    <!-- Basic Information Section -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-info-circle"></i> Basic Information
                        </div>
                        
                        <!-- Quiz Title -->
                        <div class="form-group">
                            <label for="title" class="form-label required">
                                <i class="fas fa-heading"></i> Quiz Title
                            </label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}" 
                                   required
                                   placeholder="e.g., JavaScript Fundamentals Quiz"
                                   class="form-input @error('title') error @enderror">
                            @error('title')
                                <span class="form-error">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </span>
                            @enderror
                            <span class="form-help">
                                <i class="fas fa-info-circle"></i> Enter a descriptive title for your quiz
                            </span>
                        </div>
                        
                        <!-- Quiz Description -->
                        <div class="form-group">
                            <label for="description" class="form-label required">
                                <i class="fas fa-align-left"></i> Description
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="3"
                                      required
                                      placeholder="Describe what this quiz covers..."
                                      class="form-textarea @error('description') error @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="form-error">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </span>
                            @enderror
                            <span class="form-help">
                                <i class="fas fa-info-circle"></i> Provide a clear description of the quiz content
                            </span>
                        </div>
                        
                        <!-- Quiz Settings -->
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="passing_score" class="form-label required">
                                    <i class="fas fa-trophy"></i> Passing Score (%)
                                </label>
                                <input type="number" 
                                       id="passing_score" 
                                       name="passing_score" 
                                       value="{{ old('passing_score', 70) }}" 
                                       min="0"
                                       max="100"
                                       required
                                       class="form-input @error('passing_score') error @enderror">
                                @error('passing_score')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="duration" class="form-label required">
                                    <i class="fas fa-clock"></i> Duration (minutes)
                                </label>
                                <input type="number" 
                                       id="duration" 
                                       name="duration" 
                                       value="{{ old('duration', 30) }}" 
                                       min="1"
                                       max="180"
                                       required
                                       class="form-input @error('duration') error @enderror">
                                @error('duration')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Availability Settings (Optional) -->
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="available_from" class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Available From
                                </label>
                                <input type="datetime-local" 
                                       id="available_from" 
                                       name="available_from" 
                                       value="{{ old('available_from') }}"
                                       class="form-input @error('available_from') error @enderror">
                                @error('available_from')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                                <span class="form-help">
                                    <i class="fas fa-info-circle"></i> Leave empty for immediate availability
                                </span>
                            </div>
                            
                            <div class="form-group">
                                <label for="available_until" class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Available Until
                                </label>
                                <input type="datetime-local" 
                                       id="available_until" 
                                       name="available_until" 
                                       value="{{ old('available_until') }}"
                                       class="form-input @error('available_until') error @enderror">
                                @error('available_until')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                                <span class="form-help">
                                    <i class="fas fa-info-circle"></i> Leave empty for no expiration
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Questions Section -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-question-circle"></i> Questions & Options
                            <span style="margin-left: auto; font-size: 0.75rem; color: #718096; font-weight: normal;">
                                Max 4 options per question
                            </span>
                        </div>
                        
                        <div id="questions-list">
                            <!-- Questions will be added here dynamically -->
                        </div>
                        
                        <button type="button" 
                                id="add-question-btn"
                                class="btn btn-add">
                            <i class="fas fa-plus-circle"></i> Add Question
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Right Column - Sidebar -->
            <div class="sidebar-column">
                <!-- Quick Tips Card -->
                <div class="sidebar-card">
                    <div class="sidebar-card-title">
                        <i class="fas fa-lightbulb"></i> Quick Tips
                    </div>
                    
                    <div class="tips-grid">
                        <div class="tip-item">
                            <div class="tip-icon">
                                <i class="fas fa-question"></i>
                            </div>
                            <div class="tip-content">
                                <div class="tip-title">Clear Questions</div>
                                <div class="tip-description">Write clear, concise questions</div>
                            </div>
                        </div>
                        
                        <div class="tip-item">
                            <div class="tip-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="tip-content">
                                <div class="tip-title">One Correct Answer</div>
                                <div class="tip-description">Select one correct answer per question</div>
                            </div>
                        </div>
                        
                        <div class="tip-item">
                            <div class="tip-icon">
                                <i class="fas fa-list-ol"></i>
                            </div>
                            <div class="tip-content">
                                <div class="tip-title">Max 4 Options</div>
                                <div class="tip-description">Maximum 4 options per question</div>
                            </div>
                        </div>
                        
                        <div class="tip-item">
                            <div class="tip-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="tip-content">
                                <div class="tip-title">Set Passing Score</div>
                                <div class="tip-description">Define minimum score to pass</div>
                            </div>
                        </div>
                        
                        <div class="tip-item">
                            <div class="tip-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="tip-content">
                                <div class="tip-title">Time Limit</div>
                                <div class="tip-description">Set duration in minutes</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Guidelines Card -->
                <div class="sidebar-card">
                    <div class="sidebar-card-title">
                        <i class="fas fa-clipboard-check"></i> Guidelines
                    </div>
                    
                    <div class="guidelines-list">
                        <div class="guideline-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Title should be clear and descriptive</span>
                        </div>
                        <div class="guideline-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Description helps students understand the quiz</span>
                        </div>
                        <div class="guideline-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Each question must have 2-4 options</span>
                        </div>
                        <div class="guideline-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Select one correct answer per question</span>
                        </div>
                        <div class="guideline-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Quiz must have at least 1 question</span>
                        </div>
                        <div class="guideline-item">
                            <i class="fas fa-check-circle"></i>
                            <span>All questions require text and options</span>
                        </div>
                        <div class="guideline-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Optional explanations help students learn</span>
                        </div>
                    </div>
                </div>
                
                <!-- Quiz Summary Preview Card -->
                <div class="sidebar-card">
                    <div class="sidebar-card-title">
                        <i class="fas fa-chart-simple"></i> Quiz Summary
                    </div>
                    
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.375rem 0;">
                            <span style="font-size: 0.75rem; color: #718096;">Total Questions</span>
                            <span style="font-size: 0.8125rem; font-weight: 600; color: #2d3748;" id="totalQuestionsCount">0</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.375rem 0; border-top: 1px solid #edf2f7;">
                            <span style="font-size: 0.75rem; color: #718096;">Passing Score</span>
                            <span style="font-size: 0.8125rem; font-weight: 600; color: #2d3748;" id="previewPassingScore">70%</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.375rem 0; border-top: 1px solid #edf2f7;">
                            <span style="font-size: 0.75rem; color: #718096;">Duration</span>
                            <span style="font-size: 0.8125rem; font-weight: 600; color: #2d3748;" id="previewDuration">30 min</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-footer-modern">
        <a href="{{ route('teacher.quizzes.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i> Cancel
        </a>
        <button type="submit" form="quiz-form" class="btn btn-primary" id="submitButton">
            <i class="fas fa-save"></i> Create Quiz
        </button>
    </div>
</div>

<!-- Templates -->
<template id="question-template">
    <div class="question-card">
        <div class="question-header">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span class="question-number">#1</span>
                <span style="font-weight: 600; color: #2d3748;">Question</span>
            </div>
            <button type="button" class="btn btn-danger remove-question-btn">
                <i class="fas fa-trash-alt"></i> Remove
            </button>
        </div>
        
        <div class="question-content">
            <div class="form-group">
                <label class="form-label required">
                    <i class="fas fa-question-circle"></i> Question Text
                </label>
                <textarea name="questions[0][question]" 
                          class="question-text form-textarea"
                          rows="3"
                          required
                          placeholder="Enter the question..."></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-info-circle"></i> Explanation (Optional)
                </label>
                <textarea name="questions[0][explanation]" 
                          class="form-textarea"
                          rows="2"
                          placeholder="Explain why this answer is correct..."></textarea>
                <span class="form-help">
                    <i class="fas fa-info-circle"></i> Shown to students after answering
                </span>
            </div>
            
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-list"></i> Options (Select one correct answer)
                </label>
                <div class="options-list">
                    <!-- Options will be added here dynamically -->
                </div>
                
                <button type="button" 
                        class="btn btn-add-option add-option-btn"
                        style="margin-top: 0.75rem;">
                    <i class="fas fa-plus"></i> Add Option
                </button>
            </div>
        </div>
    </div>
</template>

<template id="option-template">
    <div class="option-item">
        <input type="radio" 
               class="option-radio is-correct-checkbox"
               name="questions[0][correct_answer]"
               value="0">
        <input type="text" 
               class="option-input option-text"
               name="questions[0][options][0][option_text]"
               placeholder="Enter option text"
               required>
        <button type="button" 
                class="btn btn-danger remove-option-btn"
                style="padding: 0.5rem; min-width: 36px;">
            <i class="fas fa-times"></i>
        </button>
    </div>
</template>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const titleInput = document.getElementById('title');
        const previewTitle = document.getElementById('previewTitle');
        const previewAvatar = document.getElementById('previewAvatar');
        const passingScoreInput = document.getElementById('passing_score');
        const durationInput = document.getElementById('duration');
        const previewPassingScore = document.getElementById('previewPassingScore');
        const previewDuration = document.getElementById('previewDuration');
        
        // Live preview update
        function updatePreview() {
            const title = titleInput.value.trim();
            previewTitle.textContent = title || 'New Quiz';
            
            if (title) {
                previewAvatar.textContent = title.charAt(0).toUpperCase();
            } else {
                previewAvatar.textContent = '📝';
            }
        }
        
        function updateSettings() {
            if (previewPassingScore) {
                previewPassingScore.textContent = (passingScoreInput.value || '70') + '%';
            }
            if (previewDuration) {
                previewDuration.textContent = (durationInput.value || '30') + ' min';
            }
        }
        
        if (titleInput) {
            titleInput.addEventListener('input', updatePreview);
        }
        
        if (passingScoreInput) {
            passingScoreInput.addEventListener('input', updateSettings);
        }
        
        if (durationInput) {
            durationInput.addEventListener('input', updateSettings);
        }

        const questionsContainer = document.getElementById('questions-list');
        const addQuestionBtn = document.getElementById('add-question-btn');
        const questionTemplate = document.getElementById('question-template');
        const optionTemplate = document.getElementById('option-template');
        const submitButton = document.getElementById('submitButton');
        
        let questionCount = 0;
        const MAX_OPTIONS_PER_QUESTION = 4;
        
        // Add first question by default
        addQuestion();
        
        // Add question button click event
        addQuestionBtn.addEventListener('click', addQuestion);
        
        function addQuestion() {
            const questionClone = questionTemplate.content.cloneNode(true);
            const questionCard = questionClone.querySelector('.question-card');
            
            // Update question number display
            questionCard.querySelector('.question-number').textContent = `#${questionCount + 1}`;
            
            // Update all input names with current question count
            updateQuestionNames(questionCard, questionCount);
            
            // Add remove question event
            const removeBtn = questionCard.querySelector('.remove-question-btn');
            removeBtn.addEventListener('click', function() {
                if (document.querySelectorAll('.question-card').length > 1) {
                    Swal.fire({
                        title: 'Remove Question?',
                        text: 'This question will be removed from the quiz.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#f56565',
                        cancelButtonColor: '#a0aec0',
                        confirmButtonText: 'Yes, Remove',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            questionCard.remove();
                            updateQuestionNumbers();
                            updateTotalQuestionsCount();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Cannot Remove',
                        text: 'Quiz must have at least one question.',
                        icon: 'error',
                        confirmButtonColor: '#667eea'
                    });
                }
            });
            
            // Add option button event
            const addOptionBtn = questionCard.querySelector('.add-option-btn');
            addOptionBtn.addEventListener('click', function() {
                addOption(questionCard, questionCount);
            });
            
            // Add 2 default options
            for (let i = 0; i < 2; i++) {
                addOption(questionCard, questionCount);
            }
            
            // Append to questions list
            questionsContainer.appendChild(questionCard);
            questionCount++;
            
            // Update add option button visibility
            updateAddOptionButton(questionCard);
            updateTotalQuestionsCount();
        }
        
        function updateQuestionNames(questionCard, questionIndex) {
            // Update question textarea
            const questionText = questionCard.querySelector('.question-text');
            if (questionText) {
                questionText.name = `questions[${questionIndex}][question]`;
                questionText.value = '';
            }
            
            // Update explanation textarea
            const explanation = questionCard.querySelector('textarea[name*="[explanation]"]');
            if (explanation) {
                explanation.name = `questions[${questionIndex}][explanation]`;
                explanation.value = '';
            }
            
            // Update options in this question
            updateOptionNames(questionCard, questionIndex);
        }
        
        function addOption(questionCard, questionIndex) {
            const optionsList = questionCard.querySelector('.options-list');
            const currentOptionCount = optionsList.children.length;
            
            // Check if we can add more options
            if (currentOptionCount >= MAX_OPTIONS_PER_QUESTION) {
                Swal.fire({
                    title: 'Maximum Options Reached',
                    text: `Maximum ${MAX_OPTIONS_PER_QUESTION} options allowed per question.`,
                    icon: 'warning',
                    confirmButtonColor: '#667eea'
                });
                return;
            }
            
            const optionClone = optionTemplate.content.cloneNode(true);
            const optionItem = optionClone.querySelector('.option-item');
            
            // Create radio input
            const radioInput = optionItem.querySelector('.is-correct-checkbox');
            radioInput.name = `questions[${questionIndex}][correct_answer]`;
            radioInput.value = currentOptionCount;
            
            // Create option text input
            const optionTextInput = optionItem.querySelector('.option-text');
            optionTextInput.name = `questions[${questionIndex}][options][${currentOptionCount}][option_text]`;
            optionTextInput.value = '';
            
            // Add hidden ID input (empty for new options)
            const optionIdInput = document.createElement('input');
            optionIdInput.type = 'hidden';
            optionIdInput.name = `questions[${questionIndex}][options][${currentOptionCount}][id]`;
            optionIdInput.value = '';
            optionItem.appendChild(optionIdInput);
            
            // Add remove option event
            const removeBtn = optionItem.querySelector('.remove-option-btn');
            removeBtn.addEventListener('click', function() {
                if (optionsList.children.length > 2) {
                    Swal.fire({
                        title: 'Remove Option?',
                        text: 'This option will be removed.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#f56565',
                        cancelButtonColor: '#a0aec0',
                        confirmButtonText: 'Yes, Remove',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            optionItem.remove();
                            updateOptionNames(questionCard, questionIndex);
                            updateAddOptionButton(questionCard);
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Cannot Remove',
                        text: 'Each question must have at least 2 options.',
                        icon: 'error',
                        confirmButtonColor: '#667eea'
                    });
                }
            });
            
            // Set first option as checked by default if no option is checked
            if (currentOptionCount === 0) {
                radioInput.checked = true;
            }
            
            // Append to options list
            optionsList.appendChild(optionItem);
            
            // Update add option button visibility
            updateAddOptionButton(questionCard);
        }
        
        function updateOptionNames(questionCard, questionIndex) {
            const optionsList = questionCard.querySelector('.options-list');
            const options = optionsList.querySelectorAll('.option-item');
            
            options.forEach((optionItem, index) => {
                const radio = optionItem.querySelector('.is-correct-checkbox');
                if (radio) {
                    radio.name = `questions[${questionIndex}][correct_answer]`;
                    radio.value = index;
                }
                
                const optionText = optionItem.querySelector('.option-text');
                if (optionText) {
                    optionText.name = `questions[${questionIndex}][options][${index}][option_text]`;
                }
                
                const hiddenId = optionItem.querySelector('input[type="hidden"]');
                if (hiddenId) {
                    hiddenId.name = `questions[${questionIndex}][options][${index}][id]`;
                }
            });
        }
        
        function updateQuestionNumbers() {
            const questionCards = document.querySelectorAll('.question-card');
            questionCount = questionCards.length;
            
            questionCards.forEach((card, qIndex) => {
                // Update display number
                const questionNumberSpan = card.querySelector('.question-number');
                if (questionNumberSpan) {
                    questionNumberSpan.textContent = `#${qIndex + 1}`;
                }
                
                // Update question text name
                const questionText = card.querySelector('.question-text');
                if (questionText) {
                    questionText.name = `questions[${qIndex}][question]`;
                }
                
                // Update explanation name
                const explanation = card.querySelector('textarea[name*="[explanation]"]');
                if (explanation) {
                    explanation.name = `questions[${qIndex}][explanation]`;
                }
                
                // Update options
                updateOptionNames(card, qIndex);
                
                // Update add option button visibility
                updateAddOptionButton(card);
            });
            
            updateTotalQuestionsCount();
        }
        
        function updateAddOptionButton(questionCard) {
            const optionsList = questionCard.querySelector('.options-list');
            const addOptionBtn = questionCard.querySelector('.add-option-btn');
            const currentOptionCount = optionsList.children.length;
            
            // Show/hide add option button based on option count
            if (addOptionBtn) {
                if (currentOptionCount >= MAX_OPTIONS_PER_QUESTION) {
                    addOptionBtn.style.display = 'none';
                } else {
                    addOptionBtn.style.display = 'inline-flex';
                }
            }
        }
        
        function updateTotalQuestionsCount() {
            const totalQuestions = document.querySelectorAll('.question-card').length;
            const countElement = document.getElementById('totalQuestionsCount');
            if (countElement) {
                countElement.textContent = totalQuestions;
            }
            
            // Update quiz preview badge
            const previewBadge = document.querySelector('.quiz-preview-badge');
            if (previewBadge) {
                previewBadge.innerHTML = `<i class="fas fa-check-circle"></i> Draft • ${totalQuestions} Questions`;
            }
        }
        
        // Initialize total questions count
        updateTotalQuestionsCount();
        updateSettings();
        
        // Form validation with SweetAlert2
        const form = document.getElementById('quiz-form');
        
        if (form && submitButton) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const title = document.getElementById('title').value.trim();
                const description = document.getElementById('description').value.trim();
                const passingScore = document.getElementById('passing_score').value;
                const duration = document.getElementById('duration').value;
                const availableFrom = document.getElementById('available_from').value;
                const availableUntil = document.getElementById('available_until').value;
                const questionCards = document.querySelectorAll('.question-card');
                
                let isValid = true;
                const errorMessages = [];
                
                // Validate basic info
                if (!title) {
                    isValid = false;
                    errorMessages.push('Quiz title is required.');
                    document.getElementById('title').classList.add('error');
                } else {
                    document.getElementById('title').classList.remove('error');
                }
                
                if (!description) {
                    isValid = false;
                    errorMessages.push('Quiz description is required.');
                    document.getElementById('description').classList.add('error');
                } else {
                    document.getElementById('description').classList.remove('error');
                }
                
                if (!passingScore || passingScore < 0 || passingScore > 100) {
                    isValid = false;
                    errorMessages.push('Passing score must be between 0 and 100.');
                    document.getElementById('passing_score').classList.add('error');
                } else {
                    document.getElementById('passing_score').classList.remove('error');
                }
                
                if (!duration || duration < 1 || duration > 180) {
                    isValid = false;
                    errorMessages.push('Duration must be between 1 and 180 minutes.');
                    document.getElementById('duration').classList.add('error');
                } else {
                    document.getElementById('duration').classList.remove('error');
                }
                
                // Validate dates
                if (availableFrom && availableUntil) {
                    if (new Date(availableFrom) > new Date(availableUntil)) {
                        isValid = false;
                        errorMessages.push('Available until date must be after available from date.');
                        document.getElementById('available_until').classList.add('error');
                    } else {
                        document.getElementById('available_until').classList.remove('error');
                    }
                }
                
                // Validate questions
                if (questionCards.length === 0) {
                    isValid = false;
                    errorMessages.push('Please add at least one question.');
                }
                
                questionCards.forEach((card, index) => {
                    const questionText = card.querySelector('.question-text');
                    if (!questionText || !questionText.value.trim()) {
                        isValid = false;
                        errorMessages.push(`Question ${index + 1} text is required.`);
                        if (questionText) questionText.classList.add('error');
                    } else {
                        if (questionText) questionText.classList.remove('error');
                    }
                    
                    const options = card.querySelectorAll('.option-item');
                    if (options.length < 2) {
                        isValid = false;
                        errorMessages.push(`Question ${index + 1} must have at least 2 options.`);
                    }
                    
                    if (options.length > MAX_OPTIONS_PER_QUESTION) {
                        isValid = false;
                        errorMessages.push(`Question ${index + 1} cannot have more than ${MAX_OPTIONS_PER_QUESTION} options.`);
                    }
                    
                    // Check if all options have text
                    options.forEach((option, optIndex) => {
                        const optionText = option.querySelector('.option-text');
                        if (!optionText || !optionText.value.trim()) {
                            isValid = false;
                            errorMessages.push(`Question ${index + 1}, Option ${optIndex + 1} text is required.`);
                            if (optionText) optionText.classList.add('error');
                        } else {
                            if (optionText) optionText.classList.remove('error');
                        }
                    });
                    
                    // Check one correct answer is selected
                    const checkedRadio = card.querySelector('input[type="radio"]:checked');
                    if (!checkedRadio) {
                        isValid = false;
                        errorMessages.push(`Question ${index + 1} must have one correct answer selected.`);
                    }
                });
                
                if (!isValid) {
                    Swal.fire({
                        title: 'Validation Error',
                        html: errorMessages.join('<br>'),
                        icon: 'error',
                        confirmButtonColor: '#667eea'
                    });
                    return false;
                }
                
                // Show confirmation
                Swal.fire({
                    title: 'Create Quiz?',
                    text: `You are about to create a quiz with ${questionCards.length} question(s).`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#667eea',
                    cancelButtonColor: '#a0aec0',
                    confirmButtonText: 'Yes, Create',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
                        submitButton.disabled = true;
                        form.submit();
                    }
                });
            });
        }
        
        // Show notifications from session
        @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                icon: 'success',
                title: '{{ session('success') }}',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        @endif
        
        @if(session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                icon: 'error',
                title: '{{ session('error') }}',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        @endif
    });
</script>
@endpush