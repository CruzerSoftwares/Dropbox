<?php

/**
 * Core API functions to call dropbox end points
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

/**
 * Namespace in which our API will be served from
 */
namespace CruzerSoftwares;

/**
 * Main library for calling Dropbox end points
 *
 * @category   Dropbox
 * @package    DropboxAPI
 * @author     RN Kushwaha <Rn.kushwaha022@gmail.com>
 * @since      0.0.1
 */

class Dropbox {

    /**
     * Dropbox Upload end point
     * @var string
     * @access private
     */
    private $uploadEndPoint    = 'https://content.dropboxapi.com/2/files/upload';

    /**
     * Dropbox list files end point
     * @var string
     * @access private
     */
    private $listEndPoint      = 'https://api.dropboxapi.com/2/files/list_folder';

    /**
     * Dropbox get thumbnail of a file end point
     * @var string
     * @access private
     */
    private $thumbnailEndPoint = 'https://content.dropboxapi.com/2/files/get_thumbnail';

    /**
     * Dropbox user profile info end point of the token generator
     * @var string
     * @access private
     */
    private $profileEndPoint   = 'https://api.dropboxapi.com/2/users/get_current_account';

    /**
     * Dropbox token
     * @var string
     * @access private
     */
    private $token;
    
    /**
     * constructor to assign token to the API
     * @param string $token Dropbox API token
     */
    public function __construct($token){
        $this->token = $token;
    }

    /**
     * It hits the Dropbox end point via CURL
     * 
     * @param  string $endpoint the Dropbox endpoint to call
     * @param  array $headers  headers to send with the call
     * @param  json $data     data to be send in JSON format
     * @return json $response Dropbox Response
     */
    public function hitCurl($endpoint, $headers, $data){
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);

        if(curl_error($ch)) {
            return curl_error($ch);
        }

        curl_close($ch);
        return $response;
    }

    /**
     * Get Profile Information of Token Generator
     * 
     * @return json response
     */
    public function getProfile(){
        $headers = array(
            "Content-Type: application/json"
        );

        array_push($headers, "Authorization: Bearer ".$this->token);
        return $this->hitCurl($this->profileEndPoint, $headers,array());
    }

    /**
     * [upload description]
     * @param  string  $target_path Path where we want to upload a file
     * @param  string  $source      Path of the source file
     * @param  boolean $is_file     If we are uploading a file or folder
     * @param  string  $mode        Upload Mode e.g. add
     * @return json Response
     */
    public function upload( $target_path, $source, $is_file = true, $mode = 'add'){
        $data = $source;
        if ($is_file === true ){
            if (file_exists($source)){
                $data = file_get_contents($source);
            } else {
                return json_encode([ 'error' => 'Source file does not exists']);
            }
        }
        
        $headers = array(
            "Content-Type: application/octet-stream",
            "Dropbox-API-Arg: {\"path\": \"$target_path\", \"mode\": \"$mode\"}"       
        );

        try{
            $response = $this->sendFile($headers, $data);
            $resJson = json_decode($response);
            if(isset($resJson->error) || isset($resJson->path_display)){
                return $response;
            } else{
                return json_encode([ 'error' => $response]);
            }
        } catch(Exception $e){
            return json_encode([ 'error' => $e->getMessage()]);
        }
    }

    /**
     * Shows all files and folders of specified path
     * 
     * @param  string $target_path The path of folder we want to be listed
     * @return json  Response
     */
    public function listFolder( $target_path ){
        $headers = array(
            "Content-Type: application/json"
        );

        array_push($headers, "Authorization: Bearer ".$this->token);

        $options = [
            'path' => $target_path,
            'recursive' => true
        ];

        try{
            $response = $this->hitCurl($this->listEndPoint, $headers, json_encode($options));
            $resJson = json_decode($response);
            if(isset($resJson->entries)){
                return $response;
            } else{
                return json_encode([ 'error' => $response]);
            }
        } catch(Exception $e){
            return json_encode([ 'error' => $e->getMessage()]);
        }
    } 

    /**
     * Get Thumbnail of any image file
     * 
     * @param  string $target_path The path of the source file
     * @param  string $format      The format in which we want the thumbnail
     * @param  string $size        The size of the thumbnail
     * @param  string $mode        The mode of files e.g. strict
     * @return json Response
     */
    public function getThumbnail( $target_path, $format='jpeg', $size='w64h64', $mode='strict' ){
        $headers = array(
            "Content-Type: text/plain",
            "Dropbox-API-Arg: {\"path\": \"$target_path\",\"format\": \"$format\",\"size\": \"$size\",\"mode\": \"$mode\"}"
        );

        array_push($headers, "Authorization: Bearer ".$this->token);
        
        try{
            $response = $this->hitCurl($this->thumbnailEndPoint, $headers, '');
            return $response;
        } catch(Exception $e){
            return json_encode([ 'error' => $e->getMessage()]);
        }
    }

    /**
     * Sends the actual file to Dropbox
     * 
     * Called internally to upload files to the Dropbox
     * @param  array $headers  headers to send with the call
     * @param  data to be send in JSON format
     * @return json Response
     */
    public function sendFile($headers, $data){
        array_push($headers, "Authorization: Bearer ".$this->token);
        return $this->hitCurl($this->uploadEndPoint, $headers, $data);
    }

}
