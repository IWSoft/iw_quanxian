<?php
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
//生成文本框
class lib_website_editor extends CI_Model{
	
	public $upload_path = '';
	public $upload_save_url = '';
	public $upload_path_sys = '';	
	
	function __construct(){	
		parent::__construct();
		$this->load->model('M_common');
		$this->load->model('M_website_common_model','wcm');
		
		$this->upload_path = __ROOT__."/data/upload/editor/" ; ; // 编辑器上传的文件保存的位置
		$this->upload_save_url = base_url()."/data/upload/editor/"; //编辑器上传图片的访问的路径			
		
	}
	
	function Create($modelid,$value="",$session_id=""){
		
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
		$span = 25;//ceil($model['cell_width']/23);//基数
		if($span>24){
			$span = 24;		
		}
		$html .= '<div class="control-group span'.$span.'" style="width:1100px;">
            <label class="control-label">'.$model["title"].'：</label>
            <div class="controls control-row-auto" style="width:900px">
              
            ';
		//$model["cell_width"] 对文本编辑器无效
		if($model["isrequired"]>0){
			
		}				
		
		
		
		/*
		 * 生成编辑器
		 * 
		 */
		$html.='<script type="text/javascript" src="'.base_url().'/'.APPPATH.'/views/static/Js/kindeditor/kindeditor-all-min.js"></script>
<script type="text/javascript" src="'.base_url().'/'.APPPATH.'/views/static/Js/kindeditor/lang/zh_CN.js"></script>';		
		$html.="<textarea style=\"width:100%; height:150px\" id=\"".$model["field"]."\" name=\"".$model["field"]."\" placeholder=\"描述\">".$value."</textarea>";
		
		
		$html.="<script>";
		$html.="KindEditor.ready(function(K) {
			window.editor = K.create('#".$model["field"]."', {
				width: '100%',
				height: '600px',
				allowFileManager: false,
				allowUpload: false,
				afterCreate: function() {
					this.sync();
				},
				afterBlur: function() {
					this.sync();
				},
				extraFileUploadParams: {
					'cookie': '".(isset($_COOKIE['admin_auth'])?$_COOKIE['admin_auth']:"")."'
				},
				uploadJson: \"".site_url("website_category/upload")."?action=upload&session_id=".$session_id."\"
		
			});
		});
		
			</script>";		
		
		
		$html.='</div>
          </div>';
		
		
		return $html;
	}
	
	//上传文件
	function upload(){
		//包含kindeditor的上传文件
		$save_path =$this->upload_path ; // 编辑器上传的文件保存的位置
		$save_url = $this->upload_save_url; //访问的路径
		include_once __ROOT__.'/'.APPPATH."libraries/JSON.php" ;
		include_once __ROOT__.'/'.APPPATH."libraries/upload_json.php";
	}	
}
?>