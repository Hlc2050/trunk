function dialog_frame(element,width,height,isrefresh){
    try{
        if(!width||!height){
            width='90%';
            height='90%';
        }
        art.dialog.open($(element).attr("href"),{
            title:$(element).html(),
            width:width,
            height:height,
            lock:true,
            close:function(){
                if(isrefresh==1) {
                    window.location.reload(true);
                }
            }
        });
    }catch(e){alert(e.message);}
    return false;
}