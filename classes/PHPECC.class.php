<?php

class PHPECC{

	/**
	 * Hex Encode 
	 * 
	 * Encodes a decimal $number into a hexadecimal string.
	 * 
	 * @param	int	$number
	 * @return	string
	 */
	private static function hex_encode($number) {
		$hex = gmp_strval(gmp_init($number, 10), 16);
		return (strlen($hex)%2 != 0) ? '0'.$hex : $hex;
	}
	
	/**
	 * Hex Decode
	 * 
	 * Decodes a hexadecimal $hex string into a decimal number.
	 * 
	 * @param	string	$hex
	 * @return	int
	 */
	private static function hex_decode($hex) {
		return gmp_strval(gmp_init($hex, 16), 10);
	}

	/**
	 * Private Key To Public Key (BCMATH)
	 * 
	 * Accepts a $privatekey_hex as input, and does EC multiplication to obtain
	 * a new point along the curve. The X and Y coordinates are the public
	 * key, which are returned as a hexadecimal string in uncompressed
	 * format.
	 * 
	 * @param	string	$privatekey_hex
	 * @return	string
	 */
	private static function bcmath_hex_private_key_to_hex_public_key($privatekey_hex){
		//Convert hex private key to decimal
		$privatekey_dec = bcmath_Utils::bchexdec($privatekey_hex);
		
		//Genorate Curve and Point
		$g = SECcurve::generator_secp256k1();
		$point = Point::mul($privatekey_dec, $g);
		
		//Get Public Key
		$publickey_bin = "\x04" . str_pad(bcmath_Utils::bc2bin($point->getX()), 32, "\x00", STR_PAD_LEFT) 
								. str_pad(bcmath_Utils::bc2bin($point->getY()), 32, "\x00", STR_PAD_LEFT);

		//Convert to Hex
		$publickey_hex = bin2hex($publickey_bin);

		return $publickey_hex;
	}

	/**
	 * Private Key To Public Key (GMP)
	 * 
	 * Accepts a $privatekey_hex as input, and does EC multiplication to obtain
	 * a new point along the curve. The X and Y coordinates are the public
	 * key, which are returned as a hexadecimal string in uncompressed
	 * format.
	 * 
	 * @param	string	$privatekey_hex
	 * @return	string
	 */
	private static function gmp_hex_private_key_to_hex_public_key($privatekey_hex){
		//Convert hex private key to decimal
		$privatekey_dec = self::hex_decode($privatekey_hex);  

		//Genorate Curve and Point
		$g = SECcurve::generator_secp256k1();
		$point = Point::mul($privatekey_dec, $g);

		//Convert to Hex 
		$xHex = self::hex_encode($point->getX());  
		$yHex = self::hex_encode($point->getY());

		//Get Public Key
		$xHex = str_pad($xHex, 64, '0', STR_PAD_LEFT);
		$yHex = str_pad($yHex, 64, '0', STR_PAD_LEFT);
		$publickey_hex = '04'.$xHex.$yHex;

		return $publickey_hex;
	}

	/**
	 * Private Key To Public Key
	 * 
	 * Accepts a $privatekey_hex as input, and does EC multiplication to obtain
	 * a new point along the curve. The X and Y coordinates are the public
	 * key, which are returned as a hexadecimal string in uncompressed
	 * format.
	 * 
	 * @param	string	$privatekey_hex
	 * @return	string
	 */
	public static function hex_private_key_to_hex_public_key($privatekey_hex){
		if (extension_loaded('gmp') && USE_EXT == 'GMP') {
			return self::gmp_hex_private_key_to_hex_public_key($privatekey_hex);
		} else if (extension_loaded('bcmath') && USE_EXT == 'BCMATH') {
			return self::bcmath_hex_private_key_to_hex_public_key($privatekey_hex);
		}
	}

	/**
	 * Get New Private Key (BCMATH)
	 * 
	 * This function generates a new private key, a number from 1 to $n. 
	 * Once it finds an acceptable value, it will encode it in hex, pad it, 
	 * and return the private key.
	 * 
	 * @return	string
	 */
	public static function bcmath_hex_private_key_genorate(){
		//Genorate Private Key in Binary
		for ($i = 0; $i < 32; $i++) { $privatekey_bin .= chr(mt_rand(0, $i ? 0xff : 0xfe)); }
		//Convert to Hex
		return $privatekey_hex = bin2hex($privatekey_bin);
	}

	/**
	 * Get New Private Key (GMP)
	 * 
	 * This function generates a new private key, a number from 1 to $n. 
	 * Once it finds an acceptable value, it will encode it in hex, pad it, 
	 * and return the private key.
	 * 
	 * @return	string
	 */
	public static function gmp_hex_private_key_genorate() {
		$g = SECcurve::generator_secp256k1();
		$n = $g->getOrder();

		$privatekey_dec = gmp_strval(gmp_init(bin2hex(openssl_random_pseudo_bytes(32)),16));
		while($privatekey_dec >= $n) {
			$privatekey_dec = gmp_strval(gmp_init(bin2hex(openssl_random_pseudo_bytes(32)),16));
		}
		$privatekey_hex = self::hex_encode($privatekey_dec);
		return $privatekey_hex = str_pad($privatekey_hex, 64, '0', STR_PAD_LEFT);
	}

	/**
	 * Get New Private Key
	 * 
	 * This function generates a new private key, a number from 1 to $n. 
	 * Once it finds an acceptable value, it will encode it in hex, pad it, 
	 * and return the private key.
	 * 
	 * @return	string
	 */
	public static function hex_private_key_genorate(){
		if (extension_loaded('gmp') && USE_EXT == 'GMP') {
			return self::gmp_hex_private_key_genorate();
		} else if (extension_loaded('bcmath') && USE_EXT == 'BCMATH') {
			return self::bcmath_hex_private_key_genorate();
		}
	}

	/**
	 * Get New Key Pair
	 * 
	 * Generate a new private key, and convert to an uncompressed public key.
	 * 
	 * @return array
	 */
	public static function hex_keypair_genorate(){
		$keypair = array('private' => '', 'public' => '');
		$keypair['private'] = self::hex_private_key_genorate();
		$keypair['public'] = self::hex_private_key_to_hex_public_key($keypair['private']);
		return $keypair;
	}

}
?>