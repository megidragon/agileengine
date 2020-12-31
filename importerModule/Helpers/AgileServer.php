<?php
namespace ImporterModule\Helpers;

use App\Models\Importer;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use ImporterModule\Exceptions\InvalidTokenException;
use ImporterModule\ValueObject\Signatures;

class AgileServer
{
    protected $_apiKey;
    protected $_token;

    private $attempt = 1;

    public function __construct()
    {
        $this->setApiKey();
        $this->login();
    }

    protected function login()
    {
        $lastToken = $this->getLastAccessToken();
        if ($lastToken)
        {
            $this->_token = $lastToken;
            return null;
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($this->getRoute('auth'), [
            'apiKey' => $this->_apiKey
        ]);

        if (!$this->handleErrors($response))
        {
            return $this->login();
        }

        $this->setToken($response['token']);
    }

    public function listEndpoint($page=1, $limit=20)
    {
        return $this->get('images', [
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    public function showEndpoint($id)
    {
        return $this->get('images'.Signatures::URL_SEPARATOR.$id);
    }


    protected function setApiKey()
    {
        $this->_apiKey = env('OAUTH_SERVER_API_KEY');
    }

    private function get($url, $params=[])
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' =>'Bearer '.$this->_token
        ])->get($this->getRoute($url), $params);

        if (!$this->handleErrors($response))
        {
            $this->login();
        }

        return $response->json();
    }

    private function getRoute($method)
    {
        return env('OAUTH_SERVER_DOMAIN').Signatures::URL_SEPARATOR.$method;
    }

    private function handleErrors(Response $response)
    {
        if ($response->failed())
        {
            if ($response->serverError())
            {
                throw new \Exception('Server down', 500);
            }

            if ($response->clientError())
            {
                if ($this->attempt > 1)
                {
                    throw new InvalidTokenException();
                }
                $this->attempt += 1;
                return false;
            }

            throw new \Exception('Unhandled http exception in importer');
        }

        $this->attempt = 1;
        return true;
    }

    private function getLastAccessToken()
    {
        $lastRecord = Importer::latest('created_at')->first();

        if (empty($lastRecord))
        {
            return false;
        }

        $insert_date = Carbon::parse($lastRecord->created_at);

        if (Carbon::now()->diffInHours($insert_date) > 5)
        {
            return false;
        }

        return $lastRecord->access_token;
    }

    private function setToken($token)
    {
        $this->_token = $token;

        Importer::insert(['access_token' => $token]);
    }
}
