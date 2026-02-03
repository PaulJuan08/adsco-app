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
            
            <!-- Basic Quiz Info - SIMPLIFIED -->
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
        
        <div class="options-container" style="margin-top: 1rem;">
            <h5 style="font-size: 0.875rem; font-weight: 600; color: var(--dark); margin-bottom: 0.75rem;">
                Options (Select one as correct answer)
            </h5>
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
        <input type="hidden" class="option-id" value="">
        <input type="radio" 
               class="is-correct-checkbox"
               value="0">
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
        
        // Add first question by default
        addQuestion();
        
        // Add question button click event
        addQuestionBtn.addEventListener('click', addQuestion);
        
        function addQuestion() {
            const questionClone = questionTemplate.content.cloneNode(true);
            const questionCard = questionClone.querySelector('.question-card');
            
            // Update question number display
            questionCard.querySelector('.question-number').textContent = questionCount + 1;
            
            // Clear textarea for new question
            const questionText = questionCard.querySelector('.question-text');
            if (questionText) {
                questionText.value = '';
            }
            
            // Update all input names with current question count
            updateQuestionNames(questionCard, questionCount);
            
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
            
            // Add 4 default options
            const optionsList = questionCard.querySelector('.options-list');
            for (let i = 0; i < 4; i++) {
                addOption(questionCard, questionCount);
            }
            
            // Append to questions list
            questionsContainer.appendChild(questionCard);
            questionCount++;
        }
        
        function updateQuestionNames(questionCard, questionIndex) {
            // Update question inputs
            const questionInputs = questionCard.querySelectorAll('[name]');
            questionInputs.forEach(input => {
                let name = input.getAttribute('name');
                name = name.replace(/questions\[0\]/, `questions[${questionIndex}]`);
                input.setAttribute('name', name);
            });
            
            // Update option names - THIS IS THE KEY FIX
            const optionsList = questionCard.querySelector('.options-list');
            const options = optionsList.querySelectorAll('.option-item');
            
            options.forEach((optionItem, optionIndex) => {
                const optionInputs = optionItem.querySelectorAll('[name]');
                optionInputs.forEach(input => {
                    let name = input.getAttribute('name');
                    
                    // Replace question index
                    name = name.replace(/questions\[0\]/, `questions[${questionIndex}]`);
                    
                    // Replace option index
                    name = name.replace(/options\[0\]/, `options[${optionIndex}]`);
                    
                    // Update radio button value
                    if (input.classList.contains('is-correct-checkbox')) {
                        input.value = optionIndex;
                    }
                    
                    input.setAttribute('name', name);
                });
            });
        }
        
        function addOption(questionCard, questionIndex) {
            const optionsList = questionCard.querySelector('.options-list');
            const optionClone = optionTemplate.content.cloneNode(true);
            const optionItem = optionClone.querySelector('.option-item');
            
            // Get current option count
            const optionCount = optionsList.children.length;
            
            // Create unique names for this option
            const optionIdInput = document.createElement('input');
            optionIdInput.type = 'hidden';
            optionIdInput.className = 'option-id';
            optionIdInput.name = `questions[${questionIndex}][options][${optionCount}][id]`;
            optionIdInput.value = '';
            
            const radioInput = optionItem.querySelector('.is-correct-checkbox');
            radioInput.name = `questions[${questionIndex}][correct_answer]`;
            radioInput.value = optionCount;
            
            const optionTextInput = optionItem.querySelector('.option-text');
            optionTextInput.name = `questions[${questionIndex}][options][${optionCount}][option_text]`;
            
            // Replace the default inputs with our named ones
            optionItem.querySelector('.option-id')?.replaceWith(optionIdInput);
            optionItem.querySelector('.is-correct-checkbox')?.replaceWith(radioInput);
            optionItem.querySelector('.option-text')?.replaceWith(optionTextInput);
            
            // Clear option text
            optionTextInput.value = '';
            
            // Add remove option event
            const removeBtn = optionItem.querySelector('.remove-option-btn');
            removeBtn.addEventListener('click', function() {
                optionItem.remove();
                updateRadioButtonValues(questionCard, questionIndex);
            });
            
            // Set first option as checked by default
            if (optionCount === 0) {
                radioInput.checked = true;
            }
            
            // Append to options list
            optionsList.appendChild(optionItem);
        }
        
        function updateRadioButtonValues(questionCard, questionIndex) {
            const optionsList = questionCard.querySelector('.options-list');
            const options = optionsList.querySelectorAll('.option-item');
            
            options.forEach((optionItem, index) => {
                const radio = optionItem.querySelector('.is-correct-checkbox');
                if (radio) {
                    radio.value = index;
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
                    questionNumberSpan.textContent = qIndex + 1;
                }
                
                // Update question text name
                const questionText = card.querySelector('.question-text');
                if (questionText) {
                    questionText.name = `questions[${qIndex}][question]`;
                }
                
                // Update options
                const optionsList = card.querySelector('.options-list');
                if (optionsList) {
                    const options = optionsList.querySelectorAll('.option-item');
                    
                    options.forEach((optionItem, oIndex) => {
                        // Update option ID input
                        const optionIdInput = optionItem.querySelector('.option-id');
                        if (optionIdInput) {
                            optionIdInput.name = `questions[${qIndex}][options][${oIndex}][id]`;
                        }
                        
                        // Update radio button
                        const radioInput = optionItem.querySelector('.is-correct-checkbox');
                        if (radioInput) {
                            radioInput.name = `questions[${qIndex}][correct_answer]`;
                            radioInput.value = oIndex;
                        }
                        
                        // Update option text input
                        const optionTextInput = optionItem.querySelector('.option-text');
                        if (optionTextInput) {
                            optionTextInput.name = `questions[${qIndex}][options][${oIndex}][option_text]`;
                        }
                    });
                }
            });
        }
        
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
                const options = card.querySelectorAll('.option-item');
                if (options.length < 2) {
                    valid = false;
                    alert(`Question ${index + 1} must have at least 2 options.`);
                    return;
                }
                
                // Check one correct answer is selected
                const checkedRadio = card.querySelector('input[type="radio"]:checked');
                if (!checkedRadio) {
                    valid = false;
                    alert(`Question ${index + 1} must have one correct answer selected.`);
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