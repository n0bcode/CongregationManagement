# System Settings & Backups

Keep the system running smoothly and securely.

## ⚙️ General Settings

Located at **Admin > Settings**.

![System Settings Panel - Screenshot](../images/admin-settings.png)

-   **App Name**: Change the display name of the application (appears in emails and page titles).
-   **Logo**: Upload a new organization logo (PNG or SVG recommended).
-   **Date Format**: Customize how dates represent (e.g., DD/MM/YYYY).
-   **Maintenance Mode**: Toggle this **ON** when making updates. It shows a "Under Maintenance" page to non-admin users.

---

## 💾 Backups

Data safety is critical. The system supports automated and manual backups.

### Creating a Manual Backup

1.  Go to **Admin > Backups**.
2.  Click **Create Backup**.
3.  Choose the Type:
    -   **Full Backup**: Database + All uploaded files (Photos, Documents). _Recommended weekly._
    -   **Database Only**: Just the SQL data. _Fast and recommended daily._
4.  Wait for the progress bar to complete.
5.  Click **Download** to save a copy to your local machine (off-site storage).

![Backup Management Screen - Screenshot](../images/admin-backups.png)

### Automated Backups

The system attempts to run a backup every night at 02:00 AM if the cron schedule is configured. Check the **Backup Status** panel to see the "Last Successful Run".
