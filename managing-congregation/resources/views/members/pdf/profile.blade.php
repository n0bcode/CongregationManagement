<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Member Profile - {{ $member->first_name }} {{ $member->last_name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #d4af37; /* Sanctuary Gold */
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #1a202c;
        }
        .header p {
            margin: 5px 0 0;
            color: #718096;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #2f855a; /* Sanctuary Green */
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-grid td {
            padding: 5px;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            color: #4a5568;
            width: 150px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table th, .table td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f7fafc;
            color: #4a5568;
        }
        .timeline-item {
            margin-bottom: 10px;
            padding-left: 15px;
            border-left: 2px solid #cbd5e0;
        }
        .timeline-date {
            font-size: 12px;
            color: #718096;
        }
        .timeline-title {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $member->first_name }} {{ $member->last_name }}</h1>
        @if($member->religious_name)
            <p>Religious Name: {{ $member->religious_name }}</p>
        @endif
        <p>{{ $member->community->name ?? 'No Community' }} | {{ ucfirst($member->status) }}</p>
    </div>

    <div class="section">
        <div class="section-title">Personal Information</div>
        <table class="info-grid">
            <tr>
                <td class="label">Date of Birth:</td>
                <td>{{ $member->dob->format('M d, Y') }} ({{ $member->dob->age }} years)</td>
            </tr>
            <tr>
                <td class="label">Entry Date:</td>
                <td>{{ $member->entry_date ? $member->entry_date->format('M d, Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Email:</td>
                <td>{{ $member->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Phone:</td>
                <td>{{ $member->phone ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Formation Timeline</div>
        @if($member->formationEvents->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Stage</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($member->formationEvents as $event)
                        <tr>
                            <td>{{ $event->started_at->format('M d, Y') }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $event->stage->value ?? $event->stage)) }}</td>
                            <td>{{ $event->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No formation events recorded.</p>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Service History</div>
        @if($member->assignments->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Period</th>
                        <th>Community</th>
                        <th>Role</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($member->assignments as $assignment)
                        <tr>
                            <td>
                                {{ $assignment->start_date->format('M Y') }} - 
                                {{ $assignment->end_date ? $assignment->end_date->format('M Y') : 'Present' }}
                            </td>
                            <td>{{ $assignment->community->name }}</td>
                            <td>{{ $assignment->role ?? '-' }}</td>
                            <td>{{ $assignment->duration_human }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No service history recorded.</p>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Skills</div>
        @if($member->skills->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Skill</th>
                        <th>Proficiency</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($member->skills as $skill)
                        <tr>
                            <td>{{ ucfirst($skill->category) }}</td>
                            <td>{{ $skill->name }}</td>
                            <td>{{ ucfirst($skill->proficiency) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No skills recorded.</p>
        @endif
    </div>
</body>
</html>
