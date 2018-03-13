 <?php 
 require('dbconnect.php');

 if (!empty($_GET)) {
  $sql = 'UPDATE `tweets` SET `delete_flag` = 1  WHERE `tweet_id`=?';
  $delete = array($_GET['id']);
  $stmt = $dbh->prepare($sql);
  $stmt->execute($delete); 
  }

header('Location: index.php');
exit();
   ?>
