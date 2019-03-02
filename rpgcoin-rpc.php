<?php
/*

A basic PHP class for making calls to RPGcoin's Network.
https://github.com/RPGCoin/RPGCoin-RPCPHP
*/
// How to initialize RPGCoin connection/object:
//$rpgcoin = new RPGcoin('username','password');

// You can specify a host and port (Optional).
//$rpgcoin = new RPGcoin('username','password','host','port');
// Default connection settings:
//	host = localhost
//	port = 7210
//	proto = http

// For an SSL connection you can set a CA certificate or leave blank
// This will set the protocol to HTTPS and some CURL flags
//$rpgcoin->setSSL('/full/path/to/certificate.cert');

// Make calls to rpgcoind as methods for your object. Response returns an array.
// Examples:
//$rpgcoin->getinfo();
//$rpgcoin->getrawtransaction('a9a2217d055ed6dc7b0d72bdf296d8de9b3f2c1de1199c13334871e9dfe015b4',1);
//$rpgcoin->getblock('000005f7d089eca4f7653bf48d33ba6d126b8785ccde17d4765bce8122f1b6bc');
// The full response is stored in $this->response, the raw JSON is stored in $this->raw_response
// If for any reason a call fails, it will return FALSE and store its error message in $this->error :
//echo $rpgcoin->error;

// The HTTP status code is stored inside $this->status and is either an HTTP status code or will be 0 if cURL was not to connect.
// Example:
//echo $rpgcoin->status;
class RPGcoin {
    // Config options
    private $username;
    private $password;
    private $proto;
    private $host;
    private $port;
    private $url;
    private $CACertificate;
    // Info and debugging
    public $status;
    public $error;
    public $raw_response;
    public $response;
    private $id = 0;
    /**
     * @param string $username
     * @param string $password
     * @param string $host
     * @param int $port
     * @param string $proto
     * @param string $url
     */
    function __construct($username, $password, $host = 'localhost', $port = 7210, $url = null) {
        $this->username      = $username;
        $this->password      = $password;
        $this->host          = $host;
        $this->port          = $port;
        $this->url           = $url;
        // Set defaults
        $this->proto         = 'http';
        $this->CACertificate = null;
    }
    /**
     * @param string|null $certificate
     */
    function setSSL($certificate = null) {
        $this->proto         = 'https'; // force HTTPS
        $this->CACertificate = $certificate;
    }
    function __call($method, $params) {
        $this->status       = null;
        $this->error        = null;
        $this->raw_response = null;
        $this->response     = null;
        // If no parameters are passed, returns an empty array
        $params = array_values($params);
        // The ID should be unique for each call
        $this->id++;
        // Build the request, dont worry if params has an empty array
        $request = json_encode(array(
            'method' => $method,
            'params' => $params,
            'id'     => $this->id
        ));
        // Build cURL session
        $curl    = curl_init("{$this->proto}://{$this->host}:{$this->port}/{$this->url}");
        $options = array(
            CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
            CURLOPT_USERPWD        => $this->username . ':' . $this->password,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_HTTPHEADER     => array('Content-type: application/json'),
            CURLOPT_POST           => TRUE,
            CURLOPT_POSTFIELDS     => $request
        );
        // Error Prevention
        if (ini_get('open_basedir')) {
            unset($options[CURLOPT_FOLLOWLOCATION]);
        }
        if ($this->proto == 'https') {
            // If CA Certificate was specified, change CURL so it looks for it
            if ($this->CACertificate != null) {
                $options[CURLOPT_CAINFO] = $this->CACertificate;
                $options[CURLOPT_CAPATH] = DIRNAME($this->CACertificate);
            }
            else {
                // If not we need to assume SSL cannot be verified so we set this flag to FALSE to allow the connection
                $options[CURLOPT_SSL_VERIFYPEER] = FALSE;
            }
        }
        curl_setopt_array($curl, $options);
        // Execute request and decode to an array
        $this->raw_response = curl_exec($curl);
        $this->response     = json_decode($this->raw_response, TRUE);
        // If status is not 200, something is wrong
        $this->status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        // If there was no error, this will be an empty string
        $curl_error = curl_error($curl);
        curl_close($curl);
        if (!empty($curl_error)) {
            $this->error = $curl_error;
        }
        if ($this->response['error']) {
            // If rpgcoind returned an error, store it inside $this->error
            $this->error = $this->response['error']['message'];
        }
        elseif ($this->status != 200) {
            // If error message wasnt right, we make our own
            switch ($this->status) {
                case 400:
                    $this->error = 'HTTP_BAD_REQUEST';
                    break;
                case 401:
                    $this->error = 'HTTP_UNAUTHORIZED';
                    break;
                case 403:
                    $this->error = 'HTTP_FORBIDDEN';
                    break;
                case 404:
                    $this->error = 'HTTP_NOT_FOUND';
                    break;
            }
        }
        if ($this->error) {
            return FALSE;
        }
        return $this->response['result'];
    }
}

