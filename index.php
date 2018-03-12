<?php 
session_start();
require('dbconnect.php');

//ログインチェック
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  //ログインしている
  //ログイン時間の更新
  $_SESSION['time'] = time();
  //ログインユーザー情報取得
  $login_sql = 'SELECT * FROM `members` WHERE `member_id`=?';
  $login_data = array($_SESSION['id']);
  $login_stmt = $dbh->prepare($login_sql);
  $login_stmt->execute($login_data);
  $login_member = $login_stmt->fetch(PDO::FETCH_ASSOC);
  }
else
{
  //ログインしていない
  //ログイン画面へ強制遷移する
  header( "Location: login.php ");
  exit;
  }

  if (!empty($_POST)) {
    if($_POST['tweet'] == '' ){
      $error['tweet'] = 'blank';
    }
    if (!isset($error)) {
      //insert文
    $sql =' INSERT INTO `tweets` SET `tweet`=? , `member_id`=?, `reply_tweet_id`= -1 , `created`=NOW(),`modified`= NOW() ';
    $data = array($_POST['tweet'],$_SESSION['id']);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    }
  }
//一覧用の投稿全件取得
  //テーブル結合
  //INNNR JOINと OUTTER JOIN(left join と right join)
  //INNER JOIN =両方のテーブルに存在するデータのみ
  
  //OUTER JOIN(left join と right join) =複数のテーブルがあり、それらを結合する際に優先テーブルを１つ決め、そこにある情報を全て表示しながら、ほかのテーブルの情報についになるデータがあれば表示する
  //優先テーブルに指定されると、そのテーブルの情報を全て表示される

  $tweet_spl = 'SELECT * FROM `tweets` LEFT JOIN `members`on `tweets`. `member_id` = `members` . `member_id` ORDER BY `tweets`.`created` DESC ' ;
  $tweet_stmt = $dbh->prepare($tweet_spl);
  $tweet_stmt->execute();
  $tweet_list = array();




 while (true) { // trueな処理なら永遠に繰り返す
      $tweet = $tweet_stmt->fetch(PDO::FETCH_ASSOC);
      if ($tweet == false) {
          break; // データがなくなったら繰り返し処理を終える
      }
      $tweet_list[] = $tweet; // ある文だけ配列に追加し
  }

echo('<br>');
echo('<br>');
echo('<pre>');
var_dump($tweet_list) ;
echo('</pre>');

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
      <div class="col-md-4 content-margin-top">
        <legend>ようこそ<strong><?php echo $login_member['nick_name'] ?></strong>さん</legend>

        <form method="post" action="" class="form-horizontal" role="form">
            <!-- つぶやき -->
            <div class="form-group">
              <label class="col-sm-4 control-label">つぶやき</label>
              <div class="col-sm-8">
                <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"></textarea>
                <?php if(isset($error) && $error["tweet"] == 'blank'){ ?>
                  <p class="error">つぶやき内容を入力してください</p>
                <?php } ?>
              </div>
            </div>
          <ul class="paging">
            <input type="submit" class="btn btn-info" value="つぶやく">
                &nbsp;&nbsp;&nbsp;&nbsp;
                <li><a href="index.html" class="btn btn-default">前</a></li>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <li><a href="index.html" class="btn btn-default">次</a></li>
          </ul>
        </form>
      </div>

      <div class="col-md-8 content-margin-top">

  <?php foreach( $tweet_list as $nikuman ) {?>
        <div class="msg">
          <img src="picture_path/<?php echo $nikuman['picture_path'] ?>" width="48" height="48">
          <p>
            <?php echo $nikuman['tweet'] ?><span class="name"> <?php echo $nikuman["nick_name"] ?> </span>
            [<a href="#">Re</a>]
          </p>
          <p class="day">
            <a href="view.html">
              2016-01-28 18:04
            </a>
            [<a href="#" style="color: #00994C;">編集</a>]
            [<a href="#" style="color: #F33;">削除</a>]
          </p>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>
