<?php

/***************************************************************************
 *
 *	OUGC Encode URL plugin (/inc/plugins/ougc_encodeurl.php)
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

// Die if IN_MYBB is not defined, for security reasons.
defined('IN_MYBB') or die('Direct initialization of this file is not allowed.');

// PLUGINLIBRARY
defined('PLUGINLIBRARY') or define('PLUGINLIBRARY', MYBB_ROOT.'inc/plugins/pluginlibrary.php');

// Plugin API
function ougc_encodeurl_info()
{
	global $ougc_encodeurl;

	return $ougc_encodeurl->_info();
}

// _activate() routine
function ougc_encodeurl_activate()
{
	global $ougc_encodeurl;

	return $ougc_encodeurl->_activate();
}

// _is_installed() routine
function ougc_encodeurl_is_installed()
{
	global $ougc_encodeurl;

	return $ougc_encodeurl->_is_installed();
}

// _uninstall() routine
function ougc_encodeurl_uninstall()
{
	global $ougc_encodeurl;

	return $ougc_encodeurl->_uninstall();
}

// Plugin class
class OUGC_EncodeURL
{
	public $key = null;

	function __construct()
	{
		global $plugins, $settings;

		// Tell MyBB when to run the hook
		if(!defined('IN_ADMINCP'))
		{
			$plugins->add_hook('parse_message', array($this, 'hook_parse_message'));

			if(!empty($settings['ougc_encodeurl_mybbredirect']))
			{
				$plugins->add_hook('global_end', array($this, 'hook_global_end'));
			}
		}

		$this->key = (string)$settings['ougc_encodeurl_key'];
	}

	// Plugin API:_info() routine
	function _info()
	{
		global $lang;

		$this->load_language();

		return array(
			'name'			=> 'OUGC Encode URL',
			'description'	=> $lang->setting_group_ougc_encodeurl_desc,
			'website'		=> 'https://ougc.network',
			'author'		=> 'Omar G.',
			'authorsite'	=> 'https://ougc.network',
			'version'		=> '1.8.0',
			'versioncode'	=> 1800,
			'compatibility'	=> '18*',
			'codename'		=> 'ougc_encodeurl',
			'pl'			=> array(
				'version'	=> 13,
				'url'		=> 'https://community.mybb.com/mods.php?action=view&pid=573'
			)
		);
	}

	// Plugin API:_activate() routine
	function _activate()
	{
		global $PL, $lang, $mybb;
		$this->load_pluginlibrary();

		$PL->settings('ougc_encodeurl', $lang->setting_group_ougc_encodeurl, $lang->setting_group_ougc_encodeurl_desc, array(
			'forums'				=> array(
			   'title'			=> $lang->setting_ougc_encodeurl_forums,
			   'description'	=> $lang->setting_ougc_encodeurl_forums_desc,
			   'optionscode'	=> 'forumselect',
			   'value'			=> -1
			),
			'domains'				=> array(
			   'title'			=> $lang->setting_ougc_encodeurl_domains,
			   'description'	=> $lang->setting_ougc_encodeurl_domains_desc,
			   'optionscode'	=> 'textarea',
			   'value'			=> 'rapidgator.net'
			),
			'ignored'				=> array(
			   'title'			=> $lang->setting_ougc_encodeurl_ignored,
			   'description'	=> $lang->setting_ougc_encodeurl_ignored_desc,
			   'optionscode'	=> 'textarea',
			   'value'			=> (string)$_SERVER['SERVER_NAME']
			),
			'mybbredirect'				=> array(
			   'title'			=> $lang->setting_ougc_encodeurl_mybbredirect,
			   'description'	=> $lang->setting_ougc_encodeurl_mybbredirect_desc,
			   'optionscode'	=> 'yesno',
			   'value'			=> 1
			)
		));

		// Insert/update version into cache
		$plugins = $mybb->cache->read('ougc_plugins');
		if(!$plugins)
		{
			$plugins = array();
		}

		$this->load_plugin_info();

		if(!isset($plugins['encodeurl']))
		{
			$plugins['encodeurl'] = $this->plugin_info['versioncode'];
		}

		/*~*~* RUN UPDATES START *~*~*/

		/*~*~* RUN UPDATES END *~*~*/

		$plugins['encodeurl'] = $this->plugin_info['versioncode'];

		$mybb->cache->update('ougc_plugins', $plugins);
	}

	// Plugin API:_is_installed() routine
	function _is_installed()
	{
		global $cache;

		$plugins = (array)$cache->read('ougc_plugins');

		return isset($plugins['encodeurl']);
	}

	// Plugin API:_uninstall() routine
	function _uninstall()
	{
		global $PL, $cache;

		$this->load_pluginlibrary();

		// Delete settings
		$PL->settings_delete('ougc_encodeurl');

		// Delete version from cache
		$plugins = (array)$cache->read('ougc_plugins');

		if(isset($plugins['encodeurl']))
		{
			unset($plugins['encodeurl']);
		}

		if(!empty($plugins))
		{
			$cache->update('ougc_plugins', $plugins);
		}
		else
		{
			$cache->delete('ougc_plugins');
		}
	}

	// Load language file
	function load_language()
	{
		global $lang;

		isset($lang->setting_group_ougc_encodeurl) or $lang->load('ougc_encodeurl');
	}

	// Build plugin info
	function load_plugin_info()
	{
		$this->plugin_info = ougc_encodeurl_info();
	}

	// PluginLibrary requirement check
	function load_pluginlibrary()
	{
		global $lang;

		$this->load_plugin_info();
	
		$this->load_language();

		if($file_exists = file_exists(PLUGINLIBRARY))
		{
			global $PL;

			$PL or require_once PLUGINLIBRARY;
		}

		if(!$file_exists || $PL->version < $this->plugin_info['pl']['version'])
		{
			flash_message($lang->sprintf($lang->ougc_encodeurl_pluginlibrary, $this->plugin_info['pl']['ulr'], $this->plugin_info['pl']['version']), 'error');
			admin_redirect('index.php?module=config-plugins');
		}
	}

	// Hook: admin_config_settings_change
	function hook_parse_message(&$message)
	{
		global $mybb, $ougc_encodeurl_names, $post;

		static $hook = false;

		if(!$post['pid'] || !is_member($mybb->settings['ougc_encodeurl_forums'], array('usergroup' => $post['fid'])))
		{
			return;
		}
		elseif(!$hook)
		{
			global $plugins;

			$hook = true;

			$plugins->add_hook('parse_message_end', array($this, 'hook_parse_message_end'));
		}

		if(!is_array($ougc_encodeurl_names))
		{
			$ougc_encodeurl_names = array();
		}
	
		preg_match_all("/(\<a href=\")(.[^\"]*)/i", $message, $matches);

		if(!$matches || empty($matches[2]) || !is_array($matches[2]))
		{
			return;
		}

		$filtered = false;

		$post_urls = array();

		if(!empty($mybb->settings['ougc_encodeurl_domains']))
		{
			static $domains = null;

			if($domains === null)
			{
				$domains = explode(PHP_EOL, $mybb->settings['ougc_encodeurl_domains']);
				$domains = array_map('trim', $domains);
			}

			if(!empty($domains))
			{
				$filtered = true;

				foreach($domains as $domain)
				{
					foreach($matches[2] as $key => $url)
					{
						$parsed_url = parse_url($url);

						if(empty($parsed_url['host']))
						{
							continue;
						}

						if(my_strpos($parsed_url['host'], $domain) !== false)
						{
							$post_urls[$key] = $url;
						}
					}
				}
			}
		}

		if(!$filtered)
		{
			$post_urls = $matches[2];
		}

		if(!$filtered && !empty($mybb->settings['ougc_encodeurl_ignored']))
		{
			static $ignored = null;

			if($ignored === null)
			{
				$ignored = explode(PHP_EOL, $mybb->settings['ougc_encodeurl_ignored']);
				$ignored = array_map('trim', $ignored);
			}

			if(!empty($ignored))
			{
				foreach($ignored as $ignore)
				{
					foreach($post_urls as $key => $url)
					{
						$parsed_url = parse_url($url);

						if(empty($parsed_url['host']))
						{
							continue;
						}

						if(my_strpos($parsed_url['host'], $ignore) !== false)
						{
							unset($post_urls[$key]);
						}
					}
				}
			}
		}

		if(!$post_urls)
		{
			return;
		}

		$replaces = array();

		foreach($post_urls as $post_url)
		{
			$encoded_url = base64_encode($post_url);
			$encoded_url = $mybb->settings['bburl']."/misc.php?action=ougc_decodeurl&amp;url={$encoded_url}";

			$replaces[$post_url] = $encoded_url;

			$name = my_substr(htmlspecialchars_decode($post_url) , 0, 40).'...'.my_substr($post_url , -10);

			$ougc_encodeurl_names[$post['pid']][$name] = my_substr(htmlspecialchars_decode($encoded_url) , 0, 40).'...'.my_substr($encoded_url , -10);
		}

		if($replaces)
		{
			$message = str_replace(array_keys($replaces), array_values($replaces), $message);
		}
	}

	// Hook: contact_do_start
	function hook_parse_message_end(&$message)
	{
		global $ougc_encodeurl_names, $post;

		if(!$post['pid'] || empty($ougc_encodeurl_names[$post['pid']]))
		{
			return;
		}
	
		$message = str_replace(array_keys($ougc_encodeurl_names[$post['pid']]), array_values($ougc_encodeurl_names[$post['pid']]), $message);
	}

	// Hook: contact_do_start
	function hook_global_end(&$message)
	{
		global $mybb, $lang;

		if(!(defined('THIS_SCRIPT') && THIS_SCRIPT == 'misc.php' && $mybb->get_input('action') == 'ougc_decodeurl'))
		{
			return;
		}

		$url = base64_decode($mybb->get_input('url'));
	
		if(!my_validate_url($url))
		{
			error();
		}
	
		redirect($url);
	}
}

global $mybb, $ougc_encodeurl;

$ougc_encodeurl = new OUGC_EncodeURL;

if(defined('THIS_SCRIPT') && THIS_SCRIPT == 'misc.php' && $mybb->get_input('action') == 'ougc_decodeurl' && empty($mybb->settings['ougc_encodeurl_mybbredirect']))
{
	$url = base64_decode($mybb->get_input('url'));

	run_shutdown();

	if(my_validate_url($url))
	{
		header("Location: {$url}");
	}

	exit;
}