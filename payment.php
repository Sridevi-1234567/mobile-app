<?php
//error_reporting(0);
require('db.php');
session_start();
 
if (!isset($_SESSION['userId']) || !isset($_SESSION['usercategory']) || $_SESSION['usercategory'] != 'user') {
    header("Location: index.php");
    exit();
}
$user_id = $_SESSION['userId'];
$conn=getcon();
if(isset($_POST['buy'])){
    $order_id= $_POST['order_id'];
    $total_price = $_POST['total_price'];
    $query = "INSERT INTO pending_orders (user_id, order_id, total_price) VALUES ('$user_id', '$order_id', '$total_price')";
    mysqli_query($conn, $query);
}
$order_id = $_POST['order_id'];
$total_price = $_POST['total_price'];
$query = "SELECT order_id, total_price FROM pending_orders WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
if(isset($_POST['pay'])){
    $order_id = htmlspecialchars($row['order_id']);
    $total_price =htmlspecialchars($row['total_price']);
    $card_number =htmlspecialchars($_POST['card_number']);
    $expiry_date = htmlspecialchars($_POST['expiry_date']);
    $cvv = htmlspecialchars($_POST['cvv']);
    if (!is_numeric($card_number) || strlen($card_number) != 16) {
        echo "<script>alert('Invalid card number');</script>";
    header("Refresh:0; url=payment.php");
        exit();
    }
    $expiry_date = DateTime::createFromFormat('m/y', $expiry_date);
if ($expiry_date === false || $expiry_date->format('m/y') != $_POST['expiry_date']) {
    echo "<script>alert('Invalid expiry date');</script>";
    header("Refresh:0; url=payment.php");
    exit();
}
if ($expiry_date < new DateTime()) {
    echo "<script>alert('Expired card');</script>";
    header("Refresh:0; url=payment.php");
    exit();
}
    if (!is_numeric($cvv) || strlen($cvv) != 3) {
        echo "<script>alert('Invalid CVV');</script>";
        header("Refresh:0; url=payment.php");
            exit();
        
    }
    echo "Processing payment for Order ID: " . $order_id . "\n";
    echo "Total Price: " . $total_price . "\n<br>";
    echo "Card Number: " . $card_number . "\n<br>";
    echo "". $expiry_date->format("m/y"). "\n<br>";
    echo "CVV: " . $cvv . "\n<br>";

    sleep(2);
    $query="DELETE FROM pending_orders WHERE order_id = '$order_id' AND user_id = '$user_id'";
    $iscleared=mysqli_query($conn, $query);
    $query = "UPDATE orders SET order_status = 'COMPLETED' WHERE order_id = '$order_id'";
    mysqli_query($conn, $query);

    echo "Payment successful. Order ID: " . $order_id;
    echo "<script>alert('Payment successful');</script>";
    header("Refresh:0; url=viewplacedorders.php");
}
?>

<html>
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <title>Payment Gateway</title>
    <style>
        h2 {
            text-align: center;
        }
.my-form {
            margin: 0 auto;
            width: 50%; 
            padding: 20px;
            border: 2px solid black;
            border-radius: 10px;
            margin-top: 50px;
            background-color:beige;

        }
    </style>
</head>
<body>
    <h2>Payment Gateway</h2>
    <form method="POST" class="my-form">

        <label for="order_id" class="control-label">Order ID:</label><br>
        <input type="text" id="order_id" name="order_id" value="<?php echo $row['order_id']; ?>" readonly class="form-control"><br>
        <label for="total_price" class="control-label">Total Price:</label><br>
        <input type="text" id="total_price" name="total_price" value="<?php echo $row['total_price']; ?>" readonly class="form-control"><br>
        <label for="card_number" class="control-label">Card Number:</label><br>
        <input type="text" id="card_number" name="card_number" required placeholder="Enter 16 digit card number" class="form-control"><br>
        <label for="expiry_date" class="control-label">Expiry Date:</label><br>
        <input type="text" id="expiry_date" name="expiry_date" required placeholder="Enter expiry date in mm/yy" class="form-control"><br>
        <label for="cvv" class="control-label">CVV:</label><br>
        <input type="text" id="cvv" name="cvv" required placeholder="Enter 3 digit CVV" class="form-control"><br>
        <button type="submit" name="pay" value="pay" class="btn btn-success">Proceed To Pay</button>&nbsp;&nbsp;
        <a href="vieworders.php" class="btn btn-warning">Back to cart</a>
    </form>
    
</body>
</html>