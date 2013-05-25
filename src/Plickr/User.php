<?php
/**
 * @author dzsubek <szalay.attila@ustream.tv>
 */
namespace Plickr;
/**
 * User
 */
class User {
	/**
	 * @var string
	 */
	private $nsId;

	/**
	 * @var string
	 */
	private $userName;

	/**
	 * @var string
	 */
	private $fullName;

	/**
	 * @param string $nsId
	 * @param string $userName
	 * @param string $fullName
	 */
	public function __construct($nsId, $userName, $fullName)
	{
		$this->nsId     = $nsId;
		$this->userName = $userName;
		$this->fullName = $fullName;
	}

	/**
	 * @return string
	 */
	public function getFullName()
	{
		return $this->fullName;
	}

	/**
	 * @return string
	 */
	public function getNsId()
	{
		return $this->nsId;
	}

	/**
	 * @return string
	 */
	public function getUserName()
	{
		return $this->userName;
	}


}
