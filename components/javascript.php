<script type="text/javascript">
    function SetCookie(name, value, days) {
        if($.cookie('cookie_consent') != 1 && name != 'cookie_consent') return;
        const d = new Date();
        d.setTime(d.getTime() + (days*24*60*60*1000));
        let expire = "expires="+ d.toUTCString();
        document.cookie = name + "=" + value + ";" + expire + ";path=/";
    }

    <?php if(!isset($_COOKIE['cookie_consent'])) { ?>
        $(".cookiealert").addClass("show");

        $(".acceptcookies").on("click", function() {
            SetCookie("cookie_consent","1", 400);
            $(".cookiealert").removeClass("show");
        });
    <?php } ?>
       

    $(function () {

        $(window).scroll(function () {
            if($(this).scrollTop() > 100) { // When scrolled 100px
                $('.scroll_to_top').css("opacity","1");
                $('.scroll_to_top').fadeIn();
            }
            else {
                $('.scroll_to_top').fadeOut();
            }
        });

        $('.scroll_to_top').click(function () { // Click on button
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });
    });

    $(".nav-check").removeClass("active");
    <?php if($GET[0] == 'admin') {
        ?>
            $("#<?= $GET[0];?>").addClass("active");
            $("#<?= $page;?>").addClass("active");
        <?php
    }
    else {
        if($GET[0] == 'profile') {
            ?>
            $("#<?= $GET[0];?>").addClass("active");
            $("#<?= $GET[1];?>").addClass("active");
            <?php
        }
        else {
            ?>$("#<?= $page;?>").addClass("active");<?php
        }
    }
    ?>
</script>