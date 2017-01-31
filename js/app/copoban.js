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

function updateSessionState(newstate)
{
   App.set_state(newstate); 
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
            $('#idMainButton').switchClass("logo_auth","logo_unauth");
            
//        });

    } else
    {
         $('#idMainButton').switchClass("logo_unauth","logo_auth");
                 //removeClass("logo_unauth",3000).addClass("logo_auth",3000);
//        let vSelf = $(document.getElementById('idMainButton'));
//        vSelf.animate({
//            left: '50%',
//            top: '50%',
//            opacity: '0,5'
//        }, "slow");
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
                localStorage.clear();   
                //alert("Data: " + data + "\nStatus: " + status);
            });

}
;


function saveWinPos(win)
{
    localStorage.setItem('wLeft', win.left);
    localStorage.setItem('wTop', win.top);
}


