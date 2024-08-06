<?php
error_reporting(0);
require('db.php');
session_start();
if (!isset($_SESSION['userId']) || !isset($_SESSION['usercategory']) || $_SESSION['usercategory'] != 'admin') {
    header("Location:index.php");
    exit();
}

$conn = getcon();
if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST['search'])){
    $orderId = $_POST['orderId'];
    $query = "SELECT items.name, items.price, orders_details.quantity, orders_details.price, orders.total_price 
    FROM orders 
    JOIN orders_details ON orders.order_id = orders_details.order_id 
    JOIN items ON orders_details.item_id = items.id 
    WHERE orders.order_id = '$orderId'";
   $result = mysqli_query($conn, $query);
   if ($result === false) {
    die('Error: ' . mysqli_error($conn));
}
$display=false;
if (mysqli_num_rows($result) > 0) {
    $display=true;
}

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form method="POST">
        <label for="orderId" >Order ID:</label>
        <input type="text" id="orderId" name="orderId" required>
        <input type="submit" value="Search" name="search" class="btn btn-success">
    </form>
    <?php if($display) { ?>
    <table class="table">
        <tr>
            <th>Item Name</th>
            <th>Item Price</th>
            <th>Item Quantity ordered</th>
            <th>order Price</th>
            <th>Total Price</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
              <td><?php echo $row['name'];?></td>
              <td><?php echo $row['price'];?></td>
              <td><?php echo $row['quantity'];?></td>
              <td><?php echo $row['price'];?></td>
              <td><?php echo $row['total_price'];?></td> 
            </tr>
            <?php } 
    } else { ?>
            
            <h3>No orders found for given id </h3>
            <?php } ?>


            <a href="admindashboard.php" class="btn btn-primary">Back to dashboard</a>  
</body>
</html>