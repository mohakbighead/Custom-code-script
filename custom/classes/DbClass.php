<?php
require("LogClass.php");
class DbClass
{
	# @object, The PDO object
	public $pdo;

	# @object, PDO statement object
	private $sQuery;

	# @array,  The database settings
	private $settings;

	# @bool ,  Connected to the database
	private $bConnected = false;

	# @object, Object for logging exceptions	
	private $log;

	# @array, The parameters of the SQL query
	private $parameters;
	
	# @object, The DB class object
	private static $db_instance;
	
	
	
	/**
     * Create DbClass object
     *
     * @return object $db_instance - DB Object
     */
    public static function getInstance()
	{
        // If instance exists then return it, else create new instance and return it
        if (!self::$db_instance)
        {
            self::$db_instance = new DbClass();
        }
		$_SESSION['lastlogin'] = time();
        return self::$db_instance;
    }
		
    /**
	*   Default Constructor 
	*
	*	1. Instantiate Log class.
	*	2. Connect to database.
	*	3. Creates the parameter array.
	*/
		public function __construct()
		{ 			
			$this->log = new Log();	
			$this->Connect();
			$this->parameters = array();
		}
	
       /**
	*	This method makes connection to the database.
	*	
	*	1. Reads the database settings from a ini file. 
	*	2. Puts  the ini content into the settings array.
	*	3. Tries to connect to the database.
	*	4. If connection failed, exception is displayed and a log file gets created.
	*/
		private function Connect()
		{
			$this->settings = parse_ini_file("settings.ini.php");
			$dsn = 'mysql:dbname='.$this->settings["dbname"].';host='.$this->settings["host"].'';
			try 
			{
				# Read settings from INI file, set UTF8
				$this->pdo = new PDO($dsn, $this->settings["user"], $this->settings["password"], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				
				# We can now log any exceptions on Fatal error. 
				$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
				# Disable emulation of prepared statements, use REAL prepared statements instead.
				$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				
				# Connection succeeded, set the boolean to true.
				$this->bConnected = true;
			}
			catch (PDOException $e) 
			{
				# Write into log
				echo $this->ExceptionLog($e->getMessage());
				die();
			}
		}
	/*
	 *   You can use this little method if you want to close the PDO connection
	 *
	 */
	 	public function CloseConnection()
	 	{
	 		# Set the PDO object to null to close the connection
	 		# http://www.php.net/manual/en/pdo.connections.php
	 		$this->pdo = null;
	 	}
		
    /**
	*	Every method which needs to execute a SQL query uses this method.
	*	
	*	1. If not connected, connect to the database.
	*	2. Prepare Query.
	*	3. Parameterize Query.
	*	4. Execute Query.	
	*	5. On exception : Write Exception into the log + SQL query.
	*	6. Reset the Parameters.
	*/	
		private function Init($query,$parameters = "")
		{
		# Connect to database
		if(!$this->bConnected) { $this->Connect(); }
		try {
				# Prepare query
				$this->sQuery = $this->pdo->prepare($query);
				
				# Add parameters to the parameter array	
				$this->bindMore($parameters);

				# Bind parameters
				if(!empty($this->parameters)) {
					foreach($this->parameters as $param)
					{
						$parameters = explode("\x7F",$param);
						$this->sQuery->bindParam($parameters[0],$parameters[1]);
					}		
				}
				
				# Execute SQL 
				$this->succes 	= $this->sQuery->execute();		
			}
			catch(PDOException $e)
			{
					# Write into log and display Exception
					echo $this->ExceptionLog($e->getMessage(), $query );
					die();
			}

			# Reset the parameters
			$this->parameters = array();
		}
		
    /**
	*	@void 
	*
	*	Add the parameter to the parameter array
	*	@param string $para  
	*	@param string $value 
	*/	
		public function bind($para, $value)
		{	
			$this->parameters[sizeof($this->parameters)] = ":" . $para . "\x7F" . $value;
		}
    /**
	*	@void
	*	
	*	Add more parameters to the parameter array
	*	@param array $parray
	*/	
		public function bindMore($parray)
		{
			if(empty($this->parameters) && is_array($parray)) {
				$columns = array_keys($parray);
				foreach($columns as $i => &$column)	{
					$this->bind($column, $parray[$column]);
				}
			}
		}
    /**
	* 	If the SQL query  contains a SELECT or SHOW statement it returns an array containing all of the result set row
	*	If the SQL statement is a DELETE, INSERT, or UPDATE statement it returns the number of affected rows
	*
	*   @param  string $query
	*	@param  array  $params
	*	@param  int    $fetchmode
	*	@return mixed
	*/			
		public function query($query,$params = null, $fetchmode = PDO::FETCH_ASSOC)
		{
			$query = trim($query);
			
			/*View and debug prepared PDO query without looking at MySQL logs*/
			//echo $this -> interpolateQuery($query,$params);

			$this->Init($query,$params);

			$rawStatement = explode(" ", $query);
			
			# Which SQL statement is used 
			$statement = strtolower($rawStatement[0]);
			
			if ($statement === 'select' || $statement === 'show') {				
				return $this->sQuery->fetchAll($fetchmode);
			}
			elseif ( $statement === 'insert' ||  $statement === 'update' || $statement === 'delete' ) {
				return $this->sQuery->rowCount();	
			}	
			else {
				return NULL;
			}
		}
		
      /**
       *  Returns the last inserted id.
       *  @return string
       */	
		public function lastInsertId() {
			return $this->pdo->lastInsertId();
		}	
		
		public function Quote($str)
		{
			return $this->pdo->quote($str);
		}
		
    /**
	*	Returns an array which represents a column from the result set 
	*
	*	@param  string $query
	*	@param  array  $params
	*	@return array
	*/	
		public function column($query,$params = null)
		{
			$this->Init($query,$params);
			$Columns = $this->sQuery->fetchAll(PDO::FETCH_NUM);		
			
			$column = null;

			foreach($Columns as $cells) {
				$column[] = $cells[0];
			}

			return $column;
			
		}	
    /**
	*	Returns an array which represents a row from the result set 
	*
	*	@param  string $query
	*	@param  array  $params
	*   @param  int    $fetchmode
	*	@return array
	*/	
		public function row($query,$params = null,$fetchmode = PDO::FETCH_ASSOC)
		{	
			$this->Init($query,$params);
			return $this->sQuery->fetch($fetchmode);			
		}
    /**
	*	Returns the value of one single field/column
	*
	*	@param  string $query
	*	@param  array  $params
	*	@return string
	*/	
		public function single($query,$params = null)
		{
			$this->Init($query,$params);
			return $this->sQuery->fetchColumn();
		}
       /**	
	* Writes the log and returns the exception
	*
	* @param  string $message
	* @param  string $sql
	* @return string
	*/
	private function ExceptionLog($message , $sql = "")
	{
		$exception  = 'Unhandled Exception. <br />';
		$exception .= $message;
		$exception .= "<br /> You can find the error back in the log.";

		if(!empty($sql)) {
			# Add the Raw SQL to the Log
			$message .= "\r\nRaw SQL : "  . $sql;
		}
			# Write into log
			$this->log->write($message);

		return $exception;
	}			
	
	 /**
	 * Function for prevent the injection through the url or form submition 
	 */
	public function cleanData($char)
	{
		$track = array("<script", "</script>", "<html>", "</html>","src=");
		$message = str_replace($track, "", $char);
		//$message = addslashes($message);
		$message = htmlentities($message, ENT_QUOTES);
		$message = $this -> ent2ncr($message);
		//echo $message;
		return trim($message);
	}
	
	 /**
     * Clean data array for SQL
     *
     * @param string $value_array - array os strings to clean
     * @return string $value_array - cleaned string array
     */
    public function cleanDataArray($value_array){
        foreach($value_array as $id => $value){
            $value_array[$id] = $this -> cleanData($value);
        }
        return $value_array;
    }

	
	/**
	* Fetch data from setting table 
	* 
	* Define variables
	*/
	public function getsettingsData()
	{
		$results =  $this->query("SELECT * FROM settings");			
		if(!defined('SITEURL')){define("SITEURL",stripslashes($results[0]['site_url']));}
		if(!defined('ADMINMAIL')){define("ADMINMAIL",stripslashes($results[0]['admin_email']));}
		if(!defined('RECORDPERPAGE')){define("RECORDPERPAGE",stripslashes($results[0]['results']));}
	}
	
	/**
     * Generic DB method to count total rows
     *
     * @param string $query - SQL query
     */
    public function countRows($query,$params = null,$fetchmode = PDO::FETCH_ASSOC){
       	$this->Init($query,$params);
		return $this->sQuery->rowCount();
    }
	
	/**
	* @param string $query The sql query with parameter placeholders
	* @param array $params The array of substitution parameters
	* @return string The interpolated query
	*/
	public static function interpolateQuery($query, $params) {
		$keys = array();
		if($params != null)
		{
			# build a regular expression for each parameter
			foreach ($params as $key => $value) {
				if (is_string($key)) {
					$keys[] = '/:'.$key.'/';
				} else {
					$keys[] = '/[?]/';
				}
			}
		}
		$query = preg_replace($keys, $params, $query, 1, $count);
	
		#trigger_error('replaced '.$count.' keys');
	
		return $query;
	}
	
	// HTML CONVERTION
	private function ent2ncr($text) {
		
		$to_ncr = array(
			'&quot;' => '&#34;',
			'&amp;' => '&#38;',
			'&lt;' => '&#60;',
			'&gt;' => '&#62;',
			'|' => '&#124;',
			'&nbsp;' => '&#160;',
			'&iexcl;' => '&#161;',
			'&cent;' => '&#162;',
			'&pound;' => '&#163;',
			'&curren;' => '&#164;',
			'&yen;' => '&#165;',
			'&brvbar;' => '&#166;',
			'&brkbar;' => '&#166;',
			'&sect;' => '&#167;',
			'&uml;' => '&#168;',
			'&die;' => '&#168;',
			'&copy;' => '&#169;',
			'&ordf;' => '&#170;',
			'&laquo;' => '&#171;',
			'&not;' => '&#172;',
			'&shy;' => '&#173;',
			'&reg;' => '&#174;',
			'&macr;' => '&#175;',
			'&hibar;' => '&#175;',
			'&deg;' => '&#176;',
			'&plusmn;' => '&#177;',
			'&sup2;' => '&#178;',
			'&sup3;' => '&#179;',
			'&acute;' => '&#180;',
			'&micro;' => '&#181;',
			'&para;' => '&#182;',
			'&middot;' => '&#183;',
			'&cedil;' => '&#184;',
			'&sup1;' => '&#185;',
			'&ordm;' => '&#186;',
			'&raquo;' => '&#187;',
			'&frac14;' => '&#188;',
			'&frac12;' => '&#189;',
			'&frac34;' => '&#190;',
			'&iquest;' => '&#191;',
			'&Agrave;' => '&#192;',
			'&Aacute;' => '&#193;',
			'&Acirc;' => '&#194;',
			'&Atilde;' => '&#195;',
			'&Auml;' => '&#196;',
			'&Aring;' => '&#197;',
			'&AElig;' => '&#198;',
			'&Ccedil;' => '&#199;',
			'&Egrave;' => '&#200;',
			'&Eacute;' => '&#201;',
			'&Ecirc;' => '&#202;',
			'&Euml;' => '&#203;',
			'&Igrave;' => '&#204;',
			'&Iacute;' => '&#205;',
			'&Icirc;' => '&#206;',
			'&Iuml;' => '&#207;',
			'&ETH;' => '&#208;',
			'&Ntilde;' => '&#209;',
			'&Ograve;' => '&#210;',
			'&Oacute;' => '&#211;',
			'&Ocirc;' => '&#212;',
			'&Otilde;' => '&#213;',
			'&Ouml;' => '&#214;',
			'&times;' => '&#215;',
			'&Oslash;' => '&#216;',
			'&Ugrave;' => '&#217;',
			'&Uacute;' => '&#218;',
			'&Ucirc;' => '&#219;',
			'&Uuml;' => '&#220;',
			'&Yacute;' => '&#221;',
			'&THORN;' => '&#222;',
			'&szlig;' => '&#223;',
			'&agrave;' => '&#224;',
			'&aacute;' => '&#225;',
			'&acirc;' => '&#226;',
			'&atilde;' => '&#227;',
			'&auml;' => '&#228;',
			'&aring;' => '&#229;',
			'&aelig;' => '&#230;',
			'&ccedil;' => '&#231;',
			'&egrave;' => '&#232;',
			'&eacute;' => '&#233;',
			'&ecirc;' => '&#234;',
			'&euml;' => '&#235;',
			'&igrave;' => '&#236;',
			'&iacute;' => '&#237;',
			'&icirc;' => '&#238;',
			'&iuml;' => '&#239;',
			'&eth;' => '&#240;',
			'&ntilde;' => '&#241;',
			'&ograve;' => '&#242;',
			'&oacute;' => '&#243;',
			'&ocirc;' => '&#244;',
			'&otilde;' => '&#245;',
			'&ouml;' => '&#246;',
			'&divide;' => '&#247;',
			'&oslash;' => '&#248;',
			'&ugrave;' => '&#249;',
			'&uacute;' => '&#250;',
			'&ucirc;' => '&#251;',
			'&uuml;' => '&#252;',
			'&yacute;' => '&#253;',
			'&thorn;' => '&#254;',
			'&yuml;' => '&#255;',
			'&OElig;' => '&#338;',
			'&oelig;' => '&#339;',
			'&Scaron;' => '&#352;',
			'&scaron;' => '&#353;',
			'&Yuml;' => '&#376;',
			'&fnof;' => '&#402;',
			'&circ;' => '&#710;',
			'&tilde;' => '&#732;',
			'&Alpha;' => '&#913;',
			'&Beta;' => '&#914;',
			'&Gamma;' => '&#915;',
			'&Delta;' => '&#916;',
			'&Epsilon;' => '&#917;',
			'&Zeta;' => '&#918;',
			'&Eta;' => '&#919;',
			'&Theta;' => '&#920;',
			'&Iota;' => '&#921;',
			'&Kappa;' => '&#922;',
			'&Lambda;' => '&#923;',
			'&Mu;' => '&#924;',
			'&Nu;' => '&#925;',
			'&Xi;' => '&#926;',
			'&Omicron;' => '&#927;',
			'&Pi;' => '&#928;',
			'&Rho;' => '&#929;',
			'&Sigma;' => '&#931;',
			'&Tau;' => '&#932;',
			'&Upsilon;' => '&#933;',
			'&Phi;' => '&#934;',
			'&Chi;' => '&#935;',
			'&Psi;' => '&#936;',
			'&Omega;' => '&#937;',
			'&alpha;' => '&#945;',
			'&beta;' => '&#946;',
			'&gamma;' => '&#947;',
			'&delta;' => '&#948;',
			'&epsilon;' => '&#949;',
			'&zeta;' => '&#950;',
			'&eta;' => '&#951;',
			'&theta;' => '&#952;',
			'&iota;' => '&#953;',
			'&kappa;' => '&#954;',
			'&lambda;' => '&#955;',
			'&mu;' => '&#956;',
			'&nu;' => '&#957;',
			'&xi;' => '&#958;',
			'&omicron;' => '&#959;',
			'&pi;' => '&#960;',
			'&rho;' => '&#961;',
			'&sigmaf;' => '&#962;',
			'&sigma;' => '&#963;',
			'&tau;' => '&#964;',
			'&upsilon;' => '&#965;',
			'&phi;' => '&#966;',
			'&chi;' => '&#967;',
			'&psi;' => '&#968;',
			'&omega;' => '&#969;',
			'&thetasym;' => '&#977;',
			'&upsih;' => '&#978;',
			'&piv;' => '&#982;',
			'&ensp;' => '&#8194;',
			'&emsp;' => '&#8195;',
			'&thinsp;' => '&#8201;',
			'&zwnj;' => '&#8204;',
			'&zwj;' => '&#8205;',
			'&lrm;' => '&#8206;',
			'&rlm;' => '&#8207;',
			'&ndash;' => '&#8211;',
			'&mdash;' => '&#8212;',
			'&lsquo;' => '&#8216;',
			'&rsquo;' => '&#8217;',
			'&sbquo;' => '&#8218;',
			'&ldquo;' => '&#8220;',
			'&rdquo;' => '&#8221;',
			'&bdquo;' => '&#8222;',
			'&dagger;' => '&#8224;',
			'&Dagger;' => '&#8225;',
			'&bull;' => '&#8226;',
			'&hellip;' => '&#8230;',
			'&permil;' => '&#8240;',
			'&prime;' => '&#8242;',
			'&Prime;' => '&#8243;',
			'&lsaquo;' => '&#8249;',
			'&rsaquo;' => '&#8250;',
			'&oline;' => '&#8254;',
			'&frasl;' => '&#8260;',
			'&euro;' => '&#8364;',
			'&image;' => '&#8465;',
			'&weierp;' => '&#8472;',
			'&real;' => '&#8476;',
			'&trade;' => '&#8482;',
			'&alefsym;' => '&#8501;',
			'&crarr;' => '&#8629;',
			'&lArr;' => '&#8656;',
			'&uArr;' => '&#8657;',
			'&rArr;' => '&#8658;',
			'&dArr;' => '&#8659;',
			'&hArr;' => '&#8660;',
			'&forall;' => '&#8704;',
			'&part;' => '&#8706;',
			'&exist;' => '&#8707;',
			'&empty;' => '&#8709;',
			'&nabla;' => '&#8711;',
			'&isin;' => '&#8712;',
			'&notin;' => '&#8713;',
			'&ni;' => '&#8715;',
			'&prod;' => '&#8719;',
			'&sum;' => '&#8721;',
			'&minus;' => '&#8722;',
			'&lowast;' => '&#8727;',
			'&radic;' => '&#8730;',
			'&prop;' => '&#8733;',
			'&infin;' => '&#8734;',
			'&ang;' => '&#8736;',
			'&and;' => '&#8743;',
			'&or;' => '&#8744;',
			'&cap;' => '&#8745;',
			'&cup;' => '&#8746;',
			'&int;' => '&#8747;',
			'&there4;' => '&#8756;',
			'&sim;' => '&#8764;',
			'&cong;' => '&#8773;',
			'&asymp;' => '&#8776;',
			'&ne;' => '&#8800;',
			'&equiv;' => '&#8801;',
			'&le;' => '&#8804;',
			'&ge;' => '&#8805;',
			'&sub;' => '&#8834;',
			'&sup;' => '&#8835;',
			'&nsub;' => '&#8836;',
			'&sube;' => '&#8838;',
			'&supe;' => '&#8839;',
			'&oplus;' => '&#8853;',
			'&otimes;' => '&#8855;',
			'&perp;' => '&#8869;',
			'&sdot;' => '&#8901;',
			'&lceil;' => '&#8968;',
			'&rceil;' => '&#8969;',
			'&lfloor;' => '&#8970;',
			'&rfloor;' => '&#8971;',
			'&lang;' => '&#9001;',
			'&rang;' => '&#9002;',
			'&larr;' => '&#8592;',
			'&uarr;' => '&#8593;',
			'&rarr;' => '&#8594;',
			'&darr;' => '&#8595;',
			'&harr;' => '&#8596;',
			'&loz;' => '&#9674;',
			'&spades;' => '&#9824;',
			'&clubs;' => '&#9827;',
			'&hearts;' => '&#9829;',
			'&diams;' => '&#9830;'
		);
		//echo str_replace( array_keys($to_ncr), array_values($to_ncr), $text );;
		return str_replace( array_keys($to_ncr), array_values($to_ncr), $text );
	}
}
?>
