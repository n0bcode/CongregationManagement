# Managing the Congregation (MTC)

> A comprehensive management system designed for religious congregations to manage members, financials, documents, and community life efficiently and securely.

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)

## 📖 Overview

**Managing the Congregation** is a specialized ERP-like solution tailored for the unique needs of religious orders. It moves beyond simple record-keeping to provide a holistic view of community life, ensuring data integrity, privacy, and ease of administration.

The system is built to handle:

-   **Long-term Member History**: Tracking formation stages, assignments, and vows over decades.
-   **Multi-level Governance**: Supporting Generalate, Provincial, and Local Community levels.
-   **Financial Accountability**: Transparent expense tracking, budgeting, and reporting.
-   **Secure Archiving**: Digital storage for sensitive documents and historical records.

## ✨ Key Features

### 👤 Member Management

-   **Comprehensive Profiles**: Personal info, religious name, dates, and bio.
-   **Visual Timeline**: Interactive history of formation, assignments, and transfers.
-   **Health & Skills**: Track medical records and professional skills.
-   **Search & Filtering**: Advanced search with scope-based access control.

### 💰 Financial Management

-   **Expense Tracking**: Daily expense entry with receipt uploads.
-   **Approval Workflows**: Digital approval process for community expenses.
-   **Monthly Reports**: One-click generation of standardized financial reports.
-   **Fiscal Control**: Locking mechanisms for closed financial periods.

### 📂 Document Management

-   **Secure Storage**: Role-based access to sensitive documents.
-   **Categorization**: Organized folders for formation, legal, and medical files.
-   **Direct Preview & Download**: Easy access to stored files.

### 🛡️ Security & Access Control (RBAC)

-   **Granular Permissions**: Over 50+ specific permissions.
-   **Role-Based Access**: Pre-configured roles (General, Director, Secretary, Member).
-   **Scoped Data**: Users only see data relevant to their community or assignment.
-   **Audit Logs**: Detailed tracking of all system modifications.

### 📊 Reports & Dashboard

-   **Demographic Analysis**: Statistics on age, formation stage, and skills.
-   **Financial Overview**: Real-time view of community spending.
-   **Celebrations**: Automated reminders for birthdays and feast days.

## 🛠️ Technology Stack

-   **Backend**: Laravel 11 (PHP 8.2+)
-   **Database**: MySQL 8.0
-   **Frontend**: Blade Templates, TailwindCSS, Alpine.js
-   **Environment**: Docker (via Laravel Sail)
-   **Testing**: PHPUnit (Feature & Unit tests)

## 🚀 Getting Started

### Prerequisites

-   Docker Desktop installed and running.
-   Git.

### Installation

1.  **Clone the repository**

    ```bash
    git clone <repository-url>
    cd System_Blood_Group/managing-congregation
    ```

2.  **Environment Setup**

    ```bash
    cp .env.example .env
    ```

3.  **Install Dependencies**

    ```bash
    # Using a small docker container to install composer deps if you don't have PHP local
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php82-composer:latest \
        composer install --ignore-platform-reqs
    ```

4.  **Start the Application**

    ```bash
    ./vendor/bin/sail up -d
    ```

5.  **Generate Key & Database**

    ```bash
    ./vendor/bin/sail artisan key:generate
    ./vendor/bin/sail artisan migrate --seed
    ```

    > **Note**: The seeder will create default admin accounts and sample data.

6.  **Build Frontend Assets**

    ```bash
    ./vendor/bin/sail npm install
    ./vendor/bin/sail npm run dev
    ```

7.  **Access the App**
    Open [http://localhost](http://localhost) in your browser.

## 🧪 Running Tests

The project maintains a high level of test coverage. To run the test suite:

```bash
./vendor/bin/sail artisan test
```

## 👥 Default Credentials (Seeded)

| Role            | Email                  | Password   |
| --------------- | ---------------------- | ---------- |
| **Super Admin** | `admin@example.com`    | `password` |
| **General**     | `general@example.com`  | `password` |
| **Director**    | `director@example.com` | `password` |
| **Member**      | `member@example.com`   | `password` |

## 📄 License

This project is proprietary software. All rights reserved.
