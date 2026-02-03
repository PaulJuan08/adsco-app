@extends('layouts.admin')

@section('title', 'Edit Quiz - ' . $quiz->title)

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

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Edit Quiz Information</h2>
        <a href="{{ route('admin.quizzes.show', Crypt::encrypt($quiz->id)) }}" style="display: flex; align-items: center; gap: 6px; color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
            <i class="fas fa-arrow-left"></i> Back to View
        </a>
    </div>
    
    <div style="padding: 1.5rem;">
        <form action="{{ route('admin.quizzes.update', Crypt::encrypt($quiz->id)) }}" method="POST" id="quiz-form">
            @csrf
            @method('PUT')
            
            @if($errors->any())
            <div style="margin: 0 0 1.5rem; padding: 12px; background: #fee2e2; color: #991b1b; border-radius: 8px; font-size: 0.875rem;">
                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                    <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                    <strong>Please fix the following errors:</strong>
                </div>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <!-- Basic Quiz Info -->
            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 1rem; font-weight: 600; color: var(--dark); margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border);">
                    Basic Information
                </h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label for="title" class="form-label">Quiz Title *</label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $quiz->title) }}" 
                               required
                               placeholder="e.g., JavaScript Fundamentals Quiz"
                               style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%;">
                    </div>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label for="description" class="form-label">Description *</label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              required
                              placeholder="Describe what this quiz covers..."
                              style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; resize: vertical;">{{ old('description', $quiz->description) }}</textarea>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label for="duration" class="form-label">Duration (minutes)</label>
                        <input type="number" 
                               id="duration" 
                               name="duration" 
                               value="{{ old('duration', $quiz->duration) }}"
                               min="1"
                               placeholder="30"
                               style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%;">
                    </div>
                    
                    <div>
                        <label for="total_questions" class="form-label">Total Questions</label>
                        <input type="number" 
                               id="total_questions" 
                               name="total_questions" 
                               value="{{ old('total_questions', $quiz->total_questions) }}"
                               min="1"
                               placeholder="10"
                               style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%;">
                    </div>
                    
                    <div>
                        <label for="passing_score" class="form-label">Passing Score (%)</label>
                        <input type="number" 
                               id="passing_score" 
                               name="passing_score" 
                               value="{{ old('passing_score', $quiz->passing_score) }}"
                               min="1"
                               max="100"
                               placeholder="70"
                               style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%;">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <label for="available_from" class="form-label">Available From</label>
                        <input type="datetime-local" 
                               id="available_from" 
                               name="available_from" 
                               value="{{ old('available_from', $quiz->available_from ? $quiz->available_from->format('Y-m-d\TH:i') : '') }}"
                               style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%;">
                    </div>
                    
                    <div>
                        <label for="available_until" class="form-label">Available Until</label>
                        <input type="datetime-local" 
                               id="available_until" 
                               name="available_until" 
                               value="{{ old('available_until', $quiz->available_until ? $quiz->available_until->format('Y-m-d\TH:i') : '') }}"
                               style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%;">
                    </div>
                </div>
            </div>
            
            <!-- Questions Section -->
            <div style="margin-bottom: 2rem;" id="questions-container">
                <h3 style="font-size: 1rem; font-weight: 600; color: var(--dark); margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border);">
                    Questions & Options
                </h3>
                
                <div id="questions-list">
                    @foreach($quiz->questions as $questionIndex => $question)
                    <div class="question-card" data-question-id="{{ $question->id }}" style="margin-bottom: 1.5rem; padding: 1.5rem; border: 1px solid var(--border); border-radius: 8px; background: #f8fafc;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <h4 style="font-size: 0.875rem; font-weight: 600; color: var(--dark);">Question {{ $loop->iteration }}</h4>
                            <button type="button" class="remove-question-btn" style="padding: 4px 8px; background: #fee2e2; color: var(--danger); border: none; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                        
                        <input type="hidden" name="questions[{{ $questionIndex }}][id]" value="{{ $question->id }}">
                        
                        <div style="margin-bottom: 1rem;">
                            <label class="form-label">Question Text *</label>
                            <textarea name="questions[{{ $questionIndex }}][question]"
                                      rows="3"
                                      required
                                      placeholder="Enter the question..."
                                      style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; resize: vertical;">{{ old('questions.' . $questionIndex . '.question', $question->question) }}</textarea>
                        </div>
                        
                        <div style="margin-bottom: 1rem;">
                            <label class="form-label">Explanation (Optional)</label>
                            <textarea name="questions[{{ $questionIndex }}][explanation]"
                                      rows="2"
                                      placeholder="Add explanation for the correct answer..."
                                      style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; resize: vertical;">{{ old('questions.' . $questionIndex . '.explanation', $question->explanation) }}</textarea>
                        </div>
                        
                        <div class="options-container" style="margin-top: 1rem;">
                            <h5 style="font-size: 0.875rem; font-weight: 600; color: var(--dark); margin-bottom: 0.75rem;">
                                Options (Select one as correct answer)
                            </h5>
                            <div class="options-list">
                                @foreach($question->options as $optionIndex => $option)
                                <div class="option-item" style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem; padding: 12px; background: white; border: 1px solid var(--border); border-radius: 6px;">
                                    <input type="hidden" name="questions[{{ $questionIndex }}][options][{{ $optionIndex }}][id]" value="{{ $option->id }}">
                                    <input type="radio" 
                                           name="questions[{{ $questionIndex }}][correct_answer]"
                                           value="{{ $optionIndex }}"
                                           {{ $option->is_correct ? 'checked' : '' }}>
                                    <input type="text" 
                                           name="questions[{{ $questionIndex }}][options][{{ $optionIndex }}][option_text]"
                                           value="{{ old('questions.' . $questionIndex . '.options.' . $optionIndex . '.option_text', $option->option_text) }}"
                                           placeholder="Enter option text"
                                           required
                                           style="flex: 1; padding: 8px 12px; border: 1px solid var(--border); border-radius: 4px;">
                                    <button type="button" 
                                            class="remove-option-btn"
                                            style="padding: 4px 8px; background: #fee2e2; color: var(--danger); border: none; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            
                            <button type="button" 
                                    class="add-option-btn"
                                    style="margin-top: 0.5rem; padding: 8px 16px; background: #e0e7ff; color: var(--primary); border: none; border-radius: 6px; cursor: pointer; font-size: 0.875rem; font-weight: 500;">
                                <i class="fas fa-plus"></i> Add Option
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <button type="button" 
                        id="add-question-btn"
                        style="margin-top: 1rem; padding: 10px 20px; background: #f3f4f6; color: var(--dark); border: 1px dashed var(--border); border-radius: 6px; width: 100%; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-plus-circle"></i> Add New Question
                </button>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 1rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <a href="{{ route('admin.quizzes.show', Crypt::encrypt($quiz->id)) }}" 
                   style="padding: 10px 20px; background: transparent; color: var(--secondary); border: 1px solid var(--secondary); border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" 
                        style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-save"></i> Update Quiz
                </button>
            </div>
        </form>
        
        <!-- DELETE FORM -->
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
            <h3 style="font-size: 1rem; font-weight: 600; color: var(--danger); margin-bottom: 1rem;">
                <i class="fas fa-exclamation-triangle"></i> Danger Zone
            </h3>
            <form action="{{ route('admin.quizzes.destroy', Crypt::encrypt($quiz->id)) }}" method="POST" id="delete-form">
                @csrf
                @method('DELETE')
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 1rem;">
                    Once you delete a quiz, there is no going back. Please be certain.
                </p>
                <button type="button" 
                        onclick="if(confirm('Are you sure?')) this.form.submit();"
                        style="padding: 10px 20px; background: transparent; color: var(--danger); border: 1px solid var(--danger); border-radius: 6px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-trash"></i> Delete Quiz
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Template for NEW question (only used for adding NEW questions) -->
<template id="new-question-template">
    <div class="question-card" data-question-id="new" style="margin-bottom: 1.5rem; padding: 1.5rem; border: 1px solid var(--border); border-radius: 8px; background: #f8fafc;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h4 style="font-size: 0.875rem; font-weight: 600; color: var(--dark);" class="question-title">New Question</h4>
            <button type="button" class="remove-question-btn" style="padding: 4px 8px; background: #fee2e2; color: var(--danger); border: none; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">
                <i class="fas fa-trash"></i> Remove
            </button>
        </div>
        
        <input type="hidden" class="question-id" name="" value="">
        
        <div style="margin-bottom: 1rem;">
            <label class="form-label">Question Text *</label>
            <textarea class="question-text"
                      rows="3"
                      required
                      placeholder="Enter the question..."
                      style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; resize: vertical;"></textarea>
        </div>
        
        <div style="margin-bottom: 1rem;">
            <label class="form-label">Explanation (Optional)</label>
            <textarea class="question-explanation"
                      rows="2"
                      placeholder="Add explanation for the correct answer..."
                      style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; resize: vertical;"></textarea>
        </div>
        
        <div class="options-container" style="margin-top: 1rem;">
            <h5 style="font-size: 0.875rem; font-weight: 600; color: var(--dark); margin-bottom: 0.75rem;">
                Options (Select one as correct answer)
            </h5>
            <div class="options-list">
                <!-- Options will be added here -->
            </div>
            
            <button type="button" 
                    class="add-option-btn"
                    style="margin-top: 0.5rem; padding: 8px 16px; background: #e0e7ff; color: var(--primary); border: none; border-radius: 6px; cursor: pointer; font-size: 0.875rem; font-weight: 500;">
                <i class="fas fa-plus"></i> Add Option
            </button>
        </div>
    </div>
</template>

<!-- Template for NEW option -->
<template id="new-option-template">
    <div class="option-item" style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem; padding: 12px; background: white; border: 1px solid var(--border); border-radius: 6px;">
        <input type="radio" class="is-correct-checkbox" name="" value="">
        <input type="text" 
               class="option-text"
               placeholder="Enter option text"
               required
               style="flex: 1; padding: 8px 12px; border: 1px solid var(--border); border-radius: 4px;">
        <button type="button" 
                class="remove-option-btn"
                style="padding: 4px 8px; background: #fee2e2; color: var(--danger); border: none; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">
            <i class="fas fa-times"></i>
        </button>
    </div>
</template>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const questionsContainer = document.getElementById('questions-list');
        const addQuestionBtn = document.getElementById('add-question-btn');
        const newQuestionTemplate = document.getElementById('new-question-template');
        const newOptionTemplate = document.getElementById('new-option-template');
        
        let questionCount = {{ $quiz->questions->count() }};
        let newQuestionIndex = questionCount;
        
        // Add new question button click event
        addQuestionBtn.addEventListener('click', function() {
            const questionClone = newQuestionTemplate.content.cloneNode(true);
            const questionCard = questionClone.querySelector('.question-card');
            
            // Update input names for new question
            const questionText = questionCard.querySelector('.question-text');
            questionText.name = `questions[${newQuestionIndex}][question]`;
            
            const questionExplanation = questionCard.querySelector('.question-explanation');
            questionExplanation.name = `questions[${newQuestionIndex}][explanation]`;
            
            const questionIdInput = questionCard.querySelector('.question-id');
            questionIdInput.name = `questions[${newQuestionIndex}][id]`;
            questionIdInput.value = '';
            
            // Add remove question event
            const removeBtn = questionCard.querySelector('.remove-question-btn');
            removeBtn.addEventListener('click', function() {
                if (confirm('Remove this question?')) {
                    questionCard.remove();
                }
            });
            
            // Add option button event
            const addOptionBtn = questionCard.querySelector('.add-option-btn');
            addOptionBtn.addEventListener('click', function() {
                addNewOption(questionCard, newQuestionIndex);
            });
            
            // Add 4 default options for new question
            for (let i = 0; i < 4; i++) {
                addNewOption(questionCard, newQuestionIndex);
            }
            
            // Append to questions list
            questionsContainer.appendChild(questionCard);
            newQuestionIndex++;
        });
        
        // Add new option function
        function addNewOption(questionCard, qIndex) {
            const optionsList = questionCard.querySelector('.options-list');
            const optionClone = newOptionTemplate.content.cloneNode(true);
            const optionItem = optionClone.querySelector('.option-item');
            
            // Get current option count
            const optionCount = optionsList.children.length;
            
            // Update radio button
            const radio = optionItem.querySelector('.is-correct-checkbox');
            radio.name = `questions[${qIndex}][correct_answer]`;
            radio.value = optionCount;
            
            // Set first option as checked by default for new questions
            if (optionCount === 0) {
                radio.checked = true;
            }
            
            // Update option text input
            const optionText = optionItem.querySelector('.option-text');
            optionText.name = `questions[${qIndex}][options][${optionCount}][option_text]`;
            
            // Add hidden ID input (empty for new options)
            const optionIdInput = document.createElement('input');
            optionIdInput.type = 'hidden';
            optionIdInput.name = `questions[${qIndex}][options][${optionCount}][id]`;
            optionIdInput.value = '';
            optionItem.appendChild(optionIdInput);
            
            // Add remove option event
            const removeBtn = optionItem.querySelector('.remove-option-btn');
            removeBtn.addEventListener('click', function() {
                optionItem.remove();
            });
            
            // Append to options list
            optionsList.appendChild(optionItem);
        }
        
        // Add event listeners for existing remove buttons
        document.querySelectorAll('.remove-question-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Remove this question?')) {
                    this.closest('.question-card').remove();
                }
            });
        });
        
        document.querySelectorAll('.remove-option-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.option-item').remove();
            });
        });
        
        // Add option buttons for existing questions
        document.querySelectorAll('.add-option-btn').forEach((btn, index) => {
            btn.addEventListener('click', function() {
                const questionCard = this.closest('.question-card');
                const qIndex = index; // Use the existing question index
                const optionsList = questionCard.querySelector('.options-list');
                
                // Get current option count
                const optionCount = optionsList.children.length;
                
                // Create a new option item
                const optionClone = newOptionTemplate.content.cloneNode(true);
                const optionItem = optionClone.querySelector('.option-item');
                
                // Update radio button
                const radio = optionItem.querySelector('.is-correct-checkbox');
                radio.name = `questions[${qIndex}][correct_answer]`;
                radio.value = optionCount;
                
                // Update option text input
                const optionText = optionItem.querySelector('.option-text');
                optionText.name = `questions[${qIndex}][options][${optionCount}][option_text]`;
                
                // Add hidden ID input (empty for new options added to existing questions)
                const optionIdInput = document.createElement('input');
                optionIdInput.type = 'hidden';
                optionIdInput.name = `questions[${qIndex}][options][${optionCount}][id]`;
                optionIdInput.value = '';
                optionItem.appendChild(optionIdInput);
                
                // Add remove option event
                const removeBtn = optionItem.querySelector('.remove-option-btn');
                removeBtn.addEventListener('click', function() {
                    optionItem.remove();
                });
                
                // Append to options list
                optionsList.appendChild(optionItem);
            });
        });
        
        // Form validation
        document.getElementById('quiz-form').addEventListener('submit', function(e) {
            const questionCards = document.querySelectorAll('.question-card');
            
            if (questionCards.length === 0) {
                e.preventDefault();
                alert('Please add at least one question.');
                return;
            }
            
            let valid = true;
            
            questionCards.forEach((card, index) => {
                const questionText = card.querySelector('textarea[name$="[question]"]').value.trim();
                if (!questionText) {
                    valid = false;
                    alert(`Question ${index + 1} must have text.`);
                    return;
                }
                
                const options = card.querySelectorAll('.option-item');
                if (options.length < 2) {
                    valid = false;
                    alert(`Question ${index + 1} must have at least 2 options.`);
                    return;
                }
                
                const questionName = card.querySelector('textarea[name$="[question]"]').name;
                const qIndexMatch = questionName.match(/questions\[(\d+)\]\[question\]/);
                if (qIndexMatch) {
                    const qIndex = qIndexMatch[1];
                    const checkedRadio = document.querySelector(`input[name="questions[${qIndex}][correct_answer]"]:checked`);
                    if (!checkedRadio) {
                        valid = false;
                        alert(`Question ${index + 1} must have a correct answer selected.`);
                        return;
                    }
                }
                
                options.forEach((option, optIndex) => {
                    const optionText = option.querySelector('input[type="text"]').value.trim();
                    if (!optionText) {
                        valid = false;
                        alert(`Question ${index + 1}, Option ${optIndex + 1} must have text.`);
                        return;
                    }
                });
            });
            
            if (!valid) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush
@endsection