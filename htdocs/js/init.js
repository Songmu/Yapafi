function init(){
	if ($("#article").is(":has('#toc')")) {
		var cnt = 1;
		$("#article h2, #article h3, #article h4, #article h5, #article h6")
			.each(function(){
				var id = $(this).attr('id');
				if(!id){
					id = 'tocid'+ cnt;
					$(this).attr('id', id);
					cnt++;
				}
				$('#main #toc').append(
					'<li class="' + 'toc' + this.tagName.toLowerCase() + '">' + 
					'<a href="#' + id + '">' + $(this).text() + '</a>' + 
					'</li>'
				);
			});
	}
	
	$('a[href=#]').click(function(){
		$('html,body').animate({ scrollTop: 0 }, 500);
		return false;
	});
	$('a[href*=#]').click(function(){
		var scrollTo = $(this.hash).offset().top;
		$('html,body').animate({ scrollTop: scrollTo }, 500);
		return false;
	});
	if (/*@cc_on!@*/true){
		$('pre > code').addClass('prettyprint');
		prettyPrint();
	}
	$('textarea').addClass('resizable');
	
	$('textarea.resizable:not(.processed)').TextAreaResizer();
	
	$('#historymenu > li > a').click(function(){
		$('#historymenu > li').removeClass('active');
		var elms = $('#historymenu > li > a');
		for ( var i = 0; i < elms.length; i++){
			$( elms[i].hash ).hide();
		}
		$(this).parent().addClass('active');
		$(this.hash).show();
	});
}

$(function(){
	init();
	
	$('#wikisrc').one('keydown', function(){
		$(window).bind('beforeunload', function(ev){
			if( !submit_flg ){
				return ev.originalEvent.returnValue = '編集内容を破棄して別のページに移動しますか？';
    		}
		});
	});
	
	$('#previewbtn').click(function(){
		var content = $("#wikisrc").val();
		var token   = $("#token").val();
		$.ajax({
			url : 'etc/api/wiki',
			data: { 
				content: content,
				token  : token
			},
			type : 'POST',
			beforeSend : function(){
				$('#ajaxpreview').show();
				$('#previewbtn').hide();
				$('#backedit').show();
				$('#ajaxpreview').html('<img src="etc/img/ajax-loader.gif" alt="loading...">');
			},
			success: function(data){
				$('#ajaxpreview').html(data);
				init();
			},
			error : function(data){
				$('#ajaxpreview').html('<strong style="color:red">Error! Request Failed!</strong>');
			}
		});
		
	});
	
	$('#backedit').click(function(){
		$('#ajaxpreview').hide();
		$('#previewbtn').show();
		$('#backedit').hide();
	});
	
	var prev_lines = 0;
	$('textarea#editmore').keyup(function(ev){
		var content = $("#editmore").val();
		
		var lines = 0;
		for (var i = 0, l = content.length; i < l; i++){
			if (content.charAt(i) == '\n') lines++;
		}
		console.log(lines + ' ' + prev_lines);
		if( lines == prev_lines ) return;
		prev_lines = lines;
		
		var token   = $("#token").val();
		$.post(
			'etc/api/wiki',
			{ 
				content: content,
				token  : token
			},
			function(data){
				$('#article').html(data);
				$('#wikisrc').text(data);
				init();
			}
		);
	});
	$('#editmoresrc').click(function(){
		$('#editmoresrc').hide();
		$('#editmorescreen').show();
		$('#article').hide();
		$('#wikisrc').show();
	});
	$('#editmorescreen').click(function(){
		$('#editmorescreen').hide();
		$('#editmoresrc').show();
		$('#wikisrc').hide();
		$('#article').show();
	});
	
});
var submit_flg;



