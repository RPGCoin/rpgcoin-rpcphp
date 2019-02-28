# rpgcoin-RPCPHP
*

A basic PHP class for making calls to RPGcoin's Network.
https://github.com/RPGCoin/rpgcoin-RPCPHP
*/
// How to initialize RPGCoin connection/object:
//$rpgcoin = new RPGcoin('username','password');

// You can specify a host and port (Optional).
//$rpgcoin = new RPGcoin('username','password','host','port');
// Default connection settings:
//	host = localhost
//	port = 7214
//	proto = http

// For an SSL connection you can set a CA certificate or leave blank
// This will set the protocol to HTTPS and some CURL flags
//$rpgcoin->setSSL('/full/path/to/certificate.cert');

// Make calls to rpgcoind as methods for your object. Response returns an array.
// Examples:
//$rpgcoin->getinfo();
//$rpgcoin->getrawtransaction('2b849538e4d43a20daf8b19a3bac762c7edad16386e3cd7205a18035aa6646b0',1);
//$rpgcoin->getblock('000000000001f38aa42b905231c7a8a12e4508de126b683f8165f2589e844070');
// The full response is stored in $this->response, the raw JSON is stored in $this->raw_response
// If for any reason a call fails, it will return FALSE and store its error message in $this->error :
//echo $rpgcoin->error;

// The HTTP status code is stored inside $this->status and is either an HTTP status code or will be 0 if cURL was not to connect.
// Example:
//echo $rpgcoin->status;
