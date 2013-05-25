<?php
/**
 * @author dzsubek <szalay.attila@ustream.tv>
 */

namespace Plickr;

/**
 * AccessToken
 */
class AccessToken {
	/**
	 * @var string
	 */
	private $token;

	/**
	 * @var string
	 */
	private $permission;

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @param string $token
	 * @param string $permission
	 * @param user   $user
	 */
	public function __construct($token, $permission, $user)
	{
		$this->token      = $token;
		$this->permission = $permission;
		$this->user       = $user;
	}

	/**
	 * @return string
	 */
	public function getPermission()
	{
		return $this->permission;
	}

	/**
	 * @return string
	 */
	public function getToken()
	{
		return $this->token;
	}

	/**
	 * @return \Plickr\User
	 */
	public function getUser()
	{
		return $this->user;
	}

}
