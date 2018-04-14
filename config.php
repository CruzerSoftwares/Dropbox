<?php

/**
 * Includes Dropbox API TOKEN
 *
 * The configuration for using the dropbox account to upload/delete/modify files
 *
 * to Generate a token go to https://www.dropbox.com/developers/apps/create
 * and creat an app then generate token
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

if(!defined('TOKEN')){
    define('TOKEN', 'enter-your-dropbox-token-here');
}

if(!defined('MAX_UPLOAD_FILE_SIZE')){
    define('MAX_UPLOAD_FILE_SIZE', 2097152);
}

if(!defined('SITE_URL')){
    define('SITE_URL', 'http://'.$_SERVER['HTTP_HOST'].DIRECTORY_SEPARATOR.basename(__DIR__));
}