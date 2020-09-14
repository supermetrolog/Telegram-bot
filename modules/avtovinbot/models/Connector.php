<?php 
namespace app\modules\avtovinbot\models;

use app\modules\avtovinbot\exceptions\TelegramException;
use app\modules\avtovinbot\models\UpdateType;
use app\modules\avtovinbot\models\Telegram\Telegram;
use stdClass;

class Connector extends UpdateType
{
    private $update;
    private $result;
    public function __construct($update)
    {
		$this->update = $update;
		$this->result = new stdClass;
    }
    
	private function sortTextUpdate(){
		$this->result->text = $this->update->message->text;
		$this->result->update_type = self::TEXT;
	}
	 private function sortPreCheckoutQueryUpdate(){
		$this->result->update_id = $this->update->update_id;
		$this->result->id = $this->update->pre_checkout_query->id;
		$this->result->user_id = $this->update->pre_checkout_query->from->id;
	 	$this->result->language_code = $this->update->pre_checkout_query->from->language_code;
		$this->result->username = $this->update->pre_checkout_query->from->username;
		$this->result->name = $this->update->pre_checkout_query->from->first_name;
		$this->result->chat_id = $this->update->pre_checkout_query->from->id;
		$this->result->currency = $this->update->pre_checkout_query->currency;
		$this->result->total_amount = $this->update->pre_checkout_query->total_amount;
		$this->result->invoice_payload = $this->update->pre_checkout_query->invoice_payload;
		$this->result->order_info = $this->update->pre_checkout_query->order_info;
		$this->result->update_type = self::PRE_CHECKOUT_QUERY;
	}
	 private function sortSuccessfulPaymentUpdate(){
		$this->result->currency = $this->update->message->successful_payment->currency;
		$this->result->total_amount = $this->update->message->successful_payment->total_amount;
		$this->result->invoice_payload = $this->update->message->successful_payment->invoice_payload;
		$this->result->order_info = $this->update->message->successful_payment->order_info;
		$this->result->tpc_id = $this->update->message->successful_payment->telegram_payment_charge_id;
		$this->result->ppc_id = $this->update->message->successful_payment->provider_payment_charge_id;
		$this->result->update_type = self::SUCCESSFUL_PAYMENT;
	}
	 private function sortPhotoUpdate(){
		$this->result->text = $this->update->message->caption;
		if (isset($this->update->message->photo[2])) {
			$key = 2;
		}elseif (isset($this->update->message->photo[1])) {
			$key = 1;
		}else{
			$key = 0;
		}
		$this->result->file_id = $this->update->message->photo[$key]->file_id;
		$this->result->file_unique_id = $this->update->message->photo[$key]->file_unique_id;
		$this->result->file_size = $this->update->message->photo[$key]->file_size;
		$this->result->update_type = self::PHOTO;
	}

	 private function sortDocumentUpdate(){
		$this->result->text = $this->update->message->caption;
		$this->result->file_name = $this->update->message->document->file_name;
		$this->result->mime_type = $this->update->message->document->mime_type;
		$this->result->file_id = $this->update->message->document->file_id;
		$this->result->file_unique_id = $this->update->message->document->file_unique_id;
		$this->result->file_size = $this->update->message->document->file_size;
		$this->result->thumb_file_id = $this->update->message->document->thumb->file_id;
		$this->result->thumb_file_unique_id = $this->update->message->document->thumb->file_unique_id;
		$this->result->thumb_file_size = $this->update->message->document->thumb->file_size;
		$this->result->update_type = self::DOCUMENT;
	}
	 private function sortCallbackQueryUpdate(){
		$this->result->update_id = $this->update->update_id;
		$this->result->user_id = $this->update->callback_query->from->id;
	 	$this->result->language_code = $this->update->callback_query->from->language_code;
		$this->result->username = $this->update->callback_query->from->username;
		$this->result->name = $this->update->callback_query->from->first_name;
		$this->result->message_id = $this->update->callback_query->message->message_id;
		$this->result->chat_id = $this->update->callback_query->message->chat->id;
		//$this->result['date'] = $this->update->callback_query->message['date'];
		$this->result->inline_text = $this->update->callback_query->message->text;
		$this->result->text = $this->update->callback_query->data;
		$this->result->update_type = self::CALLBACK_QUERY;
	}
	
	 private function sortLocationUpdate(){
		$this->result->latitude = $this->update->message->location->latitude;
		$this->result->longitude = $this->update->message->location->longitude;
		$this->result->update_type = self::LOCATION;
	}
	 private function sortContactUpdate(){
		$this->result->phone_number = $this->update->message->contact->phone_number;
		$this->result->update_type = self::CONTACT;
	}
	 private function sortEditedUpdate(){
		$this->result->update_id = $this->update->update_id;
		$this->result->update_type = self::EDITED_MESSAGE;
  }
  

  
	 public function sortUpdate(){
		
		// var_dump($this->update->update_id);
		if ($this->update->update_id == '') {
			throw new TelegramException('No messages!');
			return false;
        }  
		Telegram::changeOffset($this->update->update_id);
		if (isset($this->update->callback_query)) {
			$this->sortCallbackQueryUpdate();
			return $this->typeConvertion();
		}elseif (isset($this->update->edited_message)) {
			$this->sortEditedUpdate();
			return $this->typeConvertion();
		}elseif (isset($this->update->pre_checkout_query)) {
			$this->sortPreCheckoutQueryUpdate();
			return $this->typeConvertion();
		}
		$this->result->update_id = $this->update->update_id;

		$this->result->message_id = $this->update->message->message_id;

		$this->result->user_id = $this->update->message->from->id;
		$this->result->language_code = $this->update->message->from->language_code;
		$this->result->username = $this->update->message->from->username;
		$this->result->name = $this->update->message->from->first_name;

		$this->result->chat_id = $this->update->message->chat->id;

		//$this->result['date'] = $this->update->message['date'];

		if (isset($this->update->message->text)) {
			$this->sortTextUpdate();
			return $this->typeConvertion();
		}elseif (isset($this->update->message->photo)) {
			$this->sortPhotoUpdate();
			return $this->typeConvertion();
		}elseif (isset($this->update->message->document)) {
			$this->sortDocumentUpdate();
			return $this->typeConvertion();
		}elseif (isset($this->update->message->contact)) {
			$this->sortContactUpdate();
			return $this->typeConvertion();
		}elseif (isset($this->update->message->location)) {
			$this->sortLocationUpdate();
			return $this->typeConvertion();
		}elseif (isset($this->update->message->successful_payment)) {
			$this->sortSuccessfulPaymentUpdate();
			return $this->typeConvertion();
		}
		
		return $this->typeConvertion();
	}

	private function typeConvertion(){
		foreach ($this->result as $key => $item) {
			$this->result->$key = (string)$item;
		}
		return $this->result;
	}
}
