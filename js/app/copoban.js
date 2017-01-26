/* global App */

function doOnLoad()
{
    App.init();
    App.runui();
}

function doOnUnload()
{
    App.finalize();
}

function btnPush()
{
    var btnPS = new Audio();
    btnPS.src = "/js/app/open.mp3";
    btnPS.play();
    let vIsWinVisible = $(document.getElementById('copobanId')).is(':visible');
       
    if (vIsWinVisible === true)
    {
//        $(this).animate({left:"-10px",top:"-10px", opacity: 0.5, zoom: 0.5 }, "slow", function() {
            App.hideLoginWindow();
            App.finalize();
            $('#idMainButton').animate({left:"50%",top:"50%", opacity: 0.5, zoom:1});
            killSession();
            
//        });

    } else
    {
          $('#idMainButton').animate({left:"50px",top:"50px", opacity: 0.5, zoom: 0.5  });
          App.showLoginWindow();

    }
}

function ShowLoginWindow()
{
    $(document.getElementById('copobanId')).show('slow');
    localStorage.setItem('isLoginWindowVisible', true);
}

function HideLoginWindow()
{
    $(document.getElementById('copobanId')).hide("slow");
    localStorage.setItem('isLoginWindowVisible', false);
}

function killSession()
{
    $.get("json.php?action=kill", function(data, status){                
                    alert("Data: " + data + "\nStatus: " + status);
            });

}
;


function saveWinPos(win)
{
    localStorage.setItem('wLeft', win.left);
    localStorage.setItem('wTop', win.top);
}


