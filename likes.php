<html>




<body>

<?php
require_once("facebook.php");
session_start();
$user=($_SESSION['user']);
$likes=($_SESSION['likes']);
echo $likes;
?>


</body>
</html>



