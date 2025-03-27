<?php
class SocialShare {
    private $facebook_app_id = 'YOUR_FACEBOOK_APP_ID';
    private $facebook_app_secret = 'YOUR_FACEBOOK_APP_SECRET';

    public function getFacebookAppId() {
        return $this->facebook_app_id;
    }

    public function getFacebookAppSecret() {
        return $this->facebook_app_secret;
    }

    // Get FB login url
   public function getFacebookLoginUrl() {
        $redirect_uri = 'http://yourdomain.com/fb-callback.php';
        
        $url = 'https://www.facebook.com/v12.0/dialog/oauth?' . http_build_query([
            'client_id' => $this->facebook_app_id,
            'redirect_uri' => $redirect_uri,
            'state' => bin2hex(random_bytes(16)),
            'scope' => 'public_profile,manage_pages,publish_pages'
        ]);
        
        return $url;
    }

    // Post
    public function postToFacebook($access_token, $message) {
        $url = "https://graph.facebook.com/me/feed";
        
        $data = [
            'message' => $message,
            'access_token' => $access_token
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
}
?>