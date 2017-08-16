<?php
namespace exface\JEasyUiTemplate\Template\Elements;

use exface\Core\Templates\AbstractAjaxTemplate\Elements\JqueryToolbarTrait;

/**
 * The jEasyUI implementation of the Toolbar widget
 *
 * @author Andrej Kabachnik
 *        
 * @method Toolbar getWidget()
 */
class euiToolbar extends euiAbstractElement
{
    use JqueryToolbarTrait;
}
?>