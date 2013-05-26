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
	const SAFETY_LEVEL_SAFE       = 1;
	const SAFETY_LEVEL_MODERATE   = 2;
	const SAFETY_LEVEL_RESTRICTED = 3;

	const CONTENT_TYPE_PHOTO      = 1;
	const CONTENT_TYPE_SCREENSHOT = 2;
	const CONTENT_TYPE_OTHER      = 3;

	const HIDDEN_KEEP_GLOBAL_SEARCH = 1;
	const HIDDEN_HIDE_GLOBAL_SEARCH = 2;

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

	/**
	 * Get photo set list
	 *
	 * @param int $page
	 * @param int $perPage
	 *
	 * @return array
	 */
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

	public function createPhotoSet($title, $primaryPhotoId, $description = '')
	{
		$result = $this->call(
			'flickr.photosets.create',
			array(
				 'title'            => $title,
				 'description'      => $description,
				 'primary_photo_id' => $primaryPhotoId,
			)
		);
var_dump($result); die();
		return $result;
	}

	public function upload(
		$path,
		$title = '',
		$description = '',
		$tags = '',
		$isPublic = 1,
		$isFriend = 0,
		$isFamily = 0,
		$safetyLevel = '',
		$contentType = '',
		$hidden = ''
	) {
		$result = $this->call(
			'upload',
			array(
				 'photo'        => '@' . $path,
				 'title'        => $title,
				 'description'  => $description,
				 'tags'         => $tags,
				 'is_public'    => $isPublic,
				 'is_friend'    => $isFriend,
				 'is_family'    => $isFamily,
				 'safety_level' => $safetyLevel,
				 'content_type' => $contentType,
				 'hidden'       => $hidden
			)
		);

		var_dump($result);
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
		$paramHolder
			->set('api_key', $this->appConfig->getApiKey())
			->set('auth_token', $this->accessToken->getToken())
			->set('format', 'json')
			->set('nojsoncallback', '1')
			->setArray($params);

		if ($method == 'upload') {
			$client = $this->getClient(true)->post('', null, $paramHolder->getArray());

		} else {
			$paramHolder->set('method', $method);
			$client = $this->getClient(false)->get('?'.$paramHolder->getQueryParams());
		}
		$result = $client->send()
			->xml();
		var_dump($result); die();

		if ($result['stat'] != 'ok') {
			throw new ApiException($result['message'], $result['code']);
		}

		return $result;
	}

	private function getClient($forUpload)
	{
		return new HttpClient(
			$forUpload ?  $this->appConfig->getUploadUrl() : $this->appConfig->getApiUrl()
		);
	}
}
