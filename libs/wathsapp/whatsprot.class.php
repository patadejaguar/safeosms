<?php
require_once 'protocol.class.php';
require_once 'func.php';
require_once 'token.php';
require_once 'rc4.php';
require_once 'mediauploader.php';
require_once 'keystream.class.php';
require_once 'tokenmap.class.php';
require_once 'events/WhatsApiEventsManager.php';
require_once 'SqliteMessageStore.php';

class SyncResult
{
    public $index;
    public $syncId;
    /** @var array $existing */
    public $existing;
    /** @var array $nonExisting */
    public $nonExisting;

    public function __construct($index, $syncId, $existing, $nonExisting)
    {
        $this->index = $index;
        $this->syncId = $syncId;
        $this->existing = $existing;
        $this->nonExisting = $nonExisting;
    }
}

class WhatsProt
{
    /**
     * Constant declarations.
     */
    const CONNECTED_STATUS = 'connected';                   // Describes the connection status with the WhatsApp server.
    const DISCONNECTED_STATUS = 'disconnected';             // Describes the connection status with the WhatsApp server.
    const MEDIA_FOLDER = 'media';                           // The relative folder to store received media files
    const PICTURES_FOLDER = 'pictures';                     // The relative folder to store picture files
    const PORT = 443;                                      // The port of the WhatsApp server.
    const TIMEOUT_SEC = 2;                                  // The timeout for the connection with the WhatsApp servers.
    const TIMEOUT_USEC = 0;                                 //
    const WHATSAPP_CHECK_HOST = 'v.whatsapp.net/v2/exist';  // The check credentials host.
    const WHATSAPP_GROUP_SERVER = 'g.us';                   // The Group server hostname
    const WHATSAPP_HOST = 'c.whatsapp.net';                 // The hostname of the WhatsApp server.
    const WHATSAPP_REGISTER_HOST = 'v.whatsapp.net/v2/register'; // The register code host.
    const WHATSAPP_REQUEST_HOST = 'v.whatsapp.net/v2/code';      // The request code host.
    const WHATSAPP_SERVER = 's.whatsapp.net';               // The hostname used to login/send messages.
    const WHATSAPP_UPLOAD_HOST = 'https://mms.whatsapp.net/client/iphone/upload.php'; // The upload host.
    const WHATSAPP_DEVICE = 'iPhone';                      // The device name.
    const WHATSAPP_VER = '2.11.14';                // The WhatsApp version.
    const WHATSAPP_USER_AGENT = 'WhatsApp/2.12.61 S40Version/14.26 Device/Nokia302'; // User agent used in request/registration code.
    const WHATSAPP_VER_CHECKER = 'https://coderus.openrepos.net/whitesoft/whatsapp_version'; // Check WhatsApp version

    /**
     * Property declarations.
     */
    protected $accountInfo;             // The AccountInfo object.
    protected $challengeFilename = 'nextChallenge.dat';
    protected $challengeData;           //
    protected $debug;                   // Determines whether debug mode is on or off.
    protected $event;                   // An instance of the WhatsApiEvent Manager.
    protected $groupList = array();     // An array with all the groups a user belongs in.
    protected $identity;                // The Device Identity token. Obtained during registration with this API or using Missvenom to sniff from your phone.
    protected $inputKey;                // Instances of the KeyStream class.
    protected $outputKey;               // Instances of the KeyStream class.
    protected $groupId = false;         // Id of the group created.
    protected $lastId = false;          // Id to the last message sent.
    protected $loginStatus;             // Holds the login status.
    protected $mediaFileInfo = array(); // Media File Information
    protected $mediaQueue = array();    // Queue for media message nodes
    protected $messageCounter = 1;      // Message counter for auto-id.
    protected $messageQueue = array();  // Queue for received messages.
    protected $name;                    // The user name.
    protected $newMsgBind = false;      //
    protected $outQueue = array();      // Queue for outgoing messages.
    protected $password;                // The user password.
    protected $phoneNumber;             // The user phone number including the country code without '+' or '00'.
    public    $reader;                  // An instance of the BinaryTreeNodeReader class.
    protected $serverReceivedId;        // Confirm that the *server* has received your command.
    protected $socket;                  // A socket to connect to the WhatsApp network.
    protected $writer;                  // An instance of the BinaryTreeNodeWriter class.
    protected $messageStore;

    /**
     * Default class constructor.
     *
     * @param string $number
     *   The user phone number including the country code without '+' or '00'.
     * @param string $identity
     *  The Device Identity token. Obtained during registration with this API
     *  or using Missvenom to sniff from your phone.
     * @param string $nickname
     *   The user name.
     * @param $debug
     *   Debug on or off, false by default.
     */
    public function __construct($number, $identity, $nickname, $debug = false)
    {
        $this->writer = new BinTreeNodeWriter();
        $this->reader = new BinTreeNodeReader();
        $this->debug = $debug;
        $this->phoneNumber = $number;
        if (!$this->checkIdentity($identity)) {
            //compute identity with pseudo_random_bytes
            $this->identity = $this->buildIdentity($identity);
        } else {
            //use provided identity hash
            $this->identity = urldecode(file_get_contents($identity.'.dat'));
        }
        $this->name = $nickname;
        $this->loginStatus = static::DISCONNECTED_STATUS;
        $this->eventManager = new WhatsApiEventsManager();
    }

    /**
     * If you need use diferent challenge fileName you can use this
     *
     * @param string $filename
     */
    public function setChallengeName($filename){
        $this->challengeFilename = $filename;
    }

    /**
     * Add message to the outgoing queue.
     * @param $node
     */
    public function addMsgOutQueue($node)
    {
        $this->outQueue[] = $node;
    }

    /**
     * Check if account credentials are valid.
     *
     * WARNING: WhatsApp now changes your password everytime you use this.
     * Make sure you update your config file if the output informs about
     * a password change.
     *
     * @return object
     *   An object with server response.
     *   - status: Account status.
     *   - login: Phone number with country code.
     *   - pw: Account password.
     *   - type: Type of account.
     *   - expiration: Expiration date in UNIX TimeStamp.
     *   - kind: Kind of account.
     *   - price: Formatted price of account.
     *   - cost: Decimal amount of account.
     *   - currency: Currency price of account.
     *   - price_expiration: Price expiration in UNIX TimeStamp.
     *
     * @throws Exception
     */
    public function checkCredentials()
    {
        if (!$phone = $this->dissectPhone()) {
            throw new Exception('The provided phone number is not valid.');
        }

        $countryCode = null;
        $langCode = null;

        if ($countryCode == null && $phone['ISO3166'] != '') {
            $countryCode = $phone['ISO3166'];
        }
        if ($countryCode == null) {
            $countryCode = 'US';
        }
        if ($langCode == null && $phone['ISO639'] != '') {
            $langCode = $phone['ISO639'];
        }
        if ($langCode == null) {
            $langCode = 'en';
        }
        if ($phone['cc'] == '77') {
            $phone['cc'] = '7';
        }
        if ($phone['cc'] == '79') {
            $phone['cc'] = '7';
        }

        // Build the url.
        $host = 'https://' . static::WHATSAPP_CHECK_HOST;
        $query = array(
            'cc' => $phone['cc'],
            'in' => $phone['phone'],
            'id' => $this->identity,
            'lg' => $langCode,
            'lc' => $countryCode,
            'network_radio_type' => "1"
        );

        $response = $this->getResponse($host, $query);

        if ($response->status != 'ok') {
            $this->eventManager()->fire("onCredentialsBad",
                array(
                    $this->phoneNumber,
                    $response->status,
                    $response->reason
                ));
            if ($this->debug) {
                print_r($query);
                print_r($response);
            }
            throw new Exception('There was a problem trying to request the code.');
        } else {
            $this->eventManager()->fire("onCredentialsGood",
                array(
                    $this->phoneNumber,
                    $response->login,
                    $response->pw,
                    $response->type,
                    $response->expiration,
                    $response->kind,
                    $response->price,
                    $response->cost,
                    $response->currency,
                    $response->price_expiration
                ));
        }

        return $response;
    }

    /**
     * Register account on WhatsApp using the provided code.
     *
     * @param integer $code
     *   Numeric code value provided on requestCode().
     *
     * @return object
     *   An object with server response.
     *   - status: Account status.
     *   - login: Phone number with country code.
     *   - pw: Account password.
     *   - type: Type of account.
     *   - expiration: Expiration date in UNIX TimeStamp.
     *   - kind: Kind of account.
     *   - price: Formatted price of account.
     *   - cost: Decimal amount of account.
     *   - currency: Currency price of account.
     *   - price_expiration: Price expiration in UNIX TimeStamp.
     *
     * @throws Exception
     */
    public function codeRegister($code)
    {
        if (!$phone = $this->dissectPhone()) {
            throw new Exception('The provided phone number is not valid.');
        }

        $countryCode = null;
        $langCode = null;

        if ($countryCode == null && $phone['ISO3166'] != '') {
            $countryCode = $phone['ISO3166'];
        }
        if ($countryCode == null) {
            $countryCode = 'US';
        }
        if ($langCode == null && $phone['ISO639'] != '') {
            $langCode = $phone['ISO639'];
        }
        if ($langCode == null) {
            $langCode = 'en';
        }

        // Build the url.
        $host = 'https://' . static::WHATSAPP_REGISTER_HOST;
        $query = array(
            'cc' => $phone['cc'],
            'in' => $phone['phone'],
            'id' => $this->identity,
            'code' => $code,
            //'lg' => $langCode,
            //'lc' => $countryCode,
            //'network_radio_type' => "1"
        );

        $response = $this->getResponse($host, $query);


        if ($response->status != 'ok') {
            $this->eventManager()->fire("onCodeRegisterFailed",
                array(
                    $this->phoneNumber,
                    $response->status,
                    $response->reason,
                    $response->retry_after
                ));
            if ($this->debug) {
                print_r($query);
                print_r($response);
            }
            throw new Exception('An error occurred registering the registration code from WhatsApp.');
        } else {
            $this->eventManager()->fire("onCodeRegister",
                array(
                    $this->phoneNumber,
                    $response->login,
                    $response->pw,
                    $response->type,
                    $response->expiration,
                    $response->kind,
                    $response->price,
                    $response->cost,
                    $response->currency,
                    $response->price_expiration
                ));
        }

        return $response;
    }

    /**
     * Request a registration code from WhatsApp.
     *
     * @param string $method
     *   Accepts only 'sms' or 'voice' as a value.
     * @param string $countryCode
     *   ISO Country Code, 2 Digit.
     * @param string $langCode
     *   ISO 639-1 Language Code: two-letter codes.
     *
     * @return object
     *   An object with server response.
     *   - status: Status of the request (sent/fail).
     *   - length: Registration code lenght.
     *   - method: Used method.
     *   - reason: Reason of the status (e.g. too_recent/missing_param/bad_param).
     *   - param: The missing_param/bad_param.
     *   - retry_after: Waiting time before requesting a new code.
     *
     * @throws Exception
     */
    public function codeRequest($method = 'sms', $carrier = "T-Mobile5", $countryCode = null, $langCode = null)
    {
        if (!$phone = $this->dissectPhone()) {
            throw new Exception('The provided phone number is not valid.');
        }

        if ($countryCode == null && $phone['ISO3166'] != '') {
            $countryCode = $phone['ISO3166'];
        }
        if ($countryCode == null) {
            $countryCode = 'US';
        }
        if ($langCode == null && $phone['ISO639'] != '') {
            $langCode = $phone['ISO639'];
        }
        if ($langCode == null) {
            $langCode = 'en';
        }

        if($carrier != null)
          $mnc = $this->detectMnc(strtolower($countryCode), $carrier);
        else
          $mnc = $phone['mnc'];

        // Build the token.
        $token = generateRequestToken($phone['country'], $phone['phone']);

        // Build the url.
        $host = 'https://' . static::WHATSAPP_REQUEST_HOST;
        $query = array(
            'in' => $phone['phone'],
            'cc' => $phone['cc'],
            'id' => $this->identity,
            'lg' => $langCode,
            'lc' => $countryCode,
          //'mcc' => '000',
          //'mnc' => '000',
            'sim_mcc' => $phone['mcc'],
            'sim_mnc' => $mnc,
            'method' => $method,
          //'reason' => "self-send-jailbroken",
            'token' => $token,
          //'network_radio_type' => "1"
        );

        if ($this->debug) {
            print_r($query);
        }

        $response = $this->getResponse($host, $query);

        if ($this->debug) {
            print_r($response);
        }

        if ($response->status == 'ok') {
            $this->eventManager()->fire("onCodeRegister",
                array(
                    $this->phoneNumber,
                    $response->login,
                    $response->pw,
                    $response->type,
                    $response->expiration,
                    $response->kind,
                    $response->price,
                    $response->cost,
                    $response->currency,
                    $response->price_expiration
                ));
        } else if ($response->status != 'sent') {
            if (isset($response->reason) && $response->reason == "too_recent") {
                $this->eventManager()->fire("onCodeRequestFailedTooRecent",
                    array(
                        $this->phoneNumber,
                        $method,
                        $response->reason,
                        $response->retry_after
                    ));
                $minutes = round($response->retry_after / 60);
                throw new Exception("Code already sent. Retry after $minutes minutes.");

            } else if (isset($response->reason) && $response->reason == "too_many_guesses") {
              $this->eventManager()->fire("onCodeRequestFailedTooManyGuesses",
              array(
                $this->phoneNumber,
                $method,
                $response->reason,
                $response->retry_after
              ));
              $minutes = round($response->retry_after / 60);
              throw new Exception("Too many guesses. Retry after $minutes minutes.");

          }  else {
                $this->eventManager()->fire("onCodeRequestFailed",
                    array(
                        $this->phoneNumber,
                        $method,
                        $response->reason,
                        $response->param
                    ));
                throw new Exception('There was a problem trying to request the code.');
            }
        } else {
            $this->eventManager()->fire("onCodeRequest",
                array(
                    $this->phoneNumber,
                    $method,
                    $response->length
                ));
        }

        return $response;
    }

    /**
     * Connect (create a socket) to the WhatsApp network.
     * @return bool
     */
    public function connect()
    {
        //If we have already connected AND the socket has not been closed from the remote side - then
        //no need to connect again.
        //WARNING: Lots of bugs in PHP's detection of socket timeout/remote disconnect. Be careful changing this code.
        //http://ie2.php.net/manual/en/function.socket-read.php#115903
        //https://bugs.php.net/bug.php?id=47918
        //http://stackoverflow.com/questions/20334366/php-fsockopen-how-to-know-if-connection-is-alive
        if ($this->isConnected()) {
            return true;
        }

        $WAver = trim(file_get_contents(static::WHATSAPP_VER_CHECKER));

        $WAverS = str_replace(".","",$WAver);
        $ver = str_replace(".","",static::WHATSAPP_VER);

      //  if($ver<$WAverS)
      //  {
      //    $classesMD5 = file_get_contents('https://coderus.openrepos.net/whitesoft/whatsapp_classes');
      //
      //    updateData('token.php', $WAver, $classesMD5);
      //    updateData('whatsprot.class.php', $WAver);
      //  }

        /* Create a TCP/IP socket. */
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket !== false) {
            $result = socket_connect($socket, static::WHATSAPP_HOST, static::PORT);
            if ($result === false) {
                $socket = false;
            }
        }

        if ($socket !== false) {

            socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => static::TIMEOUT_SEC, 'usec' => static::TIMEOUT_USEC));
            socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => static::TIMEOUT_SEC, 'usec' => static::TIMEOUT_USEC));

            $this->socket = $socket;
            $this->eventManager()->fire("onConnect",
                array(
                    $this->phoneNumber,
                    $this->socket
                )
            );
            return true;
        } else {
            if ($this->debug) {
                print_r("Firing onConnectError\n");
            }
            $this->eventManager()->fire("onConnectError",
                array(
                    $this->phoneNumber,
                    $this->socket
                )
            );
            return false;
        }
    }

    /**
     * Do we have an active socket connection to whatsapp?
     * @return bool
     */
    public function isConnected()
    {
        if($this->socket === null) {
            return false;
        }
        return true;
    }


    /**
     * Disconnect from the WhatsApp network.
     */
    public function disconnect()
    {
        if (is_resource($this->socket)) {
            socket_close($this->socket);
            $this->socket = null;
            $this->eventManager()->fire("onDisconnect",
                array(
                    $this->phoneNumber,
                    $this->socket
                )
            );
        }
    }

    /**
     * @return WhatsApiEventsManager
     */
    public function eventManager()
    {
        return $this->eventManager;
    }

    /**
     * Drain the message queue for application processing.
     *
     * @return ProtocolNode[]
     *   Return the message queue list.
     */
    public function getMessages()
    {
        $ret = $this->messageQueue;
        $this->messageQueue = array();

        return $ret;
    }

    /**
     * Log into the Whatsapp server.
     *
     * ###Warning### using this method will generate a new password
     * from the WhatsApp servers each time.
     *
     * If you know your password and wish to use it without generating
     * a new password - use the loginWithPassword() method instead.
     */
    public function login()
    {
        $this->accountInfo = (array) $this->checkCredentials();
        if ($this->accountInfo['status'] == 'ok') {
            if ($this->debug) {
                print_r("New password received: " . $this->accountInfo['pw'] . "\n");
            }
            $this->password = $this->accountInfo['pw'];
        }
        $this->doLogin();
    }

    /**
     * Login to the Whatsapp server with your password
     *
     * If you already know your password you can log into the Whatsapp server
     * using this method.
     *
     * @param  string  $password         Your whatsapp password. You must already know this!
     */
    public function loginWithPassword($password)
    {
        $this->password = $password;
        if(is_readable($this->challengeFilename)) {
            $challengeData = file_get_contents($this->challengeFilename);
            if($challengeData)
                $this->challengeData = $challengeData;
        }
        $this->doLogin();
    }

    /**
     * Fetch a single message node
     * @param bool $autoReceipt
     * @return bool
     */
    public function pollMessage($autoReceipt = true, $type = "read")
    {
      if(!$this->isConnected()) {
        throw new ConnectionException('Connection Closed!');
      }

      $r = array($this->socket);
      $w = array();
      $e = array();

      if (socket_select($r, $w, $e, static::TIMEOUT_SEC, static::TIMEOUT_USEC)) {
        // Something to read
        if ($stanza = $this->readStanza()) {
          $this->processInboundData($stanza, $autoReceipt, $type);
          return true;
        }
      }

      return false;
    }

    /**
     * Send the active status. User will show up as "Online" (as long as socket is connected).
     */
    public function sendActiveStatus()
    {
        $messageNode = new ProtocolNode("presence", array("type" => "active"), null, "");
        $this->sendNode($messageNode);
    }

    /**
    * Send a request to get cipher keys from an user
    *
    * @param $number
    *    Phone number of the user you want to get the cipher keys.
    */
    public function sendGetCipherKeysFromUser($number)
    {
        $msgId = $this->createMsgId("cipher_keys_");

        $userNode = new ProtocolNode("user", array(
          "jid" => $this->getJID($number)
        ), null, null);
        $keyNode = new ProtocolNode("key", null, array($userNode), null);
        $node = new ProtocolNode("iq", array(
          "id" => $msgId,
          "xmlns" => "encrypt",
          "type" => "get",
          "to" => static::WHATSAPP_SERVER
        ), array($keyNode), null);

        $this->sendNode($node);
    }

    /**
     * Send a Broadcast Message with audio.
     *
     * The receiptiant MUST have your number (synced) and in their contact list
     * otherwise the message will not deliver to that person.
     *
     * Approx 20 (unverified) is the maximum number of targets
     *
     * @param array  $targets       An array of numbers to send to.
     * @param string $path          URL or local path to the audio file to send
     * @param bool   $storeURLmedia Keep a copy of the audio file on your server
     * @param int    $fsize
     * @param string $fhash
     */
    public function sendBroadcastAudio($targets, $path, $storeURLmedia = false, $fsize = 0, $fhash = "")
    {
        if (!is_array($targets)) {
            $targets = array($targets);
        }
        $this->sendMessageAudio($targets, $path, $storeURLmedia, $fsize, $fhash);
    }

    /**
     * Send a Broadcast Message with an image.
     *
     * The receiptiant MUST have your number (synced) and in their contact list
     * otherwise the message will not deliver to that person.
     *
     * Approx 20 (unverified) is the maximum number of targets
     *
     * @param array  $targets       An array of numbers to send to.
     * @param string $path          URL or local path to the image file to send
     * @param bool   $storeURLmedia Keep a copy of the audio file on your server
     * @param int    $fsize
     * @param string $fhash
     * @param string $caption
     */
    public function sendBroadcastImage($targets, $path, $storeURLmedia = false, $fsize = 0, $fhash = "", $caption = "")
    {
        if (!is_array($targets)) {
            $targets = array($targets);
        }
        $this->sendMessageImage($targets, $path, $storeURLmedia, $fsize, $fhash, $caption);
    }

    /**
     * Send a Broadcast Message with location data.
     *
     * The receiptiant MUST have your number (synced) and in their contact list
     * otherwise the message will not deliver to that person.
     *
     * If no name is supplied , receiver will see large sized google map
     * thumbnail of entered Lat/Long but NO name/url for location.
     *
     * With name supplied, a combined map thumbnail/name box is displayed

     * Approx 20 (unverified) is the maximum number of targets
     *
     * @param  array  $targets       An array of numbers to send to.
     * @param  float $long    The longitude of the location eg 54.31652
     * @param  float $lat     The latitude if the location eg -6.833496
     * @param  string $name    (Optional) A name to describe the location
     * @param  string $url     (Optional) A URL to link location to web resource
     */


    public function sendBroadcastLocation($targets, $long, $lat, $name = null, $url = null)
    {
        if (!is_array($targets)) {
            $targets = array($targets);
        }
        $this->sendMessageLocation($targets, $long, $lat, $name, $url);
    }

    /**
     * Send a Broadcast Message
     *
     * The receiptiant MUST have your number (synced) and in their contact list
     * otherwise the message will not deliver to that person.
     *
     * Approx 20 (unverified) is the maximum number of targets
     *
     * @param  array  $targets       An array of numbers to send to.
     * @param  string $message Your message
     */
    public function sendBroadcastMessage($targets, $message)
    {
    	$message = $this->parseMessageForEmojis($message);
        $bodyNode = new ProtocolNode("body", null, null, $message);
        $this->sendBroadcast($targets, $bodyNode, "text");
    }

    /**
     * Send a Broadcast Message with a video.
     *
     * The receiptiant MUST have your number (synced) and in their contact list
     * otherwise the message will not deliver to that person.
     *
     * Approx 20 (unverified) is the maximum number of targets
     *
     * @param array   $targets       An array of numbers to send to.
     * @param string  $path          URL or local path to the video file to send
     * @param bool    $storeURLmedia Keep a copy of the audio file on your server
     * @param int     $fsize
     * @param string  $fhash
     * @param string  $caption
     */
    public function sendBroadcastVideo($targets, $path, $storeURLmedia = false, $fsize = 0, $fhash = "", $caption = "")
    {
        if (!is_array($targets)) {
            $targets = array($targets);
        }
        $this->sendMessageVideo($targets, $path, $storeURLmedia, $fsize, $fhash, $caption);
    }

    /**
     * Delete Broadcast lists
     *
     * @param  string array $lists
     * Contains the broadcast-id list
     */
    public function sendDeleteBroadcastLists($lists)
    {
        $msgId = $this->createMsgId("delete_list_");
        $listNode = array();
        if($lists != null && count($lists) > 0)
        {
          for($i = 0; $i < count($lists); $i++)
          {
            $listNode[$i] = new ProtocolNode("list", array("id" => $lists[$i]), null, null);
          }
        }else{
          $listNode = null;
        }
        $deleteNode = new ProtocolNode("delete", null, $listNode, null);
        $node = new ProtocolNode("iq", array(
          "id" => $msgId,
          "xmlns" => "w:b",
          "type" => "set",
          "to" => "s.whatsapp.net"
        ), array($deleteNode), null);

        $this->sendNode($node);
    }

    /**
     * Clears the "dirty" status on your account
     *
     * @param  array $categories
     */
    protected function sendClearDirty($categories)
    {
        $msgId = $this->createMsgId("cleardirty");

        $catnodes = array();
        foreach ($categories as $category) {
            $catnode = new ProtocolNode("clean", array("type" => $category), null, null);
            $catnodes[] = $catnode;
        }
        $node = new ProtocolNode("iq", array(
            "id" => $msgId,
            "type" => "set",
            "to" => "s.whatsapp.net",
            "xmlns" => "urn:xmpp:whatsapp:dirty"
                ), $catnodes, null);
        $this->sendNode($node);
    }

    public function sendClientConfig()
    {
        $phone = $this->dissectPhone();

        $attr = array();
        $attr["platform"] = static::WHATSAPP_DEVICE;
        $attr["version"] = static::WHATSAPP_VER;
        $child = new ProtocolNode("config", $attr, null, "");
        $node = new ProtocolNode("iq", array("id" => $this->createMsgId("config"), "type" => "set", "xmlns" => "urn:xmpp:whatsapp:push", "to" => static::WHATSAPP_SERVER), array($child), null);
        $this->sendNode($node);
    }

    public function sendGetClientConfig()
    {
        $msgId = $this->createMsgId("sendconfig");
        $child = new ProtocolNode("config", null, null, null);
        $node = new ProtocolNode("iq", array(
            "id" => $msgId,
            "xmlns" => "urn:xmpp:whatsapp:push",
            "type" => "get",
            "to" => static::WHATSAPP_SERVER
                ), array($child), null);
        $this->sendNode($node);
        $this->waitForServer($msgId);
    }

    /**
    * Transfer your number to new one
    *
    * @param  string  $number
    * @param  string  $identity
    */
    public function sendChangeNumber($number, $identity)
    {
      $msgId = $this->createMsgId("change_number");

      $usernameNode = new ProtocolNode("username", null, null, $number);
      $passwordNode = new ProtocolNode("password", null, null, urldecode($identity));

      $modifyNode = new ProtocolNode("modify", null, array($usernameNode, $passwordNode), null);

      $iqNode = new ProtocolNode("iq", array(
        "xmlns" => "urn:xmpp:whatsapp:account",
        "id" => $msgId,
        "type" => "get",
        "to" => "c.us"
      ), array($modifyNode), null);

      $this->sendNode($iqNode);
    }

    /**
     * Send a request to return a list of groups user is currently participating
     * in.
     *
     * To capture this list you will need to bind the "onGetGroups" event.
     */
    public function sendGetGroups()
    {
        $this->sendGetGroupsFiltered("participating");
    }

    /**
    * Send a request to get new Groups V2 info
    *
    * @param $groupID
    *    The group JID
    */
    public function sendGetGroupV2Info($groupID)
    {
        $msgId = $this->createMsgId("get_groupv2_info");

        $queryNode = new ProtocolNode("query", array("request" => "interactive"), null, null);
        $node = new ProtocolNode("iq", array(
          "id" => $msgId,
          "xmlns" => "w:g2",
          "type" => "get",
          "to" => $this->getJID($groupID)
        ), array($queryNode), null);

        $this->sendNode($node);
    }

    /**
     * Send a request to return a list of groups user has started
     * in.
     *
     * To capture this list you will need to bind the "onGetGroups" event.
     */
    public function sendGetGroupsOwning()
    {
        $this->sendGetGroupsFiltered("owning");
    }

    /**
     * Send a request to get a list of people you have currently blocked
     */
    public function sendGetPrivacyBlockedList()
    {
        $msgId = $this->createMsgId("getprivacy");
        $child = new ProtocolNode("list", array(
            "name" => "default"
                ), null, null);
        $child2 = new ProtocolNode("query", array(), array($child), null);
        $node = new ProtocolNode("iq", array(
            "id" => $msgId,
            "xmlns" => "jabber:iq:privacy",
            "type" => "get"
                ), array($child2), null);
        $this->sendNode($node);
        $this->waitForServer($msgId);
    }

    /**
     * Send a request to get privacy settings
     */
    public function sendGetPrivacySettings()
    {
      $msgId = $this->createMsgId("get_privacy_settings_");
      $privacyNode = new ProtocolNode("privacy", null, null, null);
      $node = new ProtocolNode("iq", array(
        "to" => static::WHATSAPP_SERVER,
        "id" => $msgId,
        "xmlns" => "privacy",
        "type" => "get"
      ), array($privacyNode), null);

      $this->sendNode($node);
      $this->waitForServer($msgId);
    }

   /**
    * Set privacy of 'last seen', status or profile picture
    * to All, contacts or none
    *
    * @param string $category
    *   Options: 'last', 'status' or 'profile'
    * @param string $value
    *   Options: 'all', 'contacts' or 'none'
    */
    public function sendSetPrivacySettings($category, $value)
    {
      $msgId = $this->createMsgId("send_privacy_settings_");
      $categoryNode = new ProtocolNode("category", array(
        "name" => $category,
        "value" => $value
      ), null, null);
      $privacyNode = new ProtocolNode("privacy", null, array($categoryNode), null);
      $node = new ProtocolNode("iq", array(
        "to" => static::WHATSAPP_SERVER,
        "type" => "set",
        "id" => $msgId,
        "xmlns" => "privacy"
      ), array($privacyNode), null);

      $this->sendNode($node);
      $this->waitForServer($msgId);
    }

    /**
     * Get profile picture of specified user
     *
     * @param string $number
     *  Number or JID of user
     *
     * @param bool $large
     *  Request large picture
     */
    public function sendGetProfilePicture($number, $large = false)
    {
        $hash = array();
        $hash["type"] = "image";
        if (!$large) {
            $hash["type"] = "preview";
        }
        $picture = new ProtocolNode("picture", $hash, null, null);

        $hash = array();
        $hash["id"] = $this->createMsgId("getpicture");
        $hash["type"] = "get";
        $hash["xmlns"] = "w:profile:picture";
        $hash["to"] = $this->getJID($number);
        $node = new ProtocolNode("iq", $hash, array($picture), null);
        $this->sendNode($node);
        $this->waitForServer($hash["id"]);
    }

    /**
    *
    * @param string array $numbers
    *  Numbers of your contacts
    */
    public function sendGetProfilePhotoIds($numbers)
    {

        if(!is_array($numbers))
        {
            $numbers = array($numbers);
        }

        $msgId = $this->createMsgId("get_picture_ids");

        $i = 0;
        for($i; $i<count($numbers); $i++)
        {
            $userNode[$i] = new ProtocolNode("user", array("jid" => $this->getJID($numbers[$i])), null, null);
        }

        $listNode = new ProtocolNode("list", null, $userNode, null);

        $iqNode = new ProtocolNode("iq", array(
          "id" => $msgId,
          "xmlns" => "w:profile:picture",
          "type" => "get"
        ), array($listNode), null);

        $this->sendNode($iqNode);
    }

    /**
     * Request to retrieve the last online time of specific user.
     *
     * @param string $to
     *  Number or JID of user
     */
    public function sendGetRequestLastSeen($to)
    {
        $queryNode = new ProtocolNode("query", null, null, null);

        $messageHash = array();
        $messageHash["to"] = $this->getJID($to);
        $messageHash["type"] = "get";
        $messageHash["id"] = $this->createMsgId("lastseen");
        $messageHash["xmlns"] = "jabber:iq:last";

        $messageNode = new ProtocolNode("iq", $messageHash, array($queryNode), "");
        $this->sendNode($messageNode);
        $this->waitForServer($messageHash["id"]);
    }

    /**
     * Send a request to get the current server properties
     */
    public function sendGetServerProperties()
    {
        $id = $this->createMsgId("getproperties");
        $child = new ProtocolNode("props", null, null, null);
        $node = new ProtocolNode("iq", array(
            "id" => $id,
            "type" => "get",
            "xmlns" => "w",
            "to" => "s.whatsapp.net"
                ), array($child), null);
        $this->sendNode($node);
    }

	/**
	* Send a request to get the current service pricing
	*
	*  @param string $lg
	*   Language
	*  @param string $lc
	*   Country
	*/
	public function sendGetServicePricing($lg, $lc)
	{
		$msgId = $this->createMsgId("get_service_pricing_");
		$pricingNode = new ProtocolNode("pricing", array(
			"lg" => $lg,
			"lc" => $lc
		), null, null);
		$node = new ProtocolNode("iq", array(
			"id" => $msgId,
			"xmlns" => "urn:xmpp:whatsapp:account",
			"type" => "get",
			"to" => "s.whatsapp.net"
			), array($pricingNode), null);
		$this->sendNode($node);
	}

	/**
	* Send a request to extend the account
	*/
	public function sendExtendAccount()
	{
		$msgId = $this->createMsgId("extend_account_");
		$extendingNode = new ProtocolNode("extend", null, null, null);
		$node = new ProtocolNode("iq", array(
			"id" => $msgId,
			"xmlns" => "urn:xmpp:whatsapp:account",
			"type" => "set",
			"to" => "s.whatsapp.net"
			), array($extendingNode), null);
		$this->sendNode($node);
	}

  /**
	* Gets all the broadcast lists for an account
	*/
	public function sendGetBroadcastLists()
	{
		$msgId = $this->createMsgId("get_lists_");
		$listsNode = new ProtocolNode("lists", null, null, null);
		$node = new ProtocolNode("iq", array(
			"id" => $msgId,
			"xmlns" => "w:b",
			"type" => "get",
			"to" => "s.whatsapp.net"
			), array($listsNode), null);
		$this->sendNode($node);
	}

	/**
	* Send a request to get the normalized mobile number respresenting the JID
	*
	*  @param string $countryCode
	*   Contry Code
	*  @param string $number
	*   Mobile Number
	*/
	public function sendGetNormalizedJid($countryCode, $number)
	{
		$msgId = $this->createMsgId("get_normalized_jid_");
		$ccNode = new ProtocolNode("cc", null, null, $countryCode);
		$inNode = new ProtocolNode("in", null, null, $number);
		$normalizeNode = new ProtocolNode("normalize", null, array($ccNode, $inNode), null);
		$node = new ProtocolNode("iq", array(
			"id" => $msgId,
			"xmlns" => "urn:xmpp:whatsapp:account",
			"type" => "get",
			"to" => "s.whatsapp.net"
			), array($normalizeNode), null);
		$this->sendNode($node);
	}

  /**
  * Removes an account from WhatsApp
  *
  * @param string $lg
  * Language
  * @param string $lc
  * Country
  * @param string $feedback
  * User Feedback
  */
  public function sendRemoveAccount($lg = null, $lc = null, $feedback = null)
  {
    $msgId = $this->createMsgId("removeaccount_");
    if($feedback != null && strlen($feedback) > 0)
    {
        if($lg == null) {
            $lg = "";
        }

        if($lc == null) {
            $lc = "";
        }

        $child = new ProtocolNode("body", array(
            "lg" => $lg,
            "lc" => $lc
        ), null, $feedback);
        $childNode = array($child);
    }else{
        $childNode = null;
    }

    $removeNode = new ProtocolNode("remove", null, $childNode, null);
    $node = new ProtocolNode("iq", array(
        "to" => "s.whatsapp.net",
        "xmlns" => "urn:xmpp:whatsapp:account",
        "type" => "get",
        "id" => $msgId
    ), array($removeNode), null);

    $this->sendNode($node);
  }

  /**
  * Send a ping to the server
  */
	public function sendPing()
  {
    $msgId = $this->createMsgId("ping_");
    $pingNode = new ProtocolNode("ping", null, null, null);
    $node = new ProtocolNode("iq", array(
      "id" => $msgId,
      "xmlns" => "w:p",
      "type" => "get",
      "to" => "s.whatsapp.net"
    ), array($pingNode), null);
    $this->sendNode($node);
  }

  /**
  * Get voip info. of a number/s
  *
  * @param string $jids
  */
  public function sendGetHasVoipEnabled($jids)
  {
    $msgId = $this->createMsgId("voip_");

    if(!is_array($jids))
    {
      $jids = array($jids);
    }
    $userNode = array();
    foreach($jids as $jid)
    {
      $userNode[] = new ProtocolNode("user", array('jid' => $this->getJID($jid)), null, null);
    }

    $eligibleNode = new ProtocolNode("eligible", null, $userNode, null);
    $node = new ProtocolNode("iq", array(
      "id" => $msgId,
      "xmlns" => "voip",
      "type" => "get",
      "to" => static::WHATSAPP_SERVER
    ), array($eligibleNode), null);
  $this->sendNode($node);
  }

    /**
     * Get the current status message of a specific user.
     *
     * @param  string[] $jids The users' JIDs
     */
    public function sendGetStatuses($jids)
    {
        if(!is_array($jids))
        {
            $jids = array($jids);
        }
        $children = array();
        foreach($jids as $jid)
        {
            $children[] = new ProtocolNode("user", array("jid" => $this->getJID($jid)), null, null);
        }
        $node = new ProtocolNode("iq", array(
            "to" => "s.whatsapp.net",
            "type" => "get",
            "xmlns" => "status",
            "id" => $this->createMsgId("getstatus")
        ), array(
            new ProtocolNode("status", null, $children, null)
        ), null);
        $this->sendNode($node);
    }

    /**
     * Create a group chat.
     *
     * @param string $subject
     *   The group Subject
     * @param array $participants
     *   An array with the participants numbers.
     *
     * @return string
     *   The group ID.
     */
     public function sendGroupsChatCreate($subject, $participants)
     {
       if (!is_array($participants)) {
         $participants = array($participants);
       }
       foreach ($participants as $participant)
       {
         $participantNode[] = new ProtocolNode("participant", array(
           "jid" => $this->getJID($participant)
         ), null, null);
       }

       $id = $this->createMsgId("creategroup");

       $createNode = new ProtocolNode("create", array(
         "subject" => $subject
       ), $participantNode, null);

       $iqNode = new ProtocolNode("iq", array(
         "xmlns" => "w:g2",
         "id" => $id,
         "type" => "set",
         "to" => "g.us"
       ), array($createNode), null);

       $this->sendNode($iqNode);
       $this->waitForServer($id);
       $groupId = $this->groupId;

       $this->eventManager()->fire("onGroupCreate",
       array(
         $this->phoneNumber,
         $groupId
       ));

       return $groupId;
     }

     /**
     * Change group subject
     *
     * @param string $gjid
     *   The group id
     * @param string $subject
     *   The subject
     */
    public function sendSetGroupSubject($gjid, $subject)
    {
        $child = new ProtocolNode("subject", null, null, $subject);
        $node = new ProtocolNode("iq", array(
            "id" => $this->createMsgId("set_group_subject"),
            "type" => "set",
            "to" => $this->getJID($gjid),
            "xmlns" => "w:g2"
        ), array($child), null);
        $this->sendNode($node);
    }

    /**
     * End or delete a group chat
     *
     * @param  string $gjid The group ID
     */
    public function sendGroupsChatEnd($gjid)
    {
        $gjid = $this->getJID($gjid);
        $msgID = $this->createMsgId("endgroup");

        $groupData = array();
        $groupData['id'] = $gjid;
        $groupNode = new ProtocolNode('group', $groupData, null, null);

        $leaveData = array();
        $leaveData["action"] = "delete";
        $leaveNode = new ProtocolNode("leave", $leaveData, array($groupNode), null);

        $iqData = array();
        $iqData["id"] = $msgID;
        $iqData["type"] = "set";
        $iqData["xmlns"] = "w:g2";
        $iqData["to"] = static::WHATSAPP_GROUP_SERVER;
        $iqNode = new ProtocolNode("iq", $iqData, array($leaveNode), null);

        $this->sendNode($iqNode);
        $this->waitForServer($msgID);
    }

    /**
     * Leave a group chat
     *
     * @param  array $gjids An array of group IDs
     */
    public function sendGroupsLeave($gjids)
    {
        if (!is_array($gjids)) {
            $gjids = array($this->getJID($gjids));
        }
        $nodes = array();
        foreach ($gjids as $gjid) {
            $nodes[] = new ProtocolNode("group", array("id" => $this->getJID($gjid)), null, null);
        }
        $leave = new ProtocolNode("leave", array('action'=>'delete'), $nodes, null);
        $hash = array();
        $hash["id"] = $this->createMsgId("leavegroups");
        $hash["to"] = static::WHATSAPP_GROUP_SERVER;
        $hash["type"] = "set";
        $hash["xmlns"] = "w:g2";
        $node = new ProtocolNode("iq", $hash, array($leave), null);
        $this->sendNode($node);
        $this->waitForServer($hash["id"]);
    }

    /**
     * Add participant(s) to a group.
     *
     * @param string $groupId
     *   The group ID.
     * @param array $participants
     *   An array with the participants numbers to add
     */
    public function sendGroupsParticipantsAdd($groupId, $participants)
    {
        $msgId = $this->createMsgId("add_group_participants_");
        if(!is_array($participants)) {
            $participants = array($participants);
        }
        $this->sendGroupsChangeParticipants($groupId, $participants, 'add', $msgId);
    }

    /**
     * Remove participant(s) from a group.
     *
     * @param string $groupId
     *   The group ID.
     * @param array $participants
     *   An array with the participants numbers to remove
     */
    public function sendGroupsParticipantsRemove($groupId, $participants)
    {
        $msgId = $this->createMsgId("remove_group_participants_");
        if(!is_array($participants)) {
            $participants = array($participants);
        }
        $this->sendGroupsChangeParticipants($groupId, $participants, 'remove', $msgId);
    }

    /**
     * Promote participant(s) of a group
     * Makes a participant admin of a group
     *
     * @param string $gId          The group ID.
     * @param array  $participants An array with the participants numbers to promote
     */
    public function sendPromoteParticipants($gId, $participants)
    {
      $msgId = $this->createMsgId("promote_group_participants_");
      if(!is_array($participants)) {
          $participants = array($participants);
      }
      $this->sendGroupsChangeParticipants($gId, $participants, "promote", $msgId);
    }

    /**
     * Demote participant(s) of a group.
     * Remove participant of being admin of a group.
     *
     * @param string $gId          The group ID.
     * @param array  $participants An array with the participants numbers to demote
     */
    public function sendDemoteParticipants($gId, $participants)
    {
      $msgId = $this->createMsgId("demote_group_participants_");
      if(!is_array($participants)) {
          $participants = array($participants);
      }
      $this->sendGroupsChangeParticipants($gId, $participants, "demote", $msgId);
    }

   /**
    * Lock group: Paritipants cant change group subject or
    *             profile picture except admin
    *
    * @param string $gId The group ID.
    */
    public function sendLockGroup($gId)
    {
      $msgId = $this->createMsgId("lock_group_");
      $lockedNode = new ProtocolNode("locked", null, null, null);
      $node = new ProtocolNode("iq", array(
        "id" => $msgId,
        "xmlns" => "w:g2",
        "type" => "set",
        "to" => $this->getJID($gId)
      ), array($lockedNode), null);

      $this->sendNode($node);
      $this->waitForServer($msgId);
    }

    /**
     * Unlock group: Any participant can change
     *               group subject or profile picture
     *
     *
     * @param string $gId The group ID.
     */
    public function sendUnlockGroup($gId)
    {
      $msgId = $this->createMsgId("unlock_group_");
      $unlockedNode = new ProtocolNode("unlocked", null, null, null);
      $node = new ProtocolNode("iq", array(
        "id" => $msgId,
        "xmlns" => "w:g2",
        "type" => "set",
        "to" => $this->getJID($gId)
      ), array($unlockedNode), null);

      $this->sendNode($node);
      $this->waitForServer($msgId);
    }

    /**
     * Send a text message to the user/group.
     *
     * @param $to
     *   The recipient.
     * @param string $txt
     *   The text message.
     * @param $id
     *
     * @return string
     */
    public function sendMessage($to, $txt, $id = null)
    {
        $txt = $this->parseMessageForEmojis($txt);
        $bodyNode = new ProtocolNode("body", null, null, $txt);
        $id = $this->sendMessageNode($to, $bodyNode, $id);
        $this->waitForServer($id);

        if ($this->messageStore !== null) {
            $this->messageStore->saveMessage($this->phoneNumber, $to, $txt, $id, time());
        }

        return $id;
    }

    /**
     * Send a read receipt to a message
     *
     * @param $to
     *   The recipient.
     * @param $id
     */
    public function sendMessageRead($to, $id)
    {
        $messageHash = array();
        $messageHash["type"] = "read";
        $messageHash["to"] = $to;
        $messageHash["id"] = $id;
        $messageHash["t"] = time();
        $messageNode = new ProtocolNode("receipt", $messageHash, null, null);
        $this->sendNode($messageNode);
    }

    /**
     * Send audio to the user/group.
     *
     * @param string $to            The recipient.
     * @param string $filepath      The url/uri to the audio file.
     * @param bool   $storeURLmedia Keep copy of file
     * @param int    $fsize
     * @param string $fhash
     * @return bool
     */
    public function sendMessageAudio($to, $filepath, $storeURLmedia = false, $fsize = 0, $fhash = "")
    {
    	if ($fsize==0 || $fhash == "")
    	{
        	$allowedExtensions = array('3gp', 'caf', 'wav', 'mp3', 'wma', 'ogg', 'aif', 'aac', 'm4a');
        	$size = 10 * 1024 * 1024; // Easy way to set maximum file size for this media type.
        	return $this->sendCheckAndSendMedia($filepath, $size, $to, 'audio', $allowedExtensions, $storeURLmedia);
        }
        else{
    		$this->sendRequestFileUpload($fhash, 'audio', $fsize, $filepath, $to);
    		return true;
    	}
    }

    /**
     * Send the composing message status. When typing a message.
     *
     * @param string $to
     *   The recipient to send status to.
     */
    public function sendMessageComposing($to)
    {
        $this->sendChatState($to, "composing");
    }

    /**
     * Send an image file to group/user
     *
     * @param string $to            Recipient number
     * @param string $filepath      The url/uri to the image file.
     * @param bool   $storeURLmedia Keep copy of file
     * @param int    $fsize         size of the media file
     * @param string $fhash         base64 hash of the media file
     * @param string $caption
     * @return bool
     */
    public function sendMessageImage($to, $filepath, $storeURLmedia = false, $fsize = 0, $fhash = "", $caption = "")
    {
      $caption = $this->parseMessageForEmojis($caption);
    	if ($fsize==0 || $fhash == "")
    	{
        	$allowedExtensions = array('jpg', 'jpeg', 'gif', 'png');
        	$size = 5 * 1024 * 1024; // Easy way to set maximum file size for this media type.
        	return $this->sendCheckAndSendMedia($filepath, $size, $to, 'image', $allowedExtensions, $storeURLmedia, $caption);
        }
        else{
        $this->sendRequestFileUpload($fhash, 'image', $fsize, $filepath, $to, $caption);
    	return true;
    	}
    }

    /**
     * Send a location to the user/group.
     *
     * If no name is supplied , receiver will see large sized google map
     * thumbnail of entered Lat/Long but NO name/url for location.
     *
     * With name supplied, a combined map thumbnail/name box is displayed
     *
     * @param array|string $to The recipient(s) to send to.
     * @param  float $long    The longitude of the location eg 54.31652
     * @param  float $lat     The latitude if the location eg -6.833496
     * @param string $name (Optional)  The custom name you would like to give this location.
     * @param string $url (Optional) A URL to attach to the location.
     */
    public function sendMessageLocation($to, $long, $lat, $name = null, $url = null)
    {
        $mediaHash = array();
        $mediaHash['type'] = "location";
        $mediaHash['encoding'] = "raw";
        $mediaHash['latitude'] = $lat;
        $mediaHash['longitude'] = $long;
        $mediaHash['name'] = $name;
        $mediaHash['url'] = $url;

        $mediaNode = new ProtocolNode("media", $mediaHash, null, null);

        if (is_array($to)) {
            $id = $this->sendBroadcast($to, $mediaNode, "media");
        } else {
            $id = $this->sendMessageNode($to, $mediaNode);
        }
        $this->waitForServer($id);
    }

    /**
     * Send the 'paused composing message' status.
     *
     * @param string $to
     *   The recipient number or ID.
     */
    public function sendMessagePaused($to)
    {
        $this->sendChatState($to, "paused");
    }

    protected function sendChatState($to, $state)
    {
        $node = new ProtocolNode("chatstate", array("to" => $this->getJID($to)), array(new ProtocolNode($state, null, null, null)), null);
        $this->sendNode($node);
    }

    /**
     * Send a video to the user/group.
     *
     * @param string $to            The recipient to send.
     * @param string $filepath      The url/uri to the MP4/MOV video.
     * @param bool   $storeURLmedia Keep a copy of media file.
     * @param int    $fsize         size of the media file
     * @param string $fhash         base64 hash of the media file
     * @param string $caption
     *
     * @return bool
     */
    public function sendMessageVideo($to, $filepath, $storeURLmedia = false, $fsize = 0, $fhash = "", $caption = "")
    {
      $caption = $this->parseMessageForEmojis($caption);
    	if ($fsize==0 || $fhash == "")
    	{
        	$allowedExtensions = array('3gp', 'mp4', 'mov', 'avi');
        	$size = 20 * 1024 * 1024; // Easy way to set maximum file size for this media type.
        	return $this->sendCheckAndSendMedia($filepath, $size, $to, 'video', $allowedExtensions, $storeURLmedia, $caption);
        }
        else{
    		$this->sendRequestFileUpload($fhash, 'video', $fsize, $filepath, $to, $caption);
    		return true;
    	}
    }

    /**
     * Send the next message.
     */
    public function sendNextMessage()
    {
        if (count($this->outQueue) > 0) {
            $msgnode = array_shift($this->outQueue);
            $msgnode->refreshTimes();
            $this->lastId = $msgnode->getAttribute('id');
            $this->sendNode($msgnode);
        } else {
            $this->lastId = false;
        }
    }

    /**
     * Send the offline status. User will show up as "Offline".
     */
    public function sendOfflineStatus()
    {
        $messageNode = new ProtocolNode("presence", array("type" => "inactive"), null, "");
        $this->sendNode($messageNode);
    }

    /**
     * Send a pong to the whatsapp server. I'm alive!
     *
     * @param string $msgid
     *   The id of the message.
     */
    public function sendPong($msgid)
    {
        $messageHash = array();
        $messageHash["to"] = static::WHATSAPP_SERVER;
        $messageHash["id"] = $msgid;
        $messageHash["type"] = "result";

        $messageNode = new ProtocolNode("iq", $messageHash, null, "");
        $this->sendNode($messageNode);
        $this->eventManager()->fire("onSendPong",
            array(
                $this->phoneNumber,
                $msgid
            ));
    }

    public function sendAvailableForChat($nickname = null)
    {
        $presence = array();
        if($nickname)
        {
            //update nickname
            $this->name = $nickname;
        }
        $presence['name'] = $this->name;
        $node = new ProtocolNode("presence", $presence, null, "");
        $this->sendNode($node);
    }

    /**
     * Send presence status.
     *
     * @param string $type
     *   The presence status.
     */
    public function sendPresence($type = "active")
    {
        $presence = array();
        $presence['type'] = $type;
        $node = new ProtocolNode("presence", $presence, null, "");
        $this->sendNode($node);
        $this->eventManager()->fire("onSendPresence",
            array(
                $this->phoneNumber,
                $presence['type'],
                $this->name
            ));
    }

    /**
     * Send presence subscription, automatically receive presence updates as long as the socket is open.
     *
     * @param string $to
     *   Phone number.
     */
    public function sendPresenceSubscription($to)
    {
        $node = new ProtocolNode("presence", array("type" => "subscribe", "to" => $this->getJID($to)), null, "");
        $this->sendNode($node);
    }

    /**
    * Unsubscribe, will stop subscription
    *
    * @param string $to
    *   Phone number.
    */
    public function sendPresenceUnsubscription($to)
    {
      $node = new ProtocolNode("presence", array("type" => "unsubscribe", "to" => $this->getJID($to)), null, "");
      $this->sendNode($node);
    }

    /**
     * Set the picture for the group
     *
     * @param  string $gjid The groupID
     * @param  string $path The URL/URI of the image to use
     */
    public function sendSetGroupPicture($gjid, $path)
    {
        $this->sendSetPicture($gjid, $path);
    }

    /**
     * Set the list of numbers you wish to block receiving from.
     *
     * @param array $blockedJids Array of numbers to block messages from.
     */
    public function sendSetPrivacyBlockedList($blockedJids = array())
    {
        if (!is_array($blockedJids)) {
            $blockedJids = array($blockedJids);
        }
        $items = array();
        foreach ($blockedJids as $index => $jid) {
            $item = new ProtocolNode("item", array(
                "type" => "jid",
                "value" => $this->getJID($jid),
                "action" => "deny",
                "order" => $index + 1//WhatsApp stream crashes on zero index
                    ), null, null);
            $items[] = $item;
        }
        $child = new ProtocolNode("list", array("name" => "default"), $items, null);
        $child2 = new ProtocolNode("query", null, array($child), null);
        $node = new ProtocolNode("iq", array(
            "id" => $this->createMsgId("setprivacy"),
            "xmlns" => "jabber:iq:privacy",
            "type" => "set"
                ), array($child2), null);
        $this->sendNode($node);
    }

    /**
     * Set your profile picture
     *
     * @param  string $path URL/URI of image
     */
    public function sendSetProfilePicture($path)
    {
        $this->sendSetPicture($this->phoneNumber, $path);
    }

    /*
    *	Removes the profile photo
    */
	public function sendRemoveProfilePicture() {
		$picture = new ProtocolNode("picture", null, null, null);

		$thumb = new ProtocolNode("picture", array("type" => "preview"), null, null);

		$hash = array();
		$nodeID = $this->createMsgId("setphoto");
		$hash["id"] = $nodeID;
		$hash["to"] = $this->getJID($this->phoneNumber);
		$hash["type"] = "set";
		$hash["xmlns"] = "w:profile:picture";
		$node = new ProtocolNode("iq", $hash, array($picture, $thumb), null);

		$this->sendNode($node);
	}

    /**
     * Set the recovery token for your account to allow you to
     * retrieve your password at a later stage.
     * @param  string $token A user generated token.
     */
    public function sendSetRecoveryToken($token)
    {
        $child = new ProtocolNode("pin", array("xmlns" => "w:ch:p"), null, $token);
        $node = new ProtocolNode("iq", array(
            "id" => $this->createMsgId("settoken"),
            "type" => "set",
            "to" => "s.whatsapp.net"
                ), array($child), null);
        $this->sendNode($node);
    }

    /**
     * Update the user status.
     *
     * @param string $txt
     *   The text of the message status to send.
     */
    public function sendStatusUpdate($txt)
    {
        $child = new ProtocolNode("status", null, null, $txt);
        $node = new ProtocolNode("iq", array(
            "to" => "s.whatsapp.net",
            "type" => "set",
            "id" => $this->createMsgId("sendstatus"),
            "xmlns" => "status"
        ), array($child), null);

        $this->sendNode($node);
        $this->eventManager()->fire("onSendStatusUpdate",
            array(
                $this->phoneNumber,
                $txt
            ));
    }

    /**
     * Send a vCard to the user/group.
     *
     * @param $to
     *   The recipient to send.
     * @param $name
     *   The contact name.
     * @param $vCard
     *   The contact vCard to send.
     */
    public function sendVcard($to, $name, $vCard)
    {
        $vCardAttribs = array();
        $vCardAttribs['name'] = $name;
        $vCardNode = new ProtocolNode("vcard", $vCardAttribs, null, $vCard);

        $mediaAttribs = array();
        $mediaAttribs["type"] = "vcard";

        $mediaNode = new ProtocolNode("media", $mediaAttribs, array($vCardNode), "");
        $this->sendMessageNode($to, $mediaNode);
    }

    /**
     * Send a vCard to the user/group as Broadcast.
     *
     * @param $targets
     *   Array of recipients to send.
     * @param $name
     *   The vCard contact name.
     * @param $vCard
     *   The contact vCard to send.
     */
	public function sendBroadcastVcard($targets, $name, $vCard)
    {
        $vCardAttribs = array();
        $vCardAttribs['name'] = $name;
        $vCardNode = new ProtocolNode("vcard", $vCardAttribs, null, $vCard);

        $mediaAttribs = array();
        $mediaAttribs["type"] = "vcard";

        $mediaNode = new ProtocolNode("media", $mediaAttribs, array($vCardNode), "");
        $this->sendBroadcast($targets, $mediaNode, "media");
    }

    /**
     * Sets the bind of the new message.
     * @param $bind
     */
    public function setNewMessageBind($bind)
    {
        $this->newMsgBind = $bind;
    }

    /**
     * Upload file to WhatsApp servers.
     *
     * @param $file
     *   The uri of the file.
     *
     * @return string|bool
     *   Return the remote url or false on failure.
     */
    public function uploadFile($file)
    {
        $data['file'] = "@" . $file;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_URL, static::WHATSAPP_UPLOAD_HOST);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        curl_close($ch);

        $xml = simplexml_load_string($response);
        $url = strip_tags($xml->dict->string[3]->asXML());

        if (!empty($url)) {
            $this->eventManager()->fire("onUploadFile",
                array(
                    $this->phoneNumber,
                    basename($file),
                    $url
                ));
            return $url;
        } else {
            $this->eventManager()->fire("onUploadFileFailed",
                array(
                    $this->phoneNumber,
                    basename($file)
                ));
            return false;
        }
    }

    /**
     * Wait for message delivery notification.
     */
    public function waitForMessageReceipt()
    {
        $received = false;
        do {
            $this->pollMessage();
            $msgs = $this->getMessages();
            foreach ($msgs as $m) {
                // Process inbound messages.
                if ($m->getTag() == "message") {
                    if ($m->getChild('received') != null && $m->getAttribute('retry') != null) {
                        $received = true;
                    } elseif ($m->getChild('received') != null && $m->getAttribute('retry') != null) {
                        throw new Exception('There was a problem trying to send the message, please retry.');
                    }
                }
            }
        } while (!$received);
    }

    /**
     * Wait for Whatsapp server to acknowledge *it* has received message.
     * @param  string $id The id of the node sent that we are awaiting acknowledgement of.
     * @param int     $timeout
     */
    public function waitForServer($id, $timeout = 5)
    {
        $time = time();
        $this->serverReceivedId = false;
        do {
            $this->pollMessage();
        } while ($this->serverReceivedId !== $id && time() - $time < $timeout);
    }

    /**
     * Authenticate with the Whatsapp Server.
     *
     * @return String
     *   Returns binary string
     */
    protected function authenticate()
    {
        $keys = KeyStream::GenerateKeys(base64_decode($this->password), $this->challengeData);
        $this->inputKey = new KeyStream($keys[2], $keys[3]);
        $this->outputKey = new KeyStream($keys[0], $keys[1]);
        $phone = $this->dissectPhone();
        $array = "\0\0\0\0" . $this->phoneNumber . $this->challengeData;// . time() . static::WHATSAPP_USER_AGENT . " MccMnc/" . str_pad($phone["mcc"], 3, "0", STR_PAD_LEFT) . "001";
        $response = $this->outputKey->EncodeMessage($array, 0, 4, strlen($array) - 4);
        return $response;
    }

    /**
     * Add the authentication nodes.
     *
     * @return ProtocolNode
     *   Return itself.
     */
    protected function createAuthNode()
    {
        $authHash = array();
        $authHash["mechanism"] = "WAUTH-2";
        $authHash["user"] = $this->phoneNumber;
        //$authHash["passive"] = "true";
        $data = $this->createAuthBlob();
        $node = new ProtocolNode("auth", $authHash, null, $data);

        return $node;
    }

    protected function createAuthBlob()
    {
        if($this->challengeData) {
            $key = wa_pbkdf2('sha1', base64_decode($this->password), $this->challengeData, 16, 20, true);
            $this->inputKey = new KeyStream($key[2], $key[3]);
            $this->outputKey = new KeyStream($key[0], $key[1]);
            $this->reader->setKey($this->inputKey);
            //$this->writer->setKey($this->outputKey);
            $phone = $this->dissectPhone();
            $array = "\0\0\0\0" . $this->phoneNumber . $this->challengeData . time() . static::WHATSAPP_USER_AGENT . " MccMnc/" . str_pad($phone["mcc"], 3, "0", STR_PAD_LEFT) . $phone["mnc"];
            $this->challengeData = null;
            return $this->outputKey->EncodeMessage($array, 0, strlen($array), false);
        }
        return null;
    }

    /**
     * Add the auth response to protocoltreenode.
     *
     * @return ProtocolNode
     *   Return itself.
     */
    protected function createAuthResponseNode()
    {
        $resp = $this->authenticate();
        $node = new ProtocolNode("response", null, null, $resp);

        return $node;
    }

    /**
     * Add stream features.
     *
     * @return ProtocolNode Return itself.
     */
    protected function createFeaturesNode()
    {
        $readreceipts = new ProtocolNode("readreceipts", null, null, null);
        $groupsv2 = new ProtocolNode("groups_v2", null, null, null);
        $privacy = new ProtocolNode("privacy", null, null, null);
        $presencev2 = new ProtocolNode("presence", null, null, null);
        $parent = new ProtocolNode("stream:features", null, array($readreceipts, $groupsv2, $privacy, $presencev2), null);

        return $parent;
    }

    /**
     * Create a unique msg id.
     *
     * @param  string $prefix
     * @return string
     *   A message id string.
     */
    protected function createMsgId($prefix)
    {
        $msgid = "$prefix-" . time() . '-' . $this->messageCounter;
        $this->messageCounter++;

        return $msgid;
    }

    /**
     * Print a message to the debug console.
     *
     * @param string $debugMsg
     *   The debug message.
     */
    protected function debugPrint($debugMsg)
    {
        if ($this->debug) {
            echo $debugMsg;
        }
    }

    /**
     * Dissect country code from phone number.
     *
     * @return array
     *   An associative array with country code and phone number.
     *   - country: The detected country name.
     *   - cc: The detected country code (phone prefix).
     *   - phone: The phone number.
     *   - ISO3166: 2-Letter country code
     *   - ISO639: 2-Letter language code
     *   Return false if country code is not found.
     */
    protected function dissectPhone()
    {
        if (($handle = fopen(dirname(__FILE__).'/countries.csv', 'rb')) !== false) {
            while (($data = fgetcsv($handle, 1000)) !== false) {
                if (strpos($this->phoneNumber, $data[1]) === 0) {
                    // Return the first appearance.
                    fclose($handle);

                    $mcc = explode("|", $data[2]);
                    $mcc = $mcc[0];

                    //hook:
                    //fix country code for North America
                    if(substr($data[1], 0, 1) == "1")
                    {
                        $data[1] = "1";
                    }

                    $phone = array(
                        'country' => $data[0],
                        'cc' => $data[1],
                        'phone' => substr($this->phoneNumber, strlen($data[1]), strlen($this->phoneNumber)),
                        'mcc' => $mcc,
                        'ISO3166' => @$data[3],
                        'ISO639' => @$data[4],
                        'mnc' => $data[5]
                    );

                    $this->eventManager()->fire("onDissectPhone", array(
                            $this->phoneNumber,
                            $phone['country'],
                            $phone['cc'],
                            $phone['phone'],
                            $phone['mcc'],
                            $phone['ISO3166'],
                            $phone['ISO639'],
                            $phone['mnc']
                        )
                    );

                    return $phone;
                }
            }
            fclose($handle);
        }

        $this->eventManager()->fire("onDissectPhoneFailed",
            array(
                $this->phoneNumber
            ));

        return false;
    }

    /**
     * Detects mnc from specified carrier
     *
     * @param $lc LangCode
     * @param $carrierName Name of the carrier
     *
     * Returns mnc value
     */
    protected function detectMnc ($lc, $carrierName) {
      $fp = fopen(__DIR__ . DIRECTORY_SEPARATOR . 'networkinfo.csv', 'r');
      $mnc = null;

      while ($data = fgetcsv($fp, 0, ',')) {
        if (($data[4] === $lc) && ($data[7] === $carrierName)) {
          $mnc = $data[2];
          break;
        }
      }

      if($mnc == null)
        $mnc = '000';

      fclose($fp);

      return $mnc;
    }

    /**
     * Send the nodes to the Whatsapp server to log in.
     *
     * @throws Exception
     */
    protected function doLogin()
    {
        if ($this->isLoggedIn())
        {
            return true;
        }

        $this->writer->resetKey();
        $this->reader->resetKey();
        $resource = static::WHATSAPP_DEVICE . '-' . static::WHATSAPP_VER . '-' . static::PORT;
        $data = $this->writer->StartStream(static::WHATSAPP_SERVER, $resource);
        $feat = $this->createFeaturesNode();
        $auth = $this->createAuthNode();
        $this->sendData($data);
        $this->sendNode($feat);
        $this->sendNode($auth);

        $this->pollMessage();
        $this->pollMessage();
        $this->pollMessage();

        if($this->challengeData != null) {
            $data = $this->createAuthResponseNode();
            $this->sendNode($data);
            $this->reader->setKey($this->inputKey);
            $this->writer->setKey($this->outputKey);
            $this->pollMessage();
        }

        if(strcmp($this->loginStatus, static::DISCONNECTED_STATUS) == 0)
		{
			throw new Exception('Login Failure');
		}
		else
		{
            $this->eventManager()->fire("onLogin",
                array(
                    $this->phoneNumber
                ));
            $this->sendAvailableForChat();
		}
    }

    /**
     * Have we an active connection with WhatsAPP AND a valid login already?
     * @return bool
     */
    protected function isLoggedIn(){
        //If you aren't connected you can't be logged in!
        if ( ! $this->isConnected())
        {
            return false;
        }

        //We are connected - but are we logged in?
        if ( ! empty ($this->loginStatus) && $this->loginStatus === static::CONNECTED_STATUS)
        {
            return true;
        }

        return false;
    }

    /**
     * Create an identity string
     *
     * @param  string $identity File name where identity is going to be saved.
     * @return string           Correctly formatted identity
     */
    protected function buildIdentity($identity)
    {
        if (file_exists($identity.".dat"))
		{
      return urldecode(file_get_contents($identity.'.dat'));
		}
		else
		{
			$id = fopen($identity.".dat", "w");
      $bytes = strtolower(openssl_random_pseudo_bytes(20));
      fwrite($id, urlencode($bytes));
			fclose($id);

			return $bytes;
		}
    }

    protected function checkIdentity($identity)
    {
    	if (file_exists($identity.".dat"))
		  {
        $id = strlen(urldecode(file_get_contents($identity.'.dat')));
        if (($id == 20) || ($id == 16))
        {
          return true;
        }
    	}
    	else
    	{
    		return false;
    	}
    }

    public function sendSync(array $numbers, array $deletedNumbers = null, $syncType = 4, $index = 0, $last = true)
    {
        $users = array();
        $i = 0;
        for ($i; $i<count($numbers); $i++) { // number must start with '+' if international contact
            $users[$i] = new ProtocolNode("user", null, null, (substr($numbers[$i], 0, 1) != '+')?('+' . $numbers[$i]):($numbers[$i]));
        }

        if(($deletedNumbers != null) || (count($deletedNumbers) != 0))
        {
          $j = 0;
          for ($j; $j<count($deletedNumbers); $j++) {
            $users[$i] = new ProtocolNode("user", array("jid" => $this->getJID($deletedNumbers[$j]), "type" => "delete"), null, null);
            $i++;
          }
        }

        switch($syncType)
        {
            case 0:
              $mode = "full";
              $context = "registration";
              break;
            case 1:
              $mode = "full";
              $context = "interactive";
              break;
            case 2:
              $mode = "full";
              $context = "background";
              break;
            case 3:
              $mode = "delta";
              $context = "interactive";
              break;
            case 4:
              $mode = "delta";
              $context = "background";
              break;
            case 5:
              $mode = "query";
              $context = "interactive";
              break;
            case 6:
              $mode = "chunked";
              $context = "registration";
              break;
            case 7:
              $mode = "chunked";
              $context = "interactive";
              break;
            case 8:
              $mode = "chunked";
              $context = "background";
              break;
            default:
              $mode = "delta";
              $context = "background";
        }

        $id = $this->createMsgId("sendsync_");

        $node = new ProtocolNode("iq", array(
            "id" => $id,
            "xmlns" => "urn:xmpp:whatsapp:sync",
            "type" => "get"
        ), array(
            new ProtocolNode("sync", array(
                "mode" => $mode,
                "context" => $context,
                "sid" => "".((time() + 11644477200) * 10000000),
                "index" => "".$index,
                "last" => $last ? "true" : "false"
            ), $users, null)
        ), null);

        $this->sendNode($node);
        $this->waitForServer($id);

        return $id;
    }

    public function setMessageStore(MessageStoreInterface $messageStore)
    {
        $this->messageStore = $messageStore;
    }

    /**
     * Process number/jid and turn it into a JID if necessary
     *
     * @param string $number
     *  Number to process
     * @return string
     */
    protected function getJID($number)
    {
        if (!stristr($number, '@')) {
            //check if group message
            if (stristr($number, '-')) {
                //to group
                $number .= "@" . static::WHATSAPP_GROUP_SERVER;
            } else {
                //to normal user
                $number .= "@" . static::WHATSAPP_SERVER;
            }
        }

        return $number;
    }



    /**
     * Retrieves media file and info from either a URL or localpath
     *
     * @param $filepath
     * The URL or path to the mediafile you wish to send
     * @param $maxsizebytes
     * The maximum size in bytes the media file can be. Default 1MB
     *
     * @return bool  false if file information can not be obtained.
     */
    protected function getMediaFile($filepath, $maxsizebytes = 1048576)
    {
        if (filter_var($filepath, FILTER_VALIDATE_URL) !== false) {
            $this->mediaFileInfo = array();
            $this->mediaFileInfo['url'] = $filepath;

            //File is a URL. Create a curl connection but DON'T download the body content
            //because we want to see if file is too big.
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "$filepath");
            curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_NOBODY, true);

            if (curl_exec($curl) === false) {
                return false;
            }

            //While we're here, get mime type and filesize and extension
            $info = curl_getinfo($curl);
            $this->mediaFileInfo['filesize'] = $info['download_content_length'];
            $this->mediaFileInfo['filemimetype'] = $info['content_type'];
            $this->mediaFileInfo['fileextension'] = pathinfo(parse_url($this->mediaFileInfo['url'], PHP_URL_PATH), PATHINFO_EXTENSION);

            //Only download file if it's not too big
            //TODO check what max file size whatsapp server accepts.
            if ($this->mediaFileInfo['filesize'] < $maxsizebytes) {
                //Create temp file in media folder. Media folder must be writable!
                $this->mediaFileInfo['filepath'] = tempnam(getcwd() . '/' . static::MEDIA_FOLDER, 'WHA');
                $fp = fopen($this->mediaFileInfo['filepath'], 'w');
                if ($fp) {
                    curl_setopt($curl, CURLOPT_NOBODY, false);
                    curl_setopt($curl, CURLOPT_BUFFERSIZE, 1024);
                    curl_setopt($curl, CURLOPT_FILE, $fp);
                    curl_exec($curl);
                    fclose($fp);
                } else {
                    unlink($this->mediaFileInfo['filepath']);
                    curl_close($curl);
                    return false;
                }
                //Success
                curl_close($curl);
                return true;
            } else {
                //File too big. Don't Download.
                curl_close($curl);
                return false;
            }
        } else if (file_exists($filepath)) {
            //Local file
            $this->mediaFileInfo['filesize'] = filesize($filepath);
            if ($this->mediaFileInfo['filesize'] < $maxsizebytes) {
                $this->mediaFileInfo['filepath'] = $filepath;
                $this->mediaFileInfo['fileextension'] = pathinfo($filepath, PATHINFO_EXTENSION);
                $this->mediaFileInfo['filemimetype'] = get_mime($filepath);
                return true;
            } else {
                //File too big
                return false;
            }
        }
        //Couldn't tell what file was, local or URL.
        return false;
    }

    /**
     * Get a decoded JSON response from Whatsapp server
     *
     * @param  string $host  The host URL
     * @param  array $query A associative array of keys and values to send to server.
     * @return object   NULL is returned if the json cannot be decoded or if the encoded data is deeper than the recursion limit
     */
    protected function getResponse($host, $query)
    {
        // Build the url.
        $url = $host . '?' . http_build_query($query);

        // Open connection.
        $ch = curl_init();

        // Configure the connection.
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, static::WHATSAPP_USER_AGENT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: text/json'));
        // This makes CURL accept any peer!
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Get the response.
        $response = curl_exec($ch);

        // Close the connection.
        curl_close($ch);

        return json_decode($response);
    }

    /**
     * Process the challenge.
     *
     *
     * @param ProtocolNode $node
     *   The node that contains the challenge.
     */
    protected function processChallenge($node)
    {
        $this->challengeData = $node->getData();
    }

    /**
     * Process inbound data.
     *
     * @param      $data
     * @param bool $autoReceipt
     * @throws Exception
     */
    protected function processInboundData($data, $autoReceipt = true, $type = "read")
    {
        $node = $this->reader->nextTree($data);
        if( $node != null ) {
            $this->processInboundDataNode($node, $autoReceipt, $type);
        }
    }

    /**
     * Will process the data from the server after it's been decrypted and parsed.
     *
     * This also provides a convenient method to use to unit test the event framework.
     * @param ProtocolNode $node
     * @param bool         $autoReceipt
     * @throws Exception
     */
    protected function processInboundDataNode(ProtocolNode $node, $autoReceipt = true, $type = "read") {
        $this->debugPrint($node->nodeString("rx  ") . "\n");
        $this->serverReceivedId = $node->getAttribute('id');

        if ($node->getTag() == "challenge") {
            $this->processChallenge($node);
        }
        elseif($node->getTag() == "failure"  )
		{

			$this->loginStatus = static::DISCONNECTED_STATUS;

		}
        elseif ($node->getTag() == "success") {
	    	if ($node->getAttribute("status") == "active") {
	            $this->loginStatus = static::CONNECTED_STATUS;
	            $challengeData = $node->getData();
	            file_put_contents($this->challengeFilename, $challengeData);
	            $this->writer->setKey($this->outputKey);
	   		} elseif ($node->getAttribute("status") == "expired")
	   		{
            	$this->eventManager()->fire("onAccountExpired",
                	array(
                    	$this->phoneNumber,
                    	$node->getAttribute("kind"),
                    	$node->getAttribute("status"),
                    	$node->getAttribute("creation"),
                    	$node->getAttribute("expiration")
                	));
	    	}
        } elseif($node->getTag() == "failure")
        {
            $this->eventManager()->fire("onLoginFailed",
                array(
                    $this->phoneNumber,
                    $node->getChild(0)->getTag()
                ));
        }
        elseif($node->getTag() == 'ack' && $node->getAttribute("class") == "message")
        {
            $this->eventManager()->fire("onMessageReceivedServer",
                array(
                    $this->phoneNumber,
                    $node->getAttribute('from'),
                    $node->getAttribute('id'),
                    $node->getAttribute('class'),
                    $node->getAttribute('t')
                ));
        }
        elseif($node->getTag() == 'receipt')
        {
            if ($node->hasChild("list")) {
              foreach ($node->getChild("list")->getChildren() as $child) {
                $this->eventManager()->fire("onMessageReceivedClient",
                  array(
                    $this->phoneNumber,
                    $node->getAttribute('from'),
                    $child->getAttribute('id'),
                    $node->getAttribute('type'),
                    $node->getAttribute('t')
                  ));
              }
            }

            $this->eventManager()->fire("onMessageReceivedClient",
                array(
                    $this->phoneNumber,
                    $node->getAttribute('from'),
                    $node->getAttribute('id'),
                    $node->getAttribute('type'),
                    $node->getAttribute('t')
                ));

            $ackNode = new ProtocolNode("ack", array(
              "to" => $node->getAttribute('from'),
              "id" => $node->getAttribute('id'),
              "type" => $type,
              "t" => time()
              ), null, null);

            $this->sendNode($ackNode);
        }
        if ($node->getTag() == "message") {
            array_push($this->messageQueue, $node);

            if ($node->hasChild('x') && $this->lastId == $node->getAttribute('id')) {
                $this->sendNextMessage();
            }
            if ($this->newMsgBind && $node->getChild('body')) {
                $this->newMsgBind->process($node);
            }
            if ($node->getAttribute("type") == "text" && $node->getChild('body') != null) {
                $author = $node->getAttribute("participant");
                if($author == "")
                {
                    //private chat message
                    $this->eventManager()->fire("onGetMessage",
                        array(
                            $this->phoneNumber,
                            $node->getAttribute('from'),
                            $node->getAttribute('id'),
                            $node->getAttribute('type'),
                            $node->getAttribute('t'),
                            $node->getAttribute("notify"),
                            $node->getChild("body")->getData()
                        ));

                        if ($this->messageStore !== null) {
                            $this->messageStore->saveMessage($node->getAttribute('from'), $this->phoneNumber, $node->getChild("body")->getData(), $node->getAttribute('id'), $node->getAttribute('t'));
                        }
                }
                else
                {
                    //group chat message
                    $this->eventManager()->fire("onGetGroupMessage",
                        array(
                            $this->phoneNumber,
                            $node->getAttribute('from'),
                            $author,
                            $node->getAttribute('id'),
                            $node->getAttribute('type'),
                            $node->getAttribute('t'),
                            $node->getAttribute("notify"),
                            $node->getChild("body")->getData()
                        ));
                }

                if($autoReceipt)
                {
                    $this->sendMessageReceived($node, $type, $author);
                }
            }
            if ($node->getAttribute("type") == "text" && $node->getChild(0)->getTag() == 'enc') {
              // TODO
              if($autoReceipt)
              {
                $this->sendMessageReceived($node, $type);
              }
            }
            if ($node->getAttribute("type") == "media" && $node->getChild('media') != null) {
                if ($node->getChild("media")->getAttribute('type') == 'image') {

                    if ($node->getAttribute("participant") == null) {
                        $this->eventManager()->fire("onGetImage",
                            array(
                                $this->phoneNumber,
                                $node->getAttribute('from'),
                                $node->getAttribute('id'),
                                $node->getAttribute('type'),
                                $node->getAttribute('t'),
                                $node->getAttribute('notify'),
                                $node->getChild("media")->getAttribute('size'),
                                $node->getChild("media")->getAttribute('url'),
                                $node->getChild("media")->getAttribute('file'),
                                $node->getChild("media")->getAttribute('mimetype'),
                                $node->getChild("media")->getAttribute('filehash'),
                                $node->getChild("media")->getAttribute('width'),
                                $node->getChild("media")->getAttribute('height'),
                                $node->getChild("media")->getData(),
                                $node->getChild("media")->getAttribute('caption')
                            ));
                    } else {
                        $this->eventManager()->fire("onGetGroupImage",
                            array(
                                $this->phoneNumber,
                                $node->getAttribute('from'),
                                $node->getAttribute('participant'),
                                $node->getAttribute('id'),
                                $node->getAttribute('type'),
                                $node->getAttribute('t'),
                                $node->getAttribute('notify'),
                                $node->getChild("media")->getAttribute('size'),
                                $node->getChild("media")->getAttribute('url'),
                                $node->getChild("media")->getAttribute('file'),
                                $node->getChild("media")->getAttribute('mimetype'),
                                $node->getChild("media")->getAttribute('filehash'),
                                $node->getChild("media")->getAttribute('width'),
                                $node->getChild("media")->getAttribute('height'),
                                $node->getChild("media")->getData(),
                                $node->getChild("media")->getAttribute('caption')
                            ));
                    }

                } elseif ($node->getChild("media")->getAttribute('type') == 'video') {

                    if ($node->getAttribute("participant") == null) {
                        $this->eventManager()->fire("onGetVideo",
                            array(
                                $this->phoneNumber,
                                $node->getAttribute('from'),
                                $node->getAttribute('id'),
                                $node->getAttribute('type'),
                                $node->getAttribute('t'),
                                $node->getAttribute('notify'),
                                $node->getChild("media")->getAttribute('url'),
                                $node->getChild("media")->getAttribute('file'),
                                $node->getChild("media")->getAttribute('size'),
                                $node->getChild("media")->getAttribute('mimetype'),
                                $node->getChild("media")->getAttribute('filehash'),
                                $node->getChild("media")->getAttribute('duration'),
                                $node->getChild("media")->getAttribute('vcodec'),
                                $node->getChild("media")->getAttribute('acodec'),
                                $node->getChild("media")->getData(),
                                $node->getChild("media")->getAttribute('caption')
                            ));
                    } else {
                        $this->eventManager()->fire("onGetGroupVideo",
                            array(
                                $this->phoneNumber,
                                $node->getAttribute('from'),
                                $node->getAttribute('participant'),
                                $node->getAttribute('id'),
                                $node->getAttribute('type'),
                                $node->getAttribute('t'),
                                $node->getAttribute('notify'),
                                $node->getChild("media")->getAttribute('url'),
                                $node->getChild("media")->getAttribute('file'),
                                $node->getChild("media")->getAttribute('size'),
                                $node->getChild("media")->getAttribute('mimetype'),
                                $node->getChild("media")->getAttribute('filehash'),
                                $node->getChild("media")->getAttribute('duration'),
                                $node->getChild("media")->getAttribute('vcodec'),
                                $node->getChild("media")->getAttribute('acodec'),
                                $node->getChild("media")->getData(),
                                $node->getChild("media")->getAttribute('caption')
                            ));
                    }
                } elseif ($node->getChild("media")->getAttribute('type') == 'audio') {
                    $author = $node->getAttribute("participant");
                    $this->eventManager()->fire("onGetAudio",
                        array(
                            $this->phoneNumber,
                            $node->getAttribute('from'),
                            $node->getAttribute('id'),
                            $node->getAttribute('type'),
                            $node->getAttribute('t'),
                            $node->getAttribute('notify'),
                            $node->getChild("media")->getAttribute('size'),
                            $node->getChild("media")->getAttribute('url'),
                            $node->getChild("media")->getAttribute('file'),
                            $node->getChild("media")->getAttribute('mimetype'),
                            $node->getChild("media")->getAttribute('filehash'),
                            $node->getChild("media")->getAttribute('seconds'),
                            $node->getChild("media")->getAttribute('acodec'),
                            $author,
                        ));
                } elseif ($node->getChild("media")->getAttribute('type') == 'vcard') {
                    if($node->getChild("media")->hasChild('vcard')) {
                        $name = $node->getChild("media")->getChild("vcard")->getAttribute('name');
                        $data = $node->getChild("media")->getChild("vcard")->getData();
                    } else {
                        $name = "NO_NAME";
                        $data = $node->getChild("media")->getData();
                    }
                    $author = $node->getAttribute("participant");

                    $this->eventManager()->fire("onGetvCard",
                        array(
                            $this->phoneNumber,
                            $node->getAttribute('from'),
                            $node->getAttribute('id'),
                            $node->getAttribute('type'),
                            $node->getAttribute('t'),
                            $node->getAttribute('notify'),
                            $name,
                            $data,
                            $author
                        ));
                } elseif ($node->getChild("media")->getAttribute('type') == 'location') {
                    $url = $node->getChild("media")->getAttribute('url');
                    $name = $node->getChild("media")->getAttribute('name');
                    $author = $node->getAttribute("participant");

                    $this->eventManager()->fire("onGetLocation",
                        array(
                            $this->phoneNumber,
                            $node->getAttribute('from'),
                            $node->getAttribute('id'),
                            $node->getAttribute('type'),
                            $node->getAttribute('t'),
                            $node->getAttribute('notify'),
                            $name,
                            $node->getChild("media")->getAttribute('longitude'),
                            $node->getChild("media")->getAttribute('latitude'),
                            $url,
                            $node->getChild("media")->getData(),
                            $author
                        ));
                }

                if($autoReceipt)
                {
                    $this->sendMessageReceived($node, $type);
                }
            }
            if ($node->getChild('received') != null) {
                $this->eventManager()->fire("onMessageReceivedClient",
                    array(
                        $this->phoneNumber,
                        $node->getAttribute('from'),
                        $node->getAttribute('id'),
                        $node->getAttribute('type'),
                        $node->getAttribute('t')
                    ));
            }
        }
        if ($node->getTag() == "presence" && $node->getAttribute("status") == "dirty") {
            //clear dirty
            $categories = array();
            if (count($node->getChildren()) > 0)
                foreach ($node->getChildren() as $child) {
                    if ($child->getTag() == "category") {
                        $categories[] = $child->getAttribute("name");
                    }
                }
            $this->sendClearDirty($categories);
        }
	if (strcmp($node->getTag(), "presence") == 0
            && strncmp($node->getAttribute('from'), $this->phoneNumber, strlen($this->phoneNumber)) != 0
            && strpos($node->getAttribute('from'), "-") == false) {
            $presence = array();
            if($node->getAttribute('type') == null){
                $this->eventManager()->fire("onPresence",
                    array(
                        $this->phoneNumber,
                        $node->getAttribute('from'),
                        $presence['type'] = "available"
                    ));
            }
            else{
                $this->eventManager()->fire("onPresence",
                    array(
                        $this->phoneNumber,
                        $node->getAttribute('from'),
                        $presence['type'] = "unavailable"
                    ));
            }
        }
        if ($node->getTag() == "presence"
            && strncmp($node->getAttribute('from'), $this->phoneNumber, strlen($this->phoneNumber)) != 0
            && strpos($node->getAttribute('from'), "-") !== false
            && $node->getAttribute('type') != null) {
            $groupId = self::parseJID($node->getAttribute('from'));
            if ($node->getAttribute('add') != null) {
                $this->eventManager()->fire("onGroupsParticipantsAdd",
                    array(
                        $this->phoneNumber,
                        $groupId,
                        self::parseJID($node->getAttribute('add'))
                    ));
            } elseif ($node->getAttribute('remove') != null) {
                $this->eventManager()->fire("onGroupsParticipantsRemove",
                    array(
                        $this->phoneNumber,
                        $groupId,
                        self::parseJID($node->getAttribute('remove'))
                    ));
            }
        }
        if (strcmp($node->getTag(), "chatstate") == 0
            && strncmp($node->getAttribute('from'), $this->phoneNumber, strlen($this->phoneNumber)) != 0
            && strpos($node->getAttribute('from'), "-") == false) {
            if($node->getChild(0)->getTag() == "composing"){
                $this->eventManager()->fire("onMessageComposing",
                    array(
                        $this->phoneNumber,
                        $node->getAttribute('from'),
                        $node->getAttribute('id'),
                        "composing",
                        $node->getAttribute('t')
                    ));
            }
            else{
                $this->eventManager()->fire("onMessagePaused",
                    array(
                        $this->phoneNumber,
                        $node->getAttribute('from'),
                        $node->getAttribute('id'),
                        "paused",
                        $node->getAttribute('t')
                    ));
            }
        }
        if ($node->getTag() == "iq"
            && $node->getAttribute('type') == "get"
            && $node->getAttribute('xmlns') == "urn:xmpp:ping") {
            $this->eventManager()->fire("onPing",
                array(
                    $this->phoneNumber,
                    $node->getAttribute('id')
                ));
            $this->sendPong($node->getAttribute('id'));
        }
        if ($node->getTag() == "iq"
            && $node->getChild("sync") != null) {

            //sync result
            $sync = $node->getChild('sync');
            $existing = $sync->getChild("in");
            $nonexisting = $sync->getChild("out");

            //process existing first
            $existingUsers = array();
            if (!empty($existing)) {
                foreach ($existing->getChildren() as $child) {
                    $existingUsers[$child->getData()] = $child->getAttribute("jid");
                }
            }

            //now process failed numbers
            $failedNumbers = array();
            if (!empty($nonexisting)) {
                foreach ($nonexisting->getChildren() as $child) {
                    $failedNumbers[] = str_replace('+', '', $child->getData());
                }
            }

            $index = $sync->getAttribute("index");

            $result = new SyncResult($index, $sync->getAttribute("sid"), $existingUsers, $failedNumbers);

            $this->eventManager()->fire("onGetSyncResult",
                array(
                    $result
                ));
        }
        if ($node->getTag() == "receipt") {
            $this->eventManager()->fire("onGetReceipt",
                array(
                    $node->getAttribute('from'),
                    $node->getAttribute('id'),
                    $node->getAttribute('offline'),
                    $node->getAttribute('retry')
                ));
        }
        if ($node->getTag() == "iq"
            && $node->getAttribute('type') == "result") {
            if ($node->getChild("query") != null) {
                if ($node->getChild(0)->getAttribute('xmlns') == 'jabber:iq:privacy') {
                    // ToDo: We need to get explicitly list out the children as arguments
                    //       here.
                    $this->eventManager()->fire("onGetPrivacyBlockedList",
                        array(
                            $this->phoneNumber,
                            $node->getChild(0)->getChild(0)->getChildren()
                        ));
                }
                $this->eventManager()->fire("onGetRequestLastSeen",
                    array(
                        $this->phoneNumber,
                        $node->getAttribute('from'),
                        $node->getAttribute('id'),
                        $node->getChild(0)->getAttribute('seconds')
                    ));
                array_push($this->messageQueue, $node);
            }
            if ($node->getChild("props") != null) {
                //server properties
                $props = array();
                foreach($node->getChild(0)->getChildren() as $child) {
                    $props[$child->getAttribute("name")] = $child->getAttribute("value");
                }
                $this->eventManager()->fire("onGetServerProperties",
                    array(
                        $this->phoneNumber,
                        $node->getChild(0)->getAttribute("version"),
                        $props
                    ));
            }
            if ($node->getChild("picture") != null) {
                $this->eventManager()->fire("onGetProfilePicture",
                    array(
                        $this->phoneNumber,
                        $node->getAttribute("from"),
                        $node->getChild("picture")->getAttribute("type"),
                        $node->getChild("picture")->getData()
                    ));
            }
            if ($node->getChild("media") != null || $node->getChild("duplicate") != null) {
                $this->processUploadResponse($node);
            }
            if ($node->nodeIdContains("group")) {
                //There are multiple types of Group reponses. Also a valid group response can have NO children.
                //Events fired depend on text in the ID field.
                $groupList = array();
                if ($node->getChild(0) != null && $node->getChild(0)->getChildren() != null) {
                    foreach ($node->getChild(0)->getChildren() as $child) {
                        $groupList[] = $child->getAttributes();
                    }
                }
                if($node->nodeIdContains('creategroup')){
                    $this->groupId = $node->getChild(0)->getAttribute('id');
                    $this->eventManager()->fire("onGroupsChatCreate",
                        array(
                            $this->phoneNumber,
                            $this->groupId
                        ));
                }
                if($node->nodeIdContains('endgroup')){
                    $this->groupId = $node->getChild(0)->getChild(0)->getAttribute('id');
                    $this->eventManager()->fire("onGroupsChatEnd",
                        array(
                            $this->phoneNumber,
                            $this->groupId
                        ));
                }
                if($node->nodeIdContains('getgroups')){
                    $this->eventManager()->fire("onGetGroups",
                        array(
                            $this->phoneNumber,
                            $groupList
                        ));
                }
                if($node->nodeIdContains('getgroupinfo')){
                    $this->eventManager()->fire("onGetGroupsInfo",
                        array(
                            $this->phoneNumber,
                            $groupList
                        ));
                }
                if($node->nodeIdContains('getgroupparticipants')){
                    $groupId = self::parseJID($node->getAttribute('from'));
                    $this->eventManager()->fire("onGetGroupParticipants",
                        array(
                            $this->phoneNumber,
                            $groupId,
                            $groupList
                        ));
                }
            }
            if($node->nodeIdContains('get_groupv2_info')){
                $groupId = self::parseJID($node->getAttribute('from'));

                $groupList = array();
                $groupChild = $node->getChild(0);
                if ($groupChild != null) {
                     $creator = $groupChild->getAttribute('creator');
                     $creation = $groupChild->getAttribute('creation');
                     $subject = $groupChild->getAttribute('subject');
                     if ($groupChild->getChild(0) != null) {
                          foreach ($groupChild->getChildren() as $child) {
                                $participants[] = $child->getAttribute('jid');
                                    if($child->getAttribute('type') != null)
                                        $admin = $child->getAttribute('jid');
				                   }
			               }
		            }

		            $this->eventManager()->fire("onGetGroupV2Info",
			          array(
                     $this->phoneNumber,
                     $creator,
                     $creation,
                     $subject,
                     $participants,
                     $admin
		            ));
            }
            if ($node->nodeIdContains("get_lists")) {
                $broadcastLists = array();
                if ($node->getChild(0) != null) {
                    $childArray = $node->getChildren();
                    foreach ($childArray as $list) {
                      if($list->getChildren() != null) {
                        foreach ( $list->getChildren() as $sublist) {
                            $id = $sublist->getAttribute("id");
                            $name = $sublist->getAttribute("name");
                            $broadcastLists[$id]['name'] = $name;
                            $recipients = array();
                            foreach ($sublist->getChildren() as $recipient) {
                                array_push($recipients, $recipient->getAttribute('jid'));
                            }
                            $broadcastLists[$id]['recipients'] = $recipients;
                        }
                      }
                    }
                }
                $this->eventManager()->fire("onGetBroadcastLists",
                    array(
                        $this->phoneNumber,
                        $broadcastLists
                    ));
            }
            if($node->getChild("pricing") != null)
            {
                $this->eventManager()->fire("onGetServicePricing",
                    array(
                        $this->phoneNumber,
                        $node->getChild(0)->getAttribute("price"),
                        $node->getChild(0)->getAttribute("cost"),
                        $node->getChild(0)->getAttribute("currency"),
                        $node->getChild(0)->getAttribute("expiration")
                    ));
            }
            if($node->getChild("extend") != null)
            {
                $this->eventManager()->fire("onGetExtendAccount",
                    array(
                        $this->phoneNumber,
                        $node->getChild("account")->getAttribute("kind"),
                        $node->getChild("account")->getAttribute("status"),
                        $node->getChild("account")->getAttribute("creation"),
                        $node->getChild("account")->getAttribute("expiration")
                    ));
            }
            if($node->getChild("normalize") != null)
            {
                $this->eventManager()->fire("onGetNormalizedJid",
                    array(
                        $this->phoneNumber,
                        $node->getChild(0)->getAttribute("result")
                    ));
            }
            if($node->getChild("status") != null)
            {
                $child = $node->getChild("status");
                foreach($child->getChildren() as $status)
                {
                    $this->eventManager()->fire("onGetStatus",
                        array(
                            $this->phoneNumber,
                            $status->getAttribute("jid"),
                            "requested",
                            $node->getAttribute("id"),
                            $status->getAttribute("t"),
                            $status->getData()
                        ));
                }
            }
        }
        if ($node->getTag() == "iq" && $node->getAttribute('type') == "error") {
            $this->eventManager()->fire("onGetError",
                array(
                    $this->phoneNumber,
                    $node->getAttribute('from'),
                    $node->getAttribute('id'),
                    $node->getChild(0)
                ));
        }

        if ($node->getTag() == "message" && $node->getAttribute('type') == "media" && $node->getChild(0)->getAttribute('type') == "image" ) {

          $msgId = $this->createMsgId("ack-media");

          $ackNode = new ProtocolNode("ack", array(
            "url" => $node->getChild(0)->getAttribute('url')
          ), null, null);

          $iqNode = new ProtocolNode("iq", array(
            "id" => $msgId,
            "xmlns" => "w:m",
            "type" => "set",
            "to" => static::WHATSAPP_SERVER
          ), array($ackNode), null);
          $this->sendNode($iqNode);
        }

        $children = $node->getChild(0);
        if ($node->getTag() == "stream:error" && empty($children) == false && $node->getChild(0)->getTag() == "system-shutdown")
        {
            $this->eventManager()->fire("onStreamError",
                array(
                    $node->getChild(0)->getTag()
                ));
        }


        if ($node->getTag() == "stream:error")
        {
            $this->eventManager()->fire("onStreamError",
                array(
                    $node->getChild(0)->getTag()
                ));
        }

        if($node->getTag() == "notification")
        {
            $name = $node->getAttribute("notify");
            $type = $node->getAttribute("type");
            switch($type)
            {
                case "status":
                    $this->eventManager()->fire("onGetStatus",
                        array(
                            $this->phoneNumber, //my number
                            $node->getAttribute("from"),
                            $node->getChild(0)->getTag(),
                            $node->getAttribute("id"),
                            $node->getAttribute("t"),
                            $node->getChild(0)->getData()
                        ));
                    break;
                case "picture":
                    if ($node->hasChild('set')) {
                        $this->eventManager()->fire("onProfilePictureChanged",
                            array(
                                $this->phoneNumber,
                                $node->getAttribute('from'),
                                $node->getAttribute('id'),
                                $node->getAttribute('t')
                            ));
                    } else if ($node->hasChild('delete')) {
                        $this->eventManager()->fire("onProfilePictureDeleted",
                            array(
                                $this->phoneNumber,
                                $node->getAttribute('from'),
                                $node->getAttribute('id'),
                                $node->getAttribute('t')
                            ));
                    }
                    //TODO
                    break;
                case "contacts":
                    //TODO
                    break;
                case "encrypt":
                    $value = $node->getChild(0)->getAttribute('value');
                    if (is_numeric($value)){
                      $this->eventManager()->fire("onGetKeysLeft",
                        array(
                          $this->phoneNumber,
                          $node->getChild(0)->getAttribute('value')
                        ));
                    }
                    else{
                      echo "Corrupt Stream: value " . $value . "is not numeric";
                    }
                    break;
                case "w:gp2":
                    if ($node->hasChild('remove')) {
                    	if ($node->getChild(0)->hasChild('participant'))
                        $this->eventManager()->fire("onGroupsParticipantsRemove",
                            array(
                                $this->phoneNumber,
                                $node->getAttribute('from'),
                                $node->getChild(0)->getChild(0)->getAttribute('jid')
                            ));
                  } else if ($node->hasChild('add')) {
                        $this->eventManager()->fire("onGroupsParticipantsAdd",
                            array(
                                $this->phoneNumber,
                                $node->getAttribute('from'),
                                $node->getChild(0)->getChild(0)->getAttribute('jid')
                            ));
                  }
                    else if ($node->hasChild('create')) {
                        $groupMembers = array();
                        foreach ($node->getChild(0)->getChild(0)->getChildren() AS $cn) {
                            $groupMembers[] = $cn->getAttribute('jid');
                        }
                        $this->eventManager()->fire("onGroupisCreated",
                            array(
                                $this->phoneNumber,
                                $node->getChild(0)->getChild(0)->getAttribute('creator'),
                                $node->getChild(0)->getChild(0)->getAttribute('id'),
                                $node->getChild(0)->getChild(0)->getAttribute('subject'),
                                $node->getAttribute('participant'),
                                $node->getChild(0)->getChild(0)->getAttribute('creation'),
                                $groupMembers
                            ));
                  }
                    else if ($node->hasChild('subject')) {
                        $this->eventManager()->fire("onGetGroupsSubject",
                            array(
                                $this->phoneNumber,
                                $node->getAttribute('from'),
                                $node->getAttribute('t'),
                                $node->getAttribute('participant'),
                                $node->getAttribute('notify'),
                                $node->getChild(0)->getAttribute('subject')
                            ));
                    }
                    break;
                  case "account":
                    if (($node->getChild(0)->getAttribute('author')) == "")
                      $author = "Paypal";
                    else
                      $author = $node->getChild(0)->getAttribute('author');
                      $this->eventManager()->fire("onPaidAccount",
                          array(
                              $this->phoneNumber,
                              $author,
                              $node->getChild(0)->getChild(0)->getAttribute('kind'),
                              $node->getChild(0)->getChild(0)->getAttribute('status'),
                              $node->getChild(0)->getChild(0)->getAttribute('creation'),
                              $node->getChild(0)->getChild(0)->getAttribute('expiration')
                          ));
                    break;
                  case "features":
                    if($node->getChild(0)->getChild(0) == "encrypt")
                    {
                      $this->eventManager()->fire("onGetFeature",
                          array(
                              $this->phoneNumber,
                              $node->getAttribute('from'),
                              $node->getChild(0)->getChild(0)->getAttribute('value'),
                            ));
                      }
                    break;
                default:
                    throw new Exception("Method $type not implemented");
            }
            $this->sendNotificationAck($node);
        }
        if($node->getTag() == "ib")
        {
            foreach($node->getChildren() as $child)
            {
                switch($child->getTag())
                {
                    case "dirty":
                        $this->sendClearDirty(array($child->getAttribute("type")));
                        break;
                    case "offline":

                        break;
                    default:
                        throw new Exception("ib handler for " . $child->getTag() . " not implemented");
                }
            }
        }
        if($node->getTag() == "ack")
        {
            ////on get ack
        }

    }

    /**
     * @param $node ProtocolNode
     */
    protected function sendNotificationAck($node)
    {
        $from = $node->getAttribute("from");
        $to = $node->getAttribute("to");
        $participant = $node->getAttribute("participant");
        $id = $node->getAttribute("id");
        $type = $node->getAttribute("type");

        $attributes = array();
        if($to)
            $attributes["from"] = $to;
        if($participant)
            $attributes["participant"] = $participant;
        $attributes["to"] = $from;
        $attributes["class"] = "notification";
        $attributes["id"] = $id;
        $attributes["type"] = $type;
        $ack = new ProtocolNode("ack", $attributes, null, null);
        $this->sendNode($ack);
    }

    /**
     * Process and save media image
     *
     * @param ProtocolNode $node
     * ProtocolNode containing media
     */
    protected function processMediaImage($node)
    {
        $media = $node->getChild("media");
        if ($media != null) {
            $filename = $media->getAttribute("file");
            $url = $media->getAttribute("url");

            //save thumbnail
            $data = $media->getData();
            $fp = @fopen(static::MEDIA_FOLDER . "/thumb_" . $filename, "w");
            if ($fp) {
                fwrite($fp, $data);
                fclose($fp);
            }

            //download and save original
            $data = file_get_contents($url);
            $fp = @fopen(static::MEDIA_FOLDER . "/" . $filename, "w");
            if ($fp) {
                fwrite($fp, $data);
                fclose($fp);
            }
        }
    }

    /**
     * Processes received picture node
     *
     * @param ProtocolNode $node
     *  ProtocolNode containing the picture
     */
    protected function processProfilePicture($node)
    {
        $pictureNode = $node->getChild("picture");

        if ($pictureNode != null) {
            $type = $pictureNode->getAttribute("type");
            $data = $pictureNode->getData();
            if ($type == "preview") {
                $filename = static::PICTURES_FOLDER . "/preview_" . $node->getAttribute("from") . ".jpg";
            } else {
                $filename = static::PICTURES_FOLDER . "/" . $node->getAttribute("from") . ".jpg";
            }
            $fp = @fopen($filename, "w");
            if ($fp) {
                fwrite($fp, $data);
                fclose($fp);
            }
        }
    }

    /**
     * If the media file was originally from a URL, this function either deletes it
     * or renames it depending on the user option.
     *
     * @param bool $storeURLmedia Save or delete the media file from local server
     */
    protected function processTempMediaFile($storeURLmedia)
    {
        if (isset($this->mediaFileInfo['url'])) {
            if ($storeURLmedia) {
                if (is_file($this->mediaFileInfo['filepath'])) {
                    rename($this->mediaFileInfo['filepath'], $this->mediaFileInfo['filepath'] . $this->mediaFileInfo['fileextension']);
                }
            } else {
                if (is_file($this->mediaFileInfo['filepath'])) {
                    unlink($this->mediaFileInfo['filepath']);
                }
            }
        }
    }

    /**
     * Process media upload response
     *
     * @param ProtocolNode $node
     *  Message node
     * @return bool
     */
    protected function processUploadResponse($node)
    {
        $id = $node->getAttribute("id");
        $messageNode = @$this->mediaQueue[$id];
        if ($messageNode == null) {
            //message not found, can't send!
            $this->eventManager()->fire("onMediaUploadFailed",
                array(
                    $this->phoneNumber,
                    $id,
                    $node,
                    $messageNode,
                    "Message node not found in queue"
                ));
            return false;
        }

        $duplicate = $node->getChild("duplicate");
        if ($duplicate != null) {
            //file already on whatsapp servers
            $url = $duplicate->getAttribute("url");
            $filesize = $duplicate->getAttribute("size");
//          $mimetype = $duplicate->getAttribute("mimetype");
            $filehash = $duplicate->getAttribute("filehash");
            $filetype = $duplicate->getAttribute("type");
//          $width = $duplicate->getAttribute("width");
//          $height = $duplicate->getAttribute("height");
            $exploded = explode("/", $url);
            $filename = array_pop($exploded);
        } else {
            //upload new file
            $json = WhatsMediaUploader::pushFile($node, $messageNode, $this->mediaFileInfo, $this->phoneNumber);

            if (!$json) {
                //failed upload
                $this->eventManager()->fire("onMediaUploadFailed",
                    array(
                        $this->phoneNumber,
                        $id,
                        $node,
                        $messageNode,
                        "Failed to push file to server"
                    ));
                return false;
            }

            $url = $json->url;
            $filesize = $json->size;
//          $mimetype = $json->mimetype;
            $filehash = $json->filehash;
            $filetype = $json->type;
//          $width = $json->width;
//          $height = $json->height;
            $filename = $json->name;
        }

        $mediaAttribs = array();
        $mediaAttribs["type"] = $filetype;
        $mediaAttribs["url"] = $url;
        $mediaAttribs["encoding"] = "raw";
        $mediaAttribs["file"] = $filename;
        $mediaAttribs["size"] = $filesize;
        if($this->mediaQueue[$id]['caption'] != '')
          $mediaAttribs["caption"] = $this->mediaQueue[$id]['caption'];

        $filepath = $this->mediaQueue[$id]['filePath'];
        $to = $this->mediaQueue[$id]['to'];

        $icon = "";
        switch ($filetype) {
            case "image":
		            $caption = $this->mediaQueue[$id]['caption'];
                $icon = createIcon($filepath);
                break;
            case "video":
		            $caption = $this->mediaQueue[$id]['caption'];
                $icon = createVideoIcon($filepath);
                break;
            default:
		            $caption = '';
                $icon = '';
                break;
        }

        $mediaNode = new ProtocolNode("media", $mediaAttribs, null, $icon);
        if (is_array($to)) {
            $this->sendBroadcast($to, $mediaNode, "media");
        } else {
            $this->sendMessageNode($to, $mediaNode);
        }
        $this->eventManager()->fire("onMediaMessageSent",
            array(
                $this->phoneNumber,
                $to,
                $id,
                $filetype,
                $url,
                $filename,
                $filesize,
                $filehash,
                $caption,
                $icon
            ));
        return true;
    }

    /**
     * Read 1024 bytes from the whatsapp server.
     */
    public function readStanza()
    {
        $buff = '';
        if($this->socket != null)
        {
            $header = @socket_read($this->socket, 3);//read stanza header
            if($header === false) {
                $error = "socket EOF, closing socket...";
                socket_close($this->socket);
                $this->socket = null;
                $this->eventManager()->fire("onClose",
                    array(
                        $this->phoneNumber,
                        $error
                    )
                );
            }

            if(strlen($header) == 0)
            {
                //no data received
                return;
            }
            if(strlen($header) != 3)
            {
                throw new ConnectionException("Failed to read stanza header");
            }
            $treeLength = (ord($header[0]) & 0x0F) << 16;
            $treeLength |= ord($header[1]) << 8;
            $treeLength |= ord($header[2]) << 0;

            //read full length
            $buff = socket_read($this->socket, $treeLength);
            $trlen = $treeLength;
            $len = strlen($buff);
            $prev = 0;
            while(strlen($buff) < $treeLength)
            {
                $toRead = $treeLength - strlen($buff);
                $buff .= socket_read($this->socket, $toRead);
                if($len == strlen($buff))
                {
                    //no new data read, fuck it
                    break;
                }
                $len = strlen($buff);
            }

            if (strlen($buff) != $treeLength) {
                throw new ConnectionException("Tree length did not match received length (buff = " . strlen($buff) . " & treeLength = $treeLength)");
            }
            $buff = $header . $buff;
        }
        else
        {
            $this->eventManager()->fire("onDisconnect",
                array(
                    $this->phoneNumber,
                    $this->socket
                ));
        }

        return $buff;
    }

    /**
     * Checks that the media file to send is of allowable filetype and within size limits.
     *
     * @param string $filepath          The URL/URI to the media file
     * @param int    $maxSize           Maximim filesize allowed for media type
     * @param string $to                Recipient ID/number
     * @param string $type              media filetype. 'audio', 'video', 'image'
     * @param array  $allowedExtensions An array of allowable file types for the media file
     * @param bool   $storeURLmedia     Keep a copy of the media file
     * @param string $caption
     *
     * @return bool
     */
    protected function sendCheckAndSendMedia($filepath, $maxSize, $to, $type, $allowedExtensions, $storeURLmedia, $caption = "")
    {
        if ($this->getMediaFile($filepath, $maxSize) == true) {
            if (in_array($this->mediaFileInfo['fileextension'], $allowedExtensions)) {
                $b64hash = base64_encode(hash_file("sha256", $this->mediaFileInfo['filepath'], true));
                //request upload
                $this->sendRequestFileUpload($b64hash, $type, $this->mediaFileInfo['filesize'], $this->mediaFileInfo['filepath'], $to, $caption);
                $this->processTempMediaFile($storeURLmedia);
                return true;
            } else {
                //Not allowed file type.
                $this->processTempMediaFile($storeURLmedia);
                return false;
            }
        } else {
            //Didn't get media file details.
            return false;
        }
    }

    /**
     * Send a broadcast
     * @param array  $targets Array of numbers to send to
     * @param object $node
     * @param        $type
     * @return string
     */
    protected function sendBroadcast($targets, $node, $type)
    {
        if (!is_array($targets)) {
            $targets = array($targets);
        }

        $toNodes = array();
        foreach ($targets as $target) {
            $jid = $this->getJID($target);
            $hash = array("jid" => $jid);
            $toNode = new ProtocolNode("to", $hash, null, null);
            $toNodes[] = $toNode;
        }

        $broadcastNode = new ProtocolNode("broadcast", null, $toNodes, null);

        $messageHash = array();
        $messageHash["to"] = time()."@broadcast";
        $messageHash["type"] = $type;
        $id = $this->createMsgId("broadcast");
        $messageHash["id"] = $id;

        $messageNode = new ProtocolNode("message", $messageHash, array($node, $broadcastNode), null);
        $this->sendNode($messageNode);
        //listen for response
        $this->eventManager()->fire("onSendMessage",
            array(
                $this->phoneNumber,
                $targets,
                $messageHash["id"],
                $node
            ));
        return $id;
    }

    /**
     * Send data to the whatsapp server.
     * @param string $data
     */
    protected function sendData($data)
    {
        if($this->socket != null)
        {
          if (socket_write($this->socket, $data, strlen($data)) === false)
          {

              $this->disconnect();
              throw new ConnectionException('Connection Closed!');

          }
        }
    }

    /**
     * Send the getGroupList request to Whatsapp
     * @param  string $type Type of list of groups to retrieve. "owning" or "participating"
     */
    protected function sendGetGroupsFiltered($type)
    {
        $msgID = $this->createMsgId("getgroups");
        $child = new ProtocolNode($type, null, null, null);
        $node = new ProtocolNode("iq", array(
            "id" => $msgID,
            "type" => "get",
            "xmlns" => "w:g2",
            "to" => "g.us"
                ), array($child), null);
        $this->sendNode($node);
        $this->waitForServer($msgID);
    }

    /**
     * Change participants of a group.
     *
     * @param string $groupId      The group ID.
     * @param array  $participants An array with the participants.
     * @param string $tag          The tag action. 'add' or 'remove'
     * @param        $id
     */
    protected function sendGroupsChangeParticipants($groupId, $participants, $tag, $id)
    {
        $_participants = array();
        foreach ($participants as $participant) {
            $_participants[] = new ProtocolNode("participant", array("jid" => $this->getJID($participant)), null, "");
        }

        $childHash = array();
        $child = new ProtocolNode($tag, $childHash, $_participants, "");

        $setHash = array();
        $setHash["id"] = $id;
        $setHash["type"] = "set";
        $setHash["xmlns"] = "w:g2";
        $setHash["to"] = $this->getJID($groupId);

        $node = new ProtocolNode("iq", $setHash, array($child), "");

        $this->sendNode($node);
        $this->waitForServer($setHash["id"]);
    }

    /**
     * Send node to the servers.
     *
     * @param              $to
     * @param ProtocolNode $node
     * @param null         $id
     * @return null|string
     */
    protected function sendMessageNode($to, $node, $id = null)
    {
        $messageHash = array();
        $messageHash["to"] = $this->getJID($to);
        if($node->getTag() == "body")
        {
            $messageHash["type"] = "text";
        }
        else
        {
            $messageHash["type"] = "media";
        }
        $messageHash["id"] = ($id == null?$this->createMsgId("message"):$id);
        $messageHash["t"] = time();

        $messageNode = new ProtocolNode("message", $messageHash, array($node), "");
        $this->sendNode($messageNode);
        $this->eventManager()->fire("onSendMessage",
            array(
                $this->phoneNumber,
                $this->getJID($to),
                $messageHash["id"],
                $node
            ));
        $this->waitForServer($messageHash["id"]);

        return $messageHash["id"];
    }

    /**
     * Tell the server we received the message.
     *
     * @param ProtocolNode $msg The ProtocolTreeNode that contains the message.
     * @param null         $type
     */
    protected function sendMessageReceived($msg, $type = "read", $participant = null)
    {

        $messageHash = array();
        if($type == "read")
            $messageHash["type"] = $type;
        if($participant != null)
            $messageHash["participant"] = $participant;
        $messageHash["to"] = $msg->getAttribute("from");
        $messageHash["id"] = $msg->getAttribute("id");
        $messageHash["t"] = time();
        $messageNode = new ProtocolNode("receipt", $messageHash, null, null);
        $this->sendNode($messageNode);
        $this->eventManager()->fire("onSendMessageReceived",
            array(
                $this->phoneNumber,
                $msg->getAttribute("id"),
                $msg->getAttribute("from"),
                $type
            ));
    }

    /**
     * Send node to the WhatsApp server.
     * @param ProtocolNode $node
     * @param bool         $encrypt
     */
    protected function sendNode($node, $encrypt = true)
    {
        $this->debugPrint($node->nodeString("tx  ") . "\n");
        $this->sendData($this->writer->write($node, $encrypt));
    }

    /**
     * Send request to upload file
     *
     * @param string $b64hash  A base64 hash of file
     * @param string $type     File type
     * @param string $size     File size
     * @param string $filepath Path to image file
     * @param string $to       Recipient
     * @param string $caption
     */
    protected function sendRequestFileUpload($b64hash, $type, $size, $filepath, $to, $caption = "")
    {
        $hash = array();
        $hash["hash"] = $b64hash;
        $hash["type"] = $type;
        $hash["size"] = $size;
        $mediaNode = new ProtocolNode("media", $hash, null, null);

        $hash = array();
        $id = $this->createMsgId("upload");
        $hash["id"] = $id;
        $hash["to"] = static::WHATSAPP_SERVER;
        $hash["type"] = "set";
        $hash["xmlns"] = "w:m";
        $node = new ProtocolNode("iq", $hash, array($mediaNode), null);

        if (!is_array($to)) {
            $to = $this->getJID($to);
        }
        //add to queue
        $messageId = $this->createMsgId("message");
        $this->mediaQueue[$id] = array("messageNode" => $node, "filePath" => $filepath, "to" => $to, "message_id" => $messageId, "caption" => $caption);

        $this->sendNode($node);
        $this->waitForServer($hash["id"]);
    }

    /**
     * Set your profile picture
     *
     * @param string $jid
     * @param string $filepath
     *  URL or localpath to image file
     */
    protected function sendSetPicture($jid, $filepath)
    {
        $data = preprocessProfilePicture($filepath);
        $preview = createIconGD($filepath, 96, true);

        $picture = new ProtocolNode("picture", array("type" => "image"), null, $data);
        $preview = new ProtocolNode("picture", array("type" => "preview"), null, $preview);

        $hash = array();
        $nodeID = $this->createMsgId("setphoto");
        $hash["id"] = $nodeID;
        $hash["to"] = $this->getJID($jid);
        $hash["type"] = "set";
        $hash["xmlns"] = "w:profile:picture";
        $node = new ProtocolNode("iq", $hash, array($picture, $preview), null);

        $this->sendNode($node);
        $this->waitForServer($nodeID);
    }
    /**
     * Parse the message text for emojis
     *
     * This will look for special strings in the message text
     * that need to be replaced with a unicode character to show
     * the corresponding emoji.
     *
     * Emojis should be entered in the message text either as the
     * correct unicode character directly, or if this isn't possible,
     * by putting a placeholder of ##unicodeNumber## in the message text.
     * Include the surrounding ##
     * eg:
     * ##1f604## this will show the smiling face
     * ##1f1ec_1f1e7## this will show the UK flag.
     *
     * Notice that if 2 unicode characters are required they should be joined
     * with an underscore.
     *
     *
     * @param string $txt
     * The message to be parsed for emoji code.
     *
     * @return string
     */
    private function parseMessageForEmojis($txt)
    {
        $matches = null;
        preg_match_all('/##(.*?)##/', $txt, $matches, PREG_SET_ORDER);
        if (is_array($matches)) {
            foreach ($matches as $emoji) {
                $txt = str_ireplace($emoji[0], $this->unichr((string) $emoji[1]), $txt);
            }
        }

        return $txt;
    }

    /**
     * Creates the correct unicode character from the unicode code point
     *
     * @param int $int
     * @return string
     */
    private function unichr($int)
    {
        $string = null;
        $multiChars = explode('_', $int);

        foreach ($multiChars as $char) {
            $string .= mb_convert_encoding('&#' . intval($char, 16) . ';', 'UTF-8', 'HTML-ENTITIES');
        }

        return $string;
    }

    /**
     * @param string $jid
     * @return string
     */
    public static function parseJID($jid)
    {
        $parts = explode('@', $jid);
        $parts = reset($parts);
        return $parts;
    }
}
