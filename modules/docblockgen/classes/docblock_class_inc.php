<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

class docblock extends object 
{
	public function init()
	{
		require_once($this->getPearResource('PHP/DocBlockGenerator.php'));
		$this->objUser = $this->getObject('user', 'security');
	}
	
	public function genDocs($module)
	{
		$param = array('license' => 'gpl', 'category' => 'Chisimba', 'author' => $this->objUser->fullName(), 'email' => $this->objUser->email(), 'year' => date('Y'), 'link' => 'http://avoir.uwc.ac.za', 'version' => 'cvs', 'package' => $module);
		$docblockgen = new PHP_DocBlockGenerator();
		// get a list of files to process...
		$this->objConfig = $this->getObject('altconfig', 'config');
		$path = $this->objConfig->getModulePath().$module;
		chdir($path);
		
		foreach(glob('*') as $files)
		{
			if($files == 'CVS' || $files == 'error_log' || $files == 'register.conf' || $files == 'templates' || $files == 'sql' || $files == 'resources')
			{
				continue;
			}
			else {
				foreach(glob('*') as $file)
				{
					if($file != 'CVS' && $file != 'error_log' && $file != 'register.conf' && $file != 'templates' && $file != 'sql' && $file != 'resources' && is_dir($file))
					{
						chdir($file);
						foreach(glob('*.php') as $classes)
						{
							//echo "class: ".$classes."<br />";
							$docblockgen->generate($classes, $param);
						}
					}
					if($file != 'CVS' && $file != 'error_log' && $file != 'register.conf' && $file != 'templates' && $file != 'sql' && $file != 'resources' && !is_dir($file))
					{
						chdir($path);
						foreach(glob('*.php') as $file2)
						{
							//echo "file2: ".$file2."<br />";
							$docblockgen->generate($file2, $param);
						}
					}
					
					
				}
			}
				//$docblockgen->generate($files, $param);
		}
	}
}
?>