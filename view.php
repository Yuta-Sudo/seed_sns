<?php
require('dbconnect.php');
session_start();
//echo('<br>');
//echo('<br>');
// echo('<pre>');
// var_dump($_SESSION['id']) ;
// echo('</pre>');
  $sql = 'SELECT * FROM `tweets` LEFT JOIN `members`on `tweets`. `member_id` = `members` . `member_id` WHERE `tweet_id` = ?' ;
  $data = array($_GET['tweet_id']);
  $stmt = $dbh->prepare($sql);
  $stmt->execute($data);
  $tweet = $stmt->fetch(PDO::FETCH_ASSOC);


  ?>
  <!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SeedSNS</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/form.css" rel="stylesheet">
    <link href="assets/css/timeline.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">

  </head>
  <body>
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.html"><span class="strong-title"><i class="fa fa-twitter-square"></i> Seed SNS</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
                <li><a href="logout.php">ログアウト</a></li>
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-4 col-md-offset-4 content-margin-top">
        <div class="msg">
          <img src="picture_path/<?php echo $tweet['picture_path'] ?>" width="100" height="100">
          <p>投稿者 : <span class="name"><?php echo $tweet['nick_name'] ?></span></p>
          <p>
            つぶやき : <br>
            <?php echo $tweet['tweet'] ?>
          </p>
          <p class="day">
            <?php $tweet_date =$tweet['modified'];
            echo date('Y-m-d H : i',  strtotime($tweet_date));
            ?>
            <?php if($_SESSION['id'] == $tweet['member_id']){ ?>
            [<a href="delete.php?id=<?php echo $tweet['member_id']; ?>"  style="color: #F33;" onclick=" return confirm('本当に削除しますか？')">削除</a>]
            <?php } ?>
          </p>
        </div>
        <a href="index.php">&laquo;&nbsp;一覧へ戻る</a>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>
