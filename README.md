# Aircraft Hangar Management System

This repository contains the source code for an Airplane Hangar Management System, a web application that allows users to manage hangars, aircraft, reservations, and generate reports. The system is built using PHP and MySQLi for database interaction.

**Features**
- User Registration: New users can register with a unique username, password, and email. The system ensures that the username is not already taken.
- User Login: Registered users can log in using their credentials. Passwords are securely hashed and stored in the database.
- Dashboard: After logging in, users are presented with a dashboard that provides an overview of their hangars, aircraft, and reservations.
- Hangar Management: Users can create new hangars, edit existing ones, and delete hangars they own. Hangars are associated with specific users to manage access permissions.
- Aircraft Management: Users can add new aircraft, edit their details, and delete aircraft. Aircraft are associated with specific hangars to facilitate organization.
- Reservation Management: Users can create reservations for specific hangars and aircraft, view their reservations, and cancel existing reservations. The system ensures that reservations do not overlap.
- Reports Generation: Users can generate reports for a specific hangar, showing its reservations in a tabular format.

**Installation and Setup with XAMPP and Visual Studio Code**

1. Install XAMPP:
   - Download and install XAMPP from the Apache Friends website (https://www.apachefriends.org/index.html).
   - Follow the installation wizard and choose the components you want to install. Ensure that Apache, MySQL, and PHP are selected.

2. Start XAMPP:
   - Start XAMPP after installation. On Windows, you can do this by running XAMPP Control Panel and clicking the "Start" buttons next to Apache and MySQL.

3. Clone the Repository:
   - Open Visual Studio Code (or any other code editor of your choice).
   - Clone the Airplane Hangar Management System repository from GitHub to your local machine by running the following command in the terminal:
     ```
     git clone https://github.com/sally-20/Airplane-Hangar-Management.git
     ```

4. Database Setup:
   - Open `phpMyAdmin` by navigating to `http://localhost/phpmyadmin/` in your web browser.
   - Create a new database named `airplane_hangar_management`.
   - Import the `database.sql` file from the cloned repository into the newly created database. This file contains the necessary tables and data.

5. Update `config.php`:
   - In the root directory of the cloned repository, you will find a file named `config.php`.
   - Open `config.php` in your code editor and update the following variables with your MySQL database credentials:
     ```php
     $dbHost = 'localhost'; // Your MySQL host (usually 'localhost')
     $dbUser = 'root'; // Your MySQL username
     $dbPassword = ''; // Your MySQL password (if any)
     $dbName = 'airplane_hangar_management'; // The name of the database you created in step 4
     ```

6. Move the Repository Files:
   - Move the entire cloned repository folder into the `htdocs` directory of your XAMPP installation. On Windows, the default location is usually `C:\xampp\htdocs`.

7. Start Apache and MySQL:
   - Go back to XAMPP Control Panel and click the "Start" buttons next to Apache and MySQL if they are not already running.

8. Access the Application:
   - Open your web browser and go to `http://localhost/Airplane-Hangar-Management/`.
   - You should see the Airplane Hangar Management System homepage, and you can proceed to register and log in to start using the system.

9. Use the Application:
   - Register as a new user or log in with existing credentials.
   - Explore the dashboard, manage hangars, aircraft, and reservations, and generate reports.

**Note**: Remember that this setup is for local development and testing.

Thank you for using the Airplane Hangar Management System!
