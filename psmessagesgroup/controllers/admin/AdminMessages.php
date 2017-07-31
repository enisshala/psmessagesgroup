<?php

require_once(dirname(__FILE__).'/../../classes/MessagesClass.php');

class AdminMessagesController extends ModuleAdminController
{
	
	public function __construct()
	{
		$this->table = 'message_group';
		$this->module = 'psmessagesgroup';
		$this->tab = new Tab($this->id);
		$this->className = 'MessagesClass';
		$this->lang = false;
		$this->identifier = 'id_message';
		$this->bootstrap = true;
		$this->context = Context::getContext();

		$this->fields_list = array(
			'id_message' => array(
				'title' => 'ID',
				'align' => 'center',
				'class' => 'fixed-width-xs'
			),
			'id_group' =>   array(
                'title' => 'Group ID',
			),			
			'message_content' => array(
				'title' => $this->l('Content')
			),
			'approved' => array(
				'title' => $this->l('Approved'),
				'active' => 'toggle',
				'type' => 'bool',
				'align' => 'center',
				'orderby' => false
			)
		);

		$this->bulk_action = array(
			'delete' => array(
				'text' => $this->l('Delete selected'),
				'icon' => 'icon-trash',
				'confirm' => $this->l('Delete selected items?')
				)
			);

		parent::__construct();
	}

	public function renderList()
	{
		if(isset($this->_filter) && trim($this->_filter) == '')
			$this->_filter = $this->original_filter;

		$this->addRowAction('add');
		$this->addRowAction('edit');
		$this->addRowAction('delete');

		return parent::renderList();

	}

	public function postProcess()
	{
		if (Tools::isSubmit('togglemessage_group'))
		{
			$id_message = Tools::getValue('id_message');
			if($this->module->toggleMessages($id_message))
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminMessages').'&conf=4');
			else
				$this->errors = $this->module->_errors;
		}
		return parent::postProcess();
	}

	public function renderForm()
	{	
		$groups = Group::getGroups($this->default_form_language);
		$inputs[] = array(
			'type' => 'textarea',
			'label' => $this->l('Content'),
			'name' => 'message_content',
			'required' => true,
			'hint' => $this->l('Write your message.')
			);
		
		// $inputs[] = array(
  //               'type' => 'select',
  //                   'label' => $this->l('Group Access'),
  //                   'name' => 'group_name',
  //                   'options' => array(
  //                       'query' => $groups,
  //                       'id' => 'name',
  //                       'name' => 'name'
  //                   ),
  //                   'hint' => $this->l('Select one group that you would like to show the messages.')
  //           );		
		
		$inputs[] = array(
                'type' => 'select',
                    'label' => $this->l('Group Access'),
                    'name' => 'id_group',
                    'required' => true,
                    'options' => array(
                        'query' => $groups,
                        'id' => 'id_group',
                        'name' => 'name'
                    ),
                    'hint' => $this->l('Select your group.'),
                       
                );

		$inputs[] = array(
			'type' => 'switch',
			'label' => $this->l('Approved'),
			'name' => 'approved',
			'values' => array(
				array(
					'id' => 'active_on',
					'value' => 1,
					'label' => $this->l('Enabled')
					),
				array(
					'id' => 'active_of',
					'value' => 0,
					'label' => $this->l('Disabled')
					)
				),
			'hint' => $this->l('Approved your selection')
			);
		
		$inputs[] = array(
			'type' => 'hidden',
			'label' => $this->l('Created at'),
			'name' => 'date_add',
			);

		$inputs[] = array(
			'type' => 'hidden',
			'label' => $this->l('Updated at'),
			'name' => 'date_upd',
			);

		$this->fields_form = array(
			'legend' => array(
				'title' => Tools::isSubmit('updatemessage_group') ? $this->l('Modify Messages') : $this->l('Add new Messages'),
				'icon' => 'icon-cogs'
				),
			'input' => $inputs,
			'submit' => array(
				'title' => Tools::isSubmit('updatemessage_group') ? $this->l('Update') : $this->l('Add'),
				'class' => 'btn btn-default pull-right'
				)
			);

		return parent::renderForm();

	}

	public function initPageHeaderToolbar()
	{
		if(!$this->display)
		{
			$this->page_header_toolbar_btn['new'] = array(
				'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
				'desc' => $this->l('Add New')
				);
		} else if($this->display == 'view')
		{
			$this->page_header_toolbar_btn['back'] = array(
				'href' => self::$currentIndex.'&token='.$this->token,
				'desc' => $this->l('Back to the list')
				);
		}

		parent::initPageHeaderToolbar();
	}


}