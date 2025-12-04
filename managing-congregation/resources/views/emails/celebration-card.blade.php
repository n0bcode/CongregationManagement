<!DOCTYPE html>
<html>
<head>
    <title>{{ $subjectLine }}</title>
</head>
<body>
    <h1>{{ $subjectLine }}</h1>
    <p>Dear {{ $member->first_name }},</p>
    <p>Please find your celebration card attached.</p>
    <p>Best regards,<br>{{ config('app.name') }}</p>
</body>
</html>
