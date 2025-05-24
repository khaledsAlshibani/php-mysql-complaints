# Complaints Application

A web application for managing complaints and suggestions with role-based access control, built using PHP 8 for the backend and Next.js for the frontend.

> Application is not ready for production!

## Architecture Overview

### Backend (PHP 8 + MySQL)

The backend follows a service-oriented architecture and is built without any framework.

* **Technology Stack**:

  * PHP 8.2
  * MySQL
  * Phinx (for database migrations and seeding)
  * Docker (for local development and database setup)

### Frontend (Next.js + TypeScript)

* **Technology Stack**:

  * Next.js 14
  * TypeScript
  * Tailwind CSS
  * Shadcn UI

## Core Features

* User Account Management
* Complaints Submission
* Suggestions Submission
* Admin Feedback Submission

## Getting Started

### Prerequisites

#### Backend

* PHP 8.2 (CLI)
* Composer 2.7.2 or higher
* Docker (for MySQL container)

#### Frontend

* Node.js 20 or later
* pnpm (recommended) or npm

---

## Installation & Setup

### 1. Backend Setup

```bash
# Navigate to backend directory
cd backend

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Start MySQL database with Docker
docker compose up -d

# Run migrations and seeders
php scripts/setup.php

# Start the development server (http://localhost:8000)
php scripts/serve.php
```

### 2. Frontend Setup

```bash
# Navigate to frontend directory
cd frontend

# Install Node dependencies
pnpm install

# Copy environment file
cp .env.example .env.local

# Start the frontend dev server (http://localhost:3000)
pnpm dev
```