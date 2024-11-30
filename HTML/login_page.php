<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ThirstTap</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f0f4f8;
        }
        .container {
            max-width: 400px;
            margin-top: 100px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo h2 {
            color: #0033FF;
        }
        .btn-primary {
            background-color: #65ACC2;
            border: none;
        }
        .btn-primary:hover {
            background-color: #008AB5;
        }
        .error {
            color: red;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h2>ThirstTap Admin</h2>
        </div>
        <form method="POST" action="" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
            <div class="error" id="formError">
                <?php
                if (isset($_GET['error'])) {
                    echo htmlspecialchars($_GET['error']);
                }
                ?>
            </div>
        </form>
    </div>

    <script>
        function validateForm() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorDiv = document.getElementById('formError');

            // Clear previous error message
            errorDiv.textContent = '';

            // Check if fields are empty
            if (!email) {
                errorDiv.textContent = 'Email is required.';
                return false;
            }
            if (!password) {
                errorDiv.textContent = 'Password is required.';
                return false;
            }
            return true; // Proceed with form submission
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Include your database connection file
require '../WebsiteBackend/db.php';
require 'session_data.php'; // Include session data handling


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $admin['password'])) {
            // Set session data using the function from session_data.php
            setAdminSession($admin);
        
            // Redirect based on role
            if ($admin['role'] === 'Admin') {
                header("Location: dashboard.html"); // Admin dashboard
            } elseif ($admin['role'] === 'Super Admin') {
                header("Location: SuperAdminHTML/super admin dashboard.html"); // Super admin dashboard
            } else {
                header("Location: dashboard.html"); // Default redirection
            }
            exit();
        
        } else {
            // Wrong password
            header("Location: login_page.php?error=Incorrect password");
            exit();
        }
    } else {
        // Email not found
        header("Location: login_page.php?error=Email not registered");
        exit();
    }
}

// Close connection
$conn->close();
?>
