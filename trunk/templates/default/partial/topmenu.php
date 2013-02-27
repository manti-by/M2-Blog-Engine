<?php
    defined('M2_MICRO') or die('Direct Access to this location is not allowed.');

    /**
     * Top menu plugin
     * @name $topmenu
     * @package M2 Micro Framework
     * @subpackage Template
     * @author Alexander Chaika
     * @since 0.1
     */

    $user = UserEntity::getInstance();
    $username = $user->getUsername();
    $salutation = !empty($username) ? $username : $user->getEmail();
?>
<?php if ($user->isLoggined()) : ?>
<div id="usermenu">
    <?php echo T('Hi') . ' ' . $salutation; ?>!
    <a href="<?php echo Sef::getSef('index.php?module=user&action=dashboard');?>"><?php echo T('Dashboard'); ?></a> |
    <a href="<?php echo Sef::getSef('index.php?module=user&action=logout'); ?>"><?php echo T('Sign Out'); ?></a>
</div>
<?php endif; ?>

<a href="<?php echo Application::$config['http_host']; ?>" class="fl">
    <img src="<?php echo Application::$config['template_image_url_path']; ?>logo.png" alt="Manti Logo" width="114" height="56" />
</a>
<ul id="topmenu" class="fl">
    <li><a href="<?php echo Application::$config['http_host']; ?>"><?php echo T('Home'); ?></a></li>
    <li><a href="<?php echo Sef::getSef('index.php?module=blog'); ?>"><?php echo T('Blog'); ?></a></li>
    <li><a href="<?php echo Sef::getSef('index.php?module=gallery'); ?>"><?php echo T('Gallery'); ?></a></li>
    <li><a href="<?php echo Sef::getSef('index.php?module=file'); ?>"><?php echo T('Download'); ?></a></li>
    <li><a href="<?php echo Sef::getSef('index.php?module=blog&action=show&id=4'); ?>"><?php echo T('About'); ?></a></li>
</ul>