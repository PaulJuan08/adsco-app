@push('styles')
<link rel="stylesheet" href="{{ asset('css/progress.css') }}">
@endpush

<div class="table-responsive">
    <table class="progress-table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Quiz</th>
                <th>Performance</th>
                <th>Completed</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attempts as $attempt)
                @php
                    $percentage   = $attempt->percentage ?? 0;
                    $scoreClass   = $percentage >= 80 ? 'score-high' : ($percentage >= 60 ? 'score-medium' : 'score-low');
                    $passed       = $attempt->passed ?? false;
                    $passingScore = $attempt->quiz->passing_score ?? 70;
                    $hasAnswers   = false;
                    if (isset($attempt->answers)) {
                        if (is_array($attempt->answers) && count($attempt->answers) > 0) $hasAnswers = true;
                        elseif (is_string($attempt->answers) && strlen($attempt->answers) > 0) $hasAnswers = true;
                    }
                @endphp
                <tr>
                    {{-- Student --}}
                    <td>
                        <div class="student-cell">
                            @if($attempt->user)
                                <div class="student-avatar-sm" @if($attempt->user->profile_photo_url) style="padding:0;" @endif>
                                    @if($attempt->user->profile_photo_url)
                                        <img src="{{ $attempt->user->profile_photo_url }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                                    @else
                                        {{ strtoupper(substr($attempt->user->f_name ?? '', 0, 1) . substr($attempt->user->l_name ?? '', 0, 1)) }}
                                    @endif
                                </div>
                                <div class="student-info">
                                    <div class="student-name">{{ $attempt->user->full_name ?? 'Unknown' }}</div>
                                    <div class="student-meta">
                                        <span><i class="fas fa-id-card"></i> {{ $attempt->user->student_id ?? 'N/A' }}</span>
                                        @if($attempt->user->college)
                                            <span><i class="fas fa-university"></i> {{ $attempt->user->college->college_name }}</span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="student-avatar-sm" style="background:#dc2626;">
                                    <i class="fas fa-user-slash"></i>
                                </div>
                                <div class="student-info">
                                    <div class="student-name" style="color:#dc2626;">Deleted Student</div>
                                </div>
                            @endif
                        </div>
                    </td>

                    {{-- Quiz --}}
                    <td>
                        @if($attempt->quiz)
                            <div class="fw-600">{{ Str::limit($attempt->quiz->title, 40) }}</div>
                            <div class="text-muted extra-small">
                                <i class="fas fa-question-circle"></i> {{ $attempt->total_questions ?? 0 }} Qs
                                &nbsp;·&nbsp;
                                <i class="fas fa-trophy"></i> Pass: {{ $passingScore }}%
                            </div>
                        @else
                            <div class="fw-600 text-danger"><i class="fas fa-exclamation-triangle"></i> Deleted Quiz</div>
                        @endif
                    </td>

                    {{-- Performance (score + pass/fail merged) --}}
                    <td>
                        <div class="perf-cell">
                            <div class="perf-row">
                                <span class="score-badge {{ $scoreClass }}">{{ $percentage }}%</span>
                                @if($passed)
                                    <span class="status-badge status-passed" style="font-size:0.7rem;padding:0.2rem 0.5rem;">
                                        <i class="fas fa-check-circle"></i> Passed
                                    </span>
                                    @if($percentage >= $passingScore + 20)
                                        <span class="excellent-badge">Excellent</span>
                                    @elseif($percentage >= $passingScore + 10)
                                        <span class="good-badge">Good</span>
                                    @endif
                                @else
                                    <span class="status-badge status-failed" style="font-size:0.7rem;padding:0.2rem 0.5rem;">
                                        <i class="fas fa-times-circle"></i> Failed
                                    </span>
                                    @if($percentage < $passingScore - 20)
                                        <span class="needs-improvement-badge">Needs Work</span>
                                    @endif
                                @endif
                            </div>
                            <div class="perf-sub">{{ $attempt->score ?? 0 }}/{{ $attempt->total_points ?? 0 }} pts</div>
                        </div>
                    </td>

                    {{-- Completed --}}
                    <td>
                        <div>{{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y') : 'N/A' }}</div>
                        <div class="text-muted extra-small">
                            {{ $attempt->completed_at ? $attempt->completed_at->diffForHumans() : '' }}
                            @if($attempt->time_taken)
                                &nbsp;·&nbsp;<i class="fas fa-stopwatch"></i> {{ gmdate('i:s', $attempt->time_taken) }}
                            @endif
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td>
                        <div class="action-group">
                            @if($attempt->user && $attempt->user->id)
                                <a href="{{ route('admin.users.show', Crypt::encrypt($attempt->user->id)) }}"
                                   class="view-btn" title="View Student">
                                    <i class="fas fa-user"></i>
                                </a>
                            @endif
                            @if($attempt->quiz && $attempt->quiz->id)
                                <a href="{{ route('admin.quizzes.show', Crypt::encrypt($attempt->quiz->id)) }}"
                                   class="view-btn" title="View Quiz">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endif
                            @if($hasAnswers)
                                <button type="button" class="view-btn" title="View Answers"
                                        onclick="showAnswers({{ $attempt->id }})">
                                    <i class="fas fa-list-check"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="fas fa-chart-line"></i>
                            <h3>No quiz attempts found</h3>
                            <p>No students have attempted quizzes matching your filters.</p>
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

@if(isset($attempts) && $attempts instanceof \Illuminate\Pagination\AbstractPaginator && $attempts->hasPages())
<div class="pagination-info">
    <span>Showing {{ $attempts->firstItem() }}–{{ $attempts->lastItem() }} of {{ $attempts->total() }} attempts</span>
    <div class="pagination-links">{{ $attempts->appends(request()->query())->links() }}</div>
</div>
@endif

{{-- Answers Modal --}}
<div id="answersModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-list-check"></i> Student Answers</h3>
            <button onclick="closeModal()" class="modal-close">&times;</button>
        </div>
        <div id="answersContent" class="modal-body">Loading...</div>
    </div>
</div>

@push('scripts')
<script>
function showAnswers(attemptId) {
    const modal   = document.getElementById('answersModal');
    const content = document.getElementById('answersContent');
    content.innerHTML = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin spinner"></i><p class="mt-3">Loading…</p></div>';
    modal.classList.add('show');
    fetch(`/admin/todo/quiz-attempt/${attemptId}/answers`)
        .then(r => r.json())
        .then(data => {
            if (!data.answers || !data.answers.length) {
                content.innerHTML = '<div class="text-center p-4"><p>No answers available.</p></div>';
                return;
            }
            content.innerHTML = data.answers.map((a, i) => `
                <div class="answer-item">
                    <div class="answer-question">Question ${i + 1}</div>
                    <div class="answer-box">${a.question || '—'}</div>
                    <div class="answer-result ${a.is_correct ? 'correct' : 'incorrect'}">
                        <i class="fas ${a.is_correct ? 'fa-check-circle' : 'fa-times-circle'}"></i>
                        ${a.is_correct ? 'Correct' : 'Incorrect'}
                    </div>
                </div>`).join('');
        })
        .catch(() => { content.innerHTML = '<div class="text-center p-4 text-danger"><p>Error loading answers.</p></div>'; });
}
function closeModal() { document.getElementById('answersModal').classList.remove('show'); }
window.addEventListener('click', e => { if (e.target === document.getElementById('answersModal')) closeModal(); });
</script>
@endpush
