# Slahpc

Slahpc is a PHP and MySQL web application for a computer repair service.
Customers can create an account, request a repair appointment, follow its
status and price, and leave a review after the repair is completed.

Administrators can view customers and appointments, update repair statuses and
prices, and approve or reject customer reviews.

## Technologies

- PHP 8
- MySQL or MariaDB
- PDO for the database connection
- HTML5
- CSS3
- JavaScript
- XAMPP for local development

## Project Structure

```text
Projet-De-Fin-D-tude-PFE-/
|-- assets/
|   |-- app.js
|   |-- hero-bg.png
|   |-- logo-navbar.png
|   |-- logo.png
|   |-- password-toggle.js
|   |-- register-validation.js
|   `-- styles.css
|-- includes/
|   |-- app.php
|   |-- config.php
|   |-- database.php
|   |-- admin-dashboard.php
|   `-- translations.php
|-- admin.php
|-- user-dashboard.php
|-- forgot-password.php
|-- index.php
|-- login.php
|-- logout.php
|-- register.php
`-- README.md
```

## Main PHP Files

### `index.php`

The public home page of Slahpc. It:

- presents the repair services and business information;
- shows the appointment form to connected customers;
- reads active services and prices from the database;
- validates and saves new appointments;
- loads the main JavaScript and CSS files.

### `register.php`

Creates a new customer account. It validates the submitted name, email,
telephone number, address, and password. Passwords are protected with
`password_hash()` before they are stored in the database.

### `login.php`

Authenticates a customer or administrator using their email and password. A
successful login saves the user ID in the PHP session and redirects the user to
the correct dashboard.

### `logout.php`

Checks the CSRF token, clears the current session, and safely logs the user out.

### `forgot-password.php`

Displays the password recovery form. The current version accepts and validates
an email address, but it does not yet send a reset email.

### `user-dashboard.php`

The customer dashboard. It:

- requires the customer to be logged in;
- displays the customer's repair appointments;
- shows the price and current status of each appointment;
- allows a review only after an appointment is completed;
- displays the customer's previous reviews.

### `admin.php`

The administrator dashboard. It:

- allows access only to users with the `admin` role;
- displays appointment, customer, and completion statistics;
- lists customers and repair appointments;
- updates appointment statuses and prices;
- allows manual quotes for hardware repairs;
- approves or rejects customer reviews.

## Includes Files

Every main PHP page loads `includes/config.php`. That file starts the project
and loads the other shared files.

### `includes/config.php`

Contains the general project configuration:

- starts the PHP session;
- configures secure session cookie options;
- defines the database host, port, name, username, and password;
- loads all other files from the `includes` folder.

Default database settings:

```php
const DB_HOST = '127.0.0.1';
const DB_PORT = '3306';
const DB_NAME = 'slah_pc';
const DB_USER = 'root';
const DB_PASS = '';
```

### `includes/database.php`

Contains all shared database tools:

- creates the PDO connection with the `db()` function;
- enables PDO exception reporting;
- uses prepared statements instead of unsafe SQL concatenation;
- checks whether database columns exist;
- adds missing columns;
- creates the reviews table when necessary.

### `includes/app.php`

Contains the small shared application functions in one place:

- authentication and role checks;
- CSRF form protection;
- redirects, escaping, validation, and flash messages;
- common page header and footer rendering.

### `includes/admin-dashboard.php`

Contains the shared administrator sidebar, statistics, forms, and CRUD handlers
used by the separate administration pages.

### `includes/translations.php`

Contains the English, French, and Arabic interface text. It:

- saves the selected language in the session;
- returns translated text with the `t()` function;
- detects Arabic right-to-left mode;
- translates repair service names and appointment statuses.

## Assets Files

### `assets/styles.css`

Contains the complete visual design of the website, including layouts, colors,
forms, navigation, dashboards, responsive behavior, and mobile styles.

### `assets/app.js`

Controls interactive behavior on the home page, including language text,
navigation, theme controls, and appointment-related interface behavior.

### `assets/password-toggle.js`

Adds the Show/Hide button behavior to password fields.

### `assets/register-validation.js`

Checks the registration form in the browser and displays messages for an empty
name, invalid email, or password shorter than eight characters. PHP still
performs the final server-side validation.

### `assets/logo.png`

The main Slahpc logo image.

### `assets/logo-navbar.png`

The logo version displayed in the website navigation bar.

### `assets/hero-bg.png`

The background image used in the home page hero section.

## Database Connection

The connection flow is:

```text
PHP page
   |
   v
includes/config.php
   |
   v
includes/database.php -> db()
   |
   v
PDO connection
   |
   v
MySQL database: slah_pc
```

A page can communicate with MySQL like this:

```php
$pdo = db();
$statement = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$statement->execute([$email]);
$user = $statement->fetch();
```

The question mark is filled by `execute()`. This prepared-statement approach
helps protect the application from SQL injection.

## Database Tables

The application currently uses these main tables:

- `users`: customer and administrator accounts;
- `services`: repair services, prices, and availability;
- `appointments`: customer repair requests and their statuses;
- `reviews`: ratings and comments for completed appointments.

The current database may also contain `products`, `orders`, `messages`,
`invoices`, and `pc_builds`. They are reserved for additional features and are
not currently part of the main user interface.

## Installation With XAMPP

1. Install XAMPP with Apache, MySQL, and PHP.
2. Place the project inside `C:\xampp\htdocs\Slahpc`.
3. Start Apache and MySQL from the XAMPP Control Panel.
4. Check the connection settings in `includes/config.php`.
5. Import an SQL export of the `slah_pc` database through phpMyAdmin.
6. Open `http://localhost/Slahpc/`.

The project does not currently include an automatic database installation
page. Keep an SQL export with the project when moving it to another computer.

## Security

- Passwords are stored with `password_hash()`.
- Login passwords are checked with `password_verify()`.
- SQL values are sent through PDO prepared statements.
- HTML output is escaped with the `e()` function.
- POST forms use CSRF protection.
- Session cookies use `HttpOnly` and `SameSite=Lax`.
- Protected pages check the user's role.
- Prices, statuses, IDs, reviews, and account fields are validated.

## PHP Syntax Check

Run this command from the project folder:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object {
    php -l $_.FullName
}
```

## Author

Final-year project by Tarik Bufardi.
