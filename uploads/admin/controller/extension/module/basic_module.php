<?php
/**
 * Empty template of module for Opencart 2.3
 * The controller class must extend the parent class i.e. Controller
 * The controller name must be like Controller + directory path (with first character of each folder in capital) + file name (with first character in capital)
 * For version 2.3.0.0 and upper, the name of the controller must be ControllerExtensionModuleFirstModule
 */
class ControllerExtensionModuleBasicModule extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/basic_module');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->model_setting_setting->editSetting('basic_module', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
        }

        $data = $this->load->language('extension/module/basic_module');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('extension/module/basic_module', 'token=' . $this->session->data['token'], true)
        );

        $data['action'] = $this->url->link('extension/module/basic_module', 'token=' . $this->session->data['token'], true);

        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);


        if (isset($this->request->post['basic_module'])) {
            $data['basic_module'] = $this->request->post['basic_module'];
        } else {
            $data['basic_module'] = $this->config->get('basic_module');
        }

        if (isset($this->request->post['basic_module_sort_order'])) {
            $data['basic_module_sort_order'] = $this->request->post['basic_module_sort_order'];
        } else {
            $data['basic_module_sort_order'] = $this->config->get('basic_module_sort_order');
        }


        //Example of loading a model
        $this->load->model('customer/customer_group');
        $customer_groups = $this->model_customer_customer_group->getCustomerGroups(array('sort' => 'cg.sort_order'));
        $discounts = $this->config->get('basic_module_customer_group_id');

        foreach($customer_groups as $key => $group){
            if(isset($discounts[$group['customer_group_id']])){
                $customer_groups[$key]['discount'] = $discounts[$group['customer_group_id']];
            }else{
                $customer_groups[$key]['discount'] = 0;
            }
        }

        $data['customer_groups'] = $customer_groups;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/basic_module.tpl', $data));
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/basic_module')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}