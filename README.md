<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Task Manager API

This is a simple API to manage tasks with CRUD operations.

## Base URL
https://api.taskmanager.com/v1

Authorization: Bearer <jwt_token>
Content-Type: application/json


## Common Response Format
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {},
  "timestamp": "2025-07-13T10:30:00Z"
}

## Error Response Format
```json
{
  "success": false,
  "message": "Error description",
  "error_code": "ERROR_CODE",
  "timestamp": "2025-07-13T10:30:00Z"
}

## POST /auth/register
Request:
```json
{
  "name": "Sayuni Kavinhara",
  "email": "sayuni@example.com",
  "password": "SecurePass123!",
  "password_confirmation": "SecurePass123!"
}

Response:
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "Sayuni Kavinhara",
      "email": "sayuni@example.com",
      "created_at": "2025-07-13T10:30:00Z"
    },
    "token": "jwt_token_here"
  }
}

## POST /auth/login
Request:
```json
{
  "email": "sayuni@example.com",
  "password": "SecurePass123!"
}

Response:
``json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Sayuni Kavinhara",
      "email": "sayuni@example.com"
    },
    "token": "jwt_token_here",
    "expires_at": "2025-07-14T10:30:00Z"
  }
}

## POST /auth/logout
Headers: Authorization: Bearer <token>

Response:
```json
{
  "success": true,
  "message": "Logged out successfully"
}

##POST /auth/refresh
Headers: Authorization: Bearer <token>

Response:
```json:
{
  "success": true,
  "message": "Token refreshed",
  "data": {
    "token": "new_jwt_token_here",
    "expires_at": "2025-07-14T10:30:00Z"
  }
}

# Task Manager Endpoints

## GET/tasks
Headers: Authorization: Bearer <token>

Query Parameters:

status (optional): pending, completed
priority (optional): low, medium, high
page (optional): Page number (default: 1)
limit (optional): Items per page (default: 10)
search (optional): Search in title and description

Response:
```json
{
  "success": true,
  "message": "Tasks retrieved successfully",
  "data": {
    "tasks": [
      {
        "id": 1,
        "title": "Complete project documentation",
        "description": "Write comprehensive API documentation",
        "status": "pending",
        "priority": "high",
        "due_date": "2025-07-16",
        "created_at": "2025-07-13T10:30:00Z",
        "updated_at": "2025-07-13T10:30:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 10,
      "total": 1,
      "total_pages": 1
    },
    "statistics": {
      "total_tasks": 1,
      "completed_tasks": 0,
      "pending_tasks": 1,
      "high_priority_tasks": 1
    }
  }
}

## GET /tasks/{id}
Headers: Authorization: Bearer <token>
Response:
```json
{
  "success": true,
  "message": "Task retrieved successfully",
  "data": {
    "task": {
      "id": 1,
      "title": "Complete project documentation",
      "description": "Write comprehensive API documentation",
      "status": "pending",
      "priority": "high",
      "due_date": "2025-07-16",
      "created_at": "2025-07-13T10:30:00Z",
      "updated_at": "2025-07-13T10:30:00Z"
    }
  }
}

## POST /tasks
Headers: Authorization: Bearer <token>
Request:
```json
{
  "title": "Complete project documentation",
  "description": "Write comprehensive API documentation",
  "priority": "high",
  "due_date": "2025-07-16"
}

Response:
```json
{
  "success": true,
  "message": "Task created successfully",
  "data": {
    "task": {
      "id": 1,
      "title": "Complete project documentation",
      "description": "Write comprehensive API documentation",
      "status": "pending",
      "priority": "high",
      "due_date": "2025-07-16",
      "created_at": "2025-07-13T10:30:00Z",
      "updated_at": "2025-07-13T10:30:00Z"
    }
  }
}


## DELETE /tasks/{id}
Headers: Authorization: Bearer <token>

Response:
