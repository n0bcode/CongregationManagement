# Managing the Congregation (MTC)

> A comprehensive management system designed for religious congregations to manage members, financials, documents, and community life efficiently and securely.

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.3.6-777BB4?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)
![Livewire](https://img.shields.io/badge/Livewire-3.x-4E56A6?style=for-the-badge&logo=livewire)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker)

## 📖 Overview

**Managing the Congregation** is a specialized ERP-like solution tailored for the unique needs of religious orders. Built using the BMAD (Business Management and Development) methodology, it moves beyond simple record-keeping to provide a holistic view of community life, ensuring data integrity, privacy, and ease of administration.

> 📘 **New:** [**User Guide**](./managing-congregation/docs/USER_GUIDE.md) is now available!

### What Makes This Special

Unlike generic HR or CRM tools, this system is **purpose-built for religious life**, natively handling unique workflows such as:

- **Formation Lifecycle Tracking:** Seamlessly managing the progression from Postulancy to Perpetual Vows.
- **Community-Centric Logic:** Handling annual assignments, transfers, and community-level financial reporting.
- **Long-Term Stewardship:** Prioritizing the preservation of historical records and "lifelong" member data.
- **Global Accessibility:** Designed for ease of use in regions with varying technical infrastructure, such as Africa.

### Project Status

- **Current Phase**: Production (v1.0.0)
- **Release Date**: December 27, 2025
- **System Scale**: 28 models, 186 routes, 14 major modules
- **Test Coverage**: Comprehensive (PHPUnit 11.5.3)
- **Documentation**: Complete and production-ready

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

- **Backend**: Laravel 11.x (PHP 8.3.6)
- **Database**: MySQL 8.0
- **Frontend**: Blade Templates + TailwindCSS 3.4 + Livewire 3.7 (Heavy Usage) + Alpine.js
- **Build Tool**: Vite (with HMR)
- **Environment**: Docker + Laravel Sail 1.41
- **Testing**: PHPUnit 11.5.3
- **Export Libraries**: DomPDF 3.1, PHPWord 1.4, Maatwebsite Excel 3.1, Intervention Image 3.11
- **Security**: Laravel Sanctum, Dynamic RBAC with type-safe permissions

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
│   ├── storage/app/private/        # Secure File Storage
│   └── docs/
│       └── USER_GUIDE.md           # 📘 Complete User Manual (Vietnamese)
├── docs/                           # Technical & Project Documentation
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

### Quick Start (5 Minutes)

Get up and running quickly with Docker Sail:

```bash
# 1. Clone and navigate
git clone https://github.com/n0bcode/CongregationManagement.git
cd System_Blood_Group/managing-congregation

# 2. Setup environment
cp .env.example .env

# 3. Start containers and install dependencies
./vendor/bin/sail up -d
./vendor/bin/sail composer install
./vendor/bin/sail npm install

# 4. Initialize database
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed

# 5. Start development server
./vendor/bin/sail npm run dev
```

**Access the application:**

- Main App: http://localhost
- Mailpit (Email Testing): http://localhost:8025

**Default Login:**

- Email: `admin@example.com`
- Password: `password`

### 📚 Detailed Setup Guides

For comprehensive setup instructions, troubleshooting, and alternative installation methods, see:

- **[Developer Onboarding Guide](./docs/developer-onboarding.md)** - Complete quick start with prerequisites, project structure, and common tasks
- **[Developer Guide](./managing-congregation/docs/DEVELOPER_GUIDE.md)** - In-depth technical documentation
- **[Deployment Guide](./docs/deployment-guide.md)** - Production deployment procedures

## 🧪 Testing & Quality Assurance

### Quick Test Commands

```bash
# Run all tests
./vendor/bin/sail artisan test

# Run with coverage
./vendor/bin/sail artisan test --coverage

# Run specific test
./vendor/bin/sail artisan test tests/Feature/MemberTest.php
```

### Test Coverage

- **Framework:** PHPUnit 11.5.3
- **Coverage:** Comprehensive (Auth, RBAC, Models, Services, Controllers, Livewire)
- **Quality:** Laravel Pint (PSR-12), PHPStan static analysis

### 📚 Complete Testing Guide

For detailed testing patterns, best practices, and CI/CD setup, see:

- **[Testing Documentation](./docs/testing-documentation.md)** - Complete testing guide with patterns and examples

## 👥 User Roles & Access

### Default Credentials (Development)

| Role            | Email                  | Password   |
| --------------- | ---------------------- | ---------- |
| **Super Admin** | `admin@example.com`    | `password` |
| **General**     | `general@example.com`  | `password` |
| **Director**    | `director@example.com` | `password` |
| **Member**      | `member@example.com`   | `password` |

### 📚 Complete RBAC Documentation

For detailed permission matrix, dynamic role creation, and security patterns, see:

- **[Features Implemented](./docs/features-implemented.md)** - Security & Access Control section
- **[Project Context](./docs/project_context.md)** - RBAC rules and patterns

## 🔧 Development Workflow

### Quick Reference

```bash
# Daily development
./vendor/bin/sail up -d
./vendor/bin/sail npm run dev

# Code quality
./vendor/bin/sail pint              # Format code
./vendor/bin/sail phpstan analyse   # Static analysis
./vendor/bin/sail artisan test      # Run tests
```

### 📚 Complete Guides

- **[Developer Onboarding](./docs/developer-onboarding.md)** - Daily workflow, common tasks, best practices
- **[Deployment Guide](./docs/deployment-guide.md)** - Production deployment, updates, rollbacks
- **[Testing Documentation](./docs/testing-documentation.md)** - Testing patterns and CI/CD

## 📊 Implementation Status

**Current Version:** v1.0.0 (Production)  
**Release Date:** December 27, 2025  
**System Scale:** 28 models, 186 routes, 14 major modules

### 📚 Complete Feature List

For detailed implementation status and feature inventory, see:

- **[Features Implemented](./docs/features-implemented.md)** - Complete catalog of all 14 modules
- **[Feature Roadmap](./docs/feature-roadmap.md)** - 2026 strategic plans and future enhancements
- **[CHANGELOG](./CHANGELOG.md)** - Version history from 0.1.0 to 1.0.0

## 🔒 Security & Compliance

**Key Features:**

- Dynamic RBAC with 50+ permissions
- Laravel Sanctum API authentication
- Comprehensive audit logging
- Private file storage with encryption
- Community-scoped data access

### 📚 Security Documentation

- **[Features Implemented](./docs/features-implemented.md)** - Security & Access Control section
- **[Project Context](./docs/project_context.md)** - Security rules and patterns
- **[Deployment Guide](./docs/deployment-guide.md)** - Security checklist

## 🌐 Internationalization

**Current:** English  
**Planned (2026):** French, Spanish, Portuguese, Vietnamese

See **[Feature Roadmap](./docs/feature-roadmap.md)** for multi-language support plans.

## 📚 Documentation

### Comprehensive Documentation Structure

```
docs/
├── project_context.md                  # 🆕 Core rules and patterns for AI agents
├── features-implemented.md             # 🆕 Complete feature inventory
├── export-architecture.md              # 🆕 PDF/Excel/DOCX generation patterns
├── livewire-patterns.md                # 🆕 Livewire component architecture
├── deployment-guide.md                 # 🆕 Production deployment procedures
├── developer-onboarding.md             # 🆕 Quick start guide (5 minutes)
├── testing-documentation.md            # 🆕 Testing patterns and best practices
├── feature-roadmap.md                  # 🆕 2026 strategic roadmap
├── api-documentation.md                # 🆕 Planned REST API (Q3 2026)
├── documentation-update-summary.md     # 🆕 Documentation update summary
├── prd.md                              # Product Requirements (Historical)
├── architecture.md                     # Technical Architecture (Historical)
├── epics.md                            # Epic and Story Breakdown
├── ux-design-specification.md          # UI/UX Design Guidelines
├── bmm-workflow-status.yaml            # Project Status Tracking
├── sprint-artifacts/                   # Implementation Stories
├── diagrams/                           # System Architecture Diagrams
├── flowcharts/                         # Business Process Flows
└── analysis/                           # Requirements Analysis

managing-congregation/docs/
├── DEVELOPER_GUIDE.md                  # Complete Developer Documentation
└── USER_GUIDE.md                       # End-User Manual (Vietnamese)

CHANGELOG.md                            # 🆕 Version history (0.1.0 → 1.0.0)
```

### Key Documentation

#### For AI Agents & Developers (🆕 Updated December 2025)

- **[Project Context](./docs/project_context.md)**: **START HERE** - Core rules, patterns, and tech stack for AI agents
- **[Features Implemented](./docs/features-implemented.md)**: Complete inventory of all 14 modules, 28 models, 186 routes
- **[Developer Onboarding](./docs/developer-onboarding.md)**: Quick start guide - get running in 5 minutes
- **[Testing Documentation](./docs/testing-documentation.md)**: PHPUnit patterns, feature/unit/Livewire testing
- **[Livewire Patterns](./docs/livewire-patterns.md)**: Component architecture and state management
- **[Export Architecture](./docs/export-architecture.md)**: PDF/Excel/DOCX generation patterns
- **[Deployment Guide](./docs/deployment-guide.md)**: Production deployment with Docker Compose
- **[Developer Guide](./managing-congregation/docs/DEVELOPER_GUIDE.md)**: Legacy comprehensive technical documentation

#### For Product & Planning

- **[Feature Roadmap](./docs/feature-roadmap.md)**: 2026 strategic plans (PWA, AI expansion, global collaboration)
- **[API Documentation](./docs/api-documentation.md)**: Planned REST API for Q3 2026
- **[CHANGELOG](./CHANGELOG.md)**: Complete version history from 0.1.0 to 1.0.0
- **[Documentation Update Summary](./docs/documentation-update-summary.md)**: Summary of December 2025 documentation refresh
- **[PRD](./docs/prd.md)**: Original product requirements (Historical reference)
- **[Architecture](./docs/architecture.md)**: Original technical decisions (Historical reference)
- **[Epics](./docs/epics.md)**: Detailed breakdown of features and stories
- **[UX Design](./docs/ux-design-specification.md)**: Interface design and user experience

#### For End Users

- **[User Guide](./managing-congregation/docs/USER_GUIDE.md)**: Comprehensive manual for end-users (Vietnamese)
  - System overview and getting started
  - Step-by-step instructions for all modules
  - Member, community, and financial management
  - Project management and administration
  - FAQ and support information

## 🤝 Contributing

**Code Standards:**

- PHP 8.3.6 with strict typing
- PSR-12 formatting (Laravel Pint)
- PHPUnit 11.5.3 for testing
- Comprehensive test coverage required

### 📚 Development Guidelines

For complete coding standards, patterns, and contribution process, see:

- **[Project Context](./docs/project_context.md)** - Core rules and patterns
- **[Developer Onboarding](./docs/developer-onboarding.md)** - Development workflow
- **[Testing Documentation](./docs/testing-documentation.md)** - Testing requirements

## 🐛 Troubleshooting

### Quick Fixes

```bash
# Clear all caches
./vendor/bin/sail artisan optimize:clear

# Reset database
./vendor/bin/sail artisan migrate:fresh --seed

# Rebuild assets
./vendor/bin/sail npm run build
```

### 📚 Complete Troubleshooting Guide

For detailed troubleshooting, common issues, and solutions, see:

- **[Developer Onboarding](./docs/developer-onboarding.md)** - Common issues and debugging
- **[Deployment Guide](./docs/deployment-guide.md)** - Production troubleshooting
- **[Testing Documentation](./docs/testing-documentation.md)** - Test debugging

## 🔄 Deployment & Production

### Quick Production Checklist

- [ ] Environment variables configured
- [ ] SSL certificates installed
- [ ] Database backups scheduled
- [ ] Monitoring tools configured
- [ ] Security headers enabled

### Deployment Options

**Docker Compose (Recommended):**

- Full control over infrastructure
- Easy to scale and maintain
- Comprehensive guide available

**Shared Hosting (Hostinger, etc.):**

- Cost-effective for small deployments
- Requires manual configuration
- Step-by-step guide available

### 📚 Complete Deployment Guide

For detailed production deployment instructions, Docker setup, Hostinger deployment, troubleshooting, and maintenance procedures, see:

- **[Deployment Guide](./docs/deployment-guide.md)** - Complete production deployment documentation

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

_Last Updated: December 27, 2025 - v1.0.0 Production Release_
