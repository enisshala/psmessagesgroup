<?php 

class MessagesClass extends ObjectModel
{
	public $id_messages;
	public $message_content;
	//public $group_name;
	public $id_group;
	public $approved;
	public $date_add;
	public $date_upd;

	public static $definition = array(
		'table' => 'message_group',
		'primary' => 'id_message',
		'fields' => array(
			'message_content' 	=> 	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
			//'group_name' 	=> 	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
			'id_group' 			=> array('type' => self::TYPE_INT, 'validate' => 'isInt','required' => true),
			'approved' 			=> 	array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'date_add' 			=> array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' 			=> array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			)
		);
}