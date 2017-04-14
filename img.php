<?php
    $name = $_GET['name'];
    $mimes = array
    (
        'jpg' => 'image/jpg',
        'jpeg' => 'image/jpg',
        'gif' => 'image/gif',
        'png' => 'image/png'
    );

    $ext = strtolower(end(explode('.', $name)));

    $file = sys_get_temp_dir().'/'.$name;
    header('content-type: '. $mimes[$ext]);
    header('content-disposition: inline; filename="'.$name.'";');
    readfile($file);
?>