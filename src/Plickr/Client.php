<?php
/**
 * @author dzsubek <szalay.attila@ustream.tv>
 */

namespace Plickr;

use Guzzle\Http\Client as HttpClient;

/**
 * Client
 */
class Client {
	/**
	 * @var AppConfig
	 */
	protected $appConfig;

	/**
	 * @var AccessToken
	 */
	protected $accessToken;

	/**
	 * @param AppConfig   $appConfig
	 * @param AccessToken $accessToken
	 */
	public function __construct(AppConfig $appConfig, AccessToken $accessToken)
	{
		$this->appConfig   = $appConfig;
		$this->accessToken = $accessToken;
	}

	public function getPhotoSets($page, $perPage = 10)
	{
		$result = $this->call(
			'flickr.photosets.getList',
			array(
				 'user_id'  => $this->accessToken->getUser()->getNsId(),
				 'page'     => $page,
				 'per_page' => $perPage,
			)
		);
		return $result['photosets'];
	}

	/**
	 * Call a flickr api method
	 *
	 * @param $method
	 * @param $params
	 *
	 * @throws ApiException
	 * @return array
	 */
	private function call($method, $params)
	{
		$paramHolder = new ParamHolder($this->appConfig);
		$paramHolder->set('method', $method)
			->set('api_key', $this->appConfig->getApiKey())
			->set('format', 'json')
			->set('nojsoncallback', '1')
			->setArray($params);

		$result = $this->getClient()->get('?'.$paramHolder->getQueryParams())
			->send()
			->json();

		if ($result['stat'] != 'ok') {
			throw new ApiException($result['code'], $result['message']);
		}

		return $result;
	}

	private function getClient()
	{
		return new HttpClient($this->appConfig->getApiUrl());
	}
}
