<?php
// +------------------------------------------------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.playtubescript.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: wowondersocial@gmail.com   
// +------------------------------------------------------------------------+
// | PlayTube - The Ultimate Video Sharing Platform
// | Copyright (c) 2017 PlayTube. All rights reserved.
// +------------------------------------------------------------------------+
function loadPage($page_url = '', $data = array(), $set_lang = true) {
    global $music, $lang_array, $config, $countries_name, $db;
    $page = './themes/' . $config['theme'] . '/layout/' . $page_url . '.html';
    if (!file_exists($page)) {
        die("File not Exists : $page");
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
    if ($music->loggedin == true) {
        $replace = ToArray($music->user);
        $page_content = preg_replace_callback("/{{ME (.*?)}}/", function($m) use ($replace) {
            return (isset($replace[$m[1]])) ? $replace[$m[1]] : '';
        }, $page_content);
    }
    $page_content = preg_replace("/{{LINK (.*?)}}/", getLink("$1"), $page_content);
    $page_content = preg_replace_callback("/{{CONFIG (.*?)}}/", function($m) use ($config) {
        return (isset($config[$m[1]])) ? $config[$m[1]] : '';
    }, $page_content);
    return $page_content;
}
function fetchDataFromURL($url = '') {
    if (empty($url)) {
        return false;
    }
    $ch = curl_init($url);
    curl_setopt( $ch, CURLOPT_POST, false );
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
    curl_setopt( $ch, CURLOPT_HEADER, false );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt( $ch, CURLOPT_TIMEOUT, 5);
    return curl_exec( $ch );
}
function PT_LoadAdminPage($page_url = '', $data = array(), $set_lang = true) {
    global $pt, $lang_array, $config, $db;
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
            return (isset($lang_array[$m[1]])) ? $lang_array[$m[1]] : '';
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
                $object_to_replace = "{{" . $key . "}}";
                $page_content      = str_replace($object_to_replace, $replace, $page_content);
            }
        }
    }
    if (IS_LOGGED == true) {
        $replace = ToArray($pt->user);
        $page_content = preg_replace_callback("/{{ME (.*?)}}/", function($m) use ($replace) {
            return (isset($replace[$m[1]])) ? $replace[$m[1]] : '';
        }, $page_content);
    }
    $page_content = preg_replace("/{{LINK (.*?)}}/", getLink("$1"), $page_content);
    $page_content = preg_replace_callback("/{{CONFIG (.*?)}}/", function($m) use ($config) {
        return (isset($config[$m[1]])) ? $config[$m[1]] : '';
    }, $page_content);
    return $page_content;
}
function getLink($string) {
    global $site_url;
    return $site_url . '/' . $string;
}
function getMedia($media = '', $is_upload = false){
    global $music;
    if (empty($media)) {
        return '';
    }

    $media_url     = $music->config->site_url . '/' . $media;
    if ($music->config->s3_upload == 'on' && $is_upload == false) {
        $media_url = "https://" . $music->config->s3_bucket_name . ".s3.amazonaws.com/" . $media;
    } else if ($music->config->ftp_upload == "on") {
        return addhttp($music->config->ftp_endpoint) . '/' . $media;
    }

    return $media_url;
}
function PT_Slug($string, $video_id) {
    global $pt;
    if ($pt->config->seo_link != 'on') {
        return $video_id;
    }
    $slug = url_slug($string, array(
        'delimiter' => '-',
        'limit' => 100,
        'lowercase' => true,
        'replacements' => array(
            '/\b(an)\b/i' => 'a',
            '/\b(example)\b/i' => 'Test'
        )
    ));
    return $slug . '_' . $video_id . '.html';
}
function PT_URLSlug($string, $id) {
    global $pt;
    $slug = url_slug($string, array(
        'delimiter' => '-',
        'limit' => 100,
        'lowercase' => true,
        'replacements' => array(
            '/\b(an)\b/i' => 'a',
            '/\b(example)\b/i' => 'Test'
        )
    ));
    return $slug . '_' . $id . '.html';
}
function PT_LoadAdminLinkSettings($link = '') {
    global $site_url;
    return $site_url . '/admin-cp/' . $link;
}
function PT_LoadAdminLink($link = '') {
    global $site_url;
    return $site_url . '/admin-panel/' . $link;
}
function url_slug($str, $options = array()) {
    // Make sure string is in UTF-8 and strip invalid UTF-8 characters
    $str      = mb_convert_encoding((string) $str, 'UTF-8', mb_list_encodings());
    $defaults = array(
        'delimiter' => '-',
        'limit' => null,
        'lowercase' => true,
        'replacements' => array(),
        'transliterate' => false
    );
    // Merge options
    $options  = array_merge($defaults, $options);
    $char_map = array(
        // Latin
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Ä' => 'A',
        'Å' => 'A',
        'Æ' => 'AE',
        'Ç' => 'C',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ð' => 'D',
        'Ñ' => 'N',
        'Ò' => 'O',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ö' => 'O',
        'Ő' => 'O',
        'Ø' => 'O',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ű' => 'U',
        'Ý' => 'Y',
        'Þ' => 'TH',
        'ß' => 'ss',
        'à' => 'a',
        'á' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'å' => 'a',
        'æ' => 'ae',
        'ç' => 'c',
        'è' => 'e',
        'é' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ì' => 'i',
        'í' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ð' => 'd',
        'ñ' => 'n',
        'ò' => 'o',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        'ő' => 'o',
        'ø' => 'o',
        'ù' => 'u',
        'ú' => 'u',
        'û' => 'u',
        'ü' => 'u',
        'ű' => 'u',
        'ý' => 'y',
        'þ' => 'th',
        'ÿ' => 'y',
        // Latin symbols
        '©' => '(c)',
        // Greek
        'Α' => 'A',
        'Β' => 'B',
        'Γ' => 'G',
        'Δ' => 'D',
        'Ε' => 'E',
        'Ζ' => 'Z',
        'Η' => 'H',
        'Θ' => '8',
        'Ι' => 'I',
        'Κ' => 'K',
        'Λ' => 'L',
        'Μ' => 'M',
        'Ν' => 'N',
        'Ξ' => '3',
        'Ο' => 'O',
        'Π' => 'P',
        'Ρ' => 'R',
        'Σ' => 'S',
        'Τ' => 'T',
        'Υ' => 'Y',
        'Φ' => 'F',
        'Χ' => 'X',
        'Ψ' => 'PS',
        'Ω' => 'W',
        'Ά' => 'A',
        'Έ' => 'E',
        'Ί' => 'I',
        'Ό' => 'O',
        'Ύ' => 'Y',
        'Ή' => 'H',
        'Ώ' => 'W',
        'Ϊ' => 'I',
        'Ϋ' => 'Y',
        'α' => 'a',
        'β' => 'b',
        'γ' => 'g',
        'δ' => 'd',
        'ε' => 'e',
        'ζ' => 'z',
        'η' => 'h',
        'θ' => '8',
        'ι' => 'i',
        'κ' => 'k',
        'λ' => 'l',
        'μ' => 'm',
        'ν' => 'n',
        'ξ' => '3',
        'ο' => 'o',
        'π' => 'p',
        'ρ' => 'r',
        'σ' => 's',
        'τ' => 't',
        'υ' => 'y',
        'φ' => 'f',
        'χ' => 'x',
        'ψ' => 'ps',
        'ω' => 'w',
        'ά' => 'a',
        'έ' => 'e',
        'ί' => 'i',
        'ό' => 'o',
        'ύ' => 'y',
        'ή' => 'h',
        'ώ' => 'w',
        'ς' => 's',
        'ϊ' => 'i',
        'ΰ' => 'y',
        'ϋ' => 'y',
        'ΐ' => 'i',
        // Turkish
        'Ş' => 'S',
        'İ' => 'I',
        'Ç' => 'C',
        'Ü' => 'U',
        'Ö' => 'O',
        'Ğ' => 'G',
        'ş' => 's',
        'ı' => 'i',
        'ç' => 'c',
        'ü' => 'u',
        'ö' => 'o',
        'ğ' => 'g',
        // Russian
        'А' => 'A',
        'Б' => 'B',
        'В' => 'V',
        'Г' => 'G',
        'Д' => 'D',
        'Е' => 'E',
        'Ё' => 'Yo',
        'Ж' => 'Zh',
        'З' => 'Z',
        'И' => 'I',
        'Й' => 'J',
        'К' => 'K',
        'Л' => 'L',
        'М' => 'M',
        'Н' => 'N',
        'О' => 'O',
        'П' => 'P',
        'Р' => 'R',
        'С' => 'S',
        'Т' => 'T',
        'У' => 'U',
        'Ф' => 'F',
        'Х' => 'H',
        'Ц' => 'C',
        'Ч' => 'Ch',
        'Ш' => 'Sh',
        'Щ' => 'Sh',
        'Ъ' => '',
        'Ы' => 'Y',
        'Ь' => '',
        'Э' => 'E',
        'Ю' => 'Yu',
        'Я' => 'Ya',
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'д' => 'd',
        'е' => 'e',
        'ё' => 'yo',
        'ж' => 'zh',
        'з' => 'z',
        'и' => 'i',
        'й' => 'j',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ф' => 'f',
        'х' => 'h',
        'ц' => 'c',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'sh',
        'ъ' => '',
        'ы' => 'y',
        'ь' => '',
        'э' => 'e',
        'ю' => 'yu',
        'я' => 'ya',
        // Ukrainian
        'Є' => 'Ye',
        'І' => 'I',
        'Ї' => 'Yi',
        'Ґ' => 'G',
        'є' => 'ye',
        'і' => 'i',
        'ї' => 'yi',
        'ґ' => 'g',
        // Czech
        'Č' => 'C',
        'Ď' => 'D',
        'Ě' => 'E',
        'Ň' => 'N',
        'Ř' => 'R',
        'Š' => 'S',
        'Ť' => 'T',
        'Ů' => 'U',
        'Ž' => 'Z',
        'č' => 'c',
        'ď' => 'd',
        'ě' => 'e',
        'ň' => 'n',
        'ř' => 'r',
        'š' => 's',
        'ť' => 't',
        'ů' => 'u',
        'ž' => 'z',
        // Polish
        'Ą' => 'A',
        'Ć' => 'C',
        'Ę' => 'e',
        'Ł' => 'L',
        'Ń' => 'N',
        'Ó' => 'o',
        'Ś' => 'S',
        'Ź' => 'Z',
        'Ż' => 'Z',
        'ą' => 'a',
        'ć' => 'c',
        'ę' => 'e',
        'ł' => 'l',
        'ń' => 'n',
        'ó' => 'o',
        'ś' => 's',
        'ź' => 'z',
        'ż' => 'z',
        // Latvian
        'Ā' => 'A',
        'Č' => 'C',
        'Ē' => 'E',
        'Ģ' => 'G',
        'Ī' => 'i',
        'Ķ' => 'k',
        'Ļ' => 'L',
        'Ņ' => 'N',
        'Š' => 'S',
        'Ū' => 'u',
        'Ž' => 'Z',
        'ā' => 'a',
        'č' => 'c',
        'ē' => 'e',
        'ģ' => 'g',
        'ī' => 'i',
        'ķ' => 'k',
        'ļ' => 'l',
        'ņ' => 'n',
        'š' => 's',
        'ū' => 'u',
        'ž' => 'z'
    );
    // Make custom replacements
    $str      = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
    // Transliterate characters to ASCII
    if ($options['transliterate']) {
        $str = str_replace(array_keys($char_map), $char_map, $str);
    }
    // Replace non-alphanumeric characters with our delimiter
    $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
    // Remove duplicate delimiters
    $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
    // Truncate slug to max. characters
    $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
    // Remove delimiter from ends
    $str = trim($str, $options['delimiter']);
    return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
}
function br2nl($st) {
    $breaks = array(
        "<br />",
        "<br>",
        "<br/>"
    );
    return str_ireplace($breaks, "\r\n", $st);
}
function ToObject($array) {
    $object = new stdClass();
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $value = ToObject($value);
        }
        if (isset($value)) {
            $object->$key = $value;
        }
    }
    return $object;
}
function ToArray($obj) {
    if (is_object($obj))
        $obj = (array) $obj;
    if (is_array($obj)) {
        $new = array();
        foreach ($obj as $key => $val) {
            $new[$key] = ToArray($val);
        }
    } else {
        $new = $obj;
    }
    return $new;
}
function GetTerms() {
    global $mysqli;
    $data  = array();
    $query = mysqli_query($mysqli, "SELECT * FROM " . T_TERMS);
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $data[$fetched_data['type']] = $fetched_data['content'];
    }
    return $data;
}
function UpdateSeenReports() {
    global $mysqli;
    $query_one = " UPDATE " . T_REPORTS . " SET `seen` = 1 WHERE `seen` = 0";
    $sql       = mysqli_query($mysqli, $query_one);
    if ($sql) {
        return true;
    }
}
function secure($string, $censored_words = 1, $br = true) {
    global $mysqli;
    $string = trim($string);
    $string = mysqli_real_escape_string($mysqli, $string);
    $string = htmlspecialchars($string, ENT_QUOTES);
    if ($br == true) {
        $string = str_replace('\r\n', " <br>", $string);
        $string = str_replace('\n\r', " <br>", $string);
        $string = str_replace('\r', " <br>", $string);
        $string = str_replace('\n', " <br>", $string);
    } else {
        $string = str_replace('\r\n', "", $string);
        $string = str_replace('\n\r', "", $string);
        $string = str_replace('\r', "", $string);
        $string = str_replace('\n', "", $string);
    }
    $string = stripslashes($string);
    $string = str_replace('&amp;#', '&#', $string);
    $string = preg_replace("/{{(.*?)}}/", '', $string);
    if ($censored_words == 1) {
        global $config;
        $censored_words = @explode(",", $config['censored_words']);
        foreach ($censored_words as $censored_word) {
            $censored_word = trim($censored_word);
            $string        = str_replace($censored_word, '****', $string);
        }
    }
    return $string;
}
function isLogged() {
    if (isset($_POST['access_token'])) {
        $id = getUserFromSessionID($_POST['access_token'], 'mobile');
        if (is_numeric($id) && !empty($id)) {
            return true;
        }else{
            return false;
        }
    }

    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        $id = getUserFromSessionID($_SESSION['user_id']);
        if (is_numeric($id) && !empty($id)) {
            return true;
        }
    } 
    
    else if (isset($_COOKIE['user_id']) && !empty($_COOKIE['user_id'])) {
        $id = getUserFromSessionID($_COOKIE['user_id']);
        if (is_numeric($id) && !empty($id)) {
            return true;
        }
    }

    else {
        return false;
    }
}
function getUserFromSessionID($session_id, $platform = 'web') {
    global $db;
    if (empty($session_id)) {
        return false;
    }
    $platform   = secure($platform);
    $session_id = secure($session_id);
    $return     = $db->where('session_id', $session_id);
    $return     = $db->where('platform', $platform);
    return $db->getValue(T_SESSIONS, 'user_id');
}
function generateKey($minlength = 20, $maxlength = 20, $uselower = true, $useupper = true, $usenumbers = true, $usespecial = false) {
    $charset = '';
    if ($uselower) {
        $charset .= "abcdefghijklmnopqrstuvwxyz";
    }
    if ($useupper) {
        $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    }
    if ($usenumbers) {
        $charset .= "123456789";
    }
    if ($usespecial) {
        $charset .= "~@#$%^*()_+-={}|][";
    }
    if ($minlength > $maxlength) {
        $length = mt_rand($maxlength, $minlength);
    } else {
        $length = mt_rand($minlength, $maxlength);
    }
    $key = '';
    for ($i = 0; $i < $length; $i++) {
        $key .= $charset[(mt_rand(0, strlen($charset) - 1))];
    }
    return $key;
}
function resize_Crop_Image($max_width, $max_height, $source_file, $dst_dir, $quality = 80) {
    $imgsize = @getimagesize($source_file);
    $width   = $imgsize[0];
    $height  = $imgsize[1];
    $mime    = $imgsize['mime'];
    switch ($mime) {
        case 'image/gif':
            $image_create = "imagecreatefromgif";
            $image        = "imagegif";
            break;
        case 'image/png':
            $image_create = "imagecreatefrompng";
            $image        = "imagepng";
            break;
        case 'image/jpeg':
            $image_create = "imagecreatefromjpeg";
            $image        = "imagejpeg";
            break;
        default:
            return false;
            break;
    }
    $dst_img    = @imagecreatetruecolor($max_width, $max_height);
    $src_img    = $image_create($source_file);
    $width_new  = $height * $max_width / $max_height;
    $height_new = $width * $max_height / $max_width;
    if ($width_new > $width) {
        $h_point = (($height - $height_new) / 2);
        @imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
    } else {
        $w_point = (($width - $width_new) / 2);
        @imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
    }
    @imagejpeg($dst_img, $dst_dir, $quality);
    if ($dst_img)
        @imagedestroy($dst_img);
    if ($src_img)
        @imagedestroy($src_img);
}
function compressImage($source_url, $destination_url, $quality) {
    $info = getimagesize($source_url);
    if ($info['mime'] == 'image/jpeg') {
        $image = @imagecreatefromjpeg($source_url);
        @imagejpeg($image, $destination_url, $quality);
    } elseif ($info['mime'] == 'image/gif') {
        $image = @imagecreatefromgif($source_url);
        @imagegif($image, $destination_url, $quality);
    } elseif ($info['mime'] == 'image/png') {
        $image = @imagecreatefrompng($source_url);
        @imagepng($image, $destination_url);
    }
}
function time_Elapsed_String($ptime) {
    global $music;
    $etime = time() - $ptime;
    if ($etime < 45) {
        return lang('Just now');
    }
    if ($etime >= 45 && $etime < 90) {
        return lang('about a minute ago');
    }
    $day = 24 * 60 * 60;
    if ($etime > $day * 30 && $etime < $day * 45) {
        return lang('about a month ago');
    }
    $a        = array(
        365 * 24 * 60 * 60 => "year",
        30 * 24 * 60 * 60 => "month",
        24 * 60 * 60 => "day",
        60 * 60 => "hour",
        60 => "minute",
        1 => "second"
    );
    $a_plural = array(
        'year' => lang("years"),
        'month' => lang("months"),
        'day' => lang("days"),
        'hour' => lang("hours"),
        'minute' => lang("minutes"),
        'second' => lang("seconds")
    );
    foreach ($a as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r        = round($d);

            if ($music->language_type == 'rtl') {
                $time_ago = lang("ago") . ' ' . $r . ' ' . ($r > 1 ? $a_plural[$str] : $str);
            } else {
                $time_ago = $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ' . lang("ago");
            }

            return $time_ago;
        }
    }
}
function check_($check) {
    $siteurl = urlencode($_SERVER['SERVER_NAME']);
    $file    = file_get_contents('http://www.playtubescript.com/purchase.php?code=' . $check . '&url=' . $siteurl);
    $check   = json_decode($file, true);
    return $check;
}
function check_success($check) {
    $siteurl = urlencode($_SERVER['SERVER_NAME']);
    $file    = file_get_contents('http://www.playtubescript.com/purchase.php?code=' . $check . '&success=true&url=' . $siteurl);
    $check   = json_decode($file, true);
    return $check;
}
function PT_EditMarkup($text, $link = true) {
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
function markUp($text, $link = true) {
    if ($link == true) {
        $link_search = '/\[a\](.*?)\[\/a\]/i';
        if (preg_match_all($link_search, $text, $matches)) {
            foreach ($matches[1] as $match) {
                $match_decode     = urldecode($match);
                $match_decode_url = $match_decode;
                $count_url        = mb_strlen($match_decode);
                if ($count_url > 50) {
                    $match_decode_url = mb_substr($match_decode_url, 0, 30) . '....' . mb_substr($match_decode_url, 30, 20);
                }
                $match_url = $match_decode;
                if (!preg_match("/http(|s)\:\/\//", $match_decode)) {
                    $match_url = 'http://' . $match_url;
                }
                $text = str_replace('[a]' . $match . '[/a]', '<a href="' . strip_tags($match_url) . '" target="_blank" class="hash" rel="nofollow">' . $match_decode_url . '</a>', $text);
            }
        }
    }

    $link_search = '/\[img\](.*?)\[\/img\]/i';
    if (preg_match_all($link_search, $text, $matches)) {
        foreach ($matches[1] as $match) {
            $match_decode     = urldecode($match);
            $text = str_replace('[img]' . $match . '[/img]', '<a href="' . getMedia(strip_tags($match_decode)) . '" target="_blank"><img style="width:300px;border-radius: 20px;" src="' . getMedia(strip_tags($match_decode)) . '"></a>', $text);
        }
    }
    return $text;
}
function covtime($youtube_time) {
    $start = new DateTime('@0'); // Unix epoch
    $start->add(new DateInterval($youtube_time));
    return $start->format('H:i:s');
}
function PT_CreateSession() {
    $hash = sha1(rand(1111, 9999));
    if (!empty($_SESSION['hash_id'])) {
        $_SESSION['hash_id'] = $_SESSION['hash_id'];
        return $_SESSION['hash_id'];
    }
    $_SESSION['hash_id'] = $hash;
    return $hash;
}
function PT_ShortText($text = "", $len = 100) {
    if (empty($text) || !is_string($text) || !is_numeric($len) || $len < 1) {
        return "****";
    }
    if (strlen($text) > $len) {
        $text = mb_substr($text, 0, $len, "UTF-8") . "..";
    }
    return $text;
}
function PT_GetIdFromURL($url = false) {
    if (!$url) {
        return false;
    }
    $slug = @end(explode('_', $url));
    $id   = 0;
    $slug = explode('.', $slug);
    $id   = (is_array($slug) && !empty($slug[0]) && is_numeric($slug[0])) ? $slug[0] : 0;
    return $id;
}
function PT_Decode($text = '') {
    return htmlspecialchars_decode($text);
}
function PT_Backup($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, $tables = false, $backup_name = false) {
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
function size_format($bytes) {
    $size = array('2000000' => '2MB',
                  '6000000' => '6MB',
                  '12000000' => '12MB',
                  '24000000' => '24MB',
                  '48000000' => '48MB',
                  '96000000' => '96MB',
                  '256000000' => '256MB',
                  '512000000' => '512MB',
                  '1000000000' => '1GB',
                  '10000000000' => '10GB');
    return $size[$bytes];
}
// function pt_size_format($bytes) {
//     $kb = 1024;
//     $mb = $kb * 1024;
//     $gb = $mb * 1024;
//     $tb = $gb * 1024;
//     if (($bytes >= 0) && ($bytes < $kb)) {
//         return $bytes . ' B';
//     } elseif (($bytes >= $kb) && ($bytes < $mb)) {
//         return ceil($bytes / $kb) . ' KB';
//     } elseif (($bytes >= $mb) && ($bytes < $gb)) {
//         return ceil($bytes / $mb) . ' MB';
//     } elseif (($bytes >= $gb) && ($bytes < $tb)) {
//         return ceil($bytes / $gb) . ' GB';
//     } elseif ($bytes >= $tb) {
//         return ceil($bytes / $tb) . ' TB';
//     } else {
//         return $bytes . ' B';
//     }
// }
function pt_delete_field($id = false) {
    global $pt, $sqlConnect;
    if (IS_LOGGED == false || !PT_IsAdmin()) {
        return false;
    }
    $id    = PT_Secure($id);
    $table = T_FIELDS;
    $query = mysqli_query($sqlConnect, "DELETE FROM `$table` WHERE `id` = {$id}");
    if ($query) {
        $table  = T_USR_PROF_FIELDS;
        $query2 = mysqli_query($sqlConnect, "ALTER TABLE `$table` DROP `fid_{$id}`;");
        if ($query2) {
            return true;
        }
    }
    return false;
}
function pt_is_url($url = false) {
    if (empty($url)) {
        return false;
    }
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        return true;
    }
    return false;
}
function clear_cookies() {
    foreach ($_COOKIE as $key => $value) {
        setcookie($key, $value, time() - 10000, "/");
    }
}
function pt_url_domain($url) {
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
function pt_redirect($url) {
    header("Loacation: $url");
    exit();
}
function connect_to_url($url = '', $config = array()) {
    if (empty($url)) {
        return false;
    }
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
    if (!empty($config['POST'])) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $config['POST']);
    }
    if (!empty($config['bearer'])) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $config['bearer']
        ));
    }
    //execute the session
    $curl_response = curl_exec($curl);
    //finish off the session
    curl_close($curl);
    return $curl_response;
}
function verify_api_auth($user_id,$session_id, $platform = 'phone') {
    global $db;
    if (empty($session_id) || empty($user_id)) {
        return false;
    }
    $platform   = PT_Secure($platform);
    $session_id = PT_Secure($session_id);
    $user_id    = PT_Secure($user_id);

    $db->where('session_id', $session_id);
    $db->where('user_id', $user_id);
    $db->where('platform', $platform);
    return ($db->getValue(T_SESSIONS, 'COUNT(*)') == 1);
}
function pt_vrequest_exists(){
    global $db,$pt;
    if (!IS_LOGGED) {
        return false;
    }

    $user    = $pt->user->id;
    return ($db->where("user_id",$user)->getValue(T_VERIF_REQUESTS,"count(*)") > 0);
}
function pt_get_announcments() {
    global $pt, $db;
    if (IS_LOGGED === false) {
        return false;
    }

    $views_table  = T_ANNOUNCEMENT_VIEWS;
    $table        = T_ANNOUNCEMENTS;
    $user         = $pt->user->id;
    $subsql       = "SELECT `announcement_id` FROM `$views_table` WHERE `user_id` = '{$user}'";
    $fetched_data = $db->where(" `active` = '1' AND `id` NOT IN ({$subsql}) ")->orderBy('RAND()')->getOne(T_ANNOUNCEMENTS);
    return $fetched_data;
}
function pt_is_banned($ip_address = false){
    global $pt, $db;
    $table = T_BANNED_IPS;
    try {
        $ip    = $db->where('ip_address',$ip_address,'=')->getValue($table,"count(*)");
        return ($ip > 0);
    } catch (Exception $e) {
        return false;
    }
}
function pt_custom_design($a = false,$code = array()){
    global $pt;
    $theme       = $pt->config->theme;
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
function pt_notify($data = array()){
    global $pt, $db;
    if (empty($data) || !is_array($data)) {
        return false;
    }

    $t_notif = T_NOTIFICATIONS;
    $query   = $db->insert($t_notif,$data);
    return $query;
}
function pt_get_notification($args = array()){
    global $pt, $db;
    $options  = array(
        "recipient_id" => 0,
        "type" => null,
    );

    $args         = array_merge($options, $args);
    $recipient_id = $args['recipient_id'];
    $type         = $args['type'];
    $data         = array();
    $t_notif      = T_NOTIFICATIONS;

    $db->where('recipient_id',$recipient_id);
    if ($type == 'new') {
        $data = $db->where('seen',0)->getValue($t_notif,'count(*)');
    }

    else{
        $query      = $db->orderBy('id','DESC')->get($t_notif,20);
        foreach ($query as $notif_data_row) {
            $data[] = ToArray($notif_data_row);
        } 
    }

    $db->where('recipient_id',$recipient_id);
    $db->where('time',(time() - 432000));
    $db->where('seen',0,'>');
    $db->delete($t_notif);

    return $data;
}
function ffmpeg_duration($filename = false){
    global $pt;

    $ffmpeg_b = $pt->config->ffmpeg_binary_file;
    $output   = shell_exec("$ffmpeg_b -i {$filename} 2>&1");
    $ptrn     = '/Duration: ([0-9]{2}):([0-9]{2}):([^ ,])+/';
    $time     = 30;
    if (preg_match($ptrn, $output, $matches)) {
        $time = str_replace("Duration: ", "", $matches[0]);
        $time_breakdown = explode(":", $time);
        $time = round(($time_breakdown[0]*60*60) + ($time_breakdown[1]*60) + $time_breakdown[2]);
    }

    return $time;
}
function http_respond($data = array()) {
    if (is_callable('fastcgi_finish_request')) {
        session_write_close();
        fastcgi_finish_request();
        return;
    }

    ignore_user_abort(true);
    ob_start();
    $serverProtocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING);
    header($serverProtocol . ' 200 OK');
    header('Content-Encoding: none');
    header('Content-Length: ' . ob_get_length());
    header('Connection: close');
    ob_end_flush();
    ob_flush();
    flush();
}
function get_ip_address() {
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
            $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($iplist as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP))
                    return $ip;
            }
        } else {
            if (filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED']) && filter_var($_SERVER['HTTP_X_FORWARDED'], FILTER_VALIDATE_IP))
        return $_SERVER['HTTP_X_FORWARDED'];
    if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && filter_var($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'], FILTER_VALIDATE_IP))
        return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP))
        return $_SERVER['HTTP_FORWARDED_FOR'];
    if (!empty($_SERVER['HTTP_FORWARDED']) && filter_var($_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP))
        return $_SERVER['HTTP_FORWARDED'];
    return $_SERVER['REMOTE_ADDR'];
}
function thousandsCurrencyFormat($num) {

  if($num>1000) {

        $x = round($num);
        $x_number_format = number_format($x);
        $x_array = explode(',', $x_number_format);
        $x_parts = array('K', 'M', 'B', 'T');
        $x_count_parts = count($x_array) - 1;
        $x_display = $x;
        $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];

        return $x_display;

  }

  return $num;
}
function db_langs() {
    global $music, $db;
    $data   = array();
    $t_lang = T_LANGS;
    $query  = $db->rawQuery("DESCRIBE `$t_lang`");
    foreach ($query as $column) {
        $data[] = $column->Field;
    }

    unset($data[0]);
    unset($data[1]);
    unset($data[2]);
    unset($data[3]);

    return $data;
}
function get_langs($lang = 'english') {
    global $music, $db;
    $data   = array();
    $t_lang = T_LANGS;
    try {
        $query  = $db->rawQuery("SELECT `lang_key`, `$lang` FROM `$t_lang`");
    } catch (Exception $e) {

    }

    foreach ($query as $item) {
        $data[$item->lang_key] = $item->$lang;
    }

    return $data;
}
function PT_Duration($text) {
    $duration_search = '/\[d\](.*?)\[\/d\]/i';

    if (preg_match_all($duration_search, $text, $matches)) {
        foreach ($matches[1] as $match) {
            $time = explode(":", $match);
            $current_time = ($time[0]*60)+$time[1];
            $text = str_replace('[d]' . $match . '[/d]', '<a  class="hash" href="javascript:void(0)" onclick="go_to_duration('.$current_time.')">' . $match . '</a>', $text);
        }
    }
    return $text;
}
function getPageFromPath($path = '') {
    if (empty($path)) {
        return false;
    }
    $path = explode("/", $path);
    $data = array();
    $data['options'] = array();
    if (!empty($path[0])) {
        $data['page'] = $path[0];
    }
    if (!empty($path[1])) {
        unset($path[0]);
        $data['options'] = $path;
    }
    return $data;
}
function getPageFromPathAdmin($path = '') {
    if (empty($path)) {
        return false;
    }
    $path = explode("&", $path);
    $data = array();
    $data['options'] = array();
    if (!empty($path[0])) {
        $data['page'] = $path[0];
    }
    if (!empty($path[1])) {
        unset($path[0]);
        $data['options'] = $path;
        foreach ($path as $key => $value) {
            preg_match_all('/(.*)=(.*)/m', $value, $matches);
            if (!empty($matches) && !empty($matches[1]) && !empty($matches[1][0]) && !empty($matches[2]) && !empty($matches[2][0])) {
                $_GET[$matches[1][0]] = $matches[2][0];
            }
            
        }
    }
    return $data;
}
function formatSeconds($str_time) {
    sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
    return isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
}


function CreatePayment($data){
    global $db;
    if(empty($data)){
        return false;
    }

    return $db->insert(T_PAYMENTS, $data);
}
function TrackPurchaseData() {
    global $sqlConnect;
    $type_table   = T_PAYMENTS;
    $query_one    = mysqli_query($sqlConnect, "SELECT SUM(`amount`) as count FROM {$type_table} WHERE `type` = 'TRACK' AND `amount` <> 0 AND YEAR(`date`) = '".date("Y")."' AND MONTH(`date`) = '".date('n')."'");
    $fetched_data = mysqli_fetch_assoc($query_one);
    return $fetched_data['count'];
}
function CountAllPaymentData($type) {
    global $sqlConnect;
    $type_table   = T_PAYMENTS;
    $type         = Secure($type);
    $query_one    = mysqli_query($sqlConnect, "SELECT COUNT(`id`) as count FROM {$type_table} WHERE `pro_plan` = '{$type}'");
    $fetched_data = mysqli_fetch_assoc($query_one);
    return $fetched_data['count'];
}
function AmountAllPaymentData($type) {
    global $sqlConnect;
    $type_table   = T_PAYMENTS;
    $type         = Secure($type);
    $query_one    = mysqli_query($sqlConnect, "SELECT SUM(`amount`) as count FROM {$type_table} WHERE `pro_plan` = '{$type}'");
    $fetched_data = mysqli_fetch_assoc($query_one);
    return $fetched_data['count'];
}
function CountAllPayment() {
    global $sqlConnect;
    $type_table = T_PAYMENTS;
    $query_one  = mysqli_query($sqlConnect, "SELECT `amount` FROM {$type_table}");
    $final_data = 0;
    while ($fetched_data = mysqli_fetch_assoc($query_one)) {
        $final_data += $fetched_data['amount'];
    }
    return $final_data;
}
function CountThisMonthPayment() {
    global $sqlConnect;
    $type_table = T_PAYMENTS;
    $date       = date('n') . '/' . date("Y");
    $query_one  = mysqli_query($sqlConnect, "SELECT `amount` FROM {$type_table} WHERE `amount` <> 0 AND YEAR(`date`) = '".date("Y")."' AND MONTH(`date`) = '".date('n')."'");
    $final_data = 0;
    while ($fetched_data = mysqli_fetch_assoc($query_one)) {
        $final_data += $fetched_data['amount'];
    }
    return $final_data;
}
function GetRegisteredPaymentsStatics($month, $type = '') {
    global $sqlConnect;
    $year         = date("Y");
    $type_table   = T_PAYMENTS;
    $query_one    = mysqli_query($sqlConnect, "SELECT SUM(`amount`) as count FROM {$type_table} WHERE YEAR(`date`) = '".$year."' AND MONTH(`date`) = '".$month."' AND `pro_plan` = '{$type}'");
    $fetched_data = mysqli_fetch_assoc($query_one);
    return (float)$fetched_data['count'];
}
function GetTrackPaymentsStatics($month) {
    global $sqlConnect;
    $year         = date("Y");
    $type_table   = T_PAYMENTS;
    $query_one    = mysqli_query($sqlConnect, "SELECT SUM(`amount`) as count FROM {$type_table} WHERE YEAR(`date`) = '".$year."' AND MONTH(`date`) = '".$month."' AND `type` = 'TRACK'");
    $fetched_data = mysqli_fetch_assoc($query_one);
    return (float)$fetched_data['count'];
}
function ip_in_range($ip, $range) {
    if (strpos($range, '/') == false) {
        $range .= '/32';
    }
    // $range is in IP/CIDR format eg 127.0.0.1/24
    list($range, $netmask) = explode('/', $range, 2);
    $range_decimal    = ip2long($range);
    $ip_decimal       = ip2long($ip);
    $wildcard_decimal = pow(2, (32 - $netmask)) - 1;
    $netmask_decimal  = ~$wildcard_decimal;
    return (($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal));
}


function RecordUserActivities($activity, $obj = array()){
    global $music,$config,$db;
    $update = false;
    $activities = array(
        'comment',                  //done
        'upload',                   //done
        'replay_comment',           //done
        'like_track',               //done
        'dislike_track',            //done
        'like_comment',             //done
        'like_blog_comment',        //done
        'unlike_comment',           //done
        'unlike_blog_comment',      //done
        'repost',                   //done
        'track_download',           //done
        'import',                   //done
        'purchase_track',           //done
        'go_pro',                   //done
        'review_track',             //done
        'report_track',             //done
        'report_comment',           //done
        'add_to_playlist',          //done
        'create_new_playlist',      //done
        'update_profile_picture',   //done
        'update_profile_cover',     //done
    );
    if(isLogged() == false) return false;
    if(empty($obj)) return false;
    if($config['point_system'] === 'off') return false;
    if(empty($activity)) return false;
    if(!in_array($activity, $activities) || !isset($config['point_system_' . $activity . '_cost'])) return false;
    $_cost = intval($config['point_system_' . $activity . '_cost']);
    $_add_wallet = true;

    if($activity === 'update_profile_picture' || $activity === 'update_profile_cover'){
        $is_exist = $db->where('action' , $activity)->where('user_id',$music->user->id)->getOne(T_POINT_SYSTEM);
        if (!empty($is_exist)) return false;
    }

    if($activity === 'comment' || $activity === 'repost' || $activity === 'review_track' || $activity === 'report_track' ){
        if($obj['track_user_id'] === $music->user->id) return false;
    }
    if($activity === 'replay_comment' || $activity === 'like_comment' || $activity === 'report_comment'){
        if($obj['track_user_id'] === $music->user->id) return false;
        if($obj['comment_user_id'] === $music->user->id) return false;
    }
    if($activity === 'like_track' || $activity === 'dislike_track'){
        if($obj['track_user_id'] === $music->user->id) return false;
    }
    if($activity === 'unlike_comment' || $activity === 'unlike_blog_comment'){
        if($obj['track_user_id'] === $music->user->id && $activity === 'unlike_comment') return false;
        $_add_wallet = false;
    }
    $wallet_cost = $_cost * $music->config->point_system_points_to_dollar;
    if($_add_wallet) {
        $update = $db->where('id', $music->user->id)->update(T_USERS, array('wallet' => $db->inc($wallet_cost)));
    }else{
        $update = $db->where('id', $music->user->id)->update(T_USERS, array('wallet' => $db->dec($wallet_cost)));
    }
    if ($update) {
        $db->insert(T_POINT_SYSTEM,array(
            'user_id' => $music->user->id,
            'action' => $activity,
            'reword' => $_cost,
            'is_add' => ($_add_wallet) ? 1 : 0,
            'obj' => serialize($obj),
            'time' => time()
        ));
        return true;
    }else{
        return false;
    }
}
function GetPointEarned(){
    global $db,$music;
    $earned = 0;
    $points = $db->where('user_id', $music->user->id)->get(T_POINT_SYSTEM,null,array('*'));
    foreach($points as $key => $value){
        if($value->is_add == 1){
            $earned = $earned + $value->reword;
        }else{
            $earned = $earned - $value->reword;
        }
    }
    return ($earned > 0) ? $earned : '00.00';
}
function GetWalletReworded(){
    global $music;
    $points = GetPointEarned() * $music->config->point_system_points_to_dollar;
    return ($points > 0) ? $points : '00.00';
}