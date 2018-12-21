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
            //获取控制器中调用的方法
            $method=$this->router->fetch_class().'/'.$method;

            //设置主题的全局设置
            foreach ($theme['global'] as $key) {
                if(isset($theme[$key])){
                    $data[$key]=$theme[$key];
                }
            }

            if(!isset($theme[$method])){
                $theme[$method]=array();
            }
            //从视图配置中通过控制器调用的方法得到该页面对应的视图配置
            //将视图配置文件使用到的组件添加上完整的路径
            foreach ($theme[$method] as $key => $components) {
                //设置每个页面的全局变量
                if(in_array($key,$theme['global'])){
                    $data[$key]=(isset($theme[$key]) && is_array($theme[$key]))?
                    array_merge($theme[$key],$theme[$method][$key]):$theme[$method][$key];
                    continue;
                }

                if(!is_array($components)){
                    continue;
                }
                foreach ($components as $component_name) {
                    $data[$key][]=$theme_name.'/'.$component_name;
                }
            }


            stristr($theme['index'],':') === FALSE?$this->load->view($theme['index'],$data):$this->load->package($theme['index'],$data);

        }
    }

   
}