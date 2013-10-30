<?php

class Controller_GoogleAPI_Auth extends Controller
{

    function action_auth()
    {
        if (isset($_GET['code'])) {
            $gplus = GPlus::instance();
            $gplus->client->authenticateCode();
            if ($gplus->client->isAccessTokenExpired()) {
                session_destroy();
                $newAccessToken = json_decode($gplus->client->getAccessToken());
                $gplus->client->refreshToken($newAccessToken->refresh_token);
            }

            $config = Kohana::$config->load('googleapi');
            $isLoggedInSession = $config->get('is_logged_in', null);
            $redirectUrl = $config->get('post_auth_url', null);

            if (!is_null($isLoggedInSession) || !$isLoggedInSession) {
                $user = $gplus->getUser();
                \Kohana_Session::instance()->set($isLoggedInSession, true);
            }

            if (is_null($redirectUrl)) {
                $redirectUrl = Request::$current->detect_uri();
            }

            HTTP::redirect($redirectUrl);
        } else {
            $this->response->body('<script>window.close();</script>');
        }
    }
}