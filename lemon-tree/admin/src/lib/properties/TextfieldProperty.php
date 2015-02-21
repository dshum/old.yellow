<?php namespace LemonTree\Properties;

class TextfieldProperty extends BaseProperty {

	public static function create($name)
	{
		return new self($name);
	}

	public function getSearchView()
	{
		$scope = parent::getSearchView();

		$site = \App::make('site');

		$item = $this->getItem();

		$scope['relatedClass'] = $item->getNameId();

		return $scope;
	}

}
