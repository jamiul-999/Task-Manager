# Task Manager

## Overview
- Log in and create tasks.
- Set deadline
- Edit tasks
- Share your progress on Facebook

## Setup & Installation
### Install Xampp
Go to: 
>https://www.apachefriends.org/

Install Xampp and Go to Xampp Control Panel

## Create a Database
After Installing:
- Turn on Apache server and MySQL

![alt text](image.png)

- Go to the admin section in MySQL
- Create a new database called "task_manager" and select collation type "utf8_general_ci"
- Now go to the SQL section of your new database.

![alt text](image-1.png)
- Now execute the following SQL command

```sql
-- Database: task_manager
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    facebook_token VARCHAR(255),
    google_token VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    due_date DATE,
    is_completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```
Now the database has been created.

## Run the application
- Clone the repository. Now go to the following URL:
> http://localhost/TASK_MANAGER/controllers/auth/login.php
- Now Log in:
> User: Test  
> Password: password

- Now you can use the application.

## API Documentation
### Authentication
#### `POST /api/auth`
#### Request
```json
{
  "username": "test",
  "password": "password"
}
```
#### Response
```JSON
{
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoxLCJleHAiOjE3NDMzNTM5Njh9.69bacd32a2fcca5f37d0ae13544a97583a35e9af136ec22005e9b6d71b61a1c5",
    "user_id": 1
}
```
![alt text](image-2.png)

### Task Endpoints
#### 1. Get All Tasks `GET /api/tasks`

#### Headers:
```json
Authorization: Bearer <token>
```
#### Response:
```json
[
    {
        "id": 3,
        "user_id": 1,
        "title": "Task 1",
        "description": "This is task 1",
        "due_date": "2025-04-03",
        "is_completed": 0,
        "created_at": "2025-03-30 20:40:51"
    }
]
```
![alt text](image-3.png)

#### 2. Create Task `POST /api/tasks`
#### Headers
```json
Authorization: Bearer <token>
Content-Type: application/json
```
#### Request Body:
```JSON
{
  "title": "Task 2",
  "description": "From Postman",
  "due_date": "2025-04-31"
}
```
#### Response:
```json
{
    "message": "Task created"
}
```
![alt text](image-4.png)