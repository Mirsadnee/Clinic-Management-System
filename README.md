# Clinic Management System

A comprehensive clinic management system built with PHP and MySQL.

## Features

- User authentication (Admin, Doctor, Patient roles)
- Appointment scheduling
- Medical records management
- Clinic management
- Email notifications (coming soon)
- SMS reminders (coming soon)
- Online payments (coming soon)

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Composer (for PHPMailer and other dependencies)

## Installation

1. Clone the repository to your web server's document root:
   ```bash
   git clone https://github.com/yourusername/clinic-management.git
   ```

2. Create a new MySQL database and import the database structure:
   ```bash
   mysql -u root -p < database.sql
   ```

3. Configure the database connection:
   - Open `includes/config.php`
   - Update the database credentials if needed

4. Set up the web server:
   - For Apache, ensure mod_rewrite is enabled
   - Point the document root to the project directory
   - Ensure the web server has write permissions for the `uploads` directory

5. Default admin credentials:
   - Email: admin@example.com
   - Password: admin123

## Directory Structure

```
/clinic-management/
├── admin/              # Admin panel files
├── doctor/            # Doctor panel files
├── patient/           # Patient panel files
├── includes/          # Core classes and functions
├── assets/            # CSS, JS, and images
├── uploads/           # Uploaded files
├── database.sql       # Database structure
└── README.md          # This file
```

## Security Features

- Password hashing using PHP's password_hash()
- Prepared statements for all database queries
- Input validation and sanitization
- Session management
- Role-based access control

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details. # Clinic-Management-System
