<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * CodeIgniter Image Manipulation Class extended with function
 * for converting images
 * Usage:
 *   $config['source_image'] = './uploads/my_pic.png';
 *   $this->image_lib->initialize($config);
 *   $this->image_lib->convert('jpg', TRUE);
 *
 *
 * @package CodeIgniter
 * @subpackage MY_Image_lib
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.txt>
 * @link http://codeigniter.com/forums/viewthread/145527/
 * @version 1.3
 * @author Ripe <http://codeigniter.com/forums/member/119227/>
 * @modified waldmeister <http://codeigniter.com/forums/member/57608/>
 * @modified ebspromo <http://codeigniter.com/forums/member/167796/>
 */
class MY_Image_lib extends CI_Image_lib
{
    function MY_Image_lib()
    {
        parent::__construct();
    }

    /**
     * converts images
     *
     * @access public
     * @param string
     * @param bool
     * @return bool
     */
    function convert($type = 'jpg', $delete_orig = false)
    {
        $this->full_dst_path = $this->dest_folder . end($this->explode_name($this->dest_image)) . '.' . $type;
  
        if (!($src_img = $this->image_create_gd()))
        {
            return false;
        }

        if ($this->image_library == 'gd2' and function_exists('imagecreatetruecolor'))
        {
            $create = 'imagecreatetruecolor';
        } else
        {
            $create = 'imagecreate';
        }
        $copy = 'imagecopy';

        $props = $this->get_image_properties($this->full_src_path, true);
        $dst_img = $create($props['width'], $props['height']);
        $copy($dst_img, $src_img, 0, 0, 0, 0, $props['width'], $props['height']);

        $types = array('gif' => 1, 'jpg' => 2, 'jpeg' => 2, 'png' => 3);

        $this->image_type = $types[$type];

        if ($delete_orig)
        {
            // unlink($this->full_src_path);
            $this->full_src_path = $this->full_dst_path;
        }

        if ($this->dynamic_output == true)
        {
            $this->image_display_gd($dst_img);
        } else
        {
            if (!$this->image_save_gd($dst_img))
            {
                return false;
            }
        }

        imagedestroy($dst_img);
        imagedestroy($src_img);

        @chmod($this->full_dst_path, DIR_WRITE_MODE);

        return true;
    }
}

/* End of file MY_Image_lib.php */
/* Location: ./application/libraries/MY_Image_lib.php */
