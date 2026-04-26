@extends('layouts.app')

@section('title','Notes & Tasks')

@push('extra-styles')
<style>
    .notes-tabs .nav-link {
        border: 0;
        border-radius: 999px;
        color: #495057;
        font-weight: 600;
        padding: 0.65rem 1.25rem;
    }

    .notes-tabs .nav-link.active {
        background: #E34234;
        color: #fff;
        box-shadow: 0 10px 24px rgba(227, 66, 52, 0.2);
    }

    .calendar-shell {
        border: 1px solid #e9ecef;
        border-radius: 1rem;
        background: linear-gradient(180deg, #fff 0%, #fcfcfd 100%);
        padding: 1rem;
    }

    .calendar-toolbar {
        gap: 0.75rem;
    }

    .calendar-month-label {
        font-size: 1.1rem;
        font-weight: 700;
        color: #212529;
    }

    .calendar-grid,
    .calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 0.75rem;
    }

    .calendar-weekdays {
        margin-bottom: 0.75rem;
    }

    .calendar-weekday {
        text-align: center;
        font-size: 0.8rem;
        font-weight: 700;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .calendar-day {
        aspect-ratio: 2 / 1;
        min-height: 55px;
        border-radius: 1rem;
        border: 1px solid #dee2e6;
        background: #fff;
        padding: 0.75rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .calendar-day.current-month {
        border-color: #ced4da;
    }

    .calendar-day.other-month {
        background: #f8f9fa;
        color: #adb5bd;
    }

    .calendar-day.today {
        border-color: #E34234;
        box-shadow: 0 0 0 2px rgba(227, 66, 52, 0.12);
    }

    .calendar-day:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
    }

    .calendar-day-number {
        font-size: 1rem;
        font-weight: 700;
    }

    .calendar-day-note {
        font-size: 0.78rem;
        color: #6c757d;
        line-height: 1.3;
    }

    .calendar-day.other-month .calendar-day-note {
        color: inherit;
    }

    .calendar-task-list {
        list-style: disc;
        margin: 0;
        padding-left: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        overflow: hidden;
    }

    .calendar-task-item {
        font-size: 0.72rem;
        line-height: 1.25;
        color: #495057;
    }

    .calendar-task-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.35rem;
    }

    .calendar-task-title {
        flex: 1;
        min-width: 0;
        word-break: break-word;
    }

    .calendar-task-actions {
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
        flex-shrink: 0;
    }

    .calendar-task-btn {
        width: 22px;
        height: 22px;
        border: 0;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f1f3f5;
        color: #495057;
        padding: 0;
    }

    .calendar-task-btn.delete-btn {
        color: #c92a2a;
    }

    .calendar-more-btn {
        border: 0;
        background: transparent;
        color: #E34234;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0;
        text-align: left;
    }

    .day-tasks-modal .modal-content {
        border-radius: 1rem;
        overflow: hidden;
    }

    .day-tasks-modal .modal-header {
        background: #f8f9fa;
    }

    .gantt-shell {
        border: 1px solid #e9ecef;
        border-radius: 1rem;
        background: linear-gradient(180deg, #fff 0%, #fcfcfd 100%);
        overflow: hidden;
    }

    .gantt-header {
        padding: 1rem 1rem 0.75rem;
        border-bottom: 1px solid #eef1f4;
    }

    .gantt-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .gantt-board {
        overflow-x: auto;
        padding: 0 1rem 1rem;
        min-height: 25vh;
    }

    .gantt-grid {
        min-width: 980px;
    }

    .gantt-timeline,
    .gantt-row,
    .gantt-bar-row {
        display: grid;
        grid-template-columns: 240px repeat(14, minmax(48px, 1fr));
    }

    .gantt-timeline {
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 1;
    }

    .gantt-corner,
    .gantt-day-cell,
    .gantt-task-name,
    .gantt-cell {
        padding: 0.85rem 0.75rem;
        border-right: 1px solid #eef1f4;
        border-bottom: 1px solid #eef1f4;
    }

    .gantt-corner {
        font-weight: 700;
        color: #212529;
        background: #f8f9fa;
    }

    .gantt-day-cell {
        text-align: center;
        background: #f8f9fa;
    }

    .gantt-day-label {
        display: block;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #6c757d;
        margin-bottom: 0.2rem;
    }

    .gantt-day-number {
        font-size: 0.95rem;
        font-weight: 700;
        color: #212529;
    }

    .gantt-task-name {
        font-weight: 600;
        color: #212529;
        background: #fff;
        min-height: 72px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        gap: 0.35rem;
    }

    .gantt-task-name small {
        display: block;
        color: #6c757d;
        font-weight: 400;
        line-height: 1.35;
        position: relative;
        z-index: 1;
    }

    .gantt-cell {
        min-height: 72px;
        background:
            linear-gradient(180deg, rgba(248, 249, 250, 0.65) 0%, rgba(255, 255, 255, 1) 100%);
    }

    .gantt-bar-row {
        margin-top: 0;
    }

    .gantt-bar-spacer {
        border-right: 1px solid #eef1f4;
        border-bottom: 1px solid #eef1f4;
        background: transparent;
        min-height: 42px;
    }

    .gantt-bar-track {
        grid-column: 2 / -1;
        display: grid;
        grid-template-columns: repeat(14, minmax(48px, 1fr));
        align-content: start;
        padding: 0.45rem 0.3rem 0.7rem;
        border-bottom: 1px solid #eef1f4;
    }

    .gantt-bar {
        height: 14px;
        border-radius: 999px;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
    }

    .gantt-bar-meta {
        grid-column: 1 / -1;
        margin-top: 0.5rem;
        font-size: 0.72rem;
        color: #6c757d;
    }

    .gantt-empty-state {
        padding: 1.25rem;
        color: #6c757d;
        text-align: center;
    }

    .gantt-bar.bar-primary {
        background: linear-gradient(90deg, #E34234 0%, #f0655a 100%);
    }

    .gantt-bar.bar-warning {
        background: linear-gradient(90deg, #f59f00 0%, #fcc419 100%);
    }

    .gantt-bar.bar-success {
        background: linear-gradient(90deg, #2f9e44 0%, #69db7c 100%);
    }

    .notes-modal .modal-content {
        border: 0;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.18);
    }

    .notes-modal .modal-header {
        background: linear-gradient(135deg, #E34234 0%, #f0655a 100%);
        color: #fff;
        border-bottom: 0;
    }

    .notes-modal .btn-close {
        filter: invert(1);
    }

    .notes-modal .form-label {
        font-weight: 700;
        color: #212529;
    }

    .notes-modal .alert {
        border-radius: 0.85rem;
    }

    .notes-modal .ck-editor__editable_inline {
        min-height: 220px;
    }

    .notes-modal .end-date-wrap {
        display: none;
    }

    .notes-modal .end-date-wrap.is-visible {
        display: block;
    }

    .task-card-actions {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .task-card-action-btn {
        width: 30px;
        height: 30px;
        border: 0;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        color: #495057;
    }

    .task-card-action-btn:hover {
        background: #e9ecef;
    }

    .task-card-action-btn.delete-btn {
        color: #c92a2a;
    }

    .kanban-empty-state {
        border: 1px dashed #dee2e6;
        border-radius: 0.85rem;
        padding: 1rem;
        text-align: center;
        color: #6c757d;
        background: #f8f9fa;
    }

    @media (max-width: 767.98px) {
        .calendar-grid,
        .calendar-weekdays {
            gap: 0.5rem;
        }

        .calendar-day {
            min-height: 42px;
            padding: 0.6rem;
        }

        .calendar-day-note {
            font-size: 0.72rem;
        }

        .notes-tabs {
            gap: 0.5rem;
        }

        .gantt-header {
            padding: 0.9rem;
        }
    }
</style>
@endpush

@section('content')

<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Notes & Tasks</h5>

        <button class="btn btn-sm text-white" style="background:#E34234;" data-bs-toggle="modal" data-bs-target="#addNoteModal">
            <i class="fas fa-plus me-1"></i> New Task
        </button>
    </div>

    @include('dashboard.partials.advisors')

    <ul class="nav nav-pills notes-tabs mb-3" id="notesTasksTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="kanban-tab" data-bs-toggle="pill" data-bs-target="#kanban-pane" type="button" role="tab" aria-controls="kanban-pane" aria-selected="true">
                Kanban Board
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="calendar-tab" data-bs-toggle="pill" data-bs-target="#calendar-pane" type="button" role="tab" aria-controls="calendar-pane" aria-selected="false">
                Calendar View
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="gantt-tab" data-bs-toggle="pill" data-bs-target="#gantt-pane" type="button" role="tab" aria-controls="gantt-pane" aria-selected="false">
                Gantt Chart
            </button>
        </li>
    </ul>

    <div class="tab-content" id="notesTasksTabContent">
        <div class="tab-pane fade show active" id="kanban-pane" role="tabpanel" aria-labelledby="kanban-tab" tabindex="0">
            <div class="row g-2">

                <!-- TO DO -->
                <div class="col-lg-4 col-md-12">
                    <div class="card h-100">
                        <div class="card-header fw-bold">To Do</div>
                        <div class="card-body kanban-column" data-list-id="1">
                            @forelse(($tasksByList[1] ?? collect()) as $task)
                                <div class="card mb-2 kanban-card" data-task-id="{{ $task->id }}">
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                            <div class="fw-bold">{{ $task->title }}</div>
                                            <div class="task-card-actions">
                                                <button
                                                    type="button"
                                                    class="task-card-action-btn edit-note-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#addNoteModal"
                                                    data-task-id="{{ $task->id }}"
                                                    data-title="{{ $task->title }}"
                                                    data-description='@json($task->description ?? "")'
                                                    data-list-id="{{ $task->list_id }}"
                                                    data-start-date="{{ optional($task->start_date)->format('Y-m-d') }}"
                                                    data-end-date="{{ optional($task->end_date)->format('Y-m-d') }}"
                                                    title="Edit"
                                                >
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                                <form method="POST" action="/notes-tasks/{{ $task->id }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="task-card-action-btn delete-btn" title="Delete" onclick="return confirm('Delete this task?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            {{ $task->start_date ? $task->start_date->format('d M Y') : 'No start date' }}
                                            @if($task->end_date)
                                                | Ends {{ $task->end_date->format('d M Y') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            @empty
                                <div class="kanban-empty-state">No tasks in this list.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- IN PROGRESS -->
                <div class="col-lg-4 col-md-12">
                    <div class="card h-100">
                        <div class="card-header fw-bold">In Progress</div>
                        <div class="card-body kanban-column" data-list-id="2">
                            @forelse(($tasksByList[2] ?? collect()) as $task)
                                <div class="card mb-2 kanban-card border-warning" data-task-id="{{ $task->id }}">
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                            <div class="fw-bold">{{ $task->title }}</div>
                                            <div class="task-card-actions">
                                                <button
                                                    type="button"
                                                    class="task-card-action-btn edit-note-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#addNoteModal"
                                                    data-task-id="{{ $task->id }}"
                                                    data-title="{{ $task->title }}"
                                                    data-description='@json($task->description ?? "")'
                                                    data-list-id="{{ $task->list_id }}"
                                                    data-start-date="{{ optional($task->start_date)->format('Y-m-d') }}"
                                                    data-end-date="{{ optional($task->end_date)->format('Y-m-d') }}"
                                                    title="Edit"
                                                >
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                                <form method="POST" action="/notes-tasks/{{ $task->id }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="task-card-action-btn delete-btn" title="Delete" onclick="return confirm('Delete this task?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            {{ $task->start_date ? $task->start_date->format('d M Y') : 'No start date' }}
                                            @if($task->end_date)
                                                | Ends {{ $task->end_date->format('d M Y') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            @empty
                                <div class="kanban-empty-state">No tasks in this list.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- DONE -->
                <div class="col-lg-4 col-md-12">
                    <div class="card h-100">
                        <div class="card-header fw-bold">Done</div>
                        <div class="card-body kanban-column" data-list-id="3">
                            @forelse(($tasksByList[3] ?? collect()) as $task)
                                <div class="card mb-2 kanban-card border-success" data-task-id="{{ $task->id }}">
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                            <div class="fw-bold">{{ $task->title }}</div>
                                            <div class="task-card-actions">
                                                <button
                                                    type="button"
                                                    class="task-card-action-btn edit-note-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#addNoteModal"
                                                    data-task-id="{{ $task->id }}"
                                                    data-title="{{ $task->title }}"
                                                    data-description='@json($task->description ?? "")'
                                                    data-list-id="{{ $task->list_id }}"
                                                    data-start-date="{{ optional($task->start_date)->format('Y-m-d') }}"
                                                    data-end-date="{{ optional($task->end_date)->format('Y-m-d') }}"
                                                    title="Edit"
                                                >
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                                <form method="POST" action="/notes-tasks/{{ $task->id }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="task-card-action-btn delete-btn" title="Delete" onclick="return confirm('Delete this task?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            {{ $task->start_date ? $task->start_date->format('d M Y') : 'No start date' }}
                                            @if($task->end_date)
                                                | Ends {{ $task->end_date->format('d M Y') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            @empty
                                <div class="kanban-empty-state">No tasks in this list.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="tab-pane fade" id="calendar-pane" role="tabpanel" aria-labelledby="calendar-tab" tabindex="0">
            <div class="calendar-shell">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center calendar-toolbar mb-3">
                    <div>
                        <div class="calendar-month-label" id="calendarMonthLabel"></div>
                        <small class="text-muted">Monthly task calendar layout</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-outline-secondary btn-sm" type="button" id="calendarPrevBtn">
                            <i class="fas fa-chevron-left me-1"></i> Prev
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" type="button" id="calendarNextBtn">
                            Next <i class="fas fa-chevron-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <div class="calendar-weekdays mb-3">
                    <div class="calendar-weekday">Sun</div>
                    <div class="calendar-weekday">Mon</div>
                    <div class="calendar-weekday">Tue</div>
                    <div class="calendar-weekday">Wed</div>
                    <div class="calendar-weekday">Thu</div>
                    <div class="calendar-weekday">Fri</div>
                    <div class="calendar-weekday">Sat</div>
                </div>

                <div class="calendar-grid" id="calendarGrid"></div>
            </div>
        </div>

        <div class="tab-pane fade" id="gantt-pane" role="tabpanel" aria-labelledby="gantt-tab" tabindex="0">
            <div class="gantt-shell">
                <div class="gantt-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div>
                        <div class="calendar-month-label mb-1">Task Timeline</div>
                        <small class="text-muted">Only tasks with both start date and end date are shown here.</small>
                    </div>
                    <div class="gantt-controls">
                        <button class="btn btn-outline-secondary btn-sm" type="button" id="ganttPrevBtn">
                            <i class="fas fa-chevron-left me-1"></i> Prev 14 Days
                        </button>
                        <div class="text-muted small" id="ganttRangeLabel"></div>
                        <button class="btn btn-outline-secondary btn-sm" type="button" id="ganttNextBtn">
                            Next 14 Days <i class="fas fa-chevron-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <div class="gantt-board">
                    <div class="gantt-grid" id="ganttGrid"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<form id="deleteTaskForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

<div class="modal fade day-tasks-modal" id="dayTasksModal" tabindex="-1" aria-labelledby="dayTasksModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1" id="dayTasksModalLabel">Tasks For Day</h5>
                    <small class="text-muted" id="dayTasksModalSubLabel">Search and sort tasks for this date.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0" id="dayTasksTable">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>List</th>
                                <th>Start Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade notes-modal" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1" id="addNoteModalLabel">Add Note / Task</h5>
                    <small>Create and save a note/task.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addNoteForm" method="POST" action="/notes-tasks">
                    @csrf
                    <input type="hidden" name="_method" id="noteFormMethod" value="POST">
                    <input type="hidden" id="noteTaskId" name="note_task_id" value="{{ old('note_task_id') }}">

                    <input type="hidden" name="advisor_id" id="modalAdvisorId" value="{{ old('advisor_id', session('advisor_id')) }}">
                    <input type="hidden" name="investor_id" id="modalInvestorId" value="{{ old('investor_id', session('investor_id')) }}">

                    @if($errors->any())
                        <div class="alert alert-danger mb-3">
                            Please check the form fields and try again.
                        </div>
                    @endif

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label for="noteTitle" class="form-label">Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="noteTitle" name="title" value="{{ old('title') }}" placeholder="Enter note or task title">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="noteListId" class="form-label">List</label>
                            <select class="form-select @error('list_id') is-invalid @enderror" id="noteListId" name="list_id">
                                @foreach($noteTaskLists as $noteTaskList)
                                    <option value="{{ $noteTaskList->id }}" {{ (string) old('list_id', 1) === (string) $noteTaskList->id ? 'selected' : '' }}>
                                        {{ $noteTaskList->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('list_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="noteDescription" class="form-label">Description</label>
                        <textarea id="noteDescription" name="description" class="form-control @error('description') is-invalid @enderror" rows="8">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="noteStartDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="noteStartDate" name="start_date" value="{{ old('start_date') }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" value="1" id="specifyEndDate" name="has_end_date" {{ old('has_end_date') ? 'checked' : '' }}>
                                <label class="form-check-label" for="specifyEndDate">
                                    Specify end date
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 end-date-wrap" id="endDateWrap">
                        <label for="noteEndDate" class="form-label">End Date</label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="noteEndDate" name="end_date" value="{{ old('end_date') }}">
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addNoteForm" id="noteSubmitBtn" class="btn text-white" style="background:#E34234;">Save</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('page-js')
<script>
$(function () {
    var noteEditor = null;
    var calendarTasks = @json($calendarTasks);
    var ganttTasks = @json($ganttTasks);
    var addNoteModal = $('#addNoteModal');
    var dayTasksModal = $('#dayTasksModal');
    var addNoteForm = $('#addNoteForm');
    var addNoteModalLabel = $('#addNoteModalLabel');
    var noteSubmitBtn = $('#noteSubmitBtn');
    var noteDescriptionField = $('#noteDescription');
    var noteTaskIdField = $('#noteTaskId');
    var noteFormMethod = $('#noteFormMethod');
    var deleteTaskForm = $('#deleteTaskForm');
    var dayTasksTable = $('#dayTasksTable');
    var dayTasksTableInstance = null;
    var pendingEditTask = null;
    var ganttGrid = $('#ganttGrid');
    var ganttRangeLabel = $('#ganttRangeLabel');
    var ganttWindowStart = new Date(todayAtMidnight());

    function syncKanbanEmptyState(column) {
        var emptyState = column.children('.kanban-empty-state');
        var hasCards = column.children('.kanban-card').length > 0;

        if (hasCards) {
            emptyState.remove();
            return;
        }

        if (!emptyState.length) {
            column.append('<div class="kanban-empty-state">No tasks in this list.</div>');
        }
    }

    function escapeHtml(value) {
        return $('<div>').text(value || '').html();
    }

    function todayAtMidnight() {
        var now = new Date();
        return new Date(now.getFullYear(), now.getMonth(), now.getDate());
    }

    function formatIsoDate(date) {
        return [
            date.getFullYear(),
            String(date.getMonth() + 1).padStart(2, '0'),
            String(date.getDate()).padStart(2, '0')
        ].join('-');
    }

    function formatPrettyDate(dateString) {
        var date = new Date(dateString + 'T00:00:00');

        return date.toLocaleDateString('en-US', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }

    function addDays(date, days) {
        var nextDate = new Date(date);
        nextDate.setDate(nextDate.getDate() + days);
        return nextDate;
    }

    function getListNameById(listId) {
        return $('#noteListId option[value="' + listId + '"]').text() || '';
    }

    function getTaskFromButton(button) {
        return {
            id: button.data('task-id'),
            title: button.attr('data-title') || '',
            description: button.attr('data-description') ? JSON.parse(button.attr('data-description')) : '',
            list_id: button.data('list-id') || 1,
            start_date: button.attr('data-start-date') || '',
            end_date: button.attr('data-end-date') || ''
        };
    }

    function buildCalendarEditButton(task) {
        return '<button type="button" class="calendar-task-btn calendar-edit-btn"' +
            ' data-task-id="' + task.id + '"' +
            ' data-title="' + escapeHtml(task.title) + '"' +
            " data-description='" + escapeHtml(JSON.stringify(task.description || '')) + "'" +
            ' data-list-id="' + task.list_id + '"' +
            ' data-start-date="' + (task.start_date || '') + '"' +
            ' data-end-date="' + (task.end_date || '') + '"' +
            ' title="Edit"><i class="fas fa-pen"></i></button>';
    }

    function openDayTasksModal(dateKey) {
        var tasks = calendarTasks[dateKey] || [];
        var prettyDate = new Date(dateKey + 'T00:00:00');
        var tbodyMarkup = '';

        $('#dayTasksModalLabel').text('Tasks For ' + prettyDate.toLocaleDateString('en-US', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }));
        $('#dayTasksModalSubLabel').text(tasks.length + ' task(s) scheduled on this day.');

        tasks.forEach(function (task) {
            tbodyMarkup += '<tr>';
            tbodyMarkup += '<td>' + escapeHtml(task.title) + '</td>';
            tbodyMarkup += '<td>' + escapeHtml(getListNameById(task.list_id)) + '</td>';
            tbodyMarkup += '<td>' + escapeHtml(task.start_date || '') + '</td>';
            tbodyMarkup += '<td><div class="task-card-actions">';
            tbodyMarkup += buildCalendarEditButton(task);
            tbodyMarkup += '<button type="button" class="task-card-action-btn delete-btn calendar-delete-btn" data-task-id="' + task.id + '" title="Delete"><i class="fas fa-trash"></i></button>';
            tbodyMarkup += '</div></td>';
            tbodyMarkup += '</tr>';
        });

        dayTasksTable.find('tbody').html(tbodyMarkup);

        if (dayTasksTableInstance) {
            dayTasksTableInstance.destroy();
        }

        dayTasksTableInstance = dayTasksTable.DataTable({
            paging: true,
            searching: true,
            info: true,
            order: [[0, 'asc']]
        });

        dayTasksModal.modal('show');
    }

    function renderGantt() {
        var days = [];
        var windowEnd = addDays(ganttWindowStart, 13);
        var headerMarkup = '<div class="gantt-timeline"><div class="gantt-corner">Task</div>';
        var rowsMarkup = '';

        ganttRangeLabel.text(
            ganttWindowStart.toLocaleDateString('en-US', { day: '2-digit', month: 'short', year: 'numeric' }) +
            ' - ' +
            windowEnd.toLocaleDateString('en-US', { day: '2-digit', month: 'short', year: 'numeric' })
        );

        for (var dayIndex = 0; dayIndex < 14; dayIndex++) {
            var day = addDays(ganttWindowStart, dayIndex);
            days.push(day);
            headerMarkup += '<div class="gantt-day-cell"><span class="gantt-day-label">' +
                day.toLocaleDateString('en-US', { weekday: 'short' }) +
                '</span><span class="gantt-day-number">' +
                String(day.getDate()).padStart(2, '0') +
                '</span></div>';
        }

        headerMarkup += '</div>';

        ganttTasks.forEach(function (task, taskIndex) {
            var taskStart = new Date(task.start_date + 'T00:00:00');
            var taskEnd = new Date(task.end_date + 'T00:00:00');
            var visibleStart = taskStart > ganttWindowStart ? taskStart : ganttWindowStart;
            var visibleEnd = taskEnd < windowEnd ? taskEnd : windowEnd;
            var isVisible = visibleStart <= visibleEnd;
            var barStart;
            var barSpan;
            var listName = getListNameById(task.list_id);
            var isClipped = taskStart < ganttWindowStart || taskEnd > windowEnd;

            if (!isVisible) {
                return;
            }

            barStart = Math.floor((visibleStart - ganttWindowStart) / 86400000) + 1;
            barSpan = Math.floor((visibleEnd - visibleStart) / 86400000) + 1;

            rowsMarkup += '<div class="gantt-row">';
            rowsMarkup += '<div class="gantt-task-name">' + escapeHtml(task.title);
            rowsMarkup += '<small>' + escapeHtml(listName || 'Task') + '</small>';
            rowsMarkup += '</div>';

            for (var cellIndex = 0; cellIndex < 14; cellIndex++) {
                rowsMarkup += '<div class="gantt-cell"></div>';
            }

            rowsMarkup += '</div>';
            rowsMarkup += '<div class="gantt-bar-row">';
            rowsMarkup += '<div class="gantt-bar-spacer"></div>';
            rowsMarkup += '<div class="gantt-bar-track">';
            rowsMarkup += '<div class="gantt-bar bar-' + (task.list_id === 1 ? 'primary' : (task.list_id === 2 ? 'warning' : 'success')) + '" style="grid-column: ' + barStart + ' / span ' + barSpan + ';"></div>';

            if (isClipped) {
                rowsMarkup += '<div class="gantt-bar-meta" style="grid-column: ' + barStart + ' / span ' + barSpan + ';">';
                rowsMarkup += 'Start: ' + escapeHtml(formatPrettyDate(task.start_date)) + ' | End: ' + escapeHtml(formatPrettyDate(task.end_date));
                rowsMarkup += '</div>';
            }

            rowsMarkup += '</div>';
            rowsMarkup += '</div>';
        });

        if (!rowsMarkup) {
            rowsMarkup = '<div class="gantt-empty-state">No tasks with both start and end dates in this 14-day range.</div>';
        }

        ganttGrid.html(headerMarkup + rowsMarkup);
    }

    $('.kanban-column').sortable({
        connectWith: '.kanban-column',
        items: '.kanban-card',
        placeholder: 'kanban-placeholder',
        tolerance: 'pointer',
        receive: function (event, ui) {
            var destinationColumn = $(this);
            var sourceColumn = ui.sender;
            var taskCard = ui.item;
            var taskId = taskCard.data('task-id');
            var listId = destinationColumn.data('list-id');

            destinationColumn.children('.kanban-empty-state').remove();
            syncKanbanEmptyState(sourceColumn);

            if (!taskId || !listId) {
                window.location.reload();
                return;
            }

            $.ajax({
                url: '/notes-tasks/' + taskId + '/list',
                method: 'PATCH',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    list_id: listId
                }
            }).fail(function () {
                window.location.reload();
            });
        }
    });

    $('.kanban-column').each(function () {
        syncKanbanEmptyState($(this));
    });

    var today = new Date();
    var visibleMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    var monthLabel = $('#calendarMonthLabel');
    var calendarGrid = $('#calendarGrid');

    function renderCalendar() {
        var year = visibleMonth.getFullYear();
        var month = visibleMonth.getMonth();
        var firstDay = new Date(year, month, 1);
        var lastDay = new Date(year, month + 1, 0);
        var startOffset = firstDay.getDay();
        var daysInMonth = lastDay.getDate();
        var prevMonthLastDay = new Date(year, month, 0).getDate();
        var totalCells = Math.ceil((startOffset + daysInMonth) / 7) * 7;
        var markup = '';

        monthLabel.text(firstDay.toLocaleDateString('en-US', {
            month: 'long',
            year: 'numeric'
        }));

        for (var i = 0; i < totalCells; i++) {
            var dayNumber;
            var cellDate;
            var classes = ['calendar-day'];
            var taskDateKey;
            var tasksMarkup = '';

            if (i < startOffset) {
                dayNumber = prevMonthLastDay - startOffset + i + 1;
                cellDate = new Date(year, month - 1, dayNumber);
                classes.push('other-month');
            } else if (i >= startOffset + daysInMonth) {
                dayNumber = i - (startOffset + daysInMonth) + 1;
                cellDate = new Date(year, month + 1, dayNumber);
                classes.push('other-month');
            } else {
                dayNumber = i - startOffset + 1;
                cellDate = new Date(year, month, dayNumber);
                classes.push('current-month');
            }

            if (
                cellDate.getFullYear() === today.getFullYear() &&
                cellDate.getMonth() === today.getMonth() &&
                cellDate.getDate() === today.getDate()
            ) {
                classes.push('today');
            }

            taskDateKey = [
                cellDate.getFullYear(),
                String(cellDate.getMonth() + 1).padStart(2, '0'),
                String(cellDate.getDate()).padStart(2, '0')
            ].join('-');

            if (calendarTasks[taskDateKey] && calendarTasks[taskDateKey].length) {
                var visibleTasks = calendarTasks[taskDateKey].slice(0, 2);
                var hiddenTaskCount = calendarTasks[taskDateKey].length - visibleTasks.length;

                tasksMarkup += '<ul class="calendar-task-list">';

                visibleTasks.forEach(function (task) {
                    tasksMarkup += '<li class="calendar-task-item">';
                    tasksMarkup += '<div class="calendar-task-row">';
                    tasksMarkup += '<span class="calendar-task-title">' + escapeHtml(task.title) + '</span>';
                    tasksMarkup += '<span class="calendar-task-actions">';
                    tasksMarkup += buildCalendarEditButton(task);
                    tasksMarkup += '<button type="button" class="calendar-task-btn delete-btn calendar-delete-btn"';
                    tasksMarkup += ' data-task-id="' + task.id + '" title="Delete"><i class="fas fa-trash"></i></button>';
                    tasksMarkup += '</span>';
                    tasksMarkup += '</div>';
                    tasksMarkup += '</li>';
                });

                tasksMarkup += '</ul>';

                if (hiddenTaskCount > 0) {
                    tasksMarkup += '<button type="button" class="calendar-more-btn calendar-day-more-btn" data-date-key="' + taskDateKey + '">';
                    tasksMarkup += calendarTasks[taskDateKey].length + ' task(s)</button>';
                }
            } else {
                tasksMarkup = '<div class="calendar-day-note">No tasks</div>';
            }

            markup += '<div class="' + classes.join(' ') + '">';
            markup += '<div class="calendar-day-number">' + dayNumber + '</div>';
            markup += tasksMarkup;
            markup += '</div>';
        }

        calendarGrid.html(markup);
    }

    $('#calendarPrevBtn').on('click', function () {
        visibleMonth.setMonth(visibleMonth.getMonth() - 1);
        renderCalendar();
    });

    $('#calendarNextBtn').on('click', function () {
        visibleMonth.setMonth(visibleMonth.getMonth() + 1);
        renderCalendar();
    });

    $('#ganttPrevBtn').on('click', function () {
        ganttWindowStart = addDays(ganttWindowStart, -14);
        renderGantt();
    });

    $('#ganttNextBtn').on('click', function () {
        ganttWindowStart = addDays(ganttWindowStart, 14);
        renderGantt();
    });

    $('#advisor_id').on('changed.bs.select', function () {
        var advisor_id = $(this).val();
        var url = new URL(window.location.href);
        url.searchParams.set('advisor_id', advisor_id);
        window.location.href = url.toString();
    });

    $('#investor_id').on('changed.bs.select', function () {
        var investor_id = $(this).val();
        var url = new URL(window.location.href);
        url.searchParams.set('investor_id', investor_id);
        window.location.href = url.toString();
    });

    $('#specifyEndDate').on('change', function () {
        $('#endDateWrap').toggleClass('is-visible', $(this).is(':checked'));
    });

    function updateEditorContent(content) {
        noteDescriptionField.val(content || '');

        if (noteEditor) {
            noteEditor.setData(content || '');
        }
    }

    function resetNoteFormForCreate() {
        addNoteForm.attr('action', '/notes-tasks');
        noteFormMethod.val('POST');
        noteTaskIdField.val('');
        addNoteModalLabel.text('Add Note / Task');
        noteSubmitBtn.text('Save');

        if (!{{ $errors->any() ? 'true' : 'false' }}) {
            addNoteForm[0].reset();
            $('#noteListId').val('1');
            updateEditorContent('');
        }

        $('#modalAdvisorId').val($('#advisor_id').val() || '');
        $('#modalInvestorId').val($('#investor_id').val() || '');
        $('#endDateWrap').toggleClass('is-visible', $('#specifyEndDate').is(':checked'));
    }

    function populateNoteFormForEdit(task) {
        addNoteForm.attr('action', '/notes-tasks/' + task.id);
        noteFormMethod.val('PATCH');
        noteTaskIdField.val(task.id);
        addNoteModalLabel.text('Edit Note / Task');
        noteSubmitBtn.text('Update');
        $('#noteTitle').val(task.title || '');
        $('#noteListId').val(String(task.list_id || 1));
        $('#noteStartDate').val(task.start_date || '');
        $('#noteEndDate').val(task.end_date || '');
        $('#specifyEndDate').prop('checked', !!task.end_date);
        $('#modalAdvisorId').val($('#advisor_id').val() || '');
        $('#modalInvestorId').val($('#investor_id').val() || '');
        $('#endDateWrap').toggleClass('is-visible', !!task.end_date);
        updateEditorContent(task.description);
    }

    addNoteModal.on('show.bs.modal', function (event) {
        var trigger = $(event.relatedTarget);

        if (pendingEditTask) {
            populateNoteFormForEdit(pendingEditTask);
            pendingEditTask = null;
            return;
        }

        if (trigger.hasClass('edit-note-btn')) {
            populateNoteFormForEdit(getTaskFromButton(trigger));
            return;
        }

        resetNoteFormForCreate();
    });

    $('#addNoteModal').on('hidden.bs.modal', function () {
        if (!{{ $errors->any() ? 'true' : 'false' }}) {
            resetNoteFormForCreate();
        }
    });

    $('#addNoteModal').on('shown.bs.modal', function () {
        var editorContent = noteDescriptionField.val();

        if (noteEditor) {
            noteEditor.setData(editorContent || '');
            return;
        }

        ClassicEditor
            .create(document.querySelector('#noteDescription'), {
                toolbar: [
                    'heading',
                    '|',
                    'bold',
                    'italic',
                    'bulletedList',
                    'numberedList',
                    '|',
                    'link',
                    'blockQuote',
                    '|',
                    'undo',
                    'redo'
                ]
            })
            .then(function (editor) {
                noteEditor = editor;
                noteEditor.setData(editorContent || '');
            })
            .catch(function (error) {
                console.error(error);
            });
    });

    $('#addNoteForm').on('submit', function () {
        $('#modalAdvisorId').val($('#advisor_id').val() || '');
        $('#modalInvestorId').val($('#investor_id').val() || '');

        if (noteEditor) {
            $('#noteDescription').val(noteEditor.getData());
        }
    });

    $(document).on('click', '.calendar-delete-btn', function () {
        var taskId = $(this).data('task-id');

        if (!taskId || !confirm('Delete this task?')) {
            return;
        }

        deleteTaskForm.attr('action', '/notes-tasks/' + taskId);
        deleteTaskForm.trigger('submit');
    });

    $(document).on('click', '.calendar-edit-btn', function () {
        pendingEditTask = getTaskFromButton($(this));
        dayTasksModal.modal('hide');
        addNoteModal.modal('show');
    });

    $(document).on('click', '.calendar-day-more-btn', function () {
        openDayTasksModal($(this).data('date-key'));
    });

    @if($errors->any())
        @if(old('note_task_id'))
            addNoteForm.attr('action', '/notes-tasks/{{ old('note_task_id') }}');
            noteFormMethod.val('PATCH');
            addNoteModalLabel.text('Edit Note / Task');
            noteSubmitBtn.text('Update');
        @endif
        $('#addNoteModal').modal('show');
        $('#endDateWrap').toggleClass('is-visible', $('#specifyEndDate').is(':checked'));
    @endif

    renderCalendar();
    renderGantt();
});
</script>
@endpush
