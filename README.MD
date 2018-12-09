## How to use



```php

class MY_Controller extends CI_Controller {

    use codeigniter_packages_remap;

    public function __construct(){

        parent::__construct();
        //need load below config
        $this->config->load('packages', TRUE);
        $this->config->load('themes', TRUE);

    }

    public function _remap($method, $params = array()) {

        $theme='bootstrap';
        $this->load_map($method, $params, $theme);

    }

}

```
