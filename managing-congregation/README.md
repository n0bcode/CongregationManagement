# Congregation Management System

![Laravel](https://img.shields.io/badge/Laravel-11.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3.7-4E56A6?style=for-the-badge&logo=livewire&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

A specialized, robust solution for managing religious congregations. This system streamlines member tracking, formation paths, assignment histories, and community celebrations, empowering communities to focus on their mission.

---

## 📖 Key Modules

### User & Member Management

-   **Detailed Member Profiles**: Track personal data, religious names, and critical dates (Entry, First Vows, Perpetual Vows, Ordination).
-   **Document Storage**: Securely manage passport details and health records.
-   **Timeline History**: Automated chronological view of a member's journey through formation and assignments.

### Community & Housing

-   **Community Tracking**: Manage houses with extended metadata (Patron Saint, Feast Day, Foundation Date).
-   **Assignment Logic**: Smart assignment tracking with overlap prevention and history logging.

### 🎉 Celebration & Communication

-   **Automated Card Generator**: Instantly generate personalized Birthday and Anniversary cards.
    -   **Dynamic Font Selection**: Choose from **Caveat** (Playful), **Fleur De Leah** (Elegant), or **Roboto** (Modern).
    -   **Smart Branding**: Cards automatically feature the member's assigned community name.
    -   **Rich Graphics**: Includes custom confetti effects and vector assets.
-   **One-Click Actions**: Preview, Download, or Email cards directly from the dashboard.

### Security

-   **RBAC**: Granular Role-Based Access Control to ensure sensitive data is only accessible to authorized personnel.

---

## 🛠 Tech Stack

-   **Backend**: Laravel 11.x, PHP 8.2+
-   **Frontend**: Blade, TailwindCSS, Livewire 3.x
-   **Database**: MySQL / MariaDB
-   **PDF/Image Generation**: `barryvdh/laravel-dompdf`, `intervention/image` v3.11

---

## 📚 Documentation

The full documentation is organized in the `docs/` folder:

### 📖 [User Manual](docs/user-manual/getting-started.md)

For daily users and managers.

-   **[Getting Started](docs/user-manual/getting-started.md)**: Dashboard and basic navigation.
-   **[Member Management](docs/user-manual/members.md)**: Profiles, formation, documents.
-   **[Communities](docs/user-manual/communities.md)**: Housing and assignments.
-   **[Projects & Tasks](docs/user-manual/projects-tasks.md)**: Managing initiatives.
-   **[Financials](docs/user-manual/financials.md)**: Expenses and reporting.
-   **[Celebrations](docs/user-manual/celebrations.md)**: Birthday cards and events.

### 🛡️ [Administrator Guide](docs/admin-guide/rbac-overview.md)

For system administrators.

-   **[RBAC Overview](docs/admin-guide/rbac-overview.md)**: Roles and permissions.
-   **[Settings & Backups](docs/admin-guide/settings-backups.md)**: Configuration and data safety.

### 🛠️ [Technical & Archives](docs/technical/rbac/RBAC_IMPLEMENTATION_SUMMARY.md)

Implementation details and legacy documents.

---

## ⚙️ Requirements

Ensure your server meets the following requirements:

-   **PHP**: >= 8.2
-   **Database**: MySQL / MariaDB
-   **Components**: Composer, Node.js (LTS).

---

## 🚀 Quick Start

1.  **Clone & Install**

    ```bash
    git clone https://github.com/your-org/managing-congregation.git
    cd managing-congregation
    composer install
    npm install && npm run build
    ```

2.  **Configure**

    ```bash
    cp .env.example .env
    php artisan key:generate
    php artisan migrate --seed
    ```

3.  **Serve**
    ```bash
    php artisan serve
    ```

---

## 🤝 Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
