<?php


  
 
/*
 class CryptSession {
 
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
 
	function GetNewPair ()
		{
			$config = array(
				"digest_alg" => "sha512",
				"private_key_bits" => 384,
				"private_key_type" => OPENSSL_KEYTYPE_RSA,
			);
			
			// Create the private and public key
			$res = openssl_pkey_new($config);

			// Extract the private key from $res to $privKey
			openssl_pkey_export($res, $privKey);

			// Extract the public key from $res to $pubKey
			$pubKey = openssl_pkey_get_details($res);
			$pubKey = $pubKey["key"];    

			$result = array ('PrivateKey'=>$privKey, 'PublicKey'=>$pubKey);
			return $result;
		}
  }

$cryptSession  = new CryptSession();

$keys = $cryptSession::GetNewPair();

$data = 'plaintext data goes here';

// Encrypt the data to $encrypted using the public key
openssl_public_encrypt($data, $encrypted, $keys['PublicKey']);

// Decrypt the data using the private key and store the results in $decrypted
openssl_private_decrypt($encrypted, $decrypted, $keys['PrivateKey']);

echo $decrypted; */
	
?>
