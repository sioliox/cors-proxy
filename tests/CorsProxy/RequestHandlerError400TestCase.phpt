<?php

namespace HtmlDrivenTests\CorsProxy;

use Guzzle\Http\Exception\CurlException;
use HtmlDriven\CorsProxy\RequestHandler;
use HtmlDrivenTests\CorsProxy\Mock\FakeClient;
use HtmlDrivenTests\CorsProxy\Mock\FakeRequest;
use Tester\Assert;
use Tester\TestCase;
use function run;

require_once __DIR__ . '/../bootstrap.php';

/**
 * Tests invalid host name results in 400 HTTP error.
 *
 * @author RebendaJiri <jiri.rebenda@htmldriven.com>
 * 
 * @testCase
 * @httpCode 400
 */
class RequestHandlerError404TestCase extends TestCase
{
	/**
	 * @return void
	 */
	public function testError400()
	{
		$statusCode = 404;
		$headers = [];
		$body = 'Lorem ipsum dolor sit amet.';
		
		$response = function() {
			$curlException = new CurlException();
			$curlException->setError('Could not connect.', CURLE_COULDNT_CONNECT);
			throw $curlException;
		};
		
		$fakeRequest = new FakeRequest($response);
		$fakeClient = new FakeClient($fakeRequest);
		
		$requestHandler = new RequestHandler($fakeClient);
		
		ob_start();
		$requestHandler->handleRequest('http://unknown-host-123-cba.htmldriven.com/sample.json');
		$contents = ob_get_clean();
		
		$json = [
			'success' => FALSE,
			'error' => "Unable to handle request: CURL failed with message 'Could not connect.'.",
			'body' => NULL,
		];
		
		Assert::same(json_encode($json), $contents);
	}
}

run(new RequestHandlerError404TestCase());
