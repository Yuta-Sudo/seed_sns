<?php
session_start();
require('../dbconnect.php');
// echo('<br>');
// echo('<br>');
// echo('<pre>');
// var_dump($_POST) ;
// echo('</pre>');
if (!empty($_POST)) {
  //入力チェック
  //$_POSTの値が空だった時に$ERRORという配列にエラーの情報を格納する
  if ($_POST['nick_name']=='') {
    $error['nick_name'] ='blank';
    }
  if ($_POST['email']=='') {
    $error['email'] ='blank';
    }
  if ($_POST['password']=='') {
    $error['password'] ='blank';
    }elseif(strlen($_POST['password'])<4){
      $error['password'] = 'length';
    }
    //入力チェック後エラーがなければ次のcheck.phpに遷移する
    //$errorという変数が存在していなかった場合、入力が正常と認識
    if (!isset($error)) {
      //emailの重複チェック
      //DBに同じemailの登録があるかチェックする
      //なぜ？ ー＞ 重複していた場合、メールやselect文での取得の際に重複してしまう可能性あり

      //検索条件にヒットした場合を取得するSQL文を書く必要がある
      //count() sql分のカウント。ヒットした数を取得する
      $sql= 'SELECT COUNT(*) AS `mail_count` FROM `members` WHERE `email` = ?';
      $data = array($_POST['email']);
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);

      //重複しているか結果の取得
      $mail_count = $stmt->fetch(PDO::FETCH_ASSOC);

      // もし$mail_count['mail_count']が１以上の時
      if ($mail_count['mail_count'] >= 1) {
        //重複エラー
        $error['email'] = 'duplicated';
      }
      //の$error['email']が入っていない時
      if(!isset($error)){
        //画像の拡張子チェック(画像が送られてきているかどうか、他のファイルなどの拡張子でないかのチェック)
        //jpg,png,gif 拡張子はOK

        $ext = substr($_FILES['picture_path']['name'], -3);
        $ext = strtolower($ext);
        // date関数で確認ボタンを押した時日付を取得し、ファイル名に文字列を加える。
        // 拡張子の判定
        // 拡張子がjpg,またはgif,pngのいずれか時の時
        if ($ext== 'jpg' || $ext== 'png' || $ext== 'gif' ){
          $picture_path = date('YmdHis') . $_FILES['picture_path']['name'];
        //画像のアップロード
        // move_uploaded_file(ファイル名、保存先のディレクトリの位置)
        move_uploaded_file($_FILES['picture_path']['tmp_name'], '../picture_path/'.$picture_path);
        //$_SESSION = $_SESSION変数に入力された値を代入
        //注意！！！！！！！！！$_SESSIONを使用する際、ファイルの１番上にsession_start()を記入

        $_SESSION['join'] = $_POST;
        $_SESSION['join']['picture_path'] = $picture_path ;
// echo('<pre>');
// var_dump($_FILES) ;
// echo('</pre>');
// echo('<pre>');
// var_dump($_SESSION['join']['picture_path']) ;
// echo('</pre>');
// echo('<pre>');
// var_dump($nick_name) ;
// echo('</pre>');
// echo('<pre>');
// var_dump($email) ;
// echo('</pre>');
// echo('<pre>');
// var_dump($password) ;
// echo('</pre>');

        //値の取り方 (２次元配列)$_SESSION['join']['nick_name']
        // check.phpに強制的に遷移する
        header('Location: check.php');

        // check.phpに移動する
        //上の処理を無駄に処理しないように、このページで処理を終了させる
          exit();
          }else{
            $error['image']  = 'type';
       }
     }
    }
  }
// echo '<pre>';
// var_dump($_FILES) ;
// echo  '</pre>';

// echo '<pre>';
// var_dump($picture_path);
// echo  '</pre>';

// echo '<pre>';
// var_dump($_FILES['picture_path']['name']);
// echo '</pre>';

// echo '<pre>';
// var_dump($_SESSION);
// echo '</pre>';
?>



<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SeedSNS</title>

    <!-- Bootstrap -->
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../assets/css/form.css" rel="stylesheet">
    <link href="../assets/css/timeline.css" rel="stylesheet">
    <link href="../assets/css/main.css" rel="stylesheet">
    <!--
      designフォルダ内では2つパスの位置を戻ってからcssにアクセスしていることに注意！
     -->

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
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3 content-margin-top">
        <legend>会員登録</legend>
        <!-- 画像を送る際の注意点 -->
        <!-- multipart/form-data -->
        <!-- inputタグをファイルにする -->
        <form method="post" action="" class="form-horizontal" role="form" enctype="multipart/form-data">
          <!-- ニックネーム -->
          <div class="form-group">
            <label class="col-sm-4 control-label">ニックネーム</label>
            <div class="col-sm-8">
              <input type="text" name="nick_name" class="form-control" placeholder="例： Seed kun">
              <?php if (isset($error['nick_name'])&& $error['nick_name']=='blank') { ?>
                <p class="error">ニックネームを入力してください</p>
              <?php } ?>
            </div>
          </div>
          <!-- メールアドレス -->
          <div class="form-group">
            <label class="col-sm-4 control-label">メールアドレス</label>
            <div class="col-sm-8">
              <input type="email" name="email" class="form-control" placeholder="例： seed@nex.com">
               <?php if (isset($error['email'])&& $error['email']=='blank') { ?>
                <p class="error">メールアドレスを入力してください</p>
              <?php } elseif(isset($error['email'])&& $error['email']=='duplicated'){?>
              <p class="error">* 入力されたメールアドレスは登録済みです </p>
              <?php } ?>
            </div>
          </div>
          <!-- パスワード -->
          <div class="form-group">
            <label class="col-sm-4 control-label">パスワード</label>
            <div class="col-sm-8">
              <input type="password" name="password" class="form-control" placeholder="">
              <?php if (isset($error['password']) && $error['password'] == 'blank') { ?>
              <p class="error">* パスワードを入力してください。</p>
              <?php } elseif(isset($error['password']) && $error['password'] == 'length') { ?>
              <p class="error">* パスワードは4文字以上入力してください。</p>
              <?php } ?>
            </div>
          </div>
          <!-- プロフィール写真 -->
          <div class="form-group">
            <label class="col-sm-4 control-label">プロフィール写真</label>
            <div class="col-sm-8">
              <input type="file" name="picture_path" class="form-control">
              <?php if (isset($error['image']) && $error['image'] == 'type') { ?>
              <p class="error">* jpg、png、gifのいずれかの拡張子を選んでください。</p>
              <?php } ?>
            </div>
          </div>

          <input type="submit" class="btn btn-default" value="確認画面へ">
        </form>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="../../assets/js/jquery-3.1.1.js"></script>
    <script src="../../assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="../../assets/js/bootstrap.js"></script>
  </body>
</html>
