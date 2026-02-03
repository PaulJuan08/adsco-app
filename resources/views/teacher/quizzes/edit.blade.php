@extends('layouts.teacher')

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
        <a href="{{ route('teacher.quizzes.show', Crypt::encrypt($quiz->id)) }}" style="display: flex; align-items: center; gap: 6px; color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
            <i class="fas fa-arrow-left"></i> Back to View
        </a>
    </div>
    
    <div style="padding: 1.5rem;">
        <form action="{{ route('teacher.quizzes.update', Crypt::encrypt($quiz->id)) }}" method="POST" id="quiz-form">
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
                    
                    <div>
                        <label for="points_per_question" class="form-label">Default Points per Question</label>
                        <input type="number" 
                               id="points_per_question" 
                               name="points_per_question" 
                               value="{{ old('points_per_question', 1) }}"
                               min="1"
                               placeholder="1"
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
                    <!-- Questions will be populated by JavaScript -->
                </div>
                
                <button type="button" 
                        id="add-question-btn"
                        style="margin-top: 1rem; padding: 10px 20px; background: #f3f4f6; color: var(--dark); border: 1px dashed var(--border); border-radius: 6px; width: 100%; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-plus-circle"></i> Add Question
                </button>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <form action="{{ route('teacher.quizzes.destroy', Crypt::encrypt($quiz->id)) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Are you sure you want to delete this quiz? This action cannot be undone.')"
                            style="padding: 10px 20px; background: transparent; color: var(--danger); border: 1px solid var(--danger); border-radius: 6px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-trash"></i> Delete Quiz
                    </button>
                </form>
                
                <div style="display: flex; gap: 1rem;">
                    <a href="{{ route('teacher.quizzes.show', Crypt::encrypt($quiz->id)) }}" 
                       style="padding: 10px 20px; background: transparent; color: var(--secondary); border: 1px solid var(--secondary); border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" 
                            style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-save"></i> Update Quiz
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<template id="question-template">
    <div class="question-card" style="margin-bottom: 1.5rem; padding: 1.5rem; border: 1px solid var(--border); border-radius: 8px; background: #f8fafc;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h4 style="font-size: 0.875rem; font-weight: 600; color: var(--dark);" class="question-title">Question <span class="question-number">1</span></h4>
            <button type="button" class="remove-question-btn" style="padding: 4px 8px; background: #fee2e2; color: var(--danger); border: none; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">
                <i class="fas fa-trash"></i> Remove
            </button>
        </div>
        
        <input type="hidden" class="question-id" name="questions[0][id]" value="">
        
        <div style="margin-bottom: 1rem;">
            <label class="form-label">Question Text *</label>
            <textarea name="questions[0][question]" 
                      class="question-text"
                      rows="3"
                      required
                      placeholder="Enter the question..."
                      style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; resize: vertical;"></textarea>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label class="form-label">Question Type</label>
                <select name="questions[0][type]" class="question-type" style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%;">
                    <option value="single">Single Correct Answer</option>
                    <option value="multiple">Multiple Correct Answers</option>
                </select>
            </div>
            
            <div>
                <label class="form-label">Points</label>
                <input type="number" 
                       name="questions[0][points]" 
                       class="question-points"
                       value="1"
                       min="1"
                       style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%;">
            </div>
        </div>
        
        <div style="margin-bottom: 1rem;">
            <label class="form-label">Explanation (Optional)</label>
            <textarea name="questions[0][explanation]" 
                      class="question-explanation"
                      rows="2"
                      placeholder="Add explanation for the correct answer..."
                      style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; resize: vertical;"></textarea>
        </div>
        
        <div class="options-container" style="margin-top: 1rem;">
            <h5 style="font-size: 0.875rem; font-weight: 600; color: var(--dark); margin-bottom: 0.75rem;">Options</h5>
            <div class="options-list">
                <!-- Options will be added here dynamically -->
            </div>
            
            <button type="button" 
                    class="add-option-btn"
                    style="margin-top: 0.5rem; padding: 8px 16px; background: #e0e7ff; color: var(--primary); border: none; border-radius: 6px; cursor: pointer; font-size: 0.875rem; font-weight: 500;">
                <i class="fas fa-plus"></i> Add Option
            </button>
        </div>
    </div>
</template>

<template id="option-template">
    <div class="option-item" style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem; padding: 12px; background: white; border: 1px solid var(--border); border-radius: 6px;">
        <input type="hidden" class="option-id" name="questions[0][options][0][id]" value="">
        <input type="checkbox" 
               class="is-correct-checkbox"
               name="questions[0][options][0][is_correct]"
               value="1">
        <input type="text" 
               class="option-text"
               name="questions[0][options][0][option_text]"
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

<style>
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--dark);
        font-size: 0.875rem;
    }
    
    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border);
    }
    
    input, select, textarea {
        transition: border-color 0.15s ease-in-out;
    }
    
    input:focus, select:focus, textarea:focus {
        outline: none;
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const questionsContainer = document.getElementById('questions-list');
        const addQuestionBtn = document.getElementById('add-question-btn');
        const questionTemplate = document.getElementById('question-template');
        const optionTemplate = document.getElementById('option-template');
        
        let questionCount = 0;
        let optionCounts = {};
        
        // Load existing questions
        @foreach($quiz->questions as $question)
            addExistingQuestion(@json($question));
        @endforeach
        
        // If no questions, add one by default
        if (questionCount === 0) {
            addQuestion();
        }
        
        // Add question button click event
        addQuestionBtn.addEventListener('click', addQuestion);
        
        function addExistingQuestion(question) {
            const questionClone = questionTemplate.content.cloneNode(true);
            const questionCard = questionClone.querySelector('.question-card');
            const questionNumber = questionCount + 1;
            
            // Update question number
            questionCard.querySelector('.question-number').textContent = questionNumber;
            questionCard.querySelector('.question-id').value = question.id;
            questionCard.querySelector('.question-text').value = question.question;
            questionCard.querySelector('.question-type').value = question.type;
            questionCard.querySelector('.question-points').value = question.points;
            questionCard.querySelector('.question-explanation').value = question.explanation || '';
            
            // Update all input names with current question count
            const questionInputs = questionCard.querySelectorAll('[name]');
            questionInputs.forEach(input => {
                const name = input.getAttribute('name');
                const newName = name.replace(/questions\[\d+\]/, `questions[${questionCount}]`);
                input.setAttribute('name', newName);
            });
            
            // Add remove question event
            const removeBtn = questionCard.querySelector('.remove-question-btn');
            removeBtn.addEventListener('click', function() {
                questionCard.remove();
                updateQuestionNumbers();
            });
            
            // Add option button event
            const addOptionBtn = questionCard.querySelector('.add-option-btn');
            addOptionBtn.addEventListener('click', function() {
                addOption(questionCard, questionCount);
            });
            
            // Add question type change event
            const questionType = questionCard.querySelector('.question-type');
            questionType.addEventListener('change', function() {
                updateOptionCheckboxes(questionCard, this.value);
            });
            
            // Initialize option count for this question
            optionCounts[questionCount] = 0;
            
            // Add existing options
            question.options.forEach(option => {
                addExistingOption(questionCard, questionCount, option);
            });
            
            // Update option checkboxes based on question type
            updateOptionCheckboxes(questionCard, question.type);
            
            // Append to questions list
            questionsContainer.appendChild(questionCard);
            questionCount++;
        }
        
        function addQuestion() {
            const questionClone = questionTemplate.content.cloneNode(true);
            const questionCard = questionClone.querySelector('.question-card');
            const questionNumber = questionCount + 1;
            
            // Update question number
            questionCard.querySelector('.question-number').textContent = questionNumber;
            
            // Update all input names with current question count
            const questionInputs = questionCard.querySelectorAll('[name]');
            questionInputs.forEach(input => {
                const name = input.getAttribute('name');
                const newName = name.replace(/questions\[\d+\]/, `questions[${questionCount}]`);
                input.setAttribute('name', newName);
            });
            
            // Add remove question event
            const removeBtn = questionCard.querySelector('.remove-question-btn');
            removeBtn.addEventListener('click', function() {
                questionCard.remove();
                updateQuestionNumbers();
            });
            
            // Add option button event
            const addOptionBtn = questionCard.querySelector('.add-option-btn');
            addOptionBtn.addEventListener('click', function() {
                addOption(questionCard, questionCount);
            });
            
            // Add question type change event
            const questionType = questionCard.querySelector('.question-type');
            questionType.addEventListener('change', function() {
                updateOptionCheckboxes(questionCard, this.value);
            });
            
            // Initialize option count for this question
            optionCounts[questionCount] = 0;
            
            // Add 4 options by default
            for (let i = 0; i < 4; i++) {
                addOption(questionCard, questionCount);
            }
            
            // Append to questions list
            questionsContainer.appendChild(questionCard);
            questionCount++;
        }
        
        function addExistingOption(questionCard, questionIndex, option) {
            const optionsList = questionCard.querySelector('.options-list');
            const optionClone = optionTemplate.content.cloneNode(true);
            const optionItem = optionClone.querySelector('.option-item');
            const optionCount = optionCounts[questionIndex];
            
            // Update input names and values
            const optionInputs = optionItem.querySelectorAll('[name]');
            optionInputs.forEach(input => {
                const name = input.getAttribute('name');
                const newName = name.replace(/questions\[\d+\]\[options\]\[\d+\]/, `questions[${questionIndex}][options][${optionCount}]`);
                input.setAttribute('name', newName);
                
                if (input.classList.contains('option-id')) {
                    input.value = option.id;
                } else if (input.classList.contains('option-text')) {
                    input.value = option.option_text;
                } else if (input.classList.contains('is-correct-checkbox')) {
                    input.checked = option.is_correct;
                }
            });
            
            // Add remove option event
            const removeBtn = optionItem.querySelector('.remove-option-btn');
            removeBtn.addEventListener('click', function() {
                optionItem.remove();
            });
            
            // Append to options list
            optionsList.appendChild(optionItem);
            optionCounts[questionIndex]++;
        }
        
        function addOption(questionCard, questionIndex) {
            const optionsList = questionCard.querySelector('.options-list');
            const optionClone = optionTemplate.content.cloneNode(true);
            const optionItem = optionClone.querySelector('.option-item');
            const optionCount = optionCounts[questionIndex];
            
            // Update input names
            const optionInputs = optionItem.querySelectorAll('[name]');
            optionInputs.forEach(input => {
                const name = input.getAttribute('name');
                const newName = name.replace(/questions\[\d+\]\[options\]\[\d+\]/, `questions[${questionIndex}][options][${optionCount}]`);
                input.setAttribute('name', newName);
            });
            
            // Add remove option event
            const removeBtn = optionItem.querySelector('.remove-option-btn');
            removeBtn.addEventListener('click', function() {
                optionItem.remove();
            });
            
            // Append to options list
            optionsList.appendChild(optionItem);
            optionCounts[questionIndex]++;
            
            // Update checkbox type based on question type
            const questionType = questionCard.querySelector('.question-type').value;
            updateOptionCheckboxes(questionCard, questionType);
        }
        
        function updateOptionCheckboxes(questionCard, questionType) {
            const checkboxes = questionCard.querySelectorAll('.is-correct-checkbox');
            checkboxes.forEach(checkbox => {
                if (questionType === 'multiple') {
                    checkbox.type = 'checkbox';
                    checkbox.name = checkbox.name.replace('is_correct', 'correct_options[]');
                } else {
                    checkbox.type = 'radio';
                    checkbox.name = checkbox.name.replace(/\[\d+\]\[\d+\]/, `[${questionCard.dataset.index}][correct_answer]`);
                }
            });
        }
        
        function updateQuestionNumbers() {
            const questionCards = document.querySelectorAll('.question-card');
            questionCards.forEach((card, index) => {
                card.querySelector('.question-number').textContent = index + 1;
                
                // Update all input names
                const questionInputs = card.querySelectorAll('[name]');
                questionInputs.forEach(input => {
                    const oldName = input.getAttribute('name');
                    const newName = oldName.replace(/questions\[\d+\]/, `questions[${index}]`);
                    input.setAttribute('name', newName);
                });
            });
            
            questionCount = questionCards.length;
        }
        
        // Form validation
        document.getElementById('quiz-form').addEventListener('submit', function(e) {
            const questionCards = document.querySelectorAll('.question-card');
            
            if (questionCards.length === 0) {
                e.preventDefault();
                alert('Please add at least one question.');
                return;
            }
            
            // Check each question has at least 2 options
            let valid = true;
            questionCards.forEach((card, index) => {
                const options = card.querySelectorAll('.option-item');
                if (options.length < 2) {
                    valid = false;
                    alert(`Question ${index + 1} must have at least 2 options.`);
                }
                
                // Check at least one correct option is selected
                const questionType = card.querySelector('.question-type').value;
                const checkedOptions = card.querySelectorAll('.is-correct-checkbox:checked');
                if (checkedOptions.length === 0) {
                    valid = false;
                    alert(`Question ${index + 1} must have at least one correct answer.`);
                }
                
                // For single answer questions, ensure only one is selected
                if (questionType === 'single' && checkedOptions.length > 1) {
                    valid = false;
                    alert(`Question ${index + 1} can only have one correct answer.`);
                }
            });
            
            if (!valid) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush
@endsection