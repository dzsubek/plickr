<?php

namespace Plickr;

use Guzzle\Http\Client;

class WebAuth
{
	const PERMISSION_READ = 'read';

	const PERMISSION_WRITE = 'write';

	const PERMISSION_DELETE = 'read';
	/**
	 * @var AppConfig
	 */
	protected $appConfig;

	public function __construct(AppConfig $appConfig)
	{
		$this->appConfig = $appConfig;
	}

	/**
	 * @param $permission
	 *
	 * @return string
	 */
	public function getAuthUrl($permission)
	{
		$params = new ParamHolder($this->appConfig);
		$params->set('api_key', $this->appConfig->getApiKey())
			->set('perms', $permission);

		return $this->appConfig->getAuthUrl().'?'.$params->getQueryParams();
	}

	public function getToken($frob)
	{
		$params = new ParamHolder($this->appConfig);
		$params->set('api_key', $this->appConfig->getApiKey())
			->set('method', 'flickr.auth.getToken')
			->set('frob', $frob);

		$response = $this->getClient()->get('?'.$params->getQueryParams())
			->send()->xml();

		$attr = $response->attributes();
		if ($attr['stat'] == 'fail') {
			$errAttr = $response->err->attributes();
			throw new AuthFailedException((string) $errAttr['msg'], (int) $errAttr['code']);
		}
	}

	private function getClient()
	{
		return new Client($this->appConfig->getApiUrl());
	}

}