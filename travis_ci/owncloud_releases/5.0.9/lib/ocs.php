<?php

/**
* ownCloud
*
* @author Frank Karlitschek
* @author Michael Gapczynski
* @copyright 2012 Frank Karlitschek frank@owncloud.org
* @copyright 2012 Michael Gapczynski mtgap@owncloud.com
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either
* version 3 of the License, or any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*
* You should have received a copy of the GNU Affero General Public
* License along with this library.  If not, see <http://www.gnu.org/licenses/>.
*
*/

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

/**
 * Class to handle open collaboration services API requests
 *
 */
class OC_OCS {

	/**
	* reads input date from get/post/cookies and converts the date to a special data-type
	*
	* @param string HTTP method to read the key from
	* @param string Parameter to read
	* @param string Variable type to format data
	* @param mixed Default value to return if the key is not found
	* @return mixed Data or if the key is not found and no default is set it will exit with a 400 Bad request
	*/
	public static function readData($method, $key, $type = 'raw', $default = null) {
		if ($method == 'get') {
			if (isset($_GET[$key])) {
				$data = $_GET[$key];
			} else if (isset($default)) {
				return $default;
			} else {
				$data = false;
			}
		} else if ($method == 'post') {
			if (isset($_POST[$key])) {
				$data = $_POST[$key];
			} else if (isset($default)) {
				return $default;
			} else {
				$data = false;
			}
		}
		if ($data === false) {
			echo self::generateXml('', 'fail', 400, 'Bad request. Please provide a valid '.$key);
			exit();
		} else {
			// NOTE: Is the raw type necessary? It might be a little risky without sanitization
			if ($type == 'raw') return $data;
			elseif ($type == 'text') return OC_Util::sanitizeHTML($data);
			elseif ($type == 'int')  return (int) $data;
			elseif ($type == 'float') return (float) $data;
			elseif ($type == 'array') return OC_Util::sanitizeHTML($data);
			else return OC_Util::sanitizeHTML($data);
		}
	}

	public static function notFound() {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$method='get';
		}elseif($_SERVER['REQUEST_METHOD'] == 'PUT') {
			$method='put';
			parse_str(file_get_contents("php://input"), $put_vars);
		}elseif($_SERVER['REQUEST_METHOD'] == 'POST') {
			$method='post';
		}else{
			echo('internal server error: method not supported');
			exit();
		}

		$format = self::readData($method, 'format', 'text', '');
		$txt='Invalid query, please check the syntax. API specifications are here:'
		.' http://www.freedesktop.org/wiki/Specifications/open-collaboration-services. DEBUG OUTPUT:'."\n";
		$txt.=OC_OCS::getDebugOutput();
		echo(OC_OCS::generateXml($format, 'failed', 999, $txt));

	}

	/**
	* generated some debug information to make it easier to find faild API calls
	* @return debug data string
	*/
	private static function getDebugOutput() {
		$txt='';
		$txt.="debug output:\n";
		if(isset($_SERVER['REQUEST_METHOD'])) $txt.='http request method: '.$_SERVER['REQUEST_METHOD']."\n";
		if(isset($_SERVER['REQUEST_URI'])) $txt.='http request uri: '.$_SERVER['REQUEST_URI']."\n";
		if(isset($_GET)) foreach($_GET as $key=>$value) $txt.='get parameter: '.$key.'->'.$value."\n";
		if(isset($_POST)) foreach($_POST as $key=>$value) $txt.='post parameter: '.$key.'->'.$value."\n";
		return($txt);
	}


	/**
	* generates the xml or json response for the API call from an multidimenional data array.
	* @param string $format
	* @param string $status
	* @param string $statuscode
	* @param string $message
	* @param array $data
	* @param string $tag
	* @param string $tagattribute
	* @param int $dimension
	* @param int $itemscount
	* @param int $itemsperpage
	* @return string xml/json
	*/
	private static function generateXml($format, $status, $statuscode,
		$message, $data=array(), $tag='', $tagattribute='', $dimension=-1, $itemscount='', $itemsperpage='') {
		if($format=='json') {
			$json=array();
			$json['status']=$status;
			$json['statuscode']=$statuscode;
			$json['message']=$message;
			$json['totalitems']=$itemscount;
			$json['itemsperpage']=$itemsperpage;
			$json['data']=$data;
			return(json_encode($json));
		}else{
			$txt='';
			$writer = xmlwriter_open_memory();
			xmlwriter_set_indent( $writer, 2 );
			xmlwriter_start_document($writer );
			xmlwriter_start_element($writer, 'ocs');
			xmlwriter_start_element($writer, 'meta');
			xmlwriter_write_element($writer, 'status', $status);
			xmlwriter_write_element($writer, 'statuscode', $statuscode);
			xmlwriter_write_element($writer, 'message', $message);
			if($itemscount<>'') xmlwriter_write_element($writer, 'totalitems', $itemscount);
			if(!empty($itemsperpage)) xmlwriter_write_element($writer, 'itemsperpage', $itemsperpage);
			xmlwriter_end_element($writer);
			if($dimension=='0') {
				// 0 dimensions
				xmlwriter_write_element($writer, 'data', $data);

			}elseif($dimension=='1') {
				xmlwriter_start_element($writer, 'data');
				foreach($data as $key=>$entry) {
					xmlwriter_write_element($writer, $key, $entry);
				}
				xmlwriter_end_element($writer);

			}elseif($dimension=='2') {
				xmlwriter_start_element($writer, 'data');
				foreach($data as $entry) {
					xmlwriter_start_element($writer, $tag);
					if(!empty($tagattribute)) {
						xmlwriter_write_attribute($writer, 'details', $tagattribute);
					}
					foreach($entry as $key=>$value) {
						if(is_array($value)) {
							foreach($value as $k=>$v) {
								xmlwriter_write_element($writer, $k, $v);
							}
						} else {
							xmlwriter_write_element($writer, $key, $value);
						}
					}
					xmlwriter_end_element($writer);
				}
				xmlwriter_end_element($writer);

			}elseif($dimension=='3') {
				xmlwriter_start_element($writer, 'data');
				foreach($data as $entrykey=>$entry) {
					xmlwriter_start_element($writer, $tag);
					if(!empty($tagattribute)) {
						xmlwriter_write_attribute($writer, 'details', $tagattribute);
					}
					foreach($entry as $key=>$value) {
						if(is_array($value)) {
							xmlwriter_start_element($writer, $entrykey);
							foreach($value as $k=>$v) {
								xmlwriter_write_element($writer, $k, $v);
							}
							xmlwriter_end_element($writer);
						} else {
							xmlwriter_write_element($writer, $key, $value);
						}
					}
					xmlwriter_end_element($writer);
				}
				xmlwriter_end_element($writer);
			}elseif($dimension=='dynamic') {
				xmlwriter_start_element($writer, 'data');
				OC_OCS::toxml($writer, $data, 'comment');
				xmlwriter_end_element($writer);
			}

			xmlwriter_end_element($writer);

			xmlwriter_end_document( $writer );
			$txt.=xmlwriter_output_memory( $writer );
			unset($writer);
			return($txt);
		}
	}

	public static function toXml($writer, $data, $node) {
		foreach($data as $key => $value) {
			if (is_numeric($key)) {
				$key = $node;
			}
			if (is_array($value)) {
				xmlwriter_start_element($writer, $key);
				OC_OCS::toxml($writer, $value, $node);
				xmlwriter_end_element($writer);
			}else{
				xmlwriter_write_element($writer, $key, $value);
			}
		}
	}

	/**
	* get private data
	* @param string $user
	* @param string $app
	* @param string $key
	* @param bool $like use LIKE instead of = when comparing keys
	* @return array
	*/
	public static function getData($user, $app="", $key="") {
		if($app) {
			$apps=array($app);
		}else{
			$apps=OC_Preferences::getApps($user);
		}
		if($key) {
			$keys=array($key);
		}else{
			foreach($apps as $app) {
				$keys=OC_Preferences::getKeys($user, $app);
			}
		}
		$result=array();
		foreach($apps as $app) {
			foreach($keys as $key) {
				$value=OC_Preferences::getValue($user, $app, $key);
				$result[]=array('app'=>$app, 'key'=>$key, 'value'=>$value);
			}
		}
		return $result;
	}

}
