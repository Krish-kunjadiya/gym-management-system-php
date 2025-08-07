# [Gym Management System](http://synxdatagen.netlify.app)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)

A comprehensive Gym Management System built from scratch using Core PHP, MySQLi, and Bootstrap. This application provides a centralized and efficient platform for managing gym operations, with distinct roles and dashboards for administrators, staff, and members.

---

## 📸 Screenshots

| Login Page |  Public Signup |
| :---: | :---: |
| ![Screenshot 2025-07-01 000648](https://github.com/user-attachments/assets/c2b6cb06-adbe-4ed3-8470-809e443bfccf) | ![Screenshot 2025-07-01 000744](https://github.com/user-attachments/assets/46aebee5-4cfc-4911-b3d7-5fafd8bce5ee) |

| Member Dashboard | 
| :---: |
| ![localhost_kvscode_gym-management-system_member-dashboard php](https://github.com/user-attachments/assets/23f8a22a-5871-47ed-ac6e-0628c9cabe8d) |

| Admin Dashboard |
| :---: |
| ![Screenshot 2025-07-01 001429](https://github.com/user-attachments/assets/8f2de4e2-38dc-4cdb-a164-4138fa4bdb50) |

| Member Management |
| :---: |
| ![Screenshot 2025-07-01 001455](https://github.com/user-attachments/assets/e374ab8b-7a8d-4145-8005-cdfc5b54c42b) |
---

## ✨ Features

-   **Role-Based Access Control:** Three distinct user roles with different permissions.
    -   👤 **Admin:** Full control over the system. Manages staff, members, plans, and views reports.
    -   👨‍💼 **Staff:** Can manage member profiles and track daily attendance.
    -   💪 **Member:** Can view their profile, membership status, and attendance history.
-   **Secure Authentication:** Passwords are securely hashed using `password_hash()` and verified with `password_verify()`.
-   **Admin Dashboard:** A central hub with summary cards for key metrics (Total Members, Revenue, etc.).
-   **Member Management:** Full CRUD (Create, Read, Update, Delete) functionality for managing members.
-   **Staff Management:** Admins can add, view, and manage staff accounts.
-   **Membership Plan Management:** Admins can create and manage different membership plans (e.g., Monthly, Quarterly).
-   **Attendance Tracking:** Staff can mark member attendance, which is viewable by both admin and the member.
-   **Public Member Signup:** A secure, public-facing form allows new members to create an account.

---

## 🛠️ Technology Stack

-   **Frontend:** HTML5, CSS3, Bootstrap 5
-   **Backend:** Core PHP
-   **Database:** MySQL
-   **Server:** Apache (run via XAMPP/WAMP)

---

## 🚀 Setup and Installation

Follow these steps to set up the project on your local machine.

### 1. Prerequisites
-   A local server environment like [XAMPP](https://www.apachefriends.org/index.html) or WAMP. Make sure you have Apache and MySQL services running.

### 2. Clone the Repository
```bash
git clone https://github.com/Krish-kunjadiya/gym-management-system-php.git
cd gym-management-system-php
```

