<?php
error_reporting(0);
require('db.php');
session_start();
 
if (!isset($_SESSION['userId']) || !isset($_SESSION['usercategory']) || $_SESSION['usercategory'] != 'user') {
    header("Location: index.php");
    exit();
}
 
$user_id = $_SESSION['userId'];
 $conn=getcon();
 $query = "
    SELECT orders.order_id, orders.total_price, orders.order_status, orders_details.quantity, items.name, items.price
    FROM orders
    JOIN orders_details ON orders.order_id = orders_details.order_id
    JOIN items ON orders_details.item_id = items.id
    WHERE orders.user_id = '$user_id' AND orders.order_status = 'COMPLETED'
";
$result = mysqli_query($conn, $query);
 ?>
 <html>
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <title>View Placed Orders</title>
</head>
<body>
<header style="background-color:grey;color:white;text-align: center; height: 70px; padding: 20px; " >
    MOBILE ONLINE STORE
    <div style="position: absolute; top: 15px; right: 10px;">
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</header>
    <div class="container">
    <?php  if(mysqli_num_rows($result) > 0 ){ ?>
    <h2>Placed Orders</h2>
    <table class="table">
        <tr>
            <th>Order ID</th>
            <th>Item Price</th>
            <th>Quantity</th>
            <th>Item Name</th>
            <th>Total Price</th>
            
        </tr>
        <?php
        while($row = mysqli_fetch_assoc($result)) {?>
           <tr>
            <td><?php echo $row['order_id']; ?></td>
            <td><?php echo $row['price'];?></td>
            <td><?php echo $row['quantity'];?></td>
            <td><?php echo $row['name'];?></td>
            <td><?php echo $row['total_price'];?></td>
           </tr>
        
        <?php } ?>
    </table>
    <?php } else{?>
        <h2 style="color:red">No Orders found,Start shopping now! </h2>
    <?php } ?>
    <a href="viewitems.php" class="btn btn-primary">Back to Items</a>
    </div>
</body>
</html>