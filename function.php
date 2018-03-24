<?php 

session_start();
require('dbconnect.php');

//関数とは、一定の処理をまとめて名前をつけておいているプログラムの片町
//なんども同じ処理を行う際に便利


	function login_check(){

		if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
			//ログインしている
			//ログイン時間の更新
			$_SESSION['time'] = time();
			//ログインユーザー情報取得
			
			}
			else{
			//ログインしていない
			//ログイン画面へ強制遷移する
			header( "Location: login.php ");
			exit;			}
 }




 function deleteronri(){
 	if(ture){
  require('dbconnect.php');

  $sql = 'UPDATE `tweets` SET `delete_flag` = 1  WHERE `tweet_id`=?';
  $delete = array($_GET['id']);
  $stmt = $dbh->prepare($sql);
  $stmt->execute($delete); 
  header('Location: index.php');
  exit();}
 }




 function liken()
 {require('dbconnect.php');
 	if( $_GET['action'] == 'like'){

 $sql ='INSERT INTO `likes` SET  `member_id`=? , `tweet_id`=?';
      $data = array($_SESSION['id'], $_GET['id']);
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);
  header('Location: index.php?page='. $_GET['page']);
  exit();}
  if( $_GET['action'] == 'dislike'){
  	  $sql ='DELETE FROM `likes` WHERE `tweet_id`=? AND`member_id`= ?LIMIT 1';
      $data = array( $_GET['id'],$_SESSION['id']);
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);
  header('Location: index.php?page='. $_GET['page']);
  exit();
  }
}



 ?>