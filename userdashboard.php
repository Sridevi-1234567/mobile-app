<?php
error_reporting(0);
require('db.php');
session_start();
if (!isset($_SESSION['userId']) || !isset($_SESSION['usercategory']) || $_SESSION['usercategory'] != 'admin') {
    header("Location:index.php");
    exit();
}

$conn = getcon();

$query = "SELECT * FROM user_details WHERE category = 'user'";
$result = mysqli_query($conn, $query);
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
    <table class="table">
    <tr>
        <th>User ID</th>
        <th>User Name</th>
        <th>Confirmed Orders</th>
        <th>Pending Orders</th>
    </tr>
    <?php while ($user = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $user['User_Id']; ?></td>
            <td><?php echo $user['Name']; ?></td>
            <td>
                <?php
                $query = "SELECT * FROM orders WHERE user_id = " . $user['User_Id'] . " AND order_status = 'COMPLETED'";
                $confirmedOrdersResult = mysqli_query($conn, $query);
                while ($order = mysqli_fetch_assoc($confirmedOrdersResult)) {
                    echo "Order ID: " . $order['order_id'] . "<br>";
                    echo "total amount: " . $order['total_price'] . "<br>";
                }
                ?>
            </td>
            <td>
                <?php
                $query = "SELECT * FROM orders WHERE user_id = " . $user['User_Id'] . " AND order_status = 'PENDING'";
                $pendingOrdersResult = mysqli_query($conn, $query);
                while ($order = mysqli_fetch_assoc($pendingOrdersResult)) {
                    echo "Order ID: " . $order['order_id'] . "<br>";
                    echo "total amount:". $order['total_price'] . "<br>";
                }
                ?>
            </td>
        </tr> 
    <?php } ?>

    </table>
    <a href="admindashboard.php" class="btn btn-primary">Back to dashboard</a>
</body>
</html>        