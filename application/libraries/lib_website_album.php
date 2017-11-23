<?php
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
//生成相册
class lib_website_album extends CI_Model{
	
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
		////
		$html .= '<div class="control-group span'.$span.'">
            <label class="control-label">'.$model["title"].'：</label>
            <div class="controls">
              
            ';

		$html.='
				<link rel="stylesheet" type="text/css" href="'.base_url().'/'.APPPATH.'/views/static/Js/kindeditor/themes/default/default.css" />  
				<script type="text/javascript" src="'.base_url().'/'.APPPATH.'/views/static/Js/kindeditor/kindeditor-all-min.js"></script>
				<script type="text/javascript" src="'.base_url().'/'.APPPATH.'/views/static/Js/kindeditor/lang/zh_CN.js"></script>';		
		
		
		$html .= "
	    <input type='hidden' name='".$model["field"]."' id='".$model["field"]."' value='".$value."'/>
	    <input type='button' name='upload_btn' id='upload_btn' value='".$model["title"]."'/>
	     		
		<script>
	    KindEditor.ready(function(K) {  		

var editor=K.editor({
	allowFileManager:true,
	extraFileUploadParams: {
			'cookie': '".(isset($_COOKIE['admin_auth'])?$_COOKIE['admin_auth']:"")."'
	},	    		
	uploadJson:'".site_url("website_category/upload")."?action=upload&session=".$this->session->userdata('session_id')."'
	
});
			

K(\"#upload_btn\").click(
			function(){
				editor.loadPlugin('multiimage',
					function(){
					editor.plugin.multiImageDialog({
							clickFn:function(urlList){
					    var pic = document.getElementById(\"".$model["field"]."\").value;
						K.each(urlList,function(i,data){
							var tmp_url = data.url;
					    		data.url = tmp_url.replace(\"".base_url()."\",\"\");
							if(pic==\"\"){
								pic=\"{'pic':'\"+data.url+\"','beizhu':'','orderby':'50'}\";
							}
							else{
								pic+=\",{'pic':'\"+data.url+\"','beizhu':'','orderby':'50'}\";
							}
					    	pic = pic.replace(/\'/g,\"\\\"\");
							document.getElementById(\"".$model["field"]."\").value=pic;
						})
					editor.hideDialog();
			GetPicList();
							}
						})
					}			
				)
			});			
			
					
		});	
		function GetPicList(){
/*
			$.ajax(
			{
				url: url,
				async:false,
				dataType:'json',
				success:function(data)
				{}
				};
*/
			var pic = $('#".$model["field"]."').val();
			pic = eval('['+pic+']');
			pichtml = '';
			//排序
			pic.sort(function(x,y){
						return x['orderby']-y['orderby'];
					});
			for(var i=0;i<pic.length;i++){
				pichtml+='<div class=\"album_pic_list\">';
				pichtml+='<img src=\"'+pic[i].pic+'\" title=\"'+pic[i].beizhu+'\" />';				
				pichtml+='&nbsp;<span style=\"cursor:pointer;\" onclick=\"SetOrderBy(\''+pic[i].pic+'\')\">备注</span>';
				pichtml+='&nbsp;<span style=\"cursor:pointer;\" onclick=\"SetOrderBy(\''+pic[i].pic+'\')\">排序</span>';					
				pichtml+='  ';
				pichtml+='<span style=\"cursor:pointer;\" onclick=\"DelAlbumOnePic(\''+pic[i].pic+'\')\">删除</span>';	
				pichtml+='</div>';
			}
			$('#album_imgview').html(pichtml);
		}
		</script>";	
		
		$html.='</div>
          </div></div>';
		$html.= '<div class="row"><div class="controls control-row-auto">';
		$html.="<div id='album_imgview'></div>";
		$html.="</div></div>
	<script>
	GetPicList();
	function DelAlbumOnePic(picname){
					$.ajax(
					{
						url: \"".site_url("Website_category/AlbumDelPic")."\",
						type:\"post\",
						data:{pic:picname,picjson:$(\"#".$model["field"]."\").val()},
						async:false,
						dataType:'text',
						success:function(data)
						{															
							$(\"#".$model["field"]."\").val(data);
							GetPicList();
							parent.tip_show('图片删除成功，需要点击保存按钮，才生效。',1,1000);
						},
						error:function(a,b,c,d){
							alert(c);		
						}
					})
				}

	function SetOrderBy(picname){	
																		
		 BUI.use(['bui/overlay','bui/mask'],function(Overlay){					
		        var dialog = new Overlay.Dialog({
		            title:'修改图片信息',
		            width:600,
		            height:260,
					closeAction : 'destroy', //每次关闭dialog释放 非常 关键，释放缓存				
				    success : function(){		
								
						$.ajax(
						{
							url: \"".site_url("Website_category/AlbumSavePic")."?rnd=\"+Math.random(),
							data:{rnd:Math.random(),pic:$('#album_pic').val(),beizhu:$('#album_beizhu').val(),orderby:$('#album_orderby').val(),alljson:$('#".$model["field"]."').val()},		
							async:false,
							type:\"post\",
							dataType:'html',
							success:function(data){
								$('#".$model["field"]."').val(data);	
								GetPicList();							
							}
						});		                
		                this.close();		                
		             },										
		            loader : {
		              url : '".site_url("Website_category/AlbumSetPic")."',
		              autoLoad : false, //不自动加载
		              params : {rnd:Math.random(),pic : picname,alljson:$('#".$model["field"]."').val()},//附加的参数
		              lazyLoad :{event:'show',repeat:false}, //不延迟加载
 		              		
		              /*, //以下是默认选项
		              dataType : 'text',   //加载的数据类型
		              property : 'bodyContent', //将加载的内容设置到对应的属性
		              loadMask : {
		                //el , dialog 的body
		              },
		              lazyLoad : {
		                event : 'show', //显示的时候触发加载
		                repeat : true //是否重复加载
		              },
		              callback : function(text){
		                var loader = this,
		                target = loader.get('target'); //使用Loader的控件，此处是dialog
		              		alert('bb');
		                //
		              }
		              */
		            },
		            mask:false
		          });
		      dialog.show();
				
		    });									
	}
	</script>		
				";
		
		return $html;
	}
	
	//删除相册中的一张图并返回最新的图集JSON
	function DelPic(){
		$post = $this->input->post();
		//php json 键 值 必须用双引号才能识别到
		if(!empty($post['pic']) && !empty($post['picjson'])){
			$json = "[".$post['picjson']."]";
			$json=preg_replace('/.+?({.+}).+/','$1',$json);
			$json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t](//).*)#", '', $json);
			$json = "[".$json."]";						
			$json = json_decode($json,true);
			//return json_last_error();
			//return $json;			
			$delpic = str_replace(base_url(),"",$post['pic']);
			for($i=0;$i<count($json);$i++){							
				$json[$i]["pic"] = str_replace(base_url(),"",$json[$i]["pic"]);				
				if($delpic == $json[$i]["pic"]){
					//从索引位删除1个
					array_splice($json,$i,1);
					@unlink(realpath(".".$delpic));
					//删除小图
					$delpic_arr = explode(".",$delpic);
					@unlink(realpath(".".$delpic_arr[0]."_small".".".$delpic_arr[1]));
					//删除中图
					$delpic_arr = explode(".",$delpic);
					@unlink(realpath(".".$delpic_arr[0]."_mid".".".$delpic_arr[1]));					
					break;
				}
			}
			if(count($json)==0)
			{
				return "";
			}
			else{
				$json = json_encode($json);
				$json=preg_replace('/.+?({.+}).+/','$1',$json);
				$json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t](//).*)#", '', $json);			
				return $json;
			}			
			//return count($json);
		}
		else{
			return "err";
		}
	}
	
	//显示图片信息页面
	function ShowAlbumPicInfo($pic,$alljson){
		if($alljson!=""){		
			$json = json_decode("[".$alljson."]",true);		
			$val_beizhu="";
			$val_orderby=50;
			if(is_array($json)){
				foreach($json as $v){				
					if($v["pic"]==$pic){
						
						$val_beizhu=$v["beizhu"];
						$val_orderby=$v["orderby"];
						break;
					}
				}
			}
				
			$html = "";			
			$html.="备注：<br/><textarea style=\"width:100%; height:150px\" id=\"album_beizhu\" name=\"album_beizhu\">".$val_beizhu."</textarea>";		
			$html.="<br/><br/>排序（从小到大）：<select name='album_orderby' id='album_orderby'>";
			for($i=1;$i<=100;$i++){
				$html.="<option value='$i'
				".($i==$val_orderby?"selected":"")."
				>".$i."</option>";
			}		
			$html.="<input type='hidden' name='album_pic' id='album_pic' value='".$pic."'/>";
				
			return $html;
		}
		else{
			return "err";			
		}
	}
	
	function SaveAlbumPicInfo($pic,$beizhu,$orderby,$alljson){		
		$json=json_decode("[".$alljson."]",true);		
		for($i=0;$i<count($json);$i++){
			if($json[$i]["pic"]==$pic){					
				$json[$i]["beizhu"] = $beizhu;
				$json[$i]["orderby"] = $orderby;
				break;
			}
		}
		
		$json = json_encode($json);
		$json=preg_replace('/.+?({.+}).+/','$1',$json);
		$json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t](//).*)#", '', $json);		
		return $json;
	}


}
?>