# 🎫 IT Helpdesk Premium System

![PHP Badge](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL Badge](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Status Badge](https://img.shields.io/badge/Status-Stable-success?style=for-the-badge)

> **"Experience Internal Support like never before. Fast, Beautiful, and Intuitive."**

A next-generation Ticketing System designed to replace clunky, old-school support tools. Built with **Native PHP** for maximum performance and **Modern CSS** for a premium user experience.

---

## 🎨 Why This Project?

Most internal tools differ significantly from consumer apps—they are often slow and ugly. **We changed that.**
This IT Helpdesk System brings specific consumer-grade UI/UX to the corporate environment:
- **WhatsApp-Style Chat**: Familiar, fast, and responsive.
- **Crystal Clear Layout**: No more confusing menus.
- **Instant Feedback**: Visual status updates that keep everyone in the loop.

![Main Dashboard](docs/images/dashboard-cover.png)

## 🔥 Killer Features

### 1. � The "Pro" Chat Interface
We spent hours refining the chat experience (as seen in the commit history!):
- **Smart Bubbles**: Distinct designs for 'Me' vs 'Others'.
- **External Avatars**: Modern layout with avatars outside the bubble.
- **Perfect Typography**: No weird gaps, optimised line-height, and `nl2br` logic for robust formatting.
- **Adaptive Layout**: Looks perfect on a 6-inch phone screen AND a 27-inch monitor.

### 2. � Role-Based Power
- **Admin**: Full control. Manage tickets, users, and view analytics.
- **Staff/Technician**: Pick up tickets, reply instantly, and close issues.
- **User**: Submit requests in seconds and track progress in real-time.

### 3. � Insightful Dashboard
- Visual counters for **Pending**, **In Progress**, and **Resolved** tickets.
- Color-coded badges for quick scanning.

---

## 📸 Visual Tour

### 🔐 Secure & Modern Entry
![Login Screen](docs/images/login-screen.png)

### 💬 The Chat Experience

| **Seamless Mobile Experience** | **Spacious Desktop View** |
|:---:|:---:|
| <img src="docs/images/mobile-chat.png" width="300" alt="Mobile Chat UI"> | <img src="docs/images/desktop-chat.png" width="500" alt="Desktop Chat UI"> |
| *Compact, legible, and touch-friendly.* | *Optimized for productivity and clarity.* |

---

## 🛠️ Tech Stack (The "Secret Sauce")

We kept it **Lightweight & Fast** by avoiding heavy frameworks for the core logic:
- **Backend**: Native PHP 8 (No bloated dependencies).
- **Frontend**: Vanilla CSS with modern Flexbox/Grid architecture.
- **Database**: MySQL optimized for relational integrity.
- **Assets**: Remix Icon & Google Fonts (Inter).

---

## ⚙️ Quick Start Guide

Want to run this locally? It's easy.

1.  **Clone the Magic**
    ```bash
    git clone https://github.com/RonyRisvaldoLumbanBatu/it-helpdesk.git
    cd it-helpdesk
    ```

2.  **Wake up the Database**
    - Create a database (e.g., `db_it_helpdesk`).
    - Import the provided SQL file: `database/db_helpdesk.sql`.
    - Configure `config/database.php` with your credentials.

3.  **Launch!**
    Start the built-in PHP server:
    ```bash
    php -S localhost:8000 -t public
    ```
    Open `http://localhost:8000` in your browser.

4.  **Login Access**
    - **Admin**: `admin@contoh.com` / `password123`
    - **User**: `user@contoh.com` / `password123`

---

## 🤝 Contributing

Got an idea to make it even cooler?
1. Fork it.
2. Create your feature branch (`git checkout -b feature/AmazingFeature`).
3. Commit your changes.
4. Push to the branch.
5. Open a Pull Request.

---

<center>
  <p>Made with ❤️ and a lot of ☕ by <b>Rony Risvaldo</b></p>
</center>
