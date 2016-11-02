<?php
namespace exface\JEasyUiTemplate\Template\Elements;
class euiInputDate extends euiInput {
	
	protected function init(){
		parent::init();
		$this->set_element_type('datebox');
	}
	
	function generate_html(){
		/* @var $widget \exface\Core\Widgets\Input */
		$widget = $this->get_widget();
		$output = '	<input class="easyui-' . $this->get_element_type() . '" 
						style="height: 100%; width: 100%;"
						name="' . $widget->get_attribute_alias() . '"
						value="' . $this->escape_string($this->get_value_with_defaults()) . '"
						id="' . $this->get_id() . '"
						' . ($widget->is_required() ? 'required="true" ' : '') . '
						' . ($widget->is_disabled() ? 'disabled="disabled" ' : '') . '
						data-options="' . $this->build_js_data_options() . '" />
					';
		return $this->build_html_wrapper_div($output);
	}
	
	function generate_js(){
		return '';
	}
	
	protected function build_js_data_options(){
		return 'formatter:function(date){return date.toString(\'' . $this->build_js_date_format() . '\');}, parser:function(s){return Date.parse(s);}';
	}
	
	public function generate_headers(){
		$headers = parent::generate_headers();
		$headers[] = '<script type="text/javascript" src="exface/vendor/npm-asset/datejs/build/production/date.min.js"></script>';
		return $headers;
	}
	
	public function build_js_value_getter(){
		return "$('#" . $this->get_id() . "')." . $this->get_element_type() . "('getValue')";
	}
	
	protected function build_js_date_format(){
		return 'yyyy-MM-dd';
	}
}