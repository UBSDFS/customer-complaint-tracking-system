<?php
//Declare and clear variables
$email = '';
$firstName = '';
$lastName = '';
$streetAddress = '';
$city = '';
$state = '';
$zipCode = '';
$phoneNumber = '';
$password = '';
$passwordConfirmation = '';


//Declare and  clear variables for error messages
$firstName_error = '';
$password_error = '';
$passwordConfirmation_error = '';

//Retrieve values from query string and store in local variable after page load
if (isset($_POST['firstName'], $_POST['lastName'], $_POST['streetAddress'], $_POST['city'], $_POST['state'], $_POST['zipCode'], $_POST['phoneNumber'], $_POST['email'], $_POST['password'], $_POST['passwordConfirmation'])) {

    $firstName = ($_POST['firstName']);
    $lastName = ($_POST['lastName']);
    $streetAddress = ($_POST['streetAddress']);
    $city = ($_POST['city']);
    $state = ($_POST['state']);
    $zipCode = ($_POST['zipCode']);
    $phoneNumber = ($_POST['phoneNumber']);
    $email = ($_POST['email']);
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
    <link rel="stylesheet" href="/customer-complaint-tracking-system/public/assets/css/registration.css"> <!-- added path to css-->
    <title>SDC342L Project Register Page</title>
</head>

<body> <!-- Updated Registration Form to match fields in controller -->
    <main class="register-page">
        <section class="register-card">
            <header class="register-header">
                <h2>Register</h2>
            </header>

            <form method='POST'>
                <div class="field">
                    <label for="firstName">First Name:</label>
                    <input type="text" name="firstName" value="<?php echo htmlspecialchars($firstName) ?>"><br><br>
                </div>
                <div class="field">
                    <label for="lastName">Last Name:</label>
                    <input type="text" name="lastName" value="<?php echo htmlspecialchars($lastName) ?>"><br><br>
                </div>
                <div class="field">
                    <label for="streetAddress">Street Address:</label>
                    <input type="text" name="streetAddress" value="<?php echo htmlspecialchars($streetAddress) ?>"><br><br>
                </div>
                <div class="field">
                    <label for="city">City:</label>
                    <input type="text" name="city" value="<?php echo htmlspecialchars($city) ?>"><br><br>
                </div>
                <div class="field">
                    <label for="state">State:</label>
                    <input type="text" name="state" value="<?php echo htmlspecialchars($state) ?>"><br><br>
                </div>
                <div class="field">
                    <label for="zipCode">Zip Code:</label>
                    <input type="text" name="zipCode" value="<?php echo htmlspecialchars($zipCode) ?>"><br><br>
                </div>
                <div class="field">
                    <label for="phoneNumber">Phone Number:</label>
                    <input type="text" name="phoneNumber" value="<?php echo htmlspecialchars($phoneNumber) ?>"><br><br>
                </div>
                <div class="field">
                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email) ?>"><br><br>
                </div>
                <div class="field">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password"><br><br>
                </div>
                <div class="field">

                    <label for="passwordConfirmation">Confirm Password:</label> <!--link password confirmation field to password field-->
                    <input type="password" name="passwordConfirmation" id="passwordConfirmation"><br><br>
                </div>

                <div class="actions">
                    <input type="submit" value="Register"><br><br>
                    <a href="index.php">Cancel</a>
                </div>

            </form>
        </section>
    </main>
</body>

</html>
<!-- TODO:
- Add client-side validation for password confirmation matching password
- Add error message displays for each field based on validation in controller
- Connnect to backend to store user data in database
-->