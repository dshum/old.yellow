<?php namespace LemonTree;

class UserActionType {

	const ACTION_TYPE_ADD_ELEMENT_ID = 'add.element';
	const ACTION_TYPE_SAVE_ELEMENT_ID = 'save.element';
	const ACTION_TYPE_SAVE_ELEMENT_LIST_ID = 'save.element.list';
	const ACTION_TYPE_COPY_ELEMENT_ID = 'copy.element';
	const ACTION_TYPE_COPY_ELEMENT_LIST_ID = 'copy.element.list';
	const ACTION_TYPE_DROP_ELEMENT_TO_TRASH_ID = 'trash.element';
	const ACTION_TYPE_DROP_ELEMENT_LIST_TO_TRASH_ID = 'trash.element.list';
	const ACTION_TYPE_DROP_ELEMENT_ID = 'delete.element';
	const ACTION_TYPE_DROP_ELEMENT_LIST_ID = 'delete.element.list';
	const ACTION_TYPE_RESTORE_ELEMENT_ID = 'restore.element';
	const ACTION_TYPE_RESTORE_ELEMENT_LIST_ID = 'restore.element.list';
	const ACTION_TYPE_MOVE_ELEMENT_LIST_ID = 'move.element.list';
	const ACTION_TYPE_ORDER_ELEMENT_LIST_ID = 'order.element.list';
	const ACTION_TYPE_PLUGIN_ID = 'plugin';
	const ACTION_TYPE_PLUGIN_ACTION_ID = 'plugin.action';
	const ACTION_TYPE_SEARCH_ID = 'search';
	const ACTION_TYPE_ADD_GROUP_ID = 'add.group';
	const ACTION_TYPE_SAVE_GROUP_ID = 'save.group';
	const ACTION_TYPE_DROP_GROUP_ID = 'delete.group';
	const ACTION_TYPE_SAVE_ITEM_PERMISSIONS_ID = 'save.item.permissions';
	const ACTION_TYPE_SAVE_ELEMENT_PERMISSIONS_ID = 'save.element.permissions';
	const ACTION_TYPE_ADD_USER_ID = 'add.user';
	const ACTION_TYPE_SAVE_USER_ID = 'save.user';
	const ACTION_TYPE_DROP_USER_ID = 'delete.user';
	const ACTION_TYPE_SAVE_PROFILE_ID = 'save.profile';
	const ACTION_TYPE_LOGIN_ID = 'login';

	public static $actionTypeNameList = array(
		self::ACTION_TYPE_ADD_ELEMENT_ID => 'Добавление элемента',
		self::ACTION_TYPE_SAVE_ELEMENT_ID => 'Сохранение элемента',
		self::ACTION_TYPE_SAVE_ELEMENT_LIST_ID => 'Сохранение списка элементов',
		self::ACTION_TYPE_COPY_ELEMENT_ID => 'Копирование элемента',
		self::ACTION_TYPE_COPY_ELEMENT_LIST_ID => 'Копирование списка элементов',
		self::ACTION_TYPE_DROP_ELEMENT_TO_TRASH_ID => 'Удаление элемента в корзину',
		self::ACTION_TYPE_DROP_ELEMENT_LIST_TO_TRASH_ID => 'Удаление списка элементов в корзину',
		self::ACTION_TYPE_DROP_ELEMENT_ID => 'Удаление элемента',
		self::ACTION_TYPE_DROP_ELEMENT_LIST_ID => 'Удаление списка элементов',
		self::ACTION_TYPE_RESTORE_ELEMENT_ID => 'Восстановление элемента из корзины',
		self::ACTION_TYPE_RESTORE_ELEMENT_LIST_ID => 'Восстановление списка элементов из корзины',
		self::ACTION_TYPE_MOVE_ELEMENT_LIST_ID => 'Перемещение списка элементов',
		self::ACTION_TYPE_ORDER_ELEMENT_LIST_ID => 'Сортировка списка элементов',
		self::ACTION_TYPE_PLUGIN_ID => 'Плагин',
		self::ACTION_TYPE_PLUGIN_ACTION_ID => 'Плагин-экшн',
		self::ACTION_TYPE_SEARCH_ID => 'Поиск элементов',
		self::ACTION_TYPE_ADD_GROUP_ID => 'Добавление группы',
		self::ACTION_TYPE_SAVE_GROUP_ID => 'Сохранение группы',
		self::ACTION_TYPE_DROP_GROUP_ID => 'Удаление группы',
		self::ACTION_TYPE_SAVE_ITEM_PERMISSIONS_ID => 'Сохранение прав доступа по умолчанию',
		self::ACTION_TYPE_SAVE_ELEMENT_PERMISSIONS_ID => 'Сохранение прав доступа к элементам',
		self::ACTION_TYPE_ADD_USER_ID => 'Добавление пользователя',
		self::ACTION_TYPE_SAVE_USER_ID => 'Сохранение пользователя',
		self::ACTION_TYPE_DROP_USER_ID => 'Удаление пользователя',
		self::ACTION_TYPE_SAVE_PROFILE_ID => 'Сохранение профиля',
		self::ACTION_TYPE_LOGIN_ID => 'Авторизация',
	);

	public static function getActionTypeNameList()
	{
		return static::$actionTypeNameList;
	}

	public static function getActionTypeName($actionTypeId)
	{
		return
			isset(static::$actionTypeNameList[$actionTypeId])
			? static::$actionTypeNameList[$actionTypeId]
			: null;
	}

	public static function actionTypeExists($actionTypeId)
	{
		return isset(static::$actionTypeNameList[$actionTypeId]);
	}

}
