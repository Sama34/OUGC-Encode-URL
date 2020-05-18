<?php

/***************************************************************************
 *
 *	OUGC Encode URL plugin (/inc/languages/english/admin/ougc_encodeurl.lang.php)
 *	Author: Omar Gonzalez
 *	Copyright: © 2020 Omar Gonzalez
 *
 *	Website: https://ougc.network
 *
 *	Allow administrators to encode URLs within post content.
 *
 ***************************************************************************

****************************************************************************
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
****************************************************************************/
 
// Plugin APIC
$l['setting_group_ougc_encodeurl'] = 'OUGC Encode URL';
$l['setting_group_ougc_encodeurl_desc'] = 'Allow administrators to encode URLs within post content.';

// PluginLibrary
$l['ougc_encodeurl_pluginlibrary'] = 'This plugin requires <a href="{1}">PluginLibrary</a> version {2} or newer. Please upload the required files.';

// Settings
$l['setting_ougc_encodeurl_forums'] = 'Allowed Forums';
$l['setting_ougc_encodeurl_forums_desc'] = 'Select the forums where this this feature is enabled.';
$l['setting_ougc_encodeurl_domains'] = 'Affected Domains';
$l['setting_ougc_encodeurl_domains_desc'] = 'Paste each domain you want to encode, one domain per line.';
$l['setting_ougc_encodeurl_ignored'] = 'Ignored URLs';
$l['setting_ougc_encodeurl_ignored_desc'] = 'Paste each domain you want to ignore, one domain per line. This setting only works if the "Decode URLs" is left empty.';
$l['setting_ougc_encodeurl_mybbredirect'] = 'Use MyBB Redirect';
$l['setting_ougc_encodeurl_mybbredirect_desc'] = 'Enable this feature to use a friendly redirect instead of a quick redirect.';