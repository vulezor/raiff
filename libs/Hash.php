<?php
 class Hash
 {
 	//Hash::create('sha1', 'passwordhere', 'SaltIfThereIsOne')
 	/**
 	*@param string $algo the algoritam (md5, sha1, whirpool, etc)
 	*@param string $data Data to encode
 	*@param string $salt The salt (This should be the same troughout system probably)
 	*@return string The hashed/salted data
 	*/
 	public static function create($algo, $data,  $salt){
 		$context = hash_init($algo, HASH_HMAC, $salt); 
 		hash_update($context, $data);

 		return hash_final($context);
 	}
 	

 }
/* $salt = '23sd45ergdfg56RRTssasdKSK83423';
 $pass = sha1('nikolaAG35');
 echo Hash::create('sha1', $pass, $salt); */
?>