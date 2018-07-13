<?php
/**
 * @class     : MySQL Database Class
 * @date      : 24-04-2009
 * @version   : 0.1
 * @author    : Taylan Aktepe
 * @copyright : © 2009 Taylan Aktepe
 * @website   : http://www.taylanaktepe.com
 * @email     : taylanaktepe@yahoo.com
 * @license   : GNU General Public License (GPL)
 * @file      : class.mysql.php
 *
  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License (GPL)
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  More Info About The Licence At http://www.gnu.org/copyleft/gpl.html

 *
 */

/**
 * MySQL Database Class
 */
class DbManager {

    /**
     * Variables
     */
    public static $empresa = "GEO";
    public $lastQuery = null;
    public $link_id = 0; // An resource of the database link identifier.
    public $query_id = 0; // An resource of the query.
    public $record = array(); // An array of the rows.
    public $valid_charset = ''; // Valid MySQL character set.
    public $num_rows = 0; // The number of count of the rows.
    //var $insert_id = 0; // The last performed query.
    //define(DB_CHARSET, '');
    public $user;
    public $host;
    public $pass;
    public $database;
    public static $mInstance;
    public $query;
    public $errorMsg;

    public static function getMe() {
        if (self::$mInstance == null) {
            self::$mInstance = new DbManager();
            if (defined("DBHOST")) {
                self::$mInstance->connect(DBHOST, DBUSER, DBPWD, true, true, DBNAME, NULL);
                self::$mInstance->setCharset('utf-8');
            }
        }

        return self::$mInstance;
    }

    public static function reconnect() {
        self::$mInstance = null;
        self::getMe();
    }

    /**
     * mysqli_connect();
     * Connect and select database.
     * @param string The database host. (default 'localhost')
     * @param string The database username. (default 'root')
     * @param string The database user password. (default '')
     * @return boolean true if new connection, false if not. (default false)
     * @param boolean true if persistent connection, false if not. (default false)
     * @param string The database name.
     * @return string The database table prefix. (default '_')
     */
    function connect($db_host = 'localhost', $db_username = 'root', $db_password = '', $new_link_id = false, $pconnect = false, $db_name = '', $table_prefix = '_') {
        // Construct the username and tables prefix.
        $this->db_username = $db_username;
        $this->db_name = $db_name;
        $this->tbl_pre = $table_prefix;

        $this->link_id = mysqli_connect($db_host, $db_username, $db_password);
        mysqli_select_db($this->link_id, $db_name);
        //}

        if (!$this->link_id) {
            //die('No pudo conectarse: ' . mysqli_error());
            $this->db_error('connect', $db_host);
        }
        $this->host = $db_host;
        $this->user = $db_username;
        $this->pass = $db_password;
        $this->database = $db_name;
        unset($db_host, $db_username, $db_password, $new_link_id, $pconnect, $db_name, $table_prefix);
    }
    
    /**
     * 
     * @param string $charset
     */
    function setCharset($charset){
        mysqli_set_charset($this->link_id, $charset);
    }

    /**
     * mysqli_close();
     * Close the database connection.
     */
    function close() {
        if ($this->link_id)
            @mysqli_close($this->link_id);
    }

    /**
     * mysqli_escape_string();
     * MySQL escape function.
     */
    public function escape($string) {
        if (version_compare(phpversion(), '4.3.0') == '-1') {
            return mysqli_escape_string($this->link_id, $string);
        } else {
            return mysqli_real_escape_string($this->link_id, $string);
        }
    }

    /**
     * mysqli_query();
     * Query the database.
     * mysqli_affected_rows, mysqli_num_rows are in.
     * @param string The SQL query to take action.
     */
    function query($query, $shutError = false) {
        $this->query_id = mysqli_query($this->link_id, $query);
        $err = mysqli_error($this->link_id);
        $num = mysqli_errno($this->link_id);
        $this->lastQuery = $query;

        if ($num > 0) {

            $mess = $this->getErrorMsg($num);
            if ($mess == null) {
                $mess = "SQL error: " . $err . "\n";
                $mess .= "SQL errno: " . mysqli_errno($this->link_id) . "\n";
                $mess .= "<br/>";
                $mess .= "SQL: " . $query . "\n";
            }
            if (!$shutError && DEBUG) {
                ?>
                <div class="ui-widget" style="border: 0px solid white; width: 800px; height: 500px; vertical-align: bottom; float:left;" align="center">
                    <div class="ui-state-error ui-corner-all" style="margin-top: 0px; padding: 0pt 0.7em; height: auto; width: 450px;">
                        <span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span>
                        <?php echo $mess; ?>
                    </div>
                </div>
                <?php
            } else {
                $this->errorMsg = $mess;
                return null;
            }
            throw new Exception('Error de base de datos');
        }
        if (!$this->query_id) {
            //  echo "<p>$query <strong>query failed!</strong> </p>";
            //  echo "<p> Possible Error: </p>";
            // echo "<p>$mess</p>";
            exit();
        } else {
            // Get num rows.
            $this->num_rows = @mysqli_num_rows($this->query_id);
            return $this->query_id;
        }
        unset($query);
    }

    function fieldName($i) {
        return @mysqli_field_name($this->query_id, $i);
    }

    function getErrorMsg($errno) {
        /* switch ($errno) {
          case 1062: return "Registro duplicado";
          } */

        return null;
    }

    /**
     * mysqli_query();
     * Query the database.
     * mysqli_affected_rows, mysqli_num_rows are in.
     * @param string The SQL query to take action.
     */
    function queryToArray($query, $process = false) {
        $rs = $this->query($query);
        #echo $query;
        #print_r($rs);
        return $this->fetch_all_array($rs, $process);
    }

    /**
     * table_exist
     * @desc Checks if table already exist in database.
     * @param string The table name to take action.
     */
    function table_exists($table_name = '') {
        $table = $this->query("SHOW TABLES LIKE '" . $this->tbl_pre . $table_name . "'");
        if (@mysqli_fetch_row($table) == false)
            return true;
        else
            return false;
        unset($table_name);
    }

    /**
     * optimize_table
     * @desc Optimize table after many operations.
     * @param string The table name to take action.
     */
    function optimize_table($table_name = '') {
        return $this->query("OPTIMIZE TABLE " . $this->db_name . "." . $this->tbl_pre . $table_name);
    }

    function fetchAssoc($result) {
        $this->query_id = $result;
        if (isset($this->query_id)) {
            //$this->record = @mysqli_fetch_array($this->query_id);
            return @mysqli_fetch_array($result, MYSQLI_ASSOC);
        }
        return null;
    }

    /**
     * mysqli_fetch_array();
     * Fetch a result row.
     * @param resource The result of the query to take action.
     */
    function fetch_array($result) {
        $this->query_id = $result;
        if (isset($this->query_id)) {
            //$this->record = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $this->record = mysqli_fetch_array($result, MYSQLI_ASSOC);
        }
        if ($this->record) {
            $this->record = array_map('stripslashes', $this->record);
        }
        return $this->record;
        //unset($result);
    }

    public function strip($value, $process = false) {
        if ($process) {
            if (is_array($value))
                if ($this->array_is_associative($value)) {
                    foreach ($value as $k => $v)
                        $tmp_val[$k] = $this->ascii_to_entities($v);
                    $value = $tmp_val;
                }
                else
                    for ($j = 0; $j < sizeof($value); $j++)
                        $value[$j] = $this->ascii_to_entities($value[$j]);
            else
                $value = $this->ascii_to_entities($value);
        }
        return $value;
    }

    private function array_is_associative($array) {
        if (is_array($array) && !empty($array)) {
            for ($iterator = count($array) - 1; $iterator; $iterator--) {
                if (!array_key_exists($iterator, $array)) {
                    return true;
                }
            }
            return !array_key_exists(0, $array);
        }
        return false;
    }

    /**
     * Fetch all rows.
     * @param resource The result of the query to take action.
     */
    function fetch_all_array($result, $process = false) {
        $this->query_id = $result;
        $out = array();
        while ($row = $this->fetch_array($this->query_id)) {
            $out[] = $this->strip($row, $process);
        }
        $this->free_result($this->query_id);

        return $out;
        unset($result);
    }

    /*
     * Excecute a query and return the data in JSON FORMAT
     */

    function query_toArray($query) {
        $res = $this->fetch_all_array($this->query($query));
        return $res;
    }

    /*
     * Excecute a query and return the data in JSON FORMAT
     */

    function query_toJson_sql($query, $fieldsAndTitles) {
        $res = $this->fetch_all_array($this->query($query));
        $index = 0;
        $json_string = "[";
        foreach ($res as $row) {
            $json_string .= ( $index == 0) ? "" : ",";
            $arr = array();
            foreach ($fieldsAndTitles as $title => $field) {
                $arr[$title] = $row[$field];
            }

            $json_string.= json_encode($arr);
            $index++;
        }
        return $json_string . "]";
    }

    /*
     * Excecute a query and return the data in JSON FORMAT
     */

    function query_toJson($table, $where = NULL, $fieldsAndTitles = NULL) {
        $res = $this->query_select($table, $where);
        $index = 0;
        $json_string = "[";
        foreach ($res as $row) {
            $json_string .= ( $index == 0) ? "" : ",";
            $arr = array();
            foreach ($fieldsAndTitles as $title => $field) {
                $arr[$title] = $this->ascii_to_entities($row[$field]);
            }

            $json_string.= json_encode($arr);
            $index++;
        }
        return $json_string . "]";
    }

    /**
     *
     * @param type $str
     * @return type 
     */
    function ascii_to_entities($str) {
        $count = 1;
        $out = '';
        $temp = array();

        for ($i = 0, $s = strlen($str); $i < $s; $i++) {
            $ordinal = ord($str[$i]);

            if ($ordinal < 128) {
                if (count($temp) == 1) {
                    $out .= '&#' . array_shift($temp) . ';';
                    $count = 1;
                }

                $out .= $str[$i];
            } else {
                if (count($temp) == 0) {
                    $count = ($ordinal < 224) ? 2 : 3;
                }

                $temp[] = $ordinal;

                if (count($temp) == $count) {
                    $number = ($count == 3) ? (($temp['0'] % 16) * 4096) +
                            (($temp['1'] % 64) * 64) +
                            ($temp['2'] % 64) : (($temp['0'] % 32) * 64) +
                            ($temp['1'] % 64);

                    $out .= '&#' . $number . ';';
                    $count = 1;
                    $temp = array();
                } else {
                    $out .= $str[$i];
                }
            }
        }

        return $out;
    }

    /**
     * Return array with data select
     * @params table name, where, fieldTittle=>table fieldName
     * */
    function query_select($table, $where = NULL, $orderBy = NULL, $start = NULL, $limit = NULL, $orderByType = 'DESC') {
        $sql = "SELECT * FROM " . $table;

        if ($where != NULL) {
            $sql.=" WHERE " . $where;
        }
        if ($orderBy != NULL) {
            $sql.=" ORDER BY " . $orderBy . " " . $orderByType . " ";
        }
        if ($start != NULL && $limit != NULL) {
            $sql.=" LIMIT " . $start . " , " . $limit;
        }
        $this->lastQuery = $sql;
        $res = $this->fetch_all_array($this->query($sql), true);

        return $res;
    }
    
    function max_id($table, $field){
        $result = $this->queryToArray(sprintf('select max(%s) id from %s', $field, $table));
        if(count($result) > 0 ){
            return $result[0]['id'];
        }
        return false;
    }

    /**
     * mysqli_free_result();
     * Free query.
     * @param string The query to take action.
     */
    function free_result($query) {
        return @mysqli_free_result($query);
    }

    /**
     * kill_query
     * Kill the query.
     * @param string The query to take action.
     */
    function kill_query($query) {
        return $this->query("KILL $query");
    }

    /**
     * query_first
     * Fetches only first row.
     * @param string The query string to take action.
     */
    function query_first($result) {
        $query = $this->query($result);
        $out = $this->fetch_array($query);
        $this->free_result($query);
        return $out;
        unset($result);
    }

    /**
     * INSERT
     * Insert query.
     * @param string The table name.
     * @param array An array of fields and values.
     */
    function query_insert($table, $array) {
        $fields = '';
        $values = '';
        if (!is_array($array))
            return false;
        foreach ($array as $field => $value) {
            $fields .= $field . ", ";
            $values .= $value . ", ";
        }
        $fields = rtrim($fields, ', ');
        $values = rtrim($values, ', ');
        $this->query("
			    INSERT
			    INTO " . $this->tbl_pre . $table . " (" . $fields . ")
			    VALUES (" . $values . ")
		    ");
        if ($this->query) {
            return $this->query;
        } else {
            return false;
        }
        unset($array, $field, $value);
    }

    /**
     * UPDATE
     * Update query.
     * @param string The table name.
     * @param array An array of fields and values.
     * @param string Where clause of the query.
     */
    function query_update($table, $array, $where = '') {
        if (is_array($array)) {
            $query = '';
            foreach ($array as $field => $value) {
                $query .= $field . " = " . $value . ", ";
            }
            $query = rtrim($query, ', ');

            if ($where != '') {
                $query .= " WHERE $where";
            }
            $query = " UPDATE " . $this->tbl_pre . $table . " SET $query ";
            //  echo $query;
            $this->query($query);
        } else {
            return false;
        }
        unset($array, $where, $field, $value);
        return $query;
    }

    /**
     * DELETE
     * Delete query.
     * @param string The table name.
     * @param string Where clause of the query.
     */
    function query_delete($table = '', $where = '') {
        $query = !$where ? 'DELETE FROM ' . $this->tbl_pre . $table : 'DELETE FROM ' . $this->tbl_pre . $table . ' WHERE ' . $where;
        $this->query($query);
        unset($table, $where);
    }

    /**
     * ERROR
     * Error message.
     * @param string Custom message text.
     */
    function db_error($short = '', $param = '') {
        $short = preg_replace('/[^a-z0-9]/i', '', $short);
        $param = preg_replace('/[^a-z0-9]/i', '', $param);
        $errno = mysqli_errno();
        $error = mysqli_error();
        if ($errno == '')
            $errno = '<i>Unknown</i>';
        if ($error == '')
            $error = '<i>Unknown</i>';
        // Custom message text.
        if ($short == 'connect') {
            echo '
          <h2>MySQL Error</h2>
          <p><strong>MySQL error code</strong>: ' . $errno . '</p>
          <p><strong>Error message</strong>: ' . $error . '</p>
          <strong>Details</strong>: Failed to connect to database server <code>' . $param . '</code>.
          <h4>Please follow the following guidelines:</h4>
          <ul>
            <li><code>fos-config.php</code> file, the database user name and password, right?</li>
            <li><code>fos-config.php</code> file, the database server name right?</li>
            <li>Does your database server is running? If you are not sure what they mean ask your hosting company.</li>
          </ul> <br />
          <p class="help">If you need assistance, please visit <a href="" title="">Help Center</a>.</p>
          ';
        } elseif ($short == 'select') {
            echo '
          <h2>MySQL Error</h2>
          <p><strong>MySQL error code</strong>: ' . $errno . '</p>
          <p><strong>Error message</strong>: ' . $error . '</p>
          <strong>Details</strong>: Unable to select database <code>' . $this->db_name . '</code>.
          <h4>Please follow the following guidelines:</h4>
          <ul>
            <li>Does user <code>' . $this->db_username . '</code> have permission to use the <code>' . $this->db_name . '</code> database?</li>
            <li><code>fos-config.php</code> file, the database name right? If you are not sure what they mean ask your hosting company.</li>
          </ul> <br />
          <p class="help">If you need assistance, please visit <a href="" title="">Help Center</a>.</p>
          ';
        } else {
            echo '
          <h2>MySQL Error</h2>
          <p><strong>MySQL error code</strong>: ' . $errno . '</p>
          <p><strong>Error message</strong>: ' . $error . '</p>
          <strong>Details</strong>: <i>Unknown</i>.  <br />
          <p class="help">If you need assistance, please visit <a href="" title="">Help Center</a>.</p>
          ';
        }
        exit();
        unset($short, $param);
    }

// End database class
}
?>