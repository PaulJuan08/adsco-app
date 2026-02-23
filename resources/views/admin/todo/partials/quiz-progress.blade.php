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
                        <div style="font-weight: 600;">{{ $attempt->quiz->title ?? 'Unknown Quiz' }}</div>
                        <div style="font-size: 0.75rem; color: #718096;">
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
                        <div style="font-size: 0.7rem; color: #718096; margin-top: 0.25rem;">
                            {{ $attempt->score }}/{{ $attempt->total_points }} points
                        </div>
                    </td>
                    <td>
                        @if($passed)
                            <span class="status-badge status-passed" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: #f0fff4; color: #22543d; border: 1px solid #9ae6b4; border-radius: 20px; font-weight: 600;">
                                <i class="fas fa-check-circle" style="color: #48bb78;"></i>
                                <span>PASSED</span>
                                @if($percentage >= $passingScore + 20)
                                    <span style="background: #48bb78; color: white; padding: 0.125rem 0.5rem; border-radius: 12px; font-size: 0.65rem; margin-left: 0.25rem;">EXCELLENT</span>
                                @elseif($percentage >= $passingScore + 10)
                                    <span style="background: #48bb78; color: white; padding: 0.125rem 0.5rem; border-radius: 12px; font-size: 0.65rem; margin-left: 0.25rem;">GOOD</span>
                                @endif
                            </span>
                        @else
                            <span class="status-badge status-failed" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; border-radius: 20px; font-weight: 600;">
                                <i class="fas fa-times-circle" style="color: #f56565;"></i>
                                <span>FAILED</span>
                                @if($percentage < $passingScore - 20)
                                    <span style="background: #f56565; color: white; padding: 0.125rem 0.5rem; border-radius: 12px; font-size: 0.65rem; margin-left: 0.25rem;">NEEDS IMPROVEMENT</span>
                                @endif
                            </span>
                        @endif
                        <div style="font-size: 0.7rem; color: #718096; margin-top: 0.25rem; text-align: center;">
                            Passing: {{ $passingScore }}%
                        </div>
                    </td>
                    <td>
                        <div>{{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y') : 'N/A' }}</div>
                        <div style="font-size: 0.7rem; color: #718096;">
                            {{ $attempt->completed_at ? $attempt->completed_at->diffForHumans() : '' }}
                        </div>
                        @if($attempt->time_taken)
                            <div style="font-size: 0.7rem; color: #718096; margin-top: 0.25rem;">
                                <i class="fas fa-clock"></i> {{ gmdate('i:s', $attempt->time_taken) }} mins
                            </div>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
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
<div id="answersModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; max-width: 600px; width: 90%; max-height: 80vh; overflow: hidden;">
        <div style="padding: 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-list-check"></i>
                Detailed Answers
            </h3>
            <button onclick="closeModal()" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <div id="answersContent" style="padding: 1.5rem; overflow-y: auto; max-height: calc(80vh - 80px);">
            Loading...
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showAnswers(attemptId) {
        // You'll need to create a route to fetch attempt answers
        // For now, we'll show a placeholder
        const modal = document.getElementById('answersModal');
        const content = document.getElementById('answersContent');
        
        content.innerHTML = '<div style="text-align: center; padding: 2rem;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #667eea;"></i><p style="margin-top: 1rem;">Loading answers...</p></div>';
        
        modal.style.display = 'flex';
        
        // Fetch answers via AJAX
        fetch(`/admin/todo/quiz-attempt/${attemptId}/answers`)
            .then(response => response.json())
            .then(data => {
                let html = '';
                data.answers.forEach((answer, index) => {
                    html += `
                        <div style="margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #e2e8f0;">
                            <div style="font-weight: 600; margin-bottom: 0.5rem;">Question ${index + 1}:</div>
                            <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; margin-bottom: 0.5rem;">${answer.question}</div>
                            <div style="display: flex; align-items: center; gap: 0.5rem; ${answer.is_correct ? 'color: #48bb78;' : 'color: #f56565;'}">
                                <i class="fas ${answer.is_correct ? 'fa-check-circle' : 'fa-times-circle'}"></i>
                                <span>Your answer was ${answer.is_correct ? 'correct' : 'incorrect'}</span>
                            </div>
                        </div>
                    `;
                });
                content.innerHTML = html;
            })
            .catch(error => {
                content.innerHTML = '<div style="text-align: center; padding: 2rem; color: #f56565;"><i class="fas fa-exclamation-circle" style="font-size: 2rem;"></i><p style="margin-top: 1rem;">Error loading answers</p></div>';
            });
    }

    function closeModal() {
        document.getElementById('answersModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('answersModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
</script>
@endpush