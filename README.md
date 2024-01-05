# HoopsTeams CMS

HoopsTeams is a Content Management System (CMS) for basketball teams. The system includes features for managing teams, leagues, comments, and user authentication.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Usage](#usage)
- [Folder Structure](#folder-structure)
- [License](#license)

## Prerequisites

Before you begin, ensure you have met the following requirements:

- PHP installed on your server.
- PDO extension enabled.
- A MySQL or compatible database.

## Installation

1. Clone the repository to your local machine:

    ```bash
    git clone https://github.com/dzhu0/hoops-teams.git
    ```

2. Create a new MySQL database and import the provided SQL schema (`hoopsteams.sql`).

3. Configure the database connection by duplicating `dbinfo.php.template` into `dbinfo.php` and updating the database credentials:

    ```php
    // Data Source Name (DSN) required to connect to the MySQL database
    // *Required*
    // HOST
    // DBNAME
    define("DB_DSN", "mysql:host=HOST;dbname=DBNAME;charset=utf8");

    // User's username that can access the database
    // *Required*
    // USERNAME
    define("DB_USER", "USERNAME");

    // User's password required to access the database
    // *Required*
    // PASSWORD
    define("DB_PASS", "PASSWORD");
    ```

4. Set up your web server to point to the project's root directory.

## Usage

1. Navigate to the project's root directory in your web browser.

2. Start exploring the application. You can manage teams, leagues, comments, and user accounts.

3. Access the admin features by signing in with an admin-level account. Admin's username and password are `admin`.

## Folder Structure

- `tools/`: Contains PHP scripts for database connection, authentication, and other utilities.
- `markup/`: Includes common HTML head, header, and footer sections for reuse.
- `css/`: Stylesheets for styling the application.
- `logos/`: Logo images for each team.

## License

This project is licensed under the [MIT License](LICENSE.md).
