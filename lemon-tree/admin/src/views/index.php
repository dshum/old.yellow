<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Lemon Tree</title>
<link media="all" type="text/css" rel="stylesheet" href="<?=asset('packages/lemon-tree/admin/css/bootstrap.min.css')?>">
<link media="all" type="text/css" rel="stylesheet" href="<?=asset('packages/lemon-tree/admin/css/bootstrap-additions.min.css')?>">
<link media="all" type="text/css" rel="stylesheet" href="<?=asset('packages/lemon-tree/admin/css/glyphicons.css')?>">
<link media="all" type="text/css" rel="stylesheet" href="<?=asset('packages/lemon-tree/admin/css/glyphicons-halflings.css')?>">
<link media="all" type="text/css" rel="stylesheet" href="<?=asset('packages/lemon-tree/admin/css/glyphicons-bootstrap.css')?>">
<link media="all" type="text/css" rel="stylesheet" href="<?=asset('packages/lemon-tree/admin/css/default.css')?>">
<script src="<?=asset('packages/lemon-tree/admin/js/jquery.min.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/jquery.blockUI.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/bootstrap.min.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/angular.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/i18n/angular-locale_ru-ru.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/angular-animate.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/angular-ui-router.min.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/ui-bootstrap.min.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/angular-strap.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/angular-strap.tpl.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/tinymce/tinymce.min.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/app.js')?>"></script>

<script src="<?=asset('packages/lemon-tree/admin/js/components/helper/helper.js')?>"></script>

<link media="all" type="text/css" rel="stylesheet" href="<?=asset('packages/lemon-tree/admin/js/components/login/login.css')?>">
<script src="<?=asset('packages/lemon-tree/admin/js/components/login/login.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/login/login-service.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/login/login-controller.js')?>"></script>

<script src="<?=asset('packages/lemon-tree/admin/js/components/users/users.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/users/users-controller.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/users/group-users-controller.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/users/group-controller.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/users/element-permissions-controller.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/users/item-permissions-controller.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/users/user-controller.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/users/log-controller.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/users/profile-controller.js')?>"></script>

<script src="<?=asset('packages/lemon-tree/admin/js/components/favorites/favorites.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/favorites/favorites-controller.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/favorites/favorite-directive.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/favorites/favorite-service.js')?>"></script>

<script src="<?=asset('packages/lemon-tree/admin/js/components/ui-tinymce/ui-tinymce.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/ui-tinymce/ui-tinymce-directive.js')?>"></script>

<script src="<?=asset('packages/lemon-tree/admin/js/components/properties/property.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/properties/property-directive.js')?>"></script>

<script src="<?=asset('packages/lemon-tree/admin/js/components/browse/browse.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/browse/browse-controller.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/browse/search-controller.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/browse/trash-controller.js')?>"></script>

<script src="<?=asset('packages/lemon-tree/admin/js/components/edit/edit.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/edit/edit-controller.js')?>"></script>

<script src="<?=asset('packages/lemon-tree/admin/js/components/filemanager/filemanager.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/filemanager/filemanager-controller.js')?>"></script>

<script src="<?=asset('packages/lemon-tree/admin/js/components/navbar/navbar.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/navbar/navbar-controller.js')?>"></script>

<script src="<?=asset('packages/lemon-tree/admin/js/components/tree/tree.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/tree/tree-directive.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/tree/subtree-directive.js')?>"></script>

<script src="<?=asset('packages/lemon-tree/admin/js/components/modal/modal.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/modal/modal-instance-controller.js')?>"></script>

<script src="<?=asset('packages/lemon-tree/admin/js/components/context-menu/context-menu.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/context-menu/context-menu-directive.js')?>"></script>

<script src="<?=asset('packages/lemon-tree/admin/js/components/alert/alert.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/alert/alert-service.js')?>"></script>

<script src="<?=asset('packages/lemon-tree/admin/js/components/auth/auth.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/auth/auth-token-service.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/auth/auth-interceptor.js')?>"></script>

<script src="<?=asset('packages/lemon-tree/admin/js/components/form/form.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/form/form-interceptor.js')?>"></script>
<script src="<?=asset('packages/lemon-tree/admin/js/components/form/submit-on-directive.js')?>"></script>

<script src="<?=asset('packages/lemon-tree/admin/js/components/plugin/plugin.js')?>"></script>

<?php foreach ($pluginList as $plugin) :?>
<?php	if ( ! file_exists(public_path().'/js/plugins/'.$plugin.'.js')) continue;?>
<script src="<?=asset('js/plugins/'.$plugin.'.js')?>"></script>
<?php endforeach;?>
</head>
<body ng-app="adminApp" ui-view>
</body>
</html>