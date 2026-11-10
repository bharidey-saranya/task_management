<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Taskboard · Modern SaaS Task Manager')</title>

    <!-- Google Fonts: Inter & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --font-sans: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-mono: 'JetBrains Mono', monospace;

            /* Design Tokens from User Specification */
            --primary: #5B35E8;
            --primary-hover: #4d2ac7;
            --primary-gradient: linear-gradient(135deg, #5B35E8 0%, #7C4DFF 100%);
            --primary-gradient-hover: linear-gradient(135deg, #4d2ac7 0%, #6b3ee3 100%);
            --primary-subtle: #F3F0FF;
            --primary-text: #5B35E8;

            --bg: #F6F7FF;
            --surface: #FFFFFF;
            --surface-hover: #FAFBFF;
            --border: #E7E8F2;
            --border-focus: #5B35E8;

            --text-main: #15152E;
            --text-muted: #68738A;
            --text-subtle: #9EA8BD;

            --success: #16A34A;
            --success-subtle: #ECFDF5;
            --success-border: #A7F3D0;
            --success-text: #065F46;

            --danger: #EF4444;
            --danger-subtle: #FFF1F2;
            --danger-border: #FECDD3;
            --danger-text: #BE123C;

            --warning: #F59E0B;
            --warning-subtle: #FFFBEB;
            --warning-border: #FDE68A;
            --warning-text: #B45309;

            --info: #2563EB;
            --info-subtle: #EFF6FF;
            --info-border: #BFDBFE;
            --info-text: #1D4ED8;

            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;

            --shadow-xs: 0 1px 2px rgba(21, 21, 46, 0.04);
            --shadow-sm: 0 2px 6px -1px rgba(21, 21, 46, 0.05), 0 1px 3px rgba(21, 21, 46, 0.03);
            --shadow-md: 0 6px 18px -3px rgba(91, 53, 232, 0.06), 0 2px 6px -1px rgba(21, 21, 46, 0.04);
            --shadow-lg: 0 14px 30px -4px rgba(91, 53, 232, 0.08), 0 4px 10px -2px rgba(21, 21, 46, 0.03);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--bg);
            background-image: 
                radial-gradient(circle at 10% 10%, rgba(91, 53, 232, 0.04) 0%, transparent 40%),
                radial-gradient(circle at 90% 90%, rgba(124, 77, 255, 0.04) 0%, transparent 45%);
            background-attachment: fixed;
            color: var(--text-main);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 16px 20px 40px;
            flex: 1;
        }

        /* ----------------------------------------------------
           A. MAIN HORIZONTAL HEADER (NO SIDEBAR)
        ---------------------------------------------------- */
        header.app-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding: 4px 0;
            gap: 16px;
        }

        .brand-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            background: var(--primary-gradient);
            border-radius: 10px;
            display: grid;
            place-items: center;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(91, 53, 232, 0.28);
            flex-shrink: 0;
        }

        .brand-icon svg {
            width: 20px;
            height: 20px;
            stroke-width: 2.5;
        }

        .brand-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.4px;
            line-height: 1.2;
        }

        .brand-tagline {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Search input with shortcut */
        .search-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-box input {
            height: 36px;
            width: 240px;
            padding: 0 38px 0 36px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 9999px;
            font-family: inherit;
            font-size: 12.5px;
            color: var(--text-main);
            transition: all 0.2s ease;
            box-shadow: var(--shadow-xs);
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(91, 53, 232, 0.12);
            width: 280px;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            color: var(--text-subtle);
            pointer-events: none;
            display: flex;
        }

        .search-shortcut {
            position: absolute;
            right: 10px;
            padding: 1px 6px;
            background: #F1F3F9;
            border: 1px solid var(--border);
            border-radius: 5px;
            font-size: 10.5px;
            font-weight: 600;
            color: var(--text-muted);
            font-family: var(--font-mono);
            pointer-events: none;
        }

        /* User profile chip */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 3px 10px 3px 4px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 9999px;
            cursor: pointer;
            box-shadow: var(--shadow-xs);
            transition: all 0.15s ease;
        }

        .user-profile:hover {
            border-color: #D1D5E5;
            background: #FAFBFF;
        }

        .user-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--primary-gradient);
            color: #ffffff;
            font-size: 11.5px;
            font-weight: 700;
            display: grid;
            place-items: center;
        }

        .user-name {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-main);
        }

        /* ----------------------------------------------------
           B. DASHBOARD STATISTICS (4 CARDS GRID)
        ---------------------------------------------------- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }

        .stat-card {
            border-radius: 12px;
            padding: 14px 18px;
            position: relative;
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            min-height: 72px;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .stat-card-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stat-card.stat-total {
            background: linear-gradient(135deg, #F0F4FF 0%, #E8EDFF 100%);
            border: 1px solid #D9E2FF;
        }

        .stat-card.stat-completed {
            background: linear-gradient(135deg, #ECFDF5 0%, #DCFCE7 100%);
            border: 1px solid #BBF7D0;
        }

        .stat-card.stat-inprogress {
            background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%);
            border: 1px solid #FDE68A;
        }

        .stat-card.stat-highpriority {
            background: linear-gradient(135deg, #FFF1F2 0%, #FFE4E6 100%);
            border: 1px solid #FECDD3;
        }

        .stat-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .stat-total .stat-icon-box {
            background: #FFFFFF;
            color: #4F46E5;
            box-shadow: 0 2px 5px rgba(79, 70, 229, 0.15);
        }

        .stat-completed .stat-icon-box {
            background: #FFFFFF;
            color: #10B981;
            box-shadow: 0 2px 5px rgba(16, 185, 129, 0.15);
        }

        .stat-inprogress .stat-icon-box {
            background: #FFFFFF;
            color: #F59E0B;
            box-shadow: 0 2px 5px rgba(245, 158, 11, 0.15);
        }

        .stat-highpriority .stat-icon-box {
            background: #FFFFFF;
            color: #EF4444;
            box-shadow: 0 2px 5px rgba(239, 68, 68, 0.15);
        }

        .stat-value {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.5px;
            line-height: 1;
            font-family: var(--font-sans);
            text-align: right;
            margin-left: 12px;
        }

        .stat-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin: 0;
            line-height: 1.2;
        }

        /* ----------------------------------------------------
           C. CARDS & GENERAL SURFACES
        ---------------------------------------------------- */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 16px;
            box-shadow: var(--shadow-sm);
            position: relative;
        }

        .card-header-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .card-title-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--primary-subtle);
            color: var(--primary);
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .card-title {
            font-size: 15.5px;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.2px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .badge-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1px 7px;
            background: #ECE8FF;
            color: #5B35E8;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
        }

        .card-subtitle {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .card-helper-text {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ----------------------------------------------------
           D. BUTTONS & FORM INPUTS
        ---------------------------------------------------- */
        .btn {
            height: 38px;
            padding: 0 16px;
            border-radius: 8px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.15s ease;
            text-decoration: none;
            white-space: nowrap;
            border: none;
        }

        .btn-primary-purple {
            background: var(--primary-gradient);
            color: #ffffff;
            box-shadow: 0 3px 10px rgba(91, 53, 232, 0.22);
        }

        .btn-primary-purple:hover {
            background: var(--primary-gradient-hover);
            box-shadow: 0 5px 14px rgba(91, 53, 232, 0.32);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #ffffff;
            color: var(--text-main);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-xs);
        }

        .btn-secondary:hover {
            border-color: #D1D5E5;
            background: #FAFBFF;
        }

        .btn-ghost {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border);
        }

        .btn-ghost:hover {
            background: #F8FAFC;
            color: var(--text-main);
            border-color: #CBD5E1;
        }

        .btn-danger {
            background: #ffffff;
            color: #EF4444;
            border: 1px solid #FECDD3;
        }

        .btn-danger:hover {
            background: #FFF1F2;
            border-color: #F87171;
            color: #DC2626;
        }

        .btn-sm {
            height: 30px;
            padding: 0 10px;
            font-size: 12px;
            border-radius: 6px;
        }

        .form-row {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .form-control-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            flex: 1;
        }

        .input-icon-left {
            position: absolute;
            left: 12px;
            color: var(--text-subtle);
            pointer-events: none;
            display: flex;
        }

        .form-input {
            width: 100%;
            height: 38px;
            padding: 0 14px 0 38px;
            background: #FAFAFD;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: inherit;
            font-size: 13px;
            color: var(--text-main);
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            background: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(91, 53, 232, 0.12);
        }

        .form-select {
            height: 38px;
            padding: 0 32px 0 34px;
            background: #FAFAFD url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2368738A' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") no-repeat right 10px center;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-main);
            appearance: none;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 180px;
        }

        .form-select:focus {
            outline: none;
            background-color: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(91, 53, 232, 0.12);
        }

        .select-icon-left {
            position: absolute;
            left: 11px;
            color: var(--text-subtle);
            pointer-events: none;
            display: flex;
        }

        /* ----------------------------------------------------
           E. ALERTS & NOTIFICATIONS
        ---------------------------------------------------- */
        .alert-success-banner {
            background: #ECFDF5;
            border: 1px solid #A7F3D0;
            color: #065F46;
            border-radius: var(--radius-md);
            padding: 12px 18px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13.5px;
            font-weight: 500;
            box-shadow: var(--shadow-xs);
            animation: slideDown 0.25s ease-out;
        }

        .alert-danger-banner {
            background: #FFF1F2;
            border: 1px solid #FECDD3;
            color: #9F1239;
            border-radius: var(--radius-md);
            padding: 14px 18px;
            margin-bottom: 20px;
            font-size: 13.5px;
            box-shadow: var(--shadow-xs);
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ----------------------------------------------------
           F. TASK LIST & TASK ITEMS (WITH SCROLL BAR)
        ---------------------------------------------------- */
        .task-list {
            list-style: none;
            margin: 0;
            padding: 0;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            background: #ffffff;
            max-height: 420px;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-color: #C4B5FD #F8FAFD;
        }

        .task-list::-webkit-scrollbar {
            width: 7px;
        }

        .task-list::-webkit-scrollbar-track {
            background: #F8FAFD;
            border-radius: 4px;
        }

        .task-list::-webkit-scrollbar-thumb {
            background: #C4B5FD;
            border-radius: 4px;
        }

        .task-list::-webkit-scrollbar-thumb:hover {
            background: #8B5CF6;
        }

        .task-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px;
            border-bottom: 1px solid var(--border);
            background: #ffffff;
            transition: background-color 0.15s ease, transform 0.15s ease;
        }

        .task-item:last-child {
            border-bottom: none;
        }

        .task-item:hover {
            background-color: #FAFAFD;
        }

        .drag-handle {
            cursor: grab;
            color: #B4BCCB;
            font-size: 15px;
            padding: 2px 4px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            user-select: none;
            transition: color 0.15s ease, background 0.15s ease;
            touch-action: none;
        }

        .drag-handle:hover {
            color: var(--primary);
            background: #ECE8FF;
        }

        .drag-handle:active {
            cursor: grabbing;
        }

        /* PRIORITY BADGES FROM SCREENSHOT */
        .priority-badge {
            font-family: var(--font-sans);
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
            letter-spacing: 0.3px;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        .priority-badge .icon-glyph {
            font-size: 11px;
            line-height: 1;
        }

        /* Priority #1: Red/Pink (Flame) */
        .priority-badge.p-1 {
            background: #FFF1F2;
            color: #E11D48;
            border: 1px solid #FFE4E6;
        }

        /* Priority #2: Orange/Yellow (Lightning) */
        .priority-badge.p-2 {
            background: #FFFBEB;
            color: #D97706;
            border: 1px solid #FDE68A;
        }

        /* Priority #3: Blue (Flag) */
        .priority-badge.p-3 {
            background: #EFF6FF;
            color: #2563EB;
            border: 1px solid #BFDBFE;
        }

        /* Priority #4+ (General) */
        .priority-badge.p-general {
            background: #F8FAFC;
            color: #64748B;
            border: 1px solid #E2E8F0;
        }

        .task-content {
            flex: 1;
            min-width: 0;
        }

        .task-name {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-main);
            line-height: 1.35;
            margin-bottom: 2px;
        }

        .task-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            color: var(--text-muted);
            flex-wrap: wrap;
        }

        .project-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            background: #F3F0FF;
            color: #6D28D9;
            border: 1px solid #E9D5FF;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .project-tag:hover {
            background: #ECE8FF;
            color: #5B21B6;
        }

        .project-tag.none {
            background: #F1F5F9;
            color: #64748B;
            border-color: #E2E8F0;
        }

        .task-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .sortable-ghost {
            opacity: 0.35;
            background: #ECE8FF !important;
            border-color: var(--primary) !important;
        }

        .sortable-chosen {
            background: #FAFAFD !important;
            box-shadow: var(--shadow-md);
        }

        /* ----------------------------------------------------
           G. PROJECT MANAGEMENT TABLE & STATUS (COMPACT & SCROLLABLE)
        ---------------------------------------------------- */
        .projects-table-wrapper {
            width: 100%;
            max-height: 320px;
            overflow-y: auto;
            overflow-x: auto;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            scrollbar-width: thin;
            scrollbar-color: #C4B5FD #F8FAFD;
        }

        .projects-table-wrapper::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .projects-table-wrapper::-webkit-scrollbar-track {
            background: #F8FAFD;
        }

        .projects-table-wrapper::-webkit-scrollbar-thumb {
            background: #C4B5FD;
            border-radius: 3px;
        }

        .projects-table-wrapper::-webkit-scrollbar-thumb:hover {
            background: #8B5CF6;
        }

        .projects-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        .projects-table th {
            text-align: left;
            padding: 8px 12px;
            color: var(--text-muted);
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border);
            background: #FAFAFD;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .projects-table td {
            padding: 8px 12px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            font-size: 12px;
        }

        .projects-table tr:last-child td {
            border-bottom: none;
        }

        .projects-table tr:hover td {
            background: #FAFBFF;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-status.active {
            background: #DCFCE7;
            color: #15803D;
            border: 1px solid #86EFAC;
        }

        .badge-status.inactive {
            background: #F1F5F9;
            color: #64748B;
            border: 1px solid #CBD5E1;
        }

        .chips-container {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 10px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 9999px;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--text-main);
            text-decoration: none;
            transition: all 0.15s ease;
            box-shadow: var(--shadow-xs);
        }

        .chip:hover {
            border-color: #D1D5E5;
            background: #FAFBFF;
        }

        .chip.active {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(91, 53, 232, 0.3);
        }

        .chip-count {
            display: inline-block;
            padding: 1px 7px;
            font-size: 10.5px;
            font-weight: 700;
            border-radius: 9999px;
            background: #F1F3F9;
            color: var(--text-muted);
        }

        .chip.active .chip-count {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        /* ----------------------------------------------------
           H. NATIVE DIALOG MODAL & TOAST
        ---------------------------------------------------- */
        dialog#edit-dialog {
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 26px;
            width: 90%;
            max-width: 520px;
            background: var(--surface);
            box-shadow: var(--shadow-lg);
            margin: auto;
        }

        dialog::backdrop {
            background: rgba(21, 21, 46, 0.45);
            backdrop-filter: blur(4px);
        }

        .dialog-header {
            margin-bottom: 20px;
        }

        .dialog-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-main);
        }

        .dialog-footer {
            margin-top: 24px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        #toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #15152E;
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.25);
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 9999;
            pointer-events: none;
        }

        #toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        footer.app-footer {
            text-align: center;
            padding: 28px 20px;
            font-size: 12.5px;
            color: var(--text-muted);
            margin-top: auto;
        }

        /* ----------------------------------------------------
           I. RESPONSIVE MEDIA QUERIES
        ---------------------------------------------------- */
        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .container { padding: 16px 14px 40px; }
            header.app-header { flex-direction: column; align-items: stretch; gap: 14px; }
            .header-actions { justify-content: space-between; flex-wrap: wrap; }
            .search-box input { width: 100%; }
            .search-box input:focus { width: 100%; }
            .stats-grid { grid-template-columns: 1fr; }
            .card { padding: 18px 16px; }
            .form-row.stack-sm { flex-direction: column; align-items: stretch; }
            .form-row.stack-sm .btn, .form-row.stack-sm .form-select, .form-row.stack-sm .form-input { width: 100%; }
            .task-item { flex-direction: column; align-items: flex-start; gap: 12px; }
            .task-actions { width: 100%; justify-content: flex-end; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- A. MAIN HEADER (NO SIDEBAR) -->
        <header class="app-header">
            <div class="brand-group">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <div>
                    <h1 class="brand-title">Taskboard</h1>
                    <div class="brand-tagline">Drag & drop task priority management with project filtering</div>
                </div>
            </div>

            <div class="header-actions">
                <!-- Search bar with keyboard shortcut -->
                <div class="search-box">
                    <span class="search-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>
                    <input
                        type="text"
                        id="global-task-search"
                        placeholder="Search tasks or projects..."
                        aria-label="Search tasks or projects"
                    >
                    <span class="search-shortcut">⌘ K</span>
                </div>

                <!-- User profile avatar (Saranya Bharidey) -->
                <div class="user-profile" title="User Profile: Saranya Bharidey">
                    <div class="user-avatar">SB</div>
                    <span class="user-name">Saranya Bharidey</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#68738A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </div>
            </div>
        </header>

        <!-- C. SUCCESS NOTIFICATION BANNER -->
        @if(session('success'))
            <div class="alert-success-banner" id="session-success-alert">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="document.getElementById('session-success-alert').remove()" style="background:none;border:none;cursor:pointer;color:inherit;font-size:18px;display:flex;">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert-danger-banner">
                <div style="display:flex;align-items:center;gap:8px;font-weight:700;margin-bottom:4px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span>Please correct the errors below:</span>
                </div>
                <ul style="margin: 6px 0 0 26px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- MAIN BODY CONTENT -->
        @yield('content')
    </div>

    <!-- Toast notification -->
    <div id="toast">
        <span id="toast-icon">✓</span>
        <span id="toast-message">Task priorities saved</span>
    </div>

    <footer class="app-footer">
        Taskboard &middot; Premium Drag &amp; Drop Priority Management &middot; {{ date('Y') }}
    </footer>

    <script>
        function showToast(message, isError = false) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            const toastIcon = document.getElementById('toast-icon');

            toastMessage.textContent = message;
            toastIcon.textContent = isError ? '⚠' : '✓';
            toast.style.backgroundColor = isError ? '#BE123C' : '#15152E';
            toast.classList.add('show');

            clearTimeout(window.__toastTimeout);
            window.__toastTimeout = setTimeout(() => {
                toast.classList.remove('show');
            }, 2500);
        }

        // Global Search (Cmd+K / Ctrl+K)
        document.addEventListener('keydown', function(e) {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                const search = document.getElementById('global-task-search');
                if (search) search.focus();
            }
        });

        // Interactive live search filter across task titles
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('global-task-search');
            if (!searchInput) return;

            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const taskItems = document.querySelectorAll('.task-item');
                taskItems.forEach(item => {
                    const name = item.querySelector('.task-name')?.textContent.toLowerCase() || '';
                    const project = item.querySelector('.project-tag')?.textContent.toLowerCase() || '';
                    if (!query || name.includes(query) || project.includes(query)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    </script>

    <!-- SortableJS -->
    <script src="{{ asset('vendor/sortable.min.js') }}" onerror="this.onerror=null;this.src='https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js'"></script>

    @stack('scripts')
</body>
</html>
