<?php
//Declare and clear variables
$complaintTypeId = '';
$customerId = '';
$productId = '';
$complaintTypeId = '';
$details = '';
$imagePath = '';


//Declare and  clear variables for error messages
// $complaintTypeId_error = '';


//Retrieve values from query string and store in local variable after page load
if (isset($_POST['complaintTypeId'], $_POST['customerId'], $_POST['productId'], $_POST['complaintTypeId'], 
$_POST['details'], $_POST['imagePath'])) {

    $complaintTypeId = ($_POST['complaintTypeId']);
    $customerId = ($_POST['customerId']);
    $productId = ($_POST['productId']);
    $complaintTypeId = ($_POST['complaintTypeId']);
    $details = ($_POST['details']);
    $imagePath = ($_POST['imagePath']);

    header("Location: index.php");
    exit;
}

?>

<html>

<head>
    <link rel="stylesheet" href="/customer-complaint-tracking-system/public/assets/css/registration.css"> <!-- added path to css-->
    <title>SDC342L Project New Complaint Page</title>
</head>

<body>
    <main class="new-complaint-page">
        <section class="complaint-card">
            <header class="complaint-header">
                <h2>New Complaint</h2>
            </header>

            <form method='POST'>
                <div class="field">
                    <label for="complaintTypeId">Complaint Type:</label>
                    <input type="text" name="complaintTypeId" value="<?php echo htmlspecialchars($complaintTypeId) ?>"><br><br>
                </div>
                <div class="field">
                    <label for="customerId">CustomerId:</label>
                    <input type="text" name="customerId" value="<?php echo htmlspecialchars($customerId) ?>"><br><br>
                </div>
                <div class="field">
                    <label for="productId">ProductId:</label>
                    <input type="text" name="productId" value="<?php echo htmlspecialchars($productId) ?>"><br><br>
                </div>
                <div class="field">
                    <label for="complaintTypeId">complaintTypeId:</label>
                    <input type="text" name="complaintTypeId" value="<?php echo htmlspecialchars($complaintTypeId) ?>"><br><br>
                </div>
                <div class="field">
                    <label for="details">details:</label>
                    <input type="text" name="details" value="<?php echo htmlspecialchars($details) ?>"><br><br>
                </div>
                <div class="field">
                    <label for="imagePath">Image Path:</label>
                    <input type="text" name="imagePath" value="<?php echo htmlspecialchars($imagePath) ?>"><br><br>
                </div>

                <div class="actions">
                    <input type="submit" value="Submit Complaint"><br><br>
                    <a href="index.php">Cancel</a>
                </div>

            </form>
        </section>
    </main>
</body>

</html>
