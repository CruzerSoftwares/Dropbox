<?php if(!session_id()) session_start();
    
    //include config and core libraries
    require 'config.php';
    require 'helpers.php';
    require 'dropbox.php';
    
    if(isset($_GET['back'])){
        $arr = explode('/', ltrim($_GET['back'],'/'));
        array_pop($arr);
        $base = '';
        if(count($arr)) $base = '/';
        redirect('list.php?folder='.$base.implode('/',$arr));
    }
    if(isset($_GET['folder']) && $_GET['folder']=='/'){
        redirect('list.php?folder=');
    }

    if(!isset($_GET['folder'])){
        redirect('list.php?folder=');
    }

    $dropbox = new CruzerSoftwares\Dropbox(TOKEN);

    $profileData = $dropbox->getProfile();
    $profile     = json_decode($profileData);

    $_SESSION['response'] = json_decode(listFile($_GET['folder'], $dropbox));
?>

<!doctype html>
<html class="no-js" lang="en-GB">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Dropbox List Files API</title>
        <meta name="description" content="List files from Dropbox Account">
        <meta name="keywords" content="List files from Dropbox Account">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="style.css">
        <base href="<?php echo SITE_URL;?>/">
    </head>
<body>
    <div class="container">
        <?php if(isset($profile) && isset($profile->email)){
            echo '<div class="profile"><img src="'.$profile->profile_photo_url.'">Using Account:<br/>';
            echo '<p>'.$profile->name->display_name.' <br/>'.$profile->email.'</p></div>';
            echo '<div class="navigation"><ul><li><a href="list.php" class="active">List</a></li><li><a href="upload.php">Upload</a></li></div>';
        }?>
        
        <h1>List Files of Dropbox folder</h1>
        <?php if(isset($_SESSION['response'])){
            if($_GET['folder']!=''){
                echo '<a href="list.php?back='.$_GET['folder'].'">&laquo; Back</a><br/>';
            }
            echo '<table class="result" border="1" cellspacing="1" cellpadding="6">';
            $res = $_SESSION['response'];

            if(isset($res->entries)){
                if($_GET['folder']!=''){
                    unset($res->entries[0]);
                }
                echo '<tr><th>Name</th><th>Modified</th></tr>';
                foreach ($res->entries as $entry) {
                    $nn = '.tag';
                    if($entry->{$nn}=='folder'){
                        if( strpos( ltrim(str_replace($_GET['folder'], '', $entry->path_display),'/'),'/')=== false){
                            echo '<tr><td><a href="list.php?folder='.$entry->path_display.'">'.$entry->name.'</a></td><td>--</td></tr>';
                        }
                    } else{
                        if( strpos( ltrim(str_replace($_GET['folder'], '', $entry->path_display),'/'),'/')=== false){
                            $d = $dropbox->getThumbnail($entry->path_display);
                            file_put_contents('thumb/'.$entry->name, $d);
                            echo '<tr><td><img src="thumb/'.$entry->name.'"/>'.$entry->name.'</td><td>'.$entry->client_modified.'</td></tr>';
                        }
                    }
                }
            } else{
                if(isset($res->error)){
                    echo '<tr><td>Error</td><td> - '.$res->error."</td></tr>";
                } else{
                    print_r($res);
                }
            }
            echo '</table>';
            unset($_SESSION['response']);
        }?>
        
        <form method="get" class="frm">
            <label>Folder : </label>
            <input type="text" name="folder" value="<?php echo $_GET['folder'];?>">
            <button type="submit">Go</button>
        </form>
    </div>
</body>
</html>

