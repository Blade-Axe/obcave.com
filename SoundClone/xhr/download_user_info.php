<?php 
$data['status'] = 200;
if(!empty($music->user->info_file)){
   // Get parameters
   $file = $music->user->info_file;
   $filepath = $file; // upload/files/2019/20/adsoasdhalsdkjalsdjalksd.html

   // Process download
   if(file_exists($filepath)) {
       header('Content-Description: File Transfer');
       header('Content-Type: application/octet-stream');
       // rename the file to username
       header('Content-Disposition: attachment; filename="'.$music->user->username.'.html"');
       header('Expires: 0');
       header('Cache-Control: must-revalidate');
       header('Pragma: public');
       header('Content-Length: ' . filesize($filepath));
       flush(); // Flush system output buffer
       readfile($filepath);
       // delete the file
       unlink($filepath);
       // remove user data
      $db->where('id', $music->user->id)->update(T_USERS, array(
                'info_file' => ''
            ));
       exit;
   }
}
header("Content-type: application/json");
echo json_encode($data);
exit();
