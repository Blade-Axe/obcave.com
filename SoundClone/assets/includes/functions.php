<?php  
require_once "assets/includes/app.php";
use Twilio\Rest\Client;
function addhttp($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}
function GetThemes() {
    global $ask;
    $themes = glob('themes/*', GLOB_ONLYDIR);
    return $themes;
}
function Backup($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, $tables = false, $backup_name = false) {
    $mysqli = new mysqli($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name);
    $mysqli->select_db($sql_db_name);
    $mysqli->query("SET NAMES 'utf8'");
    $queryTables = $mysqli->query('SHOW TABLES');
    while ($row = $queryTables->fetch_row()) {
        $target_tables[] = $row[0];
    }
    if ($tables !== false) {
        $target_tables = array_intersect($target_tables, $tables);
    }
    $content = "-- phpMyAdmin SQL Dump
-- http://www.phpmyadmin.net
--
-- Host Connection Info: " . $mysqli->host_info . "
-- Generation Time: " . date('F d, Y \a\t H:i A ( e )') . "
-- Server version: " . mysqli_get_server_info($mysqli) . "
-- PHP Version: " . PHP_VERSION . "
--\n
SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
SET time_zone = \"+00:00\";\n
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;\n\n";
    foreach ($target_tables as $table) {
        $result        = $mysqli->query('SELECT * FROM ' . $table);
        $fields_amount = $result->field_count;
        $rows_num      = $mysqli->affected_rows;
        $res           = $mysqli->query('SHOW CREATE TABLE ' . $table);
        $TableMLine    = $res->fetch_row();
        $content       = (!isset($content) ? '' : $content) . "
-- ---------------------------------------------------------
--
-- Table structure for table : `{$table}`
--
-- ---------------------------------------------------------
\n" . $TableMLine[1] . ";\n";
        for ($i = 0, $st_counter = 0; $i < $fields_amount; $i++, $st_counter = 0) {
            while ($row = $result->fetch_row()) {
                if ($st_counter % 100 == 0 || $st_counter == 0) {
                    $content .= "\n--
-- Dumping data for table `{$table}`
--\n\nINSERT INTO " . $table . " VALUES";
                }
                $content .= "\n(";
                for ($j = 0; $j < $fields_amount; $j++) {
                    $row[$j] = str_replace("\n", "\\n", addslashes($row[$j]));
                    if (isset($row[$j])) {
                        $content .= '"' . $row[$j] . '"';
                    } else {
                        $content .= '""';
                    }
                    if ($j < ($fields_amount - 1)) {
                        $content .= ',';
                    }
                }
                $content .= ")";
                if ((($st_counter + 1) % 100 == 0 && $st_counter != 0) || $st_counter + 1 == $rows_num) {
                    $content .= ";\n";
                } else {
                    $content .= ",";
                }
                $st_counter = $st_counter + 1;
            }
        }
        $content .= "";
    }
    $content .= "
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";
    if (!file_exists('script_backups/' . date('d-m-Y'))) {
        @mkdir('script_backups/' . date('d-m-Y'), 0777, true);
    }
    if (!file_exists('script_backups/' . date('d-m-Y') . '/' . time())) {
        mkdir('script_backups/' . date('d-m-Y') . '/' . time(), 0777, true);
    }
    if (!file_exists("script_backups/" . date('d-m-Y') . '/' . time() . "/index.html")) {
        $f = @fopen("script_backups/" . date('d-m-Y') . '/' . time() . "/index.html", "a+");
        @fwrite($f, "");
        @fclose($f);
    }
    if (!file_exists('script_backups/.htaccess')) {
        $f = @fopen("script_backups/.htaccess", "a+");
        @fwrite($f, "deny from all\nOptions -Indexes");
        @fclose($f);
    }
    if (!file_exists("script_backups/" . date('d-m-Y') . "/index.html")) {
        $f = @fopen("script_backups/" . date('d-m-Y') . "/index.html", "a+");
        @fwrite($f, "");
        @fclose($f);
    }
    if (!file_exists('script_backups/index.html')) {
        $f = @fopen("script_backups/index.html", "a+");
        @fwrite($f, "");
        @fclose($f);
    }
    $folder_name = "script_backups/" . date('d-m-Y') . '/' . time();
    $put         = @file_put_contents($folder_name . '/SQL-Backup-' . time() . '-' . date('d-m-Y') . '.sql', $content);
    if ($put) {
        $rootPath = realpath('./');
        $zip      = new ZipArchive();
        $open     = $zip->open($folder_name . '/Files-Backup-' . time() . '-' . date('d-m-Y') . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($open !== true) {
            return false;
        }
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($files as $name => $file) {
            if (!preg_match('/\bscript_backups\b/', $file)) {
                if (!$file->isDir()) {
                    $filePath     = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rootPath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }
        $zip->close();
        $table = T_CONFIG;
        $date  = date('d-m-Y');
        $mysqli->query("UPDATE `$table` SET `value` = '$date' WHERE `name` = 'last_backup'");
        $mysqli->close();
        return true;
    } else {
        return false;
    }
}
function custom_design($a = false,$code = array()){
    global $music;
    $theme       = $music->config->theme;
    $data        = array();
    $custom_code = array(
        "themes/$theme/js/header.js",
        "themes/$theme/js/footer.js",
        "themes/$theme/css/custom.style.css",
    );

    if ($a == 'get') {
        foreach ($custom_code as $key => $filepath) {
            if (is_readable($filepath)) {
                $data[$key] = file_get_contents($filepath);
            }
            else{
                $data[$key] = "/* \n Error found while loading: Permission denied in $filepath \n*/";
            }
        }
    }

    else if($a == 'save' && !empty($code)){
        foreach ($code as $key => $content) {
            $filepath = $custom_code[$key];

            if (is_writable($filepath)) {
                @file_put_contents($custom_code[$key],$content);
            }

            else{
                $data[$key] = "Permission denied: $filepath is not writable";
            }
        }
    }

    return $data;
}
function GetRecommendedSongs(){
    global $music,$db;
    $interests = [];
    $data = [];
    if (IS_LOGGED === false) {
        $category_interests = $db->arrayBuilder()->get(T_USER_INTEREST,5,array('category_id'));
    }
    else{
        $category_interests = $db->arrayBuilder()->where('user_id',$music->user->id)->get(T_USER_INTEREST,null,array('category_id'));
        if (empty($category_interests)) {
            $category_interests = $db->arrayBuilder()->get(T_USER_INTEREST,5,array('category_id'));
        }
    }
    if (!empty($category_interests)) {
        foreach ($category_interests as $key => $value){
            $interests[$value['category_id']] = (int)$value['category_id'];
        }
        if (!empty($interests)) {
            $recommended = $db->arrayBuilder()->where('availability', 0)->where('category_id',array_keys($interests),'IN')->orderBy('id','DESC')->get(T_SONGS,10,array('id'));
            foreach ($recommended as $key => $value){
                $data[$key] = songData( (int)$value['id'] );
            }
        }
    }
    return $data;
}
function checkUserInterest(){
    global $music,$db;
    $category_interests = $db->arrayBuilder()->where('user_id',$music->user->id)->get(T_USER_INTEREST,null,array('category_id'));
    if( empty($category_interests) ){
        return false;
    }else{
        return true;
    }
}
function getUserInterest(){
    global $music,$db;
    $interests = [];
    $category_interests = $db->arrayBuilder()->where('user_id',$music->user->id)->get(T_USER_INTEREST,null,array('category_id'));
    if( empty($category_interests) ){
        return [];
    }else{
        foreach ($category_interests as $key => $value){
            $interests[$value['category_id']] = (int)$value['category_id'];
        }
        return $interests;
    }
}
function ImportImageFromFile($media, $custom_name = '_url_image') {
    if (empty($media)) {
        return false;
    }
    if (!file_exists('upload/photos/' . date('Y'))) {
        mkdir('upload/photos/' . date('Y'), 0777, true);
    }
    if (!file_exists('upload/photos/' . date('Y') . '/' . date('m'))) {
        mkdir('upload/photos/' . date('Y') . '/' . date('m'), 0777, true);
    }
    $extension = 0; //image_type_to_extension($size[2]);
    if (empty($extension)) {
        $extension = '.jpg';
    }
    $dir               = 'upload/photos/' . date('Y') . '/' . date('m');
    $file_dir          = $dir . '/' . GenerateKey() . $custom_name . $extension;
    $fileget           = file_get_contents($media);
    if (!empty($fileget)) {
        $importImage = @file_put_contents($file_dir, $fileget);
    }
    if (file_exists($file_dir)) {
        $upload_s3 = ShareFile($file_dir);
        $check_image = getimagesize($file_dir);
        if (!$check_image) {
            unlink($file_dir);
        }
        return $file_dir;
    } else {
        return false;
    }
}
function GetBanned($type = '') {
    global $sqlConnect;
    $data  = array();
    $query = mysqli_query($sqlConnect, "SELECT * FROM " . T_BANNED_IPS . " ORDER BY id DESC");
    if ($type == 'user') {
        while ($fetched_data = mysqli_fetch_assoc($query)) {
            if (filter_var($fetched_data['ip_address'], FILTER_VALIDATE_IP)) {
                $data[] = $fetched_data['ip_address'];
            }
        }
    } else {
        while ($fetched_data = mysqli_fetch_assoc($query)) {
            $data[] = $fetched_data;
        }
    }
    return $data;
}
function get_announcments() {
    global $music, $db;
    if (IS_LOGGED === false) {
        return false;
    }

    $views_table  = T_ANNOUNCEMENT_VIEWS;
    $table        = T_ANNOUNCEMENTS;
    $user         = $music->user->id;
    $subsql       = "SELECT `announcement_id` FROM `$views_table` WHERE `user_id` = '{$user}'";
    $fetched_data = $db->where(" `active` = '1' AND `id` NOT IN ({$subsql}) ")->orderBy('RAND()')->getOne(T_ANNOUNCEMENTS);
    return $fetched_data;
}
function UploadLogo($data = array()) {
    global $music;
    if (isset($data['file']) && !empty($data['file'])) {
        $data['file'] = Secure($data['file']);
    }
    if (isset($data['name']) && !empty($data['name'])) {
        $data['name'] = Secure($data['name']);
    }
    if (isset($data['name']) && !empty($data['name'])) {
        $data['name'] = Secure($data['name']);
    }
    if (empty($data)) {
        return false;
    }
    $allowed           = 'png';
    $new_string        = pathinfo($data['name'], PATHINFO_FILENAME) . '.' . strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
    $extension_allowed = explode(',', $allowed);
    $file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
    if (!in_array($file_extension, $extension_allowed)) {
        return false;
    }
    $logo_name = 'logo';
    if (!empty($data['light-logo'])) {
        $logo_name = 'logo-white';
    }
    if (!empty($data['favicon'])) {
        $logo_name = 'icon';
    }
    if (!empty($data['homelogo'])) {
        $logo_name = 'home-logo';
    }
    $dir      = "themes/" . $music->config->theme . "/img/";
    $filename = $dir . "$logo_name.png";
    if (move_uploaded_file($data['file'], $filename)) {
        return true;
    }
}
function LoadAdminLinkSettings($link = '') {
    global $site_url;
    return $site_url . '/admin-cp/' . $link;
}
function LoadAdminLink($link = '') {
    global $site_url;
    return $site_url . '/admin-panel/' . $link;
}
function LoadAdminPage($page_url = '', $data = array(), $set_lang = true) {
    global $music, $lang_array, $config, $db;
    $page = './admin-panel/pages/' . $page_url . '.html';

    if (!file_exists($page)) {
        return false;
    }
    $page_content = '';
    ob_start();
    require($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    if ($set_lang == true) {
        $page_content = preg_replace_callback("/{{LANG (.*?)}}/", function($m) use ($lang_array) {
            return lang($m[1]);
        }, $page_content);
    }

    if (!empty($data) && is_array($data)) {
        foreach ($data as $key => $replace) {
            if ($key == 'USER_DATA') {
                $replace = ToArray($replace);
                $page_content = preg_replace_callback("/{{USER (.*?)}}/", function($m) use ($replace) {
                    return (isset($replace[$m[1]])) ? $replace[$m[1]] : '';
                }, $page_content);
            } else {
                if( is_array($replace) || is_object($replace) ){
                    $arr = explode('_',$key);
                    $k = strtoupper($arr[0]);
                    $replace = ToArray($replace);
                    $page_content = preg_replace_callback("/{{".$k." (.*?)}}/", function($m) use ($replace) {
                        return (isset($replace[$m[1]])) ? $replace[$m[1]] : '';
                    }, $page_content);
                }else{
                    $object_to_replace = "{{" . $key . "}}";
                    $page_content      = str_replace($object_to_replace, $replace, $page_content);
                }
            }
        }
    }
    if (IS_LOGGED == true) {
        $replace = ToArray($music->user);
        $page_content = preg_replace_callback("/{{ME (.*?)}}/", function($m) use ($replace) {
            return (isset($replace[$m[1]])) ? $replace[$m[1]] : '';
        }, $page_content);
    }
    $page_content = preg_replace("/{{LINK (.*?)}}/", UrlLink("$1"), $page_content);
    $page_content = preg_replace_callback("/{{CONFIG (.*?)}}/", function($m) use ($config) {
        return (isset($config[$m[1]])) ? $config[$m[1]] : '';
    }, $page_content);
    return $page_content;
}
function UrlLink($string) {
    global $site_url;
    return rtrim($site_url ,'/') . str_replace('//','/','/' . $string);
}
function UpdateAdminDetails() {
    global $music, $db;

    $get_songs_count = $db->getValue(T_SONGS, 'count(*)');
    $update_questions_count = $db->where('name', 'total_songs')->update(T_CONFIG, array('value' => $get_songs_count));

    $get_albums_count = $db->getValue(T_ALBUMS, 'count(*)');
    $update_albums_count = $db->where('name', 'total_albums')->update(T_CONFIG, array('value' => $get_albums_count));

    $get_plays_count = $db->getValue(T_VIEWS, 'count(*)');
    $update_albums_count = $db->where('name', 'total_plays')->update(T_CONFIG, array('value' => $get_plays_count));

    $get_sales_count = number_format($db->getValue(T_PURCHAES, 'SUM(final_price)'), 2);
    $update_sales_count = $db->where('name', 'total_sales')->update(T_CONFIG, array('value' => $get_sales_count));

    $get_users_count = $db->getValue(T_USERS, 'count(*)');
    $update_users_count = $db->where('name', 'total_users')->update(T_CONFIG, array('value' => $get_users_count));

    $get_artists_count = $db->where('artist', '1')->getValue(T_USERS, 'count(*)');
    $update_artists_count = $db->where('name', 'total_artists')->update(T_CONFIG, array('value' => $get_artists_count));

    $get_playlists_count = $db->getValue(T_PLAYLISTS, 'count(*)');
    $update_playlists_count = $db->where('name', 'total_playlists')->update(T_CONFIG, array('value' => $get_playlists_count));

    $get_unactive_users_count = $db->where('active', '0')->getValue(T_USERS, 'count(*)');
    $update_unactive_users_count = $db->where('name', 'total_unactive_users')->update(T_CONFIG, array('value' => $get_unactive_users_count));

    $user_statics = array();
    $songs_statics = array();

    $months = array('1','2','3','4','5','6','7','8','9','10','11','12');
    $date = date('Y');

    foreach ($months as $value) {
        $monthNum  = $value;
        $dateObj   = DateTime::createFromFormat('!m', $monthNum);
        $monthName = $dateObj->format('F');
        $user_statics[] = array('month' => $monthName, 'new_users' => $db->where('registered', "$date/$value")->getValue(T_USERS, 'count(*)'));
        $songs_statics[] = array('month' => $monthName, 'new_songs' => $db->where('YEAR(FROM_UNIXTIME(`time`))', "$date")->where('MONTH(FROM_UNIXTIME(`time`))', "$value")->getValue(T_SONGS, 'count(*)'));
    }
    $update_user_statics = $db->where('name', 'user_statics')->update(T_CONFIG, array('value' => json_encode($user_statics)));
    $update_songs_statics = $db->where('name', 'songs_statics')->update(T_CONFIG, array('value' => json_encode($songs_statics)));

    $update_saved_count = $db->where('name', 'last_admin_collection')->update(T_CONFIG, array('value' => time()));
}
function getConfig() {
	global $db;
    $data  = array();
    $configs = $db->get(T_CONFIG);
    foreach ($configs as $key => $config) {
        $data[$config->name] = $config->value;
    }
    return $data;
}
function lang($string = '') {
	global $lang_array, $music, $db;
    $dev = true;
    $string = trim($string);
	$stringFromArray = strtolower(preg_replace('/[^a-zA-Z0-9-_\.]/','_', $string));
	if (in_array($stringFromArray, array_keys($lang_array))) {
		return $lang_array[$stringFromArray];
	}
    if ($dev == true) {
       //$insert = $db->insert(T_LANGS, ['ref' => '', 'options' => '' , 'lang_key' => $stringFromArray, 'english' => secure($string)]);
    } else {
        return '';
    }
	$lang_array[$stringFromArray] = $string;
	return $string;
}
function userData($user_id = 0, $options = array()) {
    global $db, $music, $lang, $countries_name;
    if (!empty($options['data'])) {
        $fetched_data   = $user_id;
    } 

    else {
        $fetched_data   = $db->where('id', $user_id)->getOne(T_USERS);
    }

    if (empty($fetched_data)) {
        return false;
    }

    if (empty($fetched_data->name)) {
        $fetched_data->name   = $fetched_data->username;
    }
    $fetched_data->or_avatar = $fetched_data->avatar;
    $fetched_data->or_cover = $fetched_data->cover;

    $fetched_data->avatar = getMedia($fetched_data->avatar);
    $fetched_data->cover  = getMedia($fetched_data->cover);
    $fetched_data->url    = getLink($fetched_data->username);
    $fetched_data->about_decoded = br2nl($fetched_data->about);

    if (!empty($fetched_data->name)) {
        $fetched_data->name = $fetched_data->name;
    }

    if (empty($fetched_data->about)) {
        $fetched_data->about = '';
    }
    $fetched_data->balance  = number_format($fetched_data->balance, 2);
    $fetched_data->name_v   = $fetched_data->name;
    if ($fetched_data->verified == 1 && $music->config->verification_badge == 'on') {
        $fetched_data->name_v = $fetched_data->name . ' <i class="fa fa-check-circle fa-fw verified"></i>';
    }
    $fetched_data->country_name = '';
    if (!empty($fetched_data->country_id) && !empty($countries_name) && in_array($fetched_data->country_id, array_keys($countries_name))) {
        $fetched_data->country_name  = $countries_name[$fetched_data->country_id];
    }
    
    @$fetched_data->gender_text  = ($fetched_data->gender == 'male') ? $lang->male : $lang->female;
    return $fetched_data;
}
function isUserActive($user_id = 0) {
    global $db;
    $db->where('active', '1');
    $db->where('id', secure($user_id));
    return ($db->getValue(T_USERS, 'count(*)') > 0) ? true : false;
}
function EmailExists($email = '') {
    global $db;
    return ($db->where('email', secure($email))->getValue(T_USERS, 'count(*)') > 0) ? true : false;
}
function UsernameExits($username = '') {
    global $db;
    return ($db->where('username', secure($username))->getValue(T_USERS, 'count(*)') > 0) ? true : false;
}
function createUserSession($user_id = 0,$platform = 'web') {
    global $db,$sqlConnect, $music;
    if (empty($user_id)) {
        return false;
    }
    $session_id          = sha1(rand(11111, 99999)) . time() . md5(microtime() . $user_id);
    $insert_data         = array(
        'user_id' => $user_id,
        'session_id' => $session_id,
        'platform' => $platform,
        'time' => time()
    );

    $insert              = $db->insert(T_SESSIONS, $insert_data);

    $_SESSION['user_id'] = $session_id;
    setcookie("user_id", $session_id, time() + (10 * 365 * 24 * 60 * 60), "/");
    $music->loggedin = true;

    $query_two = mysqli_query($sqlConnect, "DELETE FROM " . T_APP_SESSIONS . " WHERE `session_id` = '{$session_id}'");
    if ($query_two) {
        $ua = serialize(getBrowser());
        $delete_same_session = $db->where('user_id', $user_id)->where('platform_details', $ua)->delete(T_APP_SESSIONS);
        $query_three = mysqli_query($sqlConnect, "INSERT INTO " . T_APP_SESSIONS . " (`user_id`, `session_id`, `platform`, `platform_details`, `time`) VALUES('{$user_id}', '{$session_id}', 'web', '$ua'," . time() . ")");
        if ($query_three) {
            return $session_id;
        }
    }
}
function getBrowser() {
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";
    // First get the platform?
    if (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    } elseif (preg_match('/iphone|IPhone/i', $u_agent)) {
        $platform = 'IPhone Web';
    } elseif (preg_match('/android|Android/i', $u_agent)) {
        $platform = 'Android Web';
    } else if (preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $u_agent)) {
        $platform = 'Mobile';
    } else if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif(preg_match('/Firefox/i',$u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif(preg_match('/Chrome/i',$u_agent)) {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    } elseif(preg_match('/Safari/i',$u_agent)) {
        $bname = 'Apple Safari';
        $ub = "Safari";
    } elseif(preg_match('/Opera/i',$u_agent)) {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif(preg_match('/Netscape/i',$u_agent)) {
        $bname = 'Netscape';
        $ub = "Netscape";
    }
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        } else {
            $version= $matches['version'][1];
        }
    } else {
        $version= $matches['version'][0];
    }
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern,
        'ip_address' => get_ip_address()
    );
}
function sendMessage($data = array()) {
    global $music, $mail;
    require_once './assets/includes/mail.php';
    $email_from      = $data['from_email'] = secure($data['from_email']);
    $to_email        = $data['to_email'] = secure($data['to_email']);
    $subject         = $data['subject'];
    $data['charSet'] = secure($data['charSet']);
    
    if ($music->config->smtp_or_mail == 'mail') {
        $mail->IsMail();
    } 

    else if ($music->config->smtp_or_mail == 'smtp') {
        $mail->isSMTP();
        $mail->Host        = $music->config->smtp_host;
        $mail->SMTPAuth    = true;
        $mail->Username    = $music->config->smtp_username;
        $mail->Password    = html_entity_decode($music->config->smtp_password);
        $mail->SMTPSecure  = $music->config->smtp_encryption;
        $mail->Port        = $music->config->smtp_port;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
    } 

    else {
        return false;
    }

    $mail->IsHTML($data['is_html']);
    $mail->setFrom($data['from_email'], $data['from_name']);
    $mail->addAddress($data['to_email'], $data['to_name']);
    $mail->Subject = $data['subject'];
    $mail->CharSet = $data['charSet'];
    if (!empty($data['reply-to'])) {
        $mail->addReplyTo($data['reply-to'], $data['from_name']);
    }
    $mail->MsgHTML($data['message_body']);
    if ($mail->send()) {
        $mail->ClearAddresses();
        return true;
    }
}
function createMainSession() {
    $hash = sha1(rand(1111, 9999));
    if (!empty($_SESSION['hash'])) {
        return $_SESSION['hash'];
    }
    $_SESSION['hash'] = $hash;
    return $hash;
}
function importImageFromLogin($media) {
    global $music;
    if (!file_exists('upload/photos/' . date('Y'))) {
        mkdir('upload/photos/' . date('Y'), 0777, true);
    }
    if (!file_exists('upload/photos/' . date('Y') . '/' . date('m'))) {
        mkdir('upload/photos/' . date('Y') . '/' . date('m'), 0777, true);
    }
    $dir               = 'upload/photos/' . date('Y') . '/' . date('m');
    $file_dir          = $dir . '/' . generateKey() . '_avatar.jpg';
    $getImage          = connect_to_url($media);
    if (!empty($getImage)) {
        $importImage = file_put_contents($file_dir, $getImage);
        if ($importImage) {
            resize_Crop_Image(400, 400, $file_dir, $file_dir, 100);
        }
    }
    if (file_exists($file_dir)) {
        if ($music->config->s3_upload == 'on' || $music->config->ftp_upload = 'on') {
            PT_UploadToS3($file_dir);
        }
        return $file_dir;
    } else {
        return $music->user_default_avatar;
    }
}
function shareFile($data = array(), $type = 0) {
    global $music, $mysqli;
    $allowed = '';
    if (!file_exists('upload/photos/' . date('Y'))) {
        @mkdir('upload/photos/' . date('Y'), 0777, true);
    }
    if (!file_exists('upload/photos/' . date('Y') . '/' . date('m'))) {
        @mkdir('upload/photos/' . date('Y') . '/' . date('m'), 0777, true);
    }
    if (!file_exists('upload/audio/' . date('Y'))) {
        @mkdir('upload/audio/' . date('Y'), 0777, true);
    }
    if (!file_exists('upload/audio/' . date('Y') . '/' . date('m'))) {
        @mkdir('upload/audio/' . date('Y') . '/' . date('m'), 0777, true);
    }
    if (!file_exists('upload/waves/' . date('Y'))) {
        @mkdir('upload/waves/' . date('Y'), 0777, true);
    }
    if (!file_exists('upload/waves/' . date('Y') . '/' . date('m'))) {
        @mkdir('upload/waves/' . date('Y') . '/' . date('m'), 0777, true);
    }
    if (isset($data['file']) && !empty($data['file'])) {
        $data['file'] = $data['file'];
    }
    if (isset($data['name']) && !empty($data['name'])) {
        $data['name'] = secure($data['name']);
    }
    if (isset($data['name']) && !empty($data['name'])) {
        $data['name'] = secure($data['name']);
    }
    if (empty($data)) {
        return false;
    }
    $allowed           = 'jpg,png,jpeg,gif,mp3';
    if (!empty($data['allowed'])) {
        $allowed  = $data['allowed'];
    }
    if(!isset($data['name'])){
        return;
    }
    $new_string        = pathinfo($data['name'], PATHINFO_FILENAME) . '.' . strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
    $extension_allowed = explode(',', $allowed);
    $file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
    if (!in_array($file_extension, $extension_allowed)) {
        return array(
            'error' => 'File format not supported'
        );
    }
    if ($file_extension == 'jpg' || $file_extension == 'jpeg' || $file_extension == 'png' || $file_extension == 'gif') {
        $folder   = 'photos';
        $fileType = 'image';
    } else {
        $folder   = 'audio';
        $fileType = 'audio';
    }
    if (empty($folder) || empty($fileType)) {
        return false;
    }
    $ar = array(
        'audio/wav',
        'audio/mpeg',
        'audio/ogg',
        'audio/mp3',
        'image/png',
        'image/jpeg',
        'image/gif',
    );

    if (!in_array($data['type'], $ar)) {
        return array(
            'error' => 'File format not supported'
        );
    }
    $dir         = "upload/{$folder}/" . date('Y') . '/' . date('m');
    $filename    = $dir . '/' . generateKey() . '_' . date('d') . '_' . md5(time()) . "_{$fileType}.{$file_extension}";
    $second_file = pathinfo($filename, PATHINFO_EXTENSION);
    if (move_uploaded_file($data['file'], $filename)) {
        if ($second_file == 'jpg' || $second_file == 'jpeg' || $second_file == 'png' || $second_file == 'gif') {
            if ($type == 1) {
                @compressImage($filename, $filename, 50);
                $explode2  = @end(explode('.', $filename));
                $explode3  = @explode('.', $filename);
                $last_file = $explode3[0] . '_small.' . $explode2;
                @resize_Crop_Image(400, 400, $filename, $last_file, 60);

                if (($music->config->s3_upload == 'on' || $music->config->ftp_upload == 'on') && !empty($last_file)) {
                    $upload_s3 = PT_UploadToS3($last_file);
                }
            } 

            else {
                if ($second_file != 'gif') {
                    if (!empty($data['crop'])) {
                        $crop_image = resize_Crop_Image($data['crop']['width'], $data['crop']['height'], $filename, $filename, 60);
                    }
                    @compressImage($filename, $filename, 90);
                }

                if (($music->config->s3_upload == 'on' || $music->config->ftp_upload == 'on') && !empty($filename)) {
                    $upload_s3 = PT_UploadToS3($filename);
                }
            }
        }

        else{
            if (($music->config->s3_upload == 'on' || $music->config->ftp_upload == 'on') && !empty($filename)) {
               $upload_s3 = PT_UploadToS3($filename);
            }
        }

        $last_data             = array();
        $last_data['filename'] = $filename;
        $last_data['name']     = $data['name'];
        return $last_data;
    }
}
function RunInBackground($data = array()) {
    ob_end_clean();
    header("Content-Encoding: none");
    header("Connection: close");
    ignore_user_abort();
    ob_start();
    if (!empty($data)) {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
    $size = ob_get_length();
    header("Content-Length: $size");
    ob_end_flush();
    flush();
    session_write_close();
    if (is_callable('fastcgi_finish_request')) {
        fastcgi_finish_request();
    }
}
function isAdmin() {
    global $music;
    if (IS_LOGGED == false) {
        return false;
    }
    if ($music->user->admin == 1) {
        return true;
    }
    return false;
}
function isLiked($track_id = 0, $user_id = 0) {
    global $music, $db;
    if (IS_LOGGED == false) {
        return false;
    }
    if (empty($user_id)) {
        $user_id = $music->user->id;
    }
    $track_id = secure($track_id);
    $user_id = secure($user_id);
    return ($db->where('track_id', $track_id)->where('user_id', $user_id)->where('comment_id', 0)->getValue(T_LIKES, "COUNT(*)") > 0) ? true : false;
}
function isDisLiked($track_id = 0, $user_id = 0) {
    global $music, $db;
    if (IS_LOGGED == false) {
        return false;
    }
    if (empty($user_id)) {
        $user_id = $music->user->id;
    }
    $track_id = secure($track_id);
    $user_id = secure($user_id);
    return ($db->where('track_id', $track_id)->where('user_id', $user_id)->getValue(T_DISLIKES, "COUNT(*)") > 0) ? true : false;
}
function isFavorated($track_id = 0, $user_id = 0) {
    global $music, $db;
    if (IS_LOGGED == false) {
        return false;
    }
    if (empty($user_id)) {
        $user_id = $music->user->id;
    }
    $track_id = secure($track_id);
    $user_id = secure($user_id);
    return ($db->where('track_id', $track_id)->where('user_id', $user_id)->getValue(T_FOV, "COUNT(*)") > 0) ? true : false;
}
function isFollowing($following_id = 0, $follower_id = 0) {
    global $music, $db;
    if (isLogged() == false) {
        return false;
    }
    if (empty($follower_id)) {
        $follower_id = $music->user->id;
    }
    $following_id = secure($following_id);
    $follower_id = secure($follower_id);

    if( isset( $_POST['access_token']) && !empty($_POST['access_token']) ){
        return ($db->where('follower_id', $following_id)->where('following_id', $follower_id)->getValue(T_FOLLOWERS, "COUNT(*)") > 0) ? true : false;
    }else{
        return ($db->where('follower_id', $follower_id)->where('following_id', $following_id)->getValue(T_FOLLOWERS, "COUNT(*)") > 0) ? true : false;
    }
}
function getNotificationTextFromType($type = '') {
    global $music, $db;
    if (IS_LOGGED == false) {
        return false;
    }
    if (empty($type)) {
        return false;
    }

    $types = [
        'follow_user' => lang("started following you."),
        'liked_track' => lang("liked your song."),
        'reviewed_track' => lang("reviewed your song."),
        'disliked_track' => lang("disliked your song."),
        'liked_comment' => lang("liked your comment."),
        'purchased' => lang("purchased your song."),
        'approved_artist' => lang("Congratulations! Your request to become an artist was approved."),
        'decline_artist' => lang("Sadly, Your request to become an artist was declined."),
        'approve_receipt' => lang('We approved your bank transfer of %d!'),
        'disapprove_receipt' => lang('We have rejected your bank transfer, please contact us for more details.'),
        'new_track' => lang('upload new track.'),
        'comment_mention' => lang('mentioned you on a comment.')
    ];

    if (in_array($type, array_keys($types))) {
       return $types[$type];
    }
    return "";
}
function getFavButton($id, $type) {
    global $music, $db;
    if (IS_LOGGED == false) {
        return false;
    }

    if (empty($id) || empty($type)) {
        return false;
    }

    $music->favorated = false;

    if (isFavorated($id)) {
        $music->favorated = true; 
    }

    $audio_id = $db->where('id', $id)->getValue(T_SONGS, 'audio_id');

    return loadPage("buttons/$type", ['t_audio_id' => $audio_id]);
}
function getLikeButton($id, $type) {
    global $music, $db;
    if (IS_LOGGED == false) {
        return false;
    }

    if (empty($id) || empty($type)) {
        return false;
    }

    $music->liked = false;

    if (isLiked($id)) {
        $music->liked = true; 
    }

    $audio_id = $db->where('id', $id)->getValue(T_SONGS, 'audio_id');

    return loadPage("buttons/$type", ['t_audio_id' => $audio_id]);
}
function getDisLikeButton($id, $type) {
    global $music, $db;
    if (IS_LOGGED == false) {
        return false;
    }

    if (empty($id) || empty($type)) {
        return false;
    }

    $music->liked = false;
    $music->disliked = false;

    if (isDisLiked($id)) {
        $music->disliked = true;
    }

    $audio_id = $db->where('id', $id)->getValue(T_SONGS, 'audio_id');

    return loadPage("buttons/$type", ['t_audio_id' => $audio_id]);
}
function countLikes($id = 0) {
    global $music, $db;

    if (empty($id)) {
        return false;
    }

    $count = $db->where('track_id', $id)->getValue(T_LIKES, 'COUNT(*)');
    return $count;
}
function countDisLikes($id = 0) {
    global $music, $db;

    if (empty($id)) {
        return false;
    }

    $count = $db->where('track_id', $id)->getValue(T_DISLIKES, 'COUNT(*)');
    return $count;
}
function countCommentLikes($id = 0) {
    global $music, $db;

    if (empty($id)) {
        return false;
    }

    $count = $db->where('comment_id', $id)->getValue(T_LIKES, 'COUNT(*)');
    return $count;
}
function BlogcountCommentLikes($id = 0) {
    global $music, $db;

    if (empty($id)) {
        return false;
    }

    $count = $db->where('comment_id', $id)->getValue(T_BLOG_LIKES, 'COUNT(*)');
    return $count;
}
function GetAd($type, $admin = true) {
    global $db;
    $type      = Secure($type);
    $query_one = "SELECT `code` FROM " . T_ADS . " WHERE `placement` = '{$type}'";
    if ($admin === false) {
        $query_one .= " AND `active` = '1'";
    }
    $fetched_data = $db->rawQuery($query_one);
    if (!empty($fetched_data)) {
        return htmlspecialchars_decode($fetched_data[0]->code);
    }
    return '';
}
function getNotifications($type = 'fetch', $seen = 'both', $limit = 20) {
    global $music, $db;
    if (IS_LOGGED == false) {
        return false;
    }

    $me = $music->user->id;

    $db->where('recipient_id', $me);
    if ($seen === true) {
        $db->where('seen', '0', '>');
    } else if ($seen === false) {
        $db->where('seen', '0');
    }
    if ($type == 'fetch') {
        return $db->orderBy('id', 'DESC')->get(T_NOTIFICATION, $limit);
    } else if ($type == 'count') {
        return $db->getValue(T_NOTIFICATION, 'COUNT(*)');
    }
}
function getActivity($activity_id = 0, $fetch = true) {
    global $music, $db;

    if ($fetch == true) {
        if (empty($activity_id) || !is_numeric($activity_id)) {
            return false;
        }
        $activity_id = secure($activity_id);
        $getActivity = $db->where('id', $activity_id)->getOne(T_ACTIVITIES);
    } else {
        $getActivity = $activity_id;
    }

    $userSong = songData($getActivity->track_id);

    if (empty($userSong)) {
        return false;
    }

    $userSong->activity = $getActivity;
    $userSong->songArray['a_id'] = $getActivity->id;
    $userSong->songArray['a_type'] = $getActivity->type;
    $userSong->songArray['a_owner'] = ( $music->loggedin && $getActivity->user_id === $music->user->id ) ? true : false;
    $userSong->songArray['USER_DATA'] = userData($getActivity->user_id);
    $userSong->songArray['activity_time'] = date('c',$getActivity->time);
    $userSong->songArray['activity_time_formatted'] = time_Elapsed_String($getActivity->time);
    $userSong->songArray['activity_text'] = str_replace('|auser|', '<a href="' . getLink('user/' . $userSong->publisher->username) . '" data-load="user/' . $userSong->publisher->username . '">' . $userSong->publisher->name . '</a>', getActivityText($getActivity->type));

    $userSong->songArray['album_text'] = '';
    if (!empty($userSong->album_id) && $getActivity->type == 'uploaded_track') {
        $getAlbum = $db->where('id', $userSong->album_id)->getOne(T_ALBUMS);
        $userSong->songArray['album_text'] = lang('in') . ' <a href="' . getLink("album/$getAlbum->album_id") . '" data-load="album/' . $getAlbum->album_id . '">' . $getAlbum->title . '</a>';
        $userSong->songArray['album'] = albumData( $getAlbum->id, true, true, false );
        unset($userSong->songArray['album']->songs);
    }else{
        $userSong->songArray['album'] = new stdClass();
    }

    $music->songData = $userSong;
    if (IS_LOGGED == true) {
        $userSong->songArray['isSongOwner'] = ($music->user->id == $getActivity->user_id) ? true : false;
        if (isset($_POST['access_token']) && !empty($_POST['access_token'])) {
            $userSong->songArray['TRACK_DATA'] = songData($getActivity->track_id);
        }
    }

    return $userSong->songArray;

}
function getSpotlight($spotlight = 0, $fetch = true) {
    global $music, $db;
    $userSong = songData($spotlight->id);
    if (empty($userSong)) {
        return false;
    }
    $getActivity = $db->where('track_id', $userSong->id)->where('user_id',$userSong->user_id)->getOne(T_ACTIVITIES);
    if (!empty($getActivity)) {
        $userSong->activity = $getActivity;
        $userSong->songArray['a_id'] = $getActivity->id;
        $userSong->songArray['a_type'] = $getActivity->type;
        $userSong->songArray['a_owner'] = ( $spotlight->user_id === $music->user->id ) ? true : false;
        $userSong->songArray['activity_time'] = date('c',$getActivity->time);
        $userSong->songArray['activity_time_formatted'] = time_Elapsed_String($getActivity->time);
        $userSong->songArray['activity_text'] = str_replace('|auser|', '<a href="' . getLink('user/' . $userSong->publisher->username) . '" data-load="user/' . $userSong->publisher->username . '">' . $userSong->publisher->name . '</a>', getActivityText($getActivity->type));
    }
    // $userSong->activity = $spotlight;
    // $userSong->songArray['a_id'] = $spotlight->id;
    // $userSong->songArray['a_type'] = $spotlight->type;
    // $userSong->songArray['a_owner'] = ( $spotlight->user_id === $music->user->id ) ? true : false;
    $userSong->songArray['USER_DATA'] = userData($spotlight->user_id);
    // $userSong->songArray['activity_time'] = date('c',$spotlight->time);
    // $userSong->songArray['activity_time_formatted'] = time_Elapsed_String($spotlight->time);
    // $userSong->songArray['activity_text'] = str_replace('|auser|', '<a href="' . getLink('user/' . $userSong->publisher->username) . '" data-load="user/' . $userSong->publisher->username . '">' . $userSong->publisher->name . '</a>');
    $userSong->songArray['album_text'] = '';
    if (!empty($userSong->album_id)) {
        $getAlbum = $db->where('id', $userSong->album_id)->getOne(T_ALBUMS);
        $userSong->songArray['album_text'] = lang('in') . ' <a href="' . getLink("album/$getAlbum->album_id") . '" data-load="album/' . $getAlbum->album_id . '">' . $getAlbum->title . '</a>';
        $userSong->songArray['album'] = albumData( $getAlbum->id, true, true, false );
        unset($userSong->songArray['album']->songs);
    }else{
        $userSong->songArray['album'] = new stdClass();
    }
    $music->songData = $userSong;
    if (IS_LOGGED == true) {
        $userSong->songArray['isSongOwner'] = ($music->user->id == $spotlight->user_id) ? true : false;
        if (isset($_POST['access_token']) && !empty($_POST['access_token'])) {
            $userSong->songArray['TRACK_DATA'] = songData($spotlight->id);
        }
    }
    return $userSong->songArray;
}
function getActivityText($type) {
    global $music, $db;
    if (empty($type)) {
        return false;
    }

    $types = [
        'liked_track' => lang("liked |auser| song,"),
        'disliked_track' => lang("disliked |auser| song,"),
        'shared_track' => lang("shared |auser| song,"),
        'commented_track' => lang("commented on |auser| song,"),
        'replay_commented_track' => lang('replayed on |auser| comment,'),
        'uploaded_track' => lang("Uploaded a new song,")
    ];

    if (in_array($type, array_keys($types))) {
       return $types[$type];
    }
    return "";
}
function LikeExists($params){
    global $db;
    if(!isset($params['comment_user_id']) || !isset($params['track_id']) || !isset($params['user_id']) || !isset($params['comment_id'])) return false;
    if(empty($params['comment_user_id']) || empty($params['track_id']) || empty($params['user_id']) || empty($params['comment_id'])) return false;
    $islikeExists = $db->where('track_id',Secure($params['track_id']))->where('user_id',Secure($params['user_id']))->where('comment_id',Secure($params['comment_id']))->getOne(T_LIKES,['count(*) as likes']);
    if($islikeExists->likes > 0){
        return true;
    }else{
        return false;
    }
}
function BlogLikeExists($params){
    global $db;
    if(!isset($params['comment_user_id']) || !isset($params['article_id']) || !isset($params['user_id']) || !isset($params['comment_id'])) return false;
    if(empty($params['comment_user_id']) || empty($params['article_id']) || empty($params['user_id']) || empty($params['comment_id'])) return false;
    $islikeExists = $db->where('article_id',Secure($params['article_id']))->where('user_id',Secure($params['user_id']))->where('comment_id',Secure($params['comment_id']))->getOne(T_BLOG_LIKES,['count(*) as likes']);
    if($islikeExists->likes > 0){
        return true;
    }else{
        return false;
    }
}
function TrackReportExists($params){
    global $db;
    if(!isset($params['track_id']) || !isset($params['user_id'])) return false;
    if(empty($params['track_id']) || empty($params['user_id'])) return false;
    $isReportExists = $db->where('user_id',Secure($params['user_id']))->where('track_id',Secure($params['track_id']))->getOne(T_REPORTS,['count(*) as reports']);
    if($isReportExists->reports > 0){
        return true;
    }else{
        return false;
    }
}
function TrackReviewExists($params){
    global $db;
    if(!isset($params['track_id']) || !isset($params['user_id'])) return false;
    if(empty($params['track_id']) || empty($params['user_id'])) return false;
    $isReviewExists = $db->where('user_id',Secure($params['user_id']))->where('track_id',Secure($params['track_id']))->getOne(T_REVIEWS,['count(*) as reviews']);
    if($isReviewExists->reviews > 0){
        return true;
    }else{
        return false;
    }
}
function getTrackReportButton($params,$template = 'report-track') {
    global $music, $db;
    if (IS_LOGGED == false) {
        return false;
    }

    $user_id = (isset($params['user_id'])) ? Secure($params['user_id']) : null;
    $track_id = (isset($params['track_id'])) ? Secure($params['track_id']) : null;
    if (empty($user_id) || empty($track_id)) {
        return false;
    }

    $music->track_reported = false;

    if (TrackReportExists($params)) {
        $music->track_reported = true;
    }

    $sData = songData($track_id);
    if($sData->IsOwner){
        return false;
    }
    $music->track_reported_params = $params;
    return loadPage("buttons/".$template, ['t_id' => $track_id, 'u_id' => $user_id]);
}
function getTrackReviewButton($params,$template = 'review-track') {
    global $music, $db;
    if (IS_LOGGED == false) {
        return false;
    }

    $user_id = (isset($params['user_id'])) ? Secure($params['user_id']) : null;
    $track_id = (isset($params['track_id'])) ? Secure($params['track_id']) : null;
    if (empty($user_id) || empty($track_id)) {
        return false;
    }

    $music->track_reviewed = false;

    if (TrackReviewExists($params)) {
        $music->track_reviewed = true;
    }

    $sData = songData($track_id);
    if(!isTrackPurchased($sData->id)){
        return false;
    }
    if($sData->IsOwner){
        return false;
    }
    $music->track_reviewed_params = $params;
    return loadPage("buttons/".$template, ['t_id' => $track_id, 'u_id' => $user_id, 'oid' => $sData->audio_id]);
}
function getReviewsCount($audio_id){
    global $db;
    return $db->where('track_id', $audio_id)->getValue(T_REVIEWS, 'count(*)');
}
function CommentReportExists($params){
    global $db;
    if(!isset($params['comment_id']) || !isset($params['user_id'])) return false;
    if(empty($params['comment_id']) || empty($params['user_id'])) return false;
    $isReportExists = $db->where('user_id',Secure($params['user_id']))->where('comment_id',Secure($params['comment_id']))->getOne(T_REPORTS,['count(*) as reports']);
    if($isReportExists->reports > 0){
        return true;
    }else{
        return false;
    }
}
function BlogLikeComment($params){
    global $db;
    if(!isset($params['comment_user_id']) || !isset($params['article_id']) || !isset($params['user_id']) || !isset($params['comment_id'])) return false;
    if(empty($params['comment_user_id']) || empty($params['article_id']) || empty($params['user_id']) || empty($params['comment_id'])) return false;
    if(BlogLikeExists($params) === true) return false;

    $insert = $db->insert(T_BLOG_LIKES,array('article_id'=>Secure($params['article_id']),'user_id'=>Secure($params['user_id']),'comment_id'=>Secure($params['comment_id']),'time'=>time()));
    if ($insert) {
        return true;
    }else{
        return false;
    }
}
function BlogUnLikeComment($params){
    global $db;
    if(!isset($params['comment_user_id']) || !isset($params['article_id']) || !isset($params['user_id']) || !isset($params['comment_id'])) return false;
    if(empty($params['comment_user_id']) || empty($params['article_id']) || empty($params['user_id']) || empty($params['comment_id'])) return false;
    if(BlogLikeExists($params) === false) return false;

    $deleted = $db->where('article_id',Secure($params['article_id']))
                 ->where('user_id',Secure($params['user_id']))
                 ->where('comment_id',Secure($params['comment_id']))
                 ->delete(T_BLOG_LIKES);
    if ($deleted) {
        return true;
    }else{
        return false;
    }
}
function LikeComment($params){
    global $db;
    if(!isset($params['comment_user_id']) || !isset($params['track_id']) || !isset($params['user_id']) || !isset($params['comment_id'])) return false;
    if(empty($params['comment_user_id']) || empty($params['track_id']) || empty($params['user_id']) || empty($params['comment_id'])) return false;
    if(LikeExists($params) === true) return false;

    $insert = $db->insert(T_LIKES,array('track_id'=>Secure($params['track_id']),'user_id'=>Secure($params['user_id']),'comment_id'=>Secure($params['comment_id']),'time'=>time()));
    if ($insert) {
        $song = songData($params['track_id']);
        $create_notification = createNotification([
            'notifier_id' => Secure($params['user_id']),
            'recipient_id' => Secure($params['comment_user_id']),
            'type' => 'liked_comment',
            'track_id' => Secure($params['track_id']),
            'url' => Secure('track/' . $song->audio_id),
            'comment_id' => Secure($params['comment_id'])
        ]);
        return true;
    }else{
        return false;
    }
}
function UnLikeComment($params){
    global $db;
    if(!isset($params['comment_user_id']) || !isset($params['track_id']) || !isset($params['user_id']) || !isset($params['comment_id'])) return false;
    if(empty($params['comment_user_id']) || empty($params['track_id']) || empty($params['user_id']) || empty($params['comment_id'])) return false;
    if(LikeExists($params) === false) return false;

    $deleted = $db->where('track_id',Secure($params['track_id']))
                 ->where('user_id',Secure($params['user_id']))
                 ->where('comment_id',Secure($params['comment_id']))
                 ->delete(T_LIKES);
    if ($deleted) {
        $db->where('notifier_id', Secure($params['user_id']));
        $db->where('recipient_id', Secure($params['comment_user_id']));
        $db->where('comment_id', Secure($params['comment_id']));
        $db->where('type', 'liked_comment');
        $db->delete(T_NOTIFICATION);
        return true;
    }else{
        return false;
    }
}
function completeQuery($table = '', $col = '') {
    return $table . '.' . $col;
}
function getSpotlights($limit = 20, $offset = 0){
    global $music, $db;

    $db->where('availability', '0')
        ->where('spotlight', '1')
        ->where(completeQuery(T_SONGS, 'user_id') . ' IN (SELECT id FROM ' . T_USERS . ' WHERE is_pro = 1)');
    if (IS_LOGGED) {
        $db->where(completeQuery(T_SONGS, 'user_id') . " NOT IN (SELECT user_id FROM blocks WHERE blocked_id = {$music->user->id})");
    }
    if (!empty($offset) && $offset > 0 && is_numeric($offset)) {
        $db->where(completeQuery(T_SONGS, 'id'), $offset, '<');
    }

    return $db->orderBy('time', 'DESC')->groupBy('user_id')->get(T_SONGS, $limit, [completeQuery(T_SONGS, '*')]);

}
function getActivties($limit = 20, $offset = 0, $user_id = 0, $filter_by = []) {
    global $music, $db;

    if (!empty($user_id) && $user_id > 0 && is_numeric($user_id)) {
        $db->where(completeQuery(T_ACTIVITIES, 'user_id'), $user_id);
    } else if (empty($filter_by['spotlight'])) {
        if (IS_LOGGED == false) {
            return false;
        }
        $db->where("(" . completeQuery(T_ACTIVITIES, 'user_id') . " IN (SELECT following_id FROM " . T_FOLLOWERS . " WHERE follower_id = '{$music->user->id}') OR " . completeQuery(T_ACTIVITIES, 'user_id') . " = '{$music->user->id}')");
       
    }
    if (!empty($filter_by['likes'])) {
        $db->where(completeQuery(T_ACTIVITIES, 'type'), 'liked_track');
    }
    if (!empty($filter_by['spotlight'])) {
        $db->where(completeQuery(T_ACTIVITIES, 'type'), 'uploaded_track');
        $db->where(completeQuery(T_ACTIVITIES, 'user_id') . ' IN (SELECT id FROM ' . T_USERS . ' WHERE is_pro = 1)');
    }
    if (IS_LOGGED) {
        $db->join("songs", completeQuery(T_ACTIVITIES, 'track_id') . " = " . completeQuery(T_SONGS, 'id'), "INNER");
        $db->where(completeQuery(T_SONGS, 'user_id') . " NOT IN (SELECT user_id FROM blocks WHERE blocked_id = {$music->user->id})");
        $db->where(completeQuery(T_ACTIVITIES, 'user_id') . " NOT IN (SELECT user_id FROM blocks WHERE blocked_id = {$music->user->id})");
    }

    if (!empty($offset) && $offset > 0 && is_numeric($offset)) {
        $db->where(completeQuery(T_ACTIVITIES, 'id'), $offset, '<');
    }

    $get_public_posts = false;
    if (empty($user_id)) {
        $get_public_posts = true;
    } else {
        if (!IS_LOGGED) {
           $get_public_posts = true;
        } else {
            if ($music->user->id != $user_id) {
                $get_public_posts = true;
            }
        }
    }

    if ($get_public_posts == true) {
        $db->where(completeQuery(T_ACTIVITIES, 'track_id') . " IN (SELECT id FROM " . T_SONGS . " WHERE availability = 0)");
    }
    
    return $db->orderBy(completeQuery(T_ACTIVITIES, 'id'), 'DESC')->get(T_ACTIVITIES, $limit, [completeQuery(T_ACTIVITIES, '*')]);
}
function getPlayList($id = 0, $fetch = true) {
    global $music, $db;
    if ($fetch == true) {
        if (empty($id) || !is_numeric($id)) {
            return false;
        }
        $id = secure($id);
        $getPlayList = $db->where('id', $id)->getOne(T_PLAYLISTS);
    } else {
        $getPlayList = $id;
    }

    if (empty($getPlayList)) {
        return false;
    }

    if (isBlockedFromOneSide($getPlayList->user_id)) {
        return false;
    }
    if (IS_LOGGED == true) {
        $getPlayList->IsOwner = ($music->user->id == $getPlayList->user_id) ? true : false;
    }
    $getPlayList->thumbnail_ready = getMedia($getPlayList->thumbnail);
    $getPlayList->privacy_text = ($getPlayList->privacy == 0) ? lang("Public") : lang("Private");
    $getPlayList->url = getLink("playlist/" . $getPlayList->uid);
    $getPlayList->songs = $db->where('playlist_id', $getPlayList->id)->getValue(T_PLAYLIST_SONGS, 'count(*)');
    $getPlayList->publisher = userData($getPlayList->user_id);
    return $getPlayList;
}
function getPlayListSong($id = 0) {
    global $music, $db;
    if (empty($id) || !is_numeric($id)) {
        return false;
    }
    $id = secure($id);
    $getPlayList = $db->where('id', $id)->getOne(T_PLAYLIST_SONGS);
    if (empty($getPlayList)) {
        return false;
    }
    if (IS_LOGGED == true) {
        $getPlayList->IsOwner = ($music->user->id == $getPlayList->user_id) ? true : false;
    }
    return $getPlayList;
}
function deleteActivity($data = []) {
    global $music, $db;
    if (IS_LOGGED == false) {
        return false;
    }

    if (empty($data['user_id'])) {
        $data['user_id'] = $music->user->id;
    }

    if (empty($data['user_id']) || empty($data['type'])) {
        return false;
    }

    if ((!is_numeric($data['user_id'])) && ($data['user_id'] <= 0)) {
        return false;
    }

    $data['user_id'] = secure($data['user_id']);
    $data['type'] = secure($data['type']);
    $data['time'] = secure(time());

    $delete_same_activity = $db->where('user_id', $data['user_id'])->where('type', $data['type']);
    if (!empty($data['track_id'])) {
        $data['track_id'] = secure($data['track_id']);
        $db->where('track_id', $data['track_id']);
    }
    return $db->delete(T_ACTIVITIES);
}
function notifyUploadTrack($data = array()){
    global $db, $music;
    if (IS_LOGGED == false) {
        return false;
    }
    $getFollowers = $db->where('following_id', $music->user->id)->where("follower_id NOT IN (SELECT blocked_id FROM blocks WHERE user_id = {$music->user->id})")->orderBy('id', 'DESC')->get(T_FOLLOWERS, null, array('*'));
    foreach ($getFollowers as $key => $value){
        $create_notification = createNotification([
            'notifier_id' => $data['user_id'],
            'recipient_id' => $value->follower_id,
            'type' => 'new_track',
            'track_id' => $data['id'],
            'url' => "track/".$data['audio_id']
        ]);
    }
}
function createActivity($data = []) {
    global $music, $db;
    if (IS_LOGGED == false) {
        return false;
    }

    if (empty($data['user_id'])) {
        $data['user_id'] = $music->user->id;
    }

    if (empty($data['user_id']) || empty($data['type'])) {
        return false;
    }

    if ((!is_numeric($data['user_id'])) && ($data['user_id'] <= 0)) {
        return false;
    }

    $data['user_id'] = secure($data['user_id']);
    $data['type'] = secure($data['type']);
    $data['time'] = secure(time());

    $delete_same_activity = $db->where('user_id', $data['user_id'])->where('type', $data['type']);
    if (!empty($data['track_id'])) {
        $data['track_id'] = secure($data['track_id']);
        $db->where('track_id', $data['track_id']);
    }
    $db->delete(T_ACTIVITIES);
    $create_activity = $db->insert(T_ACTIVITIES, $data);
    if ($create_activity) {
        return true;
    }
}
function createNotification($data = []) {
    global $music, $db;
    if (isLogged() == false) {
        return false;
    }

    if (empty($data['notifier_id'])) {
        $data['notifier_id'] = $music->user->id;
    }

    if (empty($data['recipient_id']) || empty($data['type'])) {
        return false;
    }

    if ((!is_numeric($data['notifier_id']) || !is_numeric($data['recipient_id'])) && ($data['notifier_id'] <= 0 || $data['recipient_id'] <= 0)) {
        return false;
    }

    if ($data['recipient_id'] == $data['notifier_id'] ) {
        return false;
    }

    $isBlocked = ($db->where('blocked_id', $music->user->id)->where('user_id', $data['recipient_id'])->getValue(T_BLOCKS, 'count(*)') > 0);

    if ($isBlocked) {
        return false;
    }

    $data['notifier_id'] = secure($data['notifier_id']);
    $data['recipient_id'] = secure($data['recipient_id']);
    $data['type'] = secure($data['type']);
    $data['time'] = secure(time());

    $send_notification = false;
    $senderEmailNotification = $db->arrayBuilder()->where('id', $data['notifier_id'] )->getOne(T_USERS,array('id','username','name','email','avatar','email_on_follow_user','email_on_liked_track','email_on_liked_comment','email_on_artist_status_changed','email_on_receipt_status_changed','email_on_new_track','email_on_reviewed_track','email_on_comment_mention','email_on_comment_replay_mention'));
    $userEmailNotification = $db->arrayBuilder()->where('id', $data['recipient_id'])->getOne(T_USERS,array('id','username','name','email','avatar','email_on_follow_user','email_on_liked_track','email_on_liked_comment','email_on_artist_status_changed','email_on_receipt_status_changed','email_on_new_track','email_on_reviewed_track','email_on_comment_mention','email_on_comment_replay_mention'));
    $u = $music->user;
    if ($data['type'] == 'purchased'){
        RecordUserActivities('purchase_track',array('track_id' => $data['track_id']));
    }
    if($userEmailNotification !== false) {
        if ($data['type'] == 'follow_user' && $userEmailNotification['email_on_follow_user'] == 1) {
            $send_notification = true;
        }
        if (($data['type'] == 'liked_track' || $data['type'] == 'disliked_track') && $userEmailNotification['email_on_liked_track'] == 1) {
            $send_notification = true;
        }
        if ($data['type'] == 'liked_comment' && $userEmailNotification['email_on_liked_comment'] == 1) {
            $send_notification = true;
        }
        if (($data['type'] == 'approved_artist'  || $data['type'] == 'decline_artist') ){ // && $userEmailNotification['email_on_artist_status_changed'] == 1) {
            $send_notification = true;
        }
        if (($data['type'] == 'approve_receipt' || $data['type'] == 'disapprove_receipt') ){// && $userEmailNotification['email_on_receipt_status_changed'] == 1) {
            $send_notification = true;
        }
        if ($data['type'] == 'new_track' && $userEmailNotification['email_on_new_track'] == 1) {
            $send_notification = true;
        }
        if ($data['type'] == 'reviewed_track' && $userEmailNotification['email_on_reviewed_track'] == 1) {
            $send_notification = true;
        }
        if ($data['type'] == 'comment_mention' && $userEmailNotification['email_on_comment_mention'] == 1) {
            $send_notification = true;
        }
        if ($data['type'] == 'comment_replay_mention' && $userEmailNotification['email_on_comment_replay_mention'] == 1){
            $send_notification = true;
        }
    }
    if ($send_notification == true) {

        $delete_same_notification = $db->where('notifier_id', $data['notifier_id'])->where('recipient_id', $data['recipient_id'])->where('type', $data['type']);
        if (!empty($data['track_id'])) {
            $data['track_id'] = secure($data['track_id']);
            $db->where('track_id', $data['track_id']);
        }
        if(isset($data['comment_id'])) {
            if (!empty($data['comment_id'])) {
                $data['comment_id'] = secure($data['comment_id']);
                $db->where('comment_id', $data['comment_id']);
            }
        }
        $db->delete(T_NOTIFICATION);
        $create_notification = $db->insert(T_NOTIFICATION, $data);
        if ($create_notification) {
            if ($music->config->android_push_native == 1 || $music->config->ios_push_native == 1 || $music->config->push == 1) {
                NotificationWebPushNotifier();
            }
            if($music->config->emailNotification == 'on') {
                sendNotificationEmail($data, $userEmailNotification,$senderEmailNotification);
            }
            return true;
        }

    }
}
function sendNotificationEmail($notification,$userEmailNotification,$senderEmailNotification){
    global $db,$music;

    $notification_text = getNotificationTextFromType($notification['type']);
    $c = '';
    if($notification['type'] == 'approved_artist' || $notification['type'] == 'decline_artist' || $notification['type'] == 'approve_receipt' || $notification['type'] == 'disapprove_receipt'){
        $notification['url'] = '';
        $senderEmailNotification['username'] = '';
        $c = $music->config->name;
    }
    $send_email_data = array(
        'from_email' => $music->config->email,
        'from_name' => $music->config->name,
        'to_email' => $userEmailNotification['email'],
        'to_name' => $userEmailNotification['name'],
        'subject' => $music->config->name . ' - ' . lang('New notification'),
        'charSet' => 'UTF-8',
        'message_body' => loadPage('emails/notification-email', array(
            'c'=> $c,
            'uname' => $userEmailNotification['name'],
            'full_name' => $senderEmailNotification['name'],
            'username' => $senderEmailNotification['username'],
            'avater' => $senderEmailNotification['avatar'],
            'contents' => $notification_text,
            'url' => $notification['url']
        )),
        'is_html' => true
    );
    //file_put_contents($notification['type'] . '_' . time() . ".txt", json_encode($send_email_data, JSON_PRETTY_PRINT));
    $send_message = sendMessage($send_email_data);

    return true;
}
function NotificationWebPushNotifier() {
    global $sqlConnect, $music;
    if (IS_LOGGED == false) {
        return false;
    }
    if ($music->config->push == 0) {
        return false;
    }
    if ($music->config->android_push_native == 0 && $music->config->ios_push_native == 0 && $music->config->web_push == 0) {
        return false;
    }
    $user_id   = Secure($music->user->id);
    $to_ids    = array();
    $send      = '';
    $query_get = mysqli_query($sqlConnect, "SELECT * FROM " . T_NOTIFICATION . " WHERE `notifier_id` = '$user_id' AND `seen` = '0' AND `sent_push` = '0' AND `type` <> 'admin_notification' ORDER BY `id` DESC");
    if (mysqli_num_rows($query_get) > 0) {
        while ($sql_get_notification_for_push = mysqli_fetch_assoc($query_get)) {
            $notification_id = $sql_get_notification_for_push['id'];
            $to_id           = $sql_get_notification_for_push['recipient_id'];
            $to_data         = userData($sql_get_notification_for_push['recipient_id']);
            $ids             = array();
            if (!empty($to_data->android_device_id) || !empty($to_data->ios_device_id) || !empty($to_data->web_device_id)) {
                if (!empty($to_data->web_device_id) && empty($to_data->ios_device_id) && empty($to_data->android_device_id)) {
                    if ($music->config->web_push == 0) {
                        return false;
                    }
                }
                $send_array                                                     = array();
                $send_array['notification']['notification_content']             = getNotificationTextFromType($sql_get_notification_for_push['type']);
                $send_array['notification']['notification_data']['url']         = $sql_get_notification_for_push['url'];
                $send_array['notification']['notification_data']['user_data']   = $to_data;
                $send_array['notification']['notification_data']['track']       = '';
                if (!empty($sql_get_notification_for_push['track_id'])) {
                    $send_array['notification']['notification_data']['track']   = $sql_get_notification_for_push['track_id'];

                }
                if ($music->config->android_push_native == 1 && !empty($to_data->android_device_id)) {
                    $send_array['send_to'] = array($to_data->android_device_id);
                    $send_array['notification']['notification_title'] = $music->user->name;
                    $send_array['notification']['notification_image'] = $music->user->avatar;
                    $send_array['notification']['notification_data']['user_id'] = $user_id;
                    $send       = SendPushNotification($send_array, 'android_native');
                }
                if ($music->config->ios_push_native == 1 && !empty($to_data->ios_device_id)) {
                    $send_array['send_to'] = array($to_data->ios_device_id);
                    $send_array['notification']['notification_title'] = $music->user->name;
                    $send_array['notification']['notification_image'] = $music->user->avatar;
                    $send_array['notification']['notification_data']['user_id'] = $user_id;
                    $send       = SendPushNotification($send_array, 'ios_native');
                }
                if ($music->config->web_push == 1 && !empty($to_data->web_device_id)) {
                    $send_array['send_to'] = array($to_data->web_device_id);
                    $send_array['notification']['notification_title'] = $music->user->name;
                    $send_array['notification']['notification_image'] = $music->user->avatar;
                    $send_array['notification']['notification_data']['user_id'] = $user_id;

                    $send       = SendPushNotification($send_array, 'web');
                }
                if(!empty($send)){
                    $query_get_messages_for_push = mysqli_query($sqlConnect, "UPDATE " . T_NOTIFICATION . " SET `sent_push` = '1' WHERE `notifier_id` = '$user_id' AND `sent_push` = '0'");
                }
            }
        }
    }
    return true;
}
function MessagesPushNotifier() {
    global $sqlConnect, $music;
    if (IS_LOGGED == false) {
        return false;
    }
    if ($music->config->push == 0) {
        return false;
    }
    if ($music->config->android_push_messages == 0 && $music->config->ios_push_messages == 0) {
        return false;
    }
    $user_id   = Secure($music->user->id);
    $to_ids    = array();
    $query_get = mysqli_query($sqlConnect, "SELECT * FROM " . T_MESSAGES . " WHERE `from_id` = '$user_id' AND `seen` = '0' AND `sent_push` = '0' ORDER BY `id` DESC");
    if (mysqli_num_rows($query_get) > 0) {
        while ($sql_get_messages_for_push = mysqli_fetch_assoc($query_get)) {
            if (!in_array($sql_get_messages_for_push['to_id'], $to_ids)) {
                $get_session_data = GetSessionDataFromUserID($sql_get_messages_for_push['to_id']);
                if (empty($get_session_data)) {
                    $message_id = $sql_get_messages_for_push['id'];
                    $to_id      = $sql_get_messages_for_push['to_id'];
                    $to_data    = userData($sql_get_messages_for_push['to_id']);
                    if (!empty($to_data['android_device_id']) && $music->config->android_push_messages != 0) {
                        $send_array = array(
                            'send_to' => array(
                                $to_data['android_device_id']
                            ),
                            'notification' => array(
                                'notification_content' => $sql_get_messages_for_push['text'],
                                'notification_title' => $music->user->name,
                                'notification_image' => $music->user->avatar,
                                'notification_data' => array(
                                    'user_id' => $user_id
                                )
                            )
                        );
                        $send       = SendPushNotification($send_array,'android_messenger');
                        if ($send) {
                            $query_get_messages_for_push = mysqli_query($sqlConnect, "UPDATE " . T_MESSAGES . " SET `notification_id` = '$send' WHERE `id` = '$message_id'");
                        }
                    }
                    if (!empty($to_data['ios_device_id']) && $music->config->ios_push_messages != 0) {
                        $send_array = array(
                            'send_to' => array(
                                $to_data['ios_device_id']
                            ),
                            'notification' => array(
                                'notification_content' => $sql_get_messages_for_push['text'],
                                'notification_title' => $music->user->name,
                                'notification_image' => $music->user->avatar,
                                'notification_data' => array(
                                    'user_id' => $user_id
                                )
                            )
                        );
                        $send       = SendPushNotification($send_array,'ios_messenger');
                        if ($send) {
                            $query_get_messages_for_push = mysqli_query($sqlConnect, "UPDATE " . T_MESSAGES . " SET `notification_id` = '$send' WHERE `id` = '$message_id'");
                        }
                    }
                    $query_get_messages_for_push = mysqli_query($sqlConnect, "UPDATE " . T_MESSAGES . " SET `sent_push` = '1' WHERE `from_id` = '$user_id' AND `to_id` = '$to_id' AND `sent_push` = '0'");
                }
            }
            $to_ids[] = $sql_get_messages_for_push['to_id'];
        }
    }
    return true;
}

function GetSessionDataFromUserID($user_id = 0) {
    global $sqlConnect;
    if (empty($user_id)) {
        return false;
    }
    $user_id = Secure($user_id);
    $time    = time() - 30;
    $query   = mysqli_query($sqlConnect, "SELECT * FROM " . T_APP_SESSIONS . " WHERE `user_id` = '{$user_id}' AND `platform` = 'web' AND `time` > $time LIMIT 1");
    return mysqli_fetch_assoc($query);
}

function deleteUser($user_id = 0) {
    global $music, $db;
    if (IS_LOGGED == false) {
        return false;
    }

    if (empty($user_id)) {
        return false;
    }

    if ($music->user->id != $user_id && !isAdmin()) {
        return false;
    }

    $userData = userData($user_id);

    // delete images
    if ($userData->or_avatar != 'upload/photos/d-avatar.jpg') {
        unlink($userData->or_avatar);
        PT_DeleteFromToS3($userData->or_avatar);
    }
    if ($userData->or_cover != 'upload/photos/d-cover.jpg') {
        unlink($userData->or_avatar);
        PT_DeleteFromToS3($userData->or_avatar);
    }

    // delete user data
    $db->where('follower_id', $user_id)->delete(T_FOLLOWERS);
    $db->where('following_id', $user_id)->delete(T_FOLLOWERS);
    $db->where('artist_id', $user_id)->delete(T_FOLLOWERS);

    $db->where('notifier_id', $user_id)->delete(T_NOTIFICATION);
    $db->where('recipient_id', $user_id)->delete(T_NOTIFICATION);

    $db->where('user_id', $user_id)->delete(T_SESSIONS);
    $db->where('user_id', $user_id)->delete(T_COMMENTS);
    $db->where('user_one', $user_id)->delete(T_CHATS);
    $db->where('user_two', $user_id)->delete(T_CHATS);
    $db->where('user_id', $user_id)->delete(T_COPYRIGHTS);

    $db->where('user_id', $user_id)->delete(T_BLOCKS);
    $db->where('blocked_id', $user_id)->delete(T_BLOCKS);

    $db->where('user_id', $user_id)->delete(T_DOWNLOADS);
    $db->where('user_id', $user_id)->delete(T_VIEWS);

    $db->where('user_id', $user_id)->delete(T_FOV);
    $db->where('user_id', $user_id)->delete(T_LIKES);

    $db->where('to_id', $user_id)->delete(T_MESSAGES);
    $db->where('from_id', $user_id)->delete(T_MESSAGES);

    $db->where('user_id', $user_id)->delete(T_REVIEWS);

    $getUserAds = $db->where('user_id', $user_id)->get(T_USR_ADS);
    foreach ($getUserAds as $key => $ad) {
        $db->where('ad_id', $ad->id)->delete(T_ADS_TRANS);
        @unlink($ad->media);
        PT_DeleteFromToS3($ad->media);
    }

    //$db->where('user_id', $user_id)->delete(T_STATIONS);

    $getPlaylists = $db->where('user_id', $user_id)->get(T_PLAYLISTS);
    foreach ($getPlaylists as $key => $playlist) {
       @unlink($playlist->thumbnail);
       PT_DeleteFromToS3($playlist->thumbnail);
    }
    $db->where('user_id', $user_id)->get(T_PLAYLISTS);

    $db->where('user_id', $user_id)->delete(T_PURCHAES);
    $db->where('user_id', $user_id)->delete(T_PAYMENTS);
    $db->where('user_id', $user_id)->delete(T_APP_SESSIONS);
    $db->where('track_owner_id', $user_id)->delete(T_PURCHAES);
    $db->where('user_id', $user_id)->delete(T_REPORTS);
    $db->where('user_id', $user_id)->delete(T_USER_INTEREST);
    $db->where('user_id', $user_id)->delete(T_WITHDRAWAL_REQUESTS);

    $db->where('id', $user_id)->delete(T_USERS);

    $getUserSongs = $db->where('user_id', $user_id)->get(T_SONGS);
    foreach ($getUserSongs as $key => $song) {
        $deleteSong = deleteSong($song->id);
    }

    $getAlbums = $db->where('user_id', $user_id)->get(T_ALBUMS);
    foreach ($getAlbums as $key => $album) {
       @unlink($album->thumbnail);
       PT_DeleteFromToS3($album->thumbnail);
    }

    $db->where('user_id', $user_id)->delete(T_ALBUMS);

    //1- Delete bank_receipts
    $getBank_receipts = $db->where('user_id', $user_id)->get(T_BANK_RECEIPTS);
    foreach ($getBank_receipts as $key => $receipt) {
        @unlink($receipt->receipt_file);
        PT_DeleteFromToS3($receipt->receipt_file);
    }
    $db->where('user_id', $user_id)->delete(T_BANK_RECEIPTS);
    //2- Delete announcement_views
    $db->where('user_id', $user_id)->delete(T_ANNOUNCEMENT_VIEWS);
    //3- Delete artist_requests
    $db->where('user_id', $user_id)->delete(T_ARTIST_R);
    //4- Delete artist_requests
    $db->where('user_id', $user_id)->delete(T_DISLIKES);
    //5- Delete playlist_songs
    $db->where('user_id', $user_id)->delete(T_PLAYLISTS);
    //6- Delete activities
    $db->where('user_id', $user_id)->delete(T_ACTIVITIES);
    //7- Delete T_USERS_FIELDS
    $db->where('user_id', $user_id)->delete(T_USERS_FIELDS);
    //8- Delete T_USR_ADS
    $db->where('user_id', $user_id)->delete(T_USR_ADS);
    //9- Delete playlist_songs
    $db->where('user_id', $user_id)->delete(T_PLAYLIST_SONGS);

    return true;
}
function songData($track_id = 0, $createSession = true, $fetch = true) {
    global $music, $db;
    if (empty($track_id)) {
        return false;
    }
    if ($fetch == true) {
        if (!is_numeric($track_id) || $track_id <= 0) {
            return false;
        }
        $track_id = secure($track_id);
        $getTrack = $db->where('id', $track_id)->getOne(T_SONGS);
    } else {
        $getTrack = $track_id;
    }

    if (empty($getTrack)) {
        return false;
    }

    if (isBlockedFromOneSide($getTrack->user_id)) {
        return false;
    }
    if ($getTrack->availability == 1) {
        if ($music->loggedin) {
            if ($getTrack->user_id != $music->user->id && !isAdmin()) {
                return false;
            }
        }
        else{
            return false;
        }
    }

    $getTrack->thumbnail_original = $getTrack->thumbnail;
    $getTrack->audio_location_original = $getTrack->audio_location;

    $getTrack->thumbnail = getMedia($getTrack->thumbnail);
    if( $getTrack->src === 'radio' ){
        $getTrack->audio_location = $getTrack->audio_location;
    }else{
        $getTrack->audio_location = getMedia($getTrack->audio_location);
    }
    $getTrack->publisher = userData($getTrack->user_id);
    $getTrack->org_description = EditmarkUp($getTrack->description);
    $getTrack->description = markUp($getTrack->description);

    $getTrack->time_formatted = time_Elapsed_String($getTrack->time);
    $getTrack->tags_default = $getTrack->tags;
    $getTrack->tags_array = explode(",",  $getTrack->tags);
    $getTrack->tagsFiltered = [];
    $getTrack->url = getLink("track/$getTrack->audio_id");

    //$category_data = $db->arrayBuilder()->where('id',$getTrack->category_id))->getOne(T_CATEGORIES);
    //$category_data['lang'] = $db->arrayBuilder()->where('lang_key', 'cateogry_' . $getTrack->category_id)->getOne(T_LANGS);
    if( $getTrack->category_id > 0 ){
        if( isset( $music->categories->{$getTrack->category_id} ) ){
            $getTrack->category_name = $music->categories->{$getTrack->category_id};
        }else{
            $getTrack->category_name = lang('Other');
        }
    }else{
        $getTrack->category_name = lang('Other');
    }

    //$getTrack->category_name = (!empty($music->categories->{$getTrack->category_id})) ? $music->categories->{$getTrack->category_id} : lang('Other');
    //$getTrack->category_name = (!empty($music->categories->{$getTrack->category_id})) ? $music->categories->{$getTrack->category_id} : lang('Other');

    // if ($getTrack->availability == 1) {
    //     if (IS_LOGGED) {
    //         if ($getTrack->user_id != $music->user->id) {
    //             return false;
    //         }
    //     } else {
    //         return false;
    //     }
    // }

    if ($createSession == true) {
        $new_hash = $_SESSION['session_hash'] = md5(time());
    } else {
        if (isset($_SESSION['session_hash'])) {
            $new_hash = $_SESSION['session_hash'];
        } else {
            $new_hash = $_SESSION['session_hash'] = md5(time());
        }
    }
    if ($music->config->s3_upload == 'on' || $music->config->ftp_upload == "on") {
        $getTrack->secure_url = $getTrack->audio_location;
        $purchase = false;
        if ($getTrack->price > 0) {
            if (!isTrackPurchased($getTrack->id)) {
                $purchase = true;
                if (IS_LOGGED == true) {
                    if ($music->user->id == $getTrack->user_id) {
                        $purchase = false;
                    }
                }
            }
        }
        if (!empty($getTrack->demo_track) && $purchase == true && $music->config->ffmpeg_system == 'on') {
            $getTrack->secure_url = getMedia($getTrack->demo_track);
        }
    } else {
        $getTrack->secure_url = getLink("get-track.php?id=$getTrack->audio_id&hash=$new_hash");
    }
    if (!empty($getTrack->tags_array)) {
        foreach ($getTrack->tags_array as $key => $tag) {
            $getTrack->tagsFiltered[] = trim($tag);
        }
    }

    $getTrack->songArray = [
        'USER_DATA' => $getTrack->publisher,
        's_time' => $getTrack->time_formatted,
        's_name' => $getTrack->title,
        's_duration' => $getTrack->duration,
        's_thumbnail' => $getTrack->thumbnail,
        's_id' => $getTrack->id,
        's_url' => $getTrack->url,
        's_audio_id' => $getTrack->audio_id,
        's_price' => $getTrack->price,
        's_category' => $getTrack->category_name
    ];

    $getTrack->count_likes = number_format_mm(countLikes($getTrack->id));
    $getTrack->count_dislikes = number_format_mm(countDisLikes($getTrack->id));
    $getTrack->count_views = number_format_mm($db->where('track_id', $getTrack->id)->getValue(T_VIEWS, 'count(*)'));
    $getTrack->count_shares = 0;
    $getTrack->count_comment = number_format_mm($db->where('track_id', $getTrack->id)->getValue(T_COMMENTS, 'count(*)'));
    $getTrack->count_favorite = number_format_mm($db->where('track_id', $getTrack->id)->getValue(T_FOV, 'count(*)'));
    if (!empty($getTrack->price) && !empty($getTrack->demo_track) && !isTrackPurchased($getTrack->id) && $music->config->ffmpeg_system == 'on') {
        $showDemo = true;
        if (IS_LOGGED == true) {
            if ($getTrack->user_id == $music->user->id) {
                $showDemo = false;
            }
        }
        if ($showDemo == true) {
            $wave = $getTrack->dark_wave;
            $getTrack->dark_wave = str_replace('_dark.png','_demo_dark.png', $wave);
            $getTrack->light_wave = str_replace('_dark.png','_demo_light.png', $wave);
            $getTrack->duration = $getTrack->demo_duration;
        }
    }
    $getTrack->isDisLiked = 0;
    if (IS_LOGGED == true) {
        $getTrack->isDisLiked = isDisLiked($getTrack->id);
        $getTrack->IsOwner = ($music->user->id == $getTrack->publisher->id) ? true : false;
        $getTrack->IsLiked = isLiked($getTrack->id, $music->user->id);
        $getTrack->is_favoriated = isFavorated($getTrack->id);

        if($getTrack->price == 0){
            $getTrack->can_listen = true;
        }else{
            $getTrack->can_listen = false;
        }

        if($getTrack->IsOwner || isTrackPurchased($getTrack->id)){
            $getTrack->can_listen = true;
        }

    }
    $album = $db->where('id',$getTrack->album_id)->getOne(T_ALBUMS,'title');
    if($album !== null) {
        $getTrack->album_name = $album->title;
    }else{
        $getTrack->album_name = '';
    }
    $getTrack->itunes_token_url = $getTrack->itunes_affiliate_url;
    if (!empty($getTrack->itunes_token)) {
        $getTrack->itunes_token_url = $getTrack->itunes_token_url.'&at='.$getTrack->itunes_token;
    }
    $getTrack->deezer_url = '';
    if (strpos($getTrack->src, 'EEZER:')) {
        $array = explode(':', $getTrack->src);
        if (!empty($array) && !empty($array[1])) {
            $getTrack->deezer_url = 'https://www.deezer.com/en/track/'.$array[1];
        }
    }
    $getTrack->tagged_artists = array();
    $tagged_artists = $db->where('track_id',$getTrack->id)->where('approved',1)->get(T_ARTISTS_TAGS);
    if (!empty($tagged_artists)) {
        foreach ($tagged_artists as $key => $value) {
            $getTrack->tagged_artists[] = userData($value->artist_id);
        }
    }

    return $getTrack;
}
use Aws\S3\S3Client;
function PT_UploadToS3($filename, $config = array()) {
    global $music;
    if(empty($filename) || is_array($filename) || is_object($filename)){
        return false;
    }
    if ($music->config->s3_upload != 'on' && $music->config->ftp_upload != 'on') {
        return false;
    }
    if ($music->config->ftp_upload == "on" && !empty($music->config->ftp_host) && !empty($music->config->ftp_username)) {
        include_once('assets/import/ftp/vendor/autoload.php');
        $ftp = new \FtpClient\FtpClient();
        $ftp->connect($music->config->ftp_host, false, $music->config->ftp_port);
        $login = $ftp->login($music->config->ftp_username, $music->config->ftp_password);
        if ($login) {
            if (!empty($music->config->ftp_path)) {
                if ($music->config->ftp_path != "./") {
                    $ftp->chdir($music->config->ftp_path);
                }
            }
            $file_path = substr($filename, 0, strrpos( $filename, '/'));
            $file_path_info = explode('/', $file_path);
            $path = '';
            if (!$ftp->isDir($file_path)) {
                foreach ($file_path_info as $key => $value) {
                    if (!empty($path)) {
                        $path .= '/' . $value . '/' ;
                    } else {
                        $path .= $value . '/' ;
                    }
                    if (!$ftp->isDir($path)) {
                        $mkdir = $ftp->mkdir($path);
                    }
                } 
            }
            $ftp->chdir($file_path);
            $ftp->pasv(true);
            if ($ftp->putFromPath($filename)) {
                if (empty($config['delete'])) {
                    if (empty($config['amazon'])) {
                        @unlink($filename);
                    }
                } 
                $ftp->close();
                return true;
            }
            $ftp->close();
        }
    } else {
        include_once('assets/import/s3/vendor/autoload.php');
        $s3Config = (
            empty($music->config->amazone_s3_key) || 
            empty($music->config->amazone_s3_s_key) || 
            empty($music->config->region) || 
            empty($music->config->s3_bucket_name)
        );  
        
        if ($s3Config){
            return false;
        }

        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => $music->config->region,
            'credentials' => [
                'key'    => $music->config->amazone_s3_key,
                'secret' => $music->config->amazone_s3_s_key,
            ]
        ]);

        $uploaded = $s3->putObject([
            'Bucket' => $music->config->s3_bucket_name,
            'Key'    => $filename,
            'Body'   => fopen($filename, 'r+'),
            'ACL'    => 'public-read',
        ]);

        if (empty($config['delete'])) {
            if ($s3->doesObjectExist($music->config->s3_bucket_name, $filename)) {
                if (empty($config['amazon'])) {
                    @unlink($filename);
                }
                return true;
            }
        }

        else {
            return true;
        }
    }
}
function PT_DeleteFromToS3($filename, $config = array()) {
    global $music;

    if ($music->config->s3_upload != 'on' && $music->config->ftp_upload != 'on') {
        return false;
    }

    if (empty($filename)) {
        return false;
    }

    if ($music->config->ftp_upload == "on" && $music->config->ftp_username !== "" && $music->config->ftp_password !== "") {
        include_once('assets/import/ftp/vendor/autoload.php');
        $ftp = new \FtpClient\FtpClient();
        $ftp->connect($music->config->ftp_host, false, $music->config->ftp_port);
        $login = $ftp->login($music->config->ftp_username, $music->config->ftp_password);
        
        if ($login) {
            if (!empty($music->config->ftp_path)) {
                if ($music->config->ftp_path != "./") {
                    $ftp->chdir($music->config->ftp_path);
                }
            }
            $file_path = substr($filename, 0, strrpos( $filename, '/'));
            $file_name = substr($filename, strrpos( $filename, '/') + 1);
            $file_path_info = explode('/', $file_path);
            $path = '';
            if (!$ftp->isDir($file_path)) {
                return false;
            }
            $ftp->chdir($file_path);
            $ftp->pasv(true);
            if ($ftp->remove($file_name)) {
                return true;
            }
        }
    } else {
        include_once('assets/import/s3/vendor/autoload.php');
        $s3Config = (
            empty($music->config->amazone_s3_key) || 
            empty($music->config->amazone_s3_s_key) || 
            empty($music->config->region) || 
            empty($music->config->s3_bucket_name)
        ); 

        if ($s3Config){
            return false;
        }
        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => $music->config->region,
            'credentials' => [
                'key'    => $music->config->amazone_s3_key,
                'secret' => $music->config->amazone_s3_s_key,
            ]
        ]);

        $s3->deleteObject([
            'Bucket' => $music->config->s3_bucket_name,
            'Key'    => $filename,
        ]);

        if (!$s3->doesObjectExist($music->config->s3_bucket_name, $filename)) {
            return true;
        }
    }
}
function albumData($album_id = 0, $createSession = true, $fetch = true, $getAlbumSongs = true) {
    global $music, $db;
    if (empty($album_id)) {
        return false;
    }
    if ($fetch == true) {
        if (!is_numeric($album_id) || $album_id <= 0) {
            return false;
        }
        $track_id = secure($album_id);
        $getAlbum = $db->where('id', $album_id)->getOne(T_ALBUMS);
    } else {
        $getAlbum = $album_id;
    }

    if (empty($getAlbum)) {
        return false;
    }

    if (isBlockedFromOneSide($getAlbum->user_id)) {
        return false;
    }

    $getAlbum->thumbnail_original = $getAlbum->thumbnail;
    $getAlbum->thumbnail = getMedia($getAlbum->thumbnail);
    $getAlbum->publisher = userData($getAlbum->user_id);
    $getAlbum->description = markUp($getAlbum->description);
    $getAlbum->time_formatted = time_Elapsed_String($getAlbum->time);
    $getAlbum->url = getLink("album/$getAlbum->album_id");
    $getAlbum->category_name = (!empty($music->categories->{$getAlbum->category_id})) ? $music->categories->{$getAlbum->category_id} : lang('Other');

    if ($createSession == true) {
        $new_hash = $_SESSION['session_hash'] = md5(time());
    } else {
        if (isset($_SESSION['session_hash'])) {
            $new_hash = $_SESSION['session_hash'];
        } else {
            $new_hash = $_SESSION['session_hash'] = md5(time());
        }
    }
    $getAlbum->is_purchased = 0;
    if (IS_LOGGED == true) {
        $getAlbum->IsOwner = ($music->user->id == $getAlbum->user_id) ? true : false;
        if ($getAlbum->user_id !== $music->user->id && isUserBuyAlbum($getAlbum->id) && $getAlbum->price > 0) {
            $getAlbum->is_purchased = 1;
        }
    }

    $getAlbum->songs = [];
    if ($getAlbumSongs == true) {
        $songs = $db->where('album_id',$album_id)->get(T_SONGS,null,array('id'));
        foreach ($songs as $key => $song){
            $getAlbum->songs[$song->id] = songData($song->id);
        }
    }
    $getAlbum->count_songs = $db->where('album_id',$album_id)->getValue(T_SONGS,'COUNT(*)');
    return $getAlbum;
}
function deleteSong($id) {
    global $music, $db;
    if (IS_LOGGED == false) {
        return false;
    }

    if (empty($id)) {
        return false;
    }

    $id = secure($id);

    $getSong = songData($id);

    $delete_songs           = @unlink($getSong->audio_location_original);
    $delete_thumbnail       = @unlink($getSong->thumbnail_original);
    $delete_dark_wave       = @unlink($getSong->dark_wave);
    $delete_light_wave      = @unlink($getSong->light_wave);

    $delete_thumbnail       = @unlink(str_replace('_dark.png','_demo_day.png', $getSong->dark_wave));
    $delete_dark_wave       = @unlink(str_replace('_dark.png','_demo_light.png', $getSong->dark_wave));
    $delete_light_wave      = @unlink(str_replace('_dark.png','_demo_dark.png', $getSong->dark_wave));
    $delete_day_wave        = @unlink(str_replace('_dark.png','_day.png',$getSong->dark_wave));

    //Delete demo track
    $delete_demo_track      = @unlink($getSong->demo_track);
    PT_DeleteFromToS3($getSong->demo_track);

    PT_DeleteFromToS3($getSong->audio_location_original);
    PT_DeleteFromToS3($getSong->thumbnail_original);
    PT_DeleteFromToS3($getSong->dark_wave);
    PT_DeleteFromToS3($getSong->light_wave);
    PT_DeleteFromToS3(str_replace('_dark.png','_day.png', $getSong->dark_wave));
    PT_DeleteFromToS3(str_replace('_dark.png','_demo_day.png', $getSong->dark_wave));
    PT_DeleteFromToS3(str_replace('_dark.png','_demo_light.png', $getSong->dark_wave));
    PT_DeleteFromToS3(str_replace('_dark.png','_demo_dark.png', $getSong->dark_wave));

    $delete = $db->where('track_id', $id)->delete(T_LIKES);
    $delete = $db->where('track_id', $id)->delete(T_DISLIKES);
    $delete = $db->where('track_id', $id)->delete(T_NOTIFICATION);
    $delete = $db->where('track_id', $id)->delete(T_VIEWS);
    $delete = $db->where('track_id', $id)->delete(T_ACTIVITIES);
    $delete = $db->where('track_id', $id)->delete(T_COMMENTS);
    $delete = $db->where('track_id', $id)->delete(T_DOWNLOADS);
    $delete = $db->where('track_id', $id)->delete(T_FOV);
    $delete = $db->where('track_id', $id)->delete(T_PLAYLIST_SONGS);
    $delete = $db->where('track_id', $id)->delete(T_PURCHAES);
    $delete = $db->where('track_id', $id)->delete(T_REPORTS);
    $delete = $db->where('track_id', $id)->delete(T_COPYRIGHTS);
    $delete = $db->where('id', $id)->delete(T_SONGS);
    $delete = $db->where('track_id', $id)->delete(T_REVIEWS);
    
    if (!empty($getSong->album_id)) {
        $getAlbumPrice = $db->where('id', $getSong->album_id)->getValue(T_ALBUMS, 'price');
        $countSongs = $db->where('album_id', $getSong->album_id)->getValue(T_SONGS, 'count(*)');
        if ($getAlbumPrice > 0) {
            $getAlbumPrice = number_format($getAlbumPrice / $countSongs, 2);
        }
        $db->where('album_id', $getSong->album_id)->update(T_SONGS, ['price' => $getAlbumPrice]);
    }
    return true;
}
function number_format_mm($number = 0) {
    global $music, $db;

    if ($music->language == 'english') {
        $number = thousandsCurrencyFormat($number);
    } else {
        $number = number_format($number);
    }
    return $number;
}
function getBlogComment($id = 0, $fetch = true) {
    global $music, $db;
    if (empty($id)) {
        return false;
    }
    
    if ($fetch == true) {
        $id = secure($id);
        if (!is_numeric($id) || $id < 0) {
            return false;
        }
        $getComment = $db->where('id', $id)->getOne(T_BLOG_COMMENTS);
    } else {
        $getComment = $id;
    }
    
    if (empty($getComment)) {
        return false;
    } 
    $getComment->posted = time_Elapsed_String($getComment->time);
    $getComment->value = markUp($getComment->value);
    $getComment->owner = false;
    if (IS_LOGGED) {
        if (isAdmin() || $getComment->user_id == $music->user->id) {
            $getComment->owner = true;
        }
        $comment = [
            'comment_user_id' => $getComment->user_id,
            'article_id' => $getComment->article_id,
            'user_id' => $music->user->id,
            'comment_id' => $getComment->id
        ];
        if(BlogLikeExists($comment) === true ) {
            $getComment->IsLikedComment = true;
        }else{
            $getComment->IsLikedComment = false;
        }
        $getComment->countLiked = BlogcountCommentLikes($getComment->id);
    }
    return $getComment;
}
function CheckStreamUrl($streamUrl){
    $streamExist = [];
    $file_headers_1 = @get_headers($streamUrl);
    foreach ($file_headers_1 as $key => $value) {
        if (strpos($value, '200 OK') === false) {

        } else {
            $streamExist[] = 1;
        }
    }
    if(count($streamExist) > 0){
        return true;
    }else{
        return false;
    }
}
function getCommentReplay($id = 0, $fetch = true) {
    global $music, $db;
    if (empty($id)) {
        return false;
    }

    if ($fetch == true) {
        $id = secure($id);
        if (!is_numeric($id) || $id < 0) {
            return false;
        }
        $getComment = $db->where('id', $id)->getOne(T_COMMENT_REPLIES);
    } else {
        $getComment = $id;
    }

    if (empty($getComment)) {
        return false;
    }
    $getComment->org_posted = $getComment->time;
    $getComment->posted = time_Elapsed_String($getComment->time);
    $getComment->value = markUp($getComment->value);
    $getComment->owner = false;
    $comment  = $db->where('id',$getComment->comment_id)->getOne(T_COMMENTS,array('track_id'));
    $getComment->audio_id = 0;
    if (!empty($comment->track_id)) {
        $songID = songData($comment->track_id);
        $getComment->audio_id = $songID->audio_id;
    }
    
    $comment_text = $getComment->value;
    $mention_regex = '/@\[([0-9]+)\]/i';
    if (preg_match_all($mention_regex, $comment_text, $matches)) {
        foreach ($matches[1] as $match) {
            $match = secure($match);
            $match_user = userData($match);
            $match_search = '@[' . $match . ']';
            if (isset($match_user->id)) {
                $match_replace = '<a href="' . $music->config->site_url . '/' . $match_user->username . '" data-load="'.$match_user->username.'">@' . $match_user->username . '</a>';
                $comment_text = str_replace($match_search, $match_replace, $comment_text);
            }
        }
    }
    $getComment->value =  $comment_text;

    $getComment->commentUser = userData($getComment->user_id);

    if (IS_LOGGED) {
        if (isAdmin() || $getComment->user_id == $music->user->id) {
            $getComment->owner = true;
        }
        $comment = [
            'comment_user_id' => $getComment->user_id,
            'track_id' => (!empty($comment->track_id) ? $comment->track_id : 0) ,
            'user_id' => $music->user->id,
            'comment_id' => $getComment->id
        ];
//        if(LikeExists($comment) === true ) {
//            $getComment->IsLikedComment = true;
//        }else{
//            $getComment->IsLikedComment = false;
//        }
//        $getComment->countLiked = countCommentLikes($getComment->id);
    }
    return $getComment;
}
function getComment($id = 0, $fetch = true) {
    global $music, $db;
    if (empty($id)) {
        return false;
    }
    
    if ($fetch == true) {
        $id = secure($id);
        if (!is_numeric($id) || $id < 0) {
            return false;
        }
        $getComment = $db->where('id', $id)->getOne(T_COMMENTS);
    } else {
        $getComment = $id;
    }
    
    if (empty($getComment)) {
        return false;
    } 
    $getComment->org_posted = $getComment->time;
    $getComment->posted = time_Elapsed_String($getComment->time);
    $getComment->secondsFormated = '';
    if (!empty($getComment->songseconds)) {
        $getComment->secondsFormated = gmdate("i:s", $getComment->songseconds);
    }
    
    $getComment->value = markUp($getComment->value);
    $getComment->owner = false;

    $comment_text = $getComment->value;
    $mention_regex = '/@\[([0-9]+)\]/i';
    if (preg_match_all($mention_regex, $comment_text, $matches)) {
        foreach ($matches[1] as $match) {
            $match = secure($match);
            $match_user = userData($match);
            $match_search = '@[' . $match . ']';
            if (isset($match_user->id)) {
                $match_replace = '<a href="' . $music->config->site_url . '/' . $match_user->username . '" data-load="'.$match_user->username.'">@' . $match_user->username . '</a>';
                $comment_text = str_replace($match_search, $match_replace, $comment_text);
            }
        }
    }
    $getComment->value = $comment_text;

    $getComment->replies = array();
    $replies = $db->where('comment_id', $getComment->id)->get(T_COMMENT_REPLIES, null, array('*'));
    foreach($replies as $key => $replay) {
        $getComment->replies[] = getCommentReplay($replay->id);
    }

    if (IS_LOGGED) {
        if (empty($getComment->track_id)) {
            $getComment->track_id = 0;
        }
        if (isAdmin() || $getComment->user_id == $music->user->id) {
            $getComment->owner = true;
        }
        $comment = [
            'comment_user_id' => $getComment->user_id,
            'track_id' => $getComment->track_id,
            'user_id' => $music->user->id,
            'comment_id' => $getComment->id
        ];
        if(LikeExists($comment) === true ) {
            $getComment->IsLikedComment = true;
        }else{
            $getComment->IsLikedComment = false;
        }
        $getComment->countLiked = countCommentLikes($getComment->id);
    }
    return $getComment;
}


//function _getCategories() {
//    global $music, $db, $lang_array;
//
//    $getCategories = $db->get(T_CATEGORIES);
//    $cateogryArray = [];
//    foreach ($getCategories as $key => $value) {
//        $cateogryArray[$value->id] = $lang_array["cateogry_$value->id"];
//    }
//    return $cateogryArray;
//}
function getCategories($justname = true) {
    global $music, $db, $lang_array;
    $getCategories = $db->orderBy('id','DESC')->get(T_CATEGORIES);//$db->where('type','category')->get(T_LANGS,null,array('*'));
    $cateogryArray = [];
    foreach ($getCategories as $key => $value) {
        if($justname === false) {
            $cateogryArray[$value->id] = $value;
            $cateogryArray[$value->id]->cateogry_name = $db->arrayBuilder()->where('lang_key', 'cateogry_' . $value->id)->getOne(T_LANGS, $music->language)[$music->language];
        }else{
            $cateogryArray[$value->id] = $db->arrayBuilder()->where('lang_key', 'cateogry_' . $value->id)->getOne(T_LANGS, $music->language)[$music->language];
        }
    }
    return $cateogryArray;
}
function getCategoryInfo($id = 0) {
    global $music, $db;

    if (empty($id)) {
        return false;
    }
    $id = secure($id);
    $category = $db->where('id', $id)->getOne(T_CATEGORIES);
    if (empty($category)) {
        return false;
    }
    $category->background_thumb = (empty($category->background_thumb)) ? $music->config->theme_url . '/img/crowd.jpg' : $category->background_thumb;
    return $category;
}
// Paypal methods
use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Details;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
function createProPayPalLink() {
    global $music, $db;
    if ($music->config->go_pro != 'on') {
        return false;
    }
    include_once('assets/includes/paypal.php');
    $price = $music->config->pro_price;
    $total = $price;
    $product = "Pro Plan";
    $payer = new Payer();
    $payer->setPaymentMethod('paypal');
    $item = new Item();
    $item->setName($product)->setQuantity(1)->setPrice($price)->setCurrency($music->config->paypal_currency);
    $itemList = new ItemList();
    $itemList->setItems(array(
        $item
    ));
    $details = new Details();
    $details->setSubtotal($price);
    $amount = new Amount();
    $amount->setCurrency($music->config->paypal_currency)->setTotal($total)->setDetails($details);
    $transaction = new Transaction();
    $transaction->setAmount($amount)->setItemList($itemList)->setDescription('Pay For ' . $music->config->name)->setInvoiceNumber(uniqid());
    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl($music->config->site_url . "/pro-purchase/true")->setCancelUrl($music->config->site_url . "/pro-purchase/false");
    $payment = new Payment();
    $payment->setIntent('sale')->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions(array(
        $transaction
    ));
    try {
        $payment->create($paypal);
    }
    catch (Exception $e) {
        $data = array(
            'type' => 'ERROR',
            'details' => json_decode($e->getData())
        );
        if (empty($data['details'])) {
            $data['details'] = json_decode($e->getCode());
        }
        return $data;
    }
    $data = array(
        'type' => 'SUCCESS',
        'url' => $payment->getApprovalLink()
    );
    return $data;
}
function createWalletPalLink() {
    global $music, $db;
    if (!isset($_GET['price'])) {
        return false;
    }
    include_once('assets/includes/paypal.php');
    $price = (int)secure($_GET['price']);
    $total = $price;
    $product = "Wallet Replenishment";
    $payer = new Payer();
    $payer->setPaymentMethod('paypal');
    $item = new Item();
    $item->setName($product)->setQuantity(1)->setPrice($price)->setCurrency($music->config->paypal_currency);
    $itemList = new ItemList();
    $itemList->setItems(array(
        $item
    ));
    $details = new Details();
    $details->setSubtotal($price);
    $amount = new Amount();
    $amount->setCurrency($music->config->paypal_currency)->setTotal($total)->setDetails($details);
    $transaction = new Transaction();
    $transaction->setAmount($amount)->setItemList($itemList)->setDescription('Pay For ' . $music->config->name)->setInvoiceNumber(uniqid());
    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl($music->config->site_url . "/wallet-purchase/true?price=" . $price)->setCancelUrl($music->config->site_url . "/wallet-purchase/false");
    $payment = new Payment();
    $payment->setIntent('sale')->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions(array(
        $transaction
    ));
    try {
        $payment->create($paypal);
    }
    catch (Exception $e) {
        $data = array(
            'type' => 'ERROR',
            'details' => json_decode($e->getData())
        );
        if (empty($data['details'])) {
            $data['details'] = json_decode($e->getCode());
        }
        return $data;
    }
    $data = array(
        'type' => 'SUCCESS',
        'url' => $payment->getApprovalLink()
    );
    return $data;
}
function createPurchasePayPalLink($songData = []) {
    global $music, $db;
    if (empty($songData->price) || empty($songData)) {
        return false;
    }
    include_once('assets/includes/paypal.php');
    
    $product  = $songData->title;
    $price    = $songData->price;
    $total = $price;
    $payer = new Payer();
    $payer->setPaymentMethod('paypal');
    $item = new Item();
    $item->setName($product)->setQuantity(1)->setPrice($price)->setCurrency($music->config->paypal_currency);
    $itemList = new ItemList();
    $itemList->setItems(array(
        $item
    ));
    $details = new Details();
    $details->setSubtotal($price);
    $amount = new Amount();
    $amount->setCurrency($music->config->paypal_currency)->setTotal($total)->setDetails($details);
    $transaction = new Transaction();
    $transaction->setAmount($amount)->setItemList($itemList)->setDescription('Pay For ' . $music->config->name)->setInvoiceNumber(uniqid());
    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl($music->config->site_url . "/purchase/true/$songData->audio_id")->setCancelUrl($music->config->site_url . "/purchase/false/$songData->audio_id");
    $payment = new Payment();
    $payment->setIntent('sale')->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions(array(
        $transaction
    ));
    try {
        $payment->create($paypal);
    }
    catch (Exception $e) {
        $data = array(
            'type' => 'ERROR',
            'details' => json_decode($e->getData())
        );
        if (empty($data['details'])) {
            $data['details'] = json_decode($e->getCode());
        }
        return $data;
    }
    $data = array(
        'type' => 'SUCCESS',
        'url' => $payment->getApprovalLink()
    );
    return $data;
}
function createPurchasePayPalAlbumLink($albumData = []) {
    global $music, $db;
    if (empty($albumData->price) || empty($albumData)) {
        return false;
    }
    include_once('assets/includes/paypal.php');

    $product  = $albumData->title;
    $price    = $albumData->price;
    $total = $price;
    $payer = new Payer();
    $payer->setPaymentMethod('paypal');
    $item = new Item();
    $item->setName($product)->setQuantity(1)->setPrice($price)->setCurrency($music->config->paypal_currency);
    $itemList = new ItemList();
    $itemList->setItems(array(
        $item
    ));
    $details = new Details();
    $details->setSubtotal($price);
    $amount = new Amount();
    $amount->setCurrency($music->config->paypal_currency)->setTotal($total)->setDetails($details);
    $transaction = new Transaction();
    $transaction->setAmount($amount)->setItemList($itemList)->setDescription('Pay For ' . $music->config->name)->setInvoiceNumber(uniqid());
    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl($music->config->site_url . "/purchase-album/true/$albumData->album_id")->setCancelUrl($music->config->site_url . "/purchase-album/false/$albumData->album_id");
    $payment = new Payment();
    $payment->setIntent('sale')->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions(array(
        $transaction
    ));
    try {
        $payment->create($paypal);
    }
    catch (Exception $e) {
        $data = array(
            'type' => 'ERROR',
            'details' => json_decode($e->getData())
        );
        if (empty($data['details'])) {
            $data['details'] = json_decode($e->getCode());
        }
        return $data;
    }
    $data = array(
        'type' => 'SUCCESS',
        'url' => $payment->getApprovalLink()
    );
    return $data;
}
function isTrackPurchased($track_id = 0, $user_id = 0) {
    global $db, $music;
    if (IS_LOGGED == false) {
        return false;
    }

    if (empty($track_id)) {
        return false;
    }

    if (empty($user_id)) {
        $user_id = $music->user->id;
    }

    $user_id = secure($user_id);
    $track_id = secure($track_id);

    return ($db->where('track_id', $track_id)->where('user_id', $user_id)->getValue(T_PURCHAES, 'count(*)') > 0) ? true : false;
}
function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}
function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}
function isBlockedFromOneSide($user_id = 0) {
    global $db, $music;
    if (IS_LOGGED == false) {
        return false;
    }

    if (empty($user_id)) {
        return false;
    }

    $user_id = secure($user_id);

    if ($user_id == $music->user->id) {
        return false;
    }

    return ($db->where('blocked_id', $music->user->id)->where('user_id', $user_id)->getValue(T_BLOCKS, 'count(*)') > 0) ? true : false;
}
function isBlocked($user_id = 0) {
     global $db, $music;
    if (isLogged() == false) {
        return false;
    }

    if (empty($user_id)) {
        return false;
    }

    $user_id = secure($user_id);

    if ($user_id == $music->user->id) {
        return false;
    }

    return ($db->where('user_id', $music->user->id)->where('blocked_id', $user_id)->getValue(T_BLOCKS, 'count(*)') > 0 || $db->where('blocked_id', $music->user->id)->where('user_id', $user_id)->getValue(T_BLOCKS, 'count(*)') > 0) ? true : false;
}
function isUserBuyAlbum($albumId){
    global $music,$db;
    if(empty($albumId) || !isset($music->user->id)) return false;

    $songs = $db->where('album_id',$albumId)->get(T_SONGS,null,array('id'));
    $purchase = false;
    foreach ($songs as $key => $song){
        $purchase = isTrackPurchased($song->id, $music->user->id);
    }
    return $purchase;
}
//chat
function GetMessagesUserList($data = array(),$limit = 20,$offset=0) {
    global $music, $db;
    if (IS_LOGGED == false) {
        return false;
    }

    $db->where("user_one = {$music->user->id}");


    if (isset($data['keyword'])) {
        $keyword = Secure($data['keyword']);
        $db->where("user_two IN (SELECT id FROM users WHERE username LIKE '%$keyword%' OR `name` LIKE '%$keyword%')");
    }
    if (!empty($offset)) {
        $db->where('time',secure($offset),'<');
    }

    $users = $db->orderBy('time', 'DESC')->get(T_CHATS, $limit);

    $return_methods = array('obj', 'html');

    $return_method = 'obj';
    if (!empty($data['return_method'])) {
        if (in_array($data['return_method'], $return_methods)) {
            $return_method = $data['return_method'];
        }
    }

    $users_html = '';
    $data_array = array();
    foreach ($users as $key => $user) {
        $chat_time = $user->time;
        $user = UserData($user->user_two);
        if (!empty($user)) {
            $get_last_message = $db->where("((from_id = {$music->user->id} AND to_id = $user->id AND `from_deleted` = '0') OR (from_id = $user->id AND to_id = {$music->user->id} AND `to_deleted` = '0'))")->orderBy('id', 'DESC')->getOne(T_MESSAGES);
            $get_count_seen = $db->where("to_id = {$music->user->id} AND from_id = $user->id AND `from_deleted` = '0' AND seen = 0")->orderBy('id', 'DESC')->getValue(T_MESSAGES, 'COUNT(*)');
            if ($return_method == 'html') {
                $users_html .= LoadPage("messages/ajax/user-list", array(
                    'ID' => $user->id,
                    'AVATAR' => $user->avatar,
                    'NAME' => $user->name,
                    'LAST_MESSAGE' => (!empty($get_last_message->text)) ? markUp( strip_tags($get_last_message->text) ) : '',
                    'COUNT' => (!empty($get_count_seen)) ? $get_count_seen : '',
                    'USERNAME' => $user->username
                ));
            } else {
                $data_array[$key]['user'] = $user;
                $data_array[$key]['get_count_seen'] = $get_count_seen;
                $data_array[$key]['get_last_message'] = $get_last_message;
                $data_array[$key]['chat_time'] = $chat_time;
            }
        }
    }
    $users_obj = (!empty($data_array)) ? ToObject($data_array) : array();
    return (!empty($users_html)) ? $users_html : $users_obj;
}
function EditMarkup($text, $link = true) {
    if ($link == true) {
        $link_search = '/\[a\](.*?)\[\/a\]/i';
        if (preg_match_all($link_search, $text, $matches)) {
            foreach ($matches[1] as $match) {
                $match_decode     = urldecode($match);
                $match_decode_url = $match_decode;
                $count_url        = mb_strlen($match_decode);
                $match_url        = $match_decode;
                if (!preg_match("/http(|s)\:\/\//", $match_decode)) {
                    $match_url = 'http://' . $match_url;
                }
                $text = str_replace('[a]' . $match . '[/a]', $match_decode_url, $text);
            }
        }
    }
    return $text;
}
//function Markup($text, $link = true) {
//    if ($link == true) {
//        $link_search = '/\[a\](.*?)\[\/a\]/i';
//        if (preg_match_all($link_search, $text, $matches)) {
//            foreach ($matches[1] as $match) {
//                $match_decode     = urldecode($match);
//                $match_decode_url = $match_decode;
//                $count_url        = mb_strlen($match_decode);
//                if ($count_url > 50) {
//                    $match_decode_url = mb_substr($match_decode_url, 0, 30) . '....' . mb_substr($match_decode_url, 30, 20);
//                }
//                $match_url = $match_decode;
//                if (!preg_match("/http(|s)\:\/\//", $match_decode)) {
//                    $match_url = 'http://' . $match_url;
//                }
//                $text = str_replace('[a]' . $match . '[/a]', '<a href="' . strip_tags($match_url) . '" target="_blank" class="hash" rel="nofollow">' . $match_decode_url . '</a>', $text);
//            }
//        }
//    }
//    return $text;
//}
function GetMessageData($id = 0) {
    global $music, $db;
    if (empty($id) || !IS_LOGGED) {
        return false;
    }
    $fetched_data = $db->where('id', Secure($id))->getOne(T_MESSAGES);
    if (!empty($fetched_data)) {
        $fetched_data->image = '';
        $fetched_data->full_image = '';
        $link_search = '/\[img\](.*?)\[\/img\]/i';
        if (preg_match_all($link_search, $fetched_data->text, $matches)) {
            foreach ($matches[1] as $match) {
                $match_decode     = urldecode($match);
                $fetched_data->image = str_replace('[img]' . $match . '[/img]', strip_tags($match_decode), $fetched_data->text);
                $fetched_data->full_image = getMedia(strip_tags($fetched_data->image));
            }
        }
        $fetched_data->API_position  = 'left';
        if ($fetched_data->from_id == $music->user->id) {
            $fetched_data->API_position  = 'right';
        }
        $fetched_data->API_type = 'text';
        if (!empty($fetched_data->full_image)) {
            $fetched_data->API_type = 'image';
        }

        $fetched_data->text = Markup($fetched_data->text);
        return $fetched_data;
    }
    return false;
}
function GetMessages($id, $data = array(),$limit = 50) {
    global $music, $db;
    if (IS_LOGGED == false) {
        return false;
    }

    $chat_id = Secure($id);

    if (!empty($data['chat_user'])) {
        $chat_user = $data['chat_user'];
    } else {
        $chat_user = UserData($chat_id);
    }


    $where = "((`from_id` = {$chat_id} AND `to_id` = {$music->user->id} AND `to_deleted` = '0') OR (`from_id` = {$music->user->id} AND `to_id` = {$chat_id} AND `from_deleted` = '0'))";

    // count messages
    $db->where($where);
    if (!empty($data['last_id'])) {
        $data['last_id'] = Secure($data['last_id']);
        $db->where('id', $data['last_id'], '>');
    }

    if (!empty($data['first_id'])) {
        $data['first_id'] = Secure($data['first_id']);
        $db->where('id', $data['first_id'], '<');
    }

    $count_user_messages = $db->getValue(T_MESSAGES, "count(*)");
    $count_user_messages = $count_user_messages - $limit;
    if ($count_user_messages < 1) {
        $count_user_messages = 0;
    }

    // get messages
    $db->where($where);
    if (!empty($data['last_id'])) {
        $db->where('id', $data['last_id'], '>');
    }

    if (!empty($data['first_id'])) {
        $db->where('id', $data['first_id'], '<');
    }

    $get_user_messages = $db->orderBy('id', 'ASC')->get(T_MESSAGES, array($count_user_messages, $limit));

    $messages_html = '';

    $return_methods = array('obj', 'html');

    $return_method = 'obj';
    if (!empty($data['return_method'])) {
        if (in_array($data['return_method'], $return_methods)) {
            $return_method = $data['return_method'];
        }
    }

    $update_seen = array();

    foreach ($get_user_messages as $key => $message) {
        if ($return_method == 'html') {
            $message_type = 'incoming';
            if ($message->from_id == $music->user->id) {
                $message_type = 'outgoing';
            }
            $messages_html .= LoadPage("messages/ajax/$message_type", array(
                'ID' => $message->id,
                'AVATAR' => $chat_user->avatar,
                'NAME' => $chat_user->name,
                'TEXT' => MarkUp($message->text)
            ));
        }
        if ($message->seen == 0 && $message->to_id == $music->user->id) {
            $update_seen[] = $message->id;
        }
    }

    if (!empty($update_seen)) {
        $update_seen = implode(',', $update_seen);
        $update_seen = $db->where("id IN ($update_seen)")->update(T_MESSAGES, array('seen' => time()));
    }

    return (!empty($messages_html)) ? $messages_html : $get_user_messages;
}
function GetMessageButton($username = '') {
    global $music, $db, $lang;
    if (empty($username)) {
        return false;
    }
    if (IS_LOGGED == false) {
        return false;
    }
//    if ($username == $music->user->username) {
//        return false;
//    }
    $button_text  = $lang->message;
    $button_icon  = 'plus-square';
    $button_class = 'subscribe';
    return LoadPage('buttons/message', array(
        'BUTTON' => $button_class,
        'ICON' => $button_icon,
        'TEXT' => $button_text,
        'USERNAME' => $username,
    ));
}
function GetJsonFriends(){
    global $music,$db;
    $users = $db->rawQuery('SELECT 
                              followers.following_id,
                              users.username,
                              users.name
                            FROM
                              users
                              INNER JOIN followers ON (users.id = followers.following_id)
                            WHERE
                              following_id NOT IN (SELECT blocked_id FROM blocks WHERE user_id =  ' . $music->user->id .') AND 
                              followers.follower_id = ' . $music->user->id);
    $user_data = [];
    foreach ($users as $key => $value) {
        $user_data[$key]['id'] = $value->following_id;
        $user_data[$key]['text'] = ( !empty($value->name) ) ? $value->name : $value->username;
    }

    echo json_encode($user_data);
}
function echoOGTrackTags(){
    global $music;
    echo '<ink rel="canonical" href="'.$music->config->site_url.'" />';
    echo '<ink rel="home" href="'.$music->config->site_url.'" />';
    if($music->site_pagename == 'track') {
        echo '<meta property="og:title" content="' . $music->songData->title . '">';
        echo '<meta property="og:image" content="' . $music->songData->thumbnail . '">';
        echo '<meta property="og:image:width" content="500">';
        echo '<meta property="og:image:height" content="500">';
        echo '<meta property="og:description" content="' . $music->songData->org_description . '">';
        echo '<meta property="og:url" content="'.$music->songData->url.'" />';
        echo '<script type="application/ld+json">{"@context": "https://schema.org","@type": "AudioObject","contentUrl": "'.$music->songData->audio_location.'", "description": "'.$music->songData->org_description.'","duration": "'.$music->songData->duration.'","encodingFormat": "audio/mpeg","name": "'.$music->songData->title.'"}</script>';
    }else if($music->site_pagename == 'playlist') {
        echo '<meta property="og:title" content="' . $music->playlist->name . '">';
        echo '<meta property="og:image" content="' . getMedia( $music->playlist->thumbnail ) .'">';
        echo '<meta property="og:image:width" content="500">';
        echo '<meta property="og:image:height" content="500">';
        echo '<meta property="og:description" content="' . $music->playlist->name . '">';
        echo '<meta property="og:url" content="'.$music->playlist->url.'" />';
    }else if($music->site_pagename == 'album') {
        echo '<meta property="og:title" content="' . $music->albumData->title . '">';
        echo '<meta property="og:image" content="' . getMedia( $music->albumData->thumbnail ) .'">';
        echo '<meta property="og:image:width" content="500">';
        echo '<meta property="og:image:height" content="500">';
        echo '<meta property="og:description" content="' . $music->albumData->description . '">';
        echo '<meta property="og:url" content="'.$music->config->site_url.'/album/'.$music->albumData->album_id.'" />';
    }else if($music->site_pagename == 'blog_article') {
        echo '<meta property="og:title" content="' . $music->articleData['title']. '">';
        echo '<meta property="og:image" content="' . getMedia( $music->articleData['thumbnail'] ) .'">';
        echo '<meta property="og:image:width" content="500">';
        echo '<meta property="og:image:height" content="500">';
        echo '<meta property="og:description" content="' . $music->articleData['description'] . '">';
        echo '<meta property="og:url" content="'.$music->articleData['url'].'" />';
    }else{
        echo '<meta property="og:title" content="' . $music->config->title . '">';
        echo '<meta property="og:image" content="' . $music->config->theme_url .'/img/logo.png">';
        echo '<meta property="og:image:width" content="500">';
        echo '<meta property="og:image:height" content="500">';
        echo '<meta property="og:description" content="' . $music->config->description . '">';
        echo '<meta property="og:url" content="'.$music->config->site_url.'" />';
    }
}
function Sql_Result($res, $row = 0, $col = 0) {
    $numrows = mysqli_num_rows($res);
    if ($numrows && $row <= ($numrows - 1) && $row >= 0) {
        mysqli_data_seek($res, $row);
        $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
        if (isset($resrow[$col])) {
            return $resrow[$col];
        }
    }
    return false;
}
function UserIdFromUsername($username) {
    global $sqlConnect;
    if (empty($username)) {
        return false;
    }
    $username = Secure($username);
    $query    = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_USERS . " WHERE `username` = '{$username}'");
    return Sql_Result($query, 0, 'id');
}
function GetUsersByName($name = '', $friends = false, $limit = 25) {
    global $sqlConnect, $music;
    if (isLogged() == false || !$name) {
        return false;
    }
    $user        = $music->user->id;
    $name        = Secure($name);
    $data        = array();
    $sub_sql     = "";
    $t_users     = T_USERS;
    $t_followers = T_FOLLOWERS;
    if ($friends == true) {
        $sub_sql = "
        AND ( `id` IN (SELECT `follower_id` FROM $t_followers WHERE `follower_id` <> {$user})  OR
        `id` IN (SELECT `following_id` FROM $t_followers WHERE  `following_id` <> {$user}))";
    }
    $limit_text = '';
    if (!empty($limit) && is_numeric($limit)) {
        $limit      = Secure($limit);
        $limit_text = 'LIMIT ' . $limit;
    }
    $sql   = "SELECT `id` FROM " . T_USERS . " WHERE `id` <> {$user} AND `username`  LIKE '%$name%' {$sub_sql} $limit_text";
    $query = mysqli_query($sqlConnect, $sql);
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $data[] = UserData($fetched_data['id']);
    }
    return $data;
}
function RegisterAdminNotification($registration_data = array()) {
    global $sqlConnect, $music;
    if (isLogged() == false || empty($registration_data) || empty($registration_data['text'])) {
        return false;
    }
    if (empty($registration_data['full_link']) || empty($registration_data['recipients'])) {
        return false;
    }
    if (!is_array($registration_data['recipients']) || count($registration_data['recipients']) < 1) {
        return false;
    }
    $text  = $registration_data['text'];
    $link  = $registration_data['full_link'];
    $admin = $music->user->id;
    $time  = time();
    $sql   = "INSERT INTO " . T_NOTIFICATION . " (`notifier_id`,`recipient_id`,`type`,`text`,`url`,`time`) VALUES ";
    $val   = array();

    foreach ($registration_data['recipients'] as $user_id) {
        if ($admin != $user_id) {
            $val[] = "('$admin','$user_id','admin_notification','$text','$link','$time')";
        }
    }

    $query = mysqli_query($sqlConnect, ($sql . implode(',', $val)));
    return $query;
}
function GetUserIds() {
    global $sqlConnect, $music;
    if (isLogged() == false ) {
        return false;
    }
    $data  = array();
    $admin = $music->user->id;
    $query = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_USERS . " WHERE active = '1' AND `id` <> {$admin}");
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $data[] = $fetched_data['id'];
    }
    return $data;
}
function RegisterFollow($following_id = 0, $followers_id = 0) {
    global $music, $sqlConnect;


    if (!isset($following_id) or empty($following_id) or !is_numeric($following_id) or $following_id < 1) {
        return false;
    }
    if (!is_array($followers_id)) {
        $followers_id = array($followers_id);
    }
    foreach ($followers_id as $follower_id) {
        if (!isset($follower_id) or empty($follower_id) or !is_numeric($follower_id) or $follower_id < 1) {
            continue;
        }
        if (IsBlocked($following_id)) {
            continue;
        }
        $following_id = Secure($following_id);
        $follower_id  = Secure($follower_id);
        if (IsFollowing($following_id, $follower_id) === true) {
            continue;
        }
        $follower_data  = userData($follower_id);
        $following_data = userData($following_id);
        if (empty($follower_data->id) || empty($following_data->id)) {
            continue;
        }

        if ($following_id == $follower_id){
            continue;
        }

        $query = mysqli_query($sqlConnect, " INSERT INTO " . T_FOLLOWERS . " (`following_id`,`follower_id`) VALUES ({$following_id},{$follower_id})");
        if ($query) {
            $create_notification = createNotification([
                'notifier_id' => $follower_id,
                'recipient_id' => $following_id,
                'type' => 'follow_user',
            ]);
        }
    }
    return true;
}
function AutoFollow($user_id = 0) {
    global $music, $db;
    if (empty($user_id)) {
        return false;
    }
    if (!is_numeric($user_id) || $user_id == 0) {
        return false;
    }
    $get_users = explode(',', $music->config->auto_friend_users);
    if (!empty($get_users)) {
        foreach ($get_users as $key => $user) {
            $user = trim($user);
            $user = Secure($user);
            $getUserID = UserIdFromUsername($user);
            if (!empty($getUserID)) {
                $registerFollow = RegisterFollow($getUserID, $user_id);
            }
        }
        return true;
    } else {
        return false;
    }
}
function UserExists($username) {
    global $sqlConnect;
    if (empty($username)) {
        return false;
    }
    $username = Secure($username);
    $query    = mysqli_query($sqlConnect, "SELECT COUNT(`id`) FROM " . T_USERS . " WHERE `username` = '{$username}'");
    return (Sql_Result($query, 0) == 1) ? true : false;
}
function UserIdForLogin($username) {
    global $sqlConnect;
    if (empty($username)) {
        return false;
    }
    $username = Secure($username);
    $query    = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_USERS . " WHERE `username` = '{$username}' OR `email` = '{$username}'");
    return Sql_Result($query, 0, 'id');
}
function EmoPhone($string = '') {
    global $emo_full;
    foreach ($emo_full as $code => $name) {
        $code   = $code;
        $string = str_replace($code, $name, $string);
    }
    return $string;
}
function blog_categories(){
    global $db;
    $lang = $_SESSION['lang'];
    $blog_categories = $db->arrayBuilder()->where('ref','blog_categories')->get(T_LANGS,null,array('lang_key',$lang));
    $data = array();
    foreach ($blog_categories as $key => $value) {
        if(isset($value[$lang])) {
            $data[$value['lang_key']] = $value[$lang];
        }
    }
    return $data;
}
function RegisterNewBlogPost($registration_data) {
    global $sqlConnect;
    if (empty($registration_data)) {
        return false;
    }
    $fields = '`' . implode('`, `', array_keys($registration_data)) . '`';
    $data   = '\'' . implode('\', \'', $registration_data) . '\'';
    $query  = mysqli_query($sqlConnect, "INSERT INTO `".T_BLOG."` ({$fields}) VALUES ({$data})");
    if ($query) {
        return true;
    }
    return false;
}
function GetBlogArticles() {
    global $sqlConnect;
    $data          = array();
    $query_one     = "SELECT * FROM `".T_BLOG."` ORDER BY `id` DESC";
    $sql_query_one = mysqli_query($sqlConnect, $query_one);
    while ($fetched_data = mysqli_fetch_assoc($sql_query_one)) {
        $data[] = GetArticle($fetched_data['id']);
    }
    return $data;
}
function GetArticle($page_name) {
    global $sqlConnect;
    if (empty($page_name)) {
        return false;
    }
    $data          = array();
    $page_name     = Secure($page_name);
    $query_one     = "SELECT * FROM `".T_BLOG."` WHERE `id` = '{$page_name}'";
    $sql_query_one = mysqli_query($sqlConnect, $query_one);
    $fetched_data  = mysqli_fetch_assoc($sql_query_one);
    return $fetched_data;
}
function DeleteArticle($id, $thumbnail) {
    global $sqlConnect;
    if (empty($id)) {
        return false;
    }
    $id    = Secure($id);
    $query = mysqli_query($sqlConnect, "DELETE FROM `".T_BLOG."` WHERE `id` = {$id}");
    if ($query) {
        PT_DeleteFromToS3( $thumbnail );
        @unlink($thumbnail);
        return true;
    }
    return false;
}
function redirect($url) {
    header("Loacation: $url");
    exit();
}
function url_domain($url) {
    $host = @parse_url($url, PHP_URL_HOST);
    if (!$host) {
        $host = $url;
    }
    if (substr($host, 0, 4) == "www.") {
        $host = substr($host, 4);
    }
    if (strlen($host) > 50) {
        $host = substr($host, 0, 47) . '...';
    }
    return $host;
}
function get_user_ads($placement = 1,$ad_type = 'image') {
    global $db,$music;

    $execlude_text = '';
    if(!empty($music->user_ad_cons['uaid_'])){
        $execlude = implode(',', $music->user_ad_cons['uaid_']);
        $execlude_text = "AND id NOT IN ({$execlude})";
    }
    $placement_text = '';
    if ($ad_type == 'image') {
        $placement_text = " AND placement = {$placement} ";
    }

    $ad = $db->rawQuery("select * FROM ".T_USR_ADS." where status = 1 {$placement_text} {$execlude_text} AND ( day_limit > day_spend OR day_limit = 0 ) AND ad_type = '{$ad_type}' ORDER BY RAND() LIMIT 1");
    if (!empty($ad) && !empty($ad[0])) {
        $ad = $ad[0];
    }

    return (!empty($ad)) ? $ad : false;
}
function getAdAction($adID, $action = 'view'){
    global $db;
    $ads_result = $db->where('ad_id',$adID)->where('type',$action)->get(T_ADS_TRANS);
    $res = 0;
    if (!empty($ads_result)) {
        foreach ($ads_result as $key => $ad) {
            $res += 1;
        }
    }
    return $res;
}
function getAdSpent($adID){
    global $db;
    $ads_result = $db->where('ad_id',$adID)->where('type','spent')->get(T_ADS_TRANS);
    $res = 0;
    if (!empty($ads_result)) {
        foreach ($ads_result as $key => $ad) {
            $res += $ad->amount;
        }
    }
    return $res;
}
function register_ad_views($ad_id = false, $publisher_id = false) {
    global $music, $db;
    if (empty($ad_id) || empty($publisher_id)) {
        return false;
    }
    $ad     = $db->where('id', $ad_id)->getOne(T_USR_ADS);
    $result = false;
    if (!empty($ad)) {
        $ad_owner     = $db->where('id', $ad->user_id)->getOne(T_USERS);
        $con_price    = $music->config->ad_v_price;
        //$pub_price    = $music->config->pub_price;
        $ad_trans     = false;
        $is_owner     = false;
        $ad_tans_data = array(
            'results' => ($ad->results += 1)
        );
        if (IS_LOGGED) {
            $is_owner = ($ad->user_id == $music->user->id) ? true : false;
        }
        if (!array_key_exists($ad->id, $music->user_ad_cons['uaid_'])) {
            //if ($music->config->usr_v_mon == 'on') {
                //$track_owner = $db->where('id', $publisher_id)->getOne(T_USERS);
                //if (!empty($track_owner) && ($ad->user_id != $track_owner->id)) {
                //    $db->where('id', $publisher_id)->update(T_USERS, array(
                //        'balance' => (($track_owner->balance += $pub_price))
                //    ));
                    //$db->insert(T_ADS_TRANS,array('amount' => $pub_price,'type' => 'view', 'ad_id' => $ad->id, 'track_owner' => $publisher_id, 'time' => time()));

                //}
            //}
            $ad_tans_data['spent']              = ($ad->spent += $con_price);
            $ad_trans                           = true;
            $music->user_ad_cons['uaid_'][$ad->id] = $ad->id;
            setcookie('_uads', htmlentities(serialize($music->user_ad_cons)), time() + (10 * 365 * 24 * 60 * 60), '/');
            $db->insert(T_ADS_TRANS,array('amount' => $con_price ,'type' => 'spent', 'ad_id' => $ad->id, 'track_owner' => $publisher_id, 'time' => time()));
            $db->insert(T_ADS_TRANS,array('type' => 'view', 'ad_id' => $ad->id, 'time' => time()));

        }
        $update = $db->where('id', $ad_id)->update(T_USR_ADS, $ad_tans_data);
        if ($update && $ad_trans && !$is_owner) {
            $ad_value = ($ad_owner->wallet -= $con_price);
            if ($ad_value < 0) {
                $ad_value = 0;
            }
            $db->where('id', $ad_owner->id)->update(T_USERS, array(
                'wallet' => $ad_value
            ));
            if ($ad->day_limit > 0) {
                if ($ad->day == date("Y-m-d")) {
                    $db->where('id',$ad->id)->update(T_USR_ADS,array('day_spend' => ($ad->day_spend + $con_price)));
                }
                else{
                    $db->where('id',$ad->id)->update(T_USR_ADS,array('day_spend' => $con_price ,
                        'day'       => date("Y-m-d")));
                }
            }
            $result = true;
        }
    }
    return $result;
}
function SendSMSMessage($to, $message) {
    global $music;
    if (empty($to)) {
        return false;
    }
    if (!empty($music->config->sms_twilio_username) && !empty($music->config->sms_twilio_password) && !empty($music->config->sms_t_phone_number)) {
        include_once('assets/import/twilio/vendor/autoload.php');
        $account_sid = $music->config->sms_twilio_username;
        $auth_token  = $music->config->sms_twilio_password;
        $to          = secure($to);
        $client      = new Client($account_sid, $auth_token);
        try {
            $send = $client->account->messages->create($to, array(
                'from' => $music->config->sms_t_phone_number,
                'body' => $message
            ));
            if ($send) {
                return true;
            }
        }
        catch (Exception $e) {
            return false;
        }
        return false;
    }
    return false;
}
function VerifyIP($username = '') {
    global $music, $db;
    if (empty($username)) {
        return false;
    }
    if ($music->config->login_auth == 0) {
        return true;
    }
    $getuser = userData($username);
    $get_ip = '95.12.11.21';//get_ip_address();
    $getIpInfo = fetchDataFromURL("http://ip-api.com/json/$get_ip");
    $getIpInfo = json_decode($getIpInfo, true);
    if ($getIpInfo['status'] == 'success' && !empty($getIpInfo['regionName']) && !empty($getIpInfo['countryCode']) && !empty($getIpInfo['timezone']) && !empty($getIpInfo['city'])) {
        $create_new = false;
        $_SESSION['last_login_data'] = $getIpInfo;
        if (empty($getuser->last_login_data)) {
            $create_new = true;
        } else {
            $lastLoginData = unserialize($getuser->last_login_data);
            if (($getIpInfo['regionName'] != $lastLoginData['regionName']) || ($getIpInfo['countryCode'] != $lastLoginData['countryCode']) || ($getIpInfo['timezone'] != $lastLoginData['timezone']) || ($getIpInfo['city'] != $lastLoginData['city'])) {
                // send email
                $code = rand(111111, 999999);
                $hash_code = md5($code);
                $email['username'] = $getuser->name;
                $email['countryCode'] = $getIpInfo['countryCode'];
                $email['timezone'] = $getIpInfo['timezone'];
                $email['email'] = $getuser->email;
                $email['ip_address'] = $get_ip;
                $email['code'] = $code;
                $email['city'] = $getIpInfo['city'];
                $email['date'] = date("Y-m-d h:i:sa");
                $update_code =  $db->where('id', $username)->update(T_USERS, array('email_code' => $hash_code));
                $email_body = LoadPage("emails/unusual-login", $email);
                $send_message_data       = array(
                    'from_email' => $music->config->email,
                    'from_name' => $music->config->name,
                    'to_email' => $getuser->email,
                    'to_name' => $getuser->name,
                    'subject' => 'Please verify that it\'s you',
                    'charSet' => 'utf-8',
                    'message_body' => $email_body,
                    'is_html' => true
                );
                $send = SendMessage($send_message_data);
                if ($send && !empty($_SESSION['last_login_data'])) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
        if ($create_new == true) {
            $lastLoginData = serialize($getIpInfo);
            $update_user = $db->where('id', $username)->update(T_USERS, array('last_login_data' => $lastLoginData));
            return true;
        }
        return false;
    } else {
        return true;
    }
}
function TwoFactor($username = '') {
    global $music, $db;
    if (empty($username)) {
        return true;
    }
    if ($music->config->two_factor == 0) {
        return true;
    }
    $getuser = userData($username);
    if ($getuser->two_factor == 0 || $getuser->two_factor_verified == 0) {
        return true;
    }
    $code = rand(111111, 999999);
    $hash_code = md5($code);
    $update_code =  $db->where('id', $username)->update(T_USERS, array('email_code' => $hash_code));
    $message = "Your confirmation code is: $code";
    if (!empty($getuser->phone_number) && ($music->config->two_factor_type == 'both' || $music->config->two_factor_type == 'phone')) {
        $send_message = SendSMSMessage($getuser->phone_number, $message);
    }
    if ($music->config->two_factor_type == 'both' || $music->config->two_factor_type == 'email') {
        $send_message_data       = array(
            'from_email' => $music->config->email,
            'from_name' => $music->config->name,
            'to_email' => $getuser->email,
            'to_name' => $getuser->name,
            'subject' => 'Please verify that it\'s you',
            'charSet' => 'utf-8',
            'message_body' => $message,
            'is_html' => true
        );
        $send = SendMessage($send_message_data);
    }
    return false;
}
function ImportFormSoundCloud($trackUrl){
    global $music,$db,$user;
    if (empty($trackUrl) || IS_LOGGED == false) return false;
    require 'assets/import/getID3-1.9.14/getid3/getid3.php';

    $sound                          = array();
    $sound['itunes_affiliate_url'] = '';
    $sound['itunes_token'] = '';
    if (strpos($_POST['track_link'], 'soundcloud.com')) {

        $client_id                      = $music->config->sound_cloud_client_id;//'a3e059563d7fd3372b49b37f00a00bcf'
        $data                           = file_get_contents("https://api.soundcloud.com/resolve.json?url=$trackUrl&client_id=$client_id");
        if (empty($data)) {
            return false;
        }
        $data                           = json_decode($data, true);
        $sound['id'] = trim($data['id']);
        $sound['impoted_from'] = 'SOUNDCLOUD';
        $sound['title'] = trim($data['title']);
        $sound['description'] = trim($data['description']);
        $sound['original_content_size'] = trim($data['original_content_size']);
        $sound['duration'] = trim($data['duration']);
        $sound['original_format'] = trim($data['original_format']);
        $sound['artwork_url'] = trim($data['artwork_url']);
        $sound['tag_list'] = trim($data['tag_list']);
        $trackId = $data['id'];
        $data = file_get_contents("http://api.soundcloud.com/i1/tracks/$trackId/streams?client_id=$client_id");
        $data = json_decode($data, true);
        $sound['http_mp3_128_url'] = trim($data['http_mp3_128_url']);
    }
    elseif (strpos($_POST['track_link'], 'music.apple.com')) {
        $co = '';
        $array = explode('/', $_POST['track_link']);
        if (!empty($array[3])) {
            $co = $array[3];
        }
        $apple_id = substr($_POST['track_link'], strpos($_POST['track_link'], 'i=') + 2);
        $data                           = file_get_contents("https://itunes.apple.com/lookup?id=$apple_id&country=".$co);

        if (empty($data)) {
            return false;
        }
        $data                           = json_decode($data, true);
        if (empty($data['results']) || empty($data['results'][0])) {
            return false;
        }
        $data = $data['results'][0];
        $data['artworkUrl100'] = str_replace("100x100bb", "512x512bb", $data['artworkUrl100']);
        $sound['id'] = $trackId = trim($data['trackId']);
        $sound['impoted_from'] = 'ITUNES';
        $sound['title'] = trim($data['trackName']);
        $sound['description'] = trim($data['artistName']).' '.trim($data['trackName']);
        //$sound['original_content_size'] = trim($data['original_content_size']);
        $sound['duration'] = trim($data['trackTimeMillis']);
        $sound['original_format'] = pathinfo(trim($data['previewUrl']), PATHINFO_EXTENSION);
        $sound['artwork_url'] = trim($data['artworkUrl100']);
        $sound['tag_list'] = trim($data['primaryGenreName']);
        $sound['http_mp3_128_url'] = trim($data['previewUrl']);
        $aff_url = "https://geo.music.apple.com/us/album/";
        $itunes_aff = explode('/', trim($data['trackViewUrl']));
        foreach ($itunes_aff as $key => $value) {
            if ($value == 'album') {
                $aff_url .= $itunes_aff[$key + 1].'/'.$itunes_aff[$key + 2].'&app=music';
            }
        }
        $sound['itunes_affiliate_url'] = $aff_url;
        $sound['itunes_token'] = '';
        if ($music->config->itunes_affiliate == 'admin') {
            $sound['itunes_token'] = $music->config->itunes_partner_token;
        }
    }
    elseif (strpos($_POST['track_link'], 'deezer.com')) {
        $path = parse_url($_POST['track_link'])['path'];
        $array = explode('/', $path);
        $deezer_id = $array[count($array) - 1];
        $data                           = file_get_contents("https://api.deezer.com/track/$deezer_id");
        if (empty($data)) {
            return false;
        }
        $data                           = json_decode($data, true);
        $sound['id'] = trim($data['id']);
        $sound['impoted_from'] = 'DEEZER';
        $sound['title'] = trim($data['title']);
        $sound['description'] = trim($data['artist']['name']." ".$data['album']['title']." ".$data['title']);
        $sound['original_content_size'] = trim($data['rank']);
        $sound['duration'] = trim($data['duration']);
        $sound['original_format'] = trim(pathinfo($data['preview'], PATHINFO_EXTENSION));
        $sound['artwork_url'] = trim($data['album']['cover_xl']);
        $sound['tag_list'] = '';
        $trackId = $data['id'];
        $sound['http_mp3_128_url'] = trim($data['preview']);
    }
    
    

    //Check if this track imported before
    $imported = $db->where('src', $sound['impoted_from'].':'.$sound['id'])->getOne(T_SONGS);
    if (!empty($imported)) {
        return array('duplicated' => true);
    }else {


        if (!file_exists('upload/photos/' . date('Y'))) {
            @mkdir('upload/photos/' . date('Y'), 0777, true);
        }
        if (!file_exists('upload/photos/' . date('Y') . '/' . date('m'))) {
            @mkdir('upload/photos/' . date('Y') . '/' . date('m'), 0777, true);
        }
        if (!file_exists('upload/audio/' . date('Y'))) {
            @mkdir('upload/audio/' . date('Y'), 0777, true);
        }
        if (!file_exists('upload/audio/' . date('Y') . '/' . date('m'))) {
            @mkdir('upload/audio/' . date('Y') . '/' . date('m'), 0777, true);
        }
        if (!file_exists('upload/waves/' . date('Y'))) {
            @mkdir('upload/waves/' . date('Y'), 0777, true);
        }
        if (!file_exists('upload/waves/' . date('Y') . '/' . date('m'))) {
            @mkdir('upload/waves/' . date('Y') . '/' . date('m'), 0777, true);
        }

//        if ($music->config->ffmpeg_system == "on") {
//            $music->config->s3_upload = 'off';
//            $music->config->ftp_upload = 'off';
//        }

        $Photo = "upload/photos/" . date('Y') . '/' . date('m') . '/' . generateKey() . '_' . date('d') . '_' . md5(time()) . "_image.jpg";
        file_put_contents($Photo, file_get_contents($sound['artwork_url']));

        if (($music->config->s3_upload == 'on' || $music->config->ftp_upload == 'on') && !empty($Photo) && is_file($Photo)) {
            $upload_s3 = PT_UploadToS3($Photo);
        }

        $sound['thumbnail'] = trim($Photo);

        $Audio = $originalURL = "upload/audio/" . date('Y') . '/' . date('m') . '/' . generateKey() . '_' . date('d') . '_' . md5(time()) . "_audio.mp3";
        file_put_contents($Audio, file_get_contents($sound['http_mp3_128_url']));

        if(strpos($_POST['track_link'], 'soundcloud.com') && $music->config->soundcloud_go == 'off') {
            $getID3 = new getID3;
            $file = $getID3->analyze($Audio);
            $duration = '00:00';
            if (!empty($file['playtime_string'])) {
                $duration = secure($file['playtime_string']);
            }
            if ($duration == '0:30') {
                if (file_exists($Audio)) {
                    unlink($Audio);
                }
                return array('soundcloud_pro' => true);
            }
        }
        $generateWaveDark = '';
        $generateWaveLight = '';
        $generateWaveDay = '';
        if ($music->config->ffmpeg_system != "off") {
            $time = time();
            $full_dir = str_replace('assets' . DIRECTORY_SEPARATOR . 'includes', '/', __DIR__);
            $ffmpeg_b = $music->config->ffmpeg_binary_file;
            $filepath = explode('.', $Audio)[0];
            $originalURL = $filepath . "_" . rand(11111, 99999) . "_converted.mp3";

            $audio_output_mp3 = $full_dir . $originalURL;

            $key = generateKey(40, 40);
            $generateWaveLight = 'upload/waves/' . date('Y') . '/' . date('m') . '/' . $key . "_light.png";
            $generateWaveDark = 'upload/waves/' . date('Y') . '/' . date('m') . '/' . $key . "_dark.png";
            $generateWaveDay = 'upload/waves/' . date('Y') . '/' . date('m') . '/' . $key . "_day.png";
            $audio_output_light_wave = $full_dir . $generateWaveLight;
            $audio_output_black_wave = $full_dir . $generateWaveDark;
            $audio_output_day_wave = $full_dir . $generateWaveDay;
            $audio_file_full_path = $full_dir . $Audio;
            $wavecolor = $music->config->waves_color;
            $shell = shell_exec("$ffmpeg_b -i $audio_file_full_path -map 0:a:0 -b:a 192k $audio_output_mp3 2>&1");
            $shell = shell_exec("$ffmpeg_b -y -i $audio_output_mp3 -filter_complex \"aformat=channel_layouts=mono,showwavespic=s=1100x150:colors=#474747\" -frames:v 1 $audio_output_black_wave 2>&1");
            $shell = shell_exec("$ffmpeg_b -y -i $audio_output_mp3 -filter_complex \"aformat=channel_layouts=mono,showwavespic=s=1100x150:colors=" . $wavecolor . "\" -frames:v 1 $audio_output_light_wave 2>&1");
            $shell = shell_exec("$ffmpeg_b -y -i $audio_output_mp3 -filter_complex \"aformat=channel_layouts=mono,showwavespic=s=1100x150:colors=#bfbfbf\" -frames:v 1 $audio_output_day_wave 2>&1");

            $sound['audio_location'] = $originalURL;
            if (file_exists($Audio)) {
                unlink($Audio);
            }
        }

            

        if (file_exists($generateWaveLight) && file_exists($generateWaveDark)) {
            $sound['dark_wave'] = $generateWaveDark;
            $sound['light_wave'] = $generateWaveLight;
        }

        $audio_id = generateKey(15, 15);
        $check_for_audio = $db->where('audio_id', $audio_id)->getValue(T_SONGS, 'count(*)');
        if ($check_for_audio > 0) {
            $audio_id = generateKey(15, 15);
        }

        $getID3 = new getID3;
        $file = $getID3->analyze($sound['audio_location']);
        $duration = '00:00';
        if (!empty($file['playtime_string'])) {
            $duration = secure($file['playtime_string']);
        }
        $filesize = 0;
        if (!empty($file['filesize'])) {
            $filesize = $file['filesize'];
        }
        $id = $db->insert(T_SONGS, array(
            'audio_id' => $audio_id,
            'user_id' => $user->id,
            'title' => secure($sound['title']),
            'description' => secure($sound['description']),
            'lyrics' => '',
            'tags' => str_replace(' ', ',', $sound['tag_list']),
            'duration' => $duration,
            'audio_location' => '',
            'category_id' => 0,
            'thumbnail' => $sound['thumbnail'],
            'time' => time(),
            'registered' => date('Y') . '/' . intval(date('m')),
            'size' => $filesize,
            'availability' => 0,
            'age_restriction' => 0,
            'price' => 0,
            'spotlight' => 0,
            'ffmpeg' => 0,
            'allow_downloads' => 1,
            'display_embed' => 1,
            'dark_wave' => $generateWaveLight,
            'light_wave' => $generateWaveDark,
            'audio_location' => $originalURL,
            'src' => $sound['impoted_from'].':' . $sound['id'],
            'itunes_affiliate_url' => $sound['itunes_affiliate_url'],
            'itunes_token' => $sound['itunes_token']
        ));
        if ($id) {
            $sound['audio_id'] = $audio_id;
        }
        
        PT_UploadToS3($generateWaveLight);
        PT_UploadToS3($generateWaveDark);
        PT_UploadToS3($generateWaveDay);
        PT_UploadToS3($originalURL);
        //file_put_contents('./'.$trackname.'.mp3' ,file_get_contents($mp3));
        return $sound;
    }
}

function LastSeen($user_id, $type = '') {
    global $music, $sqlConnect;
    if ($music->loggedin == false) {
        return false;
    }
    if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
        return false;
    }
    if ($type == 'first') {
        $user = userData($user_id);
        if ($music->user->status == 1) {
            return false;
        }
    } else {
        if ($music->user->status == 1) {
            return false;
        }
    }
    $user_id = Secure($user_id);
    $user_session_id = $music->user_session_id;
    $sql =  " UPDATE " . T_APP_SESSIONS . " SET `time` = " . time() . " WHERE `user_id` = {$user_id} AND `session_id` = '{$user_session_id}'";
    $query   = mysqli_query($sqlConnect, $sql);
    if ($query) {
        return true;
    } else {
        return false;
    }
}

//ImportFormSoundCloud('https://soundcloud.com/uiceheidd/bandit-ft-nba-youngboy');
//exit();
/* Function By Qassim Hassan, wp-time.com */
function http_request_call($method, $url, $header, $data, $json){
    if( $method == 1 ){
        $method_type = 1; // 1 = POST
    }else{
        $method_type = 0; // 0 = GET
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_HEADER, 0);

    if( $header !== 0 ){
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    }

    curl_setopt($curl, CURLOPT_POST, $method_type);

    if( $data !== 0 ){
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }

    $response = curl_exec($curl);

    if( $json == 0 ){
        $json = $response;
    }else{
        $json = json_decode($response, true);
    }

    curl_close($curl);

    return $json;
}

function RegisterNewField($registration_data) {
    global $music, $sqlConnect;
    if (empty($registration_data)) {
        return false;
    }
    $fields = '`' . implode('`, `', array_keys($registration_data)) . '`';
    $data   = '\'' . implode('\', \'', $registration_data) . '\'';
    $query  = mysqli_query($sqlConnect, "INSERT INTO " . T_FIELDS . " ({$fields}) VALUES ({$data})");
    if ($query) {
        $sql_id  = mysqli_insert_id($sqlConnect);
        $column  = 'fid_' . $sql_id;
        $length  = $registration_data['length'];
        $query_2 = mysqli_query($sqlConnect, "ALTER TABLE " . T_USERS_FIELDS . " ADD COLUMN `{$column}` varchar({$length}) NOT NULL DEFAULT ''");
        return true;
    }
    return false;
}
function GetWelcomeFields() {
    global $music, $sqlConnect;
    $data      = array();
    $query_one = " SELECT * FROM " . T_FIELDS . " WHERE `registration_page` = '1' ORDER BY `id` ASC";
    $sql       = mysqli_query($sqlConnect, $query_one);
    while ($fetched_data = mysqli_fetch_assoc($sql)) {
        $fetched_data['fid'] = 'fid_' . $fetched_data['id'];
        $fetched_data['name'] = preg_replace_callback("/{{LANG (.*?)}}/", function($m) use ($music) {
            return lang($m[1]);
        }, $fetched_data['name']);
        $fetched_data['description'] = preg_replace_callback("/{{LANG (.*?)}}/", function($m) use ($music) {
            return lang($m[1]);
        }, $fetched_data['description']);
        $fetched_data['type'] = preg_replace_callback("/{{LANG (.*?)}}/", function($m) use ($music) {
            return lang($m[1]);
        }, $fetched_data['type']);
        $data[]               = $fetched_data;
    }
    return $data;
}
function GetProfileFields($type = 'all') {
    global $music, $sqlConnect;
    $data       = array();
    $where      = '';
    $placements = array(
        'profile',
        'general',
        'social'
    );
    if ($type != 'all' && in_array($type, $placements)) {
        $where = "WHERE `placement` = '{$type}' AND `placement` <> 'none' AND `active` = '1'";
    } else if ($type == 'none') {
        $where = "WHERE `profile_page` = '1' AND `active` = '1'";
    } else if ($type != 'admin') {
        $where = "WHERE `active` = '1'";
    }
    $type      = Secure($type);
    $query_one = "SELECT * FROM " . T_FIELDS . " {$where} ORDER BY `id` ASC";
    $sql       = mysqli_query($sqlConnect, $query_one);
    while ($fetched_data = mysqli_fetch_assoc($sql)) {
        $fetched_data['fid'] = 'fid_' . $fetched_data['id'];
        $fetched_data['name'] = preg_replace_callback("/{{LANG (.*?)}}/", function($m) use ($music) {
            return lang($m[1]);
        }, $fetched_data['name']);
        $fetched_data['description'] = preg_replace_callback("/{{LANG (.*?)}}/", function($m) use ($music) {
            return lang($m[1]);
        }, $fetched_data['description']);
        $fetched_data['type'] = preg_replace_callback("/{{LANG (.*?)}}/", function($m) use ($music) {
            return lang($m[1]);
        }, $fetched_data['type']);
        $data[]               = $fetched_data;
    }
    return $data;
}
function GetUserCustomFields() {
    global $music, $sqlConnect;
    $data       = array();
    $where = "WHERE `active` = '1' AND `profile_page` = 1";

    $query_one = "SELECT * FROM " . T_FIELDS . " {$where} ORDER BY `id` ASC";
    $sql       = mysqli_query($sqlConnect, $query_one);
    while ($fetched_data = mysqli_fetch_assoc($sql)) {
        $fetched_data['fid'] = 'fid_' . $fetched_data['id'];
        $fetched_data['name'] = preg_replace_callback("/{{LANG (.*?)}}/", function($m) use ($music) {
            return lang($m[1]);
        }, $fetched_data['name']);
        $fetched_data['description'] = preg_replace_callback("/{{LANG (.*?)}}/", function($m) use ($music) {
            return lang($m[1]);
        }, $fetched_data['description']);
        $fetched_data['type'] = preg_replace_callback("/{{LANG (.*?)}}/", function($m) use ($music) {
            return lang($m[1]);
        }, $fetched_data['type']);
        $data[]               = $fetched_data;
    }
    return $data;
}
function UserFieldsData($user_id) {
    global $music, $sqlConnect;
    if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
        return false;
    }
    $data         = array();
    $user_id      = Secure($user_id);
    $query_one    = "SELECT * FROM " . T_USERS_FIELDS . " WHERE `user_id` = {$user_id}";
    $sql          = mysqli_query($sqlConnect, $query_one);
    $fetched_data = mysqli_fetch_assoc($sql);
    if (empty($fetched_data)) {
        return array();
    }
    return $fetched_data;
}
function UpdateUserCustomData($user_id, $update_data, $loggedin = true) {
    global $music, $sqlConnect, $cache;
    if ($loggedin == true) {
        if ($music->loggedin == false) {
            return false;
        }
    }
    if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
        return false;
    }
    if (empty($update_data)) {
        return false;
    }
    $user_id = Secure($user_id);
    if ($loggedin == true) {
        if (IsAdmin() === false) {
            if ($music->user->id != $user_id) {
                return false;
            }
        }
    }
    $update = array();
    foreach ($update_data as $field => $data) {
        foreach ($data as $key => $value) {
            $update[] = '`' . $key . '` = \'' . Secure($value, 0) . '\'';
        }
    }
    $impload     = implode(', ', $update);
    $query_one   = "UPDATE " . T_USERS_FIELDS . " SET {$impload} WHERE `user_id` = {$user_id}";
    $query_1     = mysqli_query($sqlConnect, "SELECT COUNT(`id`) as count FROM " . T_USERS_FIELDS . " WHERE `user_id` = {$user_id}");
    $query_1_sql = mysqli_fetch_assoc($query_1);
    $query       = false;
    if ($query_1_sql['count'] == 1) {
        $query = mysqli_query($sqlConnect, $query_one);
    } else {
        $query_2 = mysqli_query($sqlConnect, "INSERT INTO " . T_USERS_FIELDS . " (`user_id`) VALUES ({$user_id})");
        if ($query_2) {
            $query = mysqli_query($sqlConnect, $query_one);
        }
    }
    if ($query) {
        return true;
    }
    return false;
}
function GetFieldData($id = 0) {
    global $music, $sqlConnect;
    if (empty($id) || !is_numeric($id) || $id < 0) {
        return false;
    }
    $data         = array();
    $id           = Secure($id);
    $query_one    = "SELECT * FROM " . T_FIELDS . " WHERE `id` = {$id}";
    $sql          = mysqli_query($sqlConnect, $query_one);
    $fetched_data = mysqli_fetch_assoc($sql);
    if (empty($fetched_data)) {
        return array();
    }
    return $fetched_data;
}
function UpdateField($id, $update_data) {
    global $music, $sqlConnect, $cache;
    if ($music->loggedin == false) {
        return false;
    }
    if (empty($id) || !is_numeric($id) || $id < 0) {
        return false;
    }
    if (empty($update_data)) {
        return false;
    }
    $id = Secure($id);
    if (IsAdmin() === false) {
        return false;
    }
    $update = array();
    foreach ($update_data as $field => $data) {
        $update[] = '`' . $field . '` = \'' . Secure($data, 0) . '\'';
        if ($field == 'length') {
            $mysqli = mysqli_query($sqlConnect, "ALTER TABLE " . T_USERS_FIELDS . " CHANGE `fid_{$id}` `fid_{$id}` VARCHAR(" . Secure($data) . ") CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';");
        }
    }
    $impload   = implode(', ', $update);
    $query_one = "UPDATE " . T_FIELDS . " SET {$impload} WHERE `id` = {$id} ";
    $query     = mysqli_query($sqlConnect, $query_one);
    if ($query) {
        return true;
    }
    return false;
}
function DeleteField($id) {
    global $music, $sqlConnect;
    if ($music->loggedin == false) {
        return false;
    }
    if (IsAdmin() === false) {
        return false;
    }
    $id    = Secure($id);
    $query = mysqli_query($sqlConnect, "DELETE FROM " . T_FIELDS . " WHERE `id` = {$id}");
    if ($query) {
        $query2 = mysqli_query($sqlConnect, "ALTER TABLE " . T_USERS_FIELDS . " DROP `fid_{$id}`;");
        if ($query2) {
            return true;
        }
    }
    return false;
}


function GetAdminInvitation() {
    global $sqlConnect, $music;
    if ($music->loggedin == false || !IsAdmin()) {
        return false;
    }
    $query = mysqli_query($sqlConnect, "SELECT * FROM " . T_INVITATIONS . " ORDER BY `id` DESC ");
    $data  = array();
    $site  = $music->config->site_url . '/?invite=';
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $fetched_data['url'] = $site . $fetched_data['code'];
        $data[]              = $fetched_data;
    }
    return $data;
}
function InsertAdminInvitation() {
    global $sqlConnect, $music;
    if ($music->loggedin == false || !IsAdmin()) {
        return false;
    }
    $time  = time();
    $code  = str_replace('.','',uniqid(rand(), true));
    $sql   = "INSERT INTO " . T_INVITATIONS . " (`id`,`code`,`posted`) VALUES (NULL,'$code', '$time')";
    $site  = $music->config->site_url . '/?invite=';
    $query = mysqli_query($sqlConnect, $sql);
    if ($query) {
        $last_id = mysqli_insert_id($sqlConnect);
        $data    = mysqli_query($sqlConnect, "SELECT * FROM " . T_INVITATIONS . " WHERE `id` = {$last_id}");
        if ($data && mysqli_num_rows($data) > 0) {
            $fetched_data        = mysqli_fetch_assoc($data);
            $fetched_data['url'] = $site . $fetched_data['code'];
            return $fetched_data;
        }
    }
    return false;
}
function DeleteAdminInvitation($col = '', $val = false) {
    global $sqlConnect, $music;
    if (!$val && !$col) {
        return false;
    }
    $val = Secure($val);
    $col = Secure($col);
    return mysqli_query($sqlConnect, "DELETE FROM " . T_INVITATIONS . " WHERE `$col` = '$val'");
}
function IsAdminInvitationExists($code = false) {
    global $sqlConnect, $music;
    if (!$code) {
        return false;
    }
    $code      = Secure($code);
    $data_rows = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_INVITATIONS . " WHERE `code` = '$code' AND `status` = 'Pending'");
    return mysqli_num_rows($data_rows) > 0;
}
function GetRadioStations($search = "Music"){
    global $music;
    $curl = curl_init();
    $_api = "https://30-000-radio-stations-and-music-charts.p.rapidapi.com/rapidapi?country=ALL&keyword=".$search."&genre=ALL";
    curl_setopt_array($curl, array(
        CURLOPT_URL => $_api,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "x-rapidapi-host: 30-000-radio-stations-and-music-charts.p.rapidapi.com",
            "x-rapidapi-key: " . $music->config->rapidapi_key
        ),
    ));
    $response = curl_exec($curl);
    $response = json_decode($response, true);
    $return = array();
    if(isset($response['results'])) {
        foreach ($response['results'] as $key => $value) {
            $return[$value['i']]['radio_id'] = $value['i'];
            $return[$value['i']]['country'] = $value['c'];
            $return[$value['i']]['genre'] = $value['g'];
            $return[$value['i']]['logo'] = 'https://www.radioair.info/app/images_radios/' . $value['l'];
            $return[$value['i']]['image'] = 'https://www.radioair.info/images_radios/' . $value['l'];
            $return[$value['i']]['name'] = $value['n'];
            $return[$value['i']]['url'] = $value['u'];
            $return[$value['i']]['genre_id'] = $value['d'];
        }
    }
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        //echo "cURL Error #:" . $err;
        return array();
    } else {
        return $return;
    }
}

//var_dump(GetRadioStations("Music"));
//exit();
//function userEmailNotification($user_id){
//    global $db, $music;
//    if (empty($user_id) || !IS_LOGGED) {
//        return false;
//    }
//    $u = userData($user_id);
//    $data = array(
//        'email_on_profile_view'             => $u->email_on_profile_view,
//        'email_on_new_message'              => $u->email_on_new_message,
//        'email_on_profile_like'             => $u->email_on_profile_like,
//        'email_on_purchase_notifications'   => $u->email_on_purchase_notifications,
//        'email_on_special_offers'           => $u->email_on_special_offers,
//        'email_on_announcements'            => $u->email_on_announcements,
//        'email_on_get_gift'                 => $u->email_on_get_gift,
//        'email_on_got_new_match'            => $u->email_on_got_new_match,
//        'email_on_chat_request'             => $u->email_on_chat_request
//    );
//    if (!in_array(1, $data)) {
//        return false;
//    } else {
//        return $data;
//    }
//}
function GetCustomPages() {
    global $sqlConnect;
    $data          = array();
    $query_one     = "SELECT * FROM " . T_CUSTOM_PAGES . " ORDER BY `id` DESC";
    $sql_query_one = mysqli_query($sqlConnect, $query_one);
    while ($fetched_data = mysqli_fetch_assoc($sql_query_one)) {
        $data[] = $fetched_data;
    }
    return $data;
}
function RegisterNewPage($registration_data) {
    global $sqlConnect;
    if (empty($registration_data)) {
        return false;
    }
    $fields = '`' . implode('`, `', array_keys($registration_data)) . '`';
    $data   = '\'' . implode('\', \'', $registration_data) . '\'';
    $query  = mysqli_query($sqlConnect, "INSERT INTO " . T_CUSTOM_PAGES . " ({$fields}) VALUES ({$data})");

    if ($query) {
        return true;
    }
    return false;
}
function DeleteCustomPage($id) {
    global $music, $sqlConnect;
    if ($music->loggedin == false || !IsAdmin()) {
        return false;
    }
    $id    = Secure($id);
    $query = mysqli_query($sqlConnect, "DELETE FROM " . T_CUSTOM_PAGES . " WHERE `id` = {$id}");
    if ($query) {
        return true;
    }
    return false;
}
function UpdateCustomPageData($id, $update_data) {
    global $music, $sqlConnect;
    if ($music->loggedin == false || !IsAdmin()) {
        return false;
    }
    if (empty($id) || !is_numeric($id) || $id < 0) {
        return false;
    }
    if (empty($update_data)) {
        return false;
    }
    $id = Secure($id);
    $update = array();
    foreach ($update_data as $field => $data) {
        $update[] = '`' . $field . '` = \'' . Secure($data, 0) . '\'';
    }
    $impload   = implode(', ', $update);
    $query_one = "UPDATE " . T_CUSTOM_PAGES . " SET {$impload} WHERE `id` = {$id} ";
    $query     = mysqli_query($sqlConnect, $query_one);
    if ($query) {
        return true;
    }
    return false;
}
function GetCustomPage($page_name) {
    global $sqlConnect;
    if (empty($page_name)) {
        return false;
    }
    $data          = array();
    $page_name     = Secure($page_name);
    $query_one     = "SELECT * FROM " . T_CUSTOM_PAGES . " WHERE `page_name` = '{$page_name}'";
    $sql_query_one = mysqli_query($sqlConnect, $query_one);
    $fetched_data  = mysqli_fetch_assoc($sql_query_one);
    return $fetched_data;
}
function PublishArticle($id) {
    global $sqlConnect;
    if (empty($id)) {
        return false;
    }
    $id    = Secure($id);
    $query = mysqli_query($sqlConnect, "UPDATE `".T_BLOG."` SET `posted` = 1 WHERE `id` = {$id}");
    if ($query) {
        return true;
    }
    return false;
}
function UnPublishArticle($id) {
    global $sqlConnect;
    if (empty($id)) {
        return false;
    }
    $id    = Secure($id);
    $query = mysqli_query($sqlConnect, "UPDATE `".T_BLOG."` SET `posted` = 0 WHERE `id` = {$id}");
    if ($query) {
        return true;
    }
    return false;
}
function GetUsersByTime($type = 'week') {
    global $sqlConnect;
    $types = array('week','month','3month','6month','9month','year');
    if (empty($type) || !in_array($type, $types)) {
        return array();
    }
    $data      = array();
    $end = time() - (60 * 60 * 24 * 7);
    $start = time() - (60 * 60 * 24 * 14);
    if ($type == 'month') {
        $end = time() - (60 * 60 * 24 * 30);
        $start = time() - (60 * 60 * 24 * 60);
    }
    if ($type == '3month') {
        $end = time() - (60 * 60 * 24 * 61);
        $start = time() - (60 * 60 * 24 * 150);
    }
    if ($type == '6month') {
        $end = time() - (60 * 60 * 24 * 151);
        $start = time() - (60 * 60 * 24 * 210);
    }
    if ($type == '9month') {
        $end = time() - (60 * 60 * 24 * 211);
        $start = time() - (60 * 60 * 24 * 300);
    }
    if ($type == 'year') {
        $end = time() - (60 * 60 * 24 * 365);
    }
    $sub1 = " WHERE `last_active` >= '{$start}' ";
    $sub2 = " AND `last_active` <= '{$end}' ";
    if ($type == 'year') {
        $sub2 = "";
    }
    $query_one = " SELECT `id` FROM " . T_USERS.$sub1.$sub2;
    $sql = mysqli_query($sqlConnect, $query_one);
    if (mysqli_num_rows($sql)) {
        while ($fetched_data = mysqli_fetch_assoc($sql)) {
            $data[] = userData($fetched_data['id']);
        }
    }
        
    return $data;
}
function GetAllUsersByType($type = 'all') {
    global $sqlConnect;
    $data      = array();
    $query_one = " SELECT `id` FROM " . T_USERS;
    if ($type == 'active') {
        $query_one .= " WHERE `active` = '1'";
    } else if ($type == 'inactive') {
        $query_one .= " WHERE `active` = '0' OR `active` = '2'";
    } else if ($type == 'all') {
        $query_one .= "";
    }
    $sql = mysqli_query($sqlConnect, $query_one);
    if (mysqli_num_rows($sql)) {
        while ($fetched_data = mysqli_fetch_assoc($sql)) {
            $data[] = userData($fetched_data['id']);
        }
    }
        
    return $data;
}
function CanLogin() {
    global $music, $sqlConnect,$db;
    if ($music->loggedin == true) {
        return false;
    }
    $ip = get_ip_address();
    if (empty($ip)) {
        return true;
    }
    if ($music->config->lock_time < 1) {
        return true;
    }
    if ($music->config->bad_login_limit < 1) {
        return true;
    }

    $time      = time() - (60 * $music->config->lock_time);
    $login = $db->where('ip',$ip)->get(T_BAD_LOGIN);
    if (count($login) >= $music->config->bad_login_limit) {
        $last = end($login);
        if ($last->time >= $time) {
            return false;
        }
    }
    $db->where('time',time()-(60 * $music->config->lock_time * 2),'<')->delete(T_BAD_LOGIN);
    return true;
}
function AddBadLoginLog() {
    global $music, $sqlConnect;
    if ($music->loggedin == true) {
        return false;
    }
    $ip = get_ip_address();
    if (empty($ip)) {
        return true;
    }
    $time      = time();
    $query     = mysqli_query($sqlConnect, "INSERT INTO " . T_BAD_LOGIN . " (`ip`, `time`) VALUES ('{$ip}', '{$time}')");
    if ($query) {
        return true;
    }
}
?>