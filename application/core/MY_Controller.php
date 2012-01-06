<?php /**
 * A base controller that provides clever model 
 * loading, view loading and layout support.
 *
 * @package CodeIgniter
 * @subpackage MY_Controller
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.txt>
 * @link http://github.com/jamierumbelow/codeigniter-base-controller
 * @version 1.1.1
 * @author Jamie Rumbelow <http://jamierumbelow.net>
 * @copyright Copyright (c) 2009, Jamie Rumbelow <http://jamierumbelow.net>
 */
class MY_Controller extends CI_Controller
{

    /**
     * The view to load, only set if you want
     * to bypass the autoload magic.
     *
     * @var string
     */
    protected $view;

    /**
     * The data to pass to the view, where
     * the keys are the names of the variables
     * and the values are the values.
     *
     * @var array
     */
    public $data = array();

    /**
     * The layout to load the view into. Only
     * set if you want to bypass the magic.
     *
     * @var string
     */
    protected $layout;

    /**
     * An array of asides. The key is the name
     * to reference by and the value is the file.
     * The class will loop through these, parse them 
     * and push them via a variable to the layout. 
     * 
     * This allows any number of asides like sidebars,
     * footers etc. 
     *
     * @var array
     * @since 1.1.0
     */
    protected $asides = array();

    protected $plugins = array();

    /**
     * Prefix of the partial filename
     *
     * @var string
     */
    protected $partial_prefix = '_';

    /**
     * The models to load into the controller.
     *
     * @var array
     */
    protected $models = array();

    /**
     * The model name formatting string. Use the
     * % symbol, which will be replaced with the model
     * name. This allows you to use model names like
     * m_model, model_m or model_model_m. Do whatever
     * suits you.
     *
     * @since 1.2.0
     * @var string
     */
    protected $model_string = '%_model';

    /**
     * The prerendered data for output buffering
     * and the render() method. Generally left blank.
     *
     * @since 1.1.1
     * @var string
     */
    protected $prerendered_data = '';

    public $user_id;
    public $languages = array();

    /**
     * The class constructor, loads the models
     * from the $this->models array.
     *
     * Can't extend the default controller as it
     * can't load the default libraries due to __get()
     *
     * @author Jamie Rumbelow
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('language');
        $this->load->config('lang');

        // Current Language
        $lang = $this->uri->segment(1);

        // Available Languages (Array)
        $this->languages = $this->config->item('languages');

        if (empty($lang) || !array_key_exists($lang, $this->languages))
        {
            $lang = 'de';
        }

        $this->current_lang = $lang;
        $this->current_language = $this->languages[$lang];
        $this->config->set_item('lang', $lang);
        $this->config->set_item('language', $this->languages[$lang]);

        $this->_load_models();
    }

    /**
     * Called by CodeIgniter instead of the action
     * directly, automatically loads the views.
     *
     * @param string $method The method to call
     * @return void
     * @author Jamie Rumbelow
     */
    public function _remap($method)
    {
        if (method_exists($this, $method))
        {
            call_user_func_array(array($this, $method), array_slice($this->uri->rsegments, 2));
        } else
        {
            if (method_exists($this, '_404'))
            {
                call_user_func_array(array($this, '_404'), array($method));
            } else
            {
                show_404(strtolower(get_class($this)) . '/' . $method);
            }
        }

        $this->_load_view();
    }

    /**
     * Loads the view by figuring out the
     * controller, action and conventional routing.
     * Also takes into account $this->view, $this->layout
     * and $this->sidebar.
     *
     * @return void
     * @access private
     * @author Jamie Rumbelow
     */
    public function _load_view()
    {
        if ($this->view !== false)
        {
            $view = ($this->view !== null) ? $this->view . '.php' : $this->router->directory . $this->router->class . '/' . $this->router->method . '.php';
            $data['yield'] = $this->prerendered_data;
            $data['yield'] .= $this->load->view($view, $this->data, true);

            if (!empty($this->asides))
            {
                foreach ($this->asides as $name => $files)
                {
                    foreach ($files as $index => $file)
                    {
                        $data['fragments'][$name][] = $this->load->view($file, $this->data, true);
                    }
                }
            }

            $data['plugins'] = '';

            if (!empty($this->plugins))
            {
                foreach ($this->plugins as $name => $plugin)
                {
                    $data['plugins'] .= $this->load->view('plugins/' . $plugin, array(), true);
                }
            }

            $data = array_merge($this->data, $data);

            $data['page_title'] = $this->getPageTitle();

            if (!isset($this->layout))
            {
                if (defined('CLIENT_PATH') && file_exists(CLIENT_PATH . 'views/layouts/' . $this->router->class . '.php'))
                {
                    $this->load->view('layouts/' . $this->router->class . '.php', $data);
                } elseif (file_exists(APPPATH . 'views/layouts/' . $this->router->class . '.php'))
                {
                    $this->load->view('layouts/' . $this->router->class . '.php', $data);
                } else
                {
                    $this->load->view('layouts/application.php', $data);
                }
            } else
                if ($this->layout !== false)
                {
                    $this->load->view('layouts/' . $this->layout . '.php', $data);
                } else
                {
                    $this->output->set_output($data['yield']);
                }
        }
    }

    /**
     * Loads the models from the $this->model array.
     *
     * @return void
     * @author Jamie Rumbelow
     */
    private function _load_models()
    {
        foreach ($this->models as $model)
        {
            $this->load->model($this->_model_name($model), $model, true);
        }
    }

    /**
     * Returns the correct model name to load with, by
     * replacing the % symbol in $this->model_string.
     *
     * @param string $model The name of the model
     * @return string
     * @since 1.2.0
     * @author Jamie Rumbelow
     */
    protected function _model_name($model)
    {
        return str_replace('%', $model, $this->model_string);
    }

    /**
     * A helper method for controller actions to stop
     * from loading any views.
     *
     * @return void
     * @author Jamie Rumbelow
     */
    protected function _pass()
    {
        $this->view = false;
    }

    /**
     * A helper method to check if a request has been
     * made through XMLHttpRequest (AJAX) or not 
     *
     * @return bool
     * @author Jamie Rumbelow
     */
    protected function is_ajax()
    {
        return ($this->input->server('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest') ? true : false;
    }

    /**
     * Renders the current view and adds it to the 
     * output buffer. Useful for rendering more than one
     * view at once.
     *
     * @return void
     * @since 1.0.5
     * @author Jamie Rumbelow
     */
    protected function render()
    {
        $this->prerendered_data .= $this->load->view($this->view, $this->data, true);
    }

    /**
     * Partial rendering method, generally called via the helper.
     * renders partials and returns the result. Pass it an optional 
     * data array and an optional loop boolean to loop through a collection.
     *
     * @param string $name The partial name
     * @param array $data The data or collection to pass through
     * @param boolean $loop Whether or not to loop through a collection
     * @return string
     * @since 1.1.0
     * @author Jamie Rumbelow and Jeremy Gimbel
     */
    public function partial($name, $data = null, $loop = true, $cache = false)
    {
        $partial = '';
        $name = $this->router->directory . $this->router->class . '/' . $this->partial_prefix . $name;

        if (!isset($data))
        {
            $partial = $this->load->view($name, array(), true);
        } else
        {
            if ($loop == true)
            {
                foreach ($data as $row)
                {
                    $partial .= $this->load->view($name, (array )$row, true);
                }
            } else
            {
                $partial .= $this->load->view($name, $data, true);
            }
        }

        return $partial;
    }

    public function component($method, $args, $cache_type = false, $component_cache_expiration = 60, $serialized = false)
    {
        if (method_exists($this, $method))
        {
            // Is caching used?
            if ($cache_type)
            {
                $cache_path = $this->config->item('cache_path');

                $URI->uri_string = 'component_' . $method;
                $component_cache_path = 'system/cache/';

                switch ($cache_type)
                {
                    case 'user':
                        $component_cache_path .= 'component' . DIRECTORY_SEPARATOR . $method . DIRECTORY_SEPARATOR . 'user' . DIRECTORY_SEPARATOR . $this->user_id . DIRECTORY_SEPARATOR;
                        break;
                    default:
                        $component_cache_path .= 'component' . DIRECTORY_SEPARATOR . $method . DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR;
                        break;
                }

                $this->config->set_item('cache_path', $component_cache_path);

                ob_start();
                if ($this->output->_display_cache($this->config, $URI))
                {
                    $output = ob_get_contents();
                    ob_end_clean();

                    if ($serialized)
                    {
                        $output = unserialize($output);
                    }
                    $this->setData($method, $output);
                    return;
                }
                ob_end_clean();
            }

            $output = call_user_func_array(array($this, $method), $args);
            $this->setData($method, $output);

            // No cache > don't write cache and return
            if (!$cache_type)
            {
                return;
            }

            if ($serialized)
            {
                $output = serialize($output);
            }


            $this->load->helper('directory');
            mkdirRecursive($component_cache_path, true);
            // echo $component_cache_path;
            $this->uri->overrideUriString($URI->uri_string);
            $cache_expiration = $this->output->cache_expiration;
            $this->output->cache_expiration = ($component_cache_expiration * 60);
            $this->output->_write_cache($output);
            $this->output->cache_expiration = $cache_expiration;

            // set org. cache path
            $this->config->set_item('cache_path', $cache_path);

        }
    }


    public function setView($view)
    {
        $this->view = $view;
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }


    public function setData($key, $data, $subkey = false)
    {
        if ($subkey)
        {
            $this->data[$key][$subkey] = $data;
        } else
        {
            $this->data[$key] = $data;
        }

    }

    public function getData($key)
    {
        return array_key_exists($key,$this->data) ? $this->data[$key] : '';
    }

    public function addView($aside, $name, $dir = false)
    {
        if ($dir == false)
        {
            $dir = $this->router->directory . $this->router->class . '/';
        }
        $this->asides[$name][] = $dir . $aside . '.php';
    }

    public function setPageTitle($title)
    {
        $this->data['page_title'][] = $title;
    }

    public function getPageTitle()
    {
        if (!isset($this->data['page_title']) || !is_array($this->data['page_title']))
        {
            if (isset($this->data['headline']))
            {
                $this->data['page_title'][] = $this->data['headline'];
            }
            if (isset($this->data['subheadline']))
            {
                $this->data['page_title'][] = $this->data['subheadline'];
            }
        }
        
        $this->data['page_title'][] = $this->config->item('site_name');

        return implode(' - ', $this->data['page_title']); 
    }

    public function addPlugin($plugin)
    {
        if (!array_key_exists($plugin, $this->plugins))
        {
            $this->plugins[$plugin] = $plugin;
        }
    }
}

/**
 * Partial rendering helper method, renders partials
 * and returns the result. Pass it an optional data array
 * and an optional loop boolean to loop through a collection.  
 * 
 * NOTE FROM JEREMY: If you are a 'elitist bastard' feel free
 * 					 to chuck this in a helper, but we really
 *					 don't care, because Jamie's Chieftain.
 *
 * @param string $name The partial name
 * @param array $data The data or collection to pass through
 * @param boolean $loop Whether or not to loop through a collection
 * @return string
 * @since 1.1.0
 * @author Jamie Rumbelow and Jeremy Gimbel
 */
function partial($name, $data = null, $loop = true)
{
    $ci = &get_instance();
    return $ci->partial($name, $data, $loop);
}
