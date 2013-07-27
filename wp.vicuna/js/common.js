window.onload = function(){
    (function(sf){
        sf.onfocus = function(){if (this.value === blankSearchKeyword) this.value = null;}
        sf.onblur = function(){if(!this.value) this.value = blankSearchKeyword;}
    })(document.getElementById('searchKeyword'));
}