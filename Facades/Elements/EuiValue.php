<?php
namespace exface\JEasyUIFacade\Facades\Elements;

use exface\Core\Interfaces\Widgets\iTakeInput;
use exface\Core\Widgets\Value;

/**
 * Generates a <div> element for a Value widget and wraps it in a masonry grid item if needed.
 * 
 * @method Value getWidget()
 * 
 * @author Andrej Kabachnik
 *
 */
class EuiValue extends EuiAbstractElement
{
    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Facades\AbstractAjaxFacade\Elements\AbstractJqueryElement::init()
     */
    protected function init()
    {
        parent::init();
        $this->setElementType($this->getCaption() ? 'span' : 'p');
        return;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Facades\AbstractAjaxFacade\Elements\AbstractJqueryElement::buildHtml()
     */
    public function buildHtml()
    {
        $value = nl2br($this->getWidget()->getValue());
        
        $output = <<<HTML

        <div id="{$this->getId()}" class="exf-value {$this->buildCssElementClass()}">{$value}</div>

HTML;
        return $this->buildHtmlGridItemWrapper($output);
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \exface\Core\Facades\AbstractAjaxFacade\Elements\AbstractJqueryElement::buildCssHeightDefaultValue()
     */
    protected function buildCssHeightDefaultValue()
    {
        return 'auto';
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Facades\AbstractAjaxFacade\Elements\AbstractJqueryElement::getCaption()
     */
    protected function getCaption() : string
    {
        $caption = parent::getCaption();
        return  $caption . ($caption !== '' && ($this->getWidget() instanceof iTakeInput) === false ?  ':' : '');
    }
    
    /**
     * Adds a <label> tag to the given HTML code and wraps it in a masonry grid item if needed.
     * 
     * Set $make_grid_item to FALSE to disable wrapping in a grid item <div> - this way the
     * grid item can be generated in a custom way. Wrapping every label-control pair by default
     * is just a convenience function, so every facade element just needs to call one single
     * wrapper by default.
     * 
     * @param string $html
     * @param boolean $make_grid_item
     * 
     * @return string
     */
    protected function buildHtmlLabelWrapper($html, $make_grid_item = true)
    {
        if ($caption = $this->getCaption()) {
            $html = '
						<label>' . $caption . '</label>
						<div class="exf-labeled-item">' . $html . '</div>';
        }
        
        if ($make_grid_item) {
            $html = $this->buildHtmlGridItemWrapper($html, $this->getTooltip());
        }
        
        return $html;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Facades\AbstractAjaxFacade\Elements\AbstractJqueryElement::buildCssElementClass()
     */
    public function buildCssElementClass()
    {
        return parent::buildCssElementClass() . ' exf-control';
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Facades\AbstractAjaxFacade\Elements\AbstractJqueryElement::buildJs()
     */
    public function buildJs()
    {
        return '';
    }
}
?>