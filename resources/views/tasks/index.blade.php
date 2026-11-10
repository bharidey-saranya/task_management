@extends('layouts.app')

@section('title', 'Taskboard · Modern SaaS Task Manager')

@section('content')

@php
    $isUnassignedFilter = ($selectedProject !== null && $selectedProject !== '' && in_array(strtolower((string)$selectedProject), ['none', 'unassigned', 'null', '0', 'no-project'], true));
    $selectedProjectModel = ($selectedProject !== null && $selectedProject !== '' && ! $isUnassignedFilter)
        ? $projects->firstWhere('id', (int)$selectedProject)
        : null;
    $isProjectInactive = ($selectedProjectModel && ! $selectedProjectModel->is_active);
@endphp

{{-- ====================================================
     B. DASHBOARD STATISTICS (4 CARDS GRID)
==================================================== --}}
<section class="stats-grid" aria-label="Dashboard Statistics">
    {{-- 1. Total Tasks --}}
    <div class="stat-card stat-total">
        <div class="stat-card-left">
            <div class="stat-icon-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                    <path d="m9 14 2 2 4-4"></path>
                </svg>
            </div>
            <div class="stat-label">Total Tasks</div>
        </div>
        <div class="stat-value">{{ $stats['total'] ?? 0 }}</div>
    </div>

    {{-- 2. Completed --}}
    <div class="stat-card stat-completed">
        <div class="stat-card-left">
            <div class="stat-icon-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-value">{{ $stats['completed'] ?? 0 }}</div>
    </div>

    {{-- 3. In Progress --}}
    <div class="stat-card stat-inprogress">
        <div class="stat-card-left">
            <div class="stat-icon-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div class="stat-label">In Progress</div>
        </div>
        <div class="stat-value">{{ $stats['in_progress'] ?? 0 }}</div>
    </div>

    {{-- 4. High Priority --}}
    <div class="stat-card stat-highpriority">
        <div class="stat-card-left">
            <div class="stat-icon-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                    <line x1="4" y1="22" x2="4" y2="15"></line>
                </svg>
            </div>
            <div class="stat-label">High Priority</div>
        </div>
        <div class="stat-value">{{ $stats['high_priority'] ?? 0 }}</div>
    </div>
</section>

{{-- ====================================================
     D. CREATE A NEW TASK CARD (WITH ILLUSTRATION)
==================================================== --}}
<section class="card" style="overflow: hidden;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
        <div class="card-title-group">
            <div class="card-title-icon" style="background: var(--primary-gradient); color: #ffffff;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </div>
            <div>
                <h2 class="card-title" style="margin: 0;">Create a New Task</h2>
                <p class="card-subtitle">Add a task to get started with your project.</p>
            </div>
        </div>

        {{-- Aesthetic SaaS Clipboard & Pen Illustration (Compact) --}}
        <div style="display: none; @media(min-width: 768px) { display: block; } opacity: 0.85; margin: -6px 0 -12px 0;">
            <svg width="78" height="60" viewBox="0 0 140 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="25" y="15" width="75" height="90" rx="14" fill="#F3F0FF" stroke="#DDD6FE" stroke-width="2.5"/>
                <rect x="45" y="8" width="35" height="14" rx="4" fill="#8B5CF6"/>
                <circle cx="62.5" cy="15" r="3" fill="#FFFFFF"/>
                <line x1="40" y1="38" x2="85" y2="38" stroke="#A78BFA" stroke-width="3.5" stroke-linecap="round"/>
                <polyline points="36 38 38 40 42 36" stroke="#8B5CF6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <line x1="40" y1="54" x2="85" y2="54" stroke="#C4B5FD" stroke-width="3" stroke-linecap="round"/>
                <polyline points="36 54 38 56 42 52" stroke="#8B5CF6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <line x1="40" y1="70" x2="75" y2="70" stroke="#C4B5FD" stroke-width="3" stroke-linecap="round"/>
                <polyline points="36 70 38 72 42 68" stroke="#8B5CF6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <!-- Pencil -->
                <g transform="rotate(35 95 65)">
                    <rect x="90" y="20" width="12" height="60" rx="3" fill="#7C4DFF"/>
                    <path d="M90 80 L96 92 L102 80 Z" fill="#DDD6FE"/>
                    <circle cx="96" cy="91" r="1.5" fill="#4C1D95"/>
                    <rect x="90" y="20" width="12" height="10" rx="2" fill="#A78BFA"/>
                </g>
            </svg>
        </div>
    </div>

    <form method="POST" action="{{ route('tasks.store', [], false) }}" id="create-task-form">
        @csrf
        <input type="hidden" name="filter_project" value="{{ $selectedProject }}">

        <div class="form-row stack-sm">
            <div class="form-control-wrapper">
                <span class="input-icon-left">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="8" y1="6" x2="21" y2="6"></line>
                        <line x1="8" y1="12" x2="21" y2="12"></line>
                        <line x1="8" y1="18" x2="21" y2="18"></line>
                        <line x1="3" y1="6" x2="3.01" y2="6"></line>
                        <line x1="3" y1="12" x2="3.01" y2="12"></line>
                        <line x1="3" y1="18" x2="3.01" y2="18"></line>
                    </svg>
                </span>
                <input
                    type="text"
                    id="create-task-name"
                    name="name"
                    class="form-input"
                    placeholder="What needs to be done? (e.g., Prepare client presentation)"
                    maxlength="255"
                    required
                    value="{{ old('name') }}"
                    autofocus
                >
            </div>

            <div style="position: relative; display: flex; align-items: center;">
                <span class="select-icon-left">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                        <polyline points="2 17 12 22 22 17"></polyline>
                        <polyline points="2 12 12 17 22 12"></polyline>
                    </svg>
                </span>
                <select name="project_id" class="form-select" aria-label="Assign to project">
                    <option value="">No Project (General)</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" @selected((string)$selectedProject === (string)$project->id) @disabled(!$project->is_active)>
                            {{ $project->name }}@if(!$project->is_active) (Inactive - Locked)@endif
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary-purple" id="btn-add-task">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Add Task</span>
            </button>
        </div>
    </form>
</section>

{{-- ====================================================
     E. TASK MANAGEMENT SECTION CARD
==================================================== --}}
<section class="card">
    <div class="card-header-flex">
        <div class="card-title-group">
            <div class="card-title-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
            </div>
            <div>
                <h2 class="card-title">
                    <span>Tasks</span>
                    <span class="badge-count">{{ $tasks->count() }}</span>
                </h2>
            </div>
        </div>

        {{-- PROJECT DROPDOWN FILTER --}}
        <form method="GET" action="{{ route('tasks.index', [], false) }}" class="form-row" style="margin:0;">
            <label for="project-filter" style="font-size:13px;font-weight:600;color:var(--text-muted);white-space:nowrap;">
                Filter Project:
            </label>
            <div style="position: relative; display: flex; align-items: center;">
                <span class="select-icon-left" style="left: 12px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                        <polyline points="2 17 12 22 22 17"></polyline>
                        <polyline points="2 12 12 17 22 12"></polyline>
                    </svg>
                </span>
                <select
                    id="project-filter"
                    name="project"
                    class="form-select"
                    style="height:40px;min-width:190px;"
                    onchange="this.form.submit()"
                >
                    <option value="">All Projects ({{ $totalActiveTasks ?? $projects->sum('tasks_count') }})</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" @selected((string)$selectedProject === (string)$project->id)>
                            {{ $project->name }} ({{ $project->tasks_count }})@if(!$project->is_active) [Inactive]@endif
                        </option>
                    @endforeach
                    <option value="none" @selected($isUnassignedFilter)>
                        Unassigned (No Project) ({{ $unassignedCount ?? 0 }})
                    </option>
                </select>
            </div>

            @if($selectedProject !== null && $selectedProject !== '')
                <a href="{{ route('tasks.index', [], false) }}" class="btn btn-ghost btn-sm" title="Clear filter">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Helper text from reference --}}
    <div class="card-helper-text">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary); flex-shrink: 0;">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
        </svg>
        <span>Drag tasks to reorder them. <strong>#1 priority goes at the top</strong>, #2 next, and priority numbers automatically update.</span>
    </div>

    {{-- Inactive Project Warning Banner --}}
    @if($isProjectInactive)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:#FFFBEB;border:1px solid #FDE68A;border-radius:var(--radius-md);margin-bottom:14px;font-size:12.5px;color:#92400E;gap:12px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:16px;">⚠️</span>
                <span>Project <strong>"{{ $selectedProjectModel->name }}"</strong> is marked as <strong>Inactive</strong>. Tasks cannot be modified or reordered until this project is reactivated.</span>
            </div>
            <form method="POST" action="{{ route('projects.toggle-status', $selectedProjectModel, false) }}" style="margin:0;">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-secondary btn-sm" style="color:var(--success);border-color:#86EFAC;font-weight:700;">
                    Activate Project
                </button>
            </form>
        </div>
    @endif

    {{-- Active filter indicator bar --}}
    @if($selectedProject !== null && $selectedProject !== '')
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 16px;background:#F3F0FF;border:1px solid #E9D5FF;border-radius:var(--radius-md);margin-bottom:16px;font-size:13px;color:#5B21B6;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span>📁</span>
                @if($isUnassignedFilter)
                    <span>Filtering: <strong>Unassigned Tasks (No Project)</strong></span>
                @else
                    @php $currProj = $projects->firstWhere('id', (int)$selectedProject); @endphp
                    <span>Filtering: <strong>{{ $currProj->name ?? 'Selected Project' }}</strong>@if($isProjectInactive) <em style="color:#B45309;font-weight:600;">(Inactive)</em>@endif</span>
                @endif
            </div>
            <a href="{{ route('tasks.index', [], false) }}" style="color:var(--primary);font-weight:700;text-decoration:none;font-size:12.5px;">
                Show All Tasks (Clear Filter) &rarr;
            </a>
        </div>
    @endif

    {{-- Task List / Empty State --}}
    @if($tasks->isEmpty())
        <div class="empty-state" style="padding: 48px 24px; text-align: center; border: 2px dashed var(--border); border-radius: var(--radius-lg); background: #ffffff;">
            <div style="font-size: 36px; margin-bottom: 8px;">📝</div>
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                @if($selectedProject !== null && $selectedProject !== '')
                    No tasks in this project yet
                @else
                    No tasks found
                @endif
            </h3>
            <p style="font-size: 13px; color: var(--text-muted); max-width: 420px; margin: 0 auto;">
                @if($selectedProject !== null && $selectedProject !== '')
                    Add your first task for this project using the form above, or <a href="{{ route('tasks.index', [], false) }}" style="color:var(--primary);font-weight:600;">view all tasks</a>.
                @else
                    You have no active tasks. Create your first task using the form above!
                @endif
            </p>
        </div>
    @else
        <ul
            class="task-list"
            id="task-list"
            data-reorder-url="{{ route('tasks.reorder', [], false) }}"
        >
            @foreach($tasks as $task)
                @php
                    $pLevel = min($task->priority, 3);
                    $iconGlyph = match($pLevel) {
                        1 => '🔥',
                        2 => '⚡',
                        3 => '⚑',
                        default => '●',
                    };
                    $taskProjectInactive = ($task->project && ! $task->project->is_active);
                @endphp
                <li class="task-item" data-id="{{ $task->id }}">
                    @if($taskProjectInactive)
                        <div style="opacity: 0.35; cursor: not-allowed; padding: 2px 4px; display: flex; align-items: center;" title="Reordering disabled (project is inactive)">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                                <circle cx="9" cy="6" r="1.75"/>
                                <circle cx="15" cy="6" r="1.75"/>
                                <circle cx="9" cy="12" r="1.75"/>
                                <circle cx="15" cy="12" r="1.75"/>
                                <circle cx="9" cy="18" r="1.75"/>
                                <circle cx="15" cy="18" r="1.75"/>
                            </svg>
                        </div>
                    @else
                        <div class="drag-handle" title="Drag to reorder priority" aria-label="Drag handle">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                                <circle cx="9" cy="6" r="1.75"/>
                                <circle cx="15" cy="6" r="1.75"/>
                                <circle cx="9" cy="12" r="1.75"/>
                                <circle cx="15" cy="12" r="1.75"/>
                                <circle cx="9" cy="18" r="1.75"/>
                                <circle cx="15" cy="18" r="1.75"/>
                            </svg>
                        </div>
                    @endif

                    {{-- Priority Badge with Flame/Lightning/Flag glyphs --}}
                    <span class="priority-badge p-{{ $pLevel }}" title="Priority #{{ $task->priority }}">
                        <span class="icon-glyph">{{ $iconGlyph }}</span>
                        <span>PRIORITY #{{ $task->priority }}</span>
                    </span>

                    <div class="task-content">
                        <div class="task-name">{{ $task->name }}</div>
                        <div class="task-meta">
                            @if($task->project)
                                <a href="{{ route('tasks.index', ['project' => $task->project->id], false) }}" class="project-tag" title="Filter by {{ $task->project->name }}">
                                    📁 {{ $task->project->name }}
                                </a>
                                @if($taskProjectInactive)
                                    <span class="badge-status inactive" style="font-size: 10px; padding: 1px 7px;">Project Inactive</span>
                                @endif
                            @else
                                <span class="project-tag none">No Project</span>
                            @endif

                            <span title="Created: {{ $task->created_at->format('Y-m-d H:i:s') }}" style="display: flex; align-items: center; gap: 4px;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <span>Created {{ $task->created_at->diffForHumans() }}</span>
                            </span>

                            @if($task->updated_at->gt($task->created_at))
                                <span title="Updated: {{ $task->updated_at->format('Y-m-d H:i:s') }}">
                                    &middot; Updated {{ $task->updated_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="task-actions">
                        @if($taskProjectInactive)
                            <button
                                type="button"
                                class="btn btn-secondary btn-sm"
                                disabled
                                style="opacity: 0.45; cursor: not-allowed;"
                                title="Cannot edit task while its project is inactive"
                            >
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                                <span>Edit</span>
                            </button>

                            <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                disabled
                                style="opacity: 0.45; cursor: not-allowed;"
                                title="Cannot delete task while its project is inactive"
                            >
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                                <span>Delete</span>
                            </button>
                        @else
                            <button
                                type="button"
                                class="btn btn-secondary btn-sm"
                                onclick="openEditModal({{ $task->id }}, @js($task->name), {{ $task->project_id ?? 'null' }}, {{ $task->priority }})"
                                title="Edit task"
                            >
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                                <span>Edit</span>
                            </button>

                            <form
                                method="POST"
                                action="{{ route('tasks.destroy', $task, false) }}"
                                onsubmit="return confirm('Are you sure you want to delete this task?');"
                                style="margin:0;"
                            >
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="filter_project" value="{{ $selectedProject }}">
                                <button type="submit" class="btn btn-danger btn-sm" title="Delete task">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                    <span>Delete</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</section>

{{-- ====================================================
     F. PROJECT MANAGEMENT SECTION CARD
==================================================== --}}
<section class="card">
    <div class="card-header-flex">
        <div class="card-title-group">
            <div class="card-title-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                    <polyline points="2 17 12 22 22 17"></polyline>
                    <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
            </div>
            <div>
                <h2 class="card-title" style="margin: 0;">Project Management</h2>
                <p class="card-subtitle">Create projects to categorize your tasks. Tasks can be grouped, filtered, and reordered per project.</p>
            </div>
        </div>

        <button
            type="button"
            class="btn btn-primary-purple btn-sm"
            onclick="toggleProjectCreationForm()"
            id="toggle-project-btn"
        >
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Create Project</span>
        </button>
    </div>

    {{-- Project Creation Form --}}
    <div id="project-create-container" style="margin-bottom: 22px; padding: 18px 20px; background: #FAFAFD; border: 1px solid var(--border); border-radius: var(--radius-md);">
        <form method="POST" action="{{ route('projects.store', [], false) }}" class="form-row stack-sm">
            @csrf
            <div class="form-control-wrapper">
                <span class="input-icon-left">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                    </svg>
                </span>
                <input
                    type="text"
                    name="name"
                    class="form-input"
                    placeholder="New project name (e.g. Mobile App Redesign)"
                    maxlength="120"
                    required
                >
            </div>
            <button type="submit" class="btn btn-primary-purple">
                <span>Save Project</span>
            </button>
        </form>
    </div>

    {{-- Quick Project Filter Chips --}}
    @if($projects->isNotEmpty())
        <div style="font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">
            Quick Project Filters:
        </div>
        <div class="chips-container" style="margin-bottom: 20px;">
            <a
                href="{{ route('tasks.index', [], false) }}"
                class="chip @if($selectedProject === null || $selectedProject === '') active @endif"
                title="View all tasks from all projects"
            >
                All Projects <span class="chip-count">{{ $totalActiveTasks ?? ($projects->sum('tasks_count') + ($unassignedCount ?? 0)) }}</span>
            </a>

            @foreach($projects as $project)
                @php $isActiveFilter = ((string)$selectedProject === (string)$project->id); @endphp
                <a
                    href="{{ route('tasks.index', ['project' => $project->id], false) }}"
                    class="chip @if($isActiveFilter) active @endif"
                    title="Filter by {{ $project->name }}"
                >
                    📁 {{ $project->name }}
                    @if(!$project->is_active)
                        <span style="font-size: 10px; opacity: 0.85; margin-left: 2px;">(Inactive)</span>
                    @endif
                    <span class="chip-count">{{ $project->tasks_count }}</span>
                </a>
            @endforeach

            <a
                href="{{ route('tasks.index', ['project' => 'none'], false) }}"
                class="chip @if($isUnassignedFilter) active @endif"
                title="Filter tasks without an assigned project"
            >
                Unassigned <span class="chip-count">{{ $unassignedCount ?? 0 }}</span>
            </a>
        </div>

        {{-- Manage Projects & Status Table matching reference --}}
        <div style="border-top: 1px solid var(--border); padding-top: 16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <div style="font-size:13.5px;font-weight:700;color:var(--text-main);">
                    Manage Projects &amp; Status
                </div>
            </div>

            <div class="projects-table-wrapper">
                <table class="projects-table">
                    <thead>
                        <tr>
                            <th style="width: 50%;">PROJECT</th>
                            <th style="width: 15%; text-align: center;">TASKS</th>
                            <th style="width: 15%;">STATUS</th>
                            <th style="width: 20%; text-align: right;">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Unassigned (No Project) Row --}}
                        <tr @if($isUnassignedFilter) style="background: #F8F5FF;" @endif>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="font-size: 17px;">📄</span>
                                    <div>
                                        <div style="font-weight: 600; color: var(--text-main); font-size: 13px;">
                                            Unassigned Tasks
                                        </div>
                                        <div style="font-size: 11px; color: var(--text-muted);">
                                            Tasks without an assigned project
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span style="font-weight: 700; font-family: var(--font-sans); font-size: 13.5px; color: var(--text-main);">
                                    {{ $unassignedCount ?? 0 }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-status active">
                                    <span>●</span>
                                    <span>Active</span>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('tasks.index', ['project' => 'none'], false) }}" class="btn btn-secondary btn-sm" title="Filter unassigned tasks">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                    </svg>
                                    <span>Filter</span>
                                </a>
                            </td>
                        </tr>
                        @foreach($projects as $project)
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <span style="font-size: 18px;">📁</span>
                                        <div>
                                            <div style="font-weight: 600; color: var(--text-main); font-size: 13.5px;">
                                                {{ $project->name }}
                                            </div>
                                            <div style="font-size: 11.5px; color: var(--text-muted);">
                                                Created {{ $project->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span style="font-weight: 700; font-family: var(--font-sans); font-size: 14px; color: var(--text-main);">
                                        {{ $project->tasks_count }}
                                    </span>
                                </td>
                                <td>
                                    @if($project->is_active)
                                        <span class="badge-status active">
                                            <span>●</span>
                                            <span>Active</span>
                                        </span>
                                    @else
                                        <span class="badge-status inactive">
                                            <span>○</span>
                                            <span>Inactive</span>
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; align-items: center; gap: 6px;">
                                        {{-- Toggle Status Button --}}
                                        <form method="POST" action="{{ route('projects.toggle-status', $project, false) }}" style="margin:0;">
                                            @csrf
                                            @method('PATCH')
                                            @if($project->is_active)
                                                <button type="submit" class="btn btn-secondary btn-sm" title="Mark this project as inactive">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
                                                        <line x1="12" y1="2" x2="12" y2="12"></line>
                                                    </svg>
                                                    <span>Mark Inactive</span>
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-secondary btn-sm" title="Mark this project as active" style="color: var(--success); border-color: #86EFAC;">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
                                                        <line x1="12" y1="2" x2="12" y2="12"></line>
                                                    </svg>
                                                    <span>Mark Active</span>
                                                </button>
                                            @endif
                                        </form>

                                        {{-- Filter Button --}}
                                        <a href="{{ route('tasks.index', ['project' => $project->id], false) }}" class="btn btn-secondary btn-sm" title="Filter tasks for this project">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                            </svg>
                                            <span>Filter</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</section>

{{-- ====================================================
     EDIT TASK MODAL DIALOG
==================================================== --}}
<dialog id="edit-dialog">
    <div class="dialog-header">
        <h3 class="dialog-title">Edit Task</h3>
    </div>

    <form id="edit-form" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="filter_project" value="{{ $selectedProject }}">

        <div class="form-group">
            <label class="form-label" for="edit-name">Task Name</label>
            <input
                id="edit-name"
                name="name"
                type="text"
                class="form-input"
                style="padding-left: 14px;"
                maxlength="255"
                required
            >
        </div>

        <div class="form-group">
            <label class="form-label" for="edit-project">Project</label>
            <select id="edit-project" name="project_id" class="form-select" style="width: 100%; padding-left: 14px;">
                <option value="">No Project (General)</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" @disabled(!$project->is_active)>
                        {{ $project->name }}@if(!$project->is_active) (Inactive)@endif
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="edit-priority">Priority</label>
            <input
                id="edit-priority"
                name="priority"
                type="number"
                min="1"
                class="form-input"
                style="padding-left: 14px;"
            >
            <small style="color:var(--text-muted);font-size:12px;margin-top:4px;display:block;">
                #1 is the highest priority. You can also drag & drop in the list.
            </small>
        </div>

        <div class="dialog-footer">
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('edit-dialog').close()">
                Cancel
            </button>
            <button type="submit" class="btn btn-primary-purple">
                Save Changes
            </button>
        </div>
    </form>
</dialog>

@endsection

@push('scripts')
<script>
    function openEditModal(id, name, projectId, priority) {
        const dialog = document.getElementById('edit-dialog');
        const form = document.getElementById('edit-form');
        if (!dialog || !form) return;
        form.action = `/tasks/${id}`;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-project').value = projectId ?? '';
        document.getElementById('edit-priority').value = priority;
        dialog.showModal();
    }

    // Toggle project creation form
    function toggleProjectCreationForm() {
        const container = document.getElementById('project-create-container');
        if (container) {
            container.style.display = container.style.display === 'none' ? 'block' : 'none';
            const input = container.querySelector('input[name="name"]');
            if (input && container.style.display !== 'none') {
                input.focus();
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Close dialog when clicking on the backdrop
        const editDialog = document.getElementById('edit-dialog');
        if (editDialog) {
            editDialog.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.close();
                }
            });
        }

        // Form submission disabled state handling
        const createTaskForm = document.getElementById('create-task-form');
        if (createTaskForm) {
            createTaskForm.addEventListener('submit', function() {
                const btn = document.getElementById('btn-add-task');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span>Adding...</span>';
                }
            });
        }

        // Initialize SortableJS for Drag and Drop
        const list = document.getElementById('task-list');
        if (!list) return;

        function setupSortable() {
            if (typeof Sortable === 'undefined') {
                setTimeout(setupSortable, 80);
                return;
            }

            new Sortable(list, {
                handle: '.drag-handle',
                animation: 180,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: async function () {
                    // Collect task IDs in the newly arranged order
                    const taskItems = [...list.querySelectorAll('.task-item')];
                    const taskIds = taskItems.map(item => Number(item.dataset.id));

                    // Immediately update badge numbers in the DOM for instant visual feedback
                    taskItems.forEach((item, index) => {
                        const badge = item.querySelector('.priority-badge');
                        if (badge) {
                            const newPriority = index + 1;
                            const pLevel = Math.min(newPriority, 3);
                            const glyph = pLevel === 1 ? '🔥' : (pLevel === 2 ? '⚡' : (pLevel === 3 ? '⚑' : '●'));
                            badge.innerHTML = `<span class="icon-glyph">${glyph}</span> <span>PRIORITY #${newPriority}</span>`;
                            badge.title = `Priority #${newPriority}`;
                            badge.className = `priority-badge p-${pLevel}`;
                        }
                    });

                    try {
                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                        const response = await fetch(list.dataset.reorderUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfMeta ? csrfMeta.getAttribute('content') : ''
                            },
                            body: JSON.stringify({
                                task_ids: taskIds
                            })
                        });

                        if (!response.ok) {
                            throw new Error('Reorder failed');
                        }

                        if (typeof showToast === 'function') {
                            showToast('✓ Task priorities updated (#1 at top)');
                        }
                    } catch (error) {
                        if (typeof showToast === 'function') {
                            showToast('Failed to save priority order. Reloading...', true);
                        }
                        setTimeout(() => window.location.reload(), 1500);
                    }
                }
            });
        }

        setupSortable();
    });
</script>
@endpush
