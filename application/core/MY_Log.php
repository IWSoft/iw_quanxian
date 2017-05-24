<?php
/**
    用于将出错信息写入数据库
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Logging Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Logging
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/general/errors.html
 */
class MY_Log extends CI_Log {

	/**
	 * Path to save log files
	 *
	 * @var string
	 */
	protected $_log_path;

	/**
	 * File permissions
	 *
	 * @var	int
	 */
	protected $_file_permissions = 0644;

	/**
	 * Level of logging
	 *
	 * @var int
	 */
	protected $_threshold = 1;

	/**
	 * Array of threshold levels to log
	 *
	 * @var array
	 */
	protected $_threshold_array = array();

	/**
	 * Format of timestamp for log files
	 *
	 * @var string
	 */
	protected $_date_fmt = 'Y-m-d H:i:s';

	/**
	 * Filename extension
	 *
	 * @var	string
	 */
	protected $_file_ext;

	/**
	 * Whether or not the logger can write to the log files
	 *
	 * @var bool
	 */
	protected $_enabled = TRUE;

	/**
	 * Predefined logging levels
	 *
	 * @var array
	 */
	protected $_levels = array('ERROR' => 1, 'DEBUG' => 2, 'INFO' => 3, 'ALL' => 4);

	/**
	 * mbstring.func_override flag
	 *
	 * @var	bool
	 */
	protected static $func_override;

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
	    //parent::__construct();
		$config =& get_config();

		isset(self::$func_override) OR self::$func_override = (extension_loaded('mbstring') && ini_get('mbstring.func_override'));

		$this->_log_path = ($config['log_path'] !== '') ? $config['log_path'] : APPPATH.'logs/';
		$this->_file_ext = (isset($config['log_file_extension']) && $config['log_file_extension'] !== '')
			? ltrim($config['log_file_extension'], '.') : 'php';

		file_exists($this->_log_path) OR mkdir($this->_log_path, 0755, TRUE);

		if ( ! is_dir($this->_log_path) OR ! is_really_writable($this->_log_path))
		{
			$this->_enabled = FALSE;
		}

		if (is_numeric($config['log_threshold']))
		{
			$this->_threshold = (int) $config['log_threshold'];
		}
		elseif (is_array($config['log_threshold']))
		{
			$this->_threshold = 0;
			$this->_threshold_array = array_flip($config['log_threshold']);
		}

		if ( ! empty($config['log_date_format']))
		{
			$this->_date_fmt = $config['log_date_format'];
		}

		if ( ! empty($config['log_file_permissions']) && is_int($config['log_file_permissions']))
		{
			$this->_file_permissions = $config['log_file_permissions'];
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Write Log File
	 *
	 * Generally this function will be called using the global log_message() function
	 *
	 * @param	string	$level 	The error level: 'error', 'debug' or 'info'
	 * @param	string	$msg 	The error message
	 * @return	bool
	 */
	public function write_log($level, $msg)
	{

        $config =& get_config();
        //$ci =& get_instance(); //初始化 为了用方法
        //$ci->load->helper('common_function');
		if ($this->_enabled === FALSE)
		{
			return FALSE;
		}

		$level = strtoupper($level);

		if (( ! isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold))
			&& ! isset($this->_threshold_array[$this->_levels[$level]]))
		{
            //file_put_contents("./aa.txt","bbb=".$level."\n".print_r($this->_levels,true));
			return FALSE;
		}

		//改为，不写入文件，写入数据库
        //die("fuck=".$config["my_save_err_to_database"]);
        if($config["my_save_err_to_database"]){

            /**
             * 如慢，可屏蔽这段
             */
              if(function_exists("curl_init")){
                  $url = $this->get_url()."config/log.php";

                  $post_data = array ("errno"=>"-2","errstr" => ( $level.":".$msg));//-2 来自MYLOG
                  $ch = curl_init();
                  curl_setopt($ch, CURLOPT_URL, $url);
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                  //curl_setopt($ch, CURLOPT_TIMEOUT,10);//超过10秒自动中止，可以减轻压力
                  curl_setopt($ch, CURLOPT_HEADER, 0);
                  // post数据
                  curl_setopt($ch, CURLOPT_POST, 1);
                  // post的变量
                  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                  $output = curl_exec($ch) or curl_error($ch);
                  //echo "out=".$output;
                  //file_put_contents("./aa.txt","aaa=".$output);
                  curl_close($ch);
              }

            //print_r($_SERVER);


                require_once(dirname(__FILE__)."../../../config/config.inc.php");
                if(isset($db)) {
                    $port = $db['default']["hostname"];
                    $port = explode(":", $port);
                    if (count($port) > 1) {
                        $port = $port[1];
                    } else {
                        $port = "3306";
                    }

                    $conn = mysqli_connect(
                        $db['default']["hostname"],
                        $db['default']["username"],
                        $db['default']["password"],
                        $db['default']["database"],
                        $port
                    );
                    $post["errno"] = "-2";
                    $post["errstr"] = $level . ":" . str_replace("\\", "/", $msg);
                    $post["errstr"] = str_replace("'", "\'", $post["errstr"]);
                    $post["errfile"] = "";
                    $post["errline"] = "";
                    $post["errcontext"] = "";
                    $post["beizhu"] = "";

                    $sql = "insert into aiw_err_log(guid,errno,errstr,errfile,errline,errcontext,beizhu,createdate
                            ,sys_user_guid,sys_user_username
                            ) values('" . $this->create_guid() . "',
                            '" . (isset($post["errno"]) ? $post["errno"] : "") . "',
                            '" . (isset($post["errstr"]) ? $post["errstr"] : "") . "',
                            '" . (isset($post["errfile"]) ? $post["errfile"] : "") . "',
                            '" . (isset($post["errline"]) ? $post["errline"] : "") . "',
                            '" . (isset($post["errcontext"]) ? $post["errcontext"] : "") . "',
                            '" . (isset($post["beizhu"]) ? $post["beizhu"] : "") . "',
                            " . time() . ",'','')";
                    //echo $sql;
                    mysqli_query($conn, $sql);
                    mysqli_close($conn);
                }



        }
        else {

            $filepath = $this->_log_path . 'log-' . date('Y-m-d') . '.' . $this->_file_ext;
            $message = '';

            if (!file_exists($filepath)) {
                $newfile = TRUE;
                // Only add protection to php files
                if ($this->_file_ext === 'php') {
                    $message .= "<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>\n\n";
                }
            }

            if (!$fp = @fopen($filepath, 'ab')) {
                return FALSE;
            }

            flock($fp, LOCK_EX);

            // Instantiating DateTime with microseconds appended to initial date is needed for proper support of this format
            if (strpos($this->_date_fmt, 'u') !== FALSE) {
                $microtime_full = microtime(TRUE);
                $microtime_short = sprintf("%06d", ($microtime_full - floor($microtime_full)) * 1000000);
                $date = new DateTime(date('Y-m-d H:i:s.' . $microtime_short, $microtime_full));
                $date = $date->format($this->_date_fmt);
            } else {
                $date = date($this->_date_fmt);
            }

            $message .=  $this->_format_line($level, $date, $msg);

            for ($written = 0, $length = self::strlen($message); $written < $length; $written += $result) {
                if (($result = fwrite($fp, self::substr($message, $written))) === FALSE) {
                    break;
                }
            }

            flock($fp, LOCK_UN);
            fclose($fp);

            if (isset($newfile) && $newfile === TRUE) {
                chmod($filepath, $this->_file_permissions);
            }

            return is_int($result);
        }


	}

	private function get_url() {
        $url = ($_SERVER["SERVER_PORT"]=="443"?"https://":"http://").$_SERVER["HTTP_HOST"];
        $config =& get_config();
        if($config["base_url"]!=""){
            $url .= $config["base_url"];
        }
        if(substr($url,strlen($url)-1,1)!="/"){
            $url.="/";
        }
        //$url.= $config["index_page"]."/";
        return $url;
    }

	// --------------------------------------------------------------------

	/**
	 * Format the log line.
	 *
	 * This is for extensibility of log formatting
	 * If you want to change the log format, extend the CI_Log class and override this method
	 *
	 * @param	string	$level 	The error level
	 * @param	string	$date 	Formatted date string
	 * @param	string	$message 	The log message
	 * @return	string	Formatted log line with a new line character '\n' at the end
	 */
	protected function _format_line($level, $date, $message)
	{
		return $level.' - '.$date.' --> '.$message."\n";
	}

	// --------------------------------------------------------------------

	/**
	 * Byte-safe strlen()
	 *
	 * @param	string	$str
	 * @return	int
	 */
	protected static function strlen($str)
	{
		return (self::$func_override)
			? mb_strlen($str, '8bit')
			: strlen($str);
	}

	// --------------------------------------------------------------------

	/**
	 * Byte-safe substr()
	 *
	 * @param	string	$str
	 * @param	int	$start
	 * @param	int	$length
	 * @return	string
	 */
	protected static function substr($str, $start, $length = NULL)
	{
		if (self::$func_override)
		{
			// mb_substr($str, $start, null, '8bit') returns an empty
			// string on PHP 5.3
			isset($length) OR $length = ($start >= 0 ? self::strlen($str) - $start : -$start);
			return mb_substr($str, $start, $length, '8bit');
		}

		return isset($length)
			? substr($str, $start, $length)
			: substr($str, $start);
	}


    private function create_guid() {
        $charid = strtolower(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);// "-"
        //$uuid = chr(123)// "{"
        $uuid=
            substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        //.chr(125);// "}"
        return $uuid;
    }
}
