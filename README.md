<div align="center">

# Employee Performance Management System
### *A Unified Workspace for HR, Managers, and Staff*

[![PHP](https://img.shields.io/badge/PHP-8%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=flat-square&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![Status](https://img.shields.io/badge/Status-Production%20Ready-success?style=flat-square)]()

**A full-stack workforce management platform serving HR, Managers, and Staff — built for real-world deployment at Jarrlix Holdings.**  
Role-based dashboards · Automated workflows · Real-time communication · Facility management

</div>

---

## Overview

The **Employee Performance Management System (EPMS)** is a production-grade, full-stack web application that brings HR Administrators, Managers, and Staff onto a single, centralized platform. Deployed for **Jarrlix Holdings**, EPMS gives each role a purpose-built dashboard and workflow — so every level of the organization can manage their responsibilities without friction.

The system addresses the operational bottlenecks common in mid-sized organizations: approval delays, siloed communication, untracked attendance, and reactive facility management. EPMS solves all of these through automation and role-based access control.

---

## Key Features

| Feature | Description |
|---|---|
| **Role-Based Dashboards** | Separate, purpose-built interfaces for HR, Managers, and Staff |
| **Leave Management** | Digital leave requests with real-time approval tracking |
| **Internal Messaging** | Cross-role communication with file attachment support |
| **Issue & Facility Tracking** | Report and route IT, maintenance, and security issues by department |
| **Attendance System** | Employee clock-in/clock-out with exportable attendance reports |
| **Performance Feedback** | Structured feedback submission and review between roles |
| **Employee Profiles** | Centralized profiles with photo upload and personal details |
| **Responsive UI** | Fully functional on desktop and mobile browsers |

---

## System Architecture

```
EPMS/
│
├── Auth/
│   ├── logIn.php                    # Entry point with session management
│   └── register.php
│
├── Dashboards/
│   ├── HR.php                       # HR admin overview + KPIs
│   ├── manager.php                  # Manager team overview
│   └── other_staff.php              # Staff self-service dashboard
│
├── Modules/
│   ├── Leave Management/            # Request, review, and approve leave
│   ├── Messaging/                   # Internal messaging per role
│   ├── Issue Tracking/              # Report, assign, and track facility issues
│   ├── Employee Management/         # Directory, profiles, HR records
│   ├── Attendance/                  # Clock-in/out and reporting
│   └── Feedback/                    # Performance feedback workflows
│
└── Config/
    └── NewDbConn.php                # PDO database connection
```

---

## Tech Stack

| Layer | Technology |
|---|---|
| **Frontend** | HTML5, CSS3, Bootstrap 5, JavaScript, FontAwesome |
| **Backend** | PHP 8+, Session Management, PDO/MySQLi |
| **Database** | MySQL (via phpMyAdmin) |
| **Server** | Apache (XAMPP) |
| **Extras** | File upload handling, Facial Recognition *(optional module)* |

---

## Screenshots

### Login

| Overview |
|---|
| ![Login](App%20pictures/Capture2.PNG) |

### HR

| HR Dashboard|
|---|
| ![HR Dashboard](App%20pictures/CaptureH1.PNG) |

 |HR Profile|
 |---|
 | ![HR Profile](App%20pictures/CaptureH2.PNG) ![HR Profile](App%20pictures/CaptureH2P.PNG)  |

| Employees|
|---|
| ![Employees](App%20pictures/CaptureH1E.PNG) |

### Manager

| Manager Dashboard|
|---|
| ![Manager Dashboard](App%20pictures/CaptureMD2.PNG) |

|Manager Profile |
|---|
| ![Manager profile](App%20pictures/CaptureMPP1.PNG) |

|Messages |
|---|
| ![Inbox](App%20pictures/CaptureMM1.PNG) ![Message](App%20pictures/CaptureWMessR.PNG)  ![New message](App%20pictures/CaptureMS.PNG) | 

|Task tracking |
|---|
| ![Task tracking](App%20pictures/CaptureMTR1.PNG)  ![Task tracking](App%20pictures/CaptureMTR2.PNG)|

|Leave|
|---|
| ![Leave](App%20pictures/CaptureML1.PNG)  ![Leave](App%20pictures/CaptureML2.PNG)|

|Feedback|
|---|
| ![Feedback](App%20pictures/CaptureMF1.PNG) ![Send Feedback](App%20pictures/CaptureMF2.PNG)  ![Received](App%20pictures/CaptureMF3.PNG) | 

### Staff 

| Dashboard|
|---|
| ![OS Dashboard](App%20pictures/CaptureW1D.PNG) |

|Manager Profile |
|---|
| ![OS profile](App%20pictures/CaptureWPP1.PNG) |

|Leave|
|---|
| ![Leave](App%20pictures/CaptureWL1.PNG) |

|Feedback|
|---|
| ![Feedback](App%20pictures/CaptureWF1.PNG) |

|Issues|
|---|
| ![Report Issue](App%20pictures/CaptureW1I.PNG)  ![my reported](App%20pictures/CaptureW2I.PNG) ![Rate](App%20pictures/CaptureRATING.PNG) ![Reported](App%20pictures/CaptureWI3.PNG)|

### Company page

|Feedback|
|---|
| ![Feedback](App%20pictures/CaptureWF1.PNG) |

---

## Getting Started

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL)
- PHP 8+
- A modern web browser (Chrome or Firefox recommended)

### Setup (under 5 minutes)

```bash
# 1. Clone or extract the project into your XAMPP htdocs folder
/htdocs/COSC300/

# 2. Start XAMPP — enable Apache and MySQL

# 3. Create the database
#    Open phpMyAdmin → create a database named: epms_db
#    Run the SQL schema from NewDbConn.php

# 4. Launch the application
http://localhost/COSC300/logIn.php
```

### Default Test Credentials

| Role | Employee ID |
|---|---|
| HR Administrator | `1` |
| Manager | `6` |
| Staff | `20` |

---

## Database Schema (Overview)

```sql
CREATE DATABASE epms_db;
USE epms_db;

-- Core tables
-- emplooyedetails   → Employee records and role assignments
-- messages          → Internal messaging across roles
-- leave_requests    → Leave applications and approval status
-- issues            → Facility issue reporting and tracking
-- clock_in_records  → Attendance timestamps per employee
```

Connection is managed via `NewDbConn.php` using PDO for secure, prepared statements.

---

## Problem → Solution Mapping

| Traditional Challenge | EPMS Solution |
|---|---|
| Paper-based leave requests and approvals | Digital requests with live status tracking |
| No centralized communication channel | Built-in messaging system across all roles |
| Difficulty routing facility issues | Automated issue assignment by department (IT, Maintenance, Security) |
| Manual attendance tracking | Clock-in/out system with audit trail |
| Fragmented employee records | Unified employee directory with profiles |
| Slow multi-step approval workflows | Role-gated, automated approval routing |

---

## Roadmap

The following enhancements are planned for future iterations:

- [ ] REST API with JSON endpoints for mobile integration
- [ ] React Native mobile application
- [ ] Advanced analytics and performance reporting dashboard
- [ ] AI-powered performance insights and trend detection
- [ ] Payroll system integration
- [ ] Facial recognition attendance (module scaffolded)
- [ ] Email and SMS notification system
- [ ] Document management with versioning
- [ ] Multi-company / multi-tenant architecture
- [ ] Fine-grained role permissions and audit logs

---

## Contributing

Contributions, bug reports, and feature suggestions are welcome.

```bash
# Fork the repo and create a feature branch
git checkout -b feature/your-feature-name

# Commit with a clear message
git commit -m "Add: description of your feature"

# Push and open a Pull Request
git push origin feature/your-feature-name
```

---

## Acknowledgements

- **Tirelo Capital** — Real-world deployment partner
- **COSC300 Course** — Academic foundation and project brief
- **Open Source Libraries** — Bootstrap 5, FontAwesome, PHPMailer

---

<div align="center">

**Built with purpose — empowering HR, Managers, and Staff in one platform.**  
⭐ Star this repo if it was useful · 🐛 [Report a bug](../../issues) · 💬 [Start a discussion](../../discussions)

</div>
