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

![image](https://github.com/user-attachments/assets/ccd6e11d-36bf-421d-b5f6-f4345526e988)

![image](https://github.com/user-attachments/assets/f880a648-4eec-4408-89c0-6a135f13c3fa)

![image](https://github.com/user-attachments/assets/25f0beb7-492b-4f47-9b0e-c21d7364f9c3)

![image](https://github.com/user-attachments/assets/1161935d-9c48-44fe-8e79-e45c08d32ea5)

![image](https://github.com/user-attachments/assets/e2f8c590-379a-4abd-8b02-2b63941469ca)

![image](https://github.com/user-attachments/assets/bde3fcf7-be24-46f8-8494-6ca88df3b737)

![image](https://github.com/user-attachments/assets/8b69ee8d-688c-477c-8c24-5777f4426a49)

![image](https://github.com/user-attachments/assets/63bc91c8-651c-4a93-989e-853b2c7b931d)

![image](https://github.com/user-attachments/assets/f71439c5-1d06-453c-baad-e6662e3deebb)

![image](https://github.com/user-attachments/assets/f4e105ed-fdff-4417-9948-73e2d11db632)

![image](https://github.com/user-attachments/assets/03737cdf-9132-4ecb-a0de-f8854fccebd1)

![image](https://github.com/user-attachments/assets/2beb72ef-53b4-43e7-9268-3413a7cdd934)

![image](https://github.com/user-attachments/assets/1e3a369a-5cd8-4f3f-8be6-74ca55759303)

![image](https://github.com/user-attachments/assets/8ae9d536-5faa-477a-80e1-9f9e7f12f885)

![image](https://github.com/user-attachments/assets/f63add85-07a8-463a-8561-aeaf06796c75)
