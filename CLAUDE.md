# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 10 backend application for project management and activity tracking. The application is designed to manage projects, users, activities, and trackings with a hierarchical structure.

## Development Environment Setup

**IMPORTANT:** This repository is developed in WSL (Windows Subsystem for Linux), but the database and main development environment run on Windows. When running commands:
- Database operations should be executed from Windows (where MySQL is running)
- File operations can be done in WSL
- Tests should be run from Windows environment

### Database Configuration
- Connection: MySQL on Windows (`127.0.0.1:3306`)
- Database name: `villdingbackend`
- The database is already migrated - do NOT run migrations from WSL

## Common Commands

### Testing
```bash
# Run all tests (execute from Windows)
php artisan test

# Run specific test suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Run specific test class
php artisan test --filter=UserControllerTest
php artisan test --filter=UserEndpointTest

# Run single test method
php artisan test --filter=test_user_can_be_created_with_all_fields
```

### Development
```bash
# Start development server
php artisan serve

# Build frontend assets
npm run dev
npm run build

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## Core Architecture

### Data Model Hierarchy

The application follows a **Project → Tracking → Activity** hierarchy:

1. **Users** (`users` table)
   - Have unique `user_code` (format: 1 uppercase letter + 6 digits, e.g., A123456)
   - Can be associated with multiple projects via many-to-many relationship
   - Can be admins of specific projects (stored in `project_user.is_admin`)
   - No `remember_token` column in database

2. **Projects** (`projects` table)
   - Belong to a `ProjectType` and `ProjectSubtype`
   - Have many-to-many relationship with `Users` through `project_user` pivot table
   - Contain multiple `Trackings` and `Activities`
   - Store image keys in Amazon S3 (`projects/{filename}`)

3. **Trackings** (`trackings` table)
   - Belong to a `Project`
   - Use **SoftDeletes** (can be soft-deleted and restored)
   - Contain multiple `Activities`
   - Track project progress over time

4. **Activities** (`activities` table)
   - Belong to both a `Project` and a `Tracking`
   - Store up to 5 image keys in Amazon S3 (`activities/{filename}`)
   - Track individual tasks and their status

### Key Relationships

```
User ←→ Project (many-to-many via project_user, with is_admin pivot)
Project → Tracking (one-to-many)
Project → Activity (one-to-many)
Tracking → Activity (one-to-many)
Project → ProjectType (many-to-one)
Project → ProjectSubtype (many-to-one)
```

### Image Storage Structure

Images are stored in an Amazon S3 bucket using Laravel's filesystem abstraction:
- `profiles/{filename}` - User profile pictures
- `projects/{filename}` - Project images
- `activities/{filename}` - Activity images (stored as JSON arrays)

URLs are generated with `Storage::disk('s3')->url($path)` and returned directly to clients. No static routes serve local files anymore—ensure the bucket or CDN exposes public read access or signed URLs as needed. Environment configuration requires `FILESYSTEM_DISK=s3`, `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, and optional `AWS_URL`.

## API Endpoint Structure

All API endpoints are prefixed with `/endpoint/` and organized by resource:

### User Management (`/endpoint/user/`)
- Authentication: `create`, `login`, `email_exists`
- User codes: `generate-code`, `verify-code`, `show-codes`
- Admin management: `makeadmin`, `removeadmin`
- CRUD: `all`, `show`, `update`, `user_code`

### Project Management (`/endpoint/project/`)
- Types/Subtypes: `types`, `subtypes`, `type/store`, `subtype/store`
- CRUD: `store`, `update/{id}`, `destroy/{id}`
- Entities: `entities/create`, `entities/check/{project_id}`
- User attachment: `attach`, `detach`, `check-attachment`

### Tracking Management (`/endpoint/tracking*/`)
- Fetch: `trackingAll`, `trackingByProject`, `trackingByWeek*`
- Soft deletes: `trackingAllWithTrashed`, `trackingOnlyTrashed`
- CRUD: `create`, `update-title/{id}`, `delete/{id}`, `restore/{id}`, `force-delete/{id}`
- Time management: `getWeeksByProject`, `getDaysByWeek`

### Activity Management (`/endpoint/activities/`)
- Fetch: `all`, `project/{id}`, `tracking/{id}`
- CRUD: `create`, `{id}` (update), `_imgs/{id}` (with images), `_complete`, `_delete/{id}`

## Testing Guidelines

### UserFactory Configuration

The `UserFactory` is configured for the custom user schema (no `remember_token`). When creating test users, all fields are auto-populated including:
- Auto-generated unique `user_code`
- Default values for `is_paid_user`, `role`, `uri`
- Fields: `name`, `last_name`, `edad`, `genero`, `telefono`

### Test Database

Tests use `RefreshDatabase` trait to reset the database between tests. The phpunit.xml is configured to use the same MySQL database by default (SQLite in-memory is commented out).

### Soft Deletes in Tests

When testing `Tracking` models, remember they use SoftDeletes:
- Use `withTrashed()` to include soft-deleted records
- Use `onlyTrashed()` to get only soft-deleted records
- Test both `delete()` and `forceDelete()` operations

## Route Organization

Routes in `routes/web.php` are organized into clear sections with comments:
1. Main routes
2. User routes (basic, codes, admin, project attachment)
3. Project routes (types, subtypes, CRUD, entities, user attachment)
4. Tracking routes (fetch, CRUD, time management)
5. Activity routes (fetch, CRUD)

When adding new routes, follow this organizational pattern and add to the appropriate section.

## Session Management

The application uses Laravel's session-based authentication (not token-based):
- UserController uses `web` middleware for session handling
- User data is stored in session upon login: `session(['user' => $user])`
- Session can be retrieved via `/endpoint/user/getSession`

## Code Style Notes

- Controllers are namespaced by domain: `User`, `Projects`, `Trackings`
- Spanish is used in some field names and comments (e.g., `edad`, `genero`, `telefono`)
- Image uploads use `Storage::disk('s3')` for file handling (with fallback cleanup for legacy local files)
- User codes are generated with format validation: `/^[A-Z][0-9]{6}$/`
