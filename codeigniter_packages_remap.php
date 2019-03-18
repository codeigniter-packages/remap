<?php

defined('BASEPATH') OR exit('No direct script access allowed');

trait codeigniter_packages_remap {

    public function load_map($method, $params = array(),$theme_name) {

        if (!method_exists($this, $method)) {
            show_404();
        }

        
        $data = call_user_func_array(array($this, $method), $params);

        if ($this->input->is_ajax_request() OR $this->input->server('REQUEST_METHOD') == 'POST') {
            //IE8不支持application/json，上传的时候会出错，不显示返回json格式的消息
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        } else {

            //加载视图配置
            $theme=$this->config->item($theme_name, 'themes');

            //设置主题的全局设置
            foreach ($theme['global'] as $key) {
                if(isset($theme[$key])){
                    $data[$key]=$theme[$key];
                }
            }

            //设置控制器参数
            $controller_name = $this->router->fetch_class();
            $controller_configs_default=array(
                'routers'=>array(
                    $method=>array(
                        'components'=>array()
                    )
                )
            );
            $controller_configs = isset($theme['routers'][$controller_name])?$theme['routers'][$controller_name]:$controller_configs_default;
            //取出控制器中方法的参数
            $method_configs = $controller_configs['routers'][$method];
            //删除控制器下的方法配置参数，当前method的配置已经在$method_configs
            unset($theme['routers'][$controller_name]['routers']);

            foreach ($controller_configs as $key=>$value) {
                $data[$key]=(isset($data[$key]) && is_array($data[$key]))?array_merge($data[$key],$value):$value;
            }

            foreach ($method_configs as $key=>$value) {
                $data[$key]=(isset($data[$key]) && is_array($data[$key]))?array_merge($data[$key],$value):$value;
            }

            if(isset($data['components'])){
                foreach ($data['components'] as $key => $value) {
                    foreach ($value as $i => $component_path) {
                       $data['components'][$key][$i]=$theme_name.'/'.$component_path;
                    }
                }
            }

            stristr($theme['index'],':') === FALSE?$this->load->view($theme['index'],$data):$this->load->package($theme['index'],$data);

        }
    }

   
}
