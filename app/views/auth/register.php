<?php
//Declare and clear variables
$username = '';
$password = '';
$passwordConfirmation = '';


//Declare and  clear variables for error messages
$username_error = '';
$password_error = '';
$passwordconfirmation_error = '';

//Retrieve values from query string and store in local variable after page load
if (isset($_POST['username'], $_POST['password'], $_POST['passwordConfirmation'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $passwordConfirmation = $_POST['passwordConfirmation'];

    // Later:
    // - validate inputs
    // - hash password
    // - insert into database

    header("Location: index.php");
    exit;
}

?>

<html>

<head>
    <title>SDC342L Project Register Page</title>
</head>

<body>
    <form method='POST'>
        <label for="username">Username:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($username) ?>"><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password"><br><br>

        <label for="passwordConfirmation">Confirm Password:</label>
        <input type="password" name="passwordConfirmation"><br><br>

        <input type="submit" value="Register"><br><br>
        <a href="index.php">Cancel</a>
    </form>
</body>

</html>