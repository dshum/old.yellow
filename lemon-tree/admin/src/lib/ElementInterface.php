<?php namespace LemonTree;

interface ElementInterface {

	public static function getCacheKey($id);

	public static function getByClassId($classId);

	public static function getWithTrashedByClassId($classId);

	public static function getOnlyTrashedByClassId($classId);

	public function getItem();

	public function getClass();

	public function getClassId();

	public function getProperty($name);

	public function equalTo($element);

	public function getAssetsName();

	public function getFolderName();

	public function getFolderHash();

	public function getFolder();

	public function setParent($parent);

	public function getParent();

	public function getParentList();

	public function getChildItemList();

	public function getHref();

	public function getBrowseUrl();

	public function getBrowseUrlAddTab();

	public function getEditUrl();

	public function getEditUrlAddTab();

	public function getCopyUrl();

	public function getDeleteUrl();

	public function getTrashUrl();

	public function getRestoreUrl();

	public function deleteFromTrash();

	public function copy();

}
