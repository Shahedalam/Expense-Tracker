# 💸 Expense Tracker Application

A simple yet powerful Expense Tracker application to help users track income and expenses, categorize transactions, and manage budgets effectively.

## 📋 Features

- Add, update, and delete income/expense transactions
- Categorize transactions (e.g., Food, Transport, Utilities)
- View monthly summaries
- User authentication and session management

## 🛠️ Tech Stack

- **Backend**: Laravel
- **Database**: MySQL
- **Authentication**: Laravel Sanctum


## 🚀 Getting Started

### Prerequisites

- PHP
- MySQL.
- Git

### API : Postman import and change environment data

- Install Postman
- On workspaces , click import button and drag & drop the Expense Tracker.postman_collection.json file to import area. 
- Click on Expense Tracker, you will see saved requests
- After login/register change Bearer Token from Authorization

### API : Postman ( name, (path, method ) )
- register (/api/v1/auth/register, POST)
- login (/api/v1/auth/login, POST)
- current-user (/api/v1/current-user, GET)
- budget 
  - index (/api/v1/budget ,GET)
  - create  (/api/v1/budget ,POST)
  - show  (/api/v1/budget/{id} ,GET)
  - update  (/api/v1/budget/{id} ,PUT)
  - delete  (/api/v1/budget/{id} ,DELETE)
- expense
  - index (/api/v1/expense ,GET)
  - create  (/api/v1/expense ,POST)
  - show  (/api/v1/expense/{id} ,GET)
  - update  (/api/v1/expense/{id} ,PUT)
  - delete  (/api/v1/expense/{id} ,DELETE)

### Installation

```bash
# Clone the repo
git clone https://github.com/Shahedalam/Expense-Tracker.git

# Navigate into the project directory
cd Expense-Tracker

# Configure environment variables
cp .env.example .env

# Docker Deploy
docker-compose build --no-cache
docker-compose up -d

# Prepare the app
docker exec expense-tracker bash -c "cd /var/www/html"
docker exec expense-tracker bash -c "php artisan migrate --force"

# To clear database and fresh restart the app
docker exec expense-tracker bash -c "php artisan migrate:fresh --force"

# To clear all catch
docker exec expense-tracker bash -c "php artisan optimize:clear"

# To update composer
docker exec expense-tracker bash -c "composer update"
