# 🎫 USTP Helpdesk Ticketing System

A web-based Helpdesk Ticketing System developed using PHP and MySQL for the University of Science and Technology of Southern Philippines (USTP).

## 📖 Overview

The system allows students and anonymous users to submit concerns directly without creating an account. Administrators and support agents can log in to manage, update, and respond to submitted tickets.

## ✨ Features

### Student / Anonymous
- Create a ticket without logging in
- Upload attachments
- Receive a unique ticket number
- Check ticket status

### Administrator / Agent
- Secure login
- View all submitted tickets
- Update ticket status
- Reply to tickets
- Delete tickets
- Manage ticket attachments

## 🛠 Technologies Used

- PHP
- MySQL
- Bootstrap 5
- HTML
- CSS
- JavaScript
- XAMPP

## 📂 Project Structure

```
Ticketing-System/
│
├── adminhome.php
├── adminticket.php
├── loginpage.php
├── anon_createticket.php
├── anon_checking_ticket.php
├── addattachment.php
├── connection.php
├── updatestatus.php
├── uploads/
└── layout/
```

## 🗄 Database

Main tables:

- tbl_ticket
- tbl_attachment
- tbl_message
- tbl_user
- tbl_department
- tbl_categories

## 👥 User Roles

### Student / Anonymous
- No login required
- Submit tickets
- Track ticket status

### Admin / Agent
- Login required
- Manage tickets
- Respond to users
- Update ticket status

## 🚀 Installation

1. Clone the repository.

```
git clone https://github.com/ronelabasjr-bit/Ticketing-System.git
```

2. Copy the project into

```
xampp/htdocs/
```

3. Import the MySQL database.

4. Update `connection.php` with your database credentials.

5. Start Apache and MySQL in XAMPP.

6. Open

```
http://localhost/Ticketing-System/
```

## 📌 Current Status

🚧 Currently under development.

The system is being redesigned so that:

- Students no longer need to register or log in.
- Only administrators and agents have accounts.
- Anonymous ticket submission is fully supported.

## 👨‍💻 Developer

**Ronel S. Abas Jr.**

Bachelor of Science in Computer Science

University of Science and Technology of Southern Philippines

## 📄 License

This project is for educational purposes.
