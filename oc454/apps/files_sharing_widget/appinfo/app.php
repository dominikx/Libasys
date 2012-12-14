<?php
/**
 * ownCloud - Picture Widget
 *
 * @author Sebastian Doell
 * @copyright 2012 Sebastian Doell <sebastian.doell ad libasys dot de>
 *
 *
 */
 
OCP\App::registerPersonal('files_sharing_widget', 'settings');
OCP\App::registerAdmin('files_sharing_widget', 'admin');
OCP\Util::addscript( 'files_sharing_widget', 'settings');
OCP\Util::addscript( 'files_sharing_widget', 'admin');
OC::$CLASSPATH['OC_Widget_Helper'] = 'apps/files_sharing_widget/lib/widgethelper.php';