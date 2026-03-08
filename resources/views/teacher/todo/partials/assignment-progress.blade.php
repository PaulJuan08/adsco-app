<div class="table-responsive">
    <table class="progress-table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Assignment</th>
                <th>Score & Status</th>
                <th>Submitted</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($submissions as $submission)
                @php
                    $status      = $submission->status ?? 'pending';
                    $statusClass = match($status) { 'graded' => 'status-graded', 'late' => 'status-late', 'submitted' => 'status-submitted', default => 'status-pending' };
                    $statusIcon  = match($status) { 'graded' => 'fa-check-circle', 'late' => 'fa-exclamation-circle', 'submitted' => 'fa-paper-plane', default => 'fa-clock' };
                    $hasScore    = $submission->score !== null && $submission->assignment && $submission->assignment->points > 0;
                    $percentage  = $hasScore ? round(($submission->score / $submission->assignment->points) * 100) : 0;
                    $scoreClass  = $percentage >= 80 ? 'score-high' : ($percentage >= 60 ? 'score-medium' : 'score-low');
                @endphp
                <tr>
                    {{-- Student --}}
                    <td>
                        <div class="student-cell">
                            <div class="student-avatar-sm" @if($submission->student->profile_photo_url) style="padding:0;" @endif>
                                @if($submission->student->profile_photo_url)
                                    <img src="{{ $submission->student->profile_photo_url }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                                @else
                                    {{ strtoupper(substr($submission->student->f_name, 0, 1) . substr($submission->student->l_name, 0, 1)) }}
                                @endif
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

                    {{-- Assignment --}}
                    <td>
                        <div class="fw-600">{{ Str::limit($submission->assignment->title ?? 'Unknown', 40) }}</div>
                        <div class="text-muted extra-small">
                            <i class="fas fa-star"></i> {{ $submission->assignment->points ?? 0 }} pts
                            @if($submission->assignment->due_date ?? false)
                                &nbsp;·&nbsp;<i class="fas fa-calendar-alt"></i> Due {{ $submission->assignment->due_date->format('M d, Y') }}
                            @endif
                        </div>
                    </td>

                    {{-- Score & Status merged --}}
                    <td>
                        <div class="perf-cell">
                            <div class="perf-row">
                                @if($hasScore)
                                    <span class="score-badge {{ $scoreClass }}">{{ $percentage }}%</span>
                                @endif
                                <span class="status-badge {{ $statusClass }}" style="font-size:0.7rem;padding:0.2rem 0.5rem;">
                                    <i class="fas {{ $statusIcon }}"></i> {{ ucfirst($status) }}
                                </span>
                            </div>
                            @if($hasScore)
                                <div class="perf-sub">{{ $submission->score }}/{{ $submission->assignment->points }} pts</div>
                            @endif
                        </div>
                    </td>

                    {{-- Submitted --}}
                    <td>
                        <div>{{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y') : 'N/A' }}</div>
                        <div class="text-muted extra-small">
                            {{ $submission->submitted_at ? $submission->submitted_at->diffForHumans() : '' }}
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td>
                        <div class="action-group">
                            <a href="{{ route('teacher.users.show', Crypt::encrypt($submission->student->id)) }}"
                               class="view-btn" title="View Student">
                                <i class="fas fa-user"></i>
                            </a>
                            <a href="{{ route('teacher.todo.submission.grade', $submission->id) }}"
                               class="view-btn" title="View Submission">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($status !== 'graded')
                                <a href="{{ route('teacher.todo.submission.grade', $submission->id) }}#grade"
                                   class="view-btn grade-btn" title="Grade">
                                    <i class="fas fa-star"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="fas fa-file-alt"></i>
                            <h3>No submissions found</h3>
                            <p>No students have submitted assignments matching your filters.</p>
                            <a href="{{ route('teacher.todo.progress', ['type' => 'assignment']) }}" class="btn-sm btn-sm-primary">
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
    <span>Showing {{ $submissions->firstItem() }}–{{ $submissions->lastItem() }} of {{ $submissions->total() }} submissions</span>
    <div class="pagination-links">{{ $submissions->appends(request()->query())->links() }}</div>
</div>
@endif
