<?php
/** Mini Adminer Lite **/
$DB_HOST='127.0.0.1';
$DB_USER='root';
$DB_PASS='';
$DB_NAME='my_database';
$ADMIN_PASS='change_me';

session_start();
if(isset($_POST['login_pass'])){
 if($_POST['login_pass']===$ADMIN_PASS){$_SESSION['logged_in']=1;}else{$err='Salah password';}
}
if(isset($_GET['logout'])){session_destroy();header('Location:?');exit;}
if(empty($_SESSION['logged_in'])){
echo "<h3>Login</h3>".(isset($err)?"<p style='color:red'>$err</p>":'')."<form method=post><input type=password name=login_pass><button>Login</button></form>";exit;
}
try{$pdo=new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",$DB_USER,$DB_PASS,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);}catch(Exception $e){exit($e->getMessage());}
function h($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
function is_select($sql){return preg_match('/^(SELECT|SHOW|DESCRIBE|EXPLAIN)\b/i',ltrim($sql));}
if(isset($_GET['dump'])){
 header('Content-Type: application/sql');
 header('Content-Disposition: attachment; filename="dump.sql"');
 $tables=$pdo->query('SHOW FULL TABLES WHERE Table_type="BASE TABLE"')->fetchAll(PDO::FETCH_NUM);
 foreach($tables as $t){$t=$t[0];$create=$pdo->query("SHOW CREATE TABLE `$t`")->fetch(PDO::FETCH_ASSOC);echo $create['Create Table'].";\n\n";$rows=$pdo->query("SELECT * FROM `$t`")->fetchAll(PDO::FETCH_ASSOC);foreach($rows as $r){$cols=array_map(fn($c)=>"`$c`",array_keys($r));$vals=array_map(fn($v)=>is_null($v)?'NULL':$pdo->quote($v),$r);echo"INSERT INTO `$t`(".implode(',',$cols).") VALUES(".implode(',',$vals).");\n";}echo"\n";}exit;
}
$sql_res='';if(!empty($_POST['sql'])){$q=$_POST['sql'];try{if(is_select($q)){$rows=$pdo->query($q)->fetchAll(PDO::FETCH_ASSOC);if($rows){$sql_res.='<table border=1><tr>';foreach(array_keys($rows[0])as$c)$sql_res.='<th>'.h($c).'</th>';foreach($rows as$r){$sql_res.='<tr>';foreach($r as$v)$sql_res.='<td>'.h($v).'</td>';}$sql_res.='</table>';}}else{$a=$pdo->exec($q);$sql_res="Query OK. Rows: $a";}}catch(Exception $e){$sql_res='<p style=color:red>'.h($e->getMessage()).'</p>';}}
$tables=$pdo->query('SHOW FULL TABLES WHERE Table_type="BASE TABLE"')->fetchAll(PDO::FETCH_NUM);
$preview='';if(!empty($_GET['preview'])){$t=$_GET['preview'];$rows=$pdo->query("SELECT * FROM `$t` LIMIT 200")->fetchAll(PDO::FETCH_ASSOC);if($rows){$preview.='<h4>'.h($t).'</h4><table border=1><tr>';foreach(array_keys($rows[0])as$c)$preview.='<th>'.h($c).'</th>';foreach($rows as$r){$preview.='<tr>';foreach($r as$v)$preview.='<td>'.h($v).'</td>';}}}
echo "<h3>Mini Adminer</h3><a href=?logout=1>Logout</a> | <a href=?dump=1>Dump DB</a><hr><div style='display:flex;gap:10px'><div><h4>Tables</h4><ul>";foreach($tables as$t)echo"<li><a href=?preview=".h($t[0]).">".h($t[0])."</a></li>";echo"</ul><form method=post><textarea name=sql rows=6 cols=25></textarea><br><button>Execute</button></form></div><div style='flex:1'><div>$sql_res</div><div>$preview</div></div></div>";
