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

	/**
	 * Create a photo set
	 *
	 * @param string $title
	 * @param int    $primaryPhotoId
	 * @param string $description
	 *
	 * @return mixed
	 */
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
		return $result['photoset'];
	}

	/**
	 * Add a photo to photo set
	 *
	 * @param int $photoSetId
	 * @param int $photoId
	 *
	 * @return bool
	 */
	public function addPhotoToSet($photoSetId, $photoId)
	{
		$result = $this->call(
			'flickr.photosets.addPhoto',
			array(
				 'photoset_id' => $photoSetId,
				 'photo_id'    => $photoId,
			)
		);
		return true;
	}

	/**
	 * Returns information for the calling user related to photo uploads.
	 *
	 * @return array
	 */
	public function getUploadStatus()
	{
		return $this->call('flickr.people.getUploadStatus');
	}

	/**
	 * Upload a photo
	 *
	 * @param string $path
	 * @param string $title
	 * @param string $description
	 * @param string $tags
	 * @param int    $isPublic
	 * @param int    $isFriend
	 * @param int    $isFamily
	 * @param string $safetyLevel
	 * @param string $contentType
	 * @param string $hidden
	 *
	 * @return int
	 * @throws ApiException
	 */
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
		$params = array(
			 'photo'        => '@' . $path,
			 'title'        => $title,
			 'description'  => $description,
			 'tags'         => $tags,
			 'is_public'    => $isPublic,
			 'is_friend'    => $isFriend,
			 'is_family'    => $isFamily,
			 'safety_level' => $safetyLevel,
			 'content_type' => $contentType,
			 'hidden'       => $hidden,
			 'format' => 'json'
		);

		$paramHolder = $this->getParamHolder()
			->setArray($params);
//		$response = $this->getClient(true)
//			->post('',null, $paramHolder->getArray())
//			->send()
//			->xml();

//		$attributes = $response->attributes();
//		if ($attributes['stat'] != 'ok') {
//			var_dump($attributes);
//			throw new ApiException($response['message'], $response['code']);
//		}

//		return (string) $response->photoid;

		$ch = curl_init($this->appConfig->getUploadUrl());
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $paramHolder->getArray());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec ($ch);

		if (!preg_match('/stat="ok"/', $response)) {
			throw new ApiException($response);
		}

		preg_match('/<photoid>([0-9]+)<\/photoid>/', $response, $match);

		return $match[1];


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
	private function call($method, $params = array())
	{
		$paramHolder = $this->getParamHolder();
		$paramHolder->set('method', $method)
			->set('format', 'json')
			->set('nojsoncallback', '1')
			->setArray($params);

		$response = $this->getClient()->get('?'.$paramHolder->getQueryParams())
			->send()
			->json();

		if ($response['stat'] != 'ok') {
			throw new ApiException($response['message']. ' ('.$method.')', $response['code']);
		}

		return $response;
	}

	/**
	 * Get HTTP client
	 *
	 * @param bool $forUpload
	 *
	 * @return HttpClient
	 */
	private function getClient($forUpload = false)
	{
		return new HttpClient(
			$forUpload ?  $this->appConfig->getUploadUrl() : $this->appConfig->getApiUrl()
		);
	}

	/**
	 * Get param holder with api_key and auth_key
	 *
	 * @return ParamHolder
	 */
	private function getParamHolder()
	{
		$paramHolder = new ParamHolder($this->appConfig);
		$paramHolder
			->set('api_key', $this->appConfig->getApiKey())
			->set('auth_token', $this->accessToken->getToken());

		return $paramHolder;
	}
}
