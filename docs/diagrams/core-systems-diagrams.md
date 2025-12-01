# Core System Diagrams

## 1. Member Management System

### 1.1 Member Lifecycle Flowchart

**Type:** Flowchart
**Description:** The journey of a member from entry to final vows or exit.

```mermaid
flowchart TD
    Start([Entry]) --> Postulancy[Postulancy Stage]
    Postulancy --> Novitiate[Novitiate Stage]
    Novitiate --> FirstVows[First Vows - Temporary]

    FirstVows --> Renewal{Renewal Needed?}
    Renewal -- Yes --> RenewVows[Renew Temporary Vows]
    RenewVows --> FirstVows

    Renewal -- No (Ready) --> FinalVows[Perpetual Vows]

    subgraph Exits
        Postulancy -.-> Exit[Exit Congregation]
        Novitiate -.-> Exit
        FirstVows -.-> Exit
        FinalVows -.-> Death([Deceased])
    end

    FinalVows --> ActiveService[Active Ministry]
    ActiveService --> Retirement[Retirement]
    Retirement --> Death
```

### 1.2 Member Transfer Sequence

**Type:** Sequence Diagram
**Description:** The process of transferring a member from one community to another.

```mermaid
sequenceDiagram
    actor Admin as Super Admin
    participant UI as System Interface
    participant MemberSvc as Member Service
    participant AssignSvc as Assignment Service
    participant DB as Database

    Admin->>UI: Select Member & Click "Transfer"
    UI->>Admin: Show Transfer Form (New House, Date)
    Admin->>UI: Select "House of Bethany" & Submit

    UI->>MemberSvc: Initiate Transfer Request
    MemberSvc->>DB: Get Current Assignment
    DB-->>MemberSvc: Return "House of Loreto"

    MemberSvc->>AssignSvc: Create New Assignment
    AssignSvc->>DB: Insert into 'assignments' (New House, Start Date)
    AssignSvc->>DB: Update Old Assignment (Set End Date)

    MemberSvc->>DB: Update Member 'community_id'

    DB-->>MemberSvc: Success
    MemberSvc-->>UI: Transfer Complete
    UI-->>Admin: Show Updated Profile & History
```

### 1.3 Member Domain Class Diagram

**Type:** Class Diagram
**Description:** Core entities for member management.

```mermaid
classDiagram
    class Member {
        +String religiousName
        +String civilName
        +Date dob
        +String status
        +transfer(Community newHouse)
    }

    class Community {
        +String name
        +String location
        +User director
    }

    class FormationStage {
        +String stageName
        +Date startDate
        +Date endDate
        +String comments
    }

    class Assignment {
        +Date startDate
        +Date endDate
        +String role
    }

    Member "1" --> "1" Community : belongs_to
    Member "1" *-- "*" FormationStage : has_history
    Member "1" *-- "*" Assignment : service_history
```

---

## 2. Financial Stewardship System

### 2.1 Monthly Reporting Cycle

**Type:** State Diagram
**Description:** The lifecycle of a monthly financial report.

```mermaid
stateDiagram-v2
    [*] --> Open : Month Starts

    state Open {
        [*] --> Collecting
        Collecting --> ExpenseEntry : Director Adds Expense
        ExpenseEntry --> Collecting
    }

    Open --> Draft : Month Ends (Auto-Trigger)

    state Draft {
        [*] --> Reviewing
        Reviewing --> Adjustment : Discrepancy Found
        Adjustment --> Reviewing
        Reviewing --> Submitting : Director Clicks Submit
    }

    Submitting --> Submitted : Lock Records
    Submitted --> Audited : Treasurer Approves
    Audited --> [*]
```

### 2.2 Expense Entry Sequence

**Type:** Sequence Diagram
**Description:** Adding an expense with an optional receipt upload.

```mermaid
sequenceDiagram
    actor Director as Community Director
    participant UI as Mobile Interface
    participant FinanceSvc as Financial Service
    participant FileSvc as File Storage
    participant DB as Database

    Director->>UI: Open "Add Expense"
    Director->>UI: Enter Details (Food, $150, Market)

    opt Receipt Upload (> Threshold)
        Director->>UI: Snap Photo of Receipt
        UI->>FileSvc: Upload Image
        FileSvc-->>UI: Return Secure URL
    end

    Director->>UI: Save Expense
    UI->>FinanceSvc: Create Expense Record
    FinanceSvc->>DB: Insert Row (with Receipt URL if any)
    DB-->>FinanceSvc: Success
    FinanceSvc-->>UI: Update Ledger View
    UI-->>Director: Show "Saved" Animation
```

---

## 3. Strategic Oversight System

### 3.1 Vow Expiry Alert Logic

**Type:** Sequence Diagram
**Description:** Automated background process to detect and alert on expiring vows.

```mermaid
sequenceDiagram
    participant Cron as Daily Scheduler
    participant AlertSvc as Alert Service
    participant DB as Database
    participant Dashboard as Generalate Dashboard

    Cron->>AlertSvc: Run 'CheckExpiringVows' Job
    AlertSvc->>DB: Query Members (VowEndDate <= Today + 30 Days)
    DB-->>AlertSvc: Return List [Sr. Agnes, Sr. Cecilia]

    loop For Each Member
        AlertSvc->>DB: Check if Alert Already Exists
        alt No Alert
            AlertSvc->>DB: Create 'Critical Alert' Record
            AlertSvc->>DB: Send Email Notification (Optional)
        end
    end

    Note right of Dashboard: Later, when General Secretary logs in...

    Dashboard->>DB: Fetch Active Alerts
    DB-->>Dashboard: Return Alerts
    Dashboard-->>User: Display Red Warning Card
```
