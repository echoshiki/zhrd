$(document).ready(function(){

//示例
	$('#id').click(function(){
	//点击
	});
	$('#id').blur(function(){
	//失去焦点
	});
	$('#id').dblclick(function(){
	//双击
	});
	$('#id').keydown(function(){
	//按键后触发
	});


//行业选择
	$('#trade_81').change(function(){

		if ($('#trade_81').val() == '132') {
			html='<input type="text" name="form[TRADE_I]" value="">';
			$('#trade_81').after(html);			
		};	



		if ($('#trade_81').val() == '130' ||$('#trade_81').val() == '131' ) {
			    art.dialog({lock:true,content:getC(site_url+'apply/index/msg_1')},
						function(){
							var t = $('#TEL').val();
							$('#TEL').nextAll().remove();
							html='<input type="hidden" name="form[TEL]" value="'+t+'" />';	
							$('#table_1').after(html);
							if (t.length == '11') { 
								$('#form_1').submit();
							} else {
								html = '<span style="color:red">请输入真实的11位手机号码。</span>'
								$('#TEL').after(html);
								return false;
							};							
						});		
		};				
	});

//销售收入判断是否弹窗
	$('#sale_last').blur(function(){
		var num = $('#sale_last').val();
		if (num > 100000) {
			    art.dialog({lock:true,content:getC(site_url+'apply/index/msg_1')},
						function(){
							var t = $('#TEL').val();
							$('#TEL').nextAll().remove();
							html='<input type="hidden" name="form[TEL]" value="'+t+'" />';	
							$('#table_1').after(html);
							if (t.length == '11') { 
								$('#form_1').submit();
							} else {
								html = '<span style="color:red">请输入真实的11位手机号码。</span>'
								$('#TEL').after(html);
								return false;
							};							
						});				
		};


	});

/*
//关键项不为空提示
	$('#company').blur(function(){
		$('#company').nextAll().remove();
		if($('#company').val() == ''){
			html = '<span style="color:red">请填写您的公司名称。</span>'
			$('#company').after(html);
		}
	});
	
	$('#licence').blur(function(){
		$('#licence').nextAll().remove();
		if($('#licence').val() == ''){
			html = '<span style="color:red">请填写您的营业执照编号。</span>'
			$('#licence').after(html);
		}
	});

	$('#sale_last').blur(function(){
		$('#sale_last').nextAll().remove();
		if($('#sale_last').val() == ''){
			html = '<span style="color:red">请填写您上年度销售收入。</span>'
			$('#sale_last').after(html);
		}
	});

	$('#demand').blur(function(){
		$('#demand').nextAll().remove();
		if($('#demand').val() == ''){
			html = '<span style="color:red">请填写您的授信需求。</span>'
			$('#demand').after(html);
		}
	});
*/


	$('#sale_last').click(function(){
		$.get(site_url+"apply/index/ts/",function(data){
			
		});
	});


	// $('#trade_26').change(function(){	
		// var id = $('#trade_26').val();
		// $.get(site_url+"apply/index/inputview/"+id,function(data,status){
			// $("#insert_place").html(data);
		// });

	// });

	$('#btn_3').click(function(){
		$('#form_1').submit();
	});


	$('#warrant_134').live("change",function(){
		var html = '';
		if ($('#warrant_134').val() == '135'){
			html = "<br />&nbsp;&nbsp;&nbsp;&nbsp;市值：<input  type='text' name='warrant[price]' />(万元)<br />取得原值：<input  type='text' name='warrant[price3]' />(万元)";
			$('#war_insert').html(html);
		}
		if ($('#warrant_134').val() == '136'){
			html = '<br/>'+warrant_com;
			$('#war_insert').html(html);
		}
		if ($('#warrant_134').val() == '137'){
			html = "<br/>市值：<input  type='text' name='warrant[price]' />(万元)";
			$('#war_insert').html(html);
		}
		if ($('#warrant_134').val() == '138'){
			html = "<br />第三方销售收入：<input  type='text' name='warrant[price2]' />(万元)<br />&nbsp;&nbsp;对外担保情况：<input type='text' name='warrant[about]'/>";
			$('#war_insert').html(html);
		}
		if ($('#warrant_134').val() == '139'){
			html = "<br />质押物：<input type='text' name='warrant[good]'/>&nbsp;市值：<input  type='text' name='warrant[price]' />(万元)</p>";
			$('#war_insert').html(html);
		}
		if ($('#warrant_134').val() == '229'){
			html = "";
			$('#war_insert').html(html);
		}

	});

	$('#warrant2_134').live("change",function(){
		var html = '';
		if ($('#warrant2_134').val() == '135'){
			html = "<br/>&nbsp;&nbsp;&nbsp;&nbsp;市值：<input  type='text' name='warrant2[price]' />(万元)<br/>取得原值：<input  type='text' name='warrant2[price3]' />(万元)";
			$('#war2_insert').html(html);
		}
		if ($('#warrant2_134').val() == '136'){
			html = '<br/>'+warrant2_com;
			$('#war2_insert').html(html);
		}
		if ($('#warrant2_134').val() == '137'){
			html = "<br/>市值：<input  type='text' name='warrant2[price]' />(万元)";
			$('#war2_insert').html(html);
		}
		if ($('#warrant2_134').val() == '138'){
			html = "<br/>第三方销售收入：<input  type='text' name='warrant2[price2]' />(万元)<br />&nbsp;&nbsp;对外担保情况：<input type='text' name='warrant2[about]'/>";
			$('#war2_insert').html(html);
		}
		if ($('#warrant2_134').val() == '139'){
			html = "<br/>质押物：<input type='text' name='warrant2[good]'/>&nbsp;市值：<input  type='text' name='warrant2[price]' />(万元)";
			$('#war2_insert').html(html);
		}
		if ($('#warrant2_134').val() == '229'){
			html = "";
			$('#war2_insert').html(html);
		}
	});

	$('#warrant3_134').live("change",function(){
		var html = '';
		if ($('#warrant3_134').val() == '135'){
			html = "<br/>&nbsp;&nbsp;&nbsp;&nbsp;市值：<input  type='text' name='warrant3[price]' />(万元)<br/>取得原值：<input  type='text' name='warrant3[price3]' />(万元)";
			$('#war3_insert').html(html);
		}
		if ($('#warrant3_134').val() == '136'){
			html = '<br/>'+warrant3_com;
			$('#war3_insert').html(html);
		}
		if ($('#warrant3_134').val() == '137'){
			html = "<br/>市值：<input  type='text' name='warrant3[price]' />(万元)";
			$('#war3_insert').html(html);
		}
		if ($('#warrant3_134').val() == '138'){
			html = "<br/>第三方销售收入：<input  type='text' name='warrant3[price2]' />(万元)<br />&nbsp;&nbsp;对外担保情况：<input type='text' name='warrant3[about]'/>";
			$('#war3_insert').html(html);
		}
		if ($('#warrant3_134').val() == '139'){
			html = "<br/>质押物：<input type='text' name='warrant3[good]'/>&nbsp;市值：<input  type='text' name='warrant3[price]' />(万元)";
			$('#war3_insert').html(html);
		}
		if ($('#warrant3_134').val() == '229'){
			html = "";
			$('#war3_insert').html(html);
		}
	});



	$('#ckcd_171').live("change",function(){
		var html = '';
		if ($('#ckcd_171').attr('checked') == 'checked') {
			html = '<br/>其他请填写：<input type="text" name="form[TALLENT][about]" />';	
		};
		$('#tall_insert').html(html);
	});

	$('#ckcd_177').live("change",function(){
		var html = '';
		if ($('#ckcd_177').attr('checked') == 'checked') {
			html = '<br />其他证书请填写：<input type="text" name="form[COM_HONOR][about]" />';	
		};
		$('#tall_insert').html(html);
	});


	//此处id245与生产环境不一样 文化荣誉
	$('.qyry').live('change',function(){
		var inputval = $(this).val();
		if(inputval == '245' ){
			$(this).siblings('.qyry').attr('disabled', ($(this).attr('checked') == 'checked') ).attr('checked',false);
			$('#tall_insert').html('');
		}
	});

	//此处id244与生产环境不一样 人才项目
	$('.rcxm').live('change',function(){
		var inputval = $(this).val();
		if(inputval == '244' ){
			$(this).siblings('.rcxm').attr('disabled', ($(this).attr('checked') == 'checked') ).attr('checked',false);
			$('#tall_insert').html('');
		}
	});


	//此处id273与生产环境不一样 合作银行 外网284
	$('.hzyh').live('change',function(){
		var inputval = $(this).val();
		var zhrd_id = null;
		if( is_ip()){
			zhrd_id = 273;
		}else{
			zhrd_id = 284;
		}
		if(inputval == zhrd_id ){
			$(this).siblings('.hzyh').attr('disabled', ($(this).attr('checked') == 'checked') ).attr('checked',false);
		}
	});

	$('#brand_1').live("change",function(){
		var html = '';
		if ($('#brand_1').attr('checked') == 'checked') {
			html = '<br />代理品牌名称：<input type="text" name="form[BRAND][about]" /><br />代理商级别：<input type="text" name="form[BRAND][about2]" />';	
		};

		$('#tall_insert').html(html);
	});

	$('#brand_2').live("change",function(){
		var html = '';
		if ($('#brand_2').attr('checked') == 'checked') {
			html = '';	
		};		
		$('#tall_insert').html(html);
	});


	$('#demand1').live("change",function(){
		var html = '';
		if ($('#demand1').attr('checked') == 'checked') {
			html = '<br /><br />授信总量：<input type="text" name="form[DEMAND_O][about2]"/>(万元)&nbsp;授信产品：<input type="text" name="form[DEMAND_O][about]" />';	
		};
		$('#tall_insert').html(html);
	});

	$('#demand2').live("change",function(){
		var html = '';
		if ($('#demand2').attr('checked') == 'checked') {
			html = '<br /><br />授信总量：<input type="text" name="form[DEMAND_O][about2]"/>(万元)&nbsp;授信产品：<input type="text" name="form[DEMAND_O][about]" />';
		};
		$('#tall_insert').html(html);
	});

	$('#demand3').live("change",function(){
		var html = '';
		if ($('#demand3').attr('checked') == 'checked') {
			html = '';	
		};		
		$('#tall_insert').html(html);
	});


	$('#stock_3').click(function(){
		$('#stock_3').html('');		
	});

	$('#stock_3').blur(function(){
		if ($('#stock_3').val() == '') {
			$('#stock_3').html('请填写原材料在产品、产成品里的金额以及所占的比例…（%）');	

		};

	});	


	$('#short').click(function(){
		$('#short').html('');		
	});

	$('#short').blur(function(){
		if ($('#short').val() == '') {
			$('#short').html('若大幅改动请说明…');	
		};
	});		


	$('#long').click(function(){
		$('#long').html('');		
	});

	$('#long').blur(function(){
		if ($('#long').val() == '') {
			$('#long').html('若大幅改动请说明…');	
		};
	});	


	$('#payable').click(function(){
		$('#payable').html('');		
	});

	$('#payable').blur(function(){
		if ($('#payable').val() == '') {
			$('#payable').html('若大幅改动请说明…');	
		};
	});	


	$('#trade_81').live("change",function(){
		var html = '';
		if ($('#trade_81').val() == '132') {
			html = '其他行业请填写：<input type="text" name="form[TRADE_I][about]" />';	
		};
		$('#trade_insert').html(html);
	});


	$('#belong_2').live("change",function(){
		var html = '';
		if ($('#belong_2').attr('checked') == 'checked') {
			html = '是否有搬迁意向：<input type="radio" value="1" name="form[ISMOVE]" />是 &nbsp;&nbsp;<input type="radio" value="2" name="form[ISMOVE]" />否';	
		};
		$('#belong_insert').html(html);
	});

	$('#belong_1').live("change",function(){
		var html = '';
		if ($('#belong_1').attr('checked') == 'checked') {
			html = '';	
		};
		$('#belong_insert').html(html);
	});



	$("#war_insert :input").live("click",function(){
		cl	= $("#war_insert :checked");							
		if(cl.length>2){$(this).removeAttr('checked')}					
	});

	$("#war2_insert :input").live("click",function(){
		cl	= $("#war2_insert :checked");							
		if(cl.length>2){$(this).removeAttr('checked')}					
	});

	$("#war3_insert :input").live("click",function(){
		cl	= $("#war3_insert :checked");							
		if(cl.length>2){$(this).removeAttr('checked')}					
	});
	
	$('#ll_1').blur(function(){
	//失去焦点
		var temp = $('#ll_1').val();
		if (temp == '无') { 
			$('#ll_2').val(temp);
			$('#ll_3').val(temp);
			$('#ll_4').val(temp);
			$('#ll_5').val(temp);
			$('#ll_6').val(temp);
			$('#ll_7').val(temp);
			$('#ll_8').val(temp);
			$('#ll_9').val(temp);
			$('#ll_10').val(temp);
			$('#ll_11').val(temp);
			$('#ll_12').val(temp);
			$('#ll_13').val(temp);
			$('#ll_14').val(temp);
			$('#ll_15').val(temp);
			$('#ll_16').val(temp);
			$('#ll_17').val(temp);
			$('#ll_18').val(temp);
			$('#ll_19').val(temp);
		};
		
	});


});


function is_ip(){
	var test_ip = location.host;
	var reg = new RegExp('([0-9]{1,3}\.){3}[0-9]{1,3}');
	if (reg.test( test_ip)) {
		return true;
	} else{
		return false;
	};
}