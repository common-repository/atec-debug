function parseWPconfig()
{
	const config=jQuery("#atec_wp_config"); 
	let html=config.html();
	html=html.replace(/(\w+_DEBUG_?\w*)/gm, "<b class='atec-red'>$1</b>");
	html=html.replace(/(WP_MEMORY_LIMIT|WP_ALLOW_REPAIR|WP_AUTO_UPDATE_CORE)/gm, "<b class='atec-red'>$1</b>");
	html=html.replace(/(\/\/\s(.*)\n)/gm, "<font color=#aaa>$1</font>");
	html=html.replace(/(\/\*\*?\s(.*)\*\/\n)/gm, "<font color=#aaa>$1</font>");
	config.html(html);
}

function parseXDebug()
{
	const xdebug=jQuery("#xdebug"); 
	let html=xdebug.html();
	html = html.replaceAll('&lt;', '<'); 
	html = html.replaceAll('&gt;', '>');
	html = html.replaceAll('&quot;', '"');
	html = html.replaceAll('&#039;', "'");
	html = html.replaceAll('&amp;', "&");
	html = html.replaceAll('<th>Docs</th>', ''); 
	xdebug.html(html).show();
};

function parseDebug()
{
	const debug=jQuery("#debug"); 
	let html=debug.html();
	html=html.replace(/on\sline\s([\d]+)/gm, "<span class='atec-red'>LINE $1</span>");
	html=html.replace(/(\/[\w\d\-\_]+\.php)\s/gm, "<b>$1 </b>");
	html=html.replace(/(PHP\sDeprecated)/gm, "<font color='blue'>Deprecated</font>");
	html=html.replace(/(Undefined)/gm, "<font color='blue'>$1</font>");
	html=html.replace(/(Uncaught)/gm, "<font color='blue'>$1</font>");
	html=html.replace(/(syntax\serror)/gm, "<font color='blue'>$1</font>");
	debug.html(html);
	if (html!=='') debug.show();
}
