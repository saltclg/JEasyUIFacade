<?php
namespace exface\JEasyUIFacade\Facades\Elements;

class EuiInputText extends EuiInput
{

    /**
     * 
     * {@inheritDoc}
     * @see \exface\JEasyUIFacade\Facades\Elements\EuiInput::init()
     */
    protected function init()
    {
        parent::init();
        $this->setElementType('textbox');
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Facades\AbstractAjaxFacade\Elements\AbstractJqueryElement::buildCssHeightDefaultValue()
     */
    protected function buildCssHeightDefaultValue()
    {
        return ($this->getHeightRelativeUnit() * 2) . 'px';
    }

    function buildHtml()
    {
        $output = ' <textarea 
							name="' . $this->getWidget()->getAttributeAlias() . '" 
							id="' . $this->getId() . '"  
							style="height: calc(100% - 6px); width: calc(100% - 6px);"
							' . ($this->getWidget()->isRequired() ? 'required="true" ' : '') . '
							' . ($this->getWidget()->isDisabled() ? 'disabled="disabled" ' : '') . '>' . $this->getWidget()->getValue() . '</textarea>
					';
        return $this->buildHtmlLabelWrapper($output);
        ;
    }

    function buildJs()
    {
        $output = '';
        $output .= $this->buildJsLiveReference();
        $output .= $this->buildJsOnChangeHandler();
        return $output;
    }

    public function buildJsValueSetterMethod($value)
    {
        return 'val(' . $value . ').trigger("change")';
    }

    /*
     * function buildJsDataOptions(){
     * return parent::buildJsDataOptions() . ', multiline: true';
     * }
     *
     * function buildJsValueSetterMethod($value){
     * return $this->getElementType() . '("setText", ' . $value . ').trigger("change")';
     * }
     */
    
    /**
     *
     * {@inheritdoc}
     *
     * @see \exface\Core\Facades\AbstractAjaxFacade\Elements\AbstractJqueryElement::buildJsValidator()
     */
    function buildJsValidator()
    {
        return 'true';
    }
}