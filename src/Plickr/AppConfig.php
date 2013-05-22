<?php
/**
 * @author dzsubek <szalay.attila@ustream.tv>
 */

namespace Plickr;

/**
 * AppConfig
 */
class AppConfig {
	/**
	 * @var string
	 */
	private $apiKey;

	/**
	 * @var string
	 */
	private $apiSecret;

	/**
	 * @var string
	 */
	private $authUrl;

	/**
	 * @var string
	 */
	private $apiUrl;

	public function __construct($config)
	{
		$this->checkConfigArray($config);
		$this->apiKey    = $config['api_key'];
		$this->apiSecret = $config['api_secret'];
		$this->authUrl   = $config['auth_url'];
		$this->apiUrl    = $config['api_url'];
	}

	/**
	 * @param array $config
	 *
	 * @throws \InvalidArgumentException
	 */
	private function checkConfigArray($config)
	{
		$required = array('api_key', 'api_secret', 'auth_url', 'api_url');
		foreach ($required as $field)
		{
			if (!array_key_exists($field, $config) || empty($config[$field])) {
				throw new \InvalidArgumentException($field.' is required');
			}
		}
	}

	/**
	 * @return string
	 */
	public function getApiKey()
	{
		return $this->apiKey;
	}

	/**
	 * @return string
	 */
	public function getApiSecret()
	{
		return $this->apiSecret;
	}

	/**
	 * @return string
	 */
	public function getAuthUrl()
	{
		return $this->authUrl;
	}

	/**
	 * @return string
	 */
	public function getApiUrl()
	{
		return $this->apiUrl;
	}

}
