<?php
 
// Class LsmCryptsession
// Autor : Claudio Adonai Muto
// WebSite : http://www.cam.pro.br
// Usage : Crypt and Decrypt datas on sessions
// 
//
// $cook->new LsmCryptSession(43532) ;
// The parameter must be a integer, 123 is default value.
//
// $sess->_setSession("nome","Lucas Sabbag Muto")
// It saves Lucas Sabbag Muto on session 'nome'. The value will be
// saved encrypted.
//
// $my_value=$sess->_getSession("nome") ;
// It recovers the decrypted value of session 'nome' on my_value
//
// PS : LSM is a tribute to my son, Lucas Sabbag Muto
//
// I joined AZDCryptClass with this class.
// Crypt :
// The string is crypted by encryptsession method and in each letter of
// this new string is crypted by encrypt method. It stores an array on
// session variable.
// Decrypt :
// The sesion variable is decrypted by decryptsession method and in each letter of
// this new string is decrypted by decrypt method. It returns
// the original value. 
// 
// PS : You can change $my_key2 atribute in class. This atribute represents the key
// that will recrypt data.
 
 
  class LsmCryptSession {
 
	var $my_key ; 
	var $my_key2 ; 
	var $my_session ; 
	var $my_value ; 
	var $sessionarray ;
	var $session ;
 
	function LsmCryptSession($key = 123) {
	  $this->my_key=$key ;
	  $this->my_key2=01011976 ;
	  $this->session= array() ;
	  $this->my_session="" ;
	  $this->my_value="" ;
	}
 
   function ed($t) { 
	  $r = md5($this->my_key); 
	  $c=0; 
	  $v = ""; 
	  for ($i=0;$i<strlen($t);$i++) { 
		 if ($c==strlen($r)) $c=0; 
		 $v.= substr($t,$i,1) ^ substr($r,$c,1); 
		 $c++; 
	  } 
	  return $v; 
   } 
 
	function decrypt($t) { 
	   $t = $this->ed(base64_decode($t)); 
	   $v = ""; 
	   for ($i=0;$i<strlen($t);$i++){ 
		  $md5 = substr($t,$i,1); 
		  $i++; 
		  $v.= (substr($t,$i,1) ^ $md5); 
	   } 
	   return $v; 
	} 
 
	function encrypt($t){ 
	  $r = $this->my_key2 ; 
	  $c=0; 
	  $v = ""; 
	  for ($i=0;$i<strlen($t);$i++){ 
		 if ($c==strlen($r)) $c=0; 
		 $v.= substr($r,$c,1) . 
			 (substr($t,$i,1) ^ substr($r,$c,1)); 
		 $c++; 
	  } 
	  return base64_encode($this->ed($v)); 
	} 
 
	function cryptsession() {
	  $valuecrypt = base64_encode($this->my_value) ;
	  for ($f=0 ; $f<=strlen($valuecrypt)-1; $f++) {
		$this->session[$f] = $this->encrypt(intval(ord($valuecrypt[$f]))*$this->my_key) ;    
	  }
	  $_SESSION["$this->my_session"] = $this->session ;
	}
 
	function decryptsession() {
	  $this->session = $_SESSION["$this->my_session"] ;
	  $this->my_value = "" ; 
	  for ($f=0 ; $f<=count($this->session)-1; $f++) {
		$this->my_value .= strval(chr($this->decrypt($this->session[$f])/$this->my_key)) ;
	  }
	  return(base64_decode($this->my_value)) ;        
	}
 
	function _setSession($session, $value) {
	  $this->my_session = $session ;
	  $this->my_value = $value ;
	  $this->cryptsession() ;
	}
 
	function _getSession($session) {
	  $this->my_session = $session ;
	  return ($this->decryptsession()) ;
	}
 
  }
 
 

?>
