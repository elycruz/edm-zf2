<?php

defined( 'DEFAULT_PHONE_FORMAT' ) 
	|| define( 'DEFAULT_PHONE_FORMAT', '(%1$d)-%2$d-%3$d' );

class Edm_Form_Helper_FormDataFormatter 
{
	public static function formatPhone( $phone, $format = PHONE_FORMAT )
	{
		return sprintf( 
			$format, 
			substr( $phone, 0, 3 ), 
			substr( $phone, 3, 3 ),
			substr( $phone, 6, 4 )
		);
	}
}