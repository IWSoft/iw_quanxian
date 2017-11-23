<?php
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
//生成日期选框
class lib_website_datebox extends CI_Model{
	
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
		$html .= "<input type='text' ";
		$html .= " style='width:".$model["cell_width"]."px' ";
		$html.= "name='".$model["field"]."'";
		$html.= "id='".$model["field"]."'";
		if($value!=""){
			$html.=' value="'.($value>0?date("Y-m-d",$value):"").'"';
		}
		if($model["isrequired"]>0){
			$html.=" required='true' ";
		}			
		$html.= " onclick=\"laydate({istime: true, format: 'YYYY-MM-DD'})\"";	
		$html.= "/>";
		if($model["isrequired"]>0){
			$html.="*";
		}		
		$html.='</div>
          </div>';
		
		
		return $html;
	}
	
	function Create2($modelid,$value=""){
	
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
		$html .= "<input type='text' ";
		$html .= " style='width:".$model["cell_width"]."px' ";
		$html.= "name='".$model["field"]."'";
		$html.= "id='".$model["field"]."'";
		if($value!=""){
			$html.=' value="'.($value>0?date("Y-m-d H:i",$value):"").'"';
		}
		if($model["isrequired"]>0){
			$html.=" required='true' ";
		}
		$html.= " onclick=\"laydate({istime: true, format: 'YYYY-MM-DD hh:mm'})\"";
		$html.= "/>";
		if($model["isrequired"]>0){
			$html.="*";
		}
		$html.='</div>
          </div>';
	
	
		return $html;
	}	
}
?>