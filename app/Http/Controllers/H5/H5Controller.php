<?php

namespace App\Http\Controllers\H5;

use EasyWeChat\Work\Application;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class H5Controller extends Controller
{
    function index()
    {
        H5Controller::getCode();

        return view('vue');
    }

    public static function getCode()
    {
        try {
            $config = [
                'app_id' => config('pay.wechat.app_id'),
                'scope'  => 'snsapi_base',
                'oauth'  => [
                    'app_id' => config('pay.wechat.app_id'),
                    'scopes'        => ['snsapi_base'],
                    'scope'  => 'snsapi_base',
                    'response_type' => 'code',
                    'callback'      => config('app.h5_domain') . '/h5/callback',
                ],
            ];
            myLog('code_config', ['data' => $config]);
            $app   = new Application($config);

            myLog('code_app', ['data' => $app]);
            $oauth = $app->oauth->redirect()->send();

            myLog('code_response', ['data' => $oauth]);

            return $oauth;
        } catch (\Exception $e) {
            myLog('code_error', ['data' => '[' . $e->getLine() . ']' . $e->getMessage()]);
        }
    }

    public function callback()
    {
        myLog('callback_response', ['result' => 'success']);
    }
}
