<?php

namespace Omniphx\Forrest\Formatters;

use Omniphx\Forrest\Interfaces\FormatterInterface;
use Omniphx\Forrest\Interfaces\RepositoryInterface;

class XMLFormatter implements FormatterInterface
{
	protected $tokenRepository;
	protected $settings;
	protected $headers;

	public function __construct(RepositoryInterface $tokenRepository, $settings) {
		$this->tokenRepository = $tokenRepository;
		$this->settings        = $settings;
	}

	public function setHeaders()
	{
		$accessToken = $this->tokenRepository->get()['access_token'];
		$tokenType   = $this->tokenRepository->get()['token_type'];

		$this->headers['Accept'] = 'application/xml';
		$this->headers['Content-Type'] = 'application/xml';
		$this->headers['Authorization'] = "$tokenType $accessToken";

		$this->setCompression();

		return $this->headers;
	}

	private function setCompression()
	{
		if (!$this->settings['defaults']['compression']) return;

		$this->headers['Accept-Encoding']  = $this->settings['defaults']['compressionType'];
		$this->headers['Content-Encoding'] = $this->settings['defaults']['compressionType'];
	}

	public function setBody($data)
	{
		return urlencode($data);
	}

	public function formatResponse($response)
	{
		$body = $response->getBody();
		$contents = (string) $body;
		$decodedXML = simplexml_load_string($contents);

		return $decodedXML;
	}
}