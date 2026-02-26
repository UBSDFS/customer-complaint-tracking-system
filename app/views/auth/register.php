<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

//Include validation class
require_once 'register_validation.php';

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


//Declare and clear variables for error messages
$firstName_error = '';
$lastName_error = '';
$streetAddress_error = '';
$city_error = '';
$state_error = '';
$zipCode_error = '';
$phoneNumber_error = '';
$email_error = '';
$password_error = '';
$passwordConfirmation_error = '';

//Retrieve values from query string and store in local variable after page load
if ($_SERVER["REQUEST_METHOD"] === "POST") {

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

    //Validate inputs
    $firstName_error = InputValidation::validateName($firstName);
    $lastName_error = InputValidation::validateName($lastName);
    $streetAddress_error = InputValidation::validateAddress($streetAddress);
    $city_error = InputValidation::validateCity($city);
    $state_error = InputValidation::validateState($state);
    $zipCode_error = InputValidation::validateZipCode($zipCode);
    $phoneNumber_error = InputValidation::validatePhoneNumber($phoneNumber);
    $email_error = InputValidation::validateEmail($email);
    $password_error = InputValidation::validatePassword($password);
    $passwordConfirmation_error = InputValidation::validatePasswordConfirmation($password, $passwordConfirmation);

    // If no validation errors, proceed with registration
    if (empty($firstName_error) && empty($lastName_error) && empty($streetAddress_error) && 
        empty($city_error) && empty($state_error) && empty($zipCode_error) && 
        empty($phoneNumber_error) && empty($email_error) && empty($password_error) && 
        empty($passwordConfirmation_error)) {
        header("Location: index.php");
        exit;
    }
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
                    <input type="text" name="firstName" value="<?php echo htmlspecialchars($firstName) ?>"><br>
                    <span class="error"><?php echo htmlspecialchars($firstName_error) ?></span><br>
                </div>
                <div class="field">
                    <label for="lastName">Last Name:</label>
                    <input type="text" name="lastName" value="<?php echo htmlspecialchars($lastName) ?>"><br>
                    <span class="error"><?php echo htmlspecialchars($lastName_error) ?></span><br>
                </div>
                <div class="field">
                    <label for="streetAddress">Street Address:</label>
                    <input type="text" name="streetAddress" value="<?php echo htmlspecialchars($streetAddress) ?>"><br>
                    <span class="error"><?php echo htmlspecialchars($streetAddress_error) ?></span><br>
                </div>
                <div class="field">
                    <label for="city">City:</label>
                    <input type="text" name="city" value="<?php echo htmlspecialchars($city) ?>"><br>
                    <span class="error"><?php echo htmlspecialchars($city_error) ?></span><br>
                </div>
                <div class="field">
                    <label for="state">State:</label>
                    <input type="text" name="state" value="<?php echo htmlspecialchars($state) ?>"><br>
                    <span class="error"><?php echo htmlspecialchars($state_error) ?></span><br>
                </div>
                <div class="field">
                    <label for="zipCode">Zip Code:</label>
                    <input type="text" name="zipCode" value="<?php echo htmlspecialchars($zipCode) ?>"><br>
                    <span class="error"><?php echo htmlspecialchars($zipCode_error) ?></span><br>
                </div>
                <div class="field">
                    <label for="phoneNumber">Phone Number:</label>
                    <input type="text" name="phoneNumber" value="<?php echo htmlspecialchars($phoneNumber) ?>"><br>
                    <span class="error"><?php echo htmlspecialchars($phoneNumber_error) ?></span><br>
                </div>
                <div class="field">
                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email) ?>"><br>
                    <span class="error"><?php echo htmlspecialchars($email_error) ?></span><br>
                </div>
                <div class="field">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password"><br>
                    <span class="error"><?php echo htmlspecialchars($password_error) ?></span><br>
                </div>
                <div class="field">
                    <label for="passwordConfirmation">Confirm Password:</label>
                    <input type="password" name="passwordConfirmation" id="passwordConfirmation"><br>
                    <span class="error"><?php echo htmlspecialchars($passwordConfirmation_error) ?></span><br>
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