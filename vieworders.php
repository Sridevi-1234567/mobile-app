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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $itemId= $_POST['item_id'];
    $order_id = $_POST['order_id'];
    $currentquantity= $_POST['quantity'];
    $total_price = $_POST['total_price'];
    $order_amount = $_POST['orderprice'];
    $order_details_id= $_POST['order_details_id'];


    $getitem="select * from items where id='".$itemId."'";
       $items=mysqli_query($conn, $getitem);
       $item=mysqli_fetch_assoc($items);
    if (isset($_POST['increase'])) {
        $isincreased = false;
        $currentquantity=$currentquantity+1;
        if( $item['quantity'] <=0){
            echo "<script>alert('Not enough quantity available')</script>";
            header("Refresh:0; url=vieworders.php");
        }
        else{
        $order_price = $currentquantity * $item['price'];
        $newtotal_price = ($total_price - $order_amount) + $order_price;
        $query = "UPDATE orders SET total_price = '$newtotal_price' WHERE order_id = '$order_id' AND user_id = '$user_id' AND order_status = 'PENDING'";
        mysqli_query($conn, $query);
        $query = "UPDATE pending_orders SET total_price = '$newtotal_price' WHERE order_id = '$order_id' AND user_id = '$user_id'";
        $res=mysqli_query($conn, $query);
        $query = "UPDATE orders_details SET quantity = '$currentquantity',price='$order_price' WHERE order_id = '$order_id' AND item_id = '$itemId' AND id = '$order_details_id'";
         $result=mysqli_query($conn, $query);
        if($result==true){
            $isincreased = true;
        }
        $isincreased = false;
        $new_quantity = $item['quantity']-1;
        $iquery = "UPDATE items SET quantity='$new_quantity' WHERE id='".$itemId."' AND $new_quantity >= 0";
        $u=mysqli_query($conn, $iquery);
        if($u==true){
            $isincreased = true;
        }
        if($isincreased==true){
            echo "<script>alert('quantity increased by 1')</script>";
            header("Refresh:0; url=vieworders.php");
        }else{
            echo "<script>alert('Error in increasing quantity')</script>";
            //header("Refresh:0; url=vieworders.php");
        }
    }

    } elseif (isset($_POST['decrease'])) {
        $isdecreased = false;  
        if($currentquantity==1){
            $new_q = $item['quantity']+1;
            $cquery = "UPDATE items SET quantity='$new_q' WHERE id='".$itemId."'";
             mysqli_query($conn, $cquery);
             $newtotal_price = $total_price - $order_amount;
            $query = "UPDATE orders SET total_price = '$newtotal_price' WHERE order_id = '$order_id' AND user_id = '$user_id' AND order_status = 'PENDING'";
            mysqli_query($conn, $query);
            $query = "UPDATE pending_orders SET total_price = '$newtotal_price' WHERE order_id = '$order_id' AND user_id = '$user_id'";
            $res=mysqli_query($conn, $query);
             $query = "DELETE FROM orders_details WHERE order_id = '$order_id' AND item_id = '$itemId' AND id = '$order_details_id'";
             mysqli_query($conn, $query);
             echo "<script>alert(' cart list cleared successfully');</script>";
             header("Refresh:0; url=vieworders.php");

        }else{
        $currentquantity=$currentquantity- 1;
        $order_price = $currentquantity * $item['price'];
        $newtotal_price = ($total_price - $order_amount) + $order_price;
        $query = "UPDATE orders SET total_price = '$newtotal_price' WHERE order_id = '$order_id' AND user_id = '$user_id' AND order_status = 'PENDING'";
        $res=mysqli_query($conn, $query);
        $query = "UPDATE pending_orders SET total_price = '$newtotal_price' WHERE order_id = '$order_id' AND user_id = '$user_id'";
        $res=mysqli_query($conn, $query);
        $query = "UPDATE orders_details SET quantity = '$currentquantity', price = '$order_price' WHERE order_id = '$order_id' AND item_id = '$itemId' AND id = '$order_details_id'";
        $res = mysqli_query($conn, $query);
        if($res==true){
            $isdecreased = true;
        }
        $isdecreased = false;
        $new_quantity = $item['quantity']+1;
        $iquery = "UPDATE items SET quantity='$new_quantity' WHERE id='".$itemId."'";
        $d=mysqli_query($conn, $iquery);
        if($d==true){
            $isdecreased = true;
        }
        if($isdecreased==true){
            echo "<script>alert('quantity decreased by 1')</script>";
            header("Refresh:0; url=vieworders.php");
        }else{
            echo "<script>alert('Error in decreasing quantity')</script>";
            header("Refresh:0; url=vieworders.php");
        }
    }
 } elseif (isset($_POST['clear_all'])) {
    $iscleared = false;
    $order_id = $_POST['order_id'];
    $orders = "SELECT * FROM orders_details WHERE order_id = '$order_id'";
    $o = mysqli_query($conn, $orders);
    while ($order = mysqli_fetch_assoc($o)) {
        $item_id = $order['item_id'];
        $quantity = $order['quantity'];
        $getitems = "SELECT quantity FROM items WHERE id = '$item_id'";
        $items = mysqli_query($conn, $getitems);
        $item = mysqli_fetch_assoc($items);
        $new_quantity = $item['quantity'] + $quantity;
        $query = "UPDATE items SET quantity = '$new_quantity' WHERE id = '$item_id'";
        $iscleared=mysqli_query($conn, $query);
    }
    $oquery = "DELETE FROM orders_details WHERE order_id = '$order_id'";
    $iscleared=mysqli_query($conn, $oquery);
    $orquery = "DELETE FROM orders WHERE order_id = '$order_id' AND user_id = '$user_id' AND order_status = 'PENDING'";
    $iscleared=mysqli_query($conn, $orquery);
    $query="DELETE FROM pending_orders WHERE order_id = '$order_id' AND user_id = '$user_id'";
    $iscleared=mysqli_query($conn, $query);
    if($iscleared==true){
    echo "<script>alert('Cart  list cleared successfully');</script>";
    header("Refresh:0; url=vieworders.php");
    exit(); 
    }
    else{
        echo "<script>alert('Error in clearing cart list');</script>";
        header("Refresh:0; url=vieworders.php");
    exit();
    }
}
}
// $query = "SELECT o.id, o.quantity, o.total_price,o.item_id, i.name, i.price FROM orders o JOIN items i ON o.item_id = i.id WHERE o.user_id = $user_id";
// $result = mysqli_query($conn, $query);
// $query = "SELECT SUM(total_price) AS grand_total FROM orders WHERE user_id = '$user_id'";
// $total = mysqli_query($conn, $query);
// $row = mysqli_fetch_assoc($total);
// $grand_total = $row['grand_total'];
$query = "SELECT o.order_id, o.total_price,od.id, od.item_id, od.quantity,od.price AS orderprice, i.name, i.price 
FROM orders o 
JOIN orders_details od ON o.order_id = od.order_id 
JOIN items i ON od.item_id = i.id 
WHERE o.user_id = $user_id AND o.order_status = 'PENDING'";
$result = mysqli_query($conn, $query);
?>
 
<!DOCTYPE html>
<html>
<head>
    <title>View Orders</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body>
<header style="background-color:grey;color:white;text-align: center; height: 70px; padding: 20px; " >
    MOBILE ONLINE STORE
    <div style="position: absolute; top: 15px; right: 10px;">
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</header>
<div class="container">
    <?php 
if(mysqli_num_rows($result) > 0) {?>
    <table class="table">
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Item Price</th>
                <th>Order Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
              $myorderid=0;
              $total_price = 0.0;
            while ($order = mysqli_fetch_assoc($result)) { 
                $total_price = $order['total_price'];
                $myorderid = $order['order_id'];
                ?>
                <tr>
                    <td><?php echo $order['name']; ?></td>
                    <td><?php echo $order['quantity']; ?></td>
                    <td>Rs.<?php echo $order['price']; ?></td>
                    <td>Rs.<?php echo $order['orderprice']; ?></td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                            <input type="hidden" name="item_id" value="<?php echo $order['item_id']; ?>">
                            <input type="hidden" name="quantity" value="<?php echo $order['quantity']; ?>">
                            <input type="hidden" name="price" value="<?php echo $order['price']; ?>">
                            <input type="hidden" name="total_price" value="<?php echo $total_price ?>">
                            <input type="hidden" name="orderprice" value="<?php echo $order['orderprice']; ?>">
                            <input type="hidden" name="order_details_id" value="<?php echo $order['id']; ?>">
                            <button type="submit" name="increase" class="btn btn-success btn-sm">+</button>
                            <button type="submit" name="decrease" class="btn btn-warning btn-sm">-</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div>
        <h3>Grand Total: Rs.<?php echo $total_price ?></h3>
    <form method="POST" action="payment.php" >
        <input type="hidden" name="order_id" value="<?php echo $myorderid ;?>">
        <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
        <button type="submit" name="buy" class="btn btn-success" value="buy">Buy</button>
    </form>
</div>
    <br>
    <div float="right">
    <form method="POST" >
        <input type="hidden" name="order_id" value="<?php echo $myorderid; ?>">
        <button type="submit" name="clear_all" class="btn btn-danger" value="clear">Clear All</button>
    </form>
    </div>
    <br>
    
    <?php } else { ?>
        <h2 style="color:red">No items found,Start shopping now! </h2>
    <?php } ?>
    <a href="viewitems.php" class="btn btn-primary mt-3">Back to Items</a>
</div>

</body>
</html>