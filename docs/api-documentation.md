# API Documentation - Congregation Management System

**Last Updated:** 2025-12-27  
**Version:** 1.0  
**Status:** Planned for Q3 2026

---

## Overview

This document outlines the planned REST API for the Congregation Management System. The API will enable external integrations, mobile applications, and inter-congregation data sharing.

**Current Status:** The system currently has internal API endpoints for real-time validation. External API is planned for Q3 2026.

---

## Current Internal APIs

### Validation API

**Endpoint:** `POST /api/validate`  
**Purpose:** Real-time field validation  
**Authentication:** Session-based (authenticated users only)

**Request:**

```json
{
  "field": "email",
  "value": "test@example.com",
  "rules": "required|email|unique:users,email"
}
```

**Response:**

```json
{
  "valid": true,
  "message": null
}
```

**Error Response:**

```json
{
  "valid": false,
  "message": "The email has already been taken."
}
```

---

## Planned External API (Q3 2026)

### Authentication

**Method:** OAuth 2.0 with Laravel Sanctum  
**Token Type:** Bearer tokens  
**Scopes:** Read, Write, Admin

**Obtaining a Token:**

```http
POST /api/v1/auth/token
Content-Type: application/json

{
  "email": "api-user@example.com",
  "password": "secure-password",
  "device_name": "External App"
}
```

**Response:**

```json
{
  "token": "1|abc123...",
  "type": "Bearer",
  "expires_in": 31536000
}
```

**Using the Token:**

```http
GET /api/v1/members
Authorization: Bearer 1|abc123...
```

---

## Planned Endpoints

### Members API

#### List Members

```http
GET /api/v1/members
Authorization: Bearer {token}
```

**Query Parameters:**

- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 20, max: 100)
- `status` - Filter by status (Active, Deceased, Exited, Transferred)
- `community_id` - Filter by community
- `search` - Search by name

**Response:**

```json
{
  "data": [
    {
      "id": 1,
      "religious_name": "Sr. Mary",
      "civil_name": "Mary Smith",
      "status": "Active",
      "community": {
        "id": 1,
        "name": "St. Joseph House"
      },
      "entry_date": "2020-01-01",
      "created_at": "2025-12-01T00:00:00Z",
      "updated_at": "2025-12-27T00:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 150,
    "last_page": 8
  },
  "links": {
    "first": "/api/v1/members?page=1",
    "last": "/api/v1/members?page=8",
    "prev": null,
    "next": "/api/v1/members?page=2"
  }
}
```

#### Get Member

```http
GET /api/v1/members/{id}
Authorization: Bearer {token}
```

**Response:**

```json
{
  "data": {
    "id": 1,
    "religious_name": "Sr. Mary",
    "civil_name": "Mary Smith",
    "date_of_birth": "1990-01-01",
    "status": "Active",
    "community": {
      "id": 1,
      "name": "St. Joseph House",
      "code": "SJH"
    },
    "entry_date": "2020-01-01",
    "formation_events": [
      {
        "id": 1,
        "stage": "Postulancy",
        "date": "2020-01-01"
      },
      {
        "id": 2,
        "stage": "Novitiate",
        "date": "2021-01-01"
      }
    ],
    "assignments": [
      {
        "id": 1,
        "role": "Community Director",
        "community": "St. Joseph House",
        "start_date": "2023-01-01",
        "end_date": null
      }
    ],
    "created_at": "2025-12-01T00:00:00Z",
    "updated_at": "2025-12-27T00:00:00Z"
  }
}
```

#### Create Member

```http
POST /api/v1/members
Authorization: Bearer {token}
Content-Type: application/json

{
  "religious_name": "Sr. Jane",
  "civil_name": "Jane Doe",
  "date_of_birth": "1995-05-15",
  "entry_date": "2024-01-01",
  "status": "Active",
  "community_id": 1
}
```

**Response:**

```json
{
  "data": {
    "id": 151,
    "religious_name": "Sr. Jane",
    "civil_name": "Jane Doe",
    "status": "Active",
    "created_at": "2025-12-27T10:00:00Z"
  },
  "message": "Member created successfully"
}
```

#### Update Member

```http
PUT /api/v1/members/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "status": "Transferred",
  "community_id": 2
}
```

#### Delete Member

```http
DELETE /api/v1/members/{id}
Authorization: Bearer {token}
```

**Response:**

```json
{
  "message": "Member deleted successfully"
}
```

---

### Communities API

#### List Communities

```http
GET /api/v1/communities
Authorization: Bearer {token}
```

**Response:**

```json
{
  "data": [
    {
      "id": 1,
      "name": "St. Joseph House",
      "code": "SJH",
      "patron_saint": "St. Joseph",
      "feast_day": "03-19",
      "foundation_date": "1950-01-01",
      "member_count": 25,
      "created_at": "2025-12-01T00:00:00Z"
    }
  ]
}
```

---

### Financial API

#### List Expenses

```http
GET /api/v1/expenses
Authorization: Bearer {token}
```

**Query Parameters:**

- `community_id` - Filter by community
- `month` - Filter by month (1-12)
- `year` - Filter by year
- `category` - Filter by category

**Response:**

```json
{
  "data": [
    {
      "id": 1,
      "amount": 150.0,
      "category": "Food",
      "description": "Groceries",
      "date": "2025-12-15",
      "community": {
        "id": 1,
        "name": "St. Joseph House"
      },
      "receipt_url": "/storage/receipts/abc123.pdf",
      "created_at": "2025-12-15T10:00:00Z"
    }
  ]
}
```

#### Create Expense

```http
POST /api/v1/expenses
Authorization: Bearer {token}
Content-Type: multipart/form-data

amount: 150.00
category: Food
description: Groceries
date: 2025-12-15
community_id: 1
receipt: [file]
```

---

### Reports API

#### Generate Report

```http
POST /api/v1/reports/generate
Authorization: Bearer {token}
Content-Type: application/json

{
  "type": "demographic",
  "format": "pdf",
  "filters": {
    "community_id": 1,
    "status": "Active"
  }
}
```

**Response:**

```json
{
  "report_id": "rep_abc123",
  "status": "processing",
  "download_url": null,
  "estimated_completion": "2025-12-27T10:05:00Z"
}
```

#### Check Report Status

```http
GET /api/v1/reports/{report_id}
Authorization: Bearer {token}
```

**Response:**

```json
{
  "report_id": "rep_abc123",
  "status": "completed",
  "download_url": "/api/v1/reports/rep_abc123/download",
  "expires_at": "2025-12-28T10:00:00Z"
}
```

#### Download Report

```http
GET /api/v1/reports/{report_id}/download
Authorization: Bearer {token}
```

**Response:** Binary file (PDF, Excel, DOCX)

---

### Webhooks (Planned)

#### Register Webhook

```http
POST /api/v1/webhooks
Authorization: Bearer {token}
Content-Type: application/json

{
  "url": "https://your-app.com/webhook",
  "events": ["member.created", "member.updated", "expense.created"],
  "secret": "your-webhook-secret"
}
```

**Response:**

```json
{
  "id": "wh_abc123",
  "url": "https://your-app.com/webhook",
  "events": ["member.created", "member.updated", "expense.created"],
  "active": true,
  "created_at": "2025-12-27T10:00:00Z"
}
```

#### Webhook Payload Example

```json
{
  "id": "evt_abc123",
  "type": "member.created",
  "created_at": "2025-12-27T10:00:00Z",
  "data": {
    "id": 151,
    "religious_name": "Sr. Jane",
    "status": "Active"
  }
}
```

---

## Error Handling

### Standard Error Response

```json
{
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "The given data was invalid.",
    "details": {
      "religious_name": ["The religious name field is required."],
      "date_of_birth": ["The date of birth must be a valid date."]
    }
  }
}
```

### HTTP Status Codes

| Code | Meaning                              |
| ---- | ------------------------------------ |
| 200  | Success                              |
| 201  | Created                              |
| 204  | No Content (successful deletion)     |
| 400  | Bad Request                          |
| 401  | Unauthorized (invalid/missing token) |
| 403  | Forbidden (insufficient permissions) |
| 404  | Not Found                            |
| 422  | Validation Error                     |
| 429  | Too Many Requests (rate limit)       |
| 500  | Internal Server Error                |

---

## Rate Limiting

**Limits:**

- **Authenticated:** 1000 requests per hour
- **Unauthenticated:** 60 requests per hour

**Headers:**

```
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999
X-RateLimit-Reset: 1640000000
```

**Rate Limit Exceeded Response:**

```json
{
  "error": {
    "code": "RATE_LIMIT_EXCEEDED",
    "message": "Too many requests. Please try again later.",
    "retry_after": 3600
  }
}
```

---

## Pagination

All list endpoints support pagination:

**Query Parameters:**

- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 20, max: 100)

**Response Structure:**

```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 150,
    "last_page": 8
  },
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  }
}
```

---

## Filtering & Sorting

**Filtering:**

```http
GET /api/v1/members?status=Active&community_id=1
```

**Sorting:**

```http
GET /api/v1/members?sort=religious_name&order=asc
```

**Multiple Filters:**

```http
GET /api/v1/members?status=Active&community_id=1&sort=entry_date&order=desc
```

---

## Versioning

**Current Version:** v1  
**Base URL:** `/api/v1/`

**Version in URL:**

```http
GET /api/v1/members
```

**Future versions will use:**

```http
GET /api/v2/members
```

---

## SDK & Client Libraries (Planned)

### Official SDKs

- **PHP:** `congregation/api-client-php`
- **JavaScript:** `@congregation/api-client-js`
- **Python:** `congregation-api-client`

### Example Usage (JavaScript)

```javascript
import { CongregationAPI } from "@congregation/api-client-js";

const api = new CongregationAPI({
  baseURL: "https://your-instance.com/api/v1",
  token: "your-api-token",
});

// List members
const members = await api.members.list({
  status: "Active",
  page: 1,
});

// Create member
const newMember = await api.members.create({
  religious_name: "Sr. Jane",
  civil_name: "Jane Doe",
  status: "Active",
});
```

---

## Security Best Practices

### For API Consumers

1. **Store tokens securely** - Never commit tokens to version control
2. **Use HTTPS only** - All API calls must use HTTPS
3. **Rotate tokens regularly** - Regenerate tokens every 90 days
4. **Validate webhook signatures** - Verify webhook payloads
5. **Handle rate limits gracefully** - Implement exponential backoff

### For API Providers

1. **Token encryption** - Tokens stored encrypted in database
2. **IP whitelisting** - Optional IP restriction for API access
3. **Audit logging** - All API calls logged for security review
4. **CORS configuration** - Strict CORS policy
5. **Input validation** - All inputs validated and sanitized

---

## Testing

### Postman Collection

Download the Postman collection: [congregation-api.postman_collection.json](./postman/congregation-api.postman_collection.json)

### Example cURL Requests

**Get Members:**

```bash
curl -X GET "https://your-instance.com/api/v1/members" \
  -H "Authorization: Bearer your-token" \
  -H "Accept: application/json"
```

**Create Member:**

```bash
curl -X POST "https://your-instance.com/api/v1/members" \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{
    "religious_name": "Sr. Jane",
    "civil_name": "Jane Doe",
    "status": "Active",
    "community_id": 1
  }'
```

---

## Support

**API Issues:** [GitHub Issues](https://github.com/your-org/managing-congregation/issues)  
**Email:** api-support@your-domain.com  
**Documentation:** https://api-docs.your-domain.com

---

## Changelog

### v1.0 (Planned Q3 2026)

- Initial API release
- OAuth 2.0 authentication
- Members, Communities, Financial endpoints
- Webhook support
- Rate limiting

---

**Document Status:** 🚧 Planned  
**Implementation:** Q3 2026  
**Maintained By:** API Team  
**Review Frequency:** Quarterly
