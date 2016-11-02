<?php
namespace exface\JEasyUiTemplate\Template\Elements;
class euiInputNumber extends euiInput {
	
	protected function init(){
		parent::init();
		$this->set_element_type('numberbox');
	}
	
	protected function build_js_data_options(){
		$output = parent::build_js_data_options();
		if ($output){
			$output .= ', ';
		}
		
		$output .= "precision: '" . $this->get_widget()->get_precision() . "'
					, decimalSeparator: ','
				";
		return $output;
	}
}