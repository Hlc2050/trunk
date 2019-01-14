define(function(require,exports, module){
    function light(opt){
        var defaultOpt = {
            ele:   "",
            time:  3000
        }
        $.extend(this,defaultOpt,opt);
        this.$bd=$("#"+this.ele)
        this.$ul=this.$bd.find("ul")
        this.$num=this.$bd.find("#num");
        this.$pre=this.$bd.find("#pre");
        this.$next=this.$bd.find("#next");
        this.$len=this.$bd.find("li").length;
        this.$sWidth =this.$bd.find("li").width();
        this.$index=0;
        this.$ul.css("width",this.$sWidth*(this.$len));
        this.showPics=function(i){
            var nowLeft = -i*this.$sWidth; //根据index值计算ul元素的left值
            this.$ul.stop(true,false).animate({"left":nowLeft},300); //通过animate()调整ul元素滚动到计算出的position
            this.$num.find("span").removeClass("on").eq(i).addClass("on"); //为当前的按钮切换到选中的效果
        }

    }
    light.prototype.pre=function(){
        var self=this;
        self.$pre.on("click",function(){
            self.$index -= 1;
            if(self.$index == -1) {self.$index = self.$len - 1;}
            self.showPics(self.$index);
            return false
        });
    }
    light.prototype.next=function(){
        var self=this;
        self.$next.on("click",function(){
            self.$index += 1;
            if(self.$index == self.$len) {self.$index = 0;}
            self.showPics(self.$index);
            return false
        });
    }
    light.prototype.autoPlay=function(){
        var picTimer;
        var self=this;
        self.$bd.hover(function() {
            clearInterval(picTimer);
        },function() {
            picTimer = setInterval(function() {
                self.showPics(self.$index);
                self.$index++;
                if(self.$index == self.$len) {self.$index = 0;}
            },self.time); //此4000代表自动播放的间隔，单位：毫秒
        }).trigger("mouseleave");

    }
    light.prototype.num=function(){
        var self=this;
        var btn="";
        for(var i=0; i < self.$len; i++) {
            btn += "<span></span>";
        }
        self.$num.append(btn).find("span").on("mouseover",function(){
            var index =$(this).index();
            self.showPics(index);
        }).eq(0).trigger("mouseover");
    }


    $("#recommend").on({
        mouseover:function(ev){
            clearTimeout(this.showBtnTimer)
            $(this).find(".preNext").show();
        },
        mouseout:function(ev){
            var _this = $(this);
            this.showBtnTimer = setTimeout(function(){
                _this.find(".preNext").hide();
            },300)
        }
    });
    function recommend(){
        var light01=new light({
            ele:"recommend",
            time:3000
        });
        light01.pre();
        light01.next();
    }
    function foucs(){
        var light02=new light({
            ele:"foucs",
            time:3000
        });
        light02.num();
        light02.autoPlay();
    };
    function orderPic(){
        var light03=new light({
            ele:"orderPic",
            time:3000
        });
        light03.autoPlay();
    };
    recommend();
    foucs();
    orderPic();



})
