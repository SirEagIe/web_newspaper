<div class="cont">
    <div class="a"></div>
    <img class="img" src="res/2.png" alt="">
    <img class="img2" src="res/1.png" alt="">
</div>

<style>
    .cont {
        width: 80%;
        margin: 50px auto 0 auto;
        position: relative;
    }

    .a {
        position: absolute;
        top: 0;
        right: 0;
        width: 0%;
        height: 100%;
        background-color: #fff;
    }

    .img {
        width: 100%;
    }
    .img2 {
        width: 100%;
        position: absolute;
        z-index: 10001;
        top: 0;
        left: 0;
    }

</style>

<button class="m10">-10</button>
<button class="p10">+10</button>

<script>
   
    var buttonP = document.querySelector(".p10");
    var buttonM = document.querySelector(".m10");
    var a = document.querySelector(".a");
    var dl;
    buttonP.addEventListener('click', event => {
        if(dl != 0) {
            dl = +a.style.width.replace("%", "");
            dl -= 10;
            a.style.width = dl+"%";
        }
    });

    buttonM.addEventListener('click', event => {
        if(dl != 100) {
            dl = +a.style.width.replace("%", "");
            dl += 10;
            a.style.width = dl+"%";
        }
    });

</script>