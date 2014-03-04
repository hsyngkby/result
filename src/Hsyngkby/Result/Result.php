<?php namespace Hsyngkby\Result;

use Illuminate\Support\MessageBag;
use Illuminate\Session\SessionManager;
use Illuminate\Config\Repository as Config;
//use Illuminate\Http\JsonResponse;
/**
 * @author Hüseyin GÖKBAY <huseyingokbay@gmail.com>
 */
class Result {

	/**
	 * Key that is used to store the flash data
	 */
	const SESSION_KEY = 'resultSession';

	/**
	 * Alert bag
	 * @var Illuminate\Support\MessageBag
	 */
	protected $bag;

	/**
	 * Session object
	 * @var Illuminate\Session\SessionManager
	 */
	protected $session;

	/**
	 * Config object
	 * @var Illuminate\Config\Repository
	 */
	protected $config;

	/**
	 * Initialize Result
	 */
	public function __construct(SessionManager $session, Config $config)
	{
		$this->session = $session;
		$this->config = $config;

		// Get all notifications from the flash input
		$this->getExisting();
	}

	/**
	 * Get all alert from session flash
	 * @return void
	 */
	public function getExisting()
	{

		// Create message bag
		if ( ! $this->bag) $this->bag = new MessageBag();

		// Get messges from flash
		$flash  = $this->session->get(self::SESSION_KEY);

		// Add Laravel errors
		if ($errors = $this->session->get('errors'))
		{
			foreach ($errors->all() as $error)
			{
				$this->bag->add('error', $error);
			}
		}

		if ($flash)
		{
			foreach ($flash as $type => $alerts)
			{
				if (is_array($alerts) and count($alerts))
				{
					foreach ($alerts as $alert)
					{
						$this->bag->add($type, $alert);
					}
				}
			}
		}

		foreach (array('success', 'error', 'warning') as $key)
		{
			if ($message = $this->session->get($key))
			{
				// Get type
				$type = $key;
				if ( ! $type) $type = 'info';

				// Add the message
				$this->bag->add($type, $message);

				// And remove from flash
				$this->session->forget($key);
			}
		}
	}
	/**
	 * Return all collections
	 * @return array
	 */
	public function bag()
	{
		return $this->bag;
	}

	/**
	 * Write all alerts to session flash
	 */
	public function setFlash()
	{
		$flash = array();

		foreach ($this->bag->getMessages() as $type => $messages)
		{
			foreach ($messages as $message)
			{
				$flash[$type][] = $message;
			}
		}

		$this->session->flash(self::SESSION_KEY, $flash);
	}

	/**
	 * Add new alert to specific collection
	 * @param string $message
	 * @param string $type
	 */
	private function add($process='',$message='',$type='info',$opt=[],$flash=false)
	{
		$message = [
			'process' => $process,
			'message' => $message,
			'opt' => $opt,
			'time' => time(),
		];

		if ($this->config->get('app.debug')){

			//$e = new \Exception();
			//$e = debug_backtrace(false);
			$trace = debug_backtrace(false);
		    $i=1;
		    foreach ($trace as $key => $value) {
		    	if ($value['function'] == '__callStatic') break;
		    	if ($value['function'] == 'call_user_func_array') break;
		    	if ($value['function'] == '{closure}') break;
		    	$i++;
		    }

			$in = $trace[$i+1];
			$infile = isset($in['file']) ? $in['file'] : '';
			$inline = isset($in['line']) ? $in['line'] : '';
			$inclass = isset($in['class']) ? $in['class'] : '';
			$intype = isset($in['type']) ? $in['type'] : '';
			$infunction = isset($in['function']) ? $in['function'] : '';
			$inargs = isset($in['args']) ? $in['args'] : '';
			$in = $infile.' '.$inline.' '.$inclass.' '.$intype.' '.$infunction.' ( '.json_encode($inargs).' )';

			$out = $trace[$i];
			$outfile = isset($out['file']) ? $out['file'] : '';
			$outline = isset($out['line']) ? $out['line'] : '';
			$outclass = isset($out['class']) ? $out['class'] : '';
			$outtype = isset($out['type']) ? $out['type'] : '';
			$outfunction = isset($out['function']) ? $out['function'] : '';
			$outargs = isset($out['args']) ? $out['args'] : '';
			$out = $outfile.' '.$outline.' '.$outclass.' '.$outtype.' '.$outfunction.' ( '.json_encode($outargs).' )';

			$message['trace']['in'] = $in;//['file'].':'.$last_call['line'];
			$message['trace']['out'] = $out;//['file'].':'.$last_call['line'];
		}
		$this->bag->add($type, $message);

		// And write to flash
		if ($flash) $this->setFlash();
	}

	/**
	 * Add new success alert
	 * @param string $message
	 */
	public function success($process='',$message='',$opt=[],$flash=false)
	{
		return $this->add($process,$message,'success',$opt,$flash);
	}
	/**
	 * Add new success alert
	 * @param string $message
	 */
	public function error($process='',$message='',$opt=[],$flash=false)
	{
		return $this->add($process,$message,'error',$opt,$flash);
	}
	/**
	 * Add new success alert
	 * @param string $message
	 */
	public function warning($process='',$message='',$opt=[],$flash=false)
	{
		return $this->add($process,$message,'warning',$opt,$flash);
	}
	/**
	 * Add new success alert
	 * @param string $message
	 */
	public function info($process='',$message='',$opt=[],$flash=false)
	{
		return $this->add($process,$message,'info',$opt,$flash);
	}


	/**
	 * Show specific alert type
	 * @param  string $type
	 * @return string
	 */
	public function render()
	{
		if ($this->bag->any())
		{
			$output = array();

			foreach ($this->bag->getMessages() as $type => $messages)
			{

				foreach ($messages as $message)
				{
					// Prepare output
					$output[] = array('type'=>$type,'data'=>$message);
				}

			}
			//$json = new JsonResponse;
			//return json_encode($output);
			return $output;
		}
	}

	public function toJson()
	{
		return json_encode( $this->render() );
	}

	public function toArray()
	{
		return $this->render();
	}
}