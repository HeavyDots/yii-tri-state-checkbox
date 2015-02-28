<?php

/**
 * TODO:
 * - make it work on ajax pages
 */
class CbTriStateCheckbox extends CInputWidget
{
  
  public $label=null;
  public $linkHtmlOptions=array();
  public $hiddenFieldHtmlOptions=array();
  
  public $statesValues=array(
    '',
    '1',
    '0',
  );
  
  public $statesClasses=array(
    'three_state_cb_link-either',
    'three_state_cb_link-with',
    'three_state_cb_link-without',
  );

	public function run()
	{
		list($name,$id)=$this->resolveNameID();
		if(isset($this->htmlOptions['id']))
			$id=$this->htmlOptions['id'];
		else
			$this->htmlOptions['id']=$id;
		if(isset($this->htmlOptions['name']))
			$name=$this->htmlOptions['name'];
        
        if ($this->label == null) {
          if ($this->hasModel()) {
            $this->label=$this->model->getAttributeLabel($this->attribute);
          } else {
            $this->label=$this->name;
          }
        }
        
        $statesKey=  md5(serialize($this->statesValues).serialize($this->statesClasses));
        
        $linkHtmlOptions=$this->linkHtmlOptions;
        $linkHtmlOptions['class']='three_state_cb_link' . (isset($linkHtmlOptions['class']) ? ' '.$linkHtmlOptions['class']:'');
        $value= $this->hasModel() ? $this->model->{$this->attribute} : $this->value;
        $linkHtmlOptions['class'] .= " ".$this->getStateClassByValue($value);
        $linkHtmlOptions['class'] .= " ".$this->getLinkSelectorClass($statesKey);
        $linkHtmlOptions['onclick']=(isset($linkHtmlOptions['onclick']) ? $linkHtmlOptions['onclick']:'return false;');
        $linkHtmlOptions['data-for-tristate']=$this->htmlOptions['id'];
        
        echo CHtml::link($this->label, '#', $linkHtmlOptions);
        
        if ($this->hasModel()) {
          echo CHtml::activeHiddenField($this->model, $this->attribute, $this->hiddenFieldHtmlOptions);
        } else {
          echo CHtml::hiddenField($this->name, $this->value, $this->hiddenFieldHtmlOptions);
        }
        
        $this->registerClientScript($statesKey);

	}
    
    private function getLinkSelectorClass($statesKey) {
      return "cbTriState_{$statesKey}_link";
    }
    
    private function registerClientScript($statesKey) {
      $cs = Yii::app()->getClientScript();
		
      $assets = Yii::app()->getAssetManager()->publish(dirname(__FILE__).DIRECTORY_SEPARATOR.'assets');
      $cs->registerCssFile($assets.'/cbTriStateCheckbox.css');

      

      $js="
        var cbTriState_{$statesKey}_nextStateValues=".CJavaScript::encode($this->arrayNext($this->statesValues, $this->statesValues)).";
        var cbTriState_{$statesKey}_nextStateClasses=".CJavaScript::encode($this->arrayNext($this->statesClasses, $this->statesValues)).";
        $('.".$this->getLinkSelectorClass($statesKey)."').click(function () {

          var for_id=$(this).attr('data-for-tristate');
          var for_object=$('#'+for_id);
          var current_state=for_object.attr('value');
          var next_state=cbTriState_{$statesKey}_nextStateValues[current_state];
          var next_class=cbTriState_{$statesKey}_nextStateClasses[current_state];
          for_object.attr('value', next_state);

          $(this).removeClass('".  implode(" ", $this->statesClasses)."');
          $(this).addClass(next_class);

          console.log('Tristate click');
          console.log('for: '+for_id);
          console.log('current state: '+current_state);
          console.log('next state: '+next_state);
          console.log('next class: '+next_class);



        });
      ";        
      $cs->registerScript('CbTriStateCheckbox#'.$statesKey,$js);
    }
    
    private function arrayNext($array, $useKeys) {
      $result=array();
      foreach ($array as $k => $v) {
        $result[$useKeys[$k]]=isset($array[$k+1]) ? $array[$k+1] : $array[0];
      }
      return $result;
    }
    
    private function getStateClassByValue($value) {
      foreach ($this->statesValues as $k => $v) {
        if ($v==$value) {
          return $this->statesClasses[$k];
        }
      }
      return '';
    }
}
