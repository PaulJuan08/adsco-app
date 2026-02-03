@extends('layouts.teacher')

@section('title', 'Create New Quiz')

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

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Quiz Information</h2>
        <a href="{{ route('teacher.quizzes.index') }}" style="display: flex; align-items: center; gap: 6px; color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
            <i class="fas fa-arrow-left"></i> Back to Quizzes
        </a>
    </div>
    
    <div style="padding: 1.5rem;">
        <form action="{{ route('teacher.quizzes.store') }}" method="POST" id="quiz-form">
            @csrf
            
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
                
                <div style="margin-bottom: 1.5rem;">
                    <label for="title" class="form-label">Quiz Title *</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title') }}" 
                           required
                           placeholder="e.g., JavaScript Fundamentals Quiz"
                           style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%;">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label for="description" class="form-label">Description *</label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              required
                              placeholder="Describe what this quiz covers..."
                              style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; resize: vertical;">{{ old('description') }}</textarea>
                </div>
            </div>
            
            <!-- Questions Section -->
            <div style="margin-bottom: 2rem;" id="questions-container">
                <h3 style="font-size: 1rem; font-weight: 600; color: var(--dark); margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border);">
                    Questions & Options
                </h3>
                
                <div id="questions-list">
                    <!-- Questions will be added here dynamically -->
                </div>
                
                <button type="button" 
                        id="add-question-btn"
                        style="margin-top: 1rem; padding: 10px 20px; background: #f3f4f6; color: var(--dark); border: 1px dashed var(--border); border-radius: 6px; width: 100%; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-plus-circle"></i> Add Question
                </button>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 1rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <a href="{{ route('teacher.quizzes.index') }}" 
                   style="padding: 10px 20px; background: transparent; color: var(--secondary); border: 1px solid var(--secondary); border-radius: 6px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" 
                        style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-save"></i> Create Quiz
                </button>
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
        
        <div style="margin-bottom: 1rem;">
            <label class="form-label">Question Text *</label>
            <textarea name="questions[0][question]" 
                      class="question-text"
                      rows="3"
                      required
                      placeholder="Enter the question..."
                      style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%; resize: vertical;"></textarea>
        </div>
        
        <div style="margin-bottom: 1rem;">
            <label class="form-label">Question Type</label>
            <select name="questions[0][type]" class="question-type" style="padding: 12px; border: 1px solid var(--border); border-radius: 8px; width: 100%;">
                <option value="single">Single Correct Answer</option>
                <option value="multiple">Multiple Correct Answers</option>
            </select>
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
    let questionCount = 0;
    let optionCounts = {};
    
    document.addEventListener('DOMContentLoaded', function() {
        const questionsContainer = document.getElementById('questions-list');
        const addQuestionBtn = document.getElementById('add-question-btn');
        const questionTemplate = document.getElementById('question-template');
        const optionTemplate = document.getElementById('option-template');
        
        // Add first question by default
        addQuestion();
        
        // Add question button click event
        addQuestionBtn.addEventListener('click', addQuestion);
        
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
                updateOptionCheckboxes(questionCard, this.value, questionCount);
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
            
            // Get current question type and update checkbox for this new option
            const questionType = questionCard.querySelector('.question-type').value;
            updateSingleOptionCheckbox(questionCard, questionType, questionIndex, optionCount - 1); // -1 because we just incremented
            
            // Also update all existing checkboxes to ensure consistency
            updateOptionCheckboxes(questionCard, questionType, questionIndex);
        }
        
        function updateSingleOptionCheckbox(questionCard, questionType, questionIndex, optionIndex) {
            const checkboxes = questionCard.querySelectorAll('.is-correct-checkbox');
            const checkbox = checkboxes[optionIndex];
            
            if (checkbox) {
                if (questionType === 'multiple') {
                    // For multiple choice: keep as checkbox
                    checkbox.type = 'checkbox';
                    checkbox.name = `questions[${questionIndex}][options][${optionIndex}][is_correct]`;
                    checkbox.value = '1';
                } else {
                    // For single choice: change to radio
                    checkbox.type = 'radio';
                    checkbox.name = `questions[${questionIndex}][correct_answer]`;
                    checkbox.value = optionIndex;
                }
            }
        }
        
        function updateOptionCheckboxes(questionCard, questionType, questionIndex) {
            const checkboxes = questionCard.querySelectorAll('.is-correct-checkbox');
            const optionsList = questionCard.querySelectorAll('.option-item');
            
            checkboxes.forEach((checkbox, index) => {
                if (questionType === 'multiple') {
                    // For multiple choice: keep as checkbox
                    checkbox.type = 'checkbox';
                    checkbox.name = `questions[${questionIndex}][options][${index}][is_correct]`;
                    checkbox.value = '1';
                } else {
                    // For single choice: change to radio
                    checkbox.type = 'radio';
                    checkbox.name = `questions[${questionIndex}][correct_answer]`;
                    checkbox.value = index;
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
                
                // Re-update checkbox types for this question
                const questionType = card.querySelector('.question-type').value;
                updateOptionCheckboxes(card, questionType, index);
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