# Selaju System API

Backend system for Selaju Apps, built with Laravel. This system handles authentication, user management, role-based access control (RBAC), content management, and dynamic menu generation.

## ✨ Features

- **Role-Based Access Control (RBAC)**: Fine-grained permissions for Users, Groups, and Modules.
- **Dynamic Menu System**: Sidebar menus are generated dynamically based on user permissions.
- **Content Management**: Manage articles and categories.
- **API-First Design**: Ready for consumption by frontend applications (Nuxt, Vue, etc.).
- **Secure Authentication**: Token-based authentication (Sanctum).

## 🚀 Getting Started

### Prerequisites

- PHP 8.1+
- Composer
- MySQL

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd selaju-system
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Environment Setup**
   Copy `.env.example` to `.env` and configure your database credentials.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Setup**
   Run migrations and seeders to set up the initial data, including default users, roles, and menus.
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Serve the Application**
   ```bash
   php artisan serve
   ```

## 🔑 Default Credentials

Use the following credentials to log in as a Super Admin:

- **Email**: `dev@gncs.dev`
- **Password**: `programmer123`
- **Group**: Super Admin

## 📡 API Documentation

### Authentication & Menus

- `GET /api/menus/sidebar`
  - **Description**: Main endpoint for fetching the sidebar menu. Returns menus and modules accessible to the authenticated user.
  - **Auth**: Required

### Public Content

- `GET /api/articles` - List all articles
- `GET /api/articles/{slug}` - View article details
- `GET /api/articles/categories` - List article categories

## 🔐 Permission System

The system uses a 3-tier permission structure:
1. **User Groups**: Roles like Super Admin, Admin, Editor.
2. **Modules**: Functional areas of the system (e.g., Dashboard, Articles, Users).
3. **Module Access**: Specific actions within a module (View, Create, Edit, Delete).

### Permission Flow
1. User logs in.
2. System fetches User Group.
3. System retrieves Permissions for that Group.
4. `GET /api/menus/sidebar` filters menus based on these permissions.

## 🎨 Database Structure

- **Menus**: Top-level sidebar items.
- **Modules**: Sub-items or pages.
- **User Groups**: Roles.
- **User Group Permissions**: Mapping between Groups and Module Access.

## 🛠 Tech Stack

- **Framework**: Laravel 10/11
- **Database**: MySQL
- **Auth**: Laravel Sanctum

---
**Status**: ✅ Active Development
