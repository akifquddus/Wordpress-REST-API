# Wordpress-REST-API
Simplified and Secure REST API to Create a Wordpress Post with Featured Image

## Verify User Wordpress URL and Login Info
```php
$fields_string = "";
$fields = array(
  'wprequest' => true,
  'user' => 'admin',
  'pass' => 'password',
  'type' => 'verify',
);

foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string, '&');

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, 'http://example.com' . $this->path);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

$result = json_decode(curl_exec($ch));
```
    
## Result
```php
{
  "status" => true,
  "message" => "Account Successfully Verified"
}
```
