# TaskMan

A task management application built with Laravel 10 and Filament 3.

## Features

- **Task Management**: Create, view, edit, and delete tasks with full CRUD operations
- **Role-Based Access Control**:
    - **Admin**: Full access to all tasks and user management
    - **Developer**: Can only view and update their assigned tasks
- **Status Workflow**: Tasks progress through statuses (Waiting → In Progress → Pending → Completed/Closed)
- **Severity Levels**: Tasks categorized by severity with color-coded badges
- **Real-time Validation**: Live form validation with dynamic field updates
- **Comment System**: Threaded comments on tasks for team collaboration
- **Database Seeding**: Pre-seeded master data (statuses, severities, users)
- **Responsive UI**: Mobile-friendly interface built with Tailwind CSS

## Tech Stack

- **Backend**: Laravel 10 (PHP 8.2)
- **Admin Panel**: Filament 3
- **Frontend**: Tailwind CSS 3, Alpine.js (via Livewire 3)
- **Database**: MySQL with Eloquent ORM
- **Testing**: PHPUnit 10
- **Development**: Laravel Herd for local serving

## Setup

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & NPM
- Laravel Herd (recommended) or any local PHP server

### Installation

1. **Clone the repository**

    ```bash
    git clone <repository-url>
    cd taskman-filament-app
    ```

2. **Install dependencies**

    ```bash
    composer install
    npm install
    ```

3. **Environment setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Database setup**

    **Option A: SQLite (Recommended for local development)**

    ```bash
    # Edit .env file
    DB_CONNECTION=sqlite
    # DB_DATABASE=database/database.sqlite (already set by default)

    # Create the database file
    touch database/database.sqlite

    php artisan migrate
    php artisan db:seed
    ```

    **Option B: MySQL/PostgreSQL**

    ```bash
    # Edit .env with your database credentials
    DB_CONNECTION=mysql
    DB_DATABASE=taskman
    DB_USERNAME=your_username
    DB_PASSWORD=your_password

    php artisan migrate
    php artisan db:seed
    ```

5. **Testing Environment (Optional)**

   To keep your development data separate from test data:

   ```bash
   # Copy .env to .env.testing
   cp .env .env.testing

   # Edit .env.testing - use SQLite for faster tests
   DB_CONNECTION=sqlite
   DB_DATABASE=:memory:

   # Or use separate test database
   # DB_DATABASE=taskman_testing
   ```

   With `DB_DATABASE=:memory:`, PHPUnit will use an in-memory SQLite database for tests - no file needed, faster execution.

6. **Email Configuration (Mailtrap)**

    For email testing during development, configure Mailtrap:

    ```bash
    # Edit .env file - replace with your Mailtrap credentials
    MAIL_MAILER=smtp
    MAIL_HOST=sandbox.smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=your_mailtrap_username
    MAIL_PASSWORD=your_mailtrap_password
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS="hello@taskman-app.test"
    MAIL_FROM_NAME="${APP_NAME}"
    ```

    Get your free Mailtrap account at [https://mailtrap.io](https://mailtrap.io)

7. **Real-time Updates with Reverb (Optional)**

    For real-time task updates and notifications:

    ```bash
    # Install Reverb
    composer require laravel/reverb

    # Publish config
    php artisan reverb:install

    # Edit .env - Reverb configuration
    REVERB_APP_ID=your_app_id
    REVERB_APP_KEY=your_app_key
    REVERB_APP_SECRET=your_app_secret
    VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
    VITE_REVERB_HOST="localhost"
    VITE_REVERB_PORT=8080
    VITE_REVERB_SCHEME="${REVERB_SCHEME:-http}"

    # Start the Reverb server
    php artisan reverb:start
    ```

    Run Reverb in a separate terminal while developing.

8. **Build assets**

    ```bash
    npm run build
    ```

9. **Serve the application**
    - With **Herd**: Automatically available at `https://taskman-app.test`
    - With **Artisan**: `php artisan serve`

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/TaskVisibilityTest.php

# Run with detailed output
php artisan test --compact
```

### Code Formatting

```bash
# Format code with Laravel Pint
vendor/bin/pint
```

## Architecture & Trade-offs

### Database Design

**Decision**: Separate `Status` and `Severity` master tables instead of PHP 8 enums.

**Trade-off**:

- ✅ Easy to manage via database, can store metadata (colors, sort order)
- ❌ Logic is code-defined anyway (status transitions), requires seeding, slower than enums

**Better Approach**: Use PHP 8 enums with Filament support - no seeding, type-safe, better performance. Only use DB tables for truly dynamic data like tags.

### Comment System Design

**Decision**: Nested/threaded comments with `parent_id` for hierarchical discussions.

**Trade-off**:

- ✅ Supports multi-level discussions in theory (current: reply-to-comment with 1-level nesting)
- ❌ Overkill for 1-on-1 conversations (admin ↔ developer), adds complexity

**Better Approach**: Flat comments with quote/reply instead of threading. Simpler than nested structure.

### Role-Based Access Control

**Decision**: Simple role-based access - Admin sees all tasks, Developer sees only assigned tasks. Implemented via query filtering in Filament table.

**Trade-off**:

- ✅ Simple for small apps - just 2 fixed roles, matches business logic
- ❌ Not scalable if roles/permissions grow

**True RBAC is overkill**: Granular permissions (can_create, can_delete) are enterprise-level complexity.

**Alternative**: Separate Admin/Developer panels for cleaner separation (more code duplication).
