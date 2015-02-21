<?php namespace LemonTree\Controllers;

class HomeController extends Controller {

	public function getIndex()
	{
		$scope = array();

		$site = \App::make('site');

		$pluginList = array();

		$browsePluginList = $site->getBrowsePlugins();
		$browseFilterList = $site->getBrowseFilters();
		$searchPluginList = $site->getSearchPlugins();
		$editPluginList = $site->getEditPlugins();

		foreach ($browsePluginList as $browsePlugin) {
			$pluginList[] = $browsePlugin;
		}

		foreach ($browseFilterList as $browseFilter) {
			$pluginList[] = $browseFilter;
		}

		foreach ($searchPluginList as $searchPlugin) {
			$pluginList[] = $searchPlugin;
		}

		foreach ($editPluginList as $editPlugin) {
			$pluginList[] = $editPlugin;
		}

		$scope['pluginList'] = $pluginList;

		return \View::make('admin::index', $scope);
	}

}
