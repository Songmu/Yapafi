$(function(){
    if ($(".column")) {
        $(".column").prepend('<div id="toc"></div>')
        var cnt = 1;
        $(".column h2, .column h3, .column h4, .column h5, .column h6")
            .each(function(){
                var id = $(this).attr('id');
                if(!id){
                    id = 'tocid'+ cnt;
                    $(this).attr('id', id);
                    cnt++;
                }
                $('#toc').append(
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
    
    $('ul.samples li').each(function(){
        $(this).append('<input type="button" value="view source" class="view_source"><input type="button" value="hide source" class="hide_source">');
    });
    
    // ソース表示機能
    $('.view_source').click(function(){
        $this = $(this);
        $this.hide().next().show();
        if( $this.next().next().length == 0 ){
            source = $this.prev().attr('href');
            source = source.replace(/\..+$/,'');
            code_block = $('<pre></pre>');
            $this.parent().append(code_block);
            
            $.ajax({
                url : 'source_viewer/'+ source,
                type : 'GET',
                beforeSend : function(){
                    code_block.html('<img src="common/img/ajax-loader.gif" alt="loading...">');
                },
                success: function(data){
                    code_block.html('<code class="prettyprint">'+data+'</pre>');
                    prettyPrint();
                },
                error : function(data){
                    code_block.html('<strong style="color:red">Error! Request Failed! or No Cntroller Exists</strong>');
                }
            });
        }
        else{
            $this.next().next().show();
        }
    });
    
    $('.hide_source').click(function(){
        $(this).hide().prev().show().next().next().hide();
    });
    
    
    if (/*@cc_on!@*/true){
        $('pre > code').addClass('prettyprint');
        prettyPrint();
    }
});
