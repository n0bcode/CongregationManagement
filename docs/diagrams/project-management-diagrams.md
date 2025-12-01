# Project Management Diagrams

## 1. Project Lifecycle Flowchart

**Type:** Flowchart
**Description:** High-level process from project creation to closing and auditing.

```mermaid
flowchart TD
    Start([Start]) --> CreateProj[Create Project]
    CreateProj --> Plan[Develop Plan]

    subgraph Planning
        Plan --> AIValidate[AI Validate Plan]
        AIValidate -- Issues --> Plan
        AIValidate -- Approved --> DefineScope[Define Duration & Target]
        DefineScope --> SetBudget[Set Budget & Funding]
        SetBudget --> AssignRoles[Assign PM, SM, Dev]
        AssignRoles --> AIDemo[AI Generate Demo/Visuals]
    end

    AssignRoles --> Execution{Execution Phase}

    subgraph Execution_Monitoring
        Execution -->|Track| Monitor[Monitor Progress]
        Execution -->|Work| Upload[Upload Evidence]
        Upload -->|Docs/Images| StoreEvidence[(Evidence Store)]
        Upload -->|Invoices| RecordExpense[Record Expense]
    end

    Monitor --> IsComplete{Project Complete?}
    IsComplete -- No --> Execution
    IsComplete -- Yes --> Close[Initiate Closing]

    subgraph Closing_Logic
        Close --> CalcBalance[Calculate Financial Balance]
        CalcBalance --> Check{Budget vs Actual?}
        Check -- "Mismatch (e.g. 70k < 100k)" --> Warn[Warning: Budget Imbalance]
        Warn --> Justify[Require Justification/Refund]
        Justify --> Finalize
        Check -- "Balanced" --> Finalize[Finalize Project]
    end

    Finalize --> GenReport[Generate Final Report]
    GenReport --> AuditReady[Ready for Audit]
    AuditReady --> End([End])
```

## 2. Project Closing Sequence

**Type:** Sequence Diagram
**Description:** Detailed interaction for the "Closing Project" feature with balance check logic.

```mermaid
sequenceDiagram
    actor PM as Project Manager
    participant UI as System Interface
    participant Logic as Project Service
    participant DB as Database

    PM->>UI: Request to Close Project
    UI->>Logic: Validate Closing Conditions
    Logic->>DB: Fetch Project Budget
    Logic->>DB: Fetch Total Expenses
    DB-->>Logic: Return Data (Budget: 100k, Actual: 70k)

    Logic->>Logic: Compare (Budget - Actual)

    alt Significant Discrepancy
        Logic-->>UI: Return Warning (Imbalance Detected)
        UI-->>PM: Show Alert: "Remaining Balance 30k. Justification Required."
        PM->>UI: Provide Justification / Refund Proof
        UI->>Logic: Submit Closing with Justification
    else Balanced
        Logic-->>UI: Ready to Close
        UI-->>PM: Confirm Closing
    end

    PM->>UI: Confirm Final Closure
    UI->>Logic: Execute Close Project
    Logic->>DB: Update Status to 'Closed'
    Logic->>DB: Save Final Report Data
    Logic-->>UI: Success Message
    UI-->>PM: Display Final Report Link
```

## 3. AI-Assisted Planning Sequence

**Type:** Sequence Diagram
**Description:** AI agent assisting in plan validation and demo generation.

```mermaid
sequenceDiagram
    actor PM as Project Manager
    participant UI as System Interface
    participant AI as AI Agent
    participant DB as Database

    PM->>UI: Draft Project Plan (Scope, Budget)
    UI->>AI: Request Plan Validation
    AI->>AI: Analyze Feasibility & Risks
    AI-->>UI: Return Validation Report (Score: 85%)

    alt Critical Issues Found
        UI-->>PM: Show Warnings & Suggestions
        PM->>UI: Revise Plan
        UI->>AI: Re-validate
    else Plan Approved
        UI-->>PM: Plan Validated
    end

    PM->>UI: Request Project Demo
    UI->>AI: Generate Visual Demo/Prototype
    AI->>AI: Create Mockups/Charts
    AI-->>UI: Return Demo Assets
    UI-->>PM: Display Project Demo
    PM->>UI: Approve & Finalize Plan
    UI->>DB: Save Project & Plan
```

## 3. Project Domain Class Diagram

**Type:** Class Diagram
**Description:** Structural model of the Project Management module.

```mermaid
classDiagram
    class Project {
        +String id
        +String name
        +String description
        +Date startDate
        +Date endDate
        +String targetAudience
        +String status
        +calculateBalance()
        +close()
    }

    class Plan {
        +String deploymentStrategy
        +String timeline
        +List milestones
    }

    class Budget {
        +Float totalAmount
        +String currency
        +String fundingSource
    }

    class Expense {
        +Float amount
        +Date date
        +String category
        +String invoiceId
    }

    class Evidence {
        +String type (Image/Doc)
        +String url
        +Date uploadDate
    }

    class User {
        +String role (PM, BM, Staff, Auditor)
        +String name
    }

    class AIService {
        +validatePlan(Plan p)
        +generateDemo(Project p)
        +analyzeRisks(Plan p)
    }

    Project "1" *-- "1" Plan
    Project "1" *-- "1" Budget
    Project "1" *-- "*" Expense
    Project "1" *-- "*" Evidence
    Project "*" -- "*" User : assigned_to
    Project "1" ..> "1" AIService : uses_support
```

## 4. Project Status State Diagram

**Type:** State Diagram
**Description:** Lifecycle states of a project entity.

```mermaid
stateDiagram-v2
    [*] --> Draft
    Draft --> Planned : Plan Approved
    Planned --> InProgress : Start Execution

    state InProgress {
        [*] --> Monitoring
        Monitoring --> EvidenceCollection
        EvidenceCollection --> Monitoring
    }

    InProgress --> Closing : Work Completed

    state Closing {
        [*] --> BalanceCheck
        BalanceCheck --> Warning : Discrepancy Found
        Warning --> Justified : Explanation Provided
        BalanceCheck --> Verified : Balanced
        Justified --> Verified
    }

    Verified --> Closed : Finalize
    Closed --> Audited : Audit Complete
    Audited --> [*]
```
