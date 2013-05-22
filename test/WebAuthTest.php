<?php
/**
 * @author dzsubek <szalay.attila@ustream.tv>
 */

class WebAuthTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var \Plickr\WebAuth
	 */
	private $webAuth;

	const AUTH_URL = 'auth_url';
	const SECRET   = 'secret';
	const KEY      = 'key';

	public function setUp()
	{
		$config = new \Plickr\AppConfig(
			array(
				 'api_key'    => self::KEY,
				 'api_secret' => self::SECRET,
				 'auth_url'   => self::AUTH_URL,
				 'api_url'   => 'apiurl',
			)
		);
		$this->webAuth = new \Plickr\WebAuth($config);
	}

	public function testGetAuthLink()
	{
		$expKey = md5(self::SECRET.'api_key'.self::KEY.'perms'.\Plickr\WebAuth::PERMISSION_WRITE);
		$expUrl = self::AUTH_URL.'?'.'api_key='.self::KEY.'&perms='.\Plickr\WebAuth::PERMISSION_WRITE.'&api_sig='.$expKey;

		$url = $this->webAuth->getAuthUrl(\Plickr\WebAuth::PERMISSION_WRITE);
		$this->assertEquals($expUrl, $url);
	}
}