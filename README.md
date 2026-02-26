Project Name: CRISP (CRISP Resolves Incident & Service Problems)

Project Description:
CRISP is a role-based, responsive web application built with PHP and MySQL that enables customers to submit product complaints (optionally with an image), technicians to manage and resolve assigned issues, and administrators to oversee users and complaints across the system.

The application follows the MVC (Model-View-Controller) architectural pattern to keep business logic, database operations, and UI concerns clearly separated.

Project Tasks
- Task 1: Set up the development environment
  - Install and configure XAMPP (Apache + MySQL) / PHP
  - Configure VS Code and project structure
  - Initialize Git and GitHub repository

- Task 2: Design the application
  - Plan database schema (users, complaints, complaint types, product types)
  - Define roles and workflows (customer → tech → admin)
  - Plan MVC folder structure and routing strategy

- Task 3: Develop the frontend
  - Build responsive UI views for each role dashboard
  - Implement complaint submission/edit forms
  - Implement status badges and dashboard tables/cards

- Task 4: Develop the backend
  - Implement MVC routing (Front Controller via public/index.php)
  - Build Models for DB operations (CRUD)
  - Build Controllers for role workflows and validations

- Task 5: Implement authentication & authorization
  - Secure login using password hashing (password_hash / password_verify)
  - Session-based authentication
  - Role-based access control (customer, technician, admin)

- Task 6: Connect to a database
  - Create MySQL schema and seed data
  - Implement database connection configuration
  - Use prepared statements (PDO recommended) to prevent SQL injection

- Task 7: Implement file upload (complaint images)
 

- Task 8: Test the application
  - Test login, role routing, complaint workflows, file upload handling
  - Validate access control (prevent role escalation and direct URL access)
  - Fix defects found during manual testing

- Task 9: Document the project
  - Create a comprehensive README
  - Create a test plan with documented test cases and results

 Project Skills Learned
- PHP MVC application structure (Controllers/Models/Views)
- Role-based authorization (customer / technician / admin)
- Password hashing and secure authentication flow
- Input validation and server-side security practices
- File upload handling
- MySQL schema design and CRUD operations
- Git/GitHub workflow and documentation discipline

 Language / Tools Used
- PHP: Backend and server-side rendering
- MySQL: Database (XAMPP / phpMyAdmin)
- HTML/CSS/JavaScript: UI + responsiveness
- Apache (XAMPP): Local web server
- Git/GitHub: Version control

 Development Process Used
    Agile / Iterative Delivery
  - Features built in phases (core flow first, then role dashboards, then security/testing)
  - Continuous refactoring as complexity increased (MVC growth)

 Notes
- Start Apache and MySQL in XAMPP before running the app.
- Ensure your database credentials in the config file match your local environment.
- If using seeded accounts, verify the passwords are stored hashed in the database.
- Recommended: keep uploaded files outside webroot or serve via a controlled handler.

 Installation / Running Locally
1. Clone the repository into your XAMPP `htdocs` directory.
2. Import the provided SQL schema/seed into MySQL (phpMyAdmin).
3. Update database configuration values (host, dbname, username, password).
4. Launch:
   - `https://localhost/<project-folder>/public/`
5. Login with an existing user:
  - Admin
      Email: admin@example.com
      Password: password!
  - Tech
      Email: tech@example.com
      Password: password!
  - Customers
      Email: customer@example.com
      Password: password!
      Email: bobjones@email.com
      Password: testword!
6. Or:
   - Register a customer using the register page
   - Log in as an existing admin to add another Tech/Admin account
  


 Link to Project
 https://github.com/UBSDFS/customer-complaint-tracking-system/tree/master
 


 License
This project is licensed under the GNU License - see the
[LICENSE](LICENSE) file for details.
