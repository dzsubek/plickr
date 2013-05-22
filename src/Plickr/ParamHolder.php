<?php
/**
 * @author dzsubek <szalay.attila@ustream.tv>
 */

namespace Plickr;
/**
 * ParamHolder
 */
class ParamHolder {

	/**
	 * @var AppConfig
	 */
	private $appConfig;

	/**
	 * @var array
	 */
	private $params = array();

	/**
	 * @param AppConfig $appConfig
	 */
	public function __construct(AppConfig $appConfig)
	{
		$this->appConfig = $appConfig;
	}

	/**
	 * @param string $name
	 * @param string $value
	 *
	 * @return ParamHolder
	 */
	public function set($name, $value)
	{
		$this->params[$name] = $value;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getQueryParams()
	{
		return http_build_query(
			$this->signParams($this->params)
		);
	}

	/**
	 * @return string
	 */
	private function signParams()
	{
		$params = $this->params;
		ksort($params);
		$sign = $this->appConfig->getApiSecret();
		foreach ($params as $key => $value) {
			$sign .= $key.$value;
		}

		$params['api_sig'] = md5($sign);
		return $params;
	}
}
