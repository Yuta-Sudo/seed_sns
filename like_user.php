<?php
	require('function.php');
	require('dbconnect.php');

	login_check();

	  echo('<br>');  echo('<br>');
  echo('<br>');  echo('<br>');
  echo('<br>');  echo('<br>');

	if (!empty($_GET)) {
		// 投稿内容
		$tweet_sql = 'SELECT * FROM `tweets` LEFT JOIN `members` ON `tweets`.`member_id`=`members`.`member_id` WHERE `tweet_id`=?';
		$tweet_data = array($_GET['tweet_id']);
		$tweet_stmt = $dbh->prepare($tweet_sql);
		$tweet_stmt->execute($tweet_data);
		// 一件のみ
		$tweet = $tweet_stmt->fetch(PDO::FETCH_ASSOC);

		// この投稿にいいねしているユーザーの情報が欲しい
		$like_sql = 'SELECT * FROM `likes` LEFT JOIN `members` ON `likes`.`member_id`=`members`.`member_id` WHERE `tweet_id`=?';
		$like_data = array($_GET['tweet_id']);
		$like_stmt = $dbh->prepare($like_sql);
		$like_stmt->execute($like_data);

		// データがない時のエラーを防ぐ
		$likers = array();

		// ある件数分ループ
		while(true) {
			$liker = $like_stmt->fetch(PDO::FETCH_ASSOC);
			if ($liker == false) {
				break;
			}
			$likers[] = $liker;
		}

		// var_dump($likers);exit;

		foreach ($likers as $like_user) {
			// member_idを元にユーザーの情報を取得
			$sql = 'SELECT * FROM `members` WHERE `member_id`=?';
			$data = array($like_user['member_id']);
			$stmt = $dbh->prepare($sql);
			$stmt->execute($data);
			$like = $stmt->fetch(PDO::FETCH_ASSOC);
			$likes[] = $like;
		}

		// echo '<pre>';
		// var_dump($likes);
		// echo '</pre>';

		// いいねをしているユーザーの数
		$like_user_count = count($likes);

	} else {
		header('Location: index.php'.$page);
		exit;
	}

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
      <div class="col-md-3 content-margin-top">
        <img src="picture_path/<?php echo $tweet['picture_path']; ?>" width="250" height="250">
        <h3><?php echo $tweet['tweet']; ?></h3>
        <br>
        <a href="index.php">&laquo;&nbsp;一覧へ戻る</a>
      </div>
      <div class="col-md-9 content-margin-top">
        <div class="msg_header">
        この投稿にいいねしているユーザー : <span style="color: green;"><?php echo $like_user_count; ?>人</span>
        </div>
        <?php foreach ($likes as $user) { ?>
        <div class="msg">
          <a href="#"><img src="picture_path/<?php echo $user['picture_path']; ?>" width="48" height="48"></a>
          <!-- クリックした時にプロフィールへ -->
          &nbsp;
          <a href="profile.php?member_id=<?php echo $user['member_id']; ?>" style="font-size: 16px;"><span class="name"><?php echo $user['nick_name'];?></span></a>
		  &nbsp;
          <!-- <a href="#">
          <button class="btn btn-sm btn-default">フォロー</button>
          </a>
          <a href="#">
          <button class="btn btn-sm btn-default">フォロー解除</button>
          </a> -->
        <?php } ?>
        </div>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>












 ?>