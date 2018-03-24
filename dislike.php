 <?php 
 require('function.php');
 login_check();

$sql ='DELETE FROM `likes` WHERE  `tweet_id`=? LIMIT 1';
      $data = array($_GET['id']);
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);
  header('Location: index.php');
  exit();

   ?>
