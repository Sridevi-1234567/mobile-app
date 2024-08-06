<?php
require('db.php');
//error_reporting(0);
session_start();

 
if (!isset($_SESSION['userId']) || !isset($_SESSION['usercategory']) || $_SESSION['usercategory'] != 'admin') {
    header("Location:index.php");
    exit();
}
$conn=getcon();
$timestamp = time() - 24 * 60 * 60;
$date = date('Y-m-d H:i:s', $timestamp);

$query = "SELECT * FROM orders WHERE order_status = 'PENDING' AND order_date < '$date'";
$result = mysqli_query($conn, $query);

while ($order = mysqli_fetch_assoc($result)) {
    $query = "SELECT * FROM order_details WHERE order_id = " . $order['order_id'];
    $orderDetailsResult = mysqli_query($conn, $query);

    while ($orderDetail = mysqli_fetch_assoc($orderDetailsResult)) {
        $query = "UPDATE items SET quantity = quantity + " . $orderDetail['quantity'] . " WHERE id = " . $orderDetail['item_id'];
        mysqli_query($conn, $query);
    }
    $query = "DELETE FROM order_details WHERE order_id = " . $order['order_id'];
    mysqli_query($conn, $query);
    $query="DELETE FROM pending_orders WHERE order_id = ". $order['order_id'];
    $result = mysqli_query($conn, $query);
    $query = "DELETE FROM orders WHERE id = " . $order['order_id'];
    mysqli_query($conn, $query);
    
}
echo "<script>alert('Items revived')</script>";
header("Refresh:0; url=admindashboard.php");

?>