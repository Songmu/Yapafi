$(function(){
    /* // –ÚŸ·‹@”\‚ğ‚Â‚¯‚æ‚¤
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
    if (/*@cc_on!@*/true){
        $('pre > code').addClass('prettyprint');
        prettyPrint();
    }
});
