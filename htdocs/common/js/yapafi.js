$(function(){
    /* // ñ⁄éüç∑çûã@î\ÇÇ¬ÇØÇÊÇ§
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
    });*/
    $('.view_source').click(function(){
        self = this;
        if( $(self).next().length == 0 ){
            source = $(self).prev().attr('href');
            source = source.replace(/\..+$/,'');
            code_block = $('<pre></pre>');
            
            $(self).parent().append(code_block);
            
            $.ajax({
    			url : 'source_viewer/'+ source,
    			type : 'GET',
    			beforeSend : function(){
    				//$('#ajaxpreview').show();
    				//$('#previewbtn').hide();
    				//$('#backedit').show();
    				code_block.html('<img src="common/img/ajax-loader.gif" alt="loading...">');
    			},
    			success: function(data){
    				code_block.html('<code class="prettyprint">'+data+'</pre>');
    				prettyPrint();
    			},
    			error : function(data){
    				code_block.html('<strong style="color:red">Error! Request Failed!</strong>');
    			}
	    	});

            
        }
    });
    
    
    if (/*@cc_on!@*/true){
        $('pre > code').addClass('prettyprint');
        prettyPrint();
    }
});
