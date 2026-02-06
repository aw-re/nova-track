# NovaTrack 🏗️
## Modern Construction Project Management System (CPMS)

**NovaTrack** is a state-of-the-art, Laravel-based platform designed to streamline construction project management. It features a premium **Glassmorphism UI**, full **Arabic/English localization**, and a robust role-based permission system.

![NovaTrack Badge](https://img.shields.io/badge/NovaTrack-v2.0-blueviolet?style=for-the-badge&logo=laravel)
![Status](https://img.shields.io/badge/Status-Stable-success?style=for-the-badge)

---

## ✨ Key Features

### 🎨 Modern UI/UX
- **Glassmorphism Design**: A premium, frosted-glass aesthetic using modern CSS variables.
- **Unified Navigation**: Context-aware sidebar that adapts seamlessly to specific user roles (Admin, Engineer, etc.).
- **Professional Landing Page**: Engaging hero section with a modern team presentation and consistent branding.
- **Responsive Layout**: Fully responsive dashboard supporting mobile and desktop.
- **Blade Components**: Reusable UI components for consistent design (Stats Cards, App Cards, Sidebar).

### 🌍 Localization (En/Ar)
- **Bilingual Support**: Full support for English and Arabic.
- **Auto RTL/LTR**: Interface direction changes automatically based on language selection.
- **Language Switcher**: Seamless toggling between languages via the navbar.

### 🛡️ Role-Based Access Control (RBAC)
- **Admin**: Full system oversight and configuration.
- **Project Owner**: Manage projects, view reports, and approve requests.
- **Engineer**: Assign tasks, review technical details, and manage sites.
- **Contractor**: Execute tasks, update progress, and request resources.

---

## 🚀 Quick Installation Guide

We have optimized the installation process to avoid common database issues. Follow these steps for a **guaranteed clean install**.

### Prerequisites
- PHP 8.1+
- Composer
- MySQL Database

### Steps
1. **Clone the Project**
   ```bash
   git clone https://github.com/your-repo/nova-track.git
   cd nova-track
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Edit `.env` and set your database credentials (`DB_DATABASE=cpms`, `DB_USERNAME`, etc.).*

4. **Database Setup (The Easy Way)**
   Instead of running migrations which might fail due to conflicts, use our **Clean Install SQL**:
   - Open **phpMyAdmin**.
   - Create a database named `cpms`.
   - Go to **Import**.
   - Select the file: `database/final_clean_install.sql`.
   - Click **Go**.
   
   *> This will automatically set up all tables, foreign keys, and seed the default users.*

5. **Run the Server**
   ```bash
   php artisan serve
   ```
   Visit `http://localhost:8000`.

---

## 🔑 Login Credentials

All accounts use the password: **`password`**

| Role | Email | Access Level |
|------|-------|--------------|
| **Admin** | `admin@example.com` | Full System Access |
| **Project Owner** | `owner@example.com` | Project Management Dashboard |
| **Engineer** | `engineer@example.com` | Technical & Tasks Dashboard |
| **Contractor** | `contractor@example.com` | Execution & Requests |

---

## 🛠️ Technology Stack

- **Framework**: Laravel 10.x
- **Frontend**: Blade Templates + Bootstrap 5
- **Styling**: Custom `novatrack.css` (Glassmorphism) + FontAwesome
- **Fonts**: Cairo (Arabic) & Poppins (English)
- **Database**: MySQL

---

## 📁 Project Structure

```
nova-track/
├── app/
│   ├── Http/Controllers/  # Controllers for Admin, Owner, Engineer...
│   ├── Models/            # Eloquent Models
│   └── View/Components/   # UI Components (StatsCard, AppCard)
├── database/
│   ├── final_clean_install.sql  # ✨ MASTER SETUP SCRIPT
│   └── migrations/        # Database history
├── resources/
│   ├── lang/              # Localization (en/ar)
│   ├── views/             # Blade Templates
│   └── css/               # Custom Styles
└── routes/
    └── web.php            # Routes with Role Middleware
```

---

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

*Built with ❤️ for Modern Construction Management.*
