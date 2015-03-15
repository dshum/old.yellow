<?php namespace LemonTree;

interface ElementInterface {

	public function getItem();

	public function getClass();

	public function getClassId();

	public function getProperty($name);

	public function equalTo($element);

	public function getAssetsName();

	public function getFolderName();

	public function getFolderHash();

	public function getFolder();

	public function getHref();

	public function getBrowseUrl();

	public function getBrowseUrlAddTab();

	public function getEditUrl();

	public function getEditUrlAddTab();

	public function getCopyUrl();

	public function getDeleteUrl();

	public function getTrashUrl();

	public function getRestoreUrl();

}
