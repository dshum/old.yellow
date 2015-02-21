<?php namespace LemonTree\Controllers;

class PluginController extends Controller {

	public function getBrowsePlugin($classId)
	{
		$scope = array();

		$site = \App::make('site');

		$browsePlugin = $site->getBrowsePlugin($classId);

		$scope['plugin'] = $browsePlugin;

		return \Response::json($scope);
	}

}
