# Taskboard · Laravel 11 Task Management Application

A clean, modern, and production-ready Task Management web application built the **Laravel way** with **PHP 8.3+**, **Laravel 11**, and **MySQL**. It features intuitive browser drag-and-drop task reordering with automatic priority recalculation, full project grouping, and project dropdown filtering.

---

## 🌟 Features

- **Create Tasks**: Save task name, project assignment, priority, and timestamps (`created_at`, `updated_at`).
- **Edit Tasks**: Update task names, move tasks between projects, or adjust priorities via a clean modal dialog.
- **Delete Tasks**: Remove tasks safely with automatic priority re-compaction to eliminate gaps.
- **Drag-and-Drop Priority Reordering**:
  - Drag tasks in the browser using the grip handle (`⠿`).
  - **#1 priority goes to the top**, #2 next down, and so on.
  - Priorities update in real-time in the browser and persist immediately to the MySQL database via an AJAX JSON endpoint protected by CSRF.
  - All priority updates are wrapped in atomic database transactions (`DB::transaction`).
- **Project Functionality (Bonus Point)**:
  - Select a project from the top dropdown to view only tasks associated with that project.
  - Option to view unassigned tasks or all tasks.
  - Create new projects directly from the interface.
  - Project chips with live task counters and quick-filter navigation.
  - Foreign key constraint configured with `nullOnDelete()` so deleting a project preserves tasks safely as unassigned.
- **Built the "Laravel Way"**:
  - **Form Requests**: Dedicated request validation classes (`StoreTaskRequest`, `UpdateTaskRequest`, `ReorderTasksRequest`, `StoreProjectRequest`).
  - **Eloquent Models & Scopes**: Model relationships (`Project hasMany Task`, `Task belongsTo Project`), query scopes (`ordered()`, `forProject()`), and cast methods.
  - **Route Model Binding**: Automatic resolution of models in controller actions.
  - **Database Migrations & Indexes**: MySQL-optimized schemas with foreign key constraints and compound indexes `(project_id, priority)`.
  - **Database Seeders & Factories**: Model factories and `DatabaseSeeder` pre-populated with realistic tasks and projects.
  - **Comprehensive Automated Tests**: 11 feature tests covering creation, validation, filtering, updates, deletion, and drag-and-drop reordering.
- **Zero-Build Responsive UI**:
  - Responsive Blade layout powered by modern CSS variables and flexbox.
  - SortableJS bundled locally (`public/vendor/sortable.min.js`) for 100% offline functionality, with CDN fallback.

---

## 📋 System Requirements

- **PHP**: `^8.3` or `^8.4` (PHP 8.3+ required)
- **Composer**: `^2.2`
- **MySQL**: `^8.0` or compatible MariaDB server (e.g. XAMPP MySQL)
- **PHP Extensions**:
  - `pdo_mysql`
  - `mbstring`
  - `openssl`
  - `curl`
  - `fileinfo`
  - `tokenizer`
  - `xml` / `dom`
  - `filter`
  - `session`
  - `ctype`

---

## 🚀 Local Development Setup

Follow these step-by-step instructions to set up and run the application locally:

### 1. Extract / Navigate to Project Directory
Open your terminal (PowerShell, Command Prompt, or Bash) and navigate to the project directory:
```bash
cd path/to/task-manager
```

### 2. Install Composer Dependencies
```bash
composer install
```

### 3. Set Up Environment File
Duplicate the `.env.example` file to create your local `.env`:

**Linux / macOS:**
```bash
cp .env.example .env
```

**Windows (PowerShell or CMD):**
```powershell
copy .env.example .env
```

Generate the unique application key:
```bash
php artisan key:generate
```

### 4. Configure MySQL Database
Open `.env` in your text editor and verify or update your MySQL connection details:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=root
DB_PASSWORD=
```

Create the MySQL database if it doesn't already exist. You can do this via MySQL CLI, phpMyAdmin, or MySQL Workbench:
```sql
CREATE DATABASE task_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Run Migrations & Seed Sample Data
Execute the migrations to create the `projects` and `tasks` tables, and seed realistic demo projects and tasks:
```bash
php artisan migrate --seed
```

> **Tip:** If you ever want a fresh database with demo data, run:
> ```bash
> php artisan migrate:fresh --seed
> ```

### 6. Start the Local Server
```bash
php artisan serve
```

### 7. Open the Application
Navigate to [http://127.0.0.1:8000](http://127.0.0.1:8000) in your web browser.

---

## 🧪 Running Automated Tests

The application includes a comprehensive test suite in `tests/Feature/TaskManagementTest.php`. The test environment uses an in-memory SQLite database configured in `phpunit.xml`, meaning tests run in milliseconds without affecting your MySQL development database.

Run the test suite:
```bash
php artisan test
```
Or directly with PHPUnit:
```bash
./vendor/bin/phpunit
```

All 11 tests will execute and pass:
- `test_tasks_page_renders_successfully`
- `test_task_can_be_created_with_priority_and_timestamps`
- `test_creating_multiple_tasks_increments_priority`
- `test_task_name_is_required`
- `test_tasks_can_be_filtered_by_project_dropdown`
- `test_tasks_filter_by_unassigned`
- `test_task_can_be_updated`
- `test_task_can_be_deleted`
- `test_tasks_reorder_via_drag_and_drop_endpoint_updates_priorities`
- `test_project_can_be_created`
- `test_project_name_must_be_unique`

---

## 📖 How to Use the Application

1. **Add a Task**:
   - Type your task description into the "What needs to be done?" field.
   - Optionally select a project from the dropdown (or leave as "No Project").
   - Click **+ Add Task**. The task will automatically be assigned the next sequential priority.
2. **Filter Tasks by Project**:
   - Select any project from the **Filter Project** dropdown at the top right of the task card.
   - The view instantly refreshes to show only tasks belonging to that project.
   - Click **Clear** or choose **All Projects** to view all tasks again.
   - You can also click any of the project chips in the Project Management section.
3. **Reorder Tasks (Drag & Drop)**:
   - Click and hold the grip icon (`⠿`) on any task item.
   - Drag the item up or down to your desired position and release.
   - Priority numbers (#1, #2, #3...) will immediately re-number in the browser.
   - An asynchronous request automatically persists the new priorities to the MySQL database.
4. **Edit a Task**:
   - Click the **Edit** button on any task.
   - In the modal dialog, modify the task name, change its assigned project, or manually set a priority.
   - Click **Save Changes**.
5. **Delete a Task**:
   - Click the **Delete** button and confirm the browser alert.
   - The task will be deleted, and remaining task priorities will automatically be re-compacted.
6. **Create a Project**:
   - Scroll to the Project Management card, enter a project name, and click **+ Create Project**.
   - The project is created and the page immediately filters to your new project.

---

## 🌐 Production Deployment Guide

To deploy this application to a production server (Ubuntu/Debian with Nginx or Apache):

### 1. Server Prerequisites
Install PHP 8.3+, Composer, MySQL, and Nginx/Apache:
```bash
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-fpm php8.3-mysql php8.3-mbstring \
    php8.3-xml php8.3-curl php8.3-zip unzip nginx mysql-server
```

### 2. Deploy Application Files
Clone or upload the project files to your server directory (e.g. `/var/www/task-manager`):
```bash
cd /var/www/task-manager
```

### 3. Install Production Dependencies
Run Composer with flags optimized for production:
```bash
composer install --no-dev --optimize-autoloader
```

### 4. Configure Production Environment
Copy and customize `.env`:
```bash
cp .env.example .env
php artisan key:generate
```
Edit `.env` for production:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager_prod
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password
```

### 5. Set File Permissions
Ensure the web server user (`www-data`) owns the storage and bootstrap cache directories:
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 6. Run Migrations & Cache Configuration
```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7. Configure Nginx Web Server
Create `/etc/nginx/sites-available/task-manager`:
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/task-manager/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
Enable the site and reload Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/task-manager /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 📁 Project Architecture

```
task-manager/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php
│   │   │   ├── ProjectController.php     # Project store & delete actions
│   │   │   └── TaskController.php        # Task CRUD & AJAX reorder logic
│   │   └── Requests/
│   │       ├── ReorderTasksRequest.php   # Reorder validation (task_ids array)
│   │       ├── StoreProjectRequest.php   # Project store validation (name unique)
│   │       ├── StoreTaskRequest.php      # Task store validation
│   │       └── UpdateTaskRequest.php     # Task update validation
│   ├── Models/
│   │   ├── Project.php                   # HasMany Task relationship
│   │   └── Task.php                      # BelongsTo Project, scopes, casts
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/
│   ├── app.php                           # Laravel 11 application bootstrap
│   └── providers.php
├── config/                               # Application configurations
├── database/
│   ├── factories/
│   │   ├── ProjectFactory.php            # Project model factory
│   │   └── TaskFactory.php               # Task model factory
│   ├── migrations/
│   │   ├── 2026_11_09_000001_create_projects_table.php
│   │   └── 2026_11_09_000002_create_tasks_table.php
│   └── seeders/
│       └── DatabaseSeeder.php            # Sample data seeder
├── public/
│   ├── index.php                         # HTTP front controller
│   └── vendor/
│       └── sortable.min.js               # Bundled SortableJS (offline drag & drop)
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php             # Main HTML shell, responsive styling, toast
│       └── tasks/
│           └── index.blade.php           # Taskboard UI, filter, drag-and-drop
├── routes/
│   ├── console.php
│   └── web.php                           # RESTful web routes
├── tests/
│   ├── Feature/
│   │   └── TaskManagementTest.php        # 11 automated feature tests
│   └── TestCase.php                      # Base test case
├── composer.json                         # PHP 8.3+ and Laravel 11 definitions
├── phpunit.xml                           # PHPUnit test configuration
└── README.md                             # Setup and deployment documentation
```

---

## 📄 License
This project is open-source software licensed under the [MIT license](LICENSE).
