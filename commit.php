<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta charset="http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
    <?php
    define('DACCESS',1);
    include 'includes/defines.php';
    include 'libraries/Database.php';
    include 'classes/openITIreland/class.nielsen.php';

    $user='admin';
    $pass='';
    $host='localhost';
    $mySqlDumpExe 	= 'c:\wamp\bin\mysql\mysql5.6.17\bin\mysqldump ';
    $mySqlExe 	= 	'c:\wamp\bin\mysql\mysql5.6.17\bin\mysql ';

    $mySqlCred 	= 	'--user='.$user.
    ' --password='.$pass .
    ' --host='.$host;

    $mySqlSrcDB = ' i561957_irishinterest ';
    $mySqlTrgDB = ' dev_irishinterest ';

    $mySqlTbl =  ' authors author_x_book categories publications publishers ';

    echo "Copying Files ...<br>";
    /*	mysqldump 	*/
    $cmd = $mySqlDumpExe.$mySqlCred.$mySqlSrcDB.$mySqlTbl. ' > temp.sql';
    exec($cmd, $output, $return);

    /* Import	*/
    $cmd = $mySqlExe . $mySqlTrgDB . ' < temp.sql';
    exec($cmd, $output, $return);

    if ($return != 0) { //0 is ok
    die('Error: ' . implode("\r\n", $output));
}

echo "Complete<br>";
echo 'Copied  '.$mySqlTbl.'from '.$mySqlSrcDB.' to '.$mySqlTrgDB.'<br>';

/* Strip embedded '-' from Books ISBN13 field in staging database. */
$db = new Database;
$rpl = array('-',' ','.');
$sql = 'select * from publications ';
$db->query($sql);
$books = $db->loadObjectList();
foreach($books as $book){
if(!$book->isbn13 == '') {
echo '<br>'.$book->isbn13;
$isbn13 = str_replace($rpl,'',$book->isbn13);
if($isbn13 != $book->isbn13) {
echo ' -> '.$isbn13;
$sql = 'update publications set isbn13	 = "'.$isbn13 . '" where id = '.$book->id;
//echo '<br>'.$sql.'<br>';
$db->query($sql);
}
}
}
	
?>
</body>
</html>