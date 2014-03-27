<?php

$basePath = "./"; //Relative project root path
$tmpDir   = "tmp/"; //Composer HOME for current project

$composerPHARFile    = "composer.phar";
$composerInstallFile = "composerinstall.php";
$composerJsonFile    = "composer.json";
$composerLockFile    = "composer.lock";

$basePath                    = rtrim(realpath('../'), '/') . '/';
$tmpDirFileFullPath          = rtrim($basePath . $tmpDir, '/') . '/';
$composerInstallFileFullPath = $basePath . $tmpDir . $composerInstallFile;
$composerPHARFileFullPath    = $basePath . $tmpDir . $composerPHARFile;
$composerJsonFileFullPath    = $basePath . $composerJsonFile;
$composerLockFileFullPath    = $basePath . $composerLockFile;

$useSystemComposer = true; //Automaticaly set to false if composer cmd isn't found

####################################################################
####################################################################

if (!function_exists('get_data')) {
	/**
	 * @param $url
	 * @return mixed
	 * @throws Exception
	 */
	function get_data($url)
	{
		if (!function_exists('curl_version')) {
			throw new Exception("This app needs the Curl PHP extension.");
		}

		if (substr($url, 0, 5) === 'https' && !extension_loaded('openssl')) {
			throw new Exception("This app needs the Open SSL PHP extension.");
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //TODO secure that, possibility of man of the middle attack
		$data = curl_exec($ch);

		$error = curl_error($ch);
		curl_close($ch);

		if ($data === false) {
			throw new Exception("Error when getting the file [$url] : $error");
		}

		return $data;
	}
}

if (!function_exists('executeCmd')) {
	/**
	 * Do an exec
	 * @param string $cmd
	 * @return string
	 * @throws Exception
	 */
	function executeCmd($cmd)
	{
		//CMD without 2>&1 , errors are shown in STDERR, and exec don't catch SDTERR
		exec($cmd . " 2>&1", $output, $return_var);

		if ($return_var > 0) {
			throw new Exception("Error when executing command : [$cmd] :\n" . implode($output, "\n") . "\n", $return_var);
		}

		return implode("\n", $output);
	}
}

if (!function_exists('commandExists')) {
	/**
	 * @param string $cmd
	 * @return bool
	 */
	function commandExists($cmd)
	{
		$returnVal = shell_exec("which $cmd");

		return !empty($returnVal);
	}
}

if (!function_exists('composerExecute')) {
	/**
	 * @param $cmd
	 * @return string
	 */
	function composerExecute($cmd)
	{
		global $basePath;
		global $composerPHARFileFullPath;
		global $tmpDirFileFullPath;
		global $useSystemComposer;

		$composerCmd = $useSystemComposer ? "composer" : "php $composerPHARFileFullPath";

		return executeCmd("cd $basePath && COMPOSER_HOME=$tmpDirFileFullPath $composerCmd --no-interaction --no-progress $cmd");
	}
}

####################################################################
####################################################################

try {

	$useSystemComposer &= commandExists('composer');

	if (!file_exists($composerJsonFileFullPath)) {
		throw new Exception("File $composerJsonFileFullPath not found");
	}

	//TMP path creation
	if (!file_exists($tmpDirFileFullPath)) {
		if (!mkdir($tmpDirFileFullPath, 0777, true)) {
			throw new Exception("Error when creating path [$tmpDirFileFullPath]");
		}
		file_put_contents($tmpDirFileFullPath . '.htaccess', "Deny from All");
	}

	if (!$useSystemComposer) {

		//Composer file downloading or updating
		if (!file_exists($composerPHARFileFullPath)) {
			echo "Downloading & installing Composer\n";

			$contentFile = get_data('https://getcomposer.org/installer');
			if (!file_put_contents($composerInstallFileFullPath, $contentFile)) {
				throw new Exception("Error when creating file [$composerInstallFileFullPath]");
			}

			executeCmd("php $composerInstallFileFullPath --install-dir=$tmpDirFileFullPath");
			unlink($composerInstallFileFullPath);
		}

		//Composer file exists verification
		if (!file_exists($composerPHARFileFullPath)) {
			throw new Exception("File [$composerPHARFileFullPath] not found.");
		}

		//Composer update
		if (time() - filemtime($composerPHARFileFullPath) > 10 * 24 * 3600) {
			composerExecute('self-update');
		}
	}

	//Go
	if (file_exists($composerLockFileFullPath)) {
		echo composerExecute('update');
	} else {
		echo composerExecute('install');
	}

} catch (Exception $e) {
	die("Error when running script : " . $e->getMessage());
}