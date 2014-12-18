<?php
 /**
 * @package			JFBAlbum 
 * @version			2.5.0
 *
 * @author			Md. Afzal Hossain <afzal.csedu@gmail.com>
 * @link			http://www.srizon.com
 * @copyright		Copyright 2012 Md. Afzal Hossain All Rights Reserved
 * @license			GNU General Public License version 2 or later
 */
 class SrzFBMycurl {
     var $_useragent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11';
     var $_url;
     var $_followlocation;
     var $_timeout;
     var $_maxRedirects;
     var $_cookieFileLocation = './cookie.txt';
     var $_post;
     var $_postFields;
     var $_referer ="http://www.google.com";

     var $_session;
     var $_webpage;
     var $_includeHeader;
     var $_noBody;
     var $_status;
     var $_binaryTransfer;
     var $authentication = 0;
     var $auth_name      = '';
     var $auth_pass      = '';

     function useAuth($use){
       $this->authentication = 0;
       if($use == true) $this->authentication = 1;
     }

     function setName($name){
       $this->auth_name = $name;
     }
     function setPass($pass){
       $this->auth_pass = $pass;
     }

     function SrzFBMycurl($url,$followlocation = true,$timeOut = 60,$maxRedirecs = 4,$binaryTransfer = false,$includeHeader = false,$noBody = false)
     {
         $this->_url = $url;
         $this->_followlocation = $followlocation;
         $this->_timeout = $timeOut;
         $this->_maxRedirects = $maxRedirecs;
         $this->_noBody = $noBody;
         $this->_includeHeader = $includeHeader;
         $this->_binaryTransfer = $binaryTransfer;

         $this->_cookieFileLocation = dirname(__FILE__).'/cookie.txt';

     }

     function setReferer($referer){
       $this->_referer = $referer;
     }

     function setCookiFileLocation($path)
     {
         $this->_cookieFileLocation = $path;
     }

     function setPost ($postFields)
     {
        $this->_post = true;
        $this->_postFields = $postFields;
     }

     function setUserAgent($userAgent)
     {
         $this->_useragent = $userAgent;
     }

     function createCurl($url = 'nul')
     {
        if($url != 'nul'){
          $this->_url = $url;
        }

         $s = curl_init();

         curl_setopt($s,CURLOPT_URL,$this->_url);
         curl_setopt($s,CURLOPT_HTTPHEADER,array('Expect:'));
         curl_setopt($s,CURLOPT_TIMEOUT,$this->_timeout);
         curl_setopt($s,CURLOPT_MAXREDIRS,$this->_maxRedirects);
         curl_setopt($s,CURLOPT_RETURNTRANSFER,true);
         if($this->_cookieFileLocation){
         curl_setopt($s,CURLOPT_COOKIEJAR,$this->_cookieFileLocation);
         curl_setopt($s,CURLOPT_COOKIEFILE,$this->_cookieFileLocation);
         }

         if($this->authentication == 1){
           curl_setopt($s, CURLOPT_USERPWD, $this->auth_name.':'.$this->auth_pass);
         }
         if($this->_post)
         {
             curl_setopt($s,CURLOPT_POST,true);
             curl_setopt($s,CURLOPT_POSTFIELDS,$this->_postFields);

         }

         if($this->_includeHeader)
         {
               curl_setopt($s,CURLOPT_HEADER,true);
         }

         if($this->_noBody)
         {
             curl_setopt($s,CURLOPT_NOBODY,true);
         }
         if($this->_useragent){
         curl_setopt($s,CURLOPT_USERAGENT,$this->_useragent);
         }
         if($this->_referer){
         curl_setopt($s,CURLOPT_REFERER,$this->_referer);
         }

         $this->_webpage = curl_exec($s);
                   $this->_status = curl_getinfo($s,CURLINFO_HTTP_CODE);
         curl_close($s);

     }

   function getHttpStatus()
   {
       return $this->_status;
   }

   function tostring(){
      return $this->_webpage;
   }
}