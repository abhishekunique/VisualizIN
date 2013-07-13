<html>
<head></head>

<body>
<?php

session_start();
$user=$_SESSION['user'];
$likes=$_SESSION['likes'];

echo $user;
echo $likes;



    <?php if ($user): ?>
      <h3>You</h3>
      <img src="https://graph.facebook.com/<?php echo $user; ?>/picture">

      <h3>Your User Object (/me)</h3>
      <pre><?php print_r($likes); ?></pre>
    <?php else: ?>
      <strong><em>You are not Connected.</em></strong>
    <?php endif ?>



?>
</body>
</html>



