<?php

$suspicious_referers = array(
	'a.steambeard.top',
	'a.tronehammer.top',
	'game.fertuk.site',
	'games.patlik.site',
	'garold.dertus.site',
	'info.seders.website',
	'info.sederes.website',
	'kar.razas.site',
	'news.grets.store',
	'ofer.bartikus.site',
	'rida.tokyo',
	'static.seders.website',
	'trast.manetero.online',
	'trast.mantero.online',
	'urlumbrella.com',
);

$suspicious_ips = array(
	'113.66.0.0/16',
	'113.111.0.0/16',
	'84.172.81.60',
	// '113.111.83.8',
	// '113.111.81.126',
);

$blocked_mails = array(
	'sample@email.tst',
);

$blocked_urls = array(
	'/([^/.]+)\.php\.suspected',
	'/([^/.]+)\.sql',
	'/([^/.]+)\.sql\.gz',
	'/([^/.]+)\.sql\.bz2',
	'/([^/.]+)\.sql\.xy',
	'/([^/.]+)\.zip',
	'/\.env',
	'/\.env/',
	'/admin/',
	'/administrator/',
	'/api/',
	'/app/',
	'/apps/',
	'/auth/',
	'/backend/',
	'/backup/',
	'/c/',
	'/cms/',
	'/cod/',
	'/components/',
	'/config\.bak\.php',
	'/configuration\.php',
	'/cp/',
	'/database/',
	'/demo/',
	'/dev/',
	'/docker/',
	'/docs/',
	'/dumper/',
	'/engine/',
	'/error\.php',
	'/framework/',
	'/google\.php',
	'/google\.fphp',
	'/images/',
	'/img/',
	'/inc/',
	'/info\.php',
	'/jm-ajax/',
	'/libraries/',
	'/main/',
	'/modules/',
	'/msd/',
	'/MSD/',
	'/mysql/',
	'/MySqlDumper/',
	'/mysqldumper/',
	'/old/',
	'/pdf/',
	'/personal/',
	'/phpinfo/',
	'/phpmyadmin/',
	'/protected/',
	'/scripts/',
	'/server-info/',
	'/server-status/',
	'/shared/',
	'/site/',
	'/smiley/',
	'/templates/',
	'/test/',
	'/tmp/',
	'/upel\.php',
	'/uploads/',
	'/upload/',
	'/V3\.php',
	'/vue/',
	'/wordpress/',
	'/wp-content/plugins/Cache',
	'/wp-content/plugins/core-plugin',
	'/wp-content/plugins/formcraft',
	'/wp-content/plugins/invato-market',
	'/wp-content/plugins/seoo',
	'/wp-content/uploader\.php',
);