<?php
$records     = 0;
$perpage     = 10;
$search      = '';
$category    = null;
$html_list   = '';
$current_tag   = '';

$music->site_pagename = "blogs";
if (!empty($path['options'][1])) {
    $arr = explode("_",$path['options'][1]);
    if( isset($arr[0]) && $arr[0] > 0 ){
        $category = Secure((int)$arr[0]);
    }
    $music->site_pagename = $path['options'][1];
}
if( !empty($category) ) {

    if ($db->where('ref', 'blog_categories')->where('lang_key', $category)->getValue(T_LANGS, 'id') === NULL) {
        header("Location: $site_url/404");
        exit();
    }
}
if( !empty($path['options'][1]) && $path['options'][1] == 'tag'){
    $keyword = Secure($path['options'][2]);
    $search = ' AND ( `title` LIKE \'%'.$keyword.'%\' OR `content` LIKE \'%'.$keyword.'%\' OR `description` LIKE \'%'.$keyword.'%\' OR `tags` LIKE \'%'.$keyword.'%\' )';
    $music->site_pagename = $path['options'][1] . '/' . $path['options'][2];
    $current_tag   = $keyword;
}
$sql = 'SELECT * FROM `'.T_BLOG.'` WHERE `posted` = 1 ';
if( !empty($category) ){
    $sql .= ' AND `category` = '. $category;
} else if( !empty($path['options'][1]) && $path['options'][1] == 'tag' ){
    $sql .= $search;
}
$sql .= ' ORDER BY `created_at` DESC, `id` DESC LIMIT '.$perpage.';';
$articles            = $db->objectBuilder()->rawQuery($sql);

//if( !empty($path['options'][1]) && $path['options'][1] == 'article') {
//    if (empty($articles)) {
//        var_dump($articles);
//        exit();
//    }
//}

if (!empty($articles)) {
    $records = count($articles);
    foreach ($articles as $key => $art) {
        $articleData = GetArticle($art->id);
        $html_list .= loadPage('blogs/article-list', [
            'thumbnail' => getMedia($articleData['thumbnail']),
            'id' => $articleData['id'],
            'title' => $articleData['title'],
            'content' => $articleData['content'],
            'description' => $articleData['description'],
            'posted' => $articleData['posted'],
            'category' => $articleData['category'],
            'category_text' => lang($articleData['category']),
            'view' => $articleData['view'],
            'shared' => $articleData['shared'],
            'tags' => $articleData['tags'],
            'created_at' => time_Elapsed_String($articleData['created_at']),
            'key' => ($key + 1)
        ]);
    }
}

$music->blog_categories = blog_categories();
$data = [];
$data['filters'] = loadPage('blogs/filters', ['active_category' => $category]);
$data['html_content'] = $html_list;
$data['records'] = $records;
$data['active_category'] = $category;
$data['current_tag'] = $current_tag;

$music->site_title = lang("Blogs");
$music->site_description = $music->config->description;
$music->site_content = loadPage("blogs/content", $data);