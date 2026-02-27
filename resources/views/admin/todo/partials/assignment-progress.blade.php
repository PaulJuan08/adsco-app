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
                            @if($submission->student)
                                <div class="student-avatar-sm">
                                    {{ strtoupper(substr($submission->student->f_name ?? '', 0, 1) . substr($submission->student->l_name ?? '', 0, 1)) }}
                                </div>
                                <div class="student-info">
                                    <div class="student-name">{{ $submission->student->full_name ?? 'Unknown Student' }}</div>
                                    <div class="student-meta">
                                        <span><i class="fas fa-id-card"></i> {{ $submission->student->student_id ?? 'N/A' }}</span>
                                        @if($submission->student->college)
                                            <span><i class="fas fa-university"></i> {{ $submission->student->college->college_name }}</span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="student-avatar-sm" style="background: #dc2626;">
                                    <i class="fas fa-user-slash"></i>
                                </div>
                                <div class="student-info">
                                    <div class="student-name" style="color: #dc2626;">Deleted Student</div>
                                    <div class="student-meta">
                                        <span><i class="fas fa-id-card"></i> N/A</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($submission->assignment)
                            <div class="fw-600">{{ $submission->assignment->title }}</div>
                            <div class="text-muted small">
                                <i class="fas fa-star"></i> {{ $submission->assignment->points ?? 0 }} points
                                @if($submission->assignment->due_date)
                                    · <i class="fas fa-calendar-alt"></i> Due {{ $submission->assignment->due_date->format('M d, Y') }}
                                @endif
                            </div>
                        @else
                            <div class="fw-600 text-danger">
                                <i class="fas fa-exclamation-triangle"></i> Deleted Assignment
                            </div>
                            <div class="text-muted small">
                                <span class="badge badge-danger">Assignment no longer exists</span>
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($submission->score !== null)
                            @if($submission->assignment)
                                @php
                                    $percentage = $submission->assignment->points > 0 
                                        ? round(($submission->score / $submission->assignment->points) * 100) 
                                        : 0;
                                    $scoreClass = $percentage >= 80 ? 'score-high' : ($percentage >= 60 ? 'score-medium' : 'score-low');
                                @endphp
                                <span class="score-badge {{ $scoreClass }}">
                                    {{ $percentage }}%
                                </span>
                                <div class="text-muted extra-small mt-1">
                                    {{ $submission->score }}/{{ $submission->assignment->points }} points
                                </div>
                            @else
                                <span class="score-badge">
                                    {{ $submission->score }} points
                                </span>
                                <div class="text-muted extra-small mt-1">
                                    (Assignment deleted)
                                </div>
                            @endif
                        @else
                            <span class="status-badge status-pending">
                                <i class="fas fa-clock"></i> Not graded
                            </span>
                        @endif
                    </td>
                    <td>
                        @php
                            $status = $submission->status ?? 'pending';
                            $statusClass = match($status) {
                                'graded' => 'status-graded',
                                'late' => 'status-late',
                                'submitted' => 'status-submitted',
                                default => 'status-pending'
                            };
                            $statusIcon = match($status) {
                                'graded' => 'fa-check-circle',
                                'late' => 'fa-exclamation-circle',
                                'submitted' => 'fa-paper-plane',
                                default => 'fa-clock'
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            <i class="fas {{ $statusIcon }}"></i>
                            {{ ucfirst($status) }}
                        </span>
                    </td>
                    <td>
                        <div>{{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y') : 'N/A' }}</div>
                        <div class="text-muted extra-small">
                            {{ $submission->submitted_at ? $submission->submitted_at->diffForHumans() : '' }}
                        </div>
                    </td>
                    <td>
                        <div class="action-group">
                            @if($submission->student && $submission->student->id)
                                <a href="{{ route('admin.users.show', Crypt::encrypt($submission->student->id)) }}" 
                                   class="view-btn" 
                                   title="View Student">
                                    <i class="fas fa-user"></i>
                                </a>
                            @endif
                            
                            @if($submission->assignment && $submission->assignment->id)
                                <a href="{{ route('admin.todo.assignment.show', Crypt::encrypt($submission->assignment->id)) }}" 
                                   class="view-btn" 
                                   title="View Assignment">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endif
                            
                            @if($submission->status !== 'graded' && $submission->assignment && $submission->assignment->id)
                                <a href="{{ route('admin.todo.submission.grade', $submission->id) }}#grade" 
                                   class="view-btn grade-btn" 
                                   title="Grade">
                                    <i class="fas fa-star"></i>
                                </a>
                            @endif
                        </div>
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

@if(isset($submissions) && $submissions instanceof \Illuminate\Pagination\AbstractPaginator && $submissions->hasPages())
    <div class="pagination-info">
        <span>
            Showing {{ $submissions->firstItem() }} to {{ $submissions->lastItem() }} of {{ $submissions->total() }} submissions
        </span>
        <div class="pagination-links">
            {{ $submissions->links() }}
        </div>
    </div>
@endif