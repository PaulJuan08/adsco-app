@extends('layouts.admin')

@section('title', 'Edit Quiz - ' . $quiz->title)

@push('styles')
<style>
    :root {
        --primary: #4f46e5;
        --primary-light: #e0e7ff;
        --primary-dark: #3730a3;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-900: #111827;
        --success: #10b981;
        --success-light: #d1fae5;
        --success-dark: #047857;
        --danger: #ef4444;
        --danger-light: #fee2e2;
        --danger-dark: #b91c1c;
        --warning: #f59e0b;
        --warning-light: #fef3c7;
        --warning-dark: #d97706;
        --radius: 0.5rem;
        --radius-sm: 0.25rem;
        --radius-lg: 0.75rem;
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    /* Form Container */
    .form-container {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 1.5rem;
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }

    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .card-icon {
        width: 42px;
        height: 42px;
        background: var(--primary-light);
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 1.125rem;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0;
    }

    .view-all-link {
        color: var(--primary);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.375rem;
        transition: all 0.2s ease;
    }

    .view-all-link:hover {
        gap: 0.625rem;
        color: var(--primary-dark);
    }

    .card-body {
        padding: 1.5rem;
    }

    .card-footer-modern {
        padding: 1.5rem;
        border-top: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    /* Form Elements */
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-700);
    }
    
    .form-label.required::after {
        content: " *";
        color: var(--danger);
    }
    
    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-sm);
        font-size: 0.875rem;
        color: var(--gray-900);
        background: white;
        transition: all 0.2s ease;
    }
    
    .form-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    
    .form-textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-sm);
        font-size: 0.875rem;
        color: var(--gray-900);
        background: white;
        transition: all 0.2s ease;
        min-height: 120px;
        resize: vertical;
        font-family: inherit;
    }
    
    .form-textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    
    /* Question Cards */
    .question-card {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        transition: all 0.2s ease;
    }
    
    .question-card:hover {
        box-shadow: var(--shadow-md);
    }
    
    .question-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--gray-200);
    }
    
    .question-number {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--primary-dark);
        background: var(--primary-light);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
    }
    
    /* Option Items */
    .option-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-sm);
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }
    
    .option-item:hover {
        border-color: var(--primary);
        background: var(--primary-light);
    }
    
    .option-radio {
        width: 18px;
        height: 18px;
        margin: 0;
        cursor: pointer;
    }
    
    .option-input {
        flex: 1;
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-sm);
        font-size: 0.875rem;
        background: white;
    }
    
    .option-input:focus {
        outline: none;
        border-color: var(--primary);
    }
    
    /* Buttons */
    .btn {
        padding: 0.625rem 1.25rem;
        border-radius: var(--radius);
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-primary {
        background: var(--primary);
        color: white;
    }
    
    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }
    
    .btn-secondary {
        background: var(--gray-100);
        color: var(--gray-700);
        border: 1px solid var(--gray-300);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-secondary:hover {
        background: var(--gray-200);
        color: var(--gray-900);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }
    
    .btn-danger {
        background: var(--danger-light);
        color: var(--danger-dark);
        border: 1px solid var(--danger);
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }
    
    .btn-danger:hover {
        background: var(--danger);
        color: white;
    }
    
    .btn-add {
        background: var(--primary-light);
        color: var(--primary-dark);
        border: 1px solid var(--primary);
        padding: 0.5rem 1rem;
        width: 100%;
        justify-content: center;
        margin-top: 1rem;
    }
    
    .btn-add:hover {
        background: var(--primary);
        color: white;
    }
    
    .btn-add-option {
        background: var(--success-light);
        color: var(--success-dark);
        border: 1px solid var(--success);
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
    }
    
    .btn-add-option:hover {
        background: var(--success);
        color: white;
    }
    
    /* Section Headers */
    .section-header {
        font-size: 1rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    /* Grid Layout */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    /* Danger Zone */
    .danger-zone {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 2px solid var(--danger);
    }
    
    .danger-zone-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        color: var(--danger-dark);
        font-weight: 600;
    }
    
    /* Error Alert */
    .error-alert {
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: var(--danger-light);
        color: var(--danger-dark);
        border-radius: var(--radius-sm);
        border: 1px solid var(--danger);
    }
    
    .error-alert-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    
    .error-list {
        margin: 0;
        padding-left: 1.25rem;
    }
    
    .error-list li {
        margin-bottom: 0.25rem;
        font-size: 0.875rem;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .card-footer-modern {
            flex-direction: column;
            gap: 1rem;
        }
        
        .card-footer-modern .btn {
            width: 100%;
            justify-content: center;
        }
        
        .question-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }
        
        .option-item {
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
        }
        
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    <!-- Edit Quiz Form -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-edit card-icon"></i>
                <h2 class="card-title">Edit Quiz: {{ $quiz->title }}</h2>
            </div>
            <a href="{{ route('admin.quizzes.show', Crypt::encrypt($quiz->id)) }}" class="view-all-link">
                <i class="fas fa-arrow-left"></i> Back to View
            </a>
        </div>
        
        <div class="card-body">
            <form action="{{ route('admin.quizzes.update', Crypt::encrypt($quiz->id)) }}" method="POST" id="quiz-form">
                @csrf
                @method('PUT')
                
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
                
                <!-- Basic Quiz Information -->
                <div class="form-group">
                    <div class="section-header">
                        <i class="fas fa-info-circle"></i>
                        Basic Information
                    </div>
                    
                    <div class="form-group">
                        <label for="title" class="form-label required">Quiz Title</label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $quiz->title) }}" 
                               required
                               placeholder="e.g., JavaScript Fundamentals Quiz"
                               class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label required">Description</label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3"
                                  required
                                  placeholder="Describe what this quiz covers..."
                                  class="form-textarea">{{ old('description', $quiz->description) }}</textarea>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="duration" class="form-label">Duration (minutes)</label>
                            <input type="number" 
                                   id="duration" 
                                   name="duration" 
                                   value="{{ old('duration', $quiz->duration) }}"
                                   min="1"
                                   placeholder="30"
                                   class="form-input">
                        </div>
                        
                        <div class="form-group">
                            <label for="passing_score" class="form-label">Passing Score (%)</label>
                            <input type="number" 
                                   id="passing_score" 
                                   name="passing_score" 
                                   value="{{ old('passing_score', $quiz->passing_score) }}"
                                   min="1"
                                   max="100"
                                   placeholder="70"
                                   class="form-input">
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="available_from" class="form-label">Available From</label>
                            <input type="datetime-local" 
                                   id="available_from" 
                                   name="available_from" 
                                   value="{{ old('available_from', $quiz->available_from ? $quiz->available_from->format('Y-m-d\TH:i') : '') }}"
                                   class="form-input">
                        </div>
                        
                        <div class="form-group">
                            <label for="available_until" class="form-label">Available Until</label>
                            <input type="datetime-local" 
                                   id="available_until" 
                                   name="available_until" 
                                   value="{{ old('available_until', $quiz->available_until ? $quiz->available_until->format('Y-m-d\TH:i') : '') }}"
                                   class="form-input">
                        </div>
                    </div>
                </div>
                
                <!-- Questions Section -->
                <div class="form-group" id="questions-container">
                    <div class="section-header">
                        <i class="fas fa-question-circle"></i>
                        Questions & Options
                    </div>
                    
                    <div id="questions-list">
                        @foreach($quiz->questions as $questionIndex => $question)
                        <div class="question-card" data-question-id="{{ $question->id }}">
                            <div class="question-header">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <span class="question-number">#{{ $loop->iteration }}</span>
                                    <span style="font-weight: 600; color: var(--gray-700);">Question</span>
                                </div>
                                <button type="button" class="btn btn-danger remove-question-btn">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                            
                            <input type="hidden" name="questions[{{ $questionIndex }}][id]" value="{{ $question->id }}">
                            
                            <div class="form-group">
                                <label class="form-label required">Question Text</label>
                                <textarea name="questions[{{ $questionIndex }}][question]"
                                          rows="3"
                                          required
                                          placeholder="Enter the question..."
                                          class="form-textarea">{{ old('questions.' . $questionIndex . '.question', $question->question) }}</textarea>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Explanation (Optional)</label>
                                <textarea name="questions[{{ $questionIndex }}][explanation]"
                                          rows="2"
                                          placeholder="Add explanation for the correct answer..."
                                          class="form-textarea">{{ old('questions.' . $questionIndex . '.explanation', $question->explanation) }}</textarea>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Options (Select one correct answer)</label>
                                <div class="options-list">
                                    @foreach($question->options as $optionIndex => $option)
                                    <div class="option-item">
                                        <input type="hidden" name="questions[{{ $questionIndex }}][options][{{ $optionIndex }}][id]" value="{{ $option->id }}">
                                        <input type="radio" 
                                               class="option-radio"
                                               name="questions[{{ $questionIndex }}][correct_answer]"
                                               value="{{ $optionIndex }}"
                                               {{ $option->is_correct ? 'checked' : '' }}>
                                        <input type="text" 
                                               class="option-input"
                                               name="questions[{{ $questionIndex }}][options][{{ $optionIndex }}][option_text]"
                                               value="{{ old('questions.' . $questionIndex . '.options.' . $optionIndex . '.option_text', $option->option_text) }}"
                                               placeholder="Enter option text"
                                               required>
                                        <button type="button" 
                                                class="btn btn-danger remove-option-btn"
                                                style="padding: 0.375rem;">
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
                        @endforeach
                    </div>
                    
                    <button type="button" 
                            id="add-question-btn"
                            class="btn btn-add">
                        <i class="fas fa-plus-circle"></i> Add New Question
                    </button>
                </div>
        </div>
        
        <div class="card-footer-modern">
            <a href="{{ route('admin.quizzes.show', Crypt::encrypt($quiz->id)) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Quiz
            </button>
            </form>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="form-container danger-zone">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-exclamation-triangle card-icon" style="background: var(--danger-light); color: var(--danger);"></i>
                <h2 class="card-title">Danger Zone</h2>
            </div>
        </div>
        
        <div class="card-body">
            <div class="danger-zone-header">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Delete Quiz</span>
            </div>
            <p style="color: var(--gray-600); font-size: 0.875rem; margin-bottom: 1rem;">
                Once you delete a quiz, there is no going back. This will remove all questions, options, and student attempts associated with this quiz.
            </p>
            <form action="{{ route('admin.quizzes.destroy', Crypt::encrypt($quiz->id)) }}" method="POST" id="delete-form">
                @csrf
                @method('DELETE')
                <button type="button" 
                        onclick="confirmDelete()"
                        class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete Quiz
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Tips Card -->
    <div class="form-container" style="margin-top: 1.5rem;">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-lightbulb card-icon"></i>
                <h2 class="card-title">Quick Tips</h2>
            </div>
        </div>
        
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--primary-light); border-radius: var(--radius-sm); border: 1px solid var(--primary);">
                    <div style="width: 44px; height: 44px; background: var(--primary); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">Edit Questions</div>
                        <div style="font-size: 0.75rem; opacity: 0.8;">Update existing questions</div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--success-light); border-radius: var(--radius-sm); border: 1px solid var(--success);">
                    <div style="width: 44px; height: 44px; background: var(--success); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">Add Questions</div>
                        <div style="font-size: 0.75rem; opacity: 0.8;">Add new questions to quiz</div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--warning-light); border-radius: var(--radius-sm); border: 1px solid var(--warning);">
                    <div style="width: 44px; height: 44px; background: var(--warning); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-save"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">Save Changes</div>
                        <div style="font-size: 0.75rem; opacity: 0.8;">Save all your updates</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates -->
    <template id="new-question-template">
        <div class="question-card" data-question-id="new">
            <div class="question-header">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span class="question-number">#NEW</span>
                    <span style="font-weight: 600; color: var(--gray-700);">New Question</span>
                </div>
                <button type="button" class="btn btn-danger remove-question-btn">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
            
            <input type="hidden" class="question-id" name="" value="">
            
            <div class="form-group">
                <label class="form-label required">Question Text</label>
                <textarea class="question-text form-textarea"
                          rows="3"
                          required
                          placeholder="Enter the question..."></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Explanation (Optional)</label>
                <textarea class="question-explanation form-textarea"
                          rows="2"
                          placeholder="Add explanation for the correct answer..."></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Options (Select one correct answer)</label>
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
    </template>

    <template id="new-option-template">
        <div class="option-item">
            <input type="radio" 
                   class="option-radio is-correct-checkbox"
                   name=""
                   value="">
            <input type="text" 
                   class="option-input option-text"
                   placeholder="Enter option text"
                   required>
            <button type="button" 
                    class="btn btn-danger remove-option-btn"
                    style="padding: 0.375rem;">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </template>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const questionsContainer = document.getElementById('questions-list');
        const addQuestionBtn = document.getElementById('add-question-btn');
        const newQuestionTemplate = document.getElementById('new-question-template');
        const newOptionTemplate = document.getElementById('new-option-template');
        
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
                if (confirm('Remove this question?')) {
                    questionCard.remove();
                    updateQuestionNumbers();
                }
            });
            
            // Add option button event
            const addOptionBtn = questionCard.querySelector('.add-option-btn');
            addOptionBtn.addEventListener('click', function() {
                addNewOption(questionCard, newQuestionIndex);
            });
            
            // Add 2 default options for new question (not 4)
            for (let i = 0; i < 2; i++) {
                addNewOption(questionCard, newQuestionIndex);
            }
            
            // Append to questions list
            questionsContainer.appendChild(questionCard);
            newQuestionIndex++;
            
            // Update add option button visibility for the new question
            updateAddOptionButton(questionCard);
        });
        
        // Add new option function
        function addNewOption(questionCard, qIndex) {
            const optionsList = questionCard.querySelector('.options-list');
            const currentOptionCount = optionsList.children.length;
            
            // Check if we can add more options
            if (currentOptionCount >= MAX_OPTIONS_PER_QUESTION) {
                alert(`Maximum ${MAX_OPTIONS_PER_QUESTION} options allowed per question.`);
                return;
            }
            
            const optionClone = newOptionTemplate.content.cloneNode(true);
            const optionItem = optionClone.querySelector('.option-item');
            
            // Update radio button
            const radio = optionItem.querySelector('.is-correct-checkbox');
            radio.name = `questions[${qIndex}][correct_answer]`;
            radio.value = currentOptionCount;
            
            // Set first option as checked by default for new questions
            if (currentOptionCount === 0 && questionCard.getAttribute('data-question-id') === 'new') {
                radio.checked = true;
            }
            
            // Update option text input
            const optionText = optionItem.querySelector('.option-text');
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
                    optionItem.remove();
                    updateRadioButtonValues(questionCard, qIndex);
                    updateAddOptionButton(questionCard);
                } else {
                    alert('Each question must have at least 2 options.');
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
                const radio = optionItem.querySelector('input[type="radio"]');
                if (radio) {
                    radio.value = index;
                }
                
                // Update option text input name
                const optionText = optionItem.querySelector('.option-text');
                if (optionText) {
                    optionText.name = `questions[${qIndex}][options][${index}][option_text]`;
                }
                
                // Update hidden ID input name
                const hiddenInput = optionItem.querySelector('input[type="hidden"][name*="[id]"]');
                if (hiddenInput) {
                    hiddenInput.name = `questions[${qIndex}][options][${index}][id]`;
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
                
                // Update add option button visibility
                updateAddOptionButton(card);
            });
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
        
        // Add event listeners for existing remove buttons
        document.querySelectorAll('.remove-question-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (document.querySelectorAll('.question-card').length > 1) {
                    if (confirm('Remove this question?')) {
                        this.closest('.question-card').remove();
                        updateQuestionNumbers();
                    }
                } else {
                    alert('Quiz must have at least one question.');
                }
            });
        });
        
        // Add event listeners for existing option remove buttons
        document.querySelectorAll('.remove-option-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const optionsList = this.closest('.options-list');
                if (optionsList.children.length > 2) {
                    this.closest('.option-item').remove();
                    
                    // Find the question card
                    const questionCard = this.closest('.question-card');
                    if (questionCard) {
                        // Find the question index
                        const questionCards = document.querySelectorAll('.question-card');
                        const qIndex = Array.from(questionCards).indexOf(questionCard);
                        
                        // Update radio button values
                        updateRadioButtonValues(questionCard, qIndex);
                        
                        // Update add option button visibility
                        updateAddOptionButton(questionCard);
                    }
                } else {
                    alert('Each question must have at least 2 options.');
                }
            });
        });
        
        // Add option buttons for existing questions
        document.querySelectorAll('.add-option-btn').forEach((btn, index) => {
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
                        alert(`Maximum ${MAX_OPTIONS_PER_QUESTION} options allowed per question.`);
                        return;
                    }
                    
                    const optionClone = newOptionTemplate.content.cloneNode(true);
                    const optionItem = optionClone.querySelector('.option-item');
                    
                    // Update radio button
                    const radio = optionItem.querySelector('.is-correct-checkbox');
                    radio.name = `questions[${qIndex}][correct_answer]`;
                    radio.value = currentOptionCount;
                    
                    // Update option text input
                    const optionText = optionItem.querySelector('.option-text');
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
                            optionItem.remove();
                            updateRadioButtonValues(questionCard, qIndex);
                            updateAddOptionButton(questionCard);
                        } else {
                            alert('Each question must have at least 2 options.');
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
        
        // Form validation
        document.getElementById('quiz-form').addEventListener('submit', function(e) {
            const questionCards = document.querySelectorAll('.question-card');
            
            if (questionCards.length === 0) {
                e.preventDefault();
                alert('Please add at least one question.');
                return false;
            }
            
            let isValid = true;
            const errorMessages = [];
            
            questionCards.forEach((card, index) => {
                const questionText = card.querySelector('textarea[name$="[question]"]');
                if (!questionText || !questionText.value.trim()) {
                    isValid = false;
                    errorMessages.push(`Question ${index + 1} text is required.`);
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
                
                // Check one correct answer is selected
                const questionName = questionText.name;
                const qIndexMatch = questionName.match(/questions\[(\d+)\]\[question\]/);
                if (qIndexMatch) {
                    const qIndex = qIndexMatch[1];
                    const checkedRadio = document.querySelector(`input[name="questions[${qIndex}][correct_answer]"]:checked`);
                    if (!checkedRadio) {
                        isValid = false;
                        errorMessages.push(`Question ${index + 1} must have one correct answer selected.`);
                    }
                }
                
                // Check if all options have text
                options.forEach((option, optIndex) => {
                    const optionText = option.querySelector('input[type="text"]');
                    if (!optionText || !optionText.value.trim()) {
                        isValid = false;
                        errorMessages.push(`Question ${index + 1}, Option ${optIndex + 1} text is required.`);
                    }
                });
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fix the following errors:\n\n' + errorMessages.join('\n'));
                return false;
            }
            
            return true;
        });
        
        // Delete confirmation
        window.confirmDelete = function() {
            if (confirm('⚠️ WARNING: Are you sure you want to delete this quiz?\n\nThis action cannot be undone. All questions, options, and student attempts will be permanently removed.')) {
                document.getElementById('delete-form').submit();
            }
        };
    });
</script>
@endpush