<?php
//settings del uploader
$settings = array(

		// Website title. Displayed of top of the page.
		//'title' => 'Combos db',

		// Description for this website
		'description' => 'If you upload useless combos, you will get <b><strike>BANNED</strike></b>',

		// Upload directory. Could be absolute or relative.
		// Default: auto-detection
		'base_path' => dirname(__FILE__) . DIRECTORY_SEPARATOR,

		// Display list uploaded files
		// Default: true
		'listfiles' => true,

		// Allow users to delete files that they have uploaded (will enable sessions)
		// Default: true
		'allow_deletion' => true,

		// Allow users to mark files as hidden
		// Default: true
		'allow_private' => false,

		// Display file sizes
		// Default: true
		'listfiles_size' => true,

		// Display file dates
		// Default: true
		'listfiles_date' => true,

		// Display file dates format
		// Default: 'F d Y H:i:s'
		'listfiles_date_format' => 'F d Y H:i:s',

		// Randomize file names. Number for file name lenght or false to disable.
		// Default: 8
		'random_name_len' => 20,

		// Keep filetype (file extension) information (if random name is activated).
		// Default: true
		'random_name_keep_type' => true,

		// Letters that are used for random file name generation (alphabet).
		// Default: 'abcdefghijklmnopqrstuvwxyz0123456789'
		'random_name_alphabet' => 'abcdefghijklmnopqrstuvwxyz0123456789',

		// Display debugging information
		// Default: false
		'debug' => false,

		// Complete URL to your directory with trailing slash (!)
		// Default: autoDetectBaseUrl()
		'url' => 'https://078db.cf/',

		// Amount of seconds that each file should be stored for (0 for no limit)
		// Default: 30 days (60 * 60 * 24 * 30)
		'time_limit' => 0,

		// Files that will be ignored
		'ignores' => array(
			'.',
			'..',
			basename($_SERVER['PHP_SELF']),
			'config.php',
		),

		// Language code
		// Default: 'en'
		'lang' => 'es',

		// Language direction
		// Default: 'ltr'
		'lang_dir' => 'ltr',
	);