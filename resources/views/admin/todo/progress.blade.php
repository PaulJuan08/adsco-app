@extends('layouts.admin')

@section('title', 'Student Progress')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/progress.css') }}">
@endpush

@section('content')
<div class="dashboard-container">

    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                @include('partials.user_avatar')
                <div class="greeting-text">
                    <h1 class="welcome-title">Student Progress</h1>
                    <p class="welcome-subtitle"><i class="fas fa-chart-line"></i> Track student performance across quizzes and assignments</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    {{-- Tabs --}}
    <div class="progress-tabs">
        <a href="{{ route('admin.todo.progress', ['type' => 'quiz'] + request()->except('type', 'page')) }}"
           class="progress-tab {{ $type === 'quiz' ? 'active' : '' }}">
            <i class="fas fa-brain"></i> Quiz Attempts
        </a>
        <a href="{{ route('admin.todo.progress', ['type' => 'assignment'] + request()->except('type', 'page')) }}"
           class="progress-tab {{ $type === 'assignment' ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i> Assignment Submissions
        </a>
    </div>

    {{-- Compact Filter Bar --}}
    <div class="filter-bar">
        <form method="GET" id="filterForm" action="{{ route('admin.todo.progress') }}">
            <input type="hidden" name="type" value="{{ $type }}">
            <div class="filter-row">

                <select name="college_id" class="filter-select auto-filter" id="college-filter">
                    <option value="">All Colleges</option>
                    @foreach($colleges as $college)
                        <option value="{{ $college->id }}" {{ $collegeId == $college->id ? 'selected' : '' }}>
                            {{ $college->college_name }}
                        </option>
                    @endforeach
                </select>

                <select name="program_id" class="filter-select auto-filter" id="program-filter" {{ !$collegeId ? 'disabled' : '' }}>
                    <option value="">All Programs</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ $programId == $program->id ? 'selected' : '' }}>
                            {{ $program->program_name }}
                        </option>
                    @endforeach
                </select>

                <select name="year" class="filter-select auto-filter">
                    <option value="">All Years</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Year {{ $y }}</option>
                    @endforeach
                </select>

                @if($type === 'quiz')
                <select name="item_id" class="filter-select auto-filter">
                    <option value="">All Quizzes</option>
                    @foreach($quizList as $quiz)
                        <option value="{{ $quiz->id }}" {{ $itemId == $quiz->id ? 'selected' : '' }}>
                            {{ Str::limit($quiz->title, 35) }}
                        </option>
                    @endforeach
                </select>
                @else
                <select name="item_id" class="filter-select auto-filter">
                    <option value="">All Assignments</option>
                    @foreach($assignList as $assignment)
                        <option value="{{ $assignment->id }}" {{ $itemId == $assignment->id ? 'selected' : '' }}>
                            {{ Str::limit($assignment->title, 35) }}
                        </option>
                    @endforeach
                </select>
                @endif

                <div class="filter-search-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search_name" class="filter-input"
                           placeholder="Search student..." value="{{ $searchName }}">
                </div>

                @if($searchName || $collegeId || $programId || $year || $itemId)
                <a href="{{ route('admin.todo.progress', ['type' => $type]) }}" class="filter-clear-btn">
                    <i class="fas fa-times"></i> Clear
                </a>
                @endif

            </div>
        </form>
    </div>

    {{-- Active item filter pill --}}
    @if($itemId)
        @php $filteredItem = $type === 'quiz' ? $quizList->firstWhere('id', $itemId) : $assignList->firstWhere('id', $itemId); @endphp
        @if($filteredItem)
        <div class="item-filter" style="margin-bottom:0.75rem;">
            <i class="fas fa-filter"></i>
            <span>Filtered by: <strong>{{ $filteredItem->title }}</strong></span>
            <a href="{{ route('admin.todo.progress', ['type' => $type] + request()->except('item_id')) }}" class="remove">
                <i class="fas fa-times-circle"></i> Remove
            </a>
        </div>
        @endif
    @endif

    {{-- Table --}}
    @if($type === 'quiz')
        @include('admin.todo.partials.quiz-progress', ['attempts' => $quizProgress])
    @else
        @include('admin.todo.partials.assignment-progress', ['submissions' => $assignmentProgress])
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filterForm');
    const collegeFilter = document.getElementById('college-filter');
    const programFilter = document.getElementById('program-filter');

    // College change: reload page so PHP loads correct programs
    if (collegeFilter) {
        collegeFilter.addEventListener('change', function () {
            programFilter.value = '';
            form.submit();
        });
    }

    // All other auto-filter selects
    document.querySelectorAll('.auto-filter:not(#college-filter)').forEach(function (el) {
        el.addEventListener('change', function () { form.submit(); });
    });

    // Debounced search
    const search = document.querySelector('input[name="search_name"]');
    if (search) {
        let t;
        search.addEventListener('input', function () {
            clearTimeout(t);
            t = setTimeout(function () { form.submit(); }, 600);
        });
    }
});
</script>
@endpush
