<?php
	ob_start();
	
	error_reporting(E_ALL);
	ini_set('display_errors', 'off');
	ini_set('log_errors', 'off');
	
	$error_handler = function ($error = '') {
		ob_clean();

		$error_crypt = new class {
			private $securekey;
			private $method = 'AES-256-CBC';
			function __construct() {
				$this->securekey = hash('sha256', 'bardzo długi klucz do dekodowania błędów systemowych przez programistę.', TRUE);
			}
			function encrypt(string $data): string
			{
				$ivSize = openssl_cipher_iv_length($this->method);
				$iv = openssl_random_pseudo_bytes($ivSize);
				$encrypted = base64_encode($iv . openssl_encrypt($data, $this->method, $this->securekey, OPENSSL_RAW_DATA, $iv));
				return $encrypted;
			}

			function decrypt(string $data): string
			{
				$data = base64_decode($data);
				$ivSize = openssl_cipher_iv_length($this->method);
				$iv = substr($data, 0, $ivSize);
				$data = openssl_decrypt(substr($data, $ivSize), $this->method, $this->securekey, OPENSSL_RAW_DATA, $iv);
				return $data;
			}
		};
		
		header($_SERVER['SERVER_PROTOCOL'] . " 503 Service Unavailable");
		header("Status: 503 Service Unavailable");
		echo '<!doctype><html><head><meta charset="UTF-8"><title>503 Service Unavailable</title></head><body>';
		echo '<h1>503 Service Unavailable</h1>';
		echo '<p>Sorry, something went wrong</p>';
		echo '<p>A team of highly trained monkeys has been dispatched to deal with this situation.</p>';
		echo '<p>If You see them, show them this information:</p>';
		echo '<p style="max-width: 500px; word-break: break-all; font-family: monospace;">';
		echo $error_crypt->encrypt($error);
		echo '</p>';
		echo '</body></html>'
		exit;
	};

	try
	{

	}
	catch (Exception $e)
	{	
		($error_handler)($e);
	}
	catch (Error $e)
	{
		($error_handler)($e);
	}
