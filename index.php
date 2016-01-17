<?php 
	session_start();
 ?>
<html>
	<head>
		<title>BOINC Bunkerer</title>
		<link rel="stylesheet" href="style.css">
		<meta charset="utf-8">
	</head>
	<body>
		<h1>Bunkerer</h1>
		<div id="instructions">
			<h3>Instructions</h3>
			<ol>
				<li>Select project</li>
				<li>Copy entries</li>
				<li>Paste into your hosts file<br>
				*nix: <span class="path">/etc/hosts</span><br>
				OS X: <span class="path">/private/etc/hosts</span><br>
				Windows: <span class="path">C:\Windows\system32\drivers\etc\hosts</span>
				</li>
			</ol>
		</div>
		<?php
			
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
						// Get ip host mapping until next project starts
						if (empty($line) || substr($line, 0, 1) === "#") {
							// All entries were read
							break;
						}
						$project_entries[] = $line;
					}
				}
				return $project_entries;
			}
			
		?>
		<form method="GET">
			<select name="project" onchange="this.form.submit()">
                        	<option value="" disabled selected>Select project</option>
				<?php
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
				?>
			</select>
			<input id="form-submit" type="submit" value="Get entries" />
			<!-- Button is not needed when JavaScript is enabled -->
                        <script>document.getElementById('form-submit').style.display = 'none'</script>
		</form>
		<?php
			if (isset($_GET["project"])) {
				$project = urldecode($_GET["project"]);
				$project_entries = get_entries_for_project($project);
				if (sizeof($project_entries) > 0) {
					echo "<div id=\"entries\">";
					for ($i = 0; $i < sizeof($project_entries); $i++) {
						echo $project_entries[$i];
						if ($i < sizeof($project_entries) - 1) {
							echo "<br>";
						}
					}
					echo "</div>";
				} else {
					echo "Invalid project specified!";
				}
			}
		?>
		<div id="footer">
			<a href="https://github.com/m4gu5/bunkerer/issues" target="_blank" title="Found a bug? Entries missing?&#013;Please create an issue or a pull request on GitHub :)">Something wrong?</a>
		</div>
	</body>
</html>
