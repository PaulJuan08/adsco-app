@push('styles')
<link rel="stylesheet" href="{{ asset('css/progress.css') }}">
@endpush

<div class="table-responsive">
    <table class="progress-table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Quiz</th>
                <th>Score</th>
                <th>Result</th>
                <th>Completed</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attempts as $attempt)
                @php
                    $percentage = $attempt->percentage ?? 0;
                    $scoreClass = $percentage >= 80 ? 'score-high' : ($percentage >= 60 ? 'score-medium' : 'score-low');
                    $passed = $attempt->passed ?? false;
                    $passingScore = $attempt->quiz->passing_score ?? 70;
                @endphp
                <tr>
                    <td>
                        <div class="student-cell">
                            <div class="student-avatar-sm">
                                {{ strtoupper(substr($attempt->user->f_name, 0, 1) . substr($attempt->user->l_name, 0, 1)) }}
                            </div>
                            <div class="student-info">
                                <div class="student-name">{{ $attempt->user->full_name }}</div>
                                <div class="student-meta">
                                    <span><i class="fas fa-id-card"></i> {{ $attempt->user->student_id ?? 'N/A' }}</span>
                                    @if($attempt->user->college)
                                        <span><i class="fas fa-university"></i> {{ $attempt->user->college->college_name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-600">{{ $attempt->quiz->title ?? 'Unknown Quiz' }}</div>
                        <div class="text-muted small">
                            <i class="fas fa-question-circle"></i> {{ $attempt->total_questions ?? 0 }} Questions
                            @if($attempt->quiz)
                                <br><i class="fas fa-trophy"></i> Pass: {{ $attempt->quiz->passing_score ?? 70 }}%
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="score-badge {{ $scoreClass }}">
                            {{ $percentage }}%
                        </span>
                        <div class="text-muted extra-small mt-1">
                            {{ $attempt->score }}/{{ $attempt->total_points }} points
                        </div>
                    </td>
                    <td>
                        @if($passed)
                            <span class="status-badge status-passed">
                                <i class="fas fa-check-circle"></i>
                                <span>PASSED</span>
                                @if($percentage >= $passingScore + 20)
                                    <span class="excellent-badge">EXCELLENT</span>
                                @elseif($percentage >= $passingScore + 10)
                                    <span class="good-badge">GOOD</span>
                                @endif
                            </span>
                        @else
                            <span class="status-badge status-failed">
                                <i class="fas fa-times-circle"></i>
                                <span>FAILED</span>
                                @if($percentage < $passingScore - 20)
                                    <span class="needs-improvement-badge">NEEDS IMPROVEMENT</span>
                                @endif
                            </span>
                        @endif
                        <div class="text-muted extra-small text-center mt-1">
                            Passing: {{ $passingScore }}%
                        </div>
                    </td>
                    <td>
                        <div>{{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y') : 'N/A' }}</div>
                        <div class="text-muted extra-small">
                            {{ $attempt->completed_at ? $attempt->completed_at->diffForHumans() : '' }}
                        </div>
                        @if($attempt->time_taken)
                            <div class="text-muted extra-small mt-1">
                                <i class="fas fa-clock"></i> {{ gmdate('i:s', $attempt->time_taken) }} mins
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="action-group">
                            <a href="{{ route('admin.users.show', Crypt::encrypt($attempt->user->id)) }}" 
                               class="view-btn" 
                               title="View Student">
                                <i class="fas fa-user"></i>
                            </a>
                            <a href="{{ route('admin.quizzes.show', Crypt::encrypt($attempt->quiz->id)) }}" 
                               class="view-btn" 
                               title="View Quiz">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($attempt->answers)
                                <button type="button" 
                                        class="view-btn" 
                                        title="View Answers"
                                        onclick="showAnswers({{ $attempt->id }})">
                                    <i class="fas fa-list-check"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fas fa-chart-line"></i>
                            <h3>No quiz attempts found</h3>
                            <p>No students have attempted any quizzes matching your filters.</p>
                            <a href="{{ route('admin.todo.progress', ['type' => 'quiz']) }}" class="btn-sm btn-sm-primary">
                                <i class="fas fa-times"></i> Clear Filters
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($attempts instanceof \Illuminate\Pagination\AbstractPaginator && $attempts->hasPages())
    <div class="pagination-info">
        <span>
            Showing {{ $attempts->firstItem() }} to {{ $attempts->lastItem() }} of {{ $attempts->total() }} attempts
        </span>
        <div class="pagination-links">
            {{ $attempts->links() }}
        </div>
    </div>
@endif

{{-- Answers Modal --}}
<div id="answersModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>
                <i class="fas fa-list-check"></i>
                Detailed Answers
            </h3>
            <button onclick="closeModal()" class="modal-close">&times;</button>
        </div>
        <div id="answersContent" class="modal-body">
            Loading...
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showAnswers(attemptId) {
        const modal = document.getElementById('answersModal');
        const content = document.getElementById('answersContent');
        
        content.innerHTML = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin spinner"></i><p class="mt-3">Loading answers...</p></div>';
        
        modal.classList.add('show');
        
        // Fetch answers via AJAX
        fetch(`/admin/todo/quiz-attempt/${attemptId}/answers`)
            .then(response => response.json())
            .then(data => {
                let html = '';
                data.answers.forEach((answer, index) => {
                    const resultClass = answer.is_correct ? 'correct' : 'incorrect';
                    html += `
                        <div class="answer-item">
                            <div class="answer-question">Question ${index + 1}:</div>
                            <div class="answer-box">${answer.question}</div>
                            <div class="answer-result ${resultClass}">
                                <i class="fas ${answer.is_correct ? 'fa-check-circle' : 'fa-times-circle'}"></i>
                                <span>Your answer was ${answer.is_correct ? 'correct' : 'incorrect'}</span>
                            </div>
                        </div>
                    `;
                });
                content.innerHTML = html;
            })
            .catch(error => {
                content.innerHTML = '<div class="text-center p-4 text-danger"><i class="fas fa-exclamation-circle fa-3x"></i><p class="mt-3">Error loading answers</p></div>';
            });
    }

    function closeModal() {
        document.getElementById('answersModal').classList.remove('show');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('answersModal');
        if (event.target === modal) {
            modal.classList.remove('show');
        }
    }
</script>
@endpush