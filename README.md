# PKL Report Application

This is a web-based application for managing student internship (PKL) reports, built with native PHP, Bootstrap, and a MySQL database. It provides role-based access for administrators and teachers to streamline the process of tracking student progress and generating final reports.

## Features

- **Role-Based Access:** Separate dashboards and functionalities for Admins and Teachers.
- **Admin Dashboard:**
    - Manage students (CRUD)
    - Manage teachers and their login accounts (CRUD)
    - Manage PKL locations (CRUD)
    - Assign students and teachers to internship locations
    - Configure application-wide settings (e.g., school name, academic year)
- **Teacher Dashboard:**
    - View a list of assigned students.
    - Input and update student scores, notes, and attendance.
    - Generate a PDF report for each student, formatted to match a specific template.
- **Secure:**
    - Uses prepared statements to prevent SQL injection.
    - Implements authorization checks to ensure data privacy.
    - Manages database credentials securely using environment variables or a local config file.

## Requirements

- PHP 8.0+
- MySQL Server
- Git
- A web server (e.g., Apache, Nginx, or the PHP built-in server)

## Setup Instructions

1.  **Clone the Repository and Submodules:**
    ```bash
    git clone --recurse-submodules <repository_url>
    cd <repository_directory>
    ```
    If you have already cloned the repository without the submodules, you can fetch them using:
    ```bash
    git submodule update --init --recursive
    ```
    This will initialize and pull the **TCPDF** library required for PDF generation.

2.  **Database Setup:**
    - Log in to your MySQL server.
    - Create a new database for the application.
      ```sql
      CREATE DATABASE pkl_report;
      ```
    - Import the database schema and default data from the `database.sql` file.
      ```bash
      mysql -u <your_username> -p pkl_report < database.sql
      ```

3.  **Configuration:**
    - The application is configured to read database credentials from environment variables for security. If you prefer to use a configuration file, follow these steps:
    - Copy the example configuration file:
      ```bash
      cp config/database.php.example config/database.php
      ```
    - Open `config/database.php` and update the `DB_HOST`, `DB_USERNAME`, `DB_PASSWORD`, and `DB_NAME` constants with your database details.
    - **Note:** The `.gitignore` file is configured to ignore `config/database.php` to prevent committing sensitive credentials.

## Running the Application

1.  **Start Your Web Server:**
    - **Using PHP's Built-in Server (for development):**
      From the project root, run the following command:
      ```bash
      php -S localhost:8000
      ```
    - **Using Apache/Nginx:**
      Configure your web server to use the project's root directory as the document root.

2.  **Access the Application:**
    - Open your web browser and navigate to `http://localhost:8000` (or the URL provided by your web server).
    - You will be redirected to the login page.

3.  **Default Login:**
    - **Username:** `admin`
    - **Password:** `password`

## Running Tests

An end-to-end test script is included to verify the core functionalities of the application.

- Before running the tests, ensure your database is set up and the configuration in `config/database.php` is correct.
- From the project's root directory, run the following command in your terminal:
  ```bash
  php run_tests.php
  ```
- The script will execute a series of tests against the database and print the results to the console.
