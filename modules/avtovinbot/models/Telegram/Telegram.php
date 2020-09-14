<?php
namespace app\modules\avtovinbot\models\Telegram;
// use app\modules\bot\models\DB;

use app\modules\avtovinbot\exceptions\TelegramException;
use yii\base\Model;


use Yii;
class Telegram extends Model{
	private const TEXT_TYPE = 'text';
	private const PHOTO_TYPE = 'photo';
	private const MEDIA_TYPE = 'media';
	private $URL = 'https://api.telegram.org/';
	private $TOKEN;
	private $DOMAIN_HOOK;


	function __construct ($token, $domain_hook){
		$this->set($token, $domain_hook);
	}

	private function set($token, $domain_hook){
		$this->TOKEN = $token;
		$this->DOMAIN_HOOK = $domain_hook;
	}

	public static function Request ($url, $postdata = null){


		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0');

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);

		//curl_setopt($ch, CURLOPT_PROXY, '85.10.219.102:1080');
		//curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_MAXCONNECTS, 1);

		if($postdata){
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		}

		$html = curl_exec($ch);
		echo curl_error($ch);
		if (!$html) {
			return false;
		}
		curl_close($ch);
		return json_decode($html, false);
	}

	// public function tg_error_log($responce, $data){
	// 	$log_path = Yii::getAlias('@app').'/config/tg_error.log';
	// 	// $current = DB::getState($data['chat_id']);
	// 	$log = date('Y-m-d H:i:s')."\n".print_r($responce, true)."\n".print_r($data, true)."\n".print_r($current, true);

	// 	return file_put_contents($log_path, $log . PHP_EOL, FILE_APPEND);
	// }
	public function is_Ok($responce, $data = null){
		// if (!$responce['ok']) {
		// 	var_dump($responce);
		// 	var_dump($data);
		// 	$this->tg_error_log($responce, $data);
		// 	if ($responce['error_code'] == 403) {
		// 		// DB::blockedBot($data['chat_id']);
		// 	}
		// 	return false;
		// }
		if (!$responce->ok) {
			print_r($responce);
			print_r($data);
			throw new TelegramException('Telegram: '.$responce->description);
		}
		print_r($data);
		return $responce;
	}



	public function setWebhook(){

		$url = $this->URL . $this->TOKEN . '/setWebhook?url='.$this->DOMAIN_HOOK;
		print_r($url);
		return $this->is_Ok($this->Request($url));
	}
	public function deleteWebhook(){
		$url = $this->URL . $this->TOKEN . '/setWebhook?url=';
		return $this->is_Ok($this->Request($url));
	}
	public function getWebhookInfo(){
		$url = $this->URL . $this->TOKEN . '/getWebhookInfo';
		return $this->is_Ok($this->Request($url));
	}
	public function getWebhookUpdate(){
		$hookUrl = 'php://input';
		return json_decode(file_get_contents($hookUrl), true);
	}
	public function getFile($data){
		$url = $this->URL . $this->TOKEN .'/getfile?'.http_build_query($data);
    	return $this->is_Ok($this->Request($url), $data);
	}
	public function downloadFile($data){
		$url = $this->URL .'file/'. $this->TOKEN .'/'.$data;
		return file_get_contents($url);
	}
	public function sendMessage($data){
  		$url = $this->URL . $this->TOKEN .'/sendmessage?'.http_build_query($data);
    	return $this->is_Ok($this->Request($url), $data);
	}

	public function sendPhoto($data){
  		$url = $this->URL . $this->TOKEN .'/sendphoto?'.http_build_query($data);
    	return $this->is_Ok($this->Request($url), $data);
	}
	public function send($data){
		// var_dump($data);
		$messageType = $this->getMessageType($data);
		switch ($messageType) {
			case self::TEXT_TYPE:
				return $this->sendMessage($data);
				break;
			case self::PHOTO_TYPE:
				return $this->sendPhoto($data);
				break;
			default:
				throw new TelegramException('Undefine message type');
				break;
		}
	}

	private function getMessageType($data){
		if (array_key_exists(self::PHOTO_TYPE, $data)) {
			return self::PHOTO_TYPE;
		}
		return self::TEXT_TYPE;
	}
	
	public function deleteMessage($data){
  		$url = $this->URL . $this->TOKEN .'/deletemessage?'.http_build_query($data);
    	return $this->is_Ok($this->Request($url), $data);
	}
	public function editMessageReplyMarkup($data){
  		$url = $this->URL . $this->TOKEN .'/editmessagereplymurkup?'.http_build_query($data);
    	return $this->is_Ok($this->Request($url), $data);
	}
	public function editMessageMedia($data){
  		$url = $this->URL . $this->TOKEN .'/editmessagemedia?'.http_build_query($data);
    	return $this->is_Ok($this->Request($url), $data);
	}
	public function editMessageCaption($data){
  		$url = $this->URL . $this->TOKEN .'/editmessagecaption?'.http_build_query($data);
    	return $this->is_Ok($this->Request($url), $data);
	}
	public function editMessageText($data){
  		$url = $this->URL . $this->TOKEN .'/editmessagetext?'.http_build_query($data);
    	return $this->is_Ok($this->Request($url), $data);
	}

	private function getEditMessageType($data){
		if (array_key_exists(self::MEDIA_TYPE, $data)) {
			return self::MEDIA_TYPE;
		}
		return self::TEXT_TYPE;
	}
	public function edit($data){
		// var_dump($data);
		$messageType = $this->getEditMessageType($data);
		switch ($messageType) {
			case self::TEXT_TYPE:
				return $this->editMessageText($data);
				break;
			case self::MEDIA_TYPE:
				return $this->editMessageMedia($data);
				break;
			default:
				throw new TelegramException('Undefine edit message type');
				break;
		}
	}
	public function getUpdates(){
		$offsetPath = Yii::getAlias('@app').'/components/offset.txt';
		$offset = file_get_contents($offsetPath);

		$url = $this->URL . $this->TOKEN . '/getUpdates?offset='.$offset;
		return $this->is_Ok($this->Request($url));
	}
	public static function changeOffset($offset){
		$offset++;
		$offsetPath = Yii::getAlias('@app'). '/components/offset.txt';
		if (!file_put_contents($offsetPath, $offset)) {
			return false;
		}
		return true;
	}

	public function sendInvoice($data){
		$url = $this->URL . $this->TOKEN .'/sendinvoice?'.http_build_query($data);
    	return $this->is_Ok($this->Request($url), $data);
	}
	public function answerPreCheckoutQuery($data){
		$url = $this->URL . $this->TOKEN .'/answerprecheckoutquery?'.http_build_query($data);
    	return $this->is_Ok($this->Request($url), $data);
	}

	public function RUPOR($data, $chat_id_list){
		foreach ($chat_id_list as $chat_id) {
			$data['chat_id'] = $chat_id['chat_id'];
			$this->sendMessage($data);
		}
		return true;
	}
}
