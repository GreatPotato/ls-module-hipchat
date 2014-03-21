<?php

class HipChat_Module extends Core_ModuleBase
{
	/**
	 * Generate the module information
	 *
	 * @access protected
	 * @return Core_ModuleInfo
	 */
	protected function createModuleInfo()
	{
		return new Core_ModuleInfo(
			'HipChat',
			'HipChat integration to send notifications straight to chat',
			'Aaron Hodges'
		);
	}
	
	
	public function subscribeEvents()
	{
		Backend::$events->addEvent('shop:onNewOrder', $this, 'notify_new_order');
		Backend::$events->addEvent('shop:onCustomerCreated', $this, 'notify_new_customer');
	}
	
	
	public function notify_new_order($order_id)
	{
		$order = Shop_Order::create()->find($order_id);
		
		$message = 'Notification: A new order was created - <a href="'. site_url(url('/shop/orders/preview/', true, true) . $order->id) . '"><strong>#' . $order->id . '</strong></a>';
		
		$hipchat = new HipChat_Api();
		$hipchat->post_room_message($message, 'yellow');
	}
	
	
	public function notify_new_customer($customer)
	{
		$message = 'Notification: A new customer has signed up - <a href="'. site_url(url('/shop/customers/preview/', true, true) . $customer->id) . '"><strong>' . $customer->first_name . ' ' . $customer->last_name . '</strong></a>';
	
		$hipchat = new HipChat_Api();
		$hipchat->post_room_message($message, 'blue');
	}
	

	public function listSettingsForms()
	{
		return array(
			'hipchat' => array(
				'icon' => '/modules/hipchat/resources/images/hipchat.png',
				'title' => 'HipChat',
				'description' => 'Configure and edit HipChat settings',
				'sort_id' => 1000,
				'section' => 'Social'
			)
		);
	}
	
	
	public function buildSettingsForm($model, $form_code)
	{
		switch ($form_code)
		{
			case 'hipchat':
				$model->add_field('authentication_code', 'Authentication code', 'full', db_varchar)->tab('Settings');
				$model->add_field('default_room', 'Default room', 'full', db_varchar)->tab('Settings');
			break;
		}
	}
	
	public function validateSettingsData($model, $form_code)
	{
		if ( empty($model->authentication_code) )
			$model->validation->setError('Please enter an authentication code', 'authentication_code', true);
			
		if ( empty($model->default_room) )
			$model->validation->setError('Please enter a valid HipChat room name', 'default_room', true);
	}
}	