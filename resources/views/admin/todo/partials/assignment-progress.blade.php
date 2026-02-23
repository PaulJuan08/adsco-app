<div class="table-responsive">
    <table class="progress-table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Assignment</th>
                <th>Score</th>
                <th>Status</th>
                <th>Submitted</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($submissions as $submission)
                <tr>
                    <td>
                        <div class="student-cell">
                            <div class="student-avatar-sm">
                                {{ strtoupper(substr($submission->student->f_name, 0, 1) . substr($submission->student->l_name, 0, 1)) }}
                            </div>
                            <div class="student-info">
                                <div class="student-name">{{ $submission->student->full_name }}</div>
                                <div class="student-meta">
                                    <span><i class="fas fa-id-card"></i> {{ $submission->student->student_id ?? 'N/A' }}</span>
                                    @if($submission->student->college)
                                        <span><i class="fas fa-university"></i> {{ $submission->student->college->college_name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $submission->assignment->title ?? 'Unknown Assignment' }}</div>
                        <div style="font-size: 0.75rem; color: #718096;">
                            <i class="fas fa-star"></i> {{ $submission->assignment->points ?? 0 }} points
                            @if($submission->assignment->due_date)
                                · <i class="fas fa-calendar-alt"></i> Due {{ $submission->assignment->due_date->format('M d, Y') }}
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($submission->score !== null)
                            @php
                                $percentage = $submission->assignment && $submission->assignment->points > 0 
                                    ? round(($submission->score / $submission->assignment->points) * 100) 
                                    : 0;
                                $scoreClass = $percentage >= 80 ? 'score-high' : ($percentage >= 60 ? 'score-medium' : 'score-low');
                            @endphp
                            <span class="score-badge {{ $scoreClass }}">
                                {{ $percentage }}%
                            </span>
                            <div style="font-size: 0.7rem; color: #718096; margin-top: 0.25rem;">
                                {{ $submission->score }}/{{ $submission->assignment->points }} points
                            </div>
                        @else
                            <span class="status-badge status-pending">
                                <i class="fas fa-clock"></i> Not graded
                            </span>
                        @endif
                    </td>
                    <td>
                        @php
                            $statusClass = match($submission->status) {
                                'graded' => 'status-graded',
                                'late' => 'status-late',
                                'submitted' => 'status-submitted',
                                default => 'status-pending'
                            };
                            $statusIcon = match($submission->status) {
                                'graded' => 'fa-check-circle',
                                'late' => 'fa-exclamation-circle',
                                'submitted' => 'fa-paper-plane',
                                default => 'fa-clock'
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            <i class="fas {{ $statusIcon }}"></i>
                            {{ ucfirst($submission->status) }}
                        </span>
                    </td>
                    <td>
                        <div>{{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y') : 'N/A' }}</div>
                        <div style="font-size: 0.7rem; color: #718096;">
                            {{ $submission->submitted_at ? $submission->submitted_at->diffForHumans() : '' }}
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('admin.users.show', Crypt::encrypt($submission->student->id)) }}" 
                           class="view-btn" 
                           title="View Student">
                            <i class="fas fa-user"></i>
                        </a>
                        <a href="{{ route('admin.todo.submission.view', $submission->id) }}" 
                           class="view-btn" 
                           title="View Submission">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if($submission->status !== 'graded')
                            <a href="{{ route('admin.todo.submission.view', $submission->id) }}#grade" 
                               class="view-btn" 
                               style="background: #48bb78; color: white; border-color: #48bb78;"
                               title="Grade">
                                <i class="fas fa-star"></i>
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fas fa-file-alt"></i>
                            <h3>No assignment submissions found</h3>
                            <p>No students have submitted any assignments matching your filters.</p>
                            <a href="{{ route('admin.todo.progress', ['type' => 'assignment']) }}" class="btn-sm btn-sm-primary">
                                <i class="fas fa-times"></i> Clear Filters
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($submissions instanceof \Illuminate\Pagination\AbstractPaginator && $submissions->hasPages())
    <div class="pagination-info">
        <span>
            Showing {{ $submissions->firstItem() }} to {{ $submissions->lastItem() }} of {{ $submissions->total() }} submissions
        </span>
        <div class="pagination-links">
            {{ $submissions->links() }}
        </div>
    </div>
@endif