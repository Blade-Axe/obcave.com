<?php
require_once('assets/init.php');
if (!empty($_GET)) {
    foreach ($_GET as $key => $value) {
        $value = preg_replace('/on[^<>=]+=[^<>]*/m', '', $value);
        $_GET[$key] = strip_tags($value);
    }
}
if (!empty($_REQUEST)) {
    foreach ($_REQUEST as $key => $value) {
        $value = preg_replace('/on[^<>=]+=[^<>]*/m', '', $value);
        $_REQUEST[$key] = strip_tags($value);
    }
}
if (!empty($_POST)) {
    foreach ($_POST as $key => $value) {
        $value = preg_replace('/on[^<>=]+=[^<>]*/m', '', $value);
        $_POST[$key] = strip_tags($value);
    }
}
if (IS_LOGGED == false) {
    header("Location: $site_url");
    exit();
}
if (IsAdmin() == false) {
    header("Location: $site_url");
    exit();
}
$path = (!empty($_GET['path'])) ? getPageFromPathAdmin($_GET['path']) : null;
$files = scandir('admin-panel/pages');
unset($files[0]);
unset($files[1]);
unset($files[2]);
$page = 'dashboard';
if (!empty($path['page']) && in_array($path['page'], $files) && file_exists('admin-panel/pages/'.$path['page'].'/content.html')) {
    $page = $path['page'];
}
$data = array();
$text = LoadAdminPage($page.'/content');
?>
<input type="hidden" id="json-data" value='<?php echo htmlspecialchars(json_encode($data));?>'>
<?php
echo $text;
?>