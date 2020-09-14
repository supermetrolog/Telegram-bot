<?php
namespace app\modules\avtovinbot\models;
class UpdateType
{
    public const TEXT = 'text';
    public const PRE_CHECKOUT_QUERY = 'pre_checkout_query';
    public const SUCCESSFUL_PAYMENT = 'successful_payment';
    public const PHOTO = 'photo';
    public const DOCUMENT = 'document';
    public const CALLBACK_QUERY = 'callback_query';
    public const LOCATION = 'location';
    public const CONTACT = 'contact';
    public const EDITED_MESSAGE = 'edited_message';
}
