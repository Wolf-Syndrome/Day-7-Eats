<?php
//First set Sessions directory and start/continue sessions
session_save_path("../Sessions");
session_start();
//Test if logged in
if (!isset($_SESSION['ClientID'])) {
    header('Location: ../index.php');
    exit;
}
//sql connect script
include('../Modules/DatabaseConnect.php');

//sql queries

$Query = $connection->query("SELECT * FROM PriceList ORDER BY ItemName ASC");
$Query->setFetchMode(PDO::FETCH_ASSOC);
$QueryItemCheck = $Query;

?>
<?php
//Submit Order Button
if (isset($_POST['SubmitOrderForm'])) {
    $ClientID = $_SESSION['ClientID'];
    $TransactionID = $_SESSION['ClientID']. '-'. date("Y"). '-'. $_POST['Term']. '-'. $_POST['Week'];
    $OrderWeek = $_POST['Week'];
    $OrderTerm = $_POST['Term'];
    //makes sure there is items on list
    if ($QueryItemCheck->rowCount() > 0) {
        //Create query to search for all matching transaction duplicates
        $QueryTransactionIDDup = $connection->prepare("SELECT TransactionID FROM PaymentStatus WHERE TransactionID = :transactionid");
        $QueryTransactionIDDup->bindParam("transactionid", $TransactionID, PDO::PARAM_STR);
        $QueryTransactionIDDup->execute();
        //tests for making transaction duplicates
        if ($QueryTransactionIDDup->rowCount() == 0) {
            //Add Transaction ID
            $OrderUploadQuery2 = $connection->prepare("INSERT INTO PaymentStatus (TransactionID) VALUES (:transactionid)");
            $OrderUploadQuery2->bindParam("transactionid", $TransactionID, PDO::PARAM_STR);
            $OrderUploadQueryResult2 = $OrderUploadQuery2->execute();

            $OrderUploadQueryTemplate = $connection->prepare("INSERT INTO Orders (ClientID,OrderTerm,OrderWeek,TransactionID,ItemName,ItemQuantity) VALUES (:clientid,:orderterm,:orderweek,:transactionid,:itemname,:itemquantity)");
            //Debug #echo $ClientID. ' '. $OrderTerm. ' '. $OrderWeek. ' '. $TransactionID. ' '. $ItemName. ' '. $ItemQuantity;
            $OrderUploadQueryTemplate->bindParam("clientid", $ClientID, PDO::PARAM_STR);
            $OrderUploadQueryTemplate->bindParam("orderterm", $OrderTerm, PDO::PARAM_STR);
            $OrderUploadQueryTemplate->bindParam("orderweek", $OrderWeek, PDO::PARAM_STR);
            $OrderUploadQueryTemplate->bindParam("transactionid", $TransactionID, PDO::PARAM_STR);

            while ($row2 = $QueryItemCheck->fetch()) {
                if ($_POST[str_replace(' ', '_', $row2["ItemName"])] != '' and $_POST[str_replace(' ', '_', $row2["ItemName"])] != '0') {
                    //Debug
                    //echo "<p>You ordered ". str_replace(' ', '_', $row2["ItemName"]). " x ". $_POST[str_replace(' ', '_', $row2["ItemName"])]. "</p>";
                    $ItemName = $row2["ItemName"];
                    $ItemQuantity = $_POST[str_replace(' ', '_', $row2["ItemName"])];
    
                    $OrderUploadQuery = $OrderUploadQueryTemplate;
                    $OrderUploadQuery->bindParam("itemname", $ItemName, PDO::PARAM_STR);
                    $OrderUploadQuery->bindParam("itemquantity", $ItemQuantity, PDO::PARAM_STR);
                    $OrderUploadQueryResult = $OrderUploadQuery->execute();
        
                    if ($OrderUploadQueryResult and $OrderUploadQueryResult2) {
                        //debug #echo "<p>worked</p>";
                        $OrderComplete = '';
                    } else {
                        echo "<p>fail</p>";
                        // echo "<p>". print_r($connection->errorInfo()). "</p>";
                    }
                    //unset($OrderUploadQuery);
                }
            }
        } else {
            $AlreadyOrdered = '';
        }
    } else {
        $SubmitOrderError = '';
    }
}

//Need to check before adding e.g. if cat food == catfood is true throw error (can't have duplicates)
?>

<!DOCTYPE html>
<html>

<head>

  <link href='https://fonts.googleapis.com/css?family=Architects Daughter' rel='stylesheet'>
  <link href='https://fonts.googleapis.com/css?family=Buda:300' rel='stylesheet'>
  <link href='https://fonts.googleapis.com/css?family=Arsenal' rel='stylesheet'>

  <! all styles>
    <link rel="stylesheet" href="../styles.css">

    <style>
      .Button {
        margin-left: 0px;
        position: absolute;
        left: 50%;
        margin-left: -35px;
      }

      .MainTitle {
        padding: 0px;
      }
    </style>
</head>


<body>

  <! debug>
    <p class='Error'> <?php echo $_SESSION['ClientID']; ?>
    </p>


    <! Title Start>
      <div class="MainTitle">
        <h1>Order Form</h1>
      </div>

      <form method="post" action="" name="OrderDates">
        <div class="MainTitle">

          <h3>Order For Term [
            <input class="InputNumber" size="1" type="text" name="Term" maxlength="2" pattern="[1-4]" required />
            ] Week [
            <input class="InputNumber" size="1" type="text" name="Week" maxlength="2" pattern="[1-9]" required />
            ]</h3>
        </div>

        <?php //Making Table?>
        <table>
          <tr>
            <th>Item</th>
            <th>Price</th>
            <th>QTY</th>
          </tr>

          <?php

//Make Content Part of Table From Database
if (!isset($OrderComplete)) {
    if ($Query->rowCount() > 0) {
        while ($row = $Query->fetch()) {
            echo '<tr><td>'. $row["ItemName"]. '</td><td>$'. $row["ItemPrice"]. '</td>'. "<td><input class='InputNumber' size='1' type='number' name=\"". str_replace(' ', '_', $row["ItemName"]). "\" min='0' max='5'></td></tr>";
        }
    } else {
        $SubmitOrderError = '';
    } ?>
        </table>
        <br>
        <button class="Button" type="submit" name="SubmitOrderForm">Submit</button>
      </form>
      <?php
} else {
        echo "<div class='MainTitle'><p>Thank you for ordering, please give any feedback or recommendations to us, remember it's a work in progress!</p>";
    }

?>

      <br>
      <?php
if (isset($SubmitOrderError)) {
    echo "<p class='Error'>Error Finding Item List</p>";
}
if (isset($AlreadyOrdered)) {
    echo "<p class='Error'>You have already ordered!</p>";
}

?>
</body>





</html>