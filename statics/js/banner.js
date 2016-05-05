/***********幻灯*****************/
auto=null;
timer=null;
var focus=new Function();
focus.prototype={
	init:function(){
		//默认动画频率
		this.aTime=this.aTime || 10;
		//默认间隔
		this.sTime=this.sTime || 5000;
		//图片容器
		this.oImg=document.getElementById('focus_m').getElementsByTagName("ul")[0];
		this.oImgLi=this.oImg.getElementsByTagName("li");
		//左右按钮
		this.oL=document.getElementById('focus_l');
		this.oR=document.getElementById('focus_r');
		//创建文字容器
		this.createTextDom();
		//默认第一帧
		this.target=0;
		//开始动画
		this.autoMove();
		//鼠标
		this.oAction();
	},
	createTextDom:function(){
		var that=this;
		//创建文字容器
		this.oText=document.createElement("div");
		this.oText.className="focus_s";
		var ul=document.createElement('ul');
		var frag=document.createDocumentFragment();
		for (var i=0;i<this.oImgLi.length;i++) {
			var li=document.createElement("li");
			li.innerHTML='<b></b>';
			if (i==0) {
				li.className="active";
			};
			frag.appendChild(li);
		};
		ul.appendChild(frag);
		this.oText.appendChild(ul);
		this.o.insertBefore(this.oText,this.o.firstChild);
		//文字容器绑定动作
		this.oTextLi=this.oText.getElementsByTagName("li");		
	},
	autoMove:function(){
		var that = this;   
		auto=setInterval(function(){that.goNext()},that.sTime);
	},
	goNext:function() {
		this.target=this.nowIndex();
		this.target==this.oTextLi.length-1 ? this.target=0:this.target++;
		this.aStep=(this.target-this.nowIndex())*this.step;
		this.removeClassName();
		this.oTextLi[this.target].className="active";
		this.startMove();
	},
	goPrev:function() {
		this.target=this.nowIndex();
		this.target==0 ? this.target=this.oTextLi.length-1 : this.target--;
		this.aStep=(this.target-this.nowIndex())*this.step;
		this.removeClassName();
		this.oTextLi[this.target].className="active";
		this.startMove();
	},
	startMove:function (){
		var that=this;
		var t=0;
		this.timer='';
		function set(){
			if (t>100) {
				clearInterval(that.timer);
			}else {
				for (var i=0;i<that.oImgLi.length;i++) {
					that.oImgLi[i].style.display='none';
				};
				that.oImgLi[that.target].style.display='block';
				that.setOpacity(that.oImg,t);
				t+=5;
			};
		};
		timer=setInterval(set,that.aTime);
	},
	setOpacity:function(elem,level){
		if(elem.filters){
			elem.style.filter = 'alpha(opacity=' + level + ')';
			elem.style.zoom = 1;
		} else {
			elem.style.opacity = level / 100;
		};
	},
	nowIndex:function(){
		for (var i=0;i<this.oTextLi.length;i++) {
			if (this.oTextLi[i].className=='active') {
				return i;
				break;
			}
		};
	},
	oAction:function(){
		var that=this;
		for (var i=0;i<this.oTextLi.length;i++) {
			this.oTextLi[i].index=i;
			this.oTextLi[i].onclick=function(){
				clearInterval(auto);
				clearInterval(timer);
				that.setOpacity(that.oImg,100);
				that.target=this.index;
				that.removeClassName();
				this.className='active';
				that.startMove();
			}
		};
		/*
		mouseEnter (that.o,'mouseenter',function(e){
				clearInterval(auto);
			}
		);
		*/
		mouseEnter (that.o,'mouseleave',function(e){
				clearInterval(auto);
				that.autoMove();
			}
		);
		this.oL.onclick=function(){
			that.goPrev();
		};
		this.oR.onclick=function(){
			that.goNext();
		};
	},
	removeClassName:function(){
		for (var i=0;i<this.oTextLi.length;i++) {
			this.oTextLi[i].className=""
		};
	}
};
var focusRun=new focus();
focusRun.o=document.getElementById("focus");
function mouseEnter(ele,type,func){
	if(window.document.all)	
		ele.attachEvent('on'+type,func);
	else{//ff
		if(type==='mouseenter')
			ele.addEventListener('mouseover',this.withoutChildFunction(func),false);
		else if(type==='mouseleave')
			ele.addEventListener('mouseout',this.withoutChildFunction(func),false);
		else
			ele.addEventListener(type,func,false);		
	};
};
function withoutChildFunction(func){
	return function(e){
		var parent=e.relatedTarget;
		while(parent!=this&&parent){
			try{
				parent=parent.parentNode;}
			catch(e){
				break;
			}
		}
		if(parent!=this)
		func(e);
	};
};
/***********登陆*****************/
var login=function(){
	var loading = new tui.Loading();
	
	var m		= document.getElementById('login_main')
	var s		= document.getElementById('login_sub');
	var li		= m.getElementsByTagName('li');
	var a		= m.getElementsByTagName('a');
	var cnzz	= document.getElementById('login_cnzz');
	var quan	= document.getElementById('login_quan');
	var input	= s.getElementsByTagName('input');
	loading.init(s);
	//
	li[0].onclick=function(){
		cnzz.style.display='block';
		quan.style.display='none';
		a[1].className='';
		a[0].className='active';
		o['cnzz']['e'].innerHTML='';
		o['quan']['e'].innerHTML='';
	};
	li[1].onclick=function(){
		cnzz.style.display='none';
		quan.style.display='block';
		a[1].className='active';
		a[0].className='';
		o['cnzz']['e'].innerHTML='';
		o['quan']['e'].innerHTML='';
	};
	//
	for (var i=0;i<input.length;i++) {
		if (input[i].type=='checkbox') {continue}
		input[i].onfocus=function(){
			var self=this;
			document.onkeydown=function(e){
				e = e || event;
				if (e.keyCode==13) {
					if (a[0].className=='active') {
						cnzzSubmit();
					}else {
						quanSubmit();
					}
				}
			};
			setTimeout(function(){self.select()},0)
		};
		input[i].onblur=function(){
			document.onkeydown=null;
		};
	};
	//
	var o={}
	o['cnzz']={
		'u':document.getElementById('username_cnzz'),
		'p':document.getElementById('password_cnzz'),
		'v':document.getElementById('verify_cnzz'),
		'i':document.getElementById('verify_input_cnzz'),
		'e':document.getElementById('error_cnzz'),
		's':document.getElementById('submit_cnzz'),
		'r':document.getElementById('login_cnzz_remeber')
	};
	o['quan']={
		'c':document.getElementById('company_quan'),
		'u':document.getElementById('username_quan'),
		'p':document.getElementById('password_quan'),
		'v':document.getElementById('verify_quan'),
		'i':document.getElementById('verify_input_quan'),
		'e':document.getElementById('error_quan'),
		's':document.getElementById('submit_quan'),
		'r':document.getElementById('login_quan_remeber')
	};
	o['count']=document.getElementById('login_count');
	o['from']=document.getElementById('from');
	o['ajax']=false;
	//
	if (o['count'].value>=3) {
		o['cnzz']['v'].style.visibility='visible';
		o['quan']['v'].style.visibility='visible';
	};
	//cnzz登陆验证
	o['cnzz']['s'].onclick=function(){
		cnzzSubmit();
	};
	function cnzzSubmit(){
		if (o['ajax']) {
			tui.Alert ('正在请求中，请稍后...');
			return;
		};
		if (o['cnzz']['u'].value=='' || o['cnzz']['u'].value=='请输入用户名') {
			o['cnzz']['e'].innerHTML='请输入用户名';
			setTimeout(function(){o['cnzz']['u'].focus()},0);
		}else if (o['cnzz']['p'].value=='' || o['cnzz']['p'].value=='******'){
			o['cnzz']['e'].innerHTML='请输入登陆密码';
			setTimeout(function(){o['cnzz']['p'].focus()},0);
		}else if (o['count'].value>=3 && o['cnzz']['i'].value==''){
			o['cnzz']['e'].innerHTML='请输入验证码';
			setTimeout(function(){o['cnzz']['i'].focus()},0);
		}else {
			o['cnzz']['e'].innerHTML='';
			var rem;
			o['cnzz']['r'].checked ? rem=1 : rem=0;
			
			//发起验证
			$.ajax({
				type : 'POST',
				url : '?mo=login&fo=login_ok',
				data : 'zh_username=' + o['cnzz']['u'].value + '&password=' + o['cnzz']['p'].value + '&verify=' + o['cnzz']['i'].value + '&type=1&rem='+rem+'&from='+o['from'].value+'&date=' + new Date(),
				async : false,
				beforeSend : function () {
					o['ajax']=true;
					loading.funcShow();
				},
				success : function (msg) {
					o['ajax']=false;
					loading.funcStop();
					changepic();
					if(msg==2){
						  o['cnzz']['e'].innerHTML="用户名或密码为空或格式错误";
						  o['count'].value++;
					  }else if(msg==3){
						 o['cnzz']['e'].innerHTML="异常错误";
						 o['count'].value++;
					  }else if(msg==4){
						 o['cnzz']['e'].innerHTML="请输入正确的验证码";
						 o['count'].value++;
					  }else if(msg==1){
						  window.location.href='/intro.html';
					  }else if(msg==6){
						 o['cnzz']['e'].innerHTML="用户名或密码错误，请重新输入";
						 o['count'].value++;
					  }else if(msg==7){
					     window.location.href='?mo=rec&fo=rec_style&from='+o['from'].value;
					  }else{
						 window.location.href='?mo=rec&fo=rec_list';
					  };
					if (o['count'].value>=3) {
						o['cnzz']['v'].style.visibility='visible';
						o['quan']['v'].style.visibility='visible';
					};
				}
			});
		};
	}
	//quan登陆验证
	o['quan']['s'].onclick=function(){
		quanSubmit();
	};
	function quanSubmit(){
		if (o['ajax']) {
			tui.Alert ('正在请求中，请稍后...');
			return;
		};
		
		if (o['quan']['c'].value=='' || o['quan']['c'].value=='请输入公司名') {
			o['quan']['e'].innerHTML='请输入公司名';
			setTimeout(function(){o['quan']['c'].focus()},0);
		}else if (o['quan']['u'].value=='' || o['quan']['u'].value=='请输入用户名') {
			o['quan']['e'].innerHTML='请输入用户名';
			setTimeout(function(){o['quan']['u'].focus()},0);
		}else if (o['quan']['p'].value=='' || o['quan']['p'].value=='******'){
			o['quan']['e'].innerHTML='请输入登陆密码';
			setTimeout(function(){o['quan']['p'].focus()},0);
		}else if (o['count'].value>=3 && o['quan']['i'].value==''){
			o['quan']['e'].innerHTML='请输入验证码';
			setTimeout(function(){o['quan']['i'].focus()},0);
		}else {
			o['quan']['e'].innerHTML='';
			var rem;
			o['quan']['r'].checked ? rem=1 : rem=0;
			//发起验证
			$.ajax({
				type:'POST',
				url : '?mo=login&fo=login_ok',
				data : 'qj_username=' + o['quan']['u'].value + '&qj_password=' + o['quan']['p'].value + '&com_name=' + o['quan']['c'].value + '&verify=' + o['quan']['i'].value + '&type=2&rem='+rem+'&from='+o['from'].value+'&date=' + new Date(),
				async : false,
				beforeSend : function () {
					o['ajax']=true;
					loading.funcShow();
				},
				success : function (msg) {
					o['ajax']=false;
					loading.funcStop();
					changepic1();
					if(msg==2){
						o['quan']['e'].innerHTML="公司名或用户名或密码为空或格式错误";
						o['count'].value++;
					}else if(msg==3){
						 o['quan']['e'].innerHTML="异常错误";
						 o['count'].value++;
					}else if(msg==4){
						 o['quan']['e'].innerHTML="请输入正确的验证码";
						 o['count'].value++;
					}else if(msg==1){
						window.location.href='/intro.html';
					}else if(msg==6){
						 o['quan']['e'].innerHTML="用户名或密码错误，请重新输入";
						 o['count'].value++;
					}else if(msg==7){
					     window.location.href='?mo=rec&fo=rec_style&from='+o['from'].value;
					}else{
						window.location.href='?mo=rec&fo=rec_list';
					};
					if (o['count'].value >= 3) {
						o['cnzz']['v'].style.visibility = 'visible';
						o['quan']['v'].style.visibility = 'visible';
					};
				}
			});
		};
	};
};
function changepic(){
	document.getElementById('validateImg').src="?mo=login&fo=indentify_code&t="+Date.parse(new Date());
};
function changepic1(){
	document.getElementById('validateImg1').src="?mo=login&fo=indentify_code&t="+Date.parse(new Date());
};
/***********tab*****************/
function tab(){
	var news_m=document.getElementById('news_m').getElementsByTagName('li');
	var news_s=document.getElementById('news_s').getElementsByTagName('ul');
	news_m[0].onclick=function(){
		if (this.className!='li_0') {
			this.className='active';
			news_m[1].className='';
			news_s[0].style.display='block';
			news_s[1].style.display='none';
		};
	};
	news_m[1].onclick=function(){
		if (this.className!='li_0') {
			this.className='active';
			news_m[0].className='';
			news_s[1].style.display='block';
			news_s[0].style.display='none';
		};
	};
};
/***********marquee*****************/
marqueeTime=null;
var marquee=function(){
	var o=document.getElementById('marquee').getElementsByTagName('ul')[0];
	var li=o.getElementsByTagName('li');
	var l=document.getElementById('marquee_l');
	var r=document.getElementById('marquee_r');
	var m=li.length-1;
	var w=135;
	var aTime=10;
	
	function goPrev(){
		clearTimeout(marqueeTime);
		startMove(false);
	};
	function goNext(){
		clearTimeout(marqueeTime);
		startMove(true);
	};
	function startMove(type){
		if (type) {
			var tt=0;	
		}else {
			var tt=w;
			var t1=document.createElement('li');
			var t2=o.getElementsByTagName('li')[m];
			t1.innerHTML=t2.innerHTML;
			o.insertBefore(t1,o.firstChild);
			o.removeChild(t2);
			o.style.marginLeft='-'+tt+'px';
		};
		function set(){
			if (type) {
				if (tt>=w) {
					clearInterval(marqueeTime);
					var t1=document.createElement('li');
					var t2=o.getElementsByTagName('li')[0];
					t1.innerHTML=t2.innerHTML;
					o.appendChild(t1);
					o.removeChild(t2);
					o.style.marginLeft=0;
				}else {
					o.style.marginLeft='-'+tt+'px';
					tt+=Math.ceil((w-tt)*0.1);
				};
			}else {
				if (tt<=0) {
					clearInterval(marqueeTime);
					o.style.marginLeft=0;
				}else {
					o.style.marginLeft='-'+tt+'px';
					tt=Math.floor(0.9*tt);
				};
			}
		};
		marqueeTime=setInterval(set,aTime);
	};
	if (li.length<7) {
		l.style.display=r.style.display='none'
	}else {
		r.onclick=function(){
			goNext();
		};
		l.onclick=function(){
			goPrev();
		};
	};
};
window.onload=function(){
	focusRun.init();
	marquee();
	tab();
	var session=document.getElementById('session');
	if (session.value!='session') {
		if (document.getElementById('login_sub')) {
			login();
		};
	};
};