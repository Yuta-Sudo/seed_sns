<?php 
require('function.php');
require('dbconnect.php');
//ログインチェック

login_check();

$login_sql = 'SELECT * FROM `members` WHERE `member_id`=?';

      $login_data = array($_SESSION['id']);
      $login_stmt = $dbh->prepare($login_sql);
      $login_stmt->execute($login_data);
      $login_member = $login_stmt->fetch(PDO::FETCH_ASSOC);

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
//ページンリング機能
  //からの変数を用意
$page = '';

  //パラメータが存在していた場合ページ番号を代入
if (isset($_GET['page'])) {
  $page = $_GET['page'];
}else{
  $page = 1;
}
// 1以下の李レギュラな数字が入ってきた時番号を強制的に１
// max カンマ区切りで羅列された数字の中から最大の数字を取得
$page= max($page,1);

//1ページ分の表示件数
$page_number = 5;

//データの件数から最大ページを計算する。
require('dbconnect.php');


    $page_sql = 'SELECT COUNT(*) AS `page_count` FROM `tweets` WHERE `delete_flag` = 0';
    $page_stmt = $dbh->prepare($page_sql);
    $page_stmt->execute();
    $page_count = $page_stmt->fetch(PDO::FETCH_ASSOC);
    $all_page_number = ceil($page_count['page_count'] / $page_number);
    //パラメータのぺージ番号が最大ページを超えていたら強制的にページとする
    //min()カンマ区切りで羅列された数字の中から最小の数字を取得
    $page = min($page, $all_page_number);
    //表示するデータの取得開始位置
    $start = ($page -1) * $page_number;

//一覧用の投稿全件取得
  //テーブル結合
  //INNNR JOINと OUTTER JOIN(left join と right join)
  //INNER JOIN =両方のテーブルに存在するデータのみ

  //OUTER JOIN(left join と right join) =複数のテーブルがあり、それらを結合する際に優先テーブルを１つ決め、そこにある情報を全て表示しながら、ほかのテーブルの情報についになるデータがあれば表示する
  //優先テーブルに指定されると、そのテーブルの情報を全て表示される
  
  //limit = テーブルから取得する
    //limit 取得する開始位置、開始する場所

  $tweet_spl = "SELECT `tweets` . *,`members` . `nick_name`,`members` . `picture_path` FROM `tweets` LEFT JOIN `members`on `tweets`. `member_id` = `members` . `member_id` WHERE `delete_flag` = 0 ORDER BY `tweets` . `modified` DESC LIMIT " . $start . "," . $page_number ;
  $tweet_stmt = $dbh->prepare($tweet_spl);
  $tweet_stmt->execute();
  $tweet_list = array();

  


 while (true) { // trueな処理なら永遠に繰り返す
      $tweet = $tweet_stmt->fetch(PDO::FETCH_ASSOC);
      if ($tweet == false) {
          break; // データがなくなったら繰り返し処理を終える
      }
      // like数を求めるSQL分
      $like_sql = 'SELECT COUNT(*) AS `like_count` FROM `likes` WHERE `tweet_id` = ?';
      $likes = array($tweet['tweet_id']);
      $like_stmt = $dbh->prepare($like_sql);
      $like_stmt->execute($likes);
      $like_count = $like_stmt->fetch(PDO::FETCH_ASSOC);
    //１行分のデータに新しいキーを用意して、$like_countを代入
      $tweet['like_count'] = $like_count['like_count'];

      //ログインしている人がlikeしているか取得
    $sql = 'SELECT  COUNT(*) as `login_like_count` FROM `likes` WHERE `member_id`= ? AND `tweet_id` = ?';
    $data = array($_SESSION['id'], $tweet['tweet_id']);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    $login_likes = $stmt->fetch(PDO::FETCH_ASSOC);
    $tweet['login_like_flag'] = $login_likes['login_like_count'];

    $tweet_list[] = $tweet; // ある文だけ配列に追加し
  }
//フォローしているユーザーの数
   $following_sql = 'SELECT COUNT(*) as `following_count` FROM `follows` WHERE `member_id`=?';
  $following_data = array($_SESSION['id']);
  $following_stmt = $dbh->prepare($following_sql);
  $following_stmt->execute($following_data);
  $following = $following_stmt->fetch(PDO::FETCH_ASSOC);

  // 自分がフォローされているユーザーの数
  $follower_sql = 'SELECT COUNT(*) as `follower_count` FROM `follows` WHERE `follower_id`=?';
  $follower_data = array($_SESSION['id']);
  $follower_stmt = $dbh->prepare($follower_sql);
  $follower_stmt->execute($follower_data);
  $follower = $follower_stmt->fetch(PDO::FETCH_ASSOC);


 


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
              <a class="navbar-brand" href="index.php"><span class="strong-title"><i class="fa fa-twitter-square"></i> Seed SNS</span></a>
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
                <?php if ($page == 1) {?>
                <li>前</li>
                <?php }else{ ?>
                <li><a href="index.php?page=<?php echo $page -1; ?>" class="btn btn-default">前</a></li>
                <?php } ?>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <?php if ($page == $all_page_number) { ?>
                <li>次</li>
                <?php }else{ ?>
                <li><a href="index.php?page=<?php echo $page +1; ?>" " class="btn btn-default">次</a></li>
                <?php } ?>
                <li><?php echo $page; ?>/<?php echo $all_page_number   ?></li>
                 </ul>
        </form>
        <iframe src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fyuta.sudo1124&tabs=timeline&width=0&height=0&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=false&appId" width="500" height="500" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
      </div>

      <div class="col-md-8 content-margin-top">
         <div class="col-md-8 content-margin-top">
        <div class="msg_header">
          <a href="follow.php">Followers<span class="badge badge-pill badge-default"><?php echo $follower['follower_count']; ?></span></a>
          <a href="following.php">Followings<span class="badge badge-pill badge-default"><?php echo $following['following_count']; ?></span></a>
        </div>

  <?php foreach( $tweet_list as $nikuman ) {?>
        <div class="msg">
          <img src="picture_path/<?php echo $nikuman['picture_path'] ?>" width="48" height="48">
          <p>
            <?php echo $nikuman['tweet'] ?><span class="name"> <?php echo $nikuman["nick_name"] ?> </span>
            <?php if($login_member['member_id'] != $nikuman['member_id']){ ?>
            [<a href="reply.php?tweet_id=<?php echo $nikuman['tweet_id']; ?>">Re</a>]
            <?php } ?>
            <?php if ($nikuman['login_like_flag'] == 0) { ?>
             [<a href="like.php?action=like&id=<?php echo $nikuman['tweet_id']; ?>&page=<?php echo $page;?>""><i class="fa fa-thumbs-o-up">いいね！</i></a>]
             <?php }else{ ?>
             [<a href="like.php?action=dislike&id=<?php echo $nikuman['tweet_id'];?>&page=<?php echo $page;?>"><i class="fa fa-thumbs-o-down">帰れ</i></a>]
                    <?php echo $nikuman['like_count']; ?>
               <?php } ?>
               [<a href="like_user.php?tweet_id=<?php echo $nikuman['tweet_id'];?>">好きな人</a>]
          </p>
          <p class="day">
            <a href="view.html">
              2016-01-28 18:04
            </a>
             <?php if($login_member['member_id'] == $nikuman['member_id']){ ?>
            [<a href="edit.php?id=<?php echo $nikuman['tweet_id']; ?>" style="color: #00994C;">編集</a>]
            [<a href="delete.php?id=<?php echo $nikuman['tweet_id']; ?>"  style="color: #F33;" onclick=" return confirm('本当に削除しますか？')">削除</a>]
             <?php } ?>
             [<a href="view.php?tweet_id=<?php echo $nikuman['tweet_id']; ?>">投稿を見る</a>]
             <?php if($nikuman['reply_tweet_id'] >=1){ ?>
              [<a href="view.php?tweet_id=<?php echo $nikuman['reply_tweet_id']; ?>" style="color: #a9a9a9;">返信元のメッセージ</a>]
              <?php } ?>
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
