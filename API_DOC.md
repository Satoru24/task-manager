# Task Manager API Documentation

## Authentication Endpoints

---

### Register User
- URL: `/api/register`
- Method: `POST`
- Auth required:
- Request Body:
```json
{
    "name": "Sayu Kavin",
    "email": "sayu.kavin@gmail.com",
    "password": "yourpassword123",
    "password_confirmation": "yourpassword123"
}
- Response:
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "Sayu Kavin",
        "email": "sayu.kavin@gmail.com"
    },
    "token": "JWT_TOKEN"
}

### Login User
-URL: `/api/login`
-Method: `POST`
-Auth required: 
-Request Body:
{
    "email": "sayu.kavin@gmail.com",
    "password": "yourpassword123"
}

-Response:
{
    "message": "Login successful",
    "token": "JWT_TOKEN"
}

## Task Manager Endpoints

## Get All Tasks
- URL: `/api/tasks`
- Method: `GET`
- Header: Authorization: Bearer <token>
- Response:
[
    {
        "id": 1,
        "title": "Complete API Docs",
        "description": "Document all API endpoints",
        "priority": "high",
        "completed": false,
        "due_date": "2025-07-20"
    }
]

## Create Task
-URL: `/api/tasks`
-Method: `POST`
-Header: Authorization: Bearer <token>
-Request Body:
{
    "title": "Complete API Docs",
    "description": "Document all API endpoints",
    "priority": "high",
    "due_date": "2025-07-20"
}

-Response:
{
    "message": "Task created successfully",
    "task": { ... }
}

## Update Task
-URL: `/api/tasks/{id}`
-Method: `PUT`
-Header: Authorization: Bearer <token>
-Request Body:
{
    "title": "Update API Docs",
    "completed": true
}

-Response:
{
    "message": "Task updated successfully",
    "task": { ... }
}

## Delete Task
-URL: `/api/tasks/{id}`
-Method: `DELETE`
-Header: Authorization: Bearer <token>
-Response:
{
    "message": "Task deleted successfully"
}

## Database Structure

-Users Table

| Field      | Type     |
| ---------- | -------- |
| id         | integer  |
| name       | string   |
| email      | string   |
| password   | string   |
| timestamps | datetime |

-Tasks Table

| Field       | Type                     |
| ----------- | ------------------------ |
| id          | integer                  |
| user\_id    | integer                  |
| title       | string                   |
| description | text                     |
| priority    | enum (low, medium, high) |
| completed   | boolean                  |
| due\_date   | date                     |
| timestamps  | datetime                 |

-Tokens Table

| Field       | Type     |
| ----------- | -------- |
| id          | integer  |
| user\_id    | integer  |
| token       | string   |
| created\_at | datetime |
| expires\_at | datetime |









