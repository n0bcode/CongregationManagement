# User Guide for Congregation Management System

**Version:** 1.0
**Last Updated:** 16/12/2025

---

## TABLE OF CONTENTS

**Part 1: Introduction**
1.1. System Purpose
1.2. Intended Audience
1.3. How to Use This Guide
1.4. Terms and Definitions
1.5. System Requirements

> **📘 For Developers**: If you are a developer looking for technical documentation, architecture details, or setup instructions, please refer to the [Developer Guide](./DEVELOPER_GUIDE.md).

**Part 2: Getting Started**
2.1. System Access
2.2. Login & Logout
2.3. Dashboard Overview

**Part 3: Functional Modules**
3.1. Member Management
3.2. Community Management
3.3. Formation Management
3.4. Financial Management
3.5. Periodic Events
3.6. Project Management
3.7. System Administration

**Part 4: Frequently Asked Questions (FAQ)**

**Part 5: Support**

---

## PART 1: INTRODUCTION

### 1.1. System Purpose

The **Congregation Management System** is a comprehensive software solution designed to support the storage, management, and retrieval of information within the Congregation. The system helps digitize management processes:

-   **Personnel Records**: Detailed storage of member information, from biographical data and health status to formation and ordination history.
-   **Communities & Assignments**: Management of community lists and the history of member transfers and assignments.
-   **Activities & Finance**: Tracking of projects, common tasks, and transparent management of expenses.
-   **Reporting**: Providing quick and accurate aggregate reports to support decision-making by the Council.

### 1.2. Intended Audience

This document is intended for:

-   **Council Members**: To grasp the overview and utilize management reports.
-   **Secretariat/Office**: To perform data entry, profile updates, and daily data management.
-   **Members**: (If granted permission) To view and update personal information or assigned tasks.

### 1.3. How to Use This Guide

-   **New Users**: Should start from **Part 2: Getting Started** to familiarize themselves with the system.
-   **Function Lookup**: Use the **Table of Contents** to navigate to specific instructions (e.g., how to "Create a New Member").
-   Pay attention to the icons:
    -   💡 **Tip**: Quick or helpful ways to do things.
    -   ⚠️ **Note**: Important points to remember to avoid errors.
    -   🚨 **Warning**: Critical actions that cannot be undone.

### 1.4. Terms and Definitions

-   **Member**: Priests, religious, or personnel belonging to the Congregation.
-   **Community**: Houses/branches of the Congregation.
-   **Assignment**: The process of changing a member's workplace or role.
-   **Ordination**: Milestones of Holy Orders (Deaconate, Priesthood...).

### 1.5. System Requirements

-   **Device**: Desktop PC, Laptop, or Tablet.
-   **Browser**: Recommended to use the latest version of **Google Chrome**, **Firefox**, or **Microsoft Edge**.
-   **Network**: Stable Internet or Intranet connection required.

---

## PART 2: GETTING STARTED

### 2.1. System Access

Open your web browser and enter the URL provided by the Administrator:
`[SYSTEM_URL]` (e.g., `https://manage.congregation.org`)

### 2.2. Login / Logout

#### Login

1.  On the login screen, enter your provided **Email**.
2.  Enter your **Password**.
3.  Click the **Login** button.
    -   _💡 Tip: Select "Remember Me" if you are using a personal computer to avoid re-entering details next time._

#### Forgot Password

1.  Click the **Forgot your password?** link on the login screen.
2.  Enter your Email address and follow the instructions sent to your mail to reset your password.

#### Logout

To ensure data security, please log out when not in use:

1.  Click on your **Avatar/Name** at the top right corner of the screen.
2.  Select **Log Out** from the dropdown menu.

### 2.3. Dashboard Overview

After successfully logging in, you will see the **Dashboard**. This is the main control center.

_*(Insert Dashboard Screenshot Here)*
![Main Dashboard Interface](link-to-image-dashboard.png)_

-   **1. Sidebar Menu**: Contains links to all main functions (Members, Communities, Reports...).
-   **2. Top Bar**: Contains Quick Search, Notifications, and Account Information.
-   **3. Widgets Area**: Displays quick summary metrics like "Total Members", "Upcoming Events", "Personnel Charts".
-   **4. Main Workspace**: Displays the detailed content of the selected function.

---

## PART 3: DETAILED FUNCTIONAL INSTRUCTIONS

### 3.1. Member Management

#### 1. Purpose

This function allows managing the entire lifecycle of a member in the Congregation, from initial inquiry (Postulancy) to Final Vows or death. Stored information includes personal profiles, formation history, and transfer records.

#### 2. Access Rights

-   **Super Admin / Admin**: Full control (View, Add, Edit, Delete, Export).
-   **Community Manager**: Manage members only within their assigned Community.
-   **Viewer**: View list and details only.

#### 3. Step-by-Step Instructions

**A. Viewing Member List**

1.  From the left navigation menu, select **Members**.
2.  The list screen will display columns: Name, Religious Name, Status, and Current Community.

![Member List](link-to-image-member-list.png)

-   **Search**: Enter name (Civil or Religious) in the search box at the top.
-   **Filter**:
    -   **Status**: Filter by status (Active, Exited, Deceased...).
    -   **Community**: Filter by affiliated community.
    -   _💡 Tip: Use "Presets" like "Active Members" or "Novitiates" for quick viewing._
-   **Sort**: Click column headers to sort ascending/descending.
-   **Export**: Select members to export (or select all), then click **Export** to download as Excel/CSV.

**B. Creating a New Member**

The creation process is designed as a **3-Step Wizard** to ensure complete information.

1.  On the list screen, click **+ Create New**.

![New Member Form](link-to-image-create-member.png)

2.  **Step 1: Personal Information**
    -   **First Name (Civil)** `*`: Birth name.
    -   **Last Name (Civil)** `*`: Birth surname.
    -   **Date of Birth** `*`: Birthday. _(Cannot be a future date)_.
    -   **Passport Information**: (Optional) Number, Place of Issue, Date of Issue/Expiry.
3.  Click **Next** to proceed.

4.  **Step 2: Religious Information**
    -   **Religious Name**: Saint name or name in religion (if any).
    -   **Member Type** `*`: Select current formation stage:
        -   `Postulant`
        -   `Novice`
        -   `Professed`
    -   **Entry Date** `*`: Date of joining the congregation.
    -   _⚠️ Note: Additional date fields will appear depending on the "Member Type" selected:_
        -   If **Novice/Professed**: Must enter **Novitiate Entry Date**.
        -   If **Professed**: Must enter **First Vows Date** and optionally **Perpetual Vows Date**.
5.  Click **Next** to proceed to the final step.

6.  **Step 3: Community Assignment**
    -   If you are **Super Admin**: You can choose the Community this member belongs to.
    -   If you are **Community Manager**: System automatically assigns to your community (you cannot change this).
7.  Click **Create Member** to finish.

**C. Viewing & Editing**

1.  Click the member's name in the list to open the **Profile** page.
2.  The Profile screen is divided into tabs:
    -   **Overview**: General info & Activity timeline.
    -   **Assignments**: Transfer history.
    -   **Formation**: Formation process.
3.  To edit, click the **Edit** button (pencil icon) on the top right or on specific info cards.
4.  Update information and click **Save**.

**D. Deleting a Member**

1.  In the list, check the box next to the member(s) to delete.
2.  Click the **Delete Selected** button that appears.
3.  **🚨 Warning**: A confirmation dialog will appear. This action moves the member to the trash, but related historical data might be affected. Consider carefully.

---

### 3.2. Community Management

#### 1. Purpose

This module is used to manage the list of Houses, Branches, or Communities belonging to the Congregation. This is the basic organizational unit for grouping members.

#### 2. Access Rights

-   **Super Admin / Admin**: Full control (Add, Edit, Delete).
-   **Other Roles**: View only.

#### 3. Step-by-Step Instructions

**A. Viewing Community List**

1.  From the navigation menu, select **Communities**.
2.  The screen displays a list table including:
    -   **Name**: Community name.
    -   **Location**: Address or region.
    -   **Members**: Number of current members in that community.
    -   **Created Date**.

![Community List](link-to-image-community-list.png)

-   **Search**: Enter community name in search box and click **Search**.
-   **View Details**: Click Community Name to enter the details page, where you can see the specific list of members belonging to that community.

**B. Creating a New Community**

1.  On the list screen, click **+ Create New** or **Create New Community**.
2.  Fill in the form:
    -   **Community Name** `*`: Full name of the community (Required).
    -   **Location**: Detailed address (City, Province, or House Number...).
    -   **Patron Saint**: The Patron Saint of the community.
    -   **Foundation Date**: Date founded.
    -   **Feast Day**: Patronal Feast Day (used for event reminders).
    -   **Contact Info**: Email and Phone number of the community.
3.  Click **Create Community** to save.

**C. Editing**

1.  In the row of the community to edit, click the **Edit** button (pencil icon) in the right column.
2.  Update necessary fields.
3.  Click **Update Community** to save changes.

**D. Deleting a Community**

1.  In the row of the community to delete, click the **Delete** button (trash can icon).
2.  Confirm action in the warning dialog.

**🚨 Important Warning**:

-   You **CANNOT DELETE** a community if it contains active members.
-   If attempted, the system will error: _"Cannot delete this community because it has X members..."_
-   **Solution**: Move all members to another community (using Assignment function) or delete those members before deleting the community.

---

### 3.3. Formation Management

#### 1. Purpose

This module is used to track the formation journey of members, from initial stages (Postulancy/Novitiate) to Final Vows milestones. The system displays this info as a visual **Timeline**.

#### 2. Access

1.  Go to **Member List**.
2.  Select a specific Member to open their profile.
3.  Select the **Formation** tab on the profile toolbar.

#### 3. Step-by-Step Instructions

**A. Viewing Formation Timeline**

-   System displays milestones chronologically from past to present.
-   Each Milestone includes: Stage name, Start Date, and Notes.
-   Future events (Planned) will be shown dimmer or marked distinctly.

![Formation Timeline](link-to-image-formation-timeline.png)

**B. Adding a New Milestone**

1.  In the Formation tab, click **Add Milestone**.
2.  A dialog appears, fill in:
    -   **Stage** `*`: Select stage (e.g., Postulancy, Novitiate, First Vows, Final Vows).
    -   **Start Date** `*`: Start date of this stage.
    -   **Notes**: Additional notes (e.g., "Ordained Deacon at...", "Studying Philosophy at...").
3.  Click **Save Milestone** to save.
    -   _💡 Tip: If recording an Ordination, create a milestone event and specify the place of ordination and Bishop's name in the Notes._

**C. Formation Documents Management**

Each formation stage allows attaching documents (e.g., Certificates, Vow Papers, Mission Letters...).

-   **Upload**: Click upload button at the corresponding milestone, select file (PDF, JPG, PNG - Max 5MB) and name the document type.
-   **View/Download**: Click View button to see the list and download.

**D. Other Related Records**

Next to the Formation tab, you can also manage:

1.  **Health Records** (Tab Health):

    -   Store medical info, conditions, or allergies.
    -   Attach medical records/prescriptions.

2.  **Skills & Expertise** (Tab Skills):

    -   Record Pastoral skills (Preaching, Catechism), Practical skills (Cooking, Driving), or Talents (Music, Languages).
    -   Rate proficiency level (Beginner, Intermediate, Advanced).

3.  **Service History** (Tab Service):
    -   Record transfer history and positions held at communities.

---

### 3.4. Financial Management

#### 1. Purpose

This module helps transparently manage expenses for each community and the whole congreagation. The system supports receipt storage, expense categorization, and periodic locking to ensure data integrity.

#### 2. Access Rights

-   **Super Admin / Admin**: Full access to all communities.
-   **Community Manager**: Manage finances only for their own community.
-   **Viewer**: No access (unless granted special permission).

#### 3. Step-by-Step Instructions

**A. Viewing Expenses**

1.  Go to **Financials** menu.
2.  Screen displays list of recent expenses.
    -   **Filters**: Filter by **Category** (Utilities, Food, Medical...), **Date Range**, or view by **Specific Month**.
    -   **Quick Stats**: System displays Total Amount corresponding to selected filters.

![Expenses List](link-to-image-expenses.png)

**B. Recording New Expense**

1.  Click **+ Record Expense**.
2.  Fill details:
    -   **Community** `*`: Select community (if managing multiple).
    -   **Date** `*`: Expense date.
    -   **Category** `*`: Expense type (e.g., Groceries, Utilities, Medical, Repairs...). System suggests common items.
    -   **Amount** `*`: Money amount (Enter number, system formats auto).
    -   **Description** `*`: Detail description.
    -   **Receipt**: Upload image or PDF of original invoice/receipt (Max 10MB).
3.  Click **Record Expense** to save.

**C. Editing & Deleting**

-   **Edit**: Click Pencil icon on the row.
-   **Delete**: Click Trash icon.
-   **⚠️ Important Note**: You **CANNOT** edit or delete expenses that belong to a **Locked Period**. To correct errors in a locked period, contact Super Admin to unlock.

**D. Period Locking**

Feature to finalize monthly accounting data.

1.  In Financial interface, click **Lock Period**.
2.  Select **Community**, **Month**, and **Year** to lock.
3.  System summarizes item count and total amount.
4.  Click **Confirm Lock**.
    -   _After locking, all expenses in that month become "Read-only"._

**E. Monthly Report**

1.  Click **Reports** or **Monthly Report**.
2.  Select Community and Month.
3.  Click **Generate Report**.
4.  Detailed report appears, grouped by Categories.
5.  To print or send, click **Export PDF** at top right.

---

### 3.5. Periodic Events

#### 1. Purpose

This module helps track and remind of recurring events (annual/monthly), e.g., Community Patronal Feast, Foundation Anniversary, Birthdays, or Solemnities.

#### 2. Instructions

**A. Creating Event**

1.  Go to **Events** menu.
2.  Click **Create Periodic Event**.
3.  Fill info:
    -   **Name** `*`: Event name.
    -   **Recurrence** `*`: Frequency (Annual, Monthly, One-time).
    -   **Dates** `*`: Start and End dates.
    -   **Community**: Applicable community (or leave blank for Congregation-wide).
4.  Click **Create Event**.

**B. Tracking Events**

-   System displays upcoming events on main Dashboard or Events module.
-   "Annual" events automatically reappear next year without re-creation.

---

### 3.6. Project Management

#### 1. Purpose

Manage large tasks with specific deadlines and budgets (e.g., Building new house, Lenten Charity Program, General Chapter Organization).

#### 2. Access Rights

-   **Project Manager**: Person in charge.
-   **Project Members**: People assigned to execute tasks.

#### 3. Main Functions

**A. Initiating Project**

1.  Go to **Projects** menu.
2.  Click **Create Project**.
3.  Declare details:
    -   **Project Name** `*`.
    -   **Community**: Lead community.
    -   **Manager**: Select person in charge.
    -   **Status**: (Planned, Active, Completed, Suspended).
    -   **Budget**: Estimated budget.
4.  Click **Create Project**.

**B. Managing Members & Tasks**

In project detail page:

-   **Add Members**: Assign other religious to the team.
-   **Create Tasks**: Divide work, assign deadlines and assignees. View progress as list or Timeline.

-   When recording an **Expense** in Financial module, you can tag it to a specific **Project**.
-   System automatically aggregates and compares **Actual Cost** vs **Budget** to alert overspending.

---

### 3.7. System Administration

#### 1. Audit Logs

System records all critical actions for transparency and data safety.

-   **Access**: Menu **Audit Logs**.
-   **Function**:
    -   View who did what, when (e.g., "Admin A deleted member B at 10:00 AM").
    -   Filter by User, Action Type (Create, Update, Delete) or Date.
    -   **Export Report**: Download "Tamper-Evident Report" PDF for evidence storage.

#### 2. Data Backups

-   **Access**: Menu **Settings > Backups**.
-   **Create Backup**: Click **Create Backup**. System packages all current data into `.sql` file.
-   **Download**: Click filename to download to PC.
-   _Note_: Restore must be done by technician via CLI for safety.

#### 3. System Settings

Allows Admin to change operational parameters:

-   Email (SMTP) info.
-   Default display configurations.

---

## PART 4: FREQUENTLY ASKED QUESTIONS (FAQ)

**Q: I accidentally deleted a member, can I restore them?**
A: Yes. Deleted data remains in database (Soft Delete). Contact Super Admin to restore.

**Q: Why don't I see the "Delete" button for a community?**
A: System prevents deleting communities with active members. Move members elsewhere first.

**Q: Can I use the software on mobile?**
A: Yes. The interface is responsive for phones and tablets.

**Q: How do I change my password?**
A: Click your name/avatar at top right -> Select **Profile**. Find **Update Password** section.

**Q: Is my data backed up?**
A: Yes. System is configured for daily auto-backups. Admin can also manually backup anytime.

**Q: I want to export member list to Excel for printing?**
A: Yes. In **Members** screen, select members (or all), then click **Export** button on toolbar.

---

## PART 5: SUPPORT

If you encounter technical issues or need detailed guidance, please contact:

-   **Congregation Technical Dept**:
    -   Email: `support@congregation.org`
    -   Hotline: `0909.xxx.xxx`
-   **Software Provider**:
    -   Website: `www.software-provider.com`

---

_(End of Document)_
