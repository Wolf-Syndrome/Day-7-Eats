<?php
//Start session
session_save_path("../Sessions");
session_start();

include('../Modules/DatabaseConnect.php');

//Make a Cookie to find width and height than set to session
setcookie("site", 'http://day7eats.tk/'); ?>

<script src='../Modules/Get_Cookie.js'></script>
<script src='../Modules/Get_Document_Size.js'></script>

<?php
if (isset($_COOKIE['width']) and isset($_COOKIE['height'])) {
    echo $_COOKIE['width']. "x". $_COOKIE['height'];
    echo "<br>". $_COOKIE['site'];
    $_SESSION['width'] = $_COOKIE['width'];
    $_SESSION['height'] = $_COOKIE['height'];
}
if (isset($_SESSION['width']) and isset($_SESSION['height'])) {
    echo "<br>". $_SESSION['width']. "x". $_SESSION['height'];
}


if (isset($_POST['login'])) {
    $ClientID = $_POST['username'];
    $Password = $_POST['password'];

    $Query = $connection->prepare("SELECT * FROM ClientList WHERE ClientID=:username");
    $Query->bindParam("username", $ClientID);
    $Query->execute();
    $Result = $Query->fetch(PDO::FETCH_ASSOC);

    if (!$Result) {
        echo '<p class="error">ClientID Password Combination is Wrong! #Wrong ClientID#</p>'; //debug
    } else {
        if (password_verify($Password, $Result['Password'])) {
            $_SESSION['ClientID'] = $Result['ClientID'];
            echo '<p class="error">Congratulations, you are logged in!</p>';
            /*Redirects to order page now*/
            ?>
<a href="Pages/Order.php">Order</a>

<?php
            header('Location: Order.php');
        } else {
            echo '<p class="error">ClientID Password Combination is Wrong! #Wrong Pass#</p>'; //debug
                //echo "<p class='error'>$res</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<meta name="author" content="Lachlan Bunt and Taylen Houslip">
<meta name="description" content="A website to designed for an assignment to order meals!">

<head>

    <link href='https://fonts.googleapis.com/css?family=Architects Daughter' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Buda:300' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Arsenal' rel='stylesheet'>

    <! all styles>
        <link rel="stylesheet" href="../styles.css">

</head>

<body>


    <div class="MainTitle" style="padding:0px;">
        <h1>Day 7 Eats Online Login</h1>
    </div>

    <form method="post" action="" name="signin-form">
        <div class="Box">
            <center>
                <div class="InputText">
                    <label>Client ID<br></label>
                </div>
                <input class="InputBox" type="text" name="username" pattern="[a-zA-Z0-9]+" required />

                <div class="InputText">
                    <label><br>Password<br></label>
                </div>
                <input class="InputBox" type="password" name="password" required />

            </center>
            <div style="text-align: center;">
            <button class="Button" style="width:75px;" type="button" onClick="location.href='Register.php'">Register</button>
            <button class="Button" style="width:75px;" type="submit" name="login">Log In</button>
            </div>
        </div>
    </form>
    <div class="footer">
        <h3>Made by Lachlan Bunt and Taylen Houslip!</h3>
    </div>
</body>

</html>
