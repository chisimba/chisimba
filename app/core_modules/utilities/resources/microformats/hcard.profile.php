<?php
	// hcard profile for hkit
	
	$this->root_class = 'vcard';
	
	$this->classes = array( 
		'fn', array('honorific-prefix', 'given-name', 'additional-name', 'family-name', 'honorific-suffix'),
		'n', array('honorific-prefix', 'given-name', 'additional-name', 'family-name', 'honorific-suffix'),
		'adr', array('post-office-box', 'extended-address', 'street-address', 'postal-code', 'country-name', 'type', 'region', 'locality'),
		'label', 'bday', 'agent', 'nickname', 'photo', 'class', 
		'email', array('type', 'value'), 
		'category', 'key', 'logo', 'mailer', 'note',
		'org', array('organization-name', 'organization-unit'),
		'tel', array('type', 'value'),
		'geo', array('latitude', 'longitude'),
		'tz', 'uid', 'url', 'rev', 'role', 'sort-string', 'sound', 'title'		
	);
	
	// classes that must only appear once per card
	$this->singles = array(
		'fn'
	);
	
	// classes that are required (not strictly enforced - give at least one!)
	$this->required = array(
		'fn'
	);

	$this->att_map = array(
		'fn'	=> array('IMG|alt'),
		'url'	=> array('A|href', 'IMG|src', 'AREA|href'),
		'photo'	=> array('IMG|src'),
		'bday'	=> array('ABBR|title'),
		'logo'	=> array('IMG|src'),
		'email' => array('A|href'),
		'geo'	=> array('ABBR|title')
	);

	
	$this->callbacks = array(
		'url'	=> array($this, 'resolvePath'),
		'photo'	=> array($this, 'resolvePath'),
		'logo'	=> array($this, 'resolvePath'),
		'email'	=> array($this, 'resolveEmail')
	);



	function hKit_hcard_post($a)
	{
		
		foreach ($a as &$vcard){
			
			hKit_implied_n_optimization($vcard);
			hKit_implied_n_from_fn($vcard);
			
		}
		
		return $a;
	
	}
	
	
	function hKit_implied_n_optimization(&$vcard)
	{
		if (array_key_exists('fn', $vcard) && !is_array($vcard['fn']) && 
			!array_key_exists('n', $vcard) && (!array_key_exists('org', $vcard) || $vcard['fn'] != $vcard['org'])){
			
			if (sizeof(explode(' ', $vcard['fn'])) == 2){
				$patterns	= array();
				$patterns[] = array('/^(\S+),\s*(\S{1})$/', 2, 1); 		// Lastname, Initial
				$patterns[] = array('/^(\S+)\s*(\S{1})\.*$/', 2, 1); 	// Lastname Initial(.)
				$patterns[] = array('/^(\S+),\s*(\S+)$/', 2, 1); 		// Lastname, Firstname
				$patterns[] = array('/^(\S+)\s*(\S+)$/', 1, 2); 		// Firstname Lastname
			
				foreach ($patterns as $pattern){
					if (preg_match($pattern[0], $vcard['fn'], $matches) === 1){
						$n					= array();
						$n['given-name']	= $matches[$pattern[1]];
						$n['family-name']	= $matches[$pattern[2]];
						$vcard['n']			= $n;
						
						
						break;
					}
				}
			}
		}
	}
	
	
	function hKit_implied_n_from_fn(&$vcard)
	{
		if (array_key_exists('fn', $vcard) && is_array($vcard['fn']) 
			&& !array_key_exists('n', $vcard) && (!array_key_exists('org', $vcard) || $vcard['fn'] != $vcard['org'])){
				
			$vcard['n']		= $vcard['fn'];
		}

		if (array_key_exists('fn', $vcard) && is_array($vcard['fn'])){
			$vcard['fn']	= $vcard['fn']['text'];
		}
	}

?>