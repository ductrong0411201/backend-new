<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;

/**
 * Class ApiServiceController.
 */
class ApiServiceController extends Controller
{
    protected $client;

    function __construct()
    {
        $this->client = new \GuzzleHttp\Client(['verify' => false]);
    }

    protected function error(RequestException $e)
    {
        $req = $e->getRequest();
        $res = $e->getResponse();
        $statusCode = 500;

        $resData = [
            'request' => [
                'url' => (string)$req->getUri(),
                'method' => $req->getMethod(),
                'data' => json_decode($req->getBody()->getContents())
            ]
        ];

        if ($e->hasResponse()) {
            $resData = array_merge($resData, [
                'response' => [
                    'status_code' => $res->getStatusCode(),
                    'message' => $res->getReasonPhrase(),
                    'data' => json_decode($res->getBody()->getContents())
                ]
            ]);

            $statusCode = $res->getStatusCode();
        }

        return response()->json($resData, $statusCode);
    }

    /**
     * Request to soap server
     *
     * @return array data
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function soapRequest($type, $url, $data)
    {
        try {
            // Make request
            $res = $this->client->request($type, $url, [
                'body' => $data,
                'headers' => [
                    'Content-Type' => 'text/xml;charset=UTF-8',
                    'Accept' => 'text/xml'
                ]
            ]);

            return $this->handleSoapResponse($res);
        } catch (RequestException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function soapRequestAsync($type, $url, $data)
    {
        try {
            // Make request
            return $this->client->requestAsync($type, $url, [
                'body' => $data,
                'headers' => [
                    'Content-Type' => 'text/xml;charset=UTF-8',
                    'Accept' => 'text/xml'
                ]
            ]);
        } catch (RequestException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get Response Data
     *
     * @param \GuzzleHttp\Psr7\Response $response
     * @return mixed
     */
    protected function handleSoapResponse(Response $response)
    {
        // Get request body
        $result = $response->getBody()->getContents();

        // Normalize tag name. Eg: <ns2:data> to <data>
        $result = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$3", $result);

        // Convert to SimpleXMLElement
        $result = new \SimpleXMLElement($result);

        // Get Body Element
        $body = $result->xpath('//SOAP-ENV:Body')[0];

        // Convert Body Element to php array
        $data = json_decode(json_encode((array)$body));

        return $data;
    }

    protected function request($type, $url, $data = [], $headers = [], $auth = [])
    {
        try {
            if (strtolower($type) == 'get') {
                $name = 'param';
            } else {
                $name = 'json';
            }
            $res = $this->client->request($type, $url, [
                $name => $data,
                'auth' => $auth,
                'headers' => $headers
            ]);

            return $this->handleResponse($res);
        } catch (RequestException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get Response Data
     *
     * @param \GuzzleHttp\Psr7\Response $response
     * @return mixed
     */
    protected function handleResponse(Response $response)
    {
        $data = json_decode($response->getBody()->getContents());

        return $data;
    }

    /**
     * Handle Exception
     *
     * @param mixed $exception
     * @return mixed
     */
    protected function handleException($exception)
    {
        if ($exception->hasResponse()) {
            $response = $exception->getResponse();

            $statusCode = $response->getStatusCode();
            $message = $exception->getMessage();
            $responseData = $this->handleResponse($response);

            return response()->json([
                'status_code' => $statusCode,
                'message' => $message,
                'error' => $responseData
            ], $statusCode);
        } else {
            $statusCode = $exception->getCode();
            $message = $exception->getMessage();

            return response()->json([
                'status_code' => $statusCode,
                'message' => $message
            ], $statusCode);
        }
    }

    /**
     * Request async
     *
     * @param $type
     * @param $url
     * @param array $data
     * @param array $headers
     * @param array $auth
     * @return \GuzzleHttp\Promise\PromiseInterface
     * @throws \Exception
     */
    protected function requestAsync($type, $url, $data = [], $headers = [], $auth = [])
    {
        try {
            if (strtolower($type) == 'get') {
                $name = 'param';
            } else {
                $name = 'json';
            }
            return $this->client->requestAsync($type, $url, [
                $name => $data,
                'auth' => $auth,
                'headers' => $headers
            ]);
        } catch (RequestException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
