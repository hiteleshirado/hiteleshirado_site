<?php

$app['screens_dir']						= __DIR__ . '/../images/';
$app['facebook_app_id']					= 'your_app_id_here';
$app['facebook_app_secret']				= 'your_app_secret_here';
$app['facebook_oauth_dialog']			= "https://www.facebook.com/dialog/oauth?client_id=%1\$s&redirect_uri=%2\$s";
$app['facebook_oauth_token_url']		= "https://graph.facebook.com/oauth/access_token?client_id=%1\$s&redirect_uri=%2\$s&client_secret=%3\$s&code=%4\$s";
$app['facebook_api_me']					= "https://graph.facebook.com/me?access_token=%1\$s";

$app['class_news_screendepository']		= 'News\ScreenDepository';
$app['class_auth_facebook']				= 'Auth\Facebook';
