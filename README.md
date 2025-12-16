# Managing the Congregation (MTC)

> A comprehensive management system designed for religious congregations to manage members, financials, documents, and community life efficiently and securely.

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)
![Livewire](https://img.shields.io/badge/Livewire-3.x-4E56A6?style=for-the-badge&logo=livewire)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker)

## 📖 Overview

**Managing the Congregation** is a specialized ERP-like solution tailored for the unique needs of religious orders. Built using the BMAD (Business Management and Development) methodology, it moves beyond simple record-keeping to provide a holistic view of community life, ensuring data integrity, privacy, and ease of administration.

### What Makes This Special

Unlike generic HR or CRM tools, this system is **purpose-built for religious life**, natively handling unique workflows such as:

- **Formation Lifecycle Tracking:** Seamlessly managing the progression from Postulancy to Perpetual Vows.
- **Community-Centric Logic:** Handling annual assignments, transfers, and community-level financial reporting.
- **Long-Term Stewardship:** Prioritizing the preservation of historical records and "lifelong" member data.
- **Global Accessibility:** Designed for ease of use in regions with varying technical infrastructure, such as Africa.

### Project Status

- **Current Phase**: Implementation (Phase 4 of BMAD Methodology)
- **Completed Epics**: 2 out of 5 (Foundation, Member Management, Financial Management partially complete)
- **Test Coverage**: 373 tests passing (100% success rate)
- **Architecture**: Complete and validated

## ✨ Key Features

### 👤 Member Management

- **Comprehensive Profiles**: Personal info, religious name, dates, biography, and sacramental records.
- **Visual Timeline**: Interactive history of formation stages, assignments, and transfers.
- **Formation Tracking**: Complete lifecycle management (Postulancy → Novitiate → First Vows → Final Vows).
- **Document Management**: Secure storage for Baptismal Certificates, Health Reports, and legal documents.
- **Passport & ID Management**: Track passport details, expiry dates, and scanned copies.
- **Search & Filtering**: Advanced search with scope-based access control.
- **Photo Management**: Profile photos with cropping and secure storage.

### 💰 Financial Management

- **Daily Expense Entry**: Simple expense tracking with category, amount, and description.
- **Receipt Upload**: Secure attachment of digital receipts for audit trails.
- **Monthly PDF Reports**: One-click generation of standardized financial reports.
- **Community Scoping**: Directors see only their community's finances.
- **Approval Workflows**: Digital approval process for community expenses.
- **Fiscal Period Locking**: Prevention of modifications to closed financial periods.

### 🏛️ Community & Governance

- **Multi-level Hierarchy**: Support for Generalate, Provincial, and Local Community levels.
- **Member Transfers**: Complete transfer history with service records.
- **House Management**: Creation and management of communities/houses.
- **Assignment Tracking**: Historical record of all member assignments and roles.

### 🛡️ Security & Access Control (RBAC)

- **Granular Permissions**: 50+ specific permissions with type-safe implementation.
- **Role-Based Access**: Pre-configured roles (Super Admin, General, Director, Member).
- **Data Scoping**: Users only see data relevant to their community or assignment.
- **Audit Logging**: Comprehensive tracking of all critical system actions.
- **Super Admin Bypass**: Administrative override for system management.

### 📊 Reports & Analytics

- **Executive Dashboard**: High-level overview for Generalate leadership.
- **Demographic Analysis**: Statistics on age, formation stage, skills, and community composition.
- **Financial Overview**: Real-time view of community spending and budgets.
- **Critical Alerts**: Automated notifications for vow expirations and important dates.
- **Custom Reporting**: Export capabilities for presentations and external reporting.

### 🔧 Advanced Features

- **BMAD Methodology**: Built using Business Management and Development workflow system.
- **Docker Containerization**: Complete development and production environments.
- **Comprehensive Testing**: 373 automated tests covering all critical functionality.
- **Backup Utilities**: Automated backup and change tracking scripts.
- **Offline Tolerance**: Designed for regions with intermittent internet connectivity.

## 🏗️ Project Architecture

### Technology Stack

- **Backend**: Laravel 11.x (PHP 8.2+)
- **Database**: MySQL 8.0
- **Frontend**: Blade Templates + TailwindCSS + Livewire 3.x
- **Build Tool**: Vite (with HMR)
- **Environment**: Docker + Laravel Sail
- **Testing**: Pest/PHPUnit
- **Security**: Laravel Sanctum, RBAC with type-safe permissions

### Development Methodology

This project follows the **BMAD (Business Management and Development) Methodology**:

- **Phase 1**: Requirements Gathering (PRD, UX Design)
- **Phase 2**: Solutioning (Architecture, Technical Specifications)
- **Phase 3**: Implementation Readiness (Validation, Planning)
- **Phase 4**: Implementation (Current Phase - Stories execution)
- **Phase 5**: Project Management (Future - Grant and project tracking)

### Key Architectural Decisions

- **RBAC Implementation**: Hybrid role-based + permission-based system with super admin bypass.
- **Data Scoping**: Global scopes ensure directors only see their community's data.
- **Formation Logic**: Dedicated service layer for Canon Law date calculations.
- **File Storage**: Private disk storage for sensitive documents with temporary URLs.
- **Offline Strategy**: Service Worker caching + Local Storage fallback for rural areas.

## 📁 Project Structure

```
System_Blood_Group/
├── managing-congregation/          # Main Laravel Application
│   ├── app/                        # Application Code
│   │   ├── Console/Commands/       # Artisan Commands
│   │   ├── Enums/                  # Type-safe Enums (UserRole, PermissionKey)
│   │   ├── Http/Controllers/       # HTTP Controllers
│   │   ├── Models/                 # Eloquent Models
│   │   ├── Policies/               # Authorization Policies
│   │   ├── Services/               # Business Logic Services
│   │   ├── View/Components/        # Blade Components
│   │   └── ValueObjects/           # Domain Objects
│   ├── database/                   # Migrations & Seeders
│   ├── docker/8.4/                # Docker Configuration
│   ├── resources/views/            # Blade Templates
│   ├── routes/                     # Route Definitions
│   ├── storage/app/private/        # Secure File Storage
│   └── tests/                      # Test Suites (373 tests)
├── docs/                           # Comprehensive Documentation
│   ├── architecture.md            # Technical Architecture
│   ├── prd.md                     # Product Requirements
│   ├── epics.md                   # Epic Breakdown
│   ├── ux-design-specification.md # UI/UX Design
│   ├── sprint-artifacts/          # Implementation Stories
│   ├── diagrams/                  # System Diagrams
│   └── flowcharts/                # Process Flows
├── tests/                          # Additional Test Suites
├── plans/                          # Technical Specifications
├── summaries/                      # Implementation Summaries
└── backup-*.sh                     # Backup Utilities
```

## 🚀 Getting Started

### Prerequisites

- **Docker Desktop** (latest version) - for Sail development
- **PHP 8.4+** - for local development
- **Composer** - PHP dependency manager
- **Node.js 20+** - for asset compilation
- **MySQL 8.0+** - database server
- **Git** (latest version)
- **4GB+ RAM** available for containers
- **10GB+ free disk space**

### Quick Start with Docker Sail (Recommended)

1. **Clone the Repository**

   ```bash
   git clone https://github.com/n0bcode/CongregationManagement.git
   cd System_Blood_Group/managing-congregation
   ```

2. **Environment Setup**

   ```bash
   cp .env.example .env
   ```

3. **Start Development Environment**

   ```bash
   # Start all containers (Laravel, MySQL, Mailpit, Redis)
   ./vendor/bin/sail up -d

   # Install PHP dependencies
   ./vendor/bin/sail composer install

   # Install Node dependencies and start development server
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run dev
   ```

4. **Database Setup**

   ```bash
   # Generate application key
   ./vendor/bin/sail artisan key:generate

   # Run migrations and seeders
   ./vendor/bin/sail artisan migrate --seed
   ```

5. **Access the Application**
   - **Main App**: [http://localhost](http://localhost)
   - **Mailpit (Email Testing)**: [http://localhost:8025](http://localhost:8025)
   - **phpMyAdmin**: [http://localhost:8080](http://localhost:8080)

### Alternative Setup (Without Sail)

If you prefer not to use Laravel Sail:

1. **Install Dependencies Locally**

   ```bash
   # Install PHP dependencies
   composer install

   # Install Node dependencies
   npm install
   ```

2. **Database Setup**

   ```bash
   # Create MySQL database
   mysql -u root -p -e "CREATE DATABASE congregation_management"

   # Or using Docker for just MySQL
   docker run --name mysql-congregation -e MYSQL_ROOT_PASSWORD=password -e MYSQL_DATABASE=congregation_management -p 3306:3306 -d mysql:8.0
   ```

3. **Environment Configuration**

   ```bash
   # Copy environment file
   cp .env.example .env

   # Generate application key
   php artisan key:generate
   ```

4. **Run Migrations and Seeders**

   ```bash
   php artisan migrate --seed
   ```

5. **Start Development Servers**

   ```bash
   # Start Laravel development server
   php artisan serve

   # Start Vite development server (in another terminal)
   npm run dev
   ```

6. **Access the Application**
   - **Main App**: [http://localhost:8000](http://localhost:8000)

### Essential Commands Reference

#### Docker Sail Commands

```bash
# Start containers
./vendor/bin/sail up -d

# Stop containers
./vendor/bin/sail down

# View container status
./vendor/bin/sail ps

# Access container shell
./vendor/bin/sail shell

# Run artisan commands
./vendor/bin/sail artisan <command>

# Run composer commands
./vendor/bin/sail composer <command>

# Run npm commands
./vendor/bin/sail npm <command>

# Run tests
./vendor/bin/sail test

# View logs
./vendor/bin/sail logs
```

#### Laravel Artisan Commands

```bash
# Database operations
php artisan migrate                    # Run migrations
php artisan migrate:rollback           # Rollback last migration
php artisan migrate:fresh              # Drop all tables and rerun migrations
php artisan migrate:status             # Show migration status
php artisan db:seed                    # Run seeders
php artisan migrate:refresh --seed     # Refresh and seed

# Cache operations
php artisan cache:clear                # Clear application cache
php artisan config:clear               # Clear config cache
php artisan route:clear                # Clear route cache
php artisan view:clear                 # Clear view cache
php artisan optimize                   # Cache config, routes, views
php artisan optimize:clear             # Clear all caches

# Key operations
php artisan key:generate              # Generate application key

# Storage operations
php artisan storage:link              # Create storage symlink

# User management (custom commands if implemented)
php artisan user:create               # Create new user
php artisan permission:seed           # Seed permissions
```

#### NPM Commands

```bash
# Development
npm run dev                           # Start Vite dev server with HMR
npm run build                         # Build for production
npm run preview                       # Preview production build

# Code quality
npm run lint                          # Run ESLint
npm run format                        # Format code
```

#### Testing Commands

```bash
# Run all tests
./vendor/bin/sail test
php artisan test

# Run specific test file
./vendor/bin/sail test tests/Feature/AuthTest.php

# Run with coverage
./vendor/bin/sail test --coverage

# Run tests in watch mode
./vendor/bin/sail test --watch
```

#### Database Commands (Direct MySQL)

```bash
# Connect to database
mysql -u root -p congregation_management

# Or via Docker
./vendor/bin/sail mysql

# Import database
mysql -u username -p database_name < backup.sql

# Export database
mysqldump -u username -p database_name > backup.sql
```

## 🧪 Testing & Quality Assurance

### Running Tests

```bash
# Run all tests
./vendor/bin/sail artisan test

# Run with coverage (if Xdebug enabled)
./vendor/bin/sail artisan test --coverage

# Run specific test suites
./vendor/bin/sail artisan test tests/Feature/Auth/
./vendor/bin/sail artisan test tests/Unit/
```

### Test Results

- **Total Tests**: 373
- **Passing**: 373 ✅
- **Coverage**: Comprehensive (Auth, Models, Services, Controllers)
- **Test Types**: Feature tests, Unit tests, Performance tests

### Quality Gates

- **Code Style**: Laravel Pint (PSR-12)
- **Static Analysis**: PHPStan integration
- **Security**: Laravel Security features + RBAC validation
- **Performance**: <2s page load times, optimized queries

## 👥 User Roles & Access

### Default Credentials

| Role            | Email                  | Password   | Permissions          |
| --------------- | ---------------------- | ---------- | -------------------- |
| **Super Admin** | `admin@example.com`    | `password` | All permissions      |
| **General**     | `general@example.com`  | `password` | Global view, reports |
| **Director**    | `director@example.com` | `password` | House-scoped CRUD    |
| **Member**      | `member@example.com`   | `password` | Read-only access     |

### Permission Matrix

- **Super Admin**: Full system access (bypass pattern)
- **General**: Global reports, member search, financial oversight
- **Director**: Community-specific data management
- **Member**: Personal profile access only

## 🔧 Development Workflow

### Daily Development

```bash
# Start environment
./vendor/bin/sail up -d

# Run tests continuously
./vendor/bin/sail artisan test --watch

# Code formatting
./vendor/bin/sail pint

# Static analysis
./vendor/bin/sail phpstan analyse
```

### Backup & Version Control

```bash
# Preview changes before backup
./backup-unified.sh preview <commit-id>

# Simple backup (recommended for daily use)
./backup-unified.sh simple <commit-id>

# Full backup with detailed logging
./backup-unified.sh full <commit-id>

# Auto-select backup mode based on changes
./backup-unified.sh <commit-id>
```

### Deployment

```bash
# Build for production
./vendor/bin/sail npm run build

# Optimize Laravel for production
./vendor/bin/sail artisan config:cache
./vendor/bin/sail artisan route:cache
./vendor/bin/sail artisan view:cache

# Run final tests
./vendor/bin/sail artisan test
```

## 📊 Current Implementation Status

### Completed Epics ✅

- **Epic 1: Foundation & Core Setup** (100% Complete)

  - Project initialization with Laravel Breeze
  - Core RBAC infrastructure with type-safe permissions
  - Database schema with proper relationships
  - User authentication and authorization

- **Epic 2: Member Lifecycle & Profile Management** (100% Complete)
  - Comprehensive member profiles with photos
  - Visual formation timeline with Canon Law calculations
  - Secure document upload for formation records
  - Member transfer history and service tracking
  - Advanced search and filtering

### In Progress 🚧

- **Epic 3: Community Financial Stewardship** (70% Complete)

  - Daily expense entry ✅
  - Receipt upload ✅
  - Monthly PDF reports ✅
  - Financial period locking ✅
  - Community creation 🔄

- **Epic 4: Strategic Oversight & Reporting** (80% Complete)
  - Generalate dashboard ✅
  - Critical vow expiry alerts ✅
  - Financial reports viewing ✅
  - Audit trail logging ✅

### Future Epics 🔮

- **Epic 5: Project & Grant Management** (Backlog)
  - Project creation and planning
  - Grant application tracking
  - Evidence upload and monitoring

## 🔒 Security Features

### Authentication & Authorization

- **Laravel Sanctum**: API authentication
- **RBAC System**: 50+ granular permissions
- **Super Admin Bypass**: Administrative override capability
- **Session Management**: 60-minute timeout, secure cookies

### Data Protection

- **Private File Storage**: Sensitive documents in encrypted storage
- **Data Scoping**: Automatic query filtering by community/house
- **Audit Logging**: All critical actions tracked with timestamps
- **Soft Deletes**: Data preservation with recovery capability

### Compliance

- **GDPR Considerations**: Data minimization and user consent
- **Religious Data Privacy**: Special handling of sacramental records
- **Access Control**: Role-based data visibility

## 🌐 Internationalization & Localization

### Supported Languages

- **Primary**: English
- **Future**: French, Spanish, Portuguese (African mission focus)

### Cultural Considerations

- **Date Formats**: Localized date display
- **Religious Terminology**: Proper handling of formation stages
- **Offline Capability**: Designed for areas with limited connectivity

## 📚 Documentation

### Comprehensive Documentation Structure

```
docs/
├── prd.md                     # Product Requirements Document
├── architecture.md           # Technical Architecture Decisions
├── epics.md                  # Epic and Story Breakdown
├── ux-design-specification.md # UI/UX Design Guidelines
├── bmm-workflow-status.yaml  # Project Status Tracking
├── sprint-artifacts/         # Implementation Stories
├── diagrams/                 # System Architecture Diagrams
├── flowcharts/               # Business Process Flows
└── analysis/                 # Requirements Analysis
```

### Key Documentation

- **[PRD](./docs/prd.md)**: Complete product requirements and user journeys
- **[Architecture](./docs/architecture.md)**: Technical decisions and implementation patterns
- **[Epics](./docs/epics.md)**: Detailed breakdown of features and stories
- **[UX Design](./docs/ux-design-specification.md)**: Interface design and user experience

## 🤝 Contributing

### Development Guidelines

1. **Follow BMAD Methodology**: All changes should align with current epic goals
2. **Write Tests First**: Maintain 100% test coverage for new features
3. **Use Type Safety**: Leverage PHP 8.4 features and custom enums
4. **Security First**: All changes must pass security review
5. **Documentation**: Update relevant docs for any architectural changes

### Code Standards

- **PHP**: PSR-12 with Laravel Pint formatting
- **JavaScript**: Standard ESLint rules
- **CSS**: TailwindCSS with component-based architecture
- **Testing**: Pest PHP syntax preferred
- **Git**: Conventional commit messages

### Pull Request Process

1. Create feature branch from `main`
2. Implement with comprehensive tests
3. Update documentation if needed
4. Pass all CI checks (tests, linting, security)
5. Request review from team lead
6. Merge after approval

## 🐛 Troubleshooting

### Common Issues

**Database Connection Issues**

```bash
# Reset database
./vendor/bin/sail artisan migrate:fresh --seed

# Check MySQL container
./vendor/bin/sail ps
```

**Permission Issues**

```bash
# Clear all Laravel caches
./vendor/bin/sail artisan optimize:clear

# Reset permissions (development only)
./vendor/bin/sail artisan db:seed --class=PermissionSeeder
```

**Asset Compilation Issues**

```bash
# Clear node modules and reinstall
rm -rf node_modules package-lock.json
./vendor/bin/sail npm install

# Force rebuild assets
./vendor/bin/sail npm run build
```

**Test Failures**

```bash
# Run specific failing test
./vendor/bin/sail artisan test tests/Feature/Auth/RbacTest.php

# Debug with verbose output
./vendor/bin/sail artisan test --verbose
```

### Performance Issues

- **Slow Page Loads**: Check database queries with Laravel Debugbar
- **Memory Issues**: Monitor PHP container resources
- **Asset Loading**: Ensure Vite HMR is working properly

### Getting Help

1. Check existing documentation in `docs/` folder
2. Review test cases for usage examples
3. Check GitHub issues for known problems
4. Contact development team for support

## 📈 Performance & Scalability

### Current Performance Metrics

- **Page Load Time**: <2 seconds on 4G networks
- **Database Queries**: Optimized with proper indexing
- **Asset Size**: Compressed and cached via Vite
- **Concurrent Users**: Tested with 50+ simultaneous users

### Scalability Considerations

- **Database**: MySQL with read replicas for reporting
- **Caching**: Redis for session and query caching
- **File Storage**: Cloud storage (S3/MinIO) for production
- **Containerization**: Horizontal scaling with Docker Swarm/Kubernetes

## 🔄 Deployment & Production

### Production Checklist

- [ ] Environment variables configured
- [ ] SSL certificates installed
- [ ] Database backups scheduled
- [ ] Monitoring tools configured
- [ ] CDN setup for assets
- [ ] Security headers enabled

### Docker Production Setup

```yaml
# docker-compose.prod.yml
version: "3.8"
services:
  app:
    image: congregation-management:latest
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
    volumes:
      - ./storage:/var/www/html/storage
```

### Hostinger Deployment Guide

#### Prerequisites for Hostinger

- **PHP Version**: 8.4 or higher
- **MySQL**: 8.0 or higher
- **SSH Access**: Enabled in Hostinger control panel
- **Node.js**: Optional (for building assets on server)

#### Hostinger Directory Structure

```
/home/u221940070/domains/admin.sdndel.org/
├── public_html/              # Document root (contains /public contents)
│   ├── index.php            # Modified entry point
│   ├── .htaccess            # Laravel routing
│   ├── build/               # Vite compiled assets
│   └── storage/             # Symlink to ../storage/app/public
├── app/                     # Laravel application code
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/                 # File storage (logs, cache, uploads)
└── vendor/                  # Composer dependencies
```

#### Step-by-Step Hostinger Deployment

1. **Upload Files to Hostinger**

   ```bash
   # Upload all files except /public folder to domain root
   # Upload contents of /public folder to public_html/
   ```

2. **Environment Configuration**

   ```bash
   # Create .env file in domain root
   APP_NAME="Managing Congregation"
   APP_ENV=production
   APP_KEY=base64:your-generated-app-key
   APP_DEBUG=false
   APP_URL=https://your-domain.com

   # Database (from Hostinger MySQL settings)
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_db_username
   DB_PASSWORD=your_db_password

   # File storage
   FILESYSTEM_DISK=public

   # Session configuration
   SESSION_DRIVER=database
   SESSION_LIFETIME=120

   # Cache (use database for shared hosting)
   CACHE_DRIVER=file
   QUEUE_CONNECTION=database
   ```

3. **Modify Entry Point (Critical)**

   ```php
   # public_html/index.php - Update paths from __DIR__.'/../' to __DIR__.'/../'
   <?php
   use Illuminate\Http\Request;

   define('LARAVEL_START', microtime(true));

   if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
       require $maintenance;
   }

   require __DIR__.'/../vendor/autoload.php';

   (require_once __DIR__.'/../bootstrap/app.php')
       ->handleRequest(Request::capture());
   ```

4. **Database Setup**

   ```bash
   # Via SSH or Hostinger File Manager
   cd /home/u221940070/domains/admin.sdndel.org

   # Run migrations
   php artisan migrate --force

   # Seed database (optional, for initial data)
   php artisan db:seed --force
   ```

5. **Storage Setup**

   ```bash
   # Create storage symlink
   php artisan storage:link

   # Set proper permissions
   chmod -R 755 storage/
   chmod -R 755 bootstrap/cache/
   ```

6. **Asset Compilation**

   ```bash
   # IMPORTANT: Build from project root (not public_html)
   cd /home/u221940070/domains/admin.sdndel.org

   # Install dependencies if needed
   npm install

   # Build assets (this creates files in public_html/build/)
   npm run build

   # Verify build files exist
   ls -la public_html/build/
   ```

7. **Cache Optimization**

   ```bash
   # Clear all caches first
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear

   # Cache for production
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

8. **Final Verification**

   ```bash
   # Test application
   php artisan tinker --execute="echo 'Laravel working'"

   # Check storage permissions
   ls -la storage/

   # Verify database connection
   php artisan migrate:status
   ```

#### Essential Hostinger Commands

```bash
# SSH Access (replace with your actual path)
ssh u221940070@your-server.hostinger.com
cd domains/admin.sdndel.org

# Laravel Commands
php artisan migrate --force                 # Run migrations
php artisan migrate:rollback --force        # Rollback migrations
php artisan db:seed --force                 # Run seeders
php artisan storage:link                    # Create storage symlink
php artisan cache:clear                     # Clear cache
php artisan config:cache                    # Cache config
php artisan route:cache                     # Cache routes
php artisan view:cache                      # Cache views
php artisan optimize                        # Optimize all
php artisan optimize:clear                  # Clear all optimizations

# File Permissions
chmod -R 755 storage/                       # Storage permissions
chmod -R 755 bootstrap/cache/               # Cache permissions
chmod 644 .env                              # Environment file

# Database Management
php artisan migrate:status                  # Check migration status
php artisan tinker                          # Laravel REPL
php artisan db:monitor                      # Database monitoring

# Queue Management (if using queues)
php artisan queue:work                      # Process queues
php artisan queue:failed                    # View failed jobs
php artisan queue:retry all                 # Retry failed jobs

# Maintenance Mode
php artisan down                           # Enable maintenance
php artisan up                             # Disable maintenance
```

#### Hostinger-Specific Configuration

```env
# .env production settings for Hostinger
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database (Hostinger MySQL)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=u221940070_congregation
DB_USERNAME=u221940070_user
DB_PASSWORD=your_password

# Session (important for shared hosting)
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Cache (file-based for shared hosting)
CACHE_DRIVER=file

# Mail (if using Hostinger SMTP)
MAIL_MAILER=smtp
MAIL_HOST=mail.your-domain.com
MAIL_PORT=587
MAIL_USERNAME=your-email@your-domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls

# Filesystem
FILESYSTEM_DISK=public
```

#### Common Hostinger Issues & Solutions

**Issue: 500 Internal Server Error**

```bash
# Check Laravel logs
tail storage/logs/laravel.log

# Clear all caches
php artisan optimize:clear

# Check .env file
cat .env | grep -v PASSWORD  # Hide sensitive data
```

**Issue: Storage Link Not Working**

```bash
# Remove old symlink
rm -rf public_html/storage

# Create new symlink
php artisan storage:link

# Or create manually
ln -s ../storage/app/public public_html/storage
```

**Issue: Vite Assets Not Loading (404 Errors)**

```bash
# Step 1: Check if build files exist in correct location
ls -la public_html/build/assets/

# Step 2: Verify .htaccess allows asset serving
cat public_html/.htaccess | grep -A 10 "RewriteEngine"

# Step 3: Clear browser cache and Laravel caches
php artisan optimize:clear

# Step 4: Check if assets are being loaded correctly in Blade
grep -r "Vite::" resources/views/

# Step 5: If using Vite directives, ensure they're correct
# In Blade templates, should be: @vite(['resources/css/app.css', 'resources/js/app.js'])
```

**Issue: Database Connection Failed**

```bash
# Check database credentials in .env
php artisan tinker --execute="DB::connection()->getPdo()"

# Test database connection
php artisan migrate:status
```

**Issue: Permission Denied**

```bash
# Fix storage permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# Check file ownership
ls -la storage/
```

#### Hostinger File Manager Alternative

If SSH is not available, use Hostinger File Manager:

1. **Upload Files**: Use File Manager to upload all project files
2. **Create .env**: Create `.env` file via File Manager
3. **Run Commands**: Use "Run PHP Script" in Advanced settings
4. **Check Logs**: View `storage/logs/laravel.log` via File Manager

#### Automated Deployment Script (Optional)

```bash
#!/bin/bash
# deploy.sh - Place in project root

echo "🚀 Starting Hostinger deployment..."

# Build assets locally
npm run build

# Upload files via FTP or Git
# (Configure your preferred upload method)

# Run remote commands via SSH
ssh user@server << EOF
cd /path/to/domain/root
php artisan migrate --force
php artisan storage:link
php artisan optimize
echo "✅ Deployment completed!"
EOF
```

#### Backup Strategy

- **Database**: Use Hostinger's built-in backup or phpMyAdmin export
- **Files**: Download via FTP or use backup scripts
- **Code**: Git versioning for rollback capability

### Backup Strategy

- **Database**: Daily automated backups via Hostinger or custom scripts
- **Files**: Secure cloud storage with encryption
- **Code**: Git versioning with tags
- **Configuration**: Environment-specific settings

## 📄 License & Legal

This project is proprietary software developed for religious congregation management. All rights reserved.

### Usage Rights

- **Internal Use**: Authorized congregation personnel only
- **Data Privacy**: Compliant with religious data protection requirements
- **Security**: Regular security audits and updates required

## 🙏 Acknowledgments

### Special Thanks

- **BMAD Methodology Team**: For the comprehensive development framework
- **Laravel Community**: For the robust PHP framework
- **Religious Communities**: For their partnership and feedback
- **Open Source Contributors**: For the tools and libraries that made this possible

### Project Team

- **Lead Developer**: Wavister
- **Methodology**: BMAD Framework
- **Testing**: Comprehensive automated test suite
- **Documentation**: Complete technical and user documentation

---

**Managing the Congregation** - Built with ❤️ for religious communities worldwide.

_Last Updated: December 2025_
