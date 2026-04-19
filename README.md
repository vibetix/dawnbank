# DawnBank - Modern Banking Solution

A comprehensive web-based banking platform that provides users with a complete digital banking experience including account management, transactions, loans, investments, and customer support.

## Features

### User Features
- **User Authentication**: Secure login and registration system
- **Account Management**: Create and manage multiple bank accounts
- **Transactions**: View transaction history and transfer funds
- **Investments**: Track and manage investment portfolios with returns calculation
- **Loan Management**: Apply for loans and track repayment status
- **Profile Management**: Update personal information and upload profile pictures
- **Support Tickets**: Submit and track support requests
- **Statements**: Download account statements in PDF format
- **Dashboard**: Real-time overview of accounts, transactions, and investments

### Admin Features
- **Admin Dashboard**: Monitor system activity and user statistics
- **User Management**: Approve/reject user registrations and manage user accounts
- **Loan Approval**: Review and approve/reject loan applications
- **Transaction Monitoring**: Track and reverse transactions if needed
- **System Reports**: Generate comprehensive system reports
- **Settings Management**: Configure system-wide settings
- **Admin Messaging**: Communicate with users directly
- **Data Visualization**: Charts and analytics for system insights

## Technology Stack

### Frontend
- HTML5
- CSS3
- JavaScript (jQuery)
- Font Awesome Icons

### Backend
- PHP 7+
- MySQL Database
- PHPMailer for email notifications
- FPDF for PDF generation

### Dependencies
- setasign/fpdf - PDF generation library
- setasign/fpdi - PDF import library
- phpmailer/phpmailer - Email sending library

## Installation

### Prerequisites
- PHP 7.0 or higher
- MySQL 5.7 or higher
- Composer
- Web server (Apache, Nginx, etc.)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/dawnbank.git
   cd dawnbank
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Database Setup**
   - Create a new MySQL database named `dawnbank`
   - Import the database schema (if provided)
   - Update database credentials in `includes/connect.php`:
     ```php
     $host = "localhost";
     $dbname = "dawnbank";
     $username = "root";
     $password = "";
     ```

4. **Configure the application**
   - Update email settings in `includes/phpmailer.php` if using email features
   - Set appropriate file permissions for upload directories

5. **Start the application**
   - Place the project in your web server's document root
   - Navigate to `http://localhost/dawnbank` (or your configured URL)

## Project Structure

```
dawnbank/
├── Index.html              # Main landing page
├── login.html              # User login page
├── about.html              # About page
├── services.html           # Services page
├── contact.html            # Contact page
├── admin/                  # Admin panel
│   ├── admin-index.html
│   ├── user.html
│   ├── accounts.html
│   ├── transactions.html
│   └── ...
├── users/                  # User pages
│   ├── index.html
│   ├── accounts.html
│   ├── transactions.html
│   ├── profile.html
│   └── ...
├── includes/               # PHP backend
│   ├── connect.php
│   ├── signin.php
│   ├── signup.php
│   ├── fetch_dashboard.php
│   ├── fetch_transactions.php
│   └── ...
├── assets/                 # CSS, JS, Images
│   ├── css/
│   ├── js/
│   └── images/
└── vendor/                 # Composer dependencies
```

## Usage

### For Users
1. Visit the homepage and click "Sign Up" to create an account
2. Log in with your credentials
3. Access your dashboard to view accounts and transactions
4. Manage investments and apply for loans
5. Download statements and submit support tickets as needed

### For Admins
1. Access the admin panel at `/admin/admin-index.html`
2. Log in with admin credentials
3. Manage users, approve loans, and monitor system activity
4. Generate reports and adjust system settings

## File Upload Requirements
- Ensure the `includes/uploads/` directory has write permissions
- Profile pictures and documents are stored in `includes/uploads/`

## Database Tables
The application uses the following main tables:
- `users` - User accounts and profiles
- `accounts` - Bank accounts
- `transactions` - Transaction history
- `loans` - Loan applications and status
- `investments` - Investment records
- `admin` - Admin user accounts
- `support_tickets` - Support requests
- `system_settings` - Application configuration

## Security Considerations

- Always use HTTPS in production
- Update database credentials in `includes/connect.php`
- Set strong passwords for database and admin accounts
- Regularly backup your database
- Keep all dependencies up to date
- Sanitize and validate all user inputs
- Use environment variables for sensitive configuration (recommended for production)

## Configuration

Key configuration files:
- `includes/connect.php` - Database connection settings
- `includes/phpmailer.php` - Email configuration

## Troubleshooting

### Database Connection Errors
- Verify MySQL is running
- Check credentials in `includes/connect.php`
- Ensure the database exists

### Upload Issues
- Check folder permissions for `includes/uploads/`
- Verify PHP `upload_tmp_dir` setting
- Check available disk space

### Email Delivery Issues
- Verify SMTP settings in `includes/phpmailer.php`
- Check email credentials
- Review server firewall/port settings

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Support

For support, questions, or bug reports, please use the support ticket system within the application or contact the development team.

## Authors

- DawnBank Development Team

## Disclaimer

This banking application is provided as-is for educational and demonstration purposes. For production use, ensure compliance with all financial regulations and security requirements applicable to your jurisdiction.

---

**Note**: This is a demonstration banking platform. For actual financial operations, ensure all applicable regulations and security standards are met.
