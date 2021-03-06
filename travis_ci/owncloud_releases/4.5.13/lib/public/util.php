<?php
/**
* ownCloud
*
* @author Frank Karlitschek
* @copyright 2012 Frank Karlitschek frank@owncloud.org
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

/**
 * Public interface of ownCloud for apps to use.
 * Utility Class.
 *
 */

// use OCP namespace for all classes that are considered public.
// This means that they should be used by apps instead of the internal ownCloud classes
namespace OCP;

/**
 * This class provides different helper functions to make the life of a developer easier
 */
class Util {
	// consts for Logging
	const DEBUG=0;
	const INFO=1;
	const WARN=2;
	const ERROR=3;
	const FATAL=4;

	/**
	 * @brief get the current installed version of ownCloud
	 * @return array
	 */
	public static function getVersion() {
		return(\OC_Util::getVersion());
	}

	/**
	 * @brief send an email
	 * @param string $toaddress
	 * @param string $toname
	 * @param string $subject
	 * @param string $mailtext
	 * @param string $fromaddress
	 * @param string $fromname
	 * @param bool $html
	 */
	public static function sendMail( $toaddress, $toname, $subject, $mailtext, $fromaddress, $fromname, $html=0, $altbody='', $ccaddress='', $ccname='', $bcc='') {
		// call the internal mail class
		\OC_MAIL::send( $toaddress, $toname, $subject, $mailtext, $fromaddress, $fromname, $html=0, $altbody='', $ccaddress='', $ccname='', $bcc='');
	}

	/**
	 * @brief write a message in the log
	 * @param string $app
	 * @param string $message
	 * @param int level
	 */
	public static function writeLog( $app, $message, $level ) {
		// call the internal log class
		\OC_LOG::write( $app, $message, $level );
	}

	/**
	 * @brief add a css file
	 * @param url  $url
	 */
	public static function addStyle( $application, $file = null ) {
		\OC_Util::addStyle( $application, $file );
	}

	/**
	 * @brief add a javascript file
	 * @param appid  $application
	 * @param filename  $file
	 */
	public static function addScript( $application, $file = null ) {
		\OC_Util::addScript( $application, $file );
	}

	/**
	 * @brief Add a custom element to the header
	 * @param string tag tag name of the element
	 * @param array $attributes array of attributes for the element
	 * @param string $text the text content for the element
	 */
	public static function addHeader( $tag, $attributes, $text='') {
		\OC_Util::addHeader( $tag, $attributes, $text );
	}

	/**
	 * @brief formats a timestamp in the "right" way
	 * @param int timestamp $timestamp
	 * @param bool dateOnly option to ommit time from the result
	 */
	public static function formatDate( $timestamp,$dateOnly=false) {
		return(\OC_Util::formatDate( $timestamp,$dateOnly ));
	}

	/**
	 * @brief Creates an absolute url
	 * @param $app app
	 * @param $file file
	 * @param $args array with param=>value, will be appended to the returned url
	 * 	The value of $args will be urlencoded
	 * @returns the url
	 *
	 * Returns a absolute url to the given app and file.
	 */
	public static function linkToAbsolute( $app, $file, $args = array() ) {
		return(\OC_Helper::linkToAbsolute( $app, $file, $args ));
	}

	/**
	 * @brief Creates an absolute url for remote use
	 * @param $service id
	 * @returns the url
	 *
	 * Returns a absolute url to the given app and file.
	 */
	public static function linkToRemote( $service ) {
		return(\OC_Helper::linkToRemote( $service ));
	}

	/**
	 * @brief Creates an absolute url for public use
	 * @param $service id
	 * @returns the url
	 *
	 * Returns a absolute url to the given app and file.
	 */
	public static function linkToPublic($service) {
		return \OC_Helper::linkToPublic($service);
	}

	/**
	* @brief Creates an url
	* @param $app app
	* @param $file file
	* @param $args array with param=>value, will be appended to the returned url
	* 	The value of $args will be urlencoded
	* @returns the url
	*
	* Returns a url to the given app and file.
	*/
	public static function linkTo( $app, $file, $args = array() ) {
		return(\OC_Helper::linkTo( $app, $file, $args ));
	}

	/**
	 * @brief Returns the server host
	 * @returns the server host
	 *
	 * Returns the server host, even if the website uses one or more
	 * reverse proxies
	 */
	public static function getServerHost() {
		return(\OC_Request::serverHost());
	}

	/**
	 * @brief returns the server hostname
	 * @returns the server hostname
	 *
	 * Returns the server host name without an eventual port number
	 */
	public static function getServerHostName() {
		$host_name = self::getServerHost();
		// strip away port number (if existing)
		$colon_pos = strpos($host_name, ':');
		if ($colon_pos != FALSE) {
			$host_name = substr($host_name, 0, $colon_pos);
		}
		return $host_name;
	}

	/**
	 * @brief Returns the default email address
	 * @param $user_part the user part of the address
	 * @returns the default email address 
	 *
	 * Assembles a default email address (using the server hostname
	 * and the given user part, and returns it
	 * Example: when given lostpassword-noreply as $user_part param,
	 *     and is currently accessed via http(s)://example.com/,
	 *     it would return 'lostpassword-noreply@example.com'
	 */
	public static function getDefaultEmailAddress($user_part) {
		$host_name = self::getServerHostName();
		// handle localhost installations
		if ($host_name === 'localhost') {
			$host_name = "example.com";
		}
		return $user_part.'@'.$host_name;
	}

	/**
	 * @brief Returns the server protocol
	 * @returns the server protocol
	 *
	 * Returns the server protocol. It respects reverse proxy servers and load balancers
	 */
	public static function getServerProtocol() {
		return(\OC_Request::serverProtocol());
	}

	/**
	 * @brief Creates path to an image
	 * @param $app app
	 * @param $image image name
	 * @returns the url
	 *
	 * Returns the path to the image.
	 */
	public static function imagePath( $app, $image ) {
		return(\OC_Helper::imagePath( $app, $image ));
	}

	/**
	 * @brief Make a human file size
	 * @param $bytes file size in bytes
	 * @returns a human readable file size
	 *
	 * Makes 2048 to 2 kB.
	 */
	public static function humanFileSize( $bytes ) {
		return(\OC_Helper::humanFileSize( $bytes ));
	}

	/**
	 * @brief Make a computer file size
	 * @param $str file size in a fancy format
	 * @returns a file size in bytes
	 *
	 * Makes 2kB to 2048.
	 *
	 * Inspired by: http://www.php.net/manual/en/function.filesize.php#92418
	 */
	public static function computerFileSize( $str ) {
		return(\OC_Helper::computerFileSize( $str ));
	}

	/**
	 * @brief connects a function to a hook
	 * @param $signalclass class name of emitter
	 * @param $signalname name of signal
	 * @param $slotclass class name of slot
	 * @param $slotname name of slot
	 * @returns true/false
	 *
	 * This function makes it very easy to connect to use hooks.
	 *
	 * TODO: write example
	 */
	static public function connectHook( $signalclass, $signalname, $slotclass, $slotname ) {
		return(\OC_Hook::connect( $signalclass, $signalname, $slotclass, $slotname ));
	}

	/**
	 * @brief emitts a signal
	 * @param $signalclass class name of emitter
	 * @param $signalname name of signal
	 * @param $params defautl: array() array with additional data
	 * @returns true if slots exists or false if not
	 *
	 * Emits a signal. To get data from the slot use references!
	 *
	 * TODO: write example
	 */
	static public function emitHook( $signalclass, $signalname, $params = array()) {
		return(\OC_Hook::emit( $signalclass, $signalname, $params ));
	}

	/**
	 * Register an get/post call. This is important to prevent CSRF attacks
	 * TODO: write example
	 */
	public static function callRegister() {
		return(\OC_Util::callRegister());
	}

	/**
	 * Check an ajax get/post call if the request token is valid. exit if not.
	 * Todo: Write howto
	 */
	public static function callCheck() {
		return(\OC_Util::callCheck());
	}

	/**
	 * @brief Used to sanitize HTML
	 *
	 * This function is used to sanitize HTML and should be applied on any string or array of strings before displaying it on a web page.
	 *
	 * @param string or array of strings
	 * @return array with sanitized strings or a single sinitized string, depends on the input parameter.
	 */
	public static function sanitizeHTML( $value ) {
		return(\OC_Util::sanitizeHTML($value));
	}

	/**
	* @brief Returns an array with all keys from input lowercased or uppercased. Numbered indices are left as is.
	*
	* @param $input The array to work on
	* @param $case Either MB_CASE_UPPER or MB_CASE_LOWER (default)
	* @param $encoding The encoding parameter is the character encoding. Defaults to UTF-8
	* @return array
	*
	*
	*/
	public static function mb_array_change_key_case($input, $case = MB_CASE_LOWER, $encoding = 'UTF-8') {
		return(\OC_Helper::mb_array_change_key_case($input, $case, $encoding));
	}

	/**
	* @brief replaces a copy of string delimited by the start and (optionally) length parameters with the string given in replacement.
	*
	* @param $input The input string. .Opposite to the PHP build-in function does not accept an array.
	* @param $replacement The replacement string.
	* @param $start If start is positive, the replacing will begin at the start'th offset into string. If start is negative, the replacing will begin at the start'th character from the end of string.
	* @param $length Length of the part to be replaced
	* @param $encoding The encoding parameter is the character encoding. Defaults to UTF-8
	* @return string
	*
	*/
	public static function mb_substr_replace($string, $replacement, $start, $length = null, $encoding = 'UTF-8') {
		return(\OC_Helper::mb_substr_replace($string, $replacement, $start, $length, $encoding));
	}

	/**
	* @brief Replace all occurrences of the search string with the replacement string
	*
	* @param $search The value being searched for, otherwise known as the needle. String.
	* @param $replace The replacement string.
	* @param $subject The string or array being searched and replaced on, otherwise known as the haystack.
	* @param $encoding The encoding parameter is the character encoding. Defaults to UTF-8
	* @param $count If passed, this will be set to the number of replacements performed.
	* @return string
	*
	*/
	public static function mb_str_replace($search, $replace, $subject, $encoding = 'UTF-8', &$count = null) {
		return(\OC_Helper::mb_str_replace($search, $replace, $subject, $encoding, $count));
	}

	/**
	* @brief performs a search in a nested array
	* @param haystack the array to be searched
	* @param needle the search string
	* @param $index optional, only search this key name
	* @return the key of the matching field, otherwise false
	*/
	public static function recursiveArraySearch($haystack, $needle, $index = null) {
		return(\OC_Helper::recursiveArraySearch($haystack, $needle, $index));
	}
}
