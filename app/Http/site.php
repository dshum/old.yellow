<?php namespace LemonTree;

use LemonTree\Properties\BaseProperty;
use LemonTree\Properties\CheckboxProperty;
use LemonTree\Properties\DatetimeProperty;
use LemonTree\Properties\FloatProperty;
use LemonTree\Properties\ImageProperty;
use LemonTree\Properties\IntegerProperty;
use LemonTree\Properties\OnetoOneProperty;
use LemonTree\Properties\RichtextProperty;
use LemonTree\Properties\TextareaProperty;
use LemonTree\Properties\TextfieldProperty;

$site = \App::make('site');

$site->

	/*
	 * Категория товаров
	 */

	addItem(
		Item::create('App\Category')->
		setTitle('Категория товаров')->
		setMainProperty('name')->
		setRoot(true)->
		addOrder()->
		addProperty(
			TextfieldProperty::create('name')->
			setTitle('Название')->
			setRequired(true)->
			setShow(true)
		)->
		addProperty(
			ImageProperty::create('image')->
			setTitle('Изображение')->
			setResize(205, 139, 80)->
			setShow(true)
		)->
		addProperty(
			TextfieldProperty::create('url')->
			setTitle('URL')->
			setRequired(true)->
			addRule('regex:/^[a-z0-9\-]+$/i', 'Допускаются латинские буквы, цифры и дефис')
		)->
		addProperty(
			TextfieldProperty::create('title')->
			setTitle('Title')
		)->
		addProperty(
			TextareaProperty::create('shortcontent')->
			setTitle('Краткое описание')->
			setShow(true)->
			setEditable(true)
		)->
		addProperty(
			RichtextProperty::create('fullcontent')->
			setTitle('Полное описание')
		)->
		addProperty(
			CheckboxProperty::create('hide')->
			setTitle('Скрыть')->
			setShow(true)->
			setEditable(true)
		)->
		addTimestamps()->
		addSoftDeletes()
	)->

	/*
	 * Подкатегория товаров
	 */

	addItem(
		Item::create('App\Subcategory')->
		setTitle('Подкатегория товаров')->
		setMainProperty('name')->
		addOrder()->
		addProperty(
			TextfieldProperty::create('name')->
			setTitle('Название')->
			setRequired(true)->
			setShow(true)
		)->
		addProperty(
			TextfieldProperty::create('url')->
			setTitle('URL')->
			setRequired(true)->
			addRule('regex:/^[a-z0-9\-]+$/i', 'Допускаются латинские буквы, цифры и дефис')
		)->
		addProperty(
			TextfieldProperty::create('title')->
			setTitle('Title')
		)->
		addProperty(
			RichtextProperty::create('fullcontent')->
			setTitle('Полное описание')
		)->
		addProperty(
			CheckboxProperty::create('hide')->
			setTitle('Скрыть')->
			setShow(true)->
			setEditable(true)
		)->
		addProperty(
			OneToOneProperty::create('category_id')->
			setTitle('Категория товаров')->
			setRelatedClass('App\Category')->
			setParent(true)->
			setRequired(true)->
			bind('Category')
		)->
		addTimestamps()->
		addSoftDeletes()
	)->

	/*
	 * Товар
	 */

	addItem(
		Item::create('App\Good')->
		setTitle('Товар')->
		setMainProperty('name')->
		addOrder()->
		addProperty(
			TextfieldProperty::create('name')->
			setTitle('Название')->
			setRequired(true)->
			setShow(true)
		)->
		addProperty(
			TextfieldProperty::create('url')->
			setTitle('URL')->
			setRequired(true)->
			addRule('regex:/^[a-z0-9\-]+$/i', 'Допускаются латинские буквы, цифры и дефис')
		)->
		addProperty(
			ImageProperty::create('image')->
			setTitle('Изображение')->
			setResize(300, 350, 80)->
			addResize('spec', 150, 200, 80)->
			addResize('other', 100, 100, 80)
		)->
		addProperty(
			FloatProperty::create('supplier_price')->
			setTitle('Цена поставщика')->
			setRequired(true)->
			setShow(true)->
			setEditable(true)
		)->
		addProperty(
			FloatProperty::create('price')->
			setTitle('Цена')->
			setRequired(true)->
			setShow(true)->
			setEditable(true)
		)->
		addProperty(
			FloatProperty::create('old_price')->
			setTitle('Старая цена')
		)->
		addProperty(
			TextfieldProperty::create('title')->
			setTitle('Title')
		)->
		addProperty(
			TextfieldProperty::create('meta_keywords')->
			setTitle('META Keywords')
		)->
		addProperty(
			TextareaProperty::create('meta_description')->
			setTitle('META Description')
		)->
		addProperty(
			TextareaProperty::create('shortcontent')->
			setTitle('Краткое описание')
		)->
		addProperty(
			RichtextProperty::create('fullcontent')->
			setTitle('Полное описание')
		)->
		addProperty(
			CheckboxProperty::create('special')->
			setTitle('Спецпредложение')
		)->
		addProperty(
			CheckboxProperty::create('novelty')->
			setTitle('Новинка')
		)->
		addProperty(
			CheckboxProperty::create('hide')->
			setTitle('Скрыть')->
			setShow(true)->
			setEditable(true)
		)->
		addProperty(
			CheckboxProperty::create('absent')->
			setTitle('Нет в наличии')->
			setShow(true)->
			setEditable(true)
		)->
		addProperty(
			OneToOneProperty::create('category_id')->
			setTitle('Категория товара')->
			setRelatedClass('App\Category')->
			setRequired(true)->
			setParent(true)->
			bind('Category')
		)->
		addProperty(
			OneToOneProperty::create('subcategory_id')->
			setTitle('Подкатегория товара')->
			setRelatedClass('App\Subcategory')->
			bind('Category')->
			bind('Category', 'Subcategory')
		)->
		addTimestamps()->
		addSoftDeletes()
	)->

	/*
	 * Раздел сайта
	 */

	addItem(
		Item::create('App\Section')->
		setTitle('Раздел сайта')->
		setMainProperty('name')->
		setRoot(true)->
		setElementPermissions(true)->
		addOrder()->
		addProperty(
			TextfieldProperty::create('name')->
			setTitle('Название')->
			setRequired(true)->
			setShow(true)
		)->
		addProperty(
			TextfieldProperty::create('url')->
			setTitle('Адрес страницы')->
			addRule('regex:/^[a-z0-9\-]+$/i', 'Допускаются латинские буквы, цифры и дефис')
		)->
		addProperty(
			TextfieldProperty::create('title')->
			setTitle('Title')
		)->
		addProperty(
			TextfieldProperty::create('h1')->
			setTitle('H1')
		)->
		addProperty(
			TextfieldProperty::create('meta_keywords')->
			setTitle('META Keywords')
		)->
		addProperty(
			TextareaProperty::create('meta_description')->
			setTitle('META Description')
		)->
		addProperty(
			TextareaProperty::create('shortcontent')->
			setTitle('Краткий текст')
		)->
		addProperty(
			RichtextProperty::create('fullcontent')->
			setTitle('Текст раздела')
		)->
		addProperty(
			OneToOneProperty::create('section_id')->
			setTitle('Раздел сайта')->
			setRelatedClass('App\Section')->
			setParent(true)->
			bind(Site::ROOT, 'Section')->
			bind('Section', 'Section')
		)->
		addTimestamps()->
		addSoftDeletes()
	)->

	/*
	 * Служебный раздел
	 */

	addItem(
		Item::create('App\ServiceSection')->
		setTitle('Служебный раздел')->
		setMainProperty('name')->
		setRoot(true)->
		setElementPermissions(true)->
		addOrder()->
		addProperty(
			TextfieldProperty::create('name')->
			setTitle('Название')->
			setRequired(true)->
			setShow(true)
		)->
		addProperty(
			OneToOneProperty::create('service_section_id')->
			setTitle('Служебный раздел')->
			setRelatedClass('App\ServiceSection')->
			setParent(true)->
			bind(Site::ROOT, 'App\ServiceSection')->
			bind('App\ServiceSection', 'App\ServiceSection')
		)->
		addTimestamps()->
		addSoftDeletes()
	)->

	/*
	 * Настройки сайта
	 */

	addItem(
		Item::create('App\SiteSettings')->
		setTitle('Настройки сайта')->
		setMainProperty('name')->
		setRoot(true)->
		addProperty(
			TextfieldProperty::create('name')->
			setTitle('Название')->
			setRequired(true)->
			setShow(true)
		)->
		addProperty(
			TextfieldProperty::create('title')->
			setTitle('Title')->
			setRequired(true)
		)->
		addProperty(
			TextfieldProperty::create('h1')->
			setTitle('H1')
		)->
		addProperty(
			TextareaProperty::create('description')->
			setTitle('META Description')
		)->
		addProperty(
			TextfieldProperty::create('keywords')->
			setTitle('META Keywords')
		)->
		addProperty(
			RichtextProperty::create('text')->
			setTitle('Текст')
		)->
		addProperty(
			TextfieldProperty::create('copyright')->
			setTitle('Copyright')
		)->
		addTimestamps()->
		addSoftDeletes()
	)->

	bind(Site::ROOT, 'App\Category', 'App\Section', 'App\ServiceSection')->
	bind('App\Category', 'App\Subcategory', 'App\Good')->
	bind('App\Subcategory', 'App\Good')->
	bind('App.ServiceSection.1', 'App\ServiceSection')->
	bind('App.ServiceSection.6', 'App\ServiceSection')->
	bind('App.ServiceSection.7', 'App\ServiceSection')->

	bindTree(Site::ROOT, 'App\Category', 'App\Section', 'App\ServiceSection', 'App\SiteSettings')->
	bindTree('App\Category', 'App\Subcategory', 'App\Good')->
	bindTree('App\Subcategory', 'App\Good')->
	bindTree('App\Section', 'App\Section')->
	bindTree('App.ServiceSection.1', 'App\ServiceSection')->
	bindTree('App.ServiceSection.6', 'App\ServiceSection')->
	bindTree('App.ServiceSection.7', 'App\ServiceSection')->

	bindBrowsePlugin('App.ServiceSection.8', 'moneyStat')->
	bindSearchPlugin('App\Good', 'goodSearch')->
	bindEditPlugin('App\ServiceSection', 'moneyStat2')->
	bindBrowseFilter('App\Good', 'goodFilter')->

	end();
