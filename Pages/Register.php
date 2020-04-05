<?php
include('../Modules/DatabaseConnect.php');

//$ScreenHeight = $_SESSION['height'];
//$ScreenWidth = $_SESSION['width'];
?>
<!DOCTYPE html>
<html>

<head>
    <link href='https://fonts.googleapis.com/css?family=Architects Daughter' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Buda:300' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Arsenal' rel='stylesheet'>
    <link rel="stylesheet" href="../styles.css">
</head>

<body>
    <div class="MainTitle" style="padding:0px;">
        <h1>Day 7 Eats Registration</h1>
    </div>

    <?php
//! Look for
if (isset($_POST['SubmitRegister'])) {
    if ($_POST['Password1'] != $_POST['Password2']) {
        $_POST['Password1'] = null;
        $_POST['Password2'] = null;
        $PasswordMatch = false;
    } else {
        $PasswordMatch = true;
    }

    $ClientID = $_POST['ClientID'];
    $FirstName = $_POST['FirstName'];
    $LastName = $_POST['LastName'];
    $YearLevel = $_POST['YearLevel'];
    $PCClass = $_POST['PCClass'];
    $Email = $_POST['Email'];
    $Password = $_POST['Password1'];
    if ($PasswordMatch == true) {
        $PasswordHash = password_hash($Password, PASSWORD_BCRYPT, ["cost" => 12]);
    }
    
    unset($Password);


    //searchs all matching ClientID's
    $QueryClientID = $connection->prepare("SELECT * FROM ClientList WHERE ClientID=:id");
    $QueryClientID->bindParam("id", $ClientID, PDO::PARAM_STR);
    $QueryClientID->execute();
    
    //searchs all matching Email's
    $QueryEmail = $connection->prepare("SELECT * FROM ClientList WHERE Email=:email");
    $QueryEmail->bindParam("email", $Email, PDO::PARAM_STR);
    $QueryEmail->execute();

    //checks for duplicates
    $Pass = 1;
    if ($QueryClientID->rowCount() > 0) {
        echo "<p class='Error'>ClientID Already in Use!</p>";
        $Pass = 0;
    }
    if ($QueryEmail->rowCount() > 0) {
        echo "<p class='Error'>Email Already in Use!</p>";
        $Pass = 0;
    }
    if ($PasswordMatch == false) {
        echo "<p class='Error'>Passwords Don't Match!</p>";
        $Pass = 0;
    }

    unset($QueryEmail);
    unset($QueryClientID);

    //Send registration data to database
    if ($Pass = 1) {
        $Query = $connection->prepare("INSERT INTO ClientList(ClientID,FirstName,LastName, YearLevel, PCClass, Password,Email) VALUES (:clientid,:firstname,:lastname,:yearlevel,:pcclass,:password,:email)"); //issue no yearlevel not working
        $Query->bindParam("clientid", $ClientID, PDO::PARAM_STR);
        $Query->bindParam("firstname", $FirstName, PDO::PARAM_STR);
        $Query->bindParam("lastname", $LastName, PDO::PARAM_STR);
        $Query->bindParam("yearlevel", $YearLevel, PDO::PARAM_STR);
        $Query->bindParam("pcclass", $PCClass, PDO::PARAM_STR);
        $Query->bindParam("password", $PasswordHash, PDO::PARAM_STR);
        $Query->bindParam("email", $Email, PDO::PARAM_STR);
        $QueryResult = $Query->execute(); // or die(print_r($Query->errorInfo(), true));
    }

    //Checks if query passed
    if ($QueryResult and $Pass==1) {
        echo '<p class="success">Your registration was successful!</p>';
        $SuccessRegistration = '';
    //Display login hyperlink to be redirected?
    } elseif ($Pass==1) {
        echo '<p class="error">Something went wrong!</p>';
    }
}
//debug
?>

    <form method="post" action="" name="registration-form">
        <div class="Box">
            <center>
                <div class="InputText">
                    <label>Client ID<br></label>
                </div>
                <input class="InputBox" type="text" name="ClientID" pattern="[a-zA-Z0-9]+" required />
                <div class="InputText">

                    <label>First Name<br></label>
                </div>
                <input class="InputBox" type="text" name="FirstName" pattern="[a-zA-Z]+" required />
                <div class="InputText">

                    <label>Last Name<br></label>
                </div>
                <input class="InputBox" type="text" name="LastName" pattern="[a-zA-Z]+" required />
                <div class="InputText">

                    <label>Year Level<br></label>
                </div>
                <input class="InputBox" type="text" name="YearLevel" pattern="[0-9." required />
                <div class="InputText">

                    <label>PC Class<br></label>
                </div>
                <input class="InputBox" type="text" name="PCClass" pattern="[a-zA-Z0-9]+" />
                <div class="InputText">

                    <label>Email<br></label>
                </div>
                <input class="InputBox" type="email" name="Email" pattern="[a-zA-Z0-9@.]+" required />
                <div class="InputText">

                    <label>Password<br></label>
                </div>
                <input class="InputBox" type="password" name="Password1" required />

                <div class="InputText">
                    <label>Confirm Password<br></label>
                </div>
                <input class="InputBox" type="password" name="Password2" required />


            </center>
            <div style="text-align: center;">
                <button class="Button" style="width:70px;" type="submit" name="SubmitRegister">Submit</button>
                <button class="Button" style="width:70px;" type="submit" name="ReturnLogin" href="Login.php">Back</button>
            </div>
    </form>
    </div>
    <p>NOTE ClientID = StudentID or a number of your choice, email must require email format, passwords must be the same
    </p>
</body>

</html>