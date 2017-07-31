<?php

if (!defined('_PS_VERSION_'))
	exit;


class psMessagesGroup extends Module
{

	private $_html = '';

	function __construct()
	{
		$this->name = 'psmessagesgroup';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'Enis Shala';
		$this->need_instance = 0;
		$this->table_name = 'message_group';

		$this->bootstrap = true;

	 	parent::__construct();

		$this->displayName = $this->l('Messages access for groups');
		$this->description = $this->l('Define which messages can be accessed by specific customer groups');
		$this->confirmUninstall = $this->l('Are you sure you want to do it?');

	}

	public function install()
	{
		if (!parent::install() OR
			!$this->_createTabs() OR
			!$this->_installTable() OR
			!$this->registerHook('displayNav') OR
			!$this->registerHook('displayBackOfficeHeader') 
			)
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!parent::uninstall() OR
			!$this->_eraseTabs() OR
			!$this->_eraseTable())
			return false;
		return true;
	}

	private function _createTabs()
	{
		$tab = new Tab();
		$tab->active = 1;
		$languages = Language::getLanguages(false);
		if(is_array($languages))
		{
			foreach ($languages as $language)
			{
				$tab->name[$language['id_lang']] = $this->displayName;
			}
		}
		$tab->class_name = 'AdminMessages';
		$tab->module = $this->name;
		$tab->id_parent = 0;

		return (bool)$tab->add();
	}

	private function _eraseTabs()
	{
		$id_tab = (int)Tab::getIdFromClassName('AdminMessages');
		if($id_tab)
		{
			$tab = new Tab($id_tab);
			$tab->delete();
		}
		return true;
	}
	private function _installTable()
	{
		$sql = '
			CREATE TABLE `'._DB_PREFIX_.$this->table_name .'` (
				`id_message` INT(12) NOT NULL AUTO_INCREMENT,
				`id_group` INT (12) NOT NULL,
				`message_content` VARCHAR(255) NOT NULL,
				`approved` TINYINT NOT NULL,
				`date_add` DATETIME NOT NULL,
				`date_upd` DATETIME NOT NULL,
				PRIMARY KEY  (`id_message`) 
				) ENGINE = ' ._MYSQL_ENGINE_;
		if(!Db::getInstance()->Execute($sql))
			return false;
		return true;
	}

	private function _eraseTable()
	{
		if(!Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.$this->table_name.'`'))
			return false;
		return true;
	}

	
	public function toggleMessages($id_message)
	{
		if(Db::getInstance()->getValue('SELECT approved FROM '._DB_PREFIX_.$this->table_name.' WHERE id_message ='.(int)$id_message))
		{
			// try to disable
			if(!Db::getInstance()->update($this->table_name, array('approved' => 0), 'id_message = '.(int)$id_message))
				$this->_errors[] = Tools::displayError('Error:') .' ' . mysql_error();
			else return true;
		} else {
			// try to enable
			if(!Db::getInstance()->update($this->table_name, array('approved' => 1), 'id_message = '.(int)$id_message))
				$this->_errors[] = Tools::displayError('Error:') .' ' . mysql_error();
			else return true;
		}
	}

	public function hookDisplayNav($params)
	{						
		$messages = Db::getInstance()->getValue('
										SELECT message_content
										FROM '._DB_PREFIX_.'message_group
										WHERE (id_group ='.(int)Group::getCurrent()->id.'
										AND approved = 1)'
										);
			$this->context->smarty->assign(array(
				'messages' => $messages
			));
		return $this->display(__FILE__, 'customerAccount.tpl');	
	}

	public function hookDisplayBackOfficeHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'views/css/psmessagesgroup.css');
	}

	public function enable($force_all = false)
	{
		Tools::clearCache();
		parent::enable();
	}

	public function disable($force_all = false)
	{
		Tools::clearCache();
		parent::disable();
	}
	
}