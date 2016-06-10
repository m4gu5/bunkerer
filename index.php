<?php
	session_start();
	include_once("bunkerer.php");
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
		<form method="GET">
			<select name="project" onchange="this.form.submit()">
                        	<option value="" disabled selected>Select project</option>
				<?php
					display_project_selection_options();
				?>
			</select>
			<?php
				output_format_selection();
			?>
			<input id="form-submit" type="submit" value="Get entries"></input>
			<!-- Button is not needed when JavaScript is enabled -->
                        <script>document.getElementById('form-submit').style.display = 'none'</script>
		</form>
		<?php
			output_entries_if_params_set();
		?>
		<div id="footer">
			<a href="https://github.com/m4gu5/bunkerer/issues" target="_blank" title="Found a bug? Entries missing?&#013;Please create an issue or a pull request on GitHub :)">Something wrong?</a>
		</div>
	</body>
</html>
