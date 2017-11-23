<?php
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
//生成单选框
class lib_website_radio extends CI_Model{
	
	function __construct(){	
		parent::__construct();
		$this->load->model('M_common');
		$this->load->model('M_website_common_model','wcm');
		
	}
	
	function Create($modelid,$value=""){
		
		$model = $this->wcm->GetModel($modelid);
		$html = "";		

		if($model["inline"]>0){
			//换行
			$html.="";
		}
		else{
			$html .= '</div>
					<div class="row">
					';
		}		
		$span = ceil($model['cell_width']/23);//基数
		if($span>24){
			$span = 24;		
		}
		$html .= '<div class="control-group span'.$span.'">
            <label class="control-label">'.$model["title"].'：</label>
            <div class="controls">
              
            ';
		$field_value = $model["field_value"];
		$arr = explode(",",$field_value);
		foreach($arr as $k=>$v){
			$arr2 = explode("=",$v);
			if(count($arr2)>0){
				$html .= "<input type='radio' ";		
				$html.= "name='".$model["field"]."'";
				$html.= "id='".$model["field"]."_".$k."'";
				$html.= ' value="'.$arr2[1].'"';
				if($value!="" && $value==$arr2[1]){
					$html.="checked";
				}		
				if($model["isrequired"]>0){
					$html.=" required='true' ";
				}				
				$html.= "/>".$arr2[0]."&nbsp;";
			}
		}
		if($model["isrequired"]>0){
			$html.="*";
		}		
		$html.='</div>
          </div>';
		
		
		return $html;
	}
}
?>