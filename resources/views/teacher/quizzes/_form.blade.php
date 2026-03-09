<style>
#crudModalBody .form-group{margin-bottom:1.1rem;}
#crudModalBody .form-label{display:flex;align-items:center;gap:.25rem;font-size:.8125rem;font-weight:600;color:#2d3748;margin-bottom:.375rem;}
#crudModalBody .form-label.required::after{content:" *";color:#f56565;}
#crudModalBody .form-input,#crudModalBody .form-textarea,#crudModalBody .form-select{display:block;width:100%;padding:.55rem .75rem;font-size:.875rem;color:#1a202c;background:#fff;border:1.5px solid #e2e8f0;border-radius:8px;transition:border-color .2s;box-sizing:border-box;}
#crudModalBody .form-input:focus,#crudModalBody .form-textarea:focus{border-color:#552b20;outline:0;box-shadow:0 0 0 3px rgba(85,43,32,.1);}
#crudModalBody .form-textarea{min-height:80px;resize:vertical;font-family:inherit;}
#crudModalBody .form-grid{display:grid;gap:1rem;margin-bottom:.5rem;}
/* Toggle */
#crudModalBody .toggle-switch{position:relative;display:inline-block;width:52px;height:26px;flex-shrink:0;}
#crudModalBody .toggle-switch input{opacity:0;width:0;height:0;}
#crudModalBody .toggle-slider{position:absolute;cursor:pointer;inset:0;background:#cbd5e0;border-radius:26px;transition:.3s;}
#crudModalBody .toggle-slider::before{position:absolute;content:"";height:20px;width:20px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s;box-shadow:0 2px 4px rgba(0,0,0,.2);}
#crudModalBody .toggle-switch input:checked + .toggle-slider{background:linear-gradient(135deg,#48bb78,#38a169);}
#crudModalBody .toggle-switch input:checked + .toggle-slider::before{transform:translateX(26px);}
/* Question cards */
#crudModalBody .question-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:1.25rem;margin-bottom:1rem;position:relative;overflow:hidden;}
#crudModalBody .question-card::before{content:'';position:absolute;top:0;left:0;width:4px;height:100%;background:linear-gradient(135deg,#552b20,#3d1f17);border-radius:4px 0 0 4px;}
#crudModalBody .question-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;padding-bottom:.625rem;border-bottom:1px solid #edf2f7;}
#crudModalBody .question-number{font-size:.8rem;font-weight:700;color:#552b20;background:rgba(85,43,32,.1);padding:.3rem .85rem;border-radius:20px;border:1px solid rgba(85,43,32,.2);}
/* Options */
#crudModalBody .option-item{display:flex;align-items:center;gap:.75rem;padding:.75rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;margin-bottom:.5rem;transition:border-color .15s;}
#crudModalBody .option-item:hover{border-color:#552b20;}
#crudModalBody .option-radio{width:16px;height:16px;margin:0;cursor:pointer;accent-color:#552b20;flex-shrink:0;}
#crudModalBody .option-input{flex:1;padding:.45rem .7rem;border:1.5px solid #e2e8f0;border-radius:6px;font-size:.875rem;background:#fff;transition:border-color .15s;outline:none;}
#crudModalBody .option-input:focus{border-color:#552b20;box-shadow:0 0 0 3px rgba(85,43,32,.08);}
/* Buttons */
#crudModalBody .qf-btn{display:inline-flex;align-items:center;justify-content:center;gap:.4rem;padding:.55rem 1.1rem;font-size:.8125rem;font-weight:600;border-radius:8px;cursor:pointer;border:1.5px solid transparent;transition:all .2s;}
#crudModalBody .qf-btn-primary{background:linear-gradient(135deg,#552b20,#3d1f17);color:#fff;box-shadow:0 3px 10px rgba(85,43,32,.25);}
#crudModalBody .qf-btn-primary:hover{box-shadow:0 5px 15px rgba(85,43,32,.35);}
#crudModalBody .qf-btn-secondary{background:#fff;border-color:#cbd5e0;color:#4a5568;}
#crudModalBody .qf-btn-secondary:hover{background:#f7fafc;border-color:#a0aec0;}
#crudModalBody .qf-btn-danger{background:#fff5f5;border-color:#feb2b2;color:#c53030;padding:.35rem .7rem;font-size:.75rem;}
#crudModalBody .qf-btn-danger:hover{background:#fed7d7;}
#crudModalBody .qf-btn-add{background:rgba(85,43,32,.08);border:1.5px dashed #552b20;color:#552b20;width:100%;justify-content:center;padding:.65rem;}
#crudModalBody .qf-btn-add:hover{background:linear-gradient(135deg,#552b20,#3d1f17);color:#fff;border-style:solid;}
#crudModalBody .qf-btn-add-opt{background:#f0fff4;border:1.5px solid #9ae6b4;color:#276749;padding:.4rem .85rem;font-size:.75rem;margin-top:.35rem;}
#crudModalBody .qf-btn-add-opt:hover{background:#c6f6d5;border-color:#48bb78;}
</style>
<form action="{{ $formAction }}" method="POST">
    @csrf
    @if($editing ?? false)
        @method('PUT')
    @endif

    {{-- Basic Info --}}
    <div class="form-group">
        <label class="form-label required">Quiz Title</label>
        <input type="text" name="title" class="form-input"
               value="{{ old('title', $quiz->title ?? '') }}"
               required placeholder="e.g., Chapter 1 Quiz">
    </div>

    <div class="form-group">
        <label class="form-label required">Description</label>
        <textarea name="description" class="form-textarea" rows="2" required
                  data-quill
                  placeholder="Brief quiz description...">{{ old('description', $quiz->description ?? '') }}</textarea>
    </div>

    <div class="form-grid" style="grid-template-columns:1fr 1fr 1fr;">
        <div class="form-group">
            <label class="form-label">Duration (minutes)</label>
            <input type="number" name="duration" class="form-input" min="1"
                   value="{{ old('duration', $quiz->duration ?? 60) }}" placeholder="60">
        </div>
        <div class="form-group">
            <label class="form-label">Passing Score (%)</label>
            <input type="number" name="passing_score" class="form-input" min="1" max="100"
                   value="{{ old('passing_score', $quiz->passing_score ?? 70) }}" placeholder="70">
        </div>
        <div class="form-group">
            <label class="form-label">Due Date</label>
            <input type="date" name="due_date" class="form-input"
                   value="{{ old('due_date', isset($quiz->due_date) ? \Carbon\Carbon::parse($quiz->due_date)->format('Y-m-d') : '') }}">
        </div>
    </div>

    <div class="form-group" style="margin-bottom:1.25rem;">
        <label class="publish-label" style="display:flex;align-items:center;gap:.75rem;cursor:pointer;">
            <label class="toggle-switch" style="margin:0;">
                <input type="checkbox" name="is_published" value="1"
                       {{ old('is_published', ($quiz->is_published ?? false) ? '1' : '') ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
            <span id="quizPublishStatus" style="font-size:.875rem;font-weight:600;color:#374151;">
                {{ old('is_published', ($quiz->is_published ?? false) ? '1' : '') ? 'Published' : 'Draft' }}
            </span>
        </label>
    </div>

    {{-- Questions --}}
    <div style="border-top:1.5px solid #f0ebe8;padding-top:1rem;">
        <div style="font-size:.9375rem;font-weight:700;color:#2d3748;margin-bottom:.75rem;display:flex;align-items:center;gap:.5rem;">
            <i class="fas fa-list-ol" style="color:#552b20;"></i> Questions
        </div>

        <div id="quizQuestionsContainer">
            @if(!empty($quiz) && $quiz->questions && $quiz->questions->count())
                @foreach($quiz->questions as $qi => $question)
                <div class="question-card" data-q-index="{{ $qi }}">
                    <div class="question-header">
                        <span class="question-number">Question {{ $qi + 1 }}</span>
                        <button type="button" onclick="quizRemoveQuestion(this)" class="qf-btn qf-btn-danger">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                    <input type="hidden" name="questions[{{ $qi }}][id]" value="{{ $question->id }}">
                    <div class="form-group">
                        <label class="form-label required">Question Text</label>
                        <input type="text" name="questions[{{ $qi }}][question]"
                               class="form-input" value="{{ $question->question }}" required
                               placeholder="Enter your question here...">
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="margin-bottom:.5rem;">
                            Answer Options <span style="font-weight:400;color:#718096;font-size:.75rem;">(select the correct one)</span>
                        </label>
                        <div class="options-list">
                            @foreach($question->options as $oi => $option)
                            <div class="option-item">
                                <input type="radio" name="questions[{{ $qi }}][correct_answer]"
                                       value="{{ $oi }}" class="option-radio"
                                       {{ $option->is_correct ? 'checked' : '' }}>
                                <input type="text" name="questions[{{ $qi }}][options][{{ $oi }}][option_text]"
                                       class="option-input" value="{{ $option->option_text }}" required
                                       placeholder="Option {{ $oi + 1 }}">
                                <button type="button" onclick="quizRemoveOption(this)" class="qf-btn qf-btn-danger">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" onclick="quizAddOption(this, {{ $qi }})" class="qf-btn qf-btn-add-opt">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Explanation <span style="font-weight:400;color:#718096;">(optional)</span></label>
                        <input type="text" name="questions[{{ $qi }}][explanation]"
                               class="form-input" value="{{ $question->explanation ?? '' }}"
                               placeholder="Explain why the correct answer is right...">
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        <button type="button" onclick="quizAddQuestion()" class="qf-btn qf-btn-add">
            <i class="fas fa-plus-circle"></i> Add Question
        </button>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:.75rem;padding-top:1rem;margin-top:.5rem;border-top:1px solid #f0ebe8;">
        <button type="button" onclick="closeCrudModal()" class="qf-btn qf-btn-secondary">
            Cancel
        </button>
        <button type="submit" class="qf-btn qf-btn-primary">
            <i class="fas fa-save"></i> {{ ($editing ?? false) ? 'Update Quiz' : 'Create Quiz' }}
        </button>
    </div>
</form>

<script>
(function() {
    var _qIdx  = {{ !empty($quiz) && $quiz->questions ? $quiz->questions->count() : 0 }};
    var _oIdx  = {};

    // Track option counts per question
    @if(!empty($quiz) && $quiz->questions && $quiz->questions->count())
    @foreach($quiz->questions as $qi => $question)
    _oIdx[{{ $qi }}] = {{ $question->options->count() }};
    @endforeach
    @endif

    // Publish toggle label
    var publishInput = document.querySelector('[name="is_published"]');
    var publishStatus = document.getElementById('quizPublishStatus');
    if (publishInput && publishStatus) {
        publishInput.addEventListener('change', function() {
            publishStatus.textContent = this.checked ? 'Published' : 'Draft';
        });
    }

    window.quizAddQuestion = function() {
        var i = _qIdx++;
        _oIdx[i] = 0;
        var card = document.createElement('div');
        card.className = 'question-card';
        card.dataset.qIndex = i;
        card.innerHTML =
            '<div class="question-header">'
            + '<span class="question-number">Question ' + (i + 1) + '</span>'
            + '<button type="button" onclick="quizRemoveQuestion(this)" class="qf-btn qf-btn-danger"><i class="fas fa-trash"></i> Remove</button>'
            + '</div>'
            + '<div class="form-group">'
            + '<label class="form-label required">Question Text</label>'
            + '<input type="text" name="questions[' + i + '][question]" class="form-input" required placeholder="Enter your question here...">'
            + '</div>'
            + '<div class="form-group">'
            + '<label class="form-label" style="margin-bottom:.5rem;">Answer Options <span style="font-weight:400;color:#718096;font-size:.75rem;">(select the correct one)</span></label>'
            + '<div class="options-list"></div>'
            + '<button type="button" onclick="quizAddOption(this,' + i + ')" class="qf-btn qf-btn-add-opt"><i class="fas fa-plus"></i> Add Option</button>'
            + '</div>'
            + '<div class="form-group" style="margin-bottom:0;">'
            + '<label class="form-label">Explanation <span style="font-weight:400;color:#718096;">(optional)</span></label>'
            + '<input type="text" name="questions[' + i + '][explanation]" class="form-input" placeholder="Explain why the correct answer is right...">'
            + '</div>';
        document.getElementById('quizQuestionsContainer').appendChild(card);
        // Auto-add 2 default options
        quizAddOption(card.querySelector('.qf-btn-add-opt'), i);
        quizAddOption(card.querySelector('.qf-btn-add-opt'), i);
    };

    window.quizAddOption = function(btn, qIdx) {
        if (_oIdx[qIdx] === undefined) _oIdx[qIdx] = 0;
        var oIdx = _oIdx[qIdx]++;
        var list = btn.previousElementSibling;
        var row = document.createElement('div');
        row.className = 'option-item';
        row.innerHTML =
            '<input type="radio" name="questions[' + qIdx + '][correct_answer]" value="' + oIdx + '" class="option-radio"' + (oIdx === 0 ? ' checked' : '') + '>'
            + '<input type="text" name="questions[' + qIdx + '][options][' + oIdx + '][option_text]" class="option-input" required placeholder="Option ' + (oIdx + 1) + '">'
            + '<button type="button" onclick="quizRemoveOption(this)" class="qf-btn qf-btn-danger"><i class="fas fa-times"></i></button>';
        list.appendChild(row);
    };

    window.quizRemoveOption = function(btn) {
        btn.closest('.option-item').remove();
    };

    window.quizRemoveQuestion = function(btn) {
        btn.closest('.question-card').remove();
    };
})();
</script>
