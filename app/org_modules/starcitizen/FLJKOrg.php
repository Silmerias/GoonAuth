<?php

class FLJKOrg extends OrgModule
{
	public function memberAdded($gameuser)
	{
		if ($this->game->GAbbr !== 'sc')
			return;

		// $query = '/api/orgs/invite';
		// $body = '{"symbol":"FLJK","message":"Your application to Goonrathi.com has been approved!  Welcome to FLJK!","nickname":"'.$gameuser->GUCachedName.'"}';
		$query = '/api/orgs/getOrgMembers';
		$body = '{"symbol":"FLJK","search":"Nalin","admin_mode":"1"}';

		// Submit our query.
		$ret = $this->submit_rsi_query($query, $body);
		Log::error(implode("\n", $ret));
		if (!isset($ret) || !is_array($ret))
			return;

		// Record an error.
		if (isset($ret['success']) && $ret['success'] == 0)
		{
			switch ($ret['code'])
			{
				case "ErrNotOrgReady":
					break;
			}
		}
	}

	public function memberRejected($gameuser)
	{
	}

	public function memberKicked($gameuser)
	{
	}

	private function get_cookies($content)
	{
		// Get returned cookies.
		$ret = array();
		$cc = array();
		preg_match_all('/Set-Cookie:(?<cookie>\s{0,}.*)$/im', $content, $cc);
		foreach ($cc['cookie'] as $c)
		{
			if (preg_match('/\s*([^=]+)=([^;]+);(.+)$/', $c, $parts))
				$ret[$parts[1]]=$parts[2];
		}
		return $ret;
	}

	private function submit_rsi_query($query, $body)
	{
		$close = function($a, $b)
		{
			curl_close($a);
			unlink($b);
		};

		$user = Config::get('goonauth.rsiUser');
		$pass = Config::get('goonauth.rsiPass');

		// Set up our cookie jar file.
		$cj = tempnam("/tmp", "rsi-cookiejar");

		// Initialize cURL and get our cookies.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://robertsspaceindustries.com');
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:29.0) Gecko/20100101 Firefox/29.0');
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIESESSION, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cj);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cj);
		//curl_setopt($ch, CURLOPT_SSLVERSION, 4);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, true);
		$content = curl_exec($ch);
		if (curl_error($ch))
		{
			Log::error("1");
			$ret = array('error' => curl_error($ch));
			$close($ch, $cj);
			return $ret;
		}

		// Get returned cookies.
		$cookies = $this->get_cookies($content);

		// If we received login information, do a sign-in.
		if (isset($user) && isset($pass))
		{
			$post = '{"username":"'.$user.'","password":"'.$pass/*md5($pass)*/.'","remember":"0"}';

			// Attempt to sign in now.
			curl_setopt($ch, CURLOPT_URL, 'https://robertsspaceindustries.com/api/account/signin');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Accept: */*',
				'Content-Type: application/json',
				'X-Rsi-Token: '.$cookies['Rsi-Token'],
				'Referer: https://robertsspaceindustries.com/')
			);
			$content = curl_exec($ch);
			if (curl_error($ch))
			{
				Log::error("2");
				$ret = array('error' => curl_error($ch));
				$close($ch, $cj);
				return $ret;
			}
		}

		// Get returned cookies.
		$cookies = $this->get_cookies($content);

		// Attempt to run our API command now.
		curl_setopt($ch, CURLOPT_URL, 'https://robertsspaceindustries.com'.$query);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Accept: */*',
			'Content-Type: application/json',
			'X-Rsi-Token: '.$cookies['Rsi-Token'],
			'Referer: https://robertsspaceindustries.com/')
		);
		$content = curl_exec($ch);
		if (curl_error($ch))
		{
			Log::error("3");
			$ret = array('error' => curl_error($ch));
			$close($ch, $cj);
			return $ret;
		}

		$ret = json_decode(utf8_encode($content), true);
		$close($ch, $cj);
		return $ret;
	}
}
