function FillAvailability(response,targetObject){
	var arr = JSON.parse(response); 
	var out;   
	if (arr == undefined)
	{
		out = 0;
	}    
	else
	{
		out = arr[0].AvailableItems;        
	};
		document.getElementById(targetObject).innerHTML  = out;        
};

function FillHotpointInfo(response,targetObject2)
{
    var arr = JSON.parse(response); 
    var out;   
    if (arr == undefined)
    {
        out = null;
    }    
    else
    {
        var trHtml = '';
        $(document.getElementById(targetObject2)).empty();
        if (arr.length > 0)
        {
           $('#idHotPointsPlace').show( "slow" ); 
           var table = document.getElementById(targetObject2).createCaption();
           if (currLang == 'uk')
           {
                       table.innerHTML = 'Гарячі закладинки';               
           }
           else
           {
                          table.innerHTML = 'Горячие закладки';
           };
           $.each(arr, function (i, item)
                {
                   if (currLang == 'uk')
                   {
                               trHtml += '<tr><td>' + item.RegionTitle + '</td>';
                   }
                   else
                   {
                               trHtml += '<tr><td>' + item.RegionTitle_ru + '</td>';                   
                   };
                                       
                   trHtml +=  '<td><span class="badge">' + item.Quantity + '</span></td><td><img src="img/cigarette.png"  height="32" width="32" ><img></td><td>x <span class="badge">' + item.PointsCount +  '</span><img src="img/chest-icon.png"  height="32" width="32" ><img></td></tr>'; 
                });
            $(document.getElementById(targetObject2)).append(trHtml);            
        }
        else
        {
           $('#idHotPointsPlace').hide( "slow" ); 
        }                            
    };
}


function GetAndFillAvailability()
{

    var xmlhttp = new XMLHttpRequest();
    var baseUrl = this.location.protocol + "//" + this.location.hostname;
    var url = baseUrl + "/api/json.php?method=getAvailaible";
    var targetObject = "idAvailCnt"; 

    xmlhttp.onreadystatechange=function() {
	    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		    FillAvailability(xmlhttp.responseText, targetObject);
	    }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
};

function GetAndFillHotPointsInfo()
{
    var xmlhttp2 = new XMLHttpRequest();
    var baseUrl2 = this.location.protocol + "//" + this.location.hostname;
    var url2 = baseUrl2 + "/ganja/get_hotpoints_info.php";
    var targetObject2 = "idTblHotPoints"; 

    xmlhttp2.onreadystatechange=function() {
        if (xmlhttp2.readyState == 4 && xmlhttp2.status == 200) {
            FillHotpointInfo(xmlhttp2.responseText, targetObject2);
        }
    }
    xmlhttp2.open("GET", url2, true);
    xmlhttp2.send();
}

function tryCreateOrder()
{
	  return;
};


$(function() {
    GetAndFillHotPointsInfo();
     var lastMessage = getCookie('lastMessage');
     var curMessage = $( "#dialog-message" ).children().attr('id');
     if (lastMessage === null)
     {
        ShowServerMessage();
        setCookie('lastMessage',curMessage, 365);
     }
     else
     {
         if (curMessage > lastMessage)
         {
            ShowServerMessage();
            setCookie('lastMessage',curMessage, 365);
         }
     }
});

