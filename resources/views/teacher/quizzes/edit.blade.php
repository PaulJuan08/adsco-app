@extends('layouts.teacher')

@section('title', 'Edit Quiz - ' . $quiz->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/quiz-form.css') }}">
@endpush

@section('content')
<div class="top-header">
    <div class="greeting">
        <h1>Edit Quiz</h1>
        <p>Update quiz information and questions</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
    </div>
</div>

<!-- Edit Quiz Form Card -->
<div class="form-container">
    <div class="card-header">
        <div class="card-title-group">
            <i class="fas fa-edit card-icon"></i>
            <h2 class="card-title">Edit Quiz: {{ $quiz->title }}</h2>
        </div>
        <a href="{{ route('teacher.quizzes.show', Crypt::encrypt($quiz->id)) }}" class="view-all-link">
            <i class="fas fa-arrow-left"></i> Back to Quiz Details
        </a>
    </div>
    
    <div class="card-body">
        <!-- Quiz Preview - Live Preview -->
        <div class="quiz-preview">
            <div class="quiz-preview-avatar" id="previewAvatar">
                {{ strtoupper(substr($quiz->title, 0, 1)) }}
            </div>
            <div class="quiz-preview-title" id="previewTitle">
                {{ $quiz->title }}
            </div>
            <div class="quiz-preview-meta">
                <span class="quiz-preview-badge {{ $quiz->is_published ? 'published' : 'draft' }}">
                    <i class="fas {{ $quiz->is_published ? 'fa-check-circle' : 'fa-clock' }}"></i>
                    {{ $quiz->is_published ? 'Published' : 'Draft' }}
                </span>
                <span class="quiz-preview-badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-question-circle"></i> 
                    {{ $quiz->questions->count() }} Questions
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
                <form action="{{ route('teacher.quizzes.update', Crypt::encrypt($quiz->id)) }}" method="POST" id="quiz-form">
                    @csrf
                    @method('PUT')
                    
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
                                   value="{{ old('title', $quiz->title) }}" 
                                   required
                                   placeholder="e.g., JavaScript Fundamentals Quiz"
                                   class="form-input @error('title') error @enderror">
                            @error('title')
                                <span class="form-error">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </span>
                            @enderror
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
                                      class="form-textarea @error('description') error @enderror">{{ old('description', $quiz->description) }}</textarea>
                            @error('description')
                                <span class="form-error">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </span>
                            @enderror
                        </div>
                        
                        <!-- Quiz Settings -->
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="passing_score" class="form-label">
                                    <i class="fas fa-trophy"></i> Passing Score (%)
                                </label>
                                <input type="number" 
                                       id="passing_score" 
                                       name="passing_score" 
                                       value="{{ old('passing_score', $quiz->passing_score) }}" 
                                       min="0"
                                       max="100"
                                       class="form-input @error('passing_score') error @enderror">
                                @error('passing_score')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                                <span class="form-help">
                                    <i class="fas fa-info-circle"></i> Leave empty for no passing score
                                </span>
                            </div>
                            
                            <div class="form-group">
                                <label for="duration" class="form-label">
                                    <i class="fas fa-clock"></i> Duration (minutes)
                                </label>
                                <input type="number" 
                                       id="duration" 
                                       name="duration" 
                                       value="{{ old('duration', $quiz->duration) }}" 
                                       min="1"
                                       class="form-input @error('duration') error @enderror">
                                @error('duration')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                                <span class="form-help">
                                    <i class="fas fa-info-circle"></i> Leave empty for no time limit
                                </span>
                            </div>
                        </div>
                        
                        <!-- Availability Settings -->
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="available_from" class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Available From
                                </label>
                                <input type="datetime-local" 
                                       id="available_from" 
                                       name="available_from" 
                                       value="{{ old('available_from', $quiz->available_from ? $quiz->available_from->format('Y-m-d\TH:i') : '') }}"
                                       class="form-input @error('available_from') error @enderror">
                                @error('available_from')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="available_until" class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Available Until
                                </label>
                                <input type="datetime-local" 
                                       id="available_until" 
                                       name="available_until" 
                                       value="{{ old('available_until', $quiz->available_until ? $quiz->available_until->format('Y-m-d\TH:i') : '') }}"
                                       class="form-input @error('available_until') error @enderror">
                                @error('available_until')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
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
                            @foreach($quiz->questions as $questionIndex => $question)
                            <div class="question-card" data-question-id="{{ $question->id }}">
                                <div class="question-header">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <span class="question-number">#{{ $loop->iteration }}</span>
                                        <span style="font-weight: 600; color: #2d3748;">Question</span>
                                    </div>
                                    <button type="button" class="btn btn-danger remove-question-btn">
                                        <i class="fas fa-trash-alt"></i> Remove
                                    </button>
                                </div>
                                
                                <div class="question-content">
                                    <input type="hidden" 
                                           name="questions[{{ $questionIndex }}][id]" 
                                           value="{{ $question->id }}">
                                    
                                    <div class="form-group">
                                        <label class="form-label required">
                                            <i class="fas fa-question-circle"></i> Question Text
                                        </label>
                                        <textarea name="questions[{{ $questionIndex }}][question]" 
                                                  class="form-textarea"
                                                  rows="3"
                                                  required
                                                  placeholder="Enter the question...">{{ old('questions.' . $questionIndex . '.question', $question->question) }}</textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-info-circle"></i> Explanation (Optional)
                                        </label>
                                        <textarea name="questions[{{ $questionIndex }}][explanation]" 
                                                  class="form-textarea"
                                                  rows="2"
                                                  placeholder="Explain why this answer is correct...">{{ old('questions.' . $questionIndex . '.explanation', $question->explanation) }}</textarea>
                                        <span class="form-help">
                                            <i class="fas fa-info-circle"></i> Shown to students after answering
                                        </span>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label required">
                                            <i class="fas fa-list"></i> Options (Select the correct answer)
                                        </label>
                                        <div class="options-list">
                                            @foreach($question->options as $optionIndex => $option)
                                            <div class="option-item">
                                                <input type="hidden" 
                                                       name="questions[{{ $questionIndex }}][options][{{ $optionIndex }}][id]" 
                                                       value="{{ $option->id }}">
                                                <input type="radio" 
                                                       class="option-radio"
                                                       name="questions[{{ $questionIndex }}][correct_answer]"
                                                       value="{{ $optionIndex }}"
                                                       {{ $option->is_correct ? 'checked' : '' }}
                                                       required>
                                                <input type="text" 
                                                       class="option-input"
                                                       name="questions[{{ $questionIndex }}][options][{{ $optionIndex }}][option_text]"
                                                       value="{{ old('questions.' . $questionIndex . '.options.' . $optionIndex . '.option_text', $option->option_text) }}"
                                                       placeholder="Enter option text"
                                                       required>
                                                <button type="button" 
                                                        class="btn btn-danger remove-option-btn"
                                                        style="padding: 0.5rem; min-width: 36px;">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            @endforeach
                                        </div>
                                        
                                        <button type="button" 
                                                class="btn btn-add-option add-option-btn"
                                                style="margin-top: 0.75rem;">
                                            <i class="fas fa-plus"></i> Add Option
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <button type="button" 
                                id="add-question-btn"
                                class="btn btn-add">
                            <i class="fas fa-plus-circle"></i> Add New Question
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Right Column - Sidebar -->
            <div class="sidebar-column">
                <!-- Quiz Summary Card -->
                <div class="sidebar-card">
                    <div class="sidebar-card-title">
                        <i class="fas fa-chart-simple"></i> Quiz Summary
                    </div>
                    
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.375rem 0;">
                            <span style="font-size: 0.75rem; color: #718096;">Quiz ID</span>
                            <span style="font-size: 0.8125rem; font-weight: 600; color: #2d3748;">#{{ $quiz->id }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.375rem 0; border-top: 1px solid #edf2f7;">
                            <span style="font-size: 0.75rem; color: #718096;">Total Questions</span>
                            <span style="font-size: 0.8125rem; font-weight: 600; color: #2d3748;" id="totalQuestionsCount">{{ $quiz->questions->count() }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.375rem 0; border-top: 1px solid #edf2f7;">
                            <span style="font-size: 0.75rem; color: #718096;">Duration</span>
                            <span style="font-size: 0.8125rem; font-weight: 600; color: #2d3748;" id="previewDuration">{{ $quiz->duration ?? 'No limit' }} min</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.375rem 0; border-top: 1px solid #edf2f7;">
                            <span style="font-size: 0.75rem; color: #718096;">Passing Score</span>
                            <span style="font-size: 0.8125rem; font-weight: 600; color: #2d3748;" id="previewPassingScore">{{ $quiz->passing_score ?? '0' }}%</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.375rem 0; border-top: 1px solid #edf2f7;">
                            <span style="font-size: 0.75rem; color: #718096;">Status</span>
                            <span style="font-size: 0.8125rem; font-weight: 600; color: {{ $quiz->is_published ? '#48bb78' : '#ed8936' }};">
                                {{ $quiz->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.375rem 0; border-top: 1px solid #edf2f7;">
                            <span style="font-size: 0.75rem; color: #718096;">Last Updated</span>
                            <span style="font-size: 0.8125rem; font-weight: 600; color: #2d3748;">{{ $quiz->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Tips Card -->
                <div class="sidebar-card">
                    <div class="sidebar-card-title">
                        <i class="fas fa-lightbulb"></i> Quick Tips
                    </div>
                    
                    <div class="tips-grid">
                        <div class="tip-item">
                            <div class="tip-icon">
                                <i class="fas fa-pencil-alt"></i>
                            </div>
                            <div class="tip-content">
                                <div class="tip-title">Edit Questions</div>
                                <div class="tip-description">Update existing questions and options</div>
                            </div>
                        </div>
                        
                        <div class="tip-item">
                            <div class="tip-icon">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <div class="tip-content">
                                <div class="tip-title">Add Questions</div>
                                <div class="tip-description">Add new questions with 2-4 options each</div>
                            </div>
                        </div>
                        
                        <div class="tip-item">
                            <div class="tip-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="tip-content">
                                <div class="tip-title">Mark Correct Answer</div>
                                <div class="tip-description">Select the radio button for correct option</div>
                            </div>
                        </div>
                        
                        <div class="tip-item">
                            <div class="tip-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="tip-content">
                                <div class="tip-title">Time Limit</div>
                                <div class="tip-description">Set duration or leave empty for no limit</div>
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
                            <span>Each question requires clear, concise text</span>
                        </div>
                        <div class="guideline-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Minimum 2 options, maximum 4 options per question</span>
                        </div>
                        <div class="guideline-item">
                            <i class="fas fa-check-circle"></i>
                            <span>One correct answer must be selected per question</span>
                        </div>
                        <div class="guideline-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Optional explanations help students learn</span>
                        </div>
                        <div class="guideline-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Quiz must have at least 1 question</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-footer-modern">
        <a href="{{ route('teacher.quizzes.show', Crypt::encrypt($quiz->id)) }}" class="btn btn-secondary">
            <i class="fas fa-times"></i> Cancel
        </a>
        <button type="submit" form="quiz-form" class="btn btn-primary" id="submitButton">
            <i class="fas fa-save"></i> Update Quiz
        </button>
    </div>
</div>

<!-- Danger Zone - Delete Quiz Card -->
<div class="form-container danger-zone">
    <div class="card-header">
        <div class="card-title-group">
            <i class="fas fa-exclamation-triangle card-icon"></i>
            <h2 class="card-title">Danger Zone</h2>
        </div>
    </div>
    
    <div class="card-body">
        <div class="danger-zone-header">
            <i class="fas fa-trash"></i>
            Delete Quiz
        </div>
        <p style="color: #4a5568; font-size: 0.8125rem; margin-bottom: 1rem; line-height: 1.5;">
            Once you delete a quiz, there is no going back. This will permanently remove all questions, 
            options, and student attempts associated with this quiz.
        </p>
        <form action="{{ route('teacher.quizzes.destroy', Crypt::encrypt($quiz->id)) }}" method="POST" id="delete-form">
            @csrf
            @method('DELETE')
            <button type="button" 
                    onclick="confirmDelete()"
                    class="btn btn-danger">
                <i class="fas fa-trash"></i> Delete Quiz Permanently
            </button>
        </form>
    </div>
</div>

<!-- Templates -->
<template id="new-question-template">
    <div class="question-card" data-question-id="new">
        <div class="question-header">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span class="question-number">Question #NEW</span>
                <span style="font-weight: 600; color: #2d3748;">New Question</span>
            </div>
            <button type="button" class="btn btn-danger remove-question-btn">
                <i class="fas fa-trash-alt"></i> Remove
            </button>
        </div>
        
        <div class="question-content">
            <input type="hidden" class="question-id" name="" value="">
            
            <div class="form-group">
                <label class="form-label required">
                    <i class="fas fa-question-circle"></i> Question Text
                </label>
                <textarea class="question-text form-textarea"
                          rows="3"
                          required
                          placeholder="Enter the question..."></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-info-circle"></i> Explanation (Optional)
                </label>
                <textarea class="question-explanation form-textarea"
                          rows="2"
                          placeholder="Explain why this answer is correct..."></textarea>
                <span class="form-help">
                    <i class="fas fa-info-circle"></i> Shown to students after answering
                </span>
            </div>
            
            <div class="form-group">
                <label class="form-label required">
                    <i class="fas fa-list"></i> Options (Select the correct answer)
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

<template id="new-option-template">
    <div class="option-item">
        <input type="radio" 
               class="option-radio"
               name=""
               value=""
               required>
        <input type="text" 
               class="option-input"
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
            previewTitle.textContent = title || '{{ $quiz->title }}';
            
            if (title) {
                previewAvatar.textContent = title.charAt(0).toUpperCase();
            }
        }
        
        function updateSettings() {
            if (previewPassingScore) {
                previewPassingScore.textContent = (passingScoreInput.value || '{{ $quiz->passing_score ?? '0' }}') + '%';
            }
            if (previewDuration) {
                previewDuration.textContent = (durationInput.value || '{{ $quiz->duration ?? 'No limit' }}') + ' min';
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
        const newQuestionTemplate = document.getElementById('new-question-template');
        const newOptionTemplate = document.getElementById('new-option-template');
        const submitButton = document.getElementById('submitButton');
        
        let existingQuestionCount = {{ $quiz->questions->count() }};
        let newQuestionIndex = existingQuestionCount;
        const MAX_OPTIONS_PER_QUESTION = 4;
        
        // Add new question button click event
        addQuestionBtn.addEventListener('click', function() {
            const questionClone = newQuestionTemplate.content.cloneNode(true);
            const questionCard = questionClone.querySelector('.question-card');
            
            // Update question number display
            questionCard.querySelector('.question-number').textContent = `#${newQuestionIndex + 1}`;
            
            // Update input names for new question
            const questionText = questionCard.querySelector('.question-text');
            questionText.name = `questions[${newQuestionIndex}][question]`;
            questionText.value = '';
            
            const questionExplanation = questionCard.querySelector('.question-explanation');
            questionExplanation.name = `questions[${newQuestionIndex}][explanation]`;
            questionExplanation.value = '';
            
            const questionIdInput = questionCard.querySelector('.question-id');
            questionIdInput.name = `questions[${newQuestionIndex}][id]`;
            questionIdInput.value = '';
            
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
                addNewOption(questionCard, newQuestionIndex);
            });
            
            // Add 2 default options for new question
            for (let i = 0; i < 2; i++) {
                addNewOption(questionCard, newQuestionIndex);
            }
            
            // Append to questions list
            questionsContainer.appendChild(questionCard);
            newQuestionIndex++;
            
            // Update add option button visibility
            updateAddOptionButton(questionCard);
            updateTotalQuestionsCount();
            
            // Scroll to new question
            questionCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
        
        // Add new option function
        function addNewOption(questionCard, qIndex) {
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
            
            const optionClone = newOptionTemplate.content.cloneNode(true);
            const optionItem = optionClone.querySelector('.option-item');
            
            // Update radio button
            const radio = optionItem.querySelector('.option-radio');
            radio.name = `questions[${qIndex}][correct_answer]`;
            radio.value = currentOptionCount;
            
            // Set first option as checked by default for new questions
            if (currentOptionCount === 0 && questionCard.dataset.questionId === 'new') {
                radio.checked = true;
            }
            
            // Update option text input
            const optionText = optionItem.querySelector('.option-input');
            optionText.name = `questions[${qIndex}][options][${currentOptionCount}][option_text]`;
            optionText.value = '';
            
            // Add hidden ID input (empty for new options)
            const optionIdInput = document.createElement('input');
            optionIdInput.type = 'hidden';
            optionIdInput.name = `questions[${qIndex}][options][${currentOptionCount}][id]`;
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
                            updateRadioButtonValues(questionCard, qIndex);
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
            
            // Append to options list
            optionsList.appendChild(optionItem);
            
            // Update add option button visibility
            updateAddOptionButton(questionCard);
        }
        
        function updateRadioButtonValues(questionCard, qIndex) {
            const optionsList = questionCard.querySelector('.options-list');
            const options = optionsList.querySelectorAll('.option-item');
            
            options.forEach((optionItem, index) => {
                const radio = optionItem.querySelector('.option-radio');
                if (radio) {
                    radio.name = `questions[${qIndex}][correct_answer]`;
                    radio.value = index;
                }
                
                const optionText = optionItem.querySelector('.option-input');
                if (optionText) {
                    optionText.name = `questions[${qIndex}][options][${index}][option_text]`;
                }
                
                const hiddenId = optionItem.querySelector('input[type="hidden"]');
                if (hiddenId) {
                    hiddenId.name = `questions[${qIndex}][options][${index}][id]`;
                }
            });
        }
        
        function updateQuestionNumbers() {
            const questionCards = document.querySelectorAll('.question-card');
            
            questionCards.forEach((card, index) => {
                const questionNumberSpan = card.querySelector('.question-number');
                if (questionNumberSpan) {
                    questionNumberSpan.textContent = `#${index + 1}`;
                }
                
                // Find the question index from input names
                const questionText = card.querySelector('textarea[name*="[question]"]');
                if (questionText) {
                    const match = questionText.name.match(/questions\[(\d+)\]/);
                    if (match) {
                        const oldIndex = match[1];
                        if (oldIndex != index) {
                            // Update all inputs in this card
                            updateInputIndex(card, oldIndex, index);
                        }
                    }
                }
            });
            
            // Update new question index
            const existingQuestions = {{ $quiz->questions->count() }};
            const newQuestions = document.querySelectorAll('.question-card[data-question-id="new"]').length;
            newQuestionIndex = existingQuestions + newQuestions;
            
            updateTotalQuestionsCount();
        }
        
        function updateInputIndex(card, oldIndex, newIndex) {
            // Update question text name
            const questionText = card.querySelector('textarea[name*="[question]"]');
            if (questionText) {
                questionText.name = questionText.name.replace(`questions[${oldIndex}]`, `questions[${newIndex}]`);
            }
            
            // Update explanation name
            const explanation = card.querySelector('textarea[name*="[explanation]"]');
            if (explanation) {
                explanation.name = explanation.name.replace(`questions[${oldIndex}]`, `questions[${newIndex}]`);
            }
            
            // Update question ID input
            const questionId = card.querySelector('input[name*="[id]"]');
            if (questionId && !questionId.name.includes('options')) {
                questionId.name = questionId.name.replace(`questions[${oldIndex}]`, `questions[${newIndex}]`);
            }
            
            // Update options
            const options = card.querySelectorAll('.option-item');
            options.forEach((option) => {
                const radio = option.querySelector('.option-radio');
                if (radio) {
                    radio.name = radio.name.replace(`questions[${oldIndex}]`, `questions[${newIndex}]`);
                }
                
                const optionText = option.querySelector('.option-input');
                if (optionText) {
                    optionText.name = optionText.name.replace(`questions[${oldIndex}]`, `questions[${newIndex}]`);
                }
                
                const hiddenId = option.querySelector('input[type="hidden"]');
                if (hiddenId) {
                    hiddenId.name = hiddenId.name.replace(`questions[${oldIndex}]`, `questions[${newIndex}]`);
                }
            });
            
            // Update add option button event listener
            const addOptionBtn = card.querySelector('.add-option-btn');
            if (addOptionBtn) {
                // Remove existing listeners by cloning
                const newAddOptionBtn = addOptionBtn.cloneNode(true);
                addOptionBtn.parentNode.replaceChild(newAddOptionBtn, addOptionBtn);
                
                newAddOptionBtn.addEventListener('click', function() {
                    addNewOption(card, newIndex);
                });
            }
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
            const previewBadge = document.querySelector('.quiz-preview-badge:nth-child(2)');
            if (previewBadge) {
                previewBadge.innerHTML = `<i class="fas fa-question-circle"></i> ${totalQuestions} Questions`;
            }
        }
        
        // Add event listeners for existing remove buttons
        document.querySelectorAll('.remove-question-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const questionCard = this.closest('.question-card');
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
        });
        
        // Add event listeners for existing option remove buttons
        document.querySelectorAll('.remove-option-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const optionsList = this.closest('.options-list');
                const optionItem = this.closest('.option-item');
                const questionCard = this.closest('.question-card');
                
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
                            
                            // Find the question index
                            const questionCards = document.querySelectorAll('.question-card');
                            const qIndex = Array.from(questionCards).indexOf(questionCard);
                            
                            // Update radio button values
                            updateRadioButtonValues(questionCard, qIndex);
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
        });
        
        // Add option buttons for existing questions
        document.querySelectorAll('.add-option-btn').forEach((btn) => {
            if (!btn.hasAttribute('data-initialized')) {
                btn.setAttribute('data-initialized', 'true');
                btn.addEventListener('click', function() {
                    const questionCard = this.closest('.question-card');
                    const optionsList = questionCard.querySelector('.options-list');
                    const currentOptionCount = optionsList.children.length;
                    const questionCards = document.querySelectorAll('.question-card');
                    const qIndex = Array.from(questionCards).indexOf(questionCard);
                    
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
                    
                    const optionClone = newOptionTemplate.content.cloneNode(true);
                    const optionItem = optionClone.querySelector('.option-item');
                    
                    // Update radio button
                    const radio = optionItem.querySelector('.option-radio');
                    radio.name = `questions[${qIndex}][correct_answer]`;
                    radio.value = currentOptionCount;
                    
                    // Update option text input
                    const optionText = optionItem.querySelector('.option-input');
                    optionText.name = `questions[${qIndex}][options][${currentOptionCount}][option_text]`;
                    optionText.value = '';
                    
                    // Add hidden ID input (empty for new options added to existing questions)
                    const optionIdInput = document.createElement('input');
                    optionIdInput.type = 'hidden';
                    optionIdInput.name = `questions[${qIndex}][options][${currentOptionCount}][id]`;
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
                                    updateRadioButtonValues(questionCard, qIndex);
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
                    
                    // Append to options list
                    optionsList.appendChild(optionItem);
                    
                    // Update add option button visibility
                    updateAddOptionButton(questionCard);
                });
            }
        });
        
        // Initialize add option button visibility for existing questions
        document.querySelectorAll('.question-card').forEach(card => {
            updateAddOptionButton(card);
        });
        
        // Initialize total questions count
        updateTotalQuestionsCount();
        updateSettings();
        
        // Form validation and submission with SweetAlert2
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
                
                if (passingScore) {
                    const score = parseInt(passingScore);
                    if (score < 0 || score > 100) {
                        isValid = false;
                        errorMessages.push('Passing score must be between 0 and 100.');
                        document.getElementById('passing_score').classList.add('error');
                    } else {
                        document.getElementById('passing_score').classList.remove('error');
                    }
                }
                
                if (duration) {
                    const dur = parseInt(duration);
                    if (dur < 1) {
                        isValid = false;
                        errorMessages.push('Duration must be at least 1 minute.');
                        document.getElementById('duration').classList.add('error');
                    } else {
                        document.getElementById('duration').classList.remove('error');
                    }
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
                    const questionText = card.querySelector('textarea[name*="[question]"], .question-text');
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
                        const optionText = option.querySelector('input[type="text"]');
                        if (!optionText || !optionText.value.trim()) {
                            isValid = false;
                            errorMessages.push(`Question ${index + 1}, Option ${optIndex + 1} text is required.`);
                            if (optionText) optionText.classList.add('error');
                        } else {
                            if (optionText) optionText.classList.remove('error');
                        }
                    });
                    
                    // Check one correct answer is selected
                    const questionCardsList = document.querySelectorAll('.question-card');
                    const qIndex = Array.from(questionCardsList).indexOf(card);
                    const checkedRadio = document.querySelector(`input[name="questions[${qIndex}][correct_answer]"]:checked`);
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
                    title: 'Update Quiz?',
                    text: `Are you sure you want to update this quiz with ${questionCards.length} question(s)?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#667eea',
                    cancelButtonColor: '#a0aec0',
                    confirmButtonText: 'Yes, Update',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                        submitButton.disabled = true;
                        form.submit();
                    }
                });
            });
        }
        
        // Delete confirmation
        window.confirmDelete = function() {
            Swal.fire({
                title: 'Delete Quiz?',
                text: '⚠️ WARNING: This action cannot be undone. All questions, options, and student attempts will be permanently removed.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f56565',
                cancelButtonColor: '#a0aec0',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const deleteBtn = document.querySelector('#delete-form button');
                    if (deleteBtn) {
                        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                        deleteBtn.disabled = true;
                    }
                    document.getElementById('delete-form').submit();
                }
            });
        };
        
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