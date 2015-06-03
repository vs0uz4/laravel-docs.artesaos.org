<?php namespace App;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Cache\Repository as Cache;

class Documentation {

	/**
	 * The filesystem implementation.
	 *
	 * @var Filesystem
	 */
	protected $files;

	/**
	 * The cache implementation.
	 *
	 * @var Cache
	 */
	protected $cache;

	/**
	 * Create a new documentation instance.
	 *
	 * @param  Filesystem  $files
	 * @param  Cache  $cache
	 * @return void
	 */
	public function __construct(Filesystem $files, Cache $cache)
	{
		$this->files = $files;
		$this->cache = $cache;
	}

	/**
	 * Get the documentation index page.
	 *
	 * @param  string  $version
	 * @return string
	 */
	public function getIndex($version, $lang="pt_BR")
	{
		return $this->cache->remember('docs.'.$version.'.index', 5, function() use ($version, $lang) {
			$path = base_path('resources/docs/'.$version.'/'.$lang.'/documentation.md');

			if ($this->files->exists($path)) {
				return $this->replaceLinks($version, markdown($this->files->get($path)));
			}

			return null;
		});
	}

	/**
	 * Get the given documentation page.
	 *
	 * @param  string  $version
	 * @param  string  $page
	 * @return string
	 */
	public function get($version, $page, $lang='pt_BR')
	{
		return $this->cache->remember('docs.'.$version.'.'.$page, 5, function() use ($version, $page, $lang) {
			$path = base_path('resources/docs/'.$version.'/'.$lang.'/'.$page.'.md');

			if ($this->files->exists($path)) {
				return $this->replaceLinks($version, markdown($this->files->get($path)));
			}

			return null;
		});
	}

	/**
	 * Replace the version place-holder in links.
	 *
	 * @param  string  $version
	 * @param  string  $content
	 * @return string
	 */
	protected function replaceLinks($version, $content)
	{
		return str_replace('{{version}}', $version, $content);
	}

	/**
	 * Check if the given section exists.
	 *
	 * @param  string  $version
	 * @param  string  $page
	 * @return boolean
	 */
	public function sectionExists($version, $page, $lang='pt_BR')
	{
		return $this->files->exists(
			base_path('resources/docs/'.$version.'/'.$lang.'/'.$page.'.md')
		);
	}

}
