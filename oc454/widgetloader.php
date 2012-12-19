<?php
/**
 * ownCloud - Picture Widget
 *
 * @author Sebastian Doell
 * @copyright 2012 Sebastian Doell <sebastian.doell ad libasys dot de>
 *
 *
 */

$pathload= "apps/files_sharing_widget/js/widget.full.js";

header ("Content-type: text/javascript");
readfile($pathload);

?>