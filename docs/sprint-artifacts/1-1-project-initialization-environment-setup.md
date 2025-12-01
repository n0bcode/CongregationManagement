# Story 1.1: Project Initialization & Environment Setup

**Status:** ready-for-dev

## User Story

As a **Developer**,
I want to **initialize the Laravel project and set up a local Docker environment**,
so that **I can begin building the application consistently.**

## Acceptance Criteria

1.  **Project Created:** A new Laravel project is created using the `laravel/laravel` starter.
2.  **Breeze Installed:** Laravel Breeze is installed with the **Blade** stack (Blade + Tailwind + Alpine).
3.  **Docker Environment:** `laravel/sail` is configured and running.
4.  **Database Connection:** The application connects successfully to the MySQL database in the Docker container.
5.  **Mailpit:** Mailpit is running and accessible for email testing.
6.  **Frontend Build:** `npm install` and `npm run dev` work correctly, compiling Tailwind assets.
7.  **Git:** The project is initialized as a git repository (if not already) and `.gitignore` is correctly configured.

## Technical Implementation Guide

### 1. Project Initialization

Follow the **Architecture Document** exactly. Run the following commands:

```bash
# Create the project (adjust 'managing-congregation' to '.' if installing in current root,
# but be careful of existing files. Arch doc suggests a subfolder or clean root)
composer create-project laravel/laravel managing-congregation

# Navigate into directory
cd managing-congregation

# Install Breeze (Dev Dependency)
composer require laravel/breeze --dev

# Install Breeze with Blade stack (No React/Vue)
php artisan breeze:install blade
```

### 2. Docker Setup (Sail)

Ensure Sail is installed (it comes with Laravel).

```bash
# Start the environment
./vendor/bin/sail up -d
```

### 3. Verification

- Access the app at `http://localhost`.
- Verify Login/Register pages exist (provided by Breeze).
- Verify Mailpit at `http://localhost:8025`.

## Architecture & Constraints

- **Framework:** Laravel 11.x
- **PHP Version:** 8.2+
- **Database:** MySQL 8.0
- **Frontend:** Blade Templates + Tailwind CSS + Alpine.js
- **Strictness:** Do **NOT** install Jetstream, Livewire (yet), or Inertia. Stick to **Breeze + Blade** as per `architecture.md`.

## Dev Notes

- **Directory Structure:** If you are running this in an existing repo with `docs/` folder, you might need to create the project in a subfolder `managing-congregation` OR move the docs into the new project structure. **Decision:** Follow `architecture.md` which implies `managing-congregation` folder.
- **Database Config:** Ensure `.env` is configured to use `mysql` host (Sail configures this automatically).

## References

- [Architecture Document: Starter Selection](file:///media/truc2tz/SantaSSD/SKS/Sources/repos/aistudio/System_Blood_Group/docs/architecture.md#selected-starter-laravel-breeze-blade-stack)
- [Epics: Story 1.1](file:///media/truc2tz/SantaSSD/SKS/Sources/repos/aistudio/System_Blood_Group/docs/epics.md#story-11-project-initialization-environment-setup)
