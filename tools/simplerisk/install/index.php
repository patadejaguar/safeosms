<?php

$release_version = "20170312-001";

// If PHP < 5.5
if (!check_php_version())
{
	echo "SimpleRisk will likely install, but you must use PHP >= 5.5 for the trend report to function properly.<br /><br />\n";
}

// If SimpleRisk configuration information was submitted
if (isset($_POST['submit']))
{
	// Database Connection Information
	$dbhost = addslashes($_POST['host']);
	$dbport = addslashes($_POST['port']);
	$dbuser = addslashes($_POST['user']);
	$dbpass = addslashes($_POST['pass']);

	// Connect to the mysql database
	$db = db_open($dbhost, $dbport, $dbuser, $dbpass, "mysql");

	// SimpleRisk Installation Information
	$sr_host = addslashes($_POST['sr_host']);
	$sr_db = addslashes($_POST['sr_db']);
	$sr_user = addslashes($_POST['sr_user']);
	$sr_pass = addslashes($_POST['sr_pass']);
	$sla_timeout = $_POST['sla_timeout'];
	$reneg_timeout = $_POST['reneg_timeout'];
	$db_sessions = $_POST['db_sessions'];
	$csp = $_POST['csp'];
	$timezone = $_POST['timezone'];

	// Create the SimpleRisk database
	$stmt = $db->prepare("CREATE DATABASE `" . $sr_db . "`");
	$stmt->execute();

	// Create the SimpleRisk user
        $stmt = $db->prepare("CREATE USER '" . $sr_user . "'@'" . $sr_host . "' IDENTIFIED BY '" . $sr_pass . "'");
        $stmt->execute();

	// Grant the SimpleRisk user permissions
	$stmt = $db->prepare("GRANT SELECT,INSERT,UPDATE,ALTER,DELETE,CREATE,DROP ON `" . $sr_db . "`.* TO '" . $sr_user . "'@'" . $sr_host . "'");
	$stmt->execute();

	// Reload the privileges
	$stmt = $db->prepare("FLUSH PRIVILEGES");
	$stmt->execute();

	// Close the mysql database
        db_close($db);

	// Get the schema
	$schema = $_POST['schema'];

	// Depending on the schema selected
	switch ($schema)
	{
		// Default - English
		case 1:
			$file = "simplerisk-en-" . $release_version . ".sql";
			load_file($dbhost, $dbport, $dbuser, $dbpass, $sr_db, $file);
			break;
		// Default - Spanish
		case 2:
			$file = "simplerisk-es-" . $release_version . ".sql";
			load_file($dbhost, $dbport, $dbuser, $dbpass, $sr_db, $file);
			break;
		// Default - Brazilian Portuguese
		case 3:
			$file = "simplerisk-bp-" . $release_version . ".sql";
			load_file($dbhost, $dbport, $dbuser, $dbpass, $sr_db, $file);
			break;
		// No actual schema submitted	
		default:
			echo "An invalid database schema was specified.<br /><br />\n";
			break;
	}

	// This should be the path to the config.php file
	$file = realpath(__DIR__ . '/../includes/config.php');

	$config_file = create_config_file($dbhost, $dbport, $sr_user, $sr_pass, $sr_db, $schema, $sla_timeout, $reneg_timeout, $db_sessions, $csp, $timezone);

	// If the config.php file exists where we expect it
	if (file_exists($file))
	{
		// If the config.php file is writable
		if (is_writable($file))
		{
			// Write out a temporary config.php file
			$tmp_dir = sys_get_temp_dir();
			$tmp_config_file = $tmp_dir . '/config.php';
			$fh = fopen($tmp_config_file, 'w');
			fwrite($fh, $config_file);
			fclose($fh);
			
			echo "Found a config.php file located at " . $file . ".  Should I update it with the configuration information below?<br /><br />\n";
			echo "<form name=\"update_config\" method=\"post\" action=\"\">\n";
			echo "<input type=\"submit\" name=\"update_config\" value=\"UPDATE\" />\n";
			echo "</form>\n";
		}
		else
		{
			echo "Found a config.php file located at " . $file . " but it is not writeable.  Replace the file with the content below to get SimpleRisk to work properly.<br /><br />\n";
			echo "<b>For security reasons, it is <u>HIGHLY</u> recommended that you delete the \"install\" directory once SimpleRisk has been confirmed to be up and running.</b><br /><br />\n";
		}
	}
	else
	{
		echo "I couldn't find a config.php file located at " . $file . ".  The contents that the config.php file needs to contain are printed out below so you can update it yourself.<br /><br />\n";
		echo "<b>For security reasons, it is <u>HIGHLY</u> recommended that you delete the \"install\" directory once SimpleRisk has been confirmed to be up and running.</b><br /><br />\n";
	}

	echo "<hr><br /><br />\n";
	echo nl2br(htmlentities($config_file, ENT_QUOTES, 'utf-8'));
}
// If the user wants to update the config.php file
else if (isset($_POST['update_config']))
{
	// Path to the file in the tmp dir
	$tmp_dir = sys_get_temp_dir();
	$tmp_config_file = realpath($tmp_dir . '/config.php');

        // This should be the path to the config.php file
        $file = realpath(__DIR__ . '/../includes/config.php');

	// If we successfully copy the file to the right location
	if (copy($tmp_config_file, $file))
	{
		// Delete the temporary file
		unlink($tmp_config_file);

		echo "Configuration file has been updated successfully.<br /><br />\n";
		echo "SimpleRisk should now be communicating with the database.<br /><br />\n";
		echo "<b>For security reasons, it is <u>HIGHLY</u> recommended that you delete the \"install\" directory once SimpleRisk has been confirmed to be up and running.</b><br /><br />\n";
	}
	else
	{
		echo "Something happened and we weren't able to place the configuration file at " . $file . ".  Sorry.<br /><br />\n";
		echo "<b>For security reasons, it is <u>HIGHLY</u> recommended that you delete the \"install\" directory once SimpleRisk has been confirmed to be up and running.</b><br /><br />\n";
	}

}
// No options have been selected yet so show the base page
else
{
	// Create a random password
	$generated_password = generate_token(20);

	echo "Enter your database information to proceed with SimpleRisk install:<br /><br />\n";
	echo "<form name=\"start\" method=\"post\" action=\"\">\n";

	// Database connection information table
	echo "<table>\n";
	echo "<thead>\n";
	echo "<tr>\n";
	echo "<th align=\"left\" colspan=\"2\"><u>Database Connection Information</u></th>\n";
	echo "</tr>\n";
	echo "</thead>\n";
	echo "<tbody>\n";
	echo "<tr>\n";
	echo "<td>Database IP/Host:</td>\n";
	echo "<td><input type=\"text\" size=\"30\" name=\"host\" value=\"localhost\" /></td>\n";
	echo "</tr>\n";
        echo "<tr>\n";
        echo "<td>Database Port:</td>\n";
        echo "<td><input type=\"text\" size=\"30\" name=\"port\" value=\"3306\" /></td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
        echo "<td>Database User:</td>\n";
        echo "<td><input type=\"text\" size=\"30\" name=\"user\" value=\"root\" /></td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
        echo "<td>Database Pass:</td>\n";
        echo "<td><input type=\"password\" size=\"30\" name=\"pass\" /></td>\n";
        echo "</tr>\n";
	echo "</tbody>\n";
	echo "</table>\n";
	echo "<br />\n";

	// SimpleRisk installation information table
	echo "<table>\n";
        echo "<thead>\n";
        echo "<tr>\n";
        echo "<th align=\"left\" colspan=\"3\"><u>SimpleRisk Installation Information</u></th>\n";
        echo "</tr>\n";
        echo "</thead>\n";
        echo "<tbody>\n";
        echo "<tr>\n";
        echo "<td>SimpleRisk Host:</td>\n";
        echo "<td><input type=\"text\" size=\"30\" name=\"sr_host\" value=\"localhost\" /></td>\n";
	echo "<td></td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
        echo "<td>SimpleRisk Database:</td>\n";
        echo "<td><input type=\"text\" size=\"30\" name=\"sr_db\" value=\"simplerisk\" /></td>\n";
	echo "<td></td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
        echo "<td>SimpleRisk User:</td>\n";
        echo "<td><input type=\"text\" size=\"30\" name=\"sr_user\" value=\"simplerisk\" /></td>\n";
	echo "<td></td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
        echo "<td>SimpleRisk Pass:</td>\n";
        echo "<td><input type=\"password\" size=\"30\" name=\"sr_pass\" value=\"" . $generated_password . "\" /></td>\n";
	echo "<td><== Automatically Generated Random Password</td>\n";
        echo "</tr>\n";
        echo "</tbody>\n";
        echo "</table>\n";
	echo "<br />\n";

        // SimpleRisk configuration information table
        echo "<table>\n";
        echo "<thead>\n";
        echo "<tr>\n";
        echo "<th align=\"left\" colspan=\"2\"><u>SimpleRisk Configuration Information</u></th>\n";
        echo "</tr>\n";
        echo "</thead>\n";
        echo "<tbody>\n";
        echo "<tr>\n";
        echo "<td>Database Schema:</td>\n";
        echo "<td>\n";
        echo "<select name=\"schema\">\n";
        echo "<option value=\"1\">Default - English</option>\n";
        echo "<option value=\"2\">Default - Spanish</option>\n";
        echo "<option value=\"3\">Default - Brazilian Portuguese</option>\n";
        echo "</select>\n";
	echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
        echo "<td>Session Last Activity Timeout:</td>\n";
	echo "<td><input type=\"text\" name=\"sla_timeout\" size=\"30\" value=\"3600\" /></td>\n";
	echo "</tr>\n";
        echo "<tr>\n";
        echo "<td>Session Renegotiation Timeout:</td>\n";
        echo "<td><input type=\"text\" name=\"reneg_timeout\" value=\"600\" /></td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
	echo "<td>Use Database for Sessions:</td>\n";
        echo "<td>\n";
	echo "<select name=\"db_sessions\">\n";
	echo "<option value=\"true\" selected>true</option>\n";
	echo "<option value=\"false\">false</option>\n";
	echo "</select><br />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
        echo "<td>Enable Content Security Policy:</td>\n";
	echo "<td>\n";
	echo "<select name=\"csp\">\n";
	echo "<option value=\"false\" selected>false</option>\n";
	echo "<option value=\"true\">true</option>\n";
	echo "</select><br />\n";
        echo "</td>\n";
        echo "</tr>\n";
	echo "<tr>\n";
        echo "<td>Default Timezone:</td>\n";
        echo "<td>\n";
	timezone_dropdown();
	echo "</td>\n";
        echo "</tr>\n";
        echo "</tbody>\n";
        echo "</table>\n";

	echo "<br /><br /><input type=\"submit\" name=\"submit\" value=\"INSTALL\" />\n";
	echo "</form>\n";
}

/******************************
 * FUNCTION: DATABASE CONNECT *
 ******************************/
function db_open($dbhost, $dbport, $dbuser, $dbpass, $dbname)
{
        // Connect to the database
        try
        {
                $db = new PDO("mysql:charset=UTF8;dbname=".$dbname.";host=".$dbhost.";port=".$dbport,$dbuser,$dbpass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
                $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
                $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET CHARACTER SET utf8");
		$db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'");

                return $db;
        }
        catch (PDOException $e)
        {
		die("Database Connection Failed: " . $e->getMessage());
                //printf("Unable to connect to the database.");
        }

        return null;
}

/*********************************
 * FUNCTION: DATABASE DISCONNECT *
 *********************************/
function db_close($db)
{
        // Close the DB connection
        $db = null;
}

/*******************************
 * FUNCTION: CHECK PHP VERSION *
 *******************************/
function check_php_version()
{
	// Get the version of PHP
	if (!defined('PHP_VERSION_ID'))
	{
		$version = explode('.', PHP_VERSION);
		define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
	}

	// Check if PHP >= 5.5 for trend report
	if (PHP_VERSION_ID >= 50500)
	{
		return true;
	}
	else return false;
}

/****************************
 * FUNCTION: GENERATE TOKEN *
 ****************************/
function generate_token($size)
{
        $token = "";
        $values = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));

        for ($i = 0; $i < $size; $i++)
        {
                $token .= $values[array_rand($values)];
        }

        return $token;
}

/***********************
 * FUNCTION: LOAD FILE *
 ***********************/
function load_file($dbhost, $dbport, $dbuser, $dbpass, $sr_db, $file)
{
	// Create the file path
	$path_to = realpath(__DIR__ . '/db/');
	$file_path = addslashes($path_to . '/' . $file);

	// Open the file for reading and load the text
	$fh = fopen($file_path, 'r');
	$text = fread($fh, filesize($file_path));
	fclose($fh);

	// Remove comments
	$pattern = "(#[^\r\n]+|/\*.*?\*/|//[^\r\n]+|--.*[\r\n])";
	$text= preg_replace($pattern,'',$text);

	// Parse into individual commands
	$sep=" |SEP| ";
	$text =ereg_replace("DROP"," $sep DROP",$text);
	$text =ereg_replace("INSERT"," $sep INSERT",$text);
	$text =ereg_replace("CREATE"," $sep CREATE",$text);
	$text =ereg_replace("--"," $sep --",$text);
	$sqls = explode($sep,$text);

        // Connect to the simplerisk database
        $db = db_open($dbhost, $dbport, $dbuser, $dbpass, $sr_db);

	// For each command
	foreach ($sqls as $sql)
	{
		$stmt = $db->prepare($sql);
		try {
        		$stmt->execute();
		} catch (PDOException $e) {
			echo 'Schema load failed: ' . $e->getMessage();
			db_close($db);
			return false;
		}
	}

	// Close the simplerisk database
        db_close($db);

	echo "Database Schema Loaded Successfully!<br /><br />\n";

	return true;
}

/********************************
 * FUNCTION: CREATE CONFIG FILE *
 ********************************/
function create_config_file($dbhost, $dbport, $sr_user, $sr_pass, $sr_db, $schema, $sla_timeout, $reneg_timeout, $db_sessions, $csp, $timezone)
{
	// Set the default language based on the selected schema
	switch ($schema)
	{
		case 1:
			$lang = "en";
			break;
		case 2:
			$lang = "es";
			break;
		case 3:
			$lang = "bp";
			break;
		default:
			$lang = "es";
			break;
	}

	$content = "<?php\n";
	$content .= " /* This Source Code Form is subject to the terms of the Mozilla Public\n";
	$content .= " * License, v. 2.0. If a copy of the MPL was not distributed with this\n";
	$content .= " * file, You can obtain one at http://mozilla.org/MPL/2.0/. */\n";
	$content .= "\n";
	$content .= "// MySQL Database Host Name\n";
	$content .= "define('DB_HOSTNAME', '" . $dbhost . "');\n";
	$content .= "\n";
	$content .= "// MySQL Database Port Number\n";
	$content .= "define('DB_PORT', '" . $dbport . "');\n";
	$content .= "\n";
	$content .= "// MySQL Database User Name\n";
	$content .= "define('DB_USERNAME', '" . $sr_user . "');\n";
	$content .= "\n";
	$content .= "// MySQL Database Password\n";
	$content .= "define('DB_PASSWORD', '" . $sr_pass . "');\n";
	$content .= "\n";
	$content .= "// MySQL Database Name\n";
	$content .= "define('DB_DATABASE', '" . $sr_db . "');\n";
	$content .= "\n";
	$content .= "// Session last activity timeout (Default: 3600 = 1h)\n";
	$content .= "define('LAST_ACTIVITY_TIMEOUT', '" . $sla_timeout . "');\n";
	$content .= "\n";
	$content .= "// Session renegotiation timeout (Default: 600 = 10m)\n";
	$content .= "define('SESSION_RENEG_TIMEOUT', '" . $reneg_timeout . "');\n";
	$content .= "\n";
	$content .= "// Use database for sessions\n";
	$content .= "define('USE_DATABASE_FOR_SESSIONS', '" . $db_sessions . "');\n";
	$content .= "\n";
        $content .= "// Enable Content Security Policy (This has broken Chrome in the past)\n";
	$content .= "define('CSP_ENABLED', '" . $csp . "');\n";
	$content .= "\n";
	$content .= "// Set the default language (Can be overridden per user)\n";
	$content .= "// Options: bp, en, es\n";
	$content .= "define('LANG_DEFAULT', '" . $lang . "');\n";
	$content .= "\n";
	$content .= "// Set the default Timezone\n";
	$content .= "// List of supported timezones here: http://www.php.net/manual/en/timezones.php\n";
	$content .= "date_default_timezone_set('" . $timezone . "');\n";
        $content .= "\n";
        $content .= "// Turn on debugging\n";
        $content .= "define('DEBUG', 'false');\n";
	$content .= "\n";
	$content .= "// Debug file\n";
	$content .= "define('DEBUG_FILE', '/tmp/debug_log');\n";
	$content .= "\n";
	$content .= "?>";

	return $content;
}

/*******************************
 * FUNCTION: TIMEZONE DROPDOWN *
 *******************************/
function timezone_dropdown()
{
	$zones = timezone_identifiers_list();
       
	echo "<select name=\"timezone\">\n";

	foreach ($zones as $zone)
	{
		echo "<option value=\"" . $zone . "\"" . ($zone == "America/Chicago" ? " selected" : "") . ">" . $zone . "</option>\n";
    	}

	echo "</select>\n";
}

?>
