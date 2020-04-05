<?php
  //look at NOTPARTOFWEBSITE/Beginning Redirect/ for annotations
  $redirectURL = 'Pages/Login.php';
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Refresh"
    content="0; url=<?php echo $redirectURL; ?>" />
</head>

<body>
  <p>Please follow <a href="$redirectURL">this link</a>.</p>
</body>

</html>