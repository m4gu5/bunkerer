<?php

	$GLOBALS["ipv4-format"] = "ipv4";
	$GLOBALS["ipv6-format"] = "ipv6";
	$GLOBALS["ipv4ipv6-format"] = "ipv4ipv6";
	$GLOBALS["plain-format"] = "plain";

	$GLOBALS["ipv4-text"] = "hosts IPv4";
	$GLOBALS["ipv6-text"] = "hosts IPv6";
	$GLOBALS["ipv4ipv6-text"] = "hosts IPv4 + IPv6";
	$GLOBALS["plain-text"] = "plain";

	$GLOBALS["ipv4-prefix"] = "127.0.0.1 ";
	$GLOBALS["ipv6-prefix"] = "::1 ";
	$GLOBALS["plain-prefix"] = "";

        function get_entries() {
		if (!isset($_SESSION["entries"])) {
			// File needs to be read
			$entries_arr = array();
			$entries = fopen("entries", "r") or die("Unable to open file containing entries!");
			while (!feof($entries)) {
				$entries_arr[] = fgets($entries);
			}
			fclose($entries);
			$_SESSION["entries"] = $entries_arr;
		}
		return $_SESSION["entries"];
	}

	function extract_project_name($line) {
		$project_name = substr($line, 1, strlen($line));
		$project_name = trim($project_name);
		return $project_name;
	}

	function get_entries_for_project($project) {
		$entries = get_entries();
		$project_found = false;
		$project_entries = array();
		for ($i = 0; $i < sizeof($entries); $i++) {
			$line = $entries[$i];
			if (!$project_found) {
				if (substr($line, 0, 1) === "#") {
					$project_name = extract_project_name($line);
					if ($project_name === $project) {
						$project_found = true;
					}
				}
			} else {
				// Get IPs/hosts to block until next project starts
				if (empty(trim($line)) || substr($line, 0, 1) === "#") {
					// All entries were read
					break;
				}
				$project_entries[] = $line;
			}
		}
		return $project_entries;
	}

	function display_project_selection_options() {
		$entries = get_entries();
		for ($i = 0; $i < sizeof($entries); $i++) {
			$line = $entries[$i];
			if (substr($line, 0, 1) === "#") {
				// This is a project name
				$project_name = extract_project_name($line);
				$selected_string = "";
				if (isset($_GET["project"]) && $project_name === $_GET["project"]) {
					// Select the requested project again
					$selected_string = "selected";
				}
				echo "<option ".$selected_string." value=\"".$project_name."\">".$project_name."</option>";
			}
		}
	}

	function output_entries($project_entries, $format) {
		echo "<div id=\"entries\">";
		for ($i = 0; $i < sizeof($project_entries); $i++) {
			// IPv4 is the default
			$prefix = $GLOBALS["ipv4-prefix"];
			if ($format === "plain") {
				$prefix = $GLOBALS["plain-prefix"];
			} else if ($format === "ipv6") {
				$prefix = $GLOBALS["ipv6-prefix"];
			} else if ($format === "ipv4ipv6") {
				echo $prefix.$project_entries[$i]."<br>";
				$prefix = "::1 ";
			}
			echo $prefix.$project_entries[$i];
			if ($i < sizeof($project_entries) - 1) {
				echo "<br>";
			}
		}
		echo "</div>";
	}

	function output_entries_if_params_set() {
		if (isset($_GET["project"])) {
			$project = urldecode($_GET["project"]);
			$format = $_GET["format"];
			$project_entries = get_entries_for_project($project);
			if (sizeof($project_entries) > 0) {
				output_entries($project_entries, $format);
				echo "</div>";
			} else {
				echo "Invalid project specified!";
			}
		}
	}

	function output_format_selection() {
		$formats = array(
			$GLOBALS["ipv4-format"] => $GLOBALS["ipv4-text"],
			$GLOBALS["ipv6-format"] => $GLOBALS["ipv6-text"],
			$GLOBALS["ipv4ipv6-format"] => $GLOBALS["ipv4ipv6-text"],
			$GLOBALS["plain-format"] => $GLOBALS["plain-text"]
		);
	
		$format_chosen = $_GET["format"];

		if (!isset($format_chosen)) {
			$format_chosen = $GLOBALS["ipv4"];
		}

		echo "<select name=\"format\" onchange=\"this.form.submit()\">";
		foreach ($formats as $value => $text) {
			$selected_string = "";
			if ($value === $format_chosen) {
				$selected_string = " selected ";
			}
			echo "<option".$selected_string." value=\"".$value."\">".$text."</option>";
		}
		echo "</select>";
	}
?>
