<?php

/**
 * Includes helper functions to run this demo application
 *
 * @category   Dropbox
 * @package    DropboxAPI
 * @author     RN Kushwaha <Rn.kushwaha022@gmail.com>
 * @copyright  2017-2018 Cruzer Softwares
 * @license    The MIT License (MIT)
 * @version    0.0.1
 * @link       https://github.com/CruzerSoftwares/DropboxAPI
 * @since      0.0.1
 */

if(!function_exists('pr')){
    function pr($ar=array()){
        echo '<pre>';
        if(is_array($ar)) print_r($ar);
        elseif(is_object($ar)) print_r($ar);
        else print_r(json_decode($ar));
        echo '</pre>';
    }
}

if(!function_exists('listFile')){
    function listFile( $target_path, $dropbox ){
        $res = $dropbox->listFolder( $target_path);
        return $res;
    }
}

if(!function_exists('redirect')){
    function redirect( $target = ''){
        header('Location: '.$target);exit();
    }
}

if(!function_exists('uploadFile')){
    function uploadFile( $dropbox, $target_path, $mode='add' ){
        if(!isset($_FILES['file'])){
            return json_encode(array( 'error' => 'Please upload a file.'));
        }

        $errors    = array();
        $file_name = $_FILES['file']['name'];
        $file_size = $_FILES['file']['size'];
        $file_tmp  = $_FILES['file']['tmp_name'];

        if($file_size > MAX_UPLOAD_FILE_SIZE){
            return json_encode(array( 'error' => 'File size is larger than allowed size.'));
        }

        $target_path = $target_path.'/'.$file_name;
        $res = $dropbox->upload( $target_path, $file_tmp, true, $mode);
        return $res;
    }
}