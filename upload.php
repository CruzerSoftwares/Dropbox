<?php if(!session_id()) session_start();
    
    //include config and core libraries
    require 'config.php';
    require 'helpers.php';
    require 'dropbox.php';

    $dropbox = new CruzerSoftwares\Dropbox(TOKEN);

    $profileData = $dropbox->getProfile();
    $profile     = json_decode($profileData);

    if(isset($_POST) && isset($_POST['submit'])){
        if($_SESSION['csrf_token'] != $_POST['_token']){
            $_SESSION['response'] = json_decode(json_encode(array('error' => 'invalid token')));
            redirect('upload.php');
        }

        if( $_POST['submit'] == 'savefile'){
            $response = uploadFile($dropbox,'/test', 'overwrite');
            $_SESSION['response'] = json_decode($response);
            redirect('upload.php');
        }
    }

    $_SESSION['csrf_token'] = substr(md5(mt_rand(10000,98888).time()),0,20);
?>

<!doctype html>
<html class="no-js" lang="en-GB">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Dropbox File Upload API</title>
        <meta name="description" content="Upload files to Dropbox Account">
        <meta name="keywords" content="Upload files to Dropbox Account">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="style.css">
        <base href="<?php echo SITE_URL;?>/">
    </head>
<body>
    <div class="container">
        <?php if(isset($profile) && isset($profile->email)){
            echo '<div class="profile"><img src="'.$profile->profile_photo_url.'">Using Account:<br/>';
            echo '<p>'.$profile->name->display_name.' <br/>'.$profile->email.'</p></div>';
            echo '<div class="navigation"><ul><li><a href="list.php">List</a></li><li><a href="upload.php" class="active">Upload</a></li></div>';
        }?>
        <h1>Upload Files to Dropbox</h1>
        <?php if(isset($_SESSION['response'])){
            echo '<table class="result" border="1" cellspacing="1" cellpadding="6">';
            $res = $_SESSION['response'];

            if(isset($res->path_display)){
                if(isset($res->name)){
                    echo '<tr><th>Uploaded File</th> <td>'.$res->name."</td></tr>";
                }
                if(isset($res->size)){
                    echo '<tr><th>File Size</th> <td> '.$res->size."</td></tr>";
                }
                if(isset($res->path_display)){
                    echo '<tr><th>Uploaded on Path</th> <td> '.$res->path_display."</td></tr>";
                }
                if(isset($res->id)){
                    echo '<tr><th>Uploaded File ID</th> <td> '.$res->id."</td></tr>";
                }
                if(isset($res->client_modified)){
                    echo '<tr><th>Modified</th> <td> '.$res->client_modified."</td></tr>";
                }
            } else{
                if(isset($res->error)){
                    echo '<tr><th>Uploaded Error</th> <td> '.$res->error."</td></tr>";
                } else{
                    print_r($res);
                }
            }
            echo '</table>';
            unset($_SESSION['response']);
        }?>
        
        <form method="post" enctype="multipart/form-data" class="frm">
            <p><i><b>Note:</b> Files will be uploaded to /test folder</i></p>
            <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token'];?>">
            <label>File : </label>
            <input type="file" name="file">
            <button type="submit" name="submit" value="savefile">Submit</button>
            <p>
                <a href="list.php?folder=">See all files</a>
            </p>
        </form>
    </div>
</body>
</html>

